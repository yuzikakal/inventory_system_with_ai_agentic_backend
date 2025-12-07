<?php
$host = getenv('DB_HOST') ?: 'localhost'; 
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'hylmi123';
$db   = getenv('DB_NAME') ?: 'hackathon_imphnen';

$conn = mysqli_connect($host, $user, $pass, $db) or die("Connection Failed");