<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($page)) { $page = 'dashboard'; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - SkinCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 bg-white min-vh-100 p-3 border-end d-print-none">
            <h5 class="fw-bold text-danger mb-4"><i class="bi bi-heart-fill"></i> SkinCare Admin</h5>
            <ul class="nav nav-pills flex-column gap-1">
                <li><a href="index.php" class="nav-link <?= $page == 'dashboard' ? 'active bg-danger' : 'text-dark' ?>">Dashboard</a></li>
                <li><a href="produk.php" class="nav-link <?= $page == 'produk' ? 'active bg-danger' : 'text-dark' ?>">Produk</a></li>
                <li><a href="pelanggan.php" class="nav-link <?= $page == 'pelanggan' ? 'active bg-danger' : 'text-dark' ?>">Pelanggan</a></li>
                <li><a href="pejualan.php" class="nav-link <?= $page == 'penjualan' ? 'active bg-danger' : 'text-dark' ?>">Riwayat Transaksi</a></li>
                <li><a href="user.php" class="nav-link <?= $page == 'user' ? 'active bg-danger' : 'text-dark' ?>">Kelola User</a></li>
                <li><a href="laporan.php" class="nav-link <?= $page == 'laporan' ? 'active bg-danger' : 'text-dark' ?>">Laporan</a></li>
            </ul>
            <hr>
            <a href="../logout.php" class="btn btn-outline-danger w-100" onclick="return confirm('Logout?')">Logout</a>
        </nav>
        <main class="col-md-10 p-4">