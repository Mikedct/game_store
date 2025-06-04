<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

if ($method == 'GET') {
    if (isset($_GET['adminID'])) {
        if ($_GET['adminID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM admin Where adminID=$_GET[adminID]");
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
            $stmt = $conn->prepare("SELECT * FROM admin WHERE firstName LIKE ?");
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
            $firstname = "%" . $_GET['lastName'] . "%";
            $stmt = $conn->prepare("SELECT * FROM admin WHERE lastName LIKE ?");
            $stmt->bind_param("s", $firstname);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['username'])) {
        if ($_GET['username'] == "") {
            echo json_encode(["message" => "username must not be empty"]);
        } else {
            $firstname = "%" . $_GET['username'] . "%";
            $stmt = $conn->prepare("SELECT * FROM admin WHERE username LIKE ?");
            $stmt->bind_param("s", $firstname);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['email'])) {
        if ($_GET['email'] == "") {
            echo json_encode(["message" => "email must not be empty"]);
        } else {
            $username = "%" . $_GET['email'] . "%";
            $stmt = $conn->prepare("SELECT * FROM admin WHERE email LIKE ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['phoneNumber'])) {
        if ($_GET['phoneNumber'] == "") {
            echo json_encode(["message" => "Phone number must not be empty"]);
        } else {
            $username = "%" . $_GET['phoneNumber'] . "%";
            $stmt = $conn->prepare("SELECT * FROM admin WHERE phoneNumber LIKE ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }

// ===== PUT =====
} elseif ($method == 'PUT') {
    if (!is_array($input)) {
        echo json_encode(["message" => "Data harus berupa array JSON dengan daftar admin yang ingin diperbarui"]);
        exit;
    }

    $allowedFields = ['firstName', 'lastName', 'username', 'email', 'dateOfBirth', 'phoneNumber', 'password'];
    $updatedAdmins = [];
    $failedAdmins = [];

    foreach ($input as $admin) {
        if (!isset($admin['adminID'])) {
            $failedAdmins[] = ["adminID" => null, "message" => "admin ID tidak ditemukan"];
            continue;
        }

        $adminID = $admin['adminID'];

        // Validasi keberadaan admin
        $stmt = $conn->prepare("SELECT adminID FROM admin WHERE adminID = ?");
        $stmt->bind_param("i", $adminID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedAdmins[] = ["id" => $adminID, "message" => "Admin ID tidak ditemukan"];
            continue;
        }

        // Validasi format dateOfBirth
        if (isset($admin['dateOfBirth']) && !DateTime::createFromFormat('Y-m-d', $admin['dateOfBirth'])) {
            $failedAdmins[] = ["id" => $adminID, "message" => "Format dateOfBirth tidak valid (harus YYYY-MM-DD)"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($admin[$field])) {
                $fieldsToUpdate[] = "$field = ?";
                $types .= is_numeric($admin[$field]) && $field != 'phoneNumber' ? 'i' : 's';
                $values[] = $admin[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedAdmins[] = ["id" => $adminID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i';
        $values[] = $adminID;

        $sql = "UPDATE admin SET " . implode(", ", $fieldsToUpdate) . " WHERE adminID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedAdmins[] = ["message" => "Data admin dengan ID $adminID berhasil diperbarui"];
        } else {
            $failedAdmins[] = ["id" => $adminID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    // Buat response dinamis
    $response = [];
    if (!empty($updatedAdmins)) {
        $response["updated"] = $updatedAdmins;
    }

    if (!empty($failedAdmins)) {
        $response["failed"] = $failedAdmins;
    }

    echo json_encode($response);
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>