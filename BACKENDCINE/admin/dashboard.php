<?php
session_start();
require '../koneksi.php';

// Cek Sesi Login Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

// 1. HITUNG TOTAL PEMASUKAN
$stmtIncome = $pdo->query("SELECT SUM(total_price) as total FROM transactions");
$income = $stmtIncome->fetch()['total'];

// 2. HITUNG TOTAL TIKET TERJUAL
$stmtCount = $pdo->query("SELECT COUNT(*) as jumlah FROM transactions");
$count = $stmtCount->fetch()['jumlah'];

// 3. LAPORAN PER FILM (Film Terlaris)
$stmtReport = $pdo->query("
    SELECT movie_title, COUNT(*) as jumlah_transaksi, SUM(total_price) as total_uang 
    FROM transactions 
    GROUP BY movie_title 
    ORDER BY jumlah_transaksi DESC
");

// 4. DAFTAR TRANSAKSI TERAKHIR
$stmtTrans = $pdo->query("SELECT * FROM transactions ORDER BY id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">🎬 CINEMA ADMIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item">
                    <a class="nav-link active fw-bold" href="dashboard.php">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="vouchers.php">Kelola Voucher</a>
                </li>

                <li class="nav-item ms-3 text-white-50">|</li>

                <li class="nav-item">
                    <span class="nav-link text-white">Halo, <b><?= htmlspecialchars($_SESSION['admin_name']) ?></b></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger btn-sm ms-3" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    
    <h3 class="mb-4">Dashboard Ringkasan</h3>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3 shadow-sm">
                <div class="card-header">Total Pemasukan</div>
                <div class="card-body">
                    <h2 class="card-title">Rp <?= number_format($income, 0, ',', '.') ?></h2>
                    <p class="card-text">Akumulasi dari seluruh transaksi berhasil.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3 shadow-sm">
                <div class="card-header">Total Tiket Terjual</div>
                <div class="card-body">
                    <h2 class="card-title"><?= $count ?> Tiket</h2>
                    <p class="card-text">Jumlah transaksi yang tercatat di sistem.</p>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-warning text-dark fw-bold">
            🔥 Laporan Penjualan per Film
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Judul Film</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $stmtReport->fetch()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['movie_title']) ?></td>
                            <td class="text-center fw-bold"><?= $row['jumlah_transaksi'] ?></td>
                            <td>Rp <?= number_format($row['total_uang'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white fw-bold">
            🕒 10 Transaksi Terakhir
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email User</th>
                            <th>Film</th>
                            <th>Metode</th>
                            <th>Total</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($t = $stmtTrans->fetch()): ?>
                        <tr>
                            <td>#<?= $t['id'] ?></td>
                            <td><?= htmlspecialchars($t['user_email']) ?></td>
                            <td><?= htmlspecialchars($t['movie_title']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= $t['payment_method'] ?></span></td>
                            <td>Rp <?= number_format($t['total_price'], 0, ',', '.') ?></td>
                            <td><?= $t['created_at'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<footer class="text-center py-4 text-muted mt-5">
    &copy; 2025 Cinema Booking Admin Panel
</footer>

</body>
</html>