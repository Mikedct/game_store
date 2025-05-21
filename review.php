<?php
header(header: "Content-Type: application/json");
include "config.php";

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

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
    } else if (isset($_GET['title'])) {
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

// ===== PUT Review =====
} elseif ($method == 'PUT') {
    if (isset($input['id'])) {
        $reviewID = $input['id'];

        // Cek apakah ID ada
        $stmt = $conn->prepare("SELECT reviewID FROM review WHERE reviewID = ?");
        $stmt->bind_param("i", $reviewID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo json_encode(["message" => "review ID tidak ditemukan"]);
            exit;
        }

        // Validasi Date jika dikirim
        if (isset($input['Date']) && !DateTime::createFromFormat('Y-m-d', $input['Date'])) {
            echo json_encode(["message" => "Format Date tidak valid (harus YYYY-MM-DD)"]);
            exit;
        }

        // Daftar field yang boleh diupdate
        $allowedFields = ['userID', 'username', 'gameID', 'title', 'Text', 'Rating', 'Date'];
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

        // Tambahkan reviewID untuk WHERE
        $types .= 'i';
        $values[] = $reviewID;

        $sql = "UPDATE review SET " . implode(", ", $fieldsToUpdate) . " WHERE reviewID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Data review berhasil diperbarui", "id" => $reviewID]);
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