<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $movie = $_POST['movie_title'];
    $price = $_POST['total_price'];
    $method = $_POST['payment_method'];
    $seats  = $_POST['seats']; // BARU: Terima string "A1,A2"

    // 1. Simpan Transaksi Utama
    $sql = "INSERT INTO transactions (user_email, movie_title, total_price, payment_method) 
            VALUES ('$email', '$movie', '$price', '$method')";

    if (mysqli_query($con, $sql)) {
        
        // 2. Simpan Kursi Satu per Satu ke tabel booked_seats
        // Pecah string "A1,A2" menjadi array
        $seat_list = explode(",", $seats); 
        
        foreach ($seat_list as $seat) {
            $seat = trim($seat); // Hapus spasi jika ada
            if (!empty($seat)) {
                $sql_seat = "INSERT INTO booked_seats (movie_title, seat_number) VALUES ('$movie', '$seat')";
                mysqli_query($con, $sql_seat);
            }
        }

        echo json_encode(["success" => true, "message" => "Pembayaran Berhasil!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal Transaksi"]);
    }
}
?>