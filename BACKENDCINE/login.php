<?php
require 'koneksi.php';
header('Content-Type: application/json');

// Matikan error display HTML agar JSON tidak rusak
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Cek apakah Android mengirim key 'password' atau 'pass'
    $password_input = isset($_POST['password']) ? $_POST['password'] : $_POST['pass'];

    try {
        // --- 1. CARI USER BERDASARKAN EMAIL (PDO) ---
        // Ini bagian yang kamu cari:
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Jika user ditemukan di database
        if ($user) {
            
            // --- 2. CEK PASSWORD (HASH) ---
            // Bandingkan password inputan dengan password acak di database
            if (password_verify($password_input, $user['password'])) {
                
                // --- 3. JIKA BENAR, BUAT OTP ---
                $otp = rand(100000, 999999);
                
                // Simpan OTP ke database user tersebut
                $updateStmt = $pdo->prepare("UPDATE users SET otp_code = ? WHERE email = ?");
                $updateStmt->execute([$otp, $email]);

                // Kirim OTP ke Android (Simulasi)
                echo json_encode([
                    "success" => true, 
                    "message" => "Verifikasi Berhasil",
                    "otp_simulasi" => $otp 
                ]);

            } else {
                // Password Salah
                echo json_encode(["success" => false, "message" => "Password salah!"]);
            }
        } else {
            // Email Tidak Ditemukan
            echo json_encode(["success" => false, "message" => "Email tidak terdaftar!"]);
        }

    } catch (Exception $e) {
        // Jika ada error database
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>