<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password']; // Pastikan di Android kirimnya 'password' sesuai tutorial sebelumnya

    // 1. Cek apakah email & password benar
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        
        // 2. Jika benar, JANGAN login dulu. Buat kode OTP.
        $otp = rand(100000, 999999); // Angka acak 6 digit
        
        // 3. Simpan OTP ke database user tersebut
        $update = "UPDATE users SET otp_code = '$otp' WHERE email = '$email'";
        mysqli_query($con, $update);

        // 4. Beri tahu Android untuk pindah ke halaman verifikasi
        // NOTE: "otp_simulasi" dikirim agar kamu bisa tes tanpa kirim email asli dulu.
        echo json_encode([
            "success" => true, 
            "message" => "Silakan masukkan kode verifikasi",
            "otp_simulasi" => $otp 
        ]);

    } else {
        echo json_encode(["success" => false, "message" => "Email atau Password salah!"]);
    }
}
?>