<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../config/db.php";

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
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
            $searchUserByToken = "SELECT username FROM account WHERE session_token = '$token'";
            $isValidToken = mysqli_query($conn, $searchUserByToken);
            if (!$isValidToken || mysqli_num_rows($isValidToken) == 0) {
                echo json_encode(["status" => "error", "message" => "not valid token"]);
                exit;
            }
            $username = mysqli_fetch_assoc($isValidToken)["username"];

            // Split query
            $queries = array_filter(array_map('trim', explode(";", $sql_script_raw)));

            // Simpan ke history (selalu simpan walau kosong/invalid)
            $sql_formatted = $conn->real_escape_string($sql_script_raw);
            $sqlInsertHistory = "INSERT INTO history_chat (request, response, sql_script, created_by) 
                                VALUES ('$request', '$response', '$sql_formatted', '$username')";
            mysqli_query($conn, $sqlInsertHistory);

            // Handle jika sql_script kosong
            if (empty($queries)) {
                echo json_encode([
                    "status" => "success",
                    "data" => [
                        "request" => $request,
                        "created_by" => $username,
                        "sql_script" => [],
                        "results" => []
                    ]
                ]);
                return;
            }

            // Jalankan query satu-satu
            $results = [];
            foreach ($queries as $query) {
                $result = mysqli_query($conn, $query);

                if ($result instanceof mysqli_result) {
                    $rows = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $rows[] = $row;
                    }
                    $results[] = [
                        "query" => $query,
                        "rows" => $rows
                    ];
                } 
                else if ($result === true) {
                    $results[] = [
                        "query" => $query,
                        "affected_rows" => mysqli_affected_rows($conn)
                    ];
                } 
                else {
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
        }

    break;

    default:
        echo json_encode(["status" => "error", "message" => "Unsupported method"]);
        break;
}