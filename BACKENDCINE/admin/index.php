<?php
session_start();
require '../koneksi.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$user]);
    $admin = $stmt->fetch();

    if ($admin && $pass === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $admin['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau Password Salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Cinema XXI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a0b0b 0%, #872341 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            color: white;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 12px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: none;
        }
        .form-control::placeholder { color: rgba(255, 255, 255, 0.7); }
        .btn-login {
            background: #ffc107;
            color: #121212;
            font-weight: bold;
            padding: 12px;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-login:hover { background: #e0a800; transform: scale(1.02); }
        .brand-icon { font-size: 50px; margin-bottom: 20px; color: #ffc107; }
    </style>
</head>
<body>

<div class="login-card text-center">
    <i class="fas fa-film brand-icon"></i>
    <h3 class="mb-4">Admin Panel</h3>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger py-2" style="font-size: 14px;"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 input-group">
            <span class="input-group-text bg-transparent border-0 text-white"><i class="fas fa-user"></i></span>
            <input type="text" name="username" class="form-control rounded" placeholder="Username" required>
        </div>
        <div class="mb-4 input-group">
            <span class="input-group-text bg-transparent border-0 text-white"><i class="fas fa-lock"></i></span>
            <input type="password" name="password" class="form-control rounded" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="btn btn-login w-100">MASUK</button>
    </form>
    <p class="mt-4 text-white-50" style="font-size: 12px;">&copy; 2025 Cinema Booking App</p>
</div>

</body>
</html>