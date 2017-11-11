<?php
include 'mysql-credentials.php';

function validateDate($dtc)
{
	$tempdate = explode('-', $dtc);
	if (checkdate($tempdate[1], $tempdate[0], $tempdate[2]))
	{
		return true;
	} else {
	return false;
	}
}


if (isset($_GET['ISIN']))
{
	$ISIN =  $_GET['ISIN'];
}
else 
{
//	$ISIN = "FR0003500008";
	$ISIN = "FR0000120271";
}

if (isset($_GET['dateselect']))
{
	$stringdate = $_GET['dateselect'];
	
	if (validateDate($stringdate))
	{
	$aujourdhui = date("d-m-Y", strtotime($stringdate));
	}
	else 
	{
		$aujourdhui = date("d-m-Y");
	}
}
else {
		$aujourdhui = date("d-m-Y");
}

$fourweeksbefore = date("Y-m-d", strtotime('-14 day'));

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "select DATE_FORMAT(hourlyprices.TimeDate, '%m/%d/%Y %H:%i:%s') as DateT, Low*Ratio as Low, Open*Ratio as Open, Close*Ratio as Close, High*Ratio as High, CumVolume/Ratio as CumVolume, Ind200.AvgPrice as MM200, Ind20.AvgPrice as MM20, Ind20.AvgPrice-2*Ind20.StddevPrice as BollInf, Ind20.AvgPrice+2*Ind20.StddevPrice as BollSup from hourlyprices left join hourlyindicators Ind200 on (hourlyprices.ISIN = Ind200.ISIN and hourlyprices.TimeDate = Ind200.TimeDate and Ind200.Period = 200) left join hourlyindicators Ind20 on (hourlyprices.ISIN = Ind20.ISIN and hourlyprices.TimeDate = Ind20.TimeDate and Ind20.Period = 20) where hourlyprices.ISIN =\"$ISIN\" and DATE_FORMAT(hourlyprices.TimeDate, '%Y-%m-%d') >= \"$fourweeksbefore\" order by hourlyprices.TimeDate ASC";

$query2 = "select DATE_FORMAT(Day, '%Y-%m-%d') as day, Low*Ratio as low, Open*Ratio as open, Close*Ratio as close, High*Ratio as high from dailyprices where ISIN = \"$ISIN\" and Day = (select max(Day) from dailyprices where ISIN = \"$ISIN\" and Day < curdate())";
$query2 = "select DATE_FORMAT(Day, '%Y-%m-%d') as day, Low*Ratio as low, Open*Ratio as open, Close*Ratio as close, High*Ratio as high from dailyprices where ISIN = \"$ISIN\" and Day = (select max(Day) from dailyprices where ISIN = \"$ISIN\" and Day < curdate())";


$returnedresult = array();

$nb_rowlast = 0;
$last_open= 0;
$last_high= 0;
$last_low= 0;
$last_last= 0;
$last_day= NULL;
if ($result2 = $conn->query($query2)) {
   $nb_rowlast = $result2->num_rows;
   // echo "Number of fetched rows query2: $nb_rowlast";
   // echo "<br /> <br />";
        if ($nb_rowlast = 1) {
                $row2 = $result2->fetch_assoc();
                $last_day  = $row2["day"];
                $last_open = $row2["open"];
                $last_high = $row2["high"];
                $last_last = $row2["close"];
                $last_low  = $row2["low"];
        }

}


if ($result = $conn->query($query)) {

//   $returnedresult = array();
      
   $nb_rows = $result->num_rows;
   $previous_vol = 0;
   $previousday =  date("Y-m-d", strtotime('-50 day'));
   
   /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
    	$nb_rows--;
	$currentdaystring = $row["DateT"];
        $currentday = date("Y-m-d", strtotime($currentdaystring));
	if ($currentday != $previousday) {
		$previousday = $currentday;
		$previous_vol = 0;
	}

    	$vol = $row["CumVolume"] - $previous_vol;
 	array_push($returnedresult, array($row["DateT"], $row["Low"], $row["Open"], $row["Close"], $row["High"], $vol, $row["MM200"], $row["MM20"], $row["BollInf"], $row["BollSup"]) );

    	$previous_vol = $row["CumVolume"];
    }

    /* push pivot data */
    array_push($returnedresult, array($last_day, $last_open, $last_high, $last_low, $last_last) );

    /* free result set */
    $result->free();

    /* send result */ 
    echo json_encode($returnedresult);
}


/* close connection */
$conn->close();


?> 
