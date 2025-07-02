<?php
header(header: "Content-Type: application/json");
include "config.php";
require "middleware.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

$user=verifyJWT();

if ($method == 'GET') {
    if (isset($_GET['orderID'])) {
        if ($_GET['orderID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM `order` Where orderID=$_GET[orderID]");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['userID'])) {
        if ($_GET['userID'] == "") {
            echo json_encode(["message" => "User ID must not be empty"]);
        } else {
            $userID = "%" . $_GET['userID'] . "%";
            $stmt = $conn->prepare("SELECT * FROM `order` WHERE userID LIKE ?");
            $stmt->bind_param("s", $userID);
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
            $stmt = $conn->prepare("SELECT * FROM `order` WHERE username LIKE ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['orderID'])) {
        if ($_GET['orderID'] == "") {
            echo json_encode(["message" => "order ID must not be empty"]);
        } else {
            $orderID = "%" . $_GET['orderID'] . "%";
            $stmt = $conn->prepare("SELECT * FROM `order` WHERE orderID LIKE ?");
            $stmt->bind_param("s", $orderID);
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
            $stmt = $conn->prepare("SELECT * FROM `order` WHERE title LIKE ?");
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM `order`");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }
}
// ===== Insert order ====
elseif ($method == "POST") {
    // Validasi field
    $requiredFields = ['userID', 'username', 'gameID', 'title', 'paymentID', 'totalPrice', 'orderDate'];
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
    $userIDInput    = trim($input['userID']);
    $username       = trim($input['username']);
    $gameIDInput       = trim($input['gameID']);
    $title    = trim($input['title']);
    $paymentIDInput   = trim($input['paymentID']);;
    $priceInput  = trim($input['totalPrice']);
    $orderDate = trim($input['orderDate']);

    $priceInput = trim($input['totalPrice']);
    if (!is_numeric($priceInput) || floatval($priceInput) < 0) {
        echo json_encode(["status" => "error", "message" => "totalPrice harus berupa angka >= 0."]);
        exit;
    }
    $totalPrice = floatval($priceInput);

     // Validasi userID
    if (!is_numeric($userIDInput)) {
        echo json_encode(["status" => "error", "message" => "userID harus berupa angka."]);
        exit;
    }
    $userID = intval($userIDInput);

    // Validasi gameID
    if (!is_numeric($gameIDInput)) {
        echo json_encode(["status" => "error", "message" => "gameID harus berupa angka."]);
        exit;
    }
    $gameID = intval($gameIDInput);

    // Validasi paymentID
    if (!is_numeric($paymentIDInput)) {
        echo json_encode(["status" => "error", "message" => "paymentID harus berupa angka."]);
        exit;
    }
    $paymentID = intval($paymentIDInput);

    // Validasi tanggal
    $dateObj = DateTime::createFromFormat('Y-m-d', $orderDate);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $orderDate) {
        echo json_encode(["status" => "error", "message" => "Format tanggal salah. Gunakan format YYYY-MM-DD"]);
        exit;
    }

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO `order` (`userID`, `username`, `gameID`, `title`, `paymentID`, `totalPrice`, `orderDate`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isisiis", $userID, $username, $gameID, $title, $paymentID, $totalPrice, $orderDate);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "order berhasil ditambahkan", "orderID" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan order", "error" => $stmt->error]);
    }
}

// ===== PUT Game=====
elseif($method == 'PUT') {
    if (isset($input['orderID'])) {
        $input = [$input];
    }

    $allowedFields = ['userID', 'username', 'gameID', 'title', 'paymentID', 'totalPrice', 'orderDate'];
    $updatedorders = [];
    $failedorders = [];

    foreach ($input as $order) {
        if (!isset($order['orderID'])) {
            $failedorders[] = ["orderID" => null, "message" => "orderID tidak ditemukan"];
            continue;
        }

        $orderID = $order['orderID'];

        // Cek order
        $stmt = $conn->prepare("SELECT orderID FROM `order` WHERE orderID = ?");
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedorders[] = ["orderID" => $orderID, "message" => "order ID tidak ditemukan"];
            continue;
        }

        // Validasi orderDate
        if (isset($order['orderDate']) && !DateTime::createFromFormat('Y-m-d', $order['orderDate'])) {
            $failedorders[] = ["orderID" => $orderID, "message" => "Format orderDate tidak valid (harus YYYY-MM-DD)"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($order[$field])) {
                $fieldsToUpdate[] = "$field = ?";

                // Tentukan tipe parameter
                if (in_array($field, ['username', 'title'])) {
                    $types .= 's'; // string
                } else if ($field === 'orderDate') {
                    $types .= 's'; // tanggal juga string
                } else {
                    $types .= 'i'; // selain itu integer
                }

                $values[] = $order[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedorders[] = ["orderID" => $orderID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i';
        $values[] = $orderID;

        $sql = "UPDATE `order` SET " . implode(", ", $fieldsToUpdate) . " WHERE orderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedorders[] = ["message" => "Data order dengan ID $orderID berhasil diperbarui"];
        } else {
            $failedorders[] = ["orderID" => $orderID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    $response = [];
    if (!empty($updatedorders)) {
        $response["updated"] = $updatedorders;
    }
    if (!empty($failedorders)) {
        $response["failed"] = $failedorders;
    }

    echo json_encode($response);
} 

// ===== Delete order =====
elseif ($method == 'DELETE') {
    $orderID = $input['orderID'] ?? null;

    if (empty($orderID)) {
        echo json_encode(["message" => "order ID wajib diisi untuk menghapus data."]);
        exit;
    }

    if (!filter_var($orderID, FILTER_VALIDATE_INT)) {
        echo json_encode(["message" => "order ID tidak valid."]);
        exit;
    }

    $stmt_check = $conn->prepare("SELECT orderID FROM `order` WHERE orderID = ?");
    $stmt_check->bind_param("i", $orderID);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        echo json_encode(["message" => "order ID tidak ditemukan."]);
        exit;
    }

    $stmt_delete = $conn->prepare("DELETE FROM `order` WHERE orderID = ?");
    $stmt_delete->bind_param("i", $orderID);

    if ($stmt_delete->execute()) {
        echo json_encode(["message" => "order berhasil dihapus.", "id" => $orderID]);
    } else {
        echo json_encode(["message" => "Gagal menghapus order.", "error" => $stmt_delete->error]);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>