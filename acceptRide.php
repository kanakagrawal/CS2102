<?php

	require("db_connect.php");
	
	$query = "SELECT client FROM ride_price WHERE rideid = ".$_POST["rideid"];
	$ride = pg_query($con, $query);
	$row = pg_fetch_assoc($ride);
	
	$insert = "INSERT INTO complete_ride VALUES(".$_POST["rideid"].", '".$row["client"]."')";
    $insert_return = pg_query($con, $insert);

    if(!$insert_return){
		echo "Error: could not insert data.";
		echo "Have you filled in all information?";
		echo $insert;
    }
	require("db_close.php");
	
	require("offeredRides.php");
?>
