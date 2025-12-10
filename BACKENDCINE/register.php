<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // 1. Cek apakah email sudah ada?
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            echo json_encode(["success" => false, "message" => "Email sudah terdaftar!"]);
        } else {
            // 2. Enkripsi Password (Hashing)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 3. Simpan ke Database
            $stmtInsert = $pdo->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            
            if ($stmtInsert->execute([$nama, $email, $hashed_password])) {
                echo json_encode(["success" => true, "message" => "Berhasil mendaftar! Silakan Login."]);
            } else {
                echo json_encode(["success" => false, "message" => "Gagal menyimpan data."]);
            }
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>