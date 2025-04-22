<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

if ($method == 'GET') {
    if (isset($_GET['paymentID'])) {
        if ($_GET['paymentID'] == "") {
            echo json_encode(["message" => "ID Kosong"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM payment Where paymentID=$_GET[paymentID]");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['paymentMethod'])) {
        if ($_GET['paymentMethod'] == "") {
            echo json_encode(["message" => "paymentMethod Kosong"]);
        } else {
            $paymentMethod = "%" . $_GET['paymentMethod'] . "%";
            $stmt = $conn->prepare("SELECT * FROM payment WHERE paymentMethod LIKE ?");
            $stmt->bind_param("s", $paymentMethod);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM payment");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }
} else {
    echo json_encode(["message" => "Method tidak di ijinkan"]);
}
?>