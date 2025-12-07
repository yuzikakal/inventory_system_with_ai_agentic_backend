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
    case "POST":
        $type = $conn->real_escape_string($_POST['type']);
        $token = $conn->real_escape_string($_POST['token']);

        $searchUserByToken = "SELECT username, session_token FROM account WHERE session_token = '$token'";
        $isValidToken = mysqli_query($conn, $searchUserByToken);

        if ($type == "tables" && $isValidToken) {
            $result = mysqli_query($conn, "SHOW TABLES");

            $tables = [];
            $nametables = [];
            $columns = [];

            if ($result) {
                while ($row = mysqli_fetch_array($result)) {
                    $nametables[] = $row[0];
                }

                // Ambil data tiap tabel
                foreach ($nametables as $tableName) {

                    // 1. Ambil full columns
                    $colQuery = "SHOW FULL COLUMNS FROM `$tableName`";
                    $colResult = mysqli_query($conn, $colQuery);
                    $colRows = [];
                    while ($c = $colResult->fetch_assoc()) {
                        $colRows[] = $c;
                    }
                    $columns[$tableName] = $colRows;

                    // 2. Ambil data tabel
                    $dataQuery = "SELECT * FROM `$tableName`";
                    $dataResult = mysqli_query($conn, $dataQuery);
                    $dataRows = [];
                    while ($r = $dataResult->fetch_assoc()) {
                        $dataRows[] = $r;
                    }
                    $tables[] = $dataRows;
                }

                echo json_encode([
                    "status" => "success",
                    "name_tables" => $nametables,
                    "columns" => $columns,
                    "data" => $tables
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => $conn->error
                ]);
            }
        }

        else if ($type == "history_chat" && $isValidToken) {
            $username = mysqli_fetch_array(mysqli_query($conn, "SELECT username FROM account WHERE session_token = '$token'"))["username"];
            $result = mysqli_query($conn, "SELECT * FROM history_chat WHERE created_by = '$username'");

            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                echo json_encode([
                    "status" => "success",
                    "data" => $rows
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => $conn->error
                ]);
            }
        }

        else {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid type or token"
            ]);
        }
    break;
    
    default:
        echo json_encode(["status" => "error", "message" => "Unsupported method"]);
        break;
}