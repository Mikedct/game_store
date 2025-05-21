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
    if (isset($input['id'])) {
        $adminID = $input['id'];

        // Cek apakah ID ada
        $stmt = $conn->prepare("SELECT adminID FROM admin WHERE adminID = ?");
        $stmt->bind_param("i", $adminID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo json_encode(["message" => "Admin ID tidak ditemukan"]);
            exit;
        }

        // Validasi dateOfBirth jika dikirim
        if (isset($input['dateOfBirth']) && !DateTime::createFromFormat('Y-m-d', $input['dateOfBirth'])) {
            echo json_encode(["message" => "Format dateOfBirth tidak valid (harus YYYY-MM-DD)"]);
            exit;
        }

        // Daftar field yang boleh diupdate
        $allowedFields = ['firstName', 'lastName', 'username', 'email', 'dateOfBirth', 'phoneNumber', 'password'];
        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $fieldsToUpdate[] = "$field = ?";
                $types .= is_numeric($input[$field]) ? 'i' : 's';
                $values[] = $input[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            echo json_encode(["message" => "Tidak ada data yang dikirim untuk diperbarui"]);
            exit;
        }

        // Tambahkan adminID untuk WHERE
        $types .= 'i';
        $values[] = $adminID;

        $sql = "UPDATE admin SET " . implode(", ", $fieldsToUpdate) . " WHERE adminID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Data admin berhasil diperbarui", "id" => $adminID]);
        } else {
            echo json_encode(["message" => "Gagal memperbarui data", "error" => $stmt->error]);
        }
    } else {
        echo json_encode(["message" => "Parameter ID wajib diisi"]);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>