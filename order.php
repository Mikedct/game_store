<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

if ($method == 'GET') {
    if (isset($_GET['orderID'])) {
        if ($_GET['orderID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM order Where orderID=$_GET[orderID]");
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
            $stmt = $conn->prepare("SELECT * FROM order WHERE userID LIKE ?");
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
            $stmt = $conn->prepare("SELECT * FROM order WHERE username LIKE ?");
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
            $stmt = $conn->prepare("SELECT * FROM order WHERE orderID LIKE ?");
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
            $stmt = $conn->prepare("SELECT * FROM order WHERE title LIKE ?");
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM order");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }

// ===== POST Order =====
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userID = $_POST['userID'];
    $gameID = $_POST['gameID'];
    $status = $_POST['status'];

    $sql = "INSERT INTO `order` (orderID, userID, gameID, orderDate, status)
            VALUES (NULL, $userID, $gameID, NOW(), '$status')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Order berhasil ditambahkan"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}


// ===== PUT Order =====
elseif ($method == 'PUT') {
    if (isset($input['id'])) {
        $orderID = $input['id'];

        // Cek apakah ID ada
        $stmt = $conn->prepare("SELECT orderID FROM order WHERE orderID = ?");
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo json_encode(["message" => "order ID tidak ditemukan"]);
            exit;
        }

        // Validasi orderDate jika dikirim
        if (isset($input['orderDate']) && !DateTime::createFromFormat('Y-m-d', $input['orderDate'])) {
            echo json_encode(["message" => "Format orderDate tidak valid (harus YYYY-MM-DD)"]);
            exit;
        }

        // Daftar field yang boleh diupdate
        $allowedFields = ['userID', 'username', 'title', 'paymentID', 'totalPrice', 'orderDate'];
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

        // Tambahkan orderID untuk WHERE
        $types .= 'i';
        $values[] = $orderID;

        $sql = "UPDATE order SET " . implode(", ", $fieldsToUpdate) . " WHERE orderID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Data order berhasil diperbarui", "id" => $orderID]);
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