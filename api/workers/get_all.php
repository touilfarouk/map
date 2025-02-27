<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT w.*, l.latitude, l.longitude, l.timestamp as last_seen 
          FROM workers w 
          LEFT JOIN (
              SELECT worker_id, latitude, longitude, timestamp 
              FROM locations l1 
              WHERE timestamp = (
                  SELECT MAX(timestamp) 
                  FROM locations l2 
                  WHERE l1.worker_id = l2.worker_id
              )
          ) l ON w.worker_id = l.worker_id";

$stmt = $db->prepare($query);
$stmt->execute();

if($stmt->rowCount() > 0) {
    $workers_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($workers_arr, array(
            "workerId" => $row['worker_id'],
            "name" => $row['name'],
            "email" => $row['email'],
            "lastLocation" => $row['latitude'] && $row['longitude'] ? array(
                "lat" => floatval($row['latitude']),
                "lng" => floatval($row['longitude'])
            ) : null,
            "timestamp" => strtotime($row['last_seen']) * 1000
        ));
    }
    
    http_response_code(200);
    echo json_encode($workers_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No workers found."));
}
?> 