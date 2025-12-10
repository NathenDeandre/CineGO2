<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    try {
        // Ambil riwayat berdasarkan email (PDO)
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_email = ? ORDER BY id DESC");
        $stmt->execute([$email]);
        $history = $stmt->fetchAll();

        echo json_encode([
            "success" => true,
            "message" => "Data ditemukan",
            "history" => $history
        ]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>