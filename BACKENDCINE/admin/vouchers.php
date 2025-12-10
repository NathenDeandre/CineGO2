<?php
session_start();
require '../koneksi.php';

// Cek Login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

// --- LOGIC 1: TAMBAH VOUCHER BARU ---
if (isset($_POST['simpan_voucher'])) {
    $code = strtoupper($_POST['code']); // Paksa huruf besar
    $discount = $_POST['discount'];
    $valid_until = $_POST['valid_until'];

    try {
        $stmt = $pdo->prepare("INSERT INTO vouchers (code, discount_amount, valid_until) VALUES (?, ?, ?)");
        $stmt->execute([$code, $discount, $valid_until]);
        $success_msg = "Voucher berhasil ditambahkan!";
    } catch (Exception $e) {
        $error_msg = "Gagal menambah voucher (Mungkin kode sudah ada).";
    }
}

// --- LOGIC 2: HAPUS VOUCHER ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM vouchers WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: vouchers.php"); 
    exit;
}

// --- LOGIC 3: AMBIL DATA VOUCHER ---
$stmtList = $pdo->query("SELECT * FROM vouchers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Voucher - Cinema Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">🎬 CINEMA ADMIN</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">📊 Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active fw-bold" href="vouchers.php">🎟️ Voucher</a>
                </li>

                <li class="nav-item ms-3">
                    <span class="text-white-50 small">Halo, <b><?= htmlspecialchars($_SESSION['admin_name']) ?></b></span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger btn-sm ms-3" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>🎟️ Manajemen Voucher</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="fas fa-plus"></i> Tambah Voucher
        </button>
    </div>

    <?php if(isset($success_msg)): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php endif; ?>
    <?php if(isset($error_msg)): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode Voucher</th>
                        <th>Potongan (Rp)</th>
                        <th>Berlaku Sampai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($row = $stmtList->fetch()): 
                        $isExpired = date('Y-m-d') > $row['valid_until'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="badge bg-warning text-dark fs-6"><?= htmlspecialchars($row['code']) ?></span></td>
                        <td class="fw-bold">Rp <?= number_format($row['discount_amount'], 0, ',', '.') ?></td>
                        <td><?= date('d M Y', strtotime($row['valid_until'])) ?></td>
                        <td>
                            <?php if($isExpired): ?>
                                <span class="badge bg-danger">Kadaluarsa</span>
                            <?php else: ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="vouchers.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus voucher ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Voucher Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Kode Voucher (Unik)</label>
                        <input type="text" name="code" class="form-control" placeholder="Contoh: LEBARAN50" required>
                        <small class="text-muted">Otomatis diubah ke huruf besar.</small>
                    </div>
                    <div class="mb-3">
                        <label>Nominal Potongan (Rp)</label>
                        <input type="number" name="discount" class="form-control" placeholder="Contoh: 10000" required>
                    </div>
                    <div class="mb-3">
                        <label>Berlaku Sampai Tanggal</label>
                        <input type="date" name="valid_until" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan_voucher" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>