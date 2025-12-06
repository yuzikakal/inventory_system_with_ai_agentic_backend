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
        $user = $conn->real_escape_string($_GET["user"]);
        if ($user){
            $sql = "SELECT * FROM inventory WHERE created_by = '$user'";
        }
        $result = mysqli_query($conn, $sql);
        $rows = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        echo json_encode(["status" => "success", "data" => $rows]);
    break;

    case "POST":
        $action = $conn->real_escape_string($_POST["action"]);

        if ($action == "create"){
            $nameitem = $conn->real_escape_string($_POST["name"]);
            $stock = $conn->real_escape_string($_POST["stock"]);
            $price = $conn->real_escape_string($_POST["price"]);
            $created_by = $conn->real_escape_string($_POST["created_by"]);

            $sql = "INSERT INTO inventory (name, stock, price, created_by) VALUES ('$nameitem', '$stock', '$price', '$created_by')";
            $result = mysqli_query($conn, $sql);

            if ($result){
                echo json_encode(["status" => "success", "data" => ["nameitem" => $nameitem, "created_by" => $created_by]]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }

        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
    break;

    case "PUT":        
        $id = $conn->real_escape_string($_POST['id']);

        if (isset($id)){
            $nameitem = $conn->real_escape_string($_POST["name"]);
            $stock = $conn->real_escape_string($_POST["stock"]);
            $price = $conn->real_escape_string($_POST["price"]);
            $created_by = $conn->real_escape_string($_POST["created_by"]);

            $result = mysqli_query($conn, "UPDATE inventory SET name = '$nameitem', stock = '$stock', price = '$price', created_by = '$created_by' WHERE ID = '$id'");

            if ($result){
                echo json_encode(["status" => "success", "data" => ["nameitem" => $id, "created_by" => $created_by]]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "$id"]);
        }

    break;

    case "DELETE":
        $token = $conn->real_escape_string($_GET['token']);
        $id = $conn->real_escape_string($_GET['id']);

        $sql = "SELECT ID, password, username, isAdmin FROM account WHERE session_token = '$token'";
        $result = mysqli_query($conn, $sql);
        
        if (!$result || $result->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Invalid token"]);
            break;
        }

        if (isset($id)){
            $result = mysqli_query($conn, "DELETE FROM inventory WHERE ID = '$id'");

            if ($result){
                echo json_encode(["status" => "success", "data" => ["id" => $id]]); // Include action in response
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "ID not provided"]);
        }
    break;
    
    default:
        echo json_encode(["status" => "error", "message" => "Unsupported method"]);
        break;
}