<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek email duplikat
    $check = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $check);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["success" => false, "message" => "Email sudah terdaftar!"]);
    } else {
        // Simpan data (Password sebaiknya di-hash, tapi untuk belajar kita plain dulu)
        $sql = "INSERT INTO users (nama, email, password) VALUES ('$nama', '$email', '$password')";
        
        if (mysqli_query($con, $sql)) {
            echo json_encode(["success" => true, "message" => "Berhasil mendaftar!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal mendaftar!"]);
        }
    }
}
?>