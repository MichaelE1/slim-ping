<?php

include 'functions.php';

// Display all device IDs
$app->get('/devices', function($request) {
	
	$db = new SQLite3 ('.\app\api\pingsdb.db');
	$query = "SELECT DISTINCT device_id FROM pings";
	$result = $db->query($query);

	$data = array();
	while($row = $result->fetchArray()) {
		$data[] = $row['device_id'];
	}
		header('Content-Type: application/json');
		echo json_encode($data);	
});


// Delete all data from database 
$app->post('/clear_data', function($request)  {

	$db = new SQLite3 ('.\app\api\pingsdb.db');
	$query = "DELETE FROM pings";
	$result = $db->exec($query);

});


// Store posted data
$app->post('/addentry',function($request) {
	$db = new SQLite3 ('.\app\api\pingsdb.db');
	$query = "INSERT INTO pings (device_id, epoch_time) VALUES (?, ?)";
	$stmt = $db->prepare($query);
	$stmt->bindParam(1, $device_id);
	$stmt->bindParam(2, $epoch_time);

	$url = $request->getAttribute('url');
	$urlparts = explode('/', $url);
	$device_id = $urlparts[1];
	$epoch_time = $urlparts[2];	

	$stmt->execute();
}); 


// Retrieve pings for specific device or all devices based on date input
$app->get('/{device_id}/{date}', function($request) {

	$device_id = $request->getAttribute('device_id');
	$startdate = $request->getAttribute('date');

	// Check if valid date
	if (dateCheckISO($startdate)) {

		$enddate = $startdate." 23:59:59";

		$startdate = strtotime($startdate);
		$enddate = strtotime($enddate);

		dbRetrieve($device_id, $startdate, $enddate);
	}
}); 



// Retrieve pings for a specfic device or all devices with input from:to
$app->get('/{device_id}/{from}/{to}', function($request) {

	$device_id = $request->getAttribute('device_id');
	$from = $request->getAttribute('from');
	$to = $request->getAttribute('to');

	$toCheck = true;
	$fromCheck = true;

	// If date is not a timestamp, check that the date is ISO
	if (!is_numeric($from)) {
		$fromCheck = dateCheckISO($from);
		if ($fromCheck) $from = strtotime($from);
	}

	if (!is_numeric($to)) {
		$toCheck = dateCheckISO($to);
		if ($toCheck) {
			$to = $to." 23:59:59";
			$to = strtotime($to);
		}
	}

	// If from:to dates are valid, retrieve pings from db
	if ($toCheck && $fromCheck) {
		dbRetrieve($device_id, $from, $to);
	} 
});

?>