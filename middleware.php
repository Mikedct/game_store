<?php
require "vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function verifyJWT() {
    $headers = apache_request_headers();
    $key = "Lol"; // Tempatkan di sini, di dalam fungsi

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["message" => "Authorization header not found"]);
        exit;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace("Bearer ", "", $authHeader);

    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array) $decoded;

    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "Invalid or expired token",
            "error" => $e->getMessage()
        ]);
        exit;
    }
}
?>
