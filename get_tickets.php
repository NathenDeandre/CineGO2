<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Query dengan Filter WHERE user_email = ...
    // ORDER BY id DESC artinya tiket terbaru muncul paling atas
    $sql = "SELECT * FROM transactions WHERE user_email = '$email' ORDER BY id DESC";
    
    $result = mysqli_query($con, $sql);
    $history = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $history[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "Data ditemukan",
        "history" => $history // Mengirim LIST tiket
    ]);
}
?>