<?php
// 1. Include connection file first!
require 'koneksi.php'; 

// 2. Set Header to JSON
header('Content-Type: application/json');

// 3. Disable error reporting to prevent HTML warnings breaking JSON
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if variables exist to prevent warnings
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $otp_input = isset($_POST['otp_code']) ? $_POST['otp_code'] : '';

    if (empty($email) || empty($otp_input)) {
        echo json_encode(["success" => false, "message" => "Email atau OTP tidak boleh kosong!"]);
        exit;
    }

    // Prepare SQL Statement (PDO)
    try {
        // Cek kecocokan Email dan OTP
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND otp_code = ?");
        $stmt->execute([$email, $otp_input]);
        $user = $stmt->fetch();

        if ($user) {
            // Jika cocok, Hapus OTP (Reset jadi NULL)
            $updateStmt = $pdo->prepare("UPDATE users SET otp_code = NULL WHERE email = ?");
            $updateStmt->execute([$email]);

            echo json_encode([
                "success" => true, 
                "message" => "Login Sukses!",
                "data" => $user
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Kode Verifikasi Salah!"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error Server: " . $e->getMessage()]);
    }
}
?>