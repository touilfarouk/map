<?php
include_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Database connection failed");
    }

    $data = json_decode(file_get_contents("php://input"));

    if(!empty($data->workerId) && !empty($data->name) && !empty($data->email)) {
        $query = "INSERT INTO workers (worker_id, name, email) VALUES (:worker_id, :name, :email)";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(":worker_id", $data->workerId);
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":email", $data->email);
        
        if($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array(
                "success" => true,
                "message" => "Worker registered successfully."
            ));
        } else {
            throw new Exception("Database execution failed");
        }
    } else {
        throw new Exception("Incomplete data provided");
    }
} catch(Exception $e) {
    error_log("Registration Error: " . $e->getMessage());
    http_response_code(503);
    echo json_encode(array(
        "success" => false,
        "message" => "Registration failed: " . $e->getMessage()
    ));
}
?> 