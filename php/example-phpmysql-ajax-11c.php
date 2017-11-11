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

$query = "select DATE_FORMAT(dailyprices.Day, '%Y-%m-%d') as day, Low, Open, Close, High, Volume, Ind200.AvgPrice as MM200, Ind20.AvgPrice as MM20, Ind20.AvgPrice-2*Ind20.StddevPrice as BollInf, Ind20.AvgPrice+2*Ind20.StddevPrice as BollSup from dailyprices left join dailyindicators Ind200 on (dailyprices.ISIN = Ind200.ISIN and dailyprices.Day = Ind200.Day and Ind200.Period = 200) left join dailyindicators Ind20 on (dailyprices.ISIN = Ind20.ISIN and dailyprices.Day = Ind20.Day and Ind20.Period = 20)  where dailyprices.ISIN = \"$ISIN\" order by dailyprices.Day DESC limit 90";



if ($result = $conn->query($query)) {

   $returnedresult = array();
   $nb_rows = $result->num_rows;
   
   
   /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
    	$nb_rows--;
    	array_push($returnedresult, array($row["day"], $row["Low"], $row["Open"], $row["Close"], $row["High"], $row["Volume"], $row["MM200"], $row["MM20"], $row["BollInf"], $row["BollSup"]) );
    }

    /* free result set */
    $result->free();

    /* send result */ 
    echo json_encode($returnedresult);
}


/* close connection */
$conn->close();


?> 
