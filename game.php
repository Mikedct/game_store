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
    } else if (isset($_GET['gameCode'])) {
        if ($_GET['gameCode'] == "") {
            echo json_encode(["message" => "Game code must not be empty"]);
        } else {
            $gameCode = "%" . $_GET['gameCode'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE gameCode LIKE ?");
            $stmt->bind_param("s", $gameCode);
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
    } else if (isset($_GET['genre'])) {
        if ($_GET['genre'] == "") {
            echo json_encode(["message" => "Genre must not be empty"]);
        } else {
            $genre = "%" . $_GET['genre'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE genre LIKE ?");
            $stmt->bind_param("s", $genre);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['developer'])) {
        if ($_GET['developer'] == "") {
            echo json_encode(["message" => "Developer must not be empty"]);
        } else {
            $developer = "%" . $_GET['developer'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE developer LIKE ?");
            $stmt->bind_param("s", $developer);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['publisher'])) {
        if ($_GET['publisher'] == "") {
            echo json_encode(["message" => "Publisher must not be empty"]);
        } else {
            $publisher = "%" . $_GET['publisher'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE publisher LIKE ?");
            $stmt->bind_param("s", $publisher);
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