<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

if ($method == 'GET') {
    if (isset($_GET['paymentID'])) {
        if ($_GET['paymentID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM payment Where paymentID=$_GET[paymentID]");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['paymentMethod'])) {
        if ($_GET['paymentMethod'] == "") {
            echo json_encode(["message" => "Payment method must not be empty"]);
        } else {
            $paymentMethod = "%" . $_GET['paymentMethod'] . "%";
            $stmt = $conn->prepare("SELECT * FROM payment WHERE paymentMethod LIKE ?");
            $stmt->bind_param("s", $paymentMethod);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['paymentStatus'])) {
        if ($_GET['paymentStatus'] == "") {
            echo json_encode(["message" => "Payment status must not be empty"]);
        } else {
            $paymentStatus = "%" . $_GET['paymentStatus'] . "%";
            $stmt = $conn->prepare("SELECT * FROM payment WHERE paymentStatus LIKE ?");
            $stmt->bind_param("s", $paymentStatus);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    }else {
        $stmt = $conn->prepare("SELECT * FROM payment");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }
}
// ===== POST =====
elseif ($method == "POST") {
    // Validasi field
    $requiredFields = ['paymentMethod', 'paymentStatus'];
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        echo json_encode(["status" => "error", "message" => "Field wajib: " . implode(', ', $missingFields)]);
        exit;
    }

    // Ambil dan bersihkan input
    $paymentMethod       = trim($input['paymentMethod']);
    $paymentStatus    = trim($input['paymentStatus']);


    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO `payment` (`paymentMethod`, `paymentStatus`) VALUES (?, ?)");
    $stmt->bind_param("ss", $paymentMethod, $paymentStatus);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "payment berhasil ditambahkan", "paymentID" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan payment", "error" => $stmt->error]);
    }
}

// ===== PUT Payment =====
else if($method == 'PUT') {
    if (isset($input['paymentID'])) {
        $input = [$input];
    }

    $allowedFields = ['paymentMethod', 'paymentStatus'];
    $updatedpayments = [];
    $failedpayments = [];

    foreach ($input as $payment) {
        if (!isset($payment['paymentID'])) {
            $failedpayments[] = ["paymentID" => null, "message" => "paymentID tidak ditemukan"];
            continue;
        }

        $paymentID = $payment['paymentID'];

        // Cek payment
        $stmt = $conn->prepare("SELECT paymentID FROM `payment` WHERE paymentID = ?");
        $stmt->bind_param("i", $paymentID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedpayments[] = ["paymentID" => $paymentID, "message" => "payment ID tidak ditemukan"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($payment[$field])) {
                $fieldsToUpdate[] = "$field = ?";

                // Tentukan tipe parameter
                if (in_array($field, ['paymentMethod', 'paymentStatus'])) {
                    $types .= 's'; // string
                } else {
                    $types .= 'i'; // selain itu integer
                }

                $values[] = $payment[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedpayments[] = ["paymentID" => $paymentID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i';
        $values[] = $paymentID;

        $sql = "UPDATE `payment` SET " . implode(", ", $fieldsToUpdate) . " WHERE paymentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedpayments[] = ["message" => "Data payment dengan ID $paymentID berhasil diperbarui"];
        } else {
            $failedpayments[] = ["paymentID" => $paymentID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    $response = [];
    if (!empty($updatedpayments)) {
        $response["updated"] = $updatedpayments;
    }
    if (!empty($failedpayments)) {
        $response["failed"] = $failedpayments;
    }

    echo json_encode($response);
} 
elseif ($method == 'DELETE') {
    $paymentID = $input['paymentID'] ?? null;

    if (empty($paymentID)) {
        echo json_encode(["message" => "payment ID wajib diisi untuk menghapus data."]);
        exit;
    }

    if (!filter_var($paymentID, FILTER_VALIDATE_INT)) {
        echo json_encode(["message" => "payment ID tidak valid."]);
        exit;
    }

    $stmt_check = $conn->prepare("SELECT paymentID FROM `payment` WHERE paymentID = ?");
    $stmt_check->bind_param("i", $paymentID);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        echo json_encode(["message" => "payment ID tidak ditemukan."]);
        exit;
    }

    $stmt_delete = $conn->prepare("DELETE FROM `payment` WHERE paymentID = ?");
    $stmt_delete->bind_param("i", $paymentID);

    if ($stmt_delete->execute()) {
        echo json_encode(["message" => "payment berhasil dihapus.", "id" => $paymentID]);
    } else {
        echo json_encode(["message" => "Gagal menghapus payment.", "error" => $stmt_delete->error]);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>