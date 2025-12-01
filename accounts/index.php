<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

include "../config/db.php";

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
    // echo json_encode("method: " . $method);
}

switch($method){
    case "GET":
        $sql = "SELECT ID, created_at, username, isAdmin FROM account";
        $result = mysqli_query($conn, $sql);
        $rows = [];

        if ($result && $row = $result->fetch_assoc()) {
            echo json_encode(["status" => "success", "data" => $row]);
        } else {
            echo json_encode(["status" => "error", "message" => "data not found"]);
        }
        break;

    case "POST":
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);

        $sql = "SELECT password FROM account WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        $row = $result->fetch_assoc();
        $hash = $row['password'];
        $verify = password_verify($password, $hash);
        if ($verify){
            $sql = "SELECT ID, created_at, username, isAdmin FROM account WHERE username = '$username'";
            $result = mysqli_query($conn, $sql);
            $row = $result->fetch_assoc();
            if (mysqli_query($conn, $sql)) {
                echo json_encode(["status" => "success", "data" => $row]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid username or password"]);
        }
        break;
}