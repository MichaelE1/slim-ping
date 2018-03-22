<?php

// Checks the formatting of an ISO date and checks that the date exists (i.e. correct month and day)
function dateCheckISO($date) {
	$formatting = preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts);
	$datecheck = checkdate($parts[2], $parts[3], $parts[1]);

	return ($formatting && $datecheck);
}

// Retrieve pings from database
function dbRetrieve($device_id, $startdate, $enddate) {
		$db = new SQLite3 ('.\app\api\pingsdb.db');

		if (strtolower($device_id) == "all") {
			$query = "SELECT device_id, epoch_time FROM pings WHERE epoch_time >= :startdate AND epoch_time < :enddate";
		} else {
			$query = "SELECT epoch_time FROM pings WHERE device_id = :device_id AND epoch_time >= :startdate AND epoch_time < :enddate";	
		}
		
		$stmt = $db->prepare($query);
		$stmt->bindParam(':device_id', $device_id);
		$stmt->bindParam(':startdate', $startdate);
		$stmt->bindParam(':enddate', $enddate);

		$result = $stmt->execute();

		$data = array();
		while($row = $result->fetchArray()) {
			if (strtolower($device_id) == "all") {
				$data[$row['device_id']][] = $row['epoch_time'];
			} else {
				$data[] = $row['epoch_time'];
			}
		}

		header('Content-Type: application/json');
		echo json_encode($data); 
}

?>