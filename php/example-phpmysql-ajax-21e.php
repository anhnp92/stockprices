<?php
include 'mysql-credentials.php';

$aujourdhui = date("d-m-Y");


if (isset($_GET['ISIN']))
{
	$ISIN =  $_GET['ISIN'];
}
else 
{
	$ISIN = "FR0003500008";
//	$ISIN = "FR0000120271";
}

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed!: " . $conn->connect_error);
}
// echo "Connected successfully <br />";

$query = "SELECT A.*, C.MM200, B.MM20, B.BollInf, B.BollSup FROM (SELECT DATE_FORMAT(Day, '%X-%V') as Week, SUBSTRING_INDEX(GROUP_CONCAT(CAST(Open*Ratio AS CHAR) ORDER BY Day ASC), ',', 1 ) as WOpen, MAX(High*Ratio) as WHigh, MIN(Low*Ratio) as WLow, SUBSTRING_INDEX(GROUP_CONCAT(CAST(Close*Ratio AS CHAR) ORDER BY Day DESC), ',', 1 ) as WClose, SUM(Volume/Ratio) as WVol FROM dailyprices where ISIN = \"$ISIN\"  AND Day > '2015-01-01' GROUP BY Week ORDER BY Day) A LEFT JOIN (SELECT Week, AvgPrice as MM20, AvgPrice-2*StddevPrice as BollInf, AvgPrice+2*StddevPrice as BollSup FROM weeklyindicators where ISIN = \"$ISIN\" AND Period = 20 ORDER BY Week) B ON A.Week = B.Week LEFT JOIN (SELECT Week, AvgPrice as MM200 FROM weeklyindicators where ISIN = \"$ISIN\" AND Period = 20 ORDER BY Week) C ON A.Week = C.Week;";

$query2 = "SELECT A.Month as day, A.MLow as low, MOpen open, MClose as close, MHigh as high from (SELECT DATE_FORMAT(Day, '%Y-%m') as Month, MIN(Low) as MLow, SUBSTRING_INDEX(GROUP_CONCAT(CAST(Open AS CHAR) ORDER BY Day ASC), ',', 1 ) as MOpen, SUBSTRING_INDEX(GROUP_CONCAT(CAST(Close AS CHAR) ORDER BY Day DESC), ',', 1 ) as MClose, MAX(High) as MHigh, SUM(Volume) as MVol FROM dailyprices WHERE ISIN = \"$ISIN\" AND Day > '2007-01-01' GROUP BY Month ORDER BY Day) A where A.Month = (select max(DATE_FORMAT(Day, '%Y-%m')) from dailyprices where ISIN = \"$ISIN\" and Day < date_sub(curdate(), INTERVAL 1 MONTH)) ; ";

// echo "$query <br /> <br />";
// echo "$query2 <br /> <br />";


$returnedresult = array();

$nb_rowlast = 0;
$last_open= 0;
$last_high= 0;
$last_low= 0;
$last_last= 0;
$last_day= NULL;
if ($result2 = $conn->query($query2)) {
   $nb_rowlast = $result2->num_rows;
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

   $nb_rows = $result->num_rows;
   
   /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
    	$nb_rows--;
    	array_push($returnedresult, array($row["Week"], $row["WLow"], $row["WOpen"], $row["WClose"], $row["WHigh"], $row["WVol"], $row["MM200"], $row["MM20"], $row["BollInf"], $row["BollSup"]) );
    }

    /* free result set */
    $result->free();

    /* push pivot data */
    array_push($returnedresult, array($last_day, $last_open, $last_high, $last_low, $last_last) );

    /* send result */ 
    echo json_encode($returnedresult);
}


/* close connection */
$conn->close();


?> 
