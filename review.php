<?php
header(header: "Content-Type: application/json");
include "config.php";
require "middleware.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

$user=verifyJWT();

if ($method == 'GET') {
    if (isset($_GET['reviewID'])) {
        if ($_GET['reviewID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM review Where reviewID=$_GET[reviewID]");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['gameID'])) {
        if ($_GET['gameID'] == "") {
            echo json_encode(["message" => "gameID must not be empty"]);
        } else {
            $gameID = "%" . $_GET['gameID'] . "%";
            $stmt = $conn->prepare("SELECT * FROM review WHERE gameID LIKE ?");
            $stmt->bind_param("s", $gameID);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['username'])) {
        if ($_GET['username'] == "") {
            echo json_encode(["message" => "username must not be empty"]);
        } else {
            $username = "%" . $_GET['username'] . "%";
            $stmt = $conn->prepare("SELECT * FROM review WHERE username LIKE ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    }else if (isset($_GET['title'])) {
        if ($_GET['title'] == "") {
            echo json_encode(["message" => "title must not be empty"]);
        } else {
            $title = "%" . $_GET['title'] . "%";
            $stmt = $conn->prepare("SELECT * FROM review WHERE title LIKE ?");
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['Rating'])) {
        if ($_GET['Rating'] == "") {
            echo json_encode(["message" => "Rating must not be empty"]);
        } else {
            $Rating = "%" . $_GET['Rating'] . "%";
            $stmt = $conn->prepare("SELECT * FROM review WHERE Rating LIKE ?");
            $stmt->bind_param("i", $Rating);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM review");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }
}

elseif ($method == "POST") {
    // Validasi field
    $requiredFields = ['userID', 'username', 'gameID', 'title', 'Text', 'Rating', 'Date'];
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
    $Text   = trim($input['Text']);;
    $Rating  = trim($input['Rating']);
    $Date = trim($input['Date']);

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

    // Validasi tanggal
    $dateObj = DateTime::createFromFormat('Y-m-d', datetime: $Date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $Date) {
        echo json_encode(["status" => "error", "message" => "Format tanggal salah. Gunakan format YYYY-MM-DD"]);
        exit;
    }

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO `review` (`userID`, `username`, `gameID`, `title`, `Text`, `Rating`, `Date`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isissis", $userID, $username, $gameID, $title, $Text, $Rating, $Date);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "review berhasil ditambahkan", "reviewID" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan review", "error" => $stmt->error]);
    }
}
// ===== PUT Review =====
else if($method == 'PUT') {
    if (isset($input['reviewID'])) {
        $input = [$input];
    }

    $allowedFields = ['userID', 'username', 'gameID', 'title', 'Text', 'Rating', 'Date'];
    $updatedreviews = [];
    $failedreviews = [];

    foreach ($input as $review) {
        if (!isset($review['reviewID'])) {
            $failedreviews[] = ["reviewID" => null, "message" => "reviewID tidak ditemukan"];
            continue;
        }

        $reviewID = $review['reviewID'];

        // Cek review
        $stmt = $conn->prepare("SELECT reviewID FROM `review` WHERE reviewID = ?");
        $stmt->bind_param("i", $reviewID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedreviews[] = ["reviewID" => $reviewID, "message" => "review ID tidak ditemukan"];
            continue;
        }

        // Validasi Date
        if (isset($order['Date']) && !DateTime::createFromFormat('Y-m-d', $order['Date'])) {
            $failedorders[] = ["orderID" => $orderID, "message" => "Format Date tidak valid (harus YYYY-MM-DD)"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($review[$field])) {
                $fieldsToUpdate[] = "$field = ?";

                // Tentukan tipe parameter
                if (in_array($field, ['username', 'title', 'Text'])) {
                    $types .= 's'; // string
                } else if ($field === 'Date') {
                    $types .= 's'; // tanggal juga string
                } else {
                    $types .= 'i'; // selain itu integer
                }

                $values[] = $review[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedreviews[] = ["reviewID" => $reviewID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i';
        $values[] = $reviewID;

        $sql = "UPDATE `review` SET " . implode(", ", $fieldsToUpdate) . " WHERE reviewID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedreviews[] = ["message" => "Data review dengan ID $reviewID berhasil diperbarui"];
        } else {
            $failedreviews[] = ["reviewID" => $reviewID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    $response = [];
    if (!empty($updatedreviews)) {
        $response["updated"] = $updatedreviews;
    }
    if (!empty($failedreviews)) {
        $response["failed"] = $failedreviews;
    }

    echo json_encode($response);
} 
// ===== Delete order =====
elseif ($method == 'DELETE') {
    $reviewID = $input['reviewID'] ?? null;

    if (empty($reviewID)) {
        echo json_encode(["message" => "review ID wajib diisi untuk menghapus data."]);
        exit;
    }

    if (!filter_var($reviewID, FILTER_VALIDATE_INT)) {
        echo json_encode(["message" => "review ID tidak valid."]);
        exit;
    }

    $stmt_check = $conn->prepare("SELECT reviewID FROM `review` WHERE reviewID = ?");
    $stmt_check->bind_param("i", $reviewID);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        echo json_encode(["message" => "review ID tidak ditemukan."]);
        exit;
    }

    $stmt_delete = $conn->prepare("DELETE FROM `review` WHERE reviewID = ?");
    $stmt_delete->bind_param("i", $reviewID);

    if ($stmt_delete->execute()) {
        echo json_encode(["message" => "review berhasil dihapus.", "id" => $reviewID]);
    } else {
        echo json_encode(["message" => "Gagal menghapus review.", "error" => $stmt_delete->error]);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>