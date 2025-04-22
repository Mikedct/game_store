<?php
    header("Content-Type: application/json");
    include "config.php";

    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents("php://input"), true);

    if($method == 'GET'){
        if(isset($_GET['id'])){
            if($_GET['id']==""){
                echo json_encode(["message" => "ID Kosong"]);
            }else{
                $stmt = $conn->prepare("SELECT * FROM users Where id=$_GET[id]");
                $stmt->execute();
                $result = $stmt->get_result();
                $users = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($users);
            }
        } else if(isset($_GET['name'])){
            if($_GET['name']==""){
                echo json_encode(["message" => "name Kosong"]);
            }else{
                $name = "%" . $_GET['name'] . "%";
                $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ?");
                $stmt->bind_param("s", $name);
                $stmt->execute();
                $result = $stmt->get_result();
                $users = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($users);
            }
        } else{
            $stmt = $conn->prepare("SELECT * FROM users");
            $stmt->execute();
                $result = $stmt->get_result();
                $users = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($users);
        }
    }
    else if($method == 'POST'){
        
    }
    else if($method == 'PUT'){
        
    }
    else if($method == 'DELETE'){
        
    }
    else{
        echo json_encode(["message" => "Method tidak di ijinkan"]);
    }
?>