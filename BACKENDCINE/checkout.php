<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $movie = $_POST['movie_title'];
    $price = $_POST['total_price'];
    $method = $_POST['payment_method'];
    $seats  = $_POST['seats']; // String "A1,A2"

    try {
        // 1. MULAI TRANSAKSI (Kunci Database Sementara)
        $pdo->beginTransaction();

        // 2. CEK KETERSEDIAAN KURSI (PENTING!)
        // Kita pecah dulu string kursinya
        $seat_list = explode(",", $seats);
        
        // Loop untuk mengecek setiap kursi yang mau dibeli
        foreach ($seat_list as $seat) {
            $seat = trim($seat);
            if (!empty($seat)) {
                // Cek apakah kursi ini SUDAH ADA di tabel booked_seats untuk film ini?
                // Kita gunakan "FOR UPDATE" untuk mengunci baris (jika perlu tingkat lanjut), 
                // tapi Select biasa sudah cukup untuk validasi dasar.
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM booked_seats WHERE movie_title = ? AND seat_number = ?");
                $stmtCheck->execute([$movie, $seat]);
                $count = $stmtCheck->fetchColumn();

                if ($count > 0) {
                    // BAHAYA: Kursi sudah dibeli orang lain duluan!
                    // Batalkan semua proses!
                    $pdo->rollBack();
                    echo json_encode(["success" => false, "message" => "Gagal! Kursi $seat baru saja dibeli orang lain."]);
                    exit; // Stop program
                }
            }
        }

        // 3. JIKA KURSI AMAN, SIMPAN TRANSAKSI UTAMA
        $stmtTrans = $pdo->prepare("INSERT INTO transactions (user_email, movie_title, total_price, payment_method) VALUES (?, ?, ?, ?)");
        $stmtTrans->execute([$email, $movie, $price, $method]);

        // 4. SIMPAN KURSI KE BOOKED_SEATS
        $stmtSeat = $pdo->prepare("INSERT INTO booked_seats (movie_title, seat_number) VALUES (?, ?)");
        
        foreach ($seat_list as $seat) {
            $seat = trim($seat);
            if (!empty($seat)) {
                $stmtSeat->execute([$movie, $seat]);
            }
        }

        // 5. SEMUA BERHASIL -> COMMIT (Simpan Permanen)
        $pdo->commit();

        echo json_encode(["success" => true, "message" => "Pembayaran Berhasil!"]);

    } catch (Exception $e) {
        // Jika ada error apapun (misal koneksi putus di tengah jalan)
        $pdo->rollBack(); // Batalkan semua perubahan
        echo json_encode(["success" => false, "message" => "Error Server: " . $e->getMessage()]);
    }
}
?>