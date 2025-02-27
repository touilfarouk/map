<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->workerId) && !empty($data->name) && !empty($data->email)) {
    $query = "INSERT INTO workers (worker_id, name, email) VALUES (:worker_id, :name, :email)";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":worker_id", $data->workerId);
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":email", $data->email);
    
    if($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "Worker registered successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to register worker."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to register worker. Data is incomplete."));
}
?> 