<?php
  session_start();
  if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit;
  }
  ?>


<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<body>
	
<!-- Navigation -->
<?php require('header.html'); ?>

<?php
  	// Connect to the database. Please change the password in the following line accordingly
    require("db_connect.php");?>
    <div class = "container">
    <form class="form-horizontal" method="GET" action="ride.php">
		<div class="form-group">
		  <div class="col-md-6">
			  <label for="origin">Departure:</label>
			  <select class="form-control" id="origin" name="origin">
			    <option>Select...</option>
			    <?php
					$locations = pg_query($con, "SELECT DISTINCT origin FROM ride");
					while($choices = pg_fetch_assoc($locations)){
						echo "<option value='".$choices['origin']."' ";
						if(isset($_GET[origin]) && $_GET[origin] == $choices['origin']){
							echo "selected";
						}
						echo">".$choices['origin']."</option>";
					}
			    ?>
			  </select>
		  </div>

		  <div class="col-md-6">
			  <label for="destination">Destination:</label>
			  <select class="form-control" id="destination" name="destination">
			    <option>Select...</option>
			    <?php
					$locations = pg_query($con, "SELECT DISTINCT destination FROM ride");
					while($choices = pg_fetch_assoc($locations)){
						echo "<option value='".$choices['destination']."' ";
						if(isset($_GET[destination]) && $_GET[destination] == $choices['destination']){
							echo "selected";
						}
						echo">".$choices['destination']."</option>";
					}
			    ?>
			  </select>
		  </div>
		</div>
		<div class = "form-group">
			<div class = "col-md-1">
			  <label for="order">Sort By:</label>
			</div>
			<div class = "col-md-4">
			  <select class="form-control" id="order" name="order">
			  	<option value="time_stamp">Date</option>
				<option value="price">Price (lowest) </option>
				<option value="price DESC">Price (higest) </option>
			  </select>
			</div>
			<div class="col-md-4"></div>
			<br>
			<div class="col-md-3">
				<button type="submit" class="btn btn-success w3-button w3-round w3-teal w3-right">Search</button>
			</div>
		</div>
	</form>
	</div>
	
	
	<!-- QUERY AND PRINT RESULT -->
	<?php
	
    $result = pg_query($con, "SELECT * FROM ride r 
		where r.rideid NOT IN (Select c.rideid from complete_ride c)
		AND r.origin LIKE '%".$_GET[origin]."%'
		AND r.destination LIKE '%".$_GET[destination]."%'
		ORDER BY ".( isset($_GET[order])? $_GET[order] :"time_stamp"));


    if (!$result) {
		echo "<h2>An error occurred.</h2>";
		exit;
	}
    while($row    = pg_fetch_assoc($result)){
    	$minBid = $row['price'] + 0.05;
		echo " 
		<div class='w3-container w3-card w3-light-grey w3-margin-bottom w3-margin-left w3-margin-right'>
		<div class='w3-row-padding'>
			<div class='w3-container w3-medium w3-half'>
				<h5 class='w3-opacity'><i class='fa fa-location-arrow fa-fw w3-margin-right w3-large w3-text-black'></i><i class='w3-margin-right w3-text-teal'>Departure</i>".$row['origin']."</h5>
				<h5 class='w3-opacity'><i class='fa fa-map-marker fa-fw w3-margin-right w3-large w3-text-black'></i><i class=' w3-margin-right w3-text-teal'>Arrival</i>".$row['destination']."</h5>
				<h5 class='w3-opacity'><i class='fa fa-calendar fa-fw w3-margin-right w3-large w3-text-black'></i><i class='w3-margin-right w3-text-teal'>Date</i>".$row['time_stamp']."</h5>
		
				<form class='w3-container w3-margin-bottom' name='rideDetails' action='rideDetails.php' method='POST'>
				<input type='hidden' name='rideid' value='".$row['rideid']."' />
				<div class='w3-center'>
				<button class='w3-button w3-small w3-white w3-border w3-border-teal w3-round-small' type='submit' value='s'>Details</button>
				</div>
				</form>
			</div>
			<div class='w3-half w3-container'>
				<h5 class='w3-opacity'><b class='w3-margin-right w3-margin-left w3-text-teal'>Current price</b>".$row['price']."<i class='fa fa-dollar fa-fw w3-margin-right w3-large w3-text-black'></i></h5>
				<div class='w3-container w3-margin-bottom'>
					<form class='w3-container w3-card' name='newBid' action='newBid.php' method='POST'>
					<input type='hidden' name='email' value='".$_SESSION['email']."'>
					<input type='hidden' name='rideid' value=".$row['rideid'].">
					<label class='w3-text-teal'>Make an offer</label>
					<input class='w3-input' type='number' name='bid' step=0.05 min='".$minBid."' />
					<div class='w3-right w3-margin-bottom'>
						<input class='w3-btn w3-teal' type='submit' value='New Bid'>
					</div>
					</form>
					
				</div>
			</div>
		</div>
		</div>
		
		";

	}
	
	require("db_close.php");
    ?> 

	
</body>

</html>
