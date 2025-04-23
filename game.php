<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

if ($method == 'GET') {
    if (isset($_GET['gameID'])) {
        if ($_GET['gameID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM game Where gameID=$_GET[gameID]");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['title'])) {
        if ($_GET['title'] == "") {
            echo json_encode(["message" => "title must not be empty"]);
        } else {
            $title = "%" . $_GET['title'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE title LIKE ?");
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['platform'])) {
        if ($_GET['platform'] == "") {
            echo json_encode(["message" => "platform must not be empty"]);
        } else {
            $platform = "%" . $_GET['platform'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE platform LIKE ?");
            $stmt->bind_param("s", $platform);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM game");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>