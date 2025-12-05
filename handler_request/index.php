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
        $action = $conn->real_escape_string($_POST["action"]);

        if ($action == "ask_ai") {
            $request = $conn->real_escape_string($_POST["request"]);
            $response = $conn->real_escape_string($_POST["response"]);
            $sql_script_raw = $_POST["sql_script"] ?? "";
            $token = $conn->real_escape_string($_POST["token"]);

            // Validasi token
            $searchUserByToken = "SELECT username, session_token FROM account WHERE session_token = '$token'";
            $isValidToken = mysqli_query($conn, $searchUserByToken);
            if (!$isValidToken || mysqli_num_rows($isValidToken) == 0) {
                echo json_encode(["status" => "error", "message" => "not valid token"]);
                exit;
            }
            $dataFromToken = mysqli_fetch_assoc($isValidToken);
            $username = $dataFromToken["username"];

            // Split multiple query berdasarkan ;
            $queries = array_filter(array_map('trim', explode(";", $sql_script_raw)));

            // Simpan ke history (gabungan semua query jadi satu string)
            $sql_formatted = $conn->real_escape_string($sql_script_raw);
            $sqlInsertHistory = "INSERT INTO history_chat (request, response, sql_script, created_by) 
                                VALUES ('$request', '$response', '$sql_formatted', '$username')";
            $insertToHistory = mysqli_query($conn, $sqlInsertHistory);
            if (!$insertToHistory) {
                echo json_encode(["status" => "error", "message" => $conn->error]);
                exit;
            }

            $results = [];
            foreach ($queries as $query) {
                $safeQuery = $query;
                $result = mysqli_query($conn, $safeQuery);

                if ($result) {
                    if ($result instanceof mysqli_result) {
                        $rows = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        $results[] = [
                            "query" => $query,
                            "rows" => $rows
                        ];
                    } else {
                        $results[] = [
                            "query" => $query,
                            "affected_rows" => mysqli_affected_rows($conn)
                        ];
                    }
                } else {
                    $results[] = [
                        "query" => $query,
                        "error" => $conn->error
                    ];
                }
            }

            echo json_encode([
                "status" => "success",
                "data" => [
                    "request" => $request,
                    "created_by" => $username,
                    "sql_script" => $queries,
                    "results" => $results
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Unsupported action"]);
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
        $id = $conn->real_escape_string($_POST['id']);

        if (isset($id)){
            $result = mysqli_query($conn, "DELETE FROM inventory WHERE ID = '$id'");

            if ($result){
                echo json_encode(["status" => "success", "data" => ["id" => $id]]);
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