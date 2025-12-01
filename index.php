<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

include "config/db.php";

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
    // echo json_encode("method: " . $method);
}

switch($method){
    case "GET":
        $sql = "SELECT * FROM inventory";
        $result = mysqli_query($conn, $sql);
        $rows = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        echo json_encode(["status" => "success", "data" => $rows]);
    break;
    
    default:
        echo json_encode(["status" => "error", "message" => "Unsupported method"]);
        break;
}