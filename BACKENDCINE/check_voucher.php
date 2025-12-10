<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['voucher_code'];
    $today = date('Y-m-d');

    try {
        // Cek kode voucher (PDO Style)
        $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE code = ? AND status = 'ACTIVE' AND valid_until >= ?");
        $stmt->execute([$code, $today]);
        $voucher = $stmt->fetch();

        if ($voucher) {
            echo json_encode([
                "success" => true,
                "message" => "Voucher Valid!",
                "discount" => (int)$voucher['discount_amount']
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Voucher Tidak Valid / Kadaluarsa"
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
?>