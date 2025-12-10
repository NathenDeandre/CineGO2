<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Email sebagai kunci (ID)
    $nama_baru = $_POST['nama'];

    try {
        // Update Nama berdasarkan Email
        $stmt = $pdo->prepare("UPDATE users SET nama = ? WHERE email = ?");
        
        if ($stmt->execute([$nama_baru, $email])) {
            echo json_encode([
                "success" => true, 
                "message" => "Profil berhasil diperbarui!",
                "data" => ["nama" => $nama_baru] // Kirim balik nama baru
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal update database."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>