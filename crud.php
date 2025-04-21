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
        if (isset($input['name']) && isset($input['email'])) {
            $email = $input['email'];
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Jika email sudah ada
            if ($stmt->num_rows > 0) {
                echo json_encode(["message" => "Email sudah terdaftar"]);
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
                $stmt->bind_param("ss", $input['name'], $input['email']);
                $stmt->execute();
                echo json_encode(["message" => "User added", "id" => $conn->insert_id]);
            } 
        }else{
                echo json_encode(["message" => "Invalid Input"]);
            }
    }
    else if($method == 'PUT'){
        if (isset($input['id']) && isset($input['name']) && isset($input['email'])) {
            $email = $input['email'];
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Jika email sudah ada
            if ($stmt->num_rows > 0) {
                echo json_encode(["message" => "Email sudah terdaftar"]);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=? ,email=? WHERE id=?");
                $stmt->bind_param("ssi", $input['name'], $input['email'], $input['id']);
                $stmt->execute();
                echo json_encode(["message" => "User Update, id : $input[id]"]);
            } 
        }else{
            echo json_encode(["message" => "Invalid Input"]);
        }
    }
    else if($method == 'DELETE'){
        if (isset($input['id'])) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $input['id']);
            $stmt->execute();
            echo json_encode(["message" => "User Deleted"]);
        } else{
            echo json_encode(["message" => "Invalid Input"]);
        }
    }
    else{
        echo json_encode(["message" => "Method tidak di ijinkan"]);
    }
?>