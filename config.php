<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "game_store";

// Membuat koneksi menggunakan MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}
?>
