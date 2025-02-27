<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->workerId) && !empty($data->latitude) && !empty($data->longitude)) {
    $query = "INSERT INTO locations (worker_id, latitude, longitude) 
              VALUES (:worker_id, :latitude, :longitude)";
    
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":worker_id", $data->workerId);
    $stmt->bindParam(":latitude", $data->latitude);
    $stmt->bindParam(":longitude", $data->longitude);
    
    if($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Location updated successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update location."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update location. Data is incomplete."));
}
?> 