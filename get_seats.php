<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie = $_POST['movie_title'];

    // Ambil semua kursi yang sudah dipesan untuk film ini
    $sql = "SELECT seat_number FROM booked_seats WHERE movie_title = '$movie'";
    $result = mysqli_query($con, $sql);
    
    $booked = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $booked[] = $row['seat_number']; // Hasil: ["A1", "A2", "C3"]
    }

    echo json_encode([
        "success" => true,
        "booked_seats" => $booked
    ]);
}
?>