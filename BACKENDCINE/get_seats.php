<?php
require 'koneksi.php';
header('Content-Type: application/json');

// Matikan error HTML
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie = $_POST['movie_title'];

    try {
        // Ambil semua kursi untuk film ini (PDO Style)
        $stmt = $pdo->prepare("SELECT seat_number FROM booked_seats WHERE movie_title = ?");
        $stmt->execute([$movie]);
        
        $booked = array();
        while ($row = $stmt->fetch()) {
            $booked[] = $row['seat_number']; // Hasil: ["A1", "A2"]
        }

        echo json_encode([
            "success" => true,
            "booked_seats" => $booked
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "success" => false, 
            "message" => "Error: " . $e->getMessage()
        ]);
    }
}
?>