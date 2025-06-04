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
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderID = $_POST['orderID'];
    $method = $_POST['method'];
    $amount = $_POST['amount'];

    $sql = "INSERT INTO payment (paymentID, orderID, method, amount, paymentDate)
            VALUES (NULL, $orderID, '$method', $amount, NOW())";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Payment berhasil ditambahkan"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

// ===== PUT Payment =====
elseif ($method == 'PUT') {
    if (isset($input['id'])) {
        $paymentID = $input['id'];

        // Validasi: apakah payment dengan ID ini ada
        $stmt = $conn->prepare("SELECT paymentID FROM payment WHERE paymentID = ?");
        $stmt->bind_param("i", $paymentID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo json_encode(["message" => "payment ID tidak ditemukan"]);
            exit;
        }

        // Daftar field yang boleh diupdate
        $allowedFields = ['paymentMethod', 'paymentStatus'];
        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $fieldsToUpdate[] = "$field = ?";
                $types .= $field == 'dateOfBirth' ? 's' : (is_numeric($input[$field]) ? 'i' : 's');
                $values[] = $input[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            echo json_encode(["message" => "Tidak ada data yang dikirim untuk diperbarui"]);
            exit;
        }

        // Tambahkan paymentID untuk klausa WHERE
        $types .= 'i';
        $values[] = $paymentID;

        // Bangun query update dinamis
        $sql = "UPDATE payment SET " . implode(", ", $fieldsToUpdate) . " WHERE paymentID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Data payment berhasil diperbarui", "id" => $paymentID]);
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