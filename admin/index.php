<?php
require_once '../config/koneksi.php';
$page = 'dashboard';
include 'includes/header.php';

$tot_produk = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM produk"));
$tot_pelanggan = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pelanggan"));
$tot_trx = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM penjualan"));
?>

<h3 class="fw-bold mb-4">Dashboard Admin</h3>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card p-3 bg-danger text-white">
            <h5>Total Transaksi</h5>
            <h2><?= $tot_trx ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 bg-warning text-dark">
            <h5>Total Produk</h5>
            <h2><?= $tot_produk ?></h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 bg-primary text-white">
            <h5>Total Pelanggan</h5>
            <h2><?= $tot_pelanggan ?></h2>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>