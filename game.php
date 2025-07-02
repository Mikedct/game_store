<?php
header(header: "Content-Type: application/json");
include "config.php";
require "middleware.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

$user = verifyJWT();

if ($method == 'GET') {
    if (isset($_GET['gameID'])) {
        if ($_GET['gameID'] == "") {
            echo json_encode(["message" => "ID must not be empty"]);
        } else {
            $stmt = $conn->prepare("SELECT * FROM game Where gameID=$_GET[gameID]");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['gameCode'])) {
        if ($_GET['gameCode'] == "") {
            echo json_encode(["message" => "Game code must not be empty"]);
        } else {
            $gameCode = "%" . $_GET['gameCode'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE gameCode LIKE ?");
            $stmt->bind_param("s", $gameCode);
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
            $stmt = $conn->prepare("SELECT * FROM game WHERE title LIKE ?");
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['genre'])) {
        if ($_GET['genre'] == "") {
            echo json_encode(["message" => "Genre must not be empty"]);
        } else {
            $genre = "%" . $_GET['genre'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE genre LIKE ?");
            $stmt->bind_param("s", $genre);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['developer'])) {
        if ($_GET['developer'] == "") {
            echo json_encode(["message" => "Developer must not be empty"]);
        } else {
            $developer = "%" . $_GET['developer'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE developer LIKE ?");
            $stmt->bind_param("s", $developer);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['publisher'])) {
        if ($_GET['publisher'] == "") {
            echo json_encode(["message" => "Publisher must not be empty"]);
        } else {
            $publisher = "%" . $_GET['publisher'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE publisher LIKE ?");
            $stmt->bind_param("s", $publisher);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else if (isset($_GET['platform'])) {
        if ($_GET['platform'] == "") {
            echo json_encode(["message" => "platform must not be empty"]);
        } else {
            $platform = "%" . $_GET['platform'] . "%";
            $stmt = $conn->prepare("SELECT * FROM game WHERE platform LIKE ?");
            $stmt->bind_param("s", $platform);
            $stmt->execute();
            $result = $stmt->get_result();
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM game");
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($users);
    }
}

// ===== Insert game ====
elseif ($method == "POST") {
    // Validasi field
    $requiredFields = ['gameCode', 'title', 'genre', 'platform', 'price', 'releaseDate', 'developer', 'publisher', 'description','image','videolink','adminID'];
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
    $gameCode    = trim($input['gameCode']);
    $title       = trim($input['title']);
    $genre       = trim($input['genre']);
    $platform    = trim($input['platform']);
    $priceInput  = trim($input['price']);
    $releaseDate = trim($input['releaseDate']);
    $developer   = trim($input['developer']);
    $publisher   = trim($input['publisher']);
    $description = trim($input['description']);
    $image = trim($input['image']);
    $videolink = trim($input['videolink']);
    $adminIDInput= trim($input['adminID']);

    // Validasi price: angka >= 0 atau string "free"
    if (strtolower($priceInput) === "free") {
        $price = 0.0;
    } elseif (is_numeric($priceInput) && floatval($priceInput) >= 0) {
        $price = floatval($priceInput);
    } else {
        echo json_encode(["status" => "error", "message" => "Harga tidak valid. Gunakan angka positif atau 'free'."]);
        exit;
    }

    // Validasi adminID
    if (!is_numeric($adminIDInput)) {
        echo json_encode(["status" => "error", "message" => "adminID harus berupa angka."]);
        exit;
    }
    $adminID = intval($adminIDInput);

    // Validasi tanggal
    $dateObj = DateTime::createFromFormat('Y-m-d', $releaseDate);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $releaseDate) {
        echo json_encode(["status" => "error", "message" => "Format tanggal salah. Gunakan format YYYY-MM-DD"]);
        exit;
    }

    // Cek duplikat
    $checkStmt = $conn->prepare("SELECT gameID FROM game WHERE gameCode = ? OR title = ?");
    $checkStmt->bind_param("ss", $gameCode, $title);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Game dengan kode atau judul ini sudah ada."]);
        exit;
    }

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO game (gameCode, title, genre, platform, price, releaseDate, developer, publisher, description, image, videolink, adminID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdssssssi", $gameCode, $title, $genre, $platform, $price, $releaseDate, $developer, $publisher, $description, $image, $videolink, $adminID);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Game berhasil ditambahkan", "gameID" => $stmt->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan game", "error" => $stmt->error]);
    }
}

// ===== PUT Game=====
else if($method == 'PUT') {
    if (isset($input['gameID'])) {
        $input = [$input];
    }

    $allowedFields = ['gameCode', 'title', 'genre', 'platform', 'price', 'releaseDate', 'developer', 'publisher', 'description', 'image','videolink', 'adminID'];
    $updatedgames = [];
    $failedgames = [];

    foreach ($input as $game) {
        if (!isset($game['gameID'])) {
            $failedgames[] = ["gameID" => null, "message" => "gameID tidak ditemukan"];
            continue;
        }

        $gameID = $game['gameID'];

        // Cek game
        $stmt = $conn->prepare("SELECT gameID FROM game WHERE gameID = ?");
        $stmt->bind_param("i", $gameID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $failedgames[] = ["gameID" => $gameID, "message" => "game ID tidak ditemukan"];
            continue;
        }

        // Validasi releaseDate
        if (isset($game['releaseDate']) && !DateTime::createFromFormat('Y-m-d', $game['releaseDate'])) {
            $failedgames[] = ["gameID" => $gameID, "message" => "Format releaseDate tidak valid (harus YYYY-MM-DD)"];
            continue;
        }

        $fieldsToUpdate = [];
        $types = '';
        $values = [];

        foreach ($allowedFields as $field) {
            if (isset($game[$field])) {
                $fieldsToUpdate[] = "$field = ?";
                // Tentukan tipe data (string 's' atau double 'd' jika perlu)
                $types .= is_numeric($game[$field]) && $field !== 'gameCode' ? 'd' : 's'; 
                $values[] = $game[$field];
            }
        }

        if (empty($fieldsToUpdate)) {
            $failedgames[] = ["gameID" => $gameID, "message" => "Tidak ada field yang dikirim"];
            continue;
        }

        $types .= 'i';
        $values[] = $gameID;

        $sql = "UPDATE game SET " . implode(", ", $fieldsToUpdate) . " WHERE gameID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $updatedgames[] = ["message" => "Data game dengan ID $gameID berhasil diperbarui"];
        } else {
            $failedgames[] = ["gameID" => $gameID, "message" => "Gagal memperbarui data", "error" => $stmt->error];
        }
    }

    $response = [];
    if (!empty($updatedgames)) {
        $response["updated"] = $updatedgames;
    }
    if (!empty($failedgames)) {
        $response["failed"] = $failedgames;
    }

    echo json_encode($response);
} 

// ===== Delete game =====
elseif ($method == 'DELETE') {
    $gameID = $input['gameID'] ?? null;

    if (empty($gameID)) {
        echo json_encode(["message" => "game ID wajib diisi untuk menghapus data."]);
        exit;
    }

    if (!filter_var($gameID, FILTER_VALIDATE_INT)) {
        echo json_encode(["message" => "game ID tidak valid."]);
        exit;
    }

    $stmt_check = $conn->prepare("SELECT gameID FROM game WHERE gameID = ?");
    $stmt_check->bind_param("i", $gameID);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        echo json_encode(["message" => "game ID tidak ditemukan."]);
        exit;
    }

    $stmt_delete = $conn->prepare("DELETE FROM game WHERE gameID = ?");
    $stmt_delete->bind_param("i", $gameID);

    if ($stmt_delete->execute()) {
        echo json_encode(["message" => "game berhasil dihapus.", "id" => $gameID]);
    } else {
        echo json_encode(["message" => "Gagal menghapus game.", "error" => $stmt_delete->error]);
    }
} else {
    echo json_encode(["message" => "Method not authorized"]);
}
?>