<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'cinema_db';

$con = mysqli_connect($host, $user, $pass, $db);

if (!$con) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>