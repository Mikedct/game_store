<?php
$host = "localhost";
$user = "root"; // Default user MySQL XAMPP
$password = ""; // Kosong jika menggunakan XAMPP
$dbname = "game_store";

// Membuat koneksi menggunakan MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}
?>
