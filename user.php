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

// ===== Insert User ====
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
    $checkStmt = $conn->prepare("SELECT userID FROM users WHERE username = ? OR email = ?");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username atau email sudah digunakan."]);
        exit;
    }

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, username, email, dateOfBirth, phoneNumber, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstName, $lastName, $username, $email, $dateOfBirth, $phoneNumber, $password);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User berhasil ditambahkan", "userID" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan user", "error" => $stmt->error]);
    }
}

// ===== PUT user =====
elseif ($method == 'PUT') {
    if (isset($input['userID'])) {
        $input = [$input];
    }

    $allowedFields = ['firstName', 'lastName', 'username', 'email', 'dateOfBirth', 'phoneNumber', 'password'];
    $updatedusers = [];
    $failedusers = [];

    foreach ($input as $user) {
        if (!isset($user['userID'])) {
            $failedusers[] = ["userID" => null, "message" => "user ID tidak ditemukan"];
            continue;
        }

        $userID = $user['userID'];

        // Cek apakah user ID ada
        $stmt = $conn->prepare("SELECT userID FROM `users` WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedusers[] = ["userID" => $userID, "message" => "user ID tidak ditemukan"];
            continue;
        }

        // Validasi format tanggal
        if (isset($user['dateOfBirth']) && !DateTime::createFromFormat('Y-m-d', $user['dateOfBirth'])) {
            $failedusers[] = ["userID" => $userID, "message" => "Format dateOfBirth tidak valid (harus YYYY-MM-DD)"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($user[$field])) {
                $fieldsToUpdate[] = "$field = ?";

                // Tentukan tipe dan nilai field
                if ($field === 'password') {
                    $types .= 's';
                    $values[] = md5($user[$field]); // Hash password dengan md5
                } elseif (in_array($field, ['FirstName', 'LastName', 'Username', 'Email', 'phoneNumber', 'dateOfBirth'])) {
                    $types .= 's';
                    $values[] = $user[$field];
                } else {
                    $types .= 'i';
                    $values[] = $user[$field];
                }
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedusers[] = ["userID" => $userID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i'; // untuk userID di WHERE
        $values[] = $userID;

        $sql = "UPDATE `users` SET " . implode(", ", $fieldsToUpdate) . " WHERE userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedusers[] = ["message" => "Data user dengan ID $userID berhasil diperbarui"];
        } else {
            $failedusers[] = ["userID" => $userID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    $response = [];
    if (!empty($updatedusers)) {
        $response["updated"] = $updatedusers;
    }
    if (!empty($failedusers)) {
        $response["failed"] = $failedusers;
    }

    echo json_encode($response);
}

// ===== Delete user =====
elseif ($method == 'DELETE') {
    $userID = $input['userID'] ?? null;

    if (empty($userID)) {
        echo json_encode(["message" => "user ID wajib diisi untuk menghapus data."]);
        exit;
    }

    if (!filter_var($userID, FILTER_VALIDATE_INT)) {
        echo json_encode(["message" => "user ID tidak valid."]);
        exit;
    }

    $stmt_check = $conn->prepare("SELECT userID FROM users WHERE userID = ?");
    $stmt_check->bind_param("i", $userID);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        echo json_encode(["message" => "user ID tidak ditemukan."]);
        exit;
    }

    $stmt_delete = $conn->prepare("DELETE FROM users WHERE userID = ?");
    $stmt_delete->bind_param("i", $userID);

    if ($stmt_delete->execute()) {
        echo json_encode(["message" => "Data user dengan ID $userID berhasil dihapus"]);
    } else {
        echo json_encode(["message" => "Gagal menghapus user.", "error" => $stmt_delete->error]);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>