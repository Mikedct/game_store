<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

if ($method == 'GET') {
    if (isset($_GET['userID'])) {
        if ($_GET['userID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM users Where userID=$_GET[userID]");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['firstName'])) {
        if ($_GET['firstName'] == "") {
            echo json_encode(["message" => "First name must not be empty"]);
        } else {
            $firstname = "%" . $_GET['firstName'] . "%";
            $stmt = $conn->prepare("SELECT * FROM users WHERE firstName LIKE ?");
            $stmt->bind_param("s", $firstname);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['lastName'])) {
        if ($_GET['lastName'] == "") {
            echo json_encode(["message" => "Last name must not be empty"]);
        } else {
            $lastName = "%" . $_GET['lastName'] . "%";
            $stmt = $conn->prepare("SELECT * FROM users WHERE lastName LIKE ?");
            $stmt->bind_param("s", $lastName);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['username'])) {
        if ($_GET['username'] == "") {
            echo json_encode(["message" => "Username must not be empty"]);
        } else {
            $username = "%" . $_GET['username'] . "%";
            $stmt = $conn->prepare("SELECT * FROM users WHERE username LIKE ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['email'])) {
        if ($_GET['email'] == "") {
            echo json_encode(["message" => "Email must not be empty"]);
        } else {
            $email = "%" . $_GET['email'] . "%";
            $stmt = $conn->prepare("SELECT * FROM users WHERE email LIKE ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['phoneNumber'])) {
        if ($_GET['phoneNumber'] == "") {
            echo json_encode(["message" => "Phone number must not be empty"]);
        } else {
            $phoneNumber = "%" . $_GET['phoneNumber'] . "%";
            $stmt = $conn->prepare("SELECT * FROM users WHERE phoneNumber LIKE ?");
            $stmt->bind_param("s", $phoneNumber);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }
}

// ===== POST =====
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $phoneNumber = $_POST['phoneNumber'];
    $password = md5($_POST['password']);

    $sql = "INSERT INTO user (userID, firstName, lastName, username, email, dateOfBirth, phoneNumber, password)
            VALUES (NULL, '$firstName', '$lastName', '$username', '$email', '$dateOfBirth', '$phoneNumber', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "User berhasil ditambahkan"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

// ===== PUT =====
else if($method == 'PUT') {
    if (isset($input['userID'])) {
        $input = [$input];
    }

    $allowedFields = ['firstName', 'lastName', 'username', 'email', 'dateOfBirth', 'phoneNumber', 'password'];
    $updatedUsers = [];
    $failedUsers = [];

    foreach ($input as $user) {
        if (!isset($user['userID'])) {
            $failedUsers[] = ["userID" => null, "message" => "userID tidak ditemukan"];
            continue;
        }

        $userID = $user['userID'];

        // Cek user
        $stmt = $conn->prepare("SELECT userID FROM users WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedUsers[] = ["userID" => $userID, "message" => "User ID tidak ditemukan"];
            continue;
        }

        // Validasi dateOfBirth
        if (isset($user['dateOfBirth']) && !DateTime::createFromFormat('Y-m-d', $user['dateOfBirth'])) {
            $failedUsers[] = ["userID" => $userID, "message" => "Format dateOfBirth tidak valid (harus YYYY-MM-DD)"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($user[$field])) {
                $fieldsToUpdate[] = "$field = ?";
                $types .= 's';

                // Hash password jika field password
                if ($field === 'password') {
                    $values[] = md5($user[$field]);
                } else {
                    $values[] = $user[$field];
                }
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedUsers[] = ["userID" => $userID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i';
        $values[] = $userID;

        $sql = "UPDATE users SET " . implode(", ", $fieldsToUpdate) . " WHERE userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedUsers[] = ["message" => "Data user dengan ID $userID berhasil diperbarui"];
        } else {
            $failedUsers[] = ["userID" => $userID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    $response = [];
    if (!empty($updatedUsers)) {
        $response["updated"] = $updatedUsers;
    }
    if (!empty($failedUsers)) {
        $response["failed"] = $failedUsers;
    }

    echo json_encode($response);
} else {
    echo json_encode(["message" => "Method not allowed"]);
}
?>
