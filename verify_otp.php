<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp_input = $_POST['otp_code'];

    // Cek apakah email dan otp cocok
    $sql = "SELECT * FROM users WHERE email = '$email' AND otp_code = '$otp_input'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Jika cocok, ambil data user
        $row = mysqli_fetch_assoc($result);
        
        // Hapus OTP agar tidak bisa dipakai lagi (Opsional, biar aman)
        mysqli_query($con, "UPDATE users SET otp_code = NULL WHERE email = '$email'");

        echo json_encode([
            "success" => true, 
            "message" => "Verifikasi Berhasil!",
            "data" => $row
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Kode Verifikasi Salah!"]);
    }
}
?>