<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];

    try {
        // 1. Ambil password lama dari database
        $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // 2. Cek apakah password lama yang diketik USER cocok dengan DATABASE
            if (password_verify($old_pass, $user['password'])) {
                
                // 3. Jika cocok, Hash password baru
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                
                // 4. Update ke database
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                
                if ($updateStmt->execute([$new_hash, $email])) {
                    echo json_encode(["success" => true, "message" => "Password berhasil diganti!"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Gagal update password."]);
                }

            } else {
                echo json_encode(["success" => false, "message" => "Password Lama Salah!"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "User tidak ditemukan."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>