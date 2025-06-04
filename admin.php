<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

// ===== Get Admin ====
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
}

// ===== Insert Admin ====
elseif ($method == "POST") {
    // Validasi field
    $requiredFields = ['firstName', 'lastName', 'username', 'email', 'dateOfBirth', 'phoneNumber', 'password'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        echo json_encode(["status" => "error", "message" => "Field wajib: " . implode(', ', $missingFields)]);
        exit;
    }

    // Ambil data
    $firstName = trim($input['firstName']);
    $lastName = trim($input['lastName']);
    $username = trim($input['username']);
    $email = trim($input['email']);
    $dateOfBirth = trim($input['dateOfBirth']);
    $phoneNumber = trim($input['phoneNumber']);
    $password = md5(trim($input['password'])); // Bisa ganti pakai password_hash

    // Validasi tanggal
    if (!DateTime::createFromFormat('Y-m-d', $dateOfBirth)) {
        echo json_encode(["status" => "error", "message" => "Format tanggal salah. Gunakan format YYYY-MM-DD"]);
        exit;
    }

    // Cek duplikat
    $checkStmt = $conn->prepare("SELECT adminID FROM admin WHERE username = ? OR email = ?");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username atau email sudah digunakan."]);
        exit;
    }

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO admin (firstName, lastName, username, email, dateOfBirth, phoneNumber, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstName, $lastName, $username, $email, $dateOfBirth, $phoneNumber, $password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Admin berhasil ditambahkan", "adminID" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan admin", "error" => $stmt->error]);
    }
}

// ===== Update Admin =====
elseif ($method == 'PUT') {
    if (isset($input['adminID'])) {
        $input = [$input];
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

        // Cek apakah admin ID ada
        $stmt = $conn->prepare("SELECT adminID FROM admin WHERE adminID = ?");
        $stmt->bind_param("i", $adminID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedAdmins[] = ["adminID" => $adminID, "message" => "Admin ID tidak ditemukan"];
            continue;
        }

        // Validasi format tanggal
        if (isset($admin['dateOfBirth']) && !DateTime::createFromFormat('Y-m-d', $admin['dateOfBirth'])) {
            $failedAdmins[] = ["adminID" => $adminID, "message" => "Format dateOfBirth tidak valid (harus YYYY-MM-DD)"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($admin[$field])) {
                $fieldsToUpdate[] = "$field = ?";

                // Tentukan tipe dan nilai field
                if ($field === 'password') {
                    $types .= 's';
                    $values[] = md5($admin[$field]); // Hash password dengan md5
                } elseif (in_array($field, ['firstName', 'lastName', 'username', 'email', 'phoneNumber', 'dateOfBirth'])) {
                    $types .= 's';
                    $values[] = $admin[$field];
                } else {
                    $types .= 'i';
                    $values[] = $admin[$field];
                }
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedAdmins[] = ["adminID" => $adminID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i'; // untuk adminID di WHERE
        $values[] = $adminID;

        $sql = "UPDATE admin SET " . implode(", ", $fieldsToUpdate) . " WHERE adminID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedAdmins[] = ["adminID" => $adminID, "message" => "Data admin berhasil diperbarui"];
        } else {
            $failedAdmins[] = ["adminID" => $adminID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    $response = [];
    if (!empty($updatedAdmins)) {
        $response["updated"] = $updatedAdmins;
    }
    if (!empty($failedAdmins)) {
        $response["failed"] = $failedAdmins;
    }

    echo json_encode($response);
}

// ===== Delete admin =====
elseif ($method == 'DELETE') {
    $adminID = $input['adminID'] ?? null;

    if (empty($adminID)) {
        echo json_encode(["message" => "Admin ID wajib diisi untuk menghapus data."]);
        exit;
    }

    if (!filter_var($adminID, FILTER_VALIDATE_INT)) {
        echo json_encode(["message" => "Admin ID tidak valid."]);
        exit;
    }

    $stmt_check = $conn->prepare("SELECT adminID FROM admin WHERE adminID = ?");
    $stmt_check->bind_param("i", $adminID);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        echo json_encode(["message" => "Admin ID tidak ditemukan."]);
        exit;
    }

    $stmt_delete = $conn->prepare("DELETE FROM admin WHERE adminID = ?");
    $stmt_delete->bind_param("i", $adminID);

    if ($stmt_delete->execute()) {
        echo json_encode(["message" => "Admin berhasil dihapus.", "id" => $adminID]);
    } else {
        echo json_encode(["message" => "Gagal menghapus admin.", "error" => $stmt_delete->error]);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>