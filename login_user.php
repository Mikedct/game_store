<?php
require "config.php";
require "vendor/autoload.php";
use Firebase\JWT\JWT;

$key = "Lol"; // Ganti dengan key rahasia Anda

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['username']) && isset($input['password'])) {
    $username = $input['username'];
    $password = md5($input['password']);

    $stmt = $conn->prepare("SELECT userID, username FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        $payload = [
            "userId" => $result['userID'],
            "username" => $result['username'],
            "exp" => time() + 3600 // Token berlaku 1 jam
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        echo json_encode(["message" => "Login successful", "token" => $jwt]);
    } else {
        echo json_encode(["message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["message" => "Username and password are required"]);
}
?>