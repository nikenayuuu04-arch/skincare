<?php
require_once '../config/koneksi.php';

$page = 'penjualan';
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
    <h3 class="fw-bold mb-0">Riwayat Transaksi</h3>
    <!-- Tombol untuk kasir membuat transaksi baru -->
</div>

<div class="card p-3 d-print-none">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">No</th>
                <th>No. Nota</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total Bayar</th>
                <th class="text-center" style="width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // COALESCE mengambil nama dari tabel pelanggan jika ada, 
            // jika pelanggan sudah dihapus oleh admin, otomatis menggunakan p.nama_pelanggan dari trigger.
            $q = mysqli_query($conn, "SELECT p.*, 
                                             COALESCE(pl.nama_pelanggan, p.nama_pelanggan, 'Pelanggan Terhapus') AS nama_pelanggan_final 
                                      FROM penjualan p 
                                      LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                                      ORDER BY p.id_penjualan DESC");
            while ($r = mysqli_fetch_assoc($q)):
            ?>
            <tr>
                <td class="text-center fw-bold"><?= $no++ ?></td>
                <td>#TRX-<?= $r['id_penjualan'] ?></td>
                <td><?= $r['tgl_penjualan'] ?></td>
                <td><b><?= htmlspecialchars($r['nama_pelanggan_final']) ?></b></td>
                <td>Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                <td class="text-center">
                    <!-- Kasir hanya dibolehkan mencetak/melihat ulang Struk -->
                    <a href="penjualan.php?cetak=<?= $r['id_penjualan'] ?>" class="btn btn-sm btn-danger fw-bold w-100">Cetak Struk</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php if (isset($_GET['cetak'])): 
    $id_print = (int)$_GET['cetak'];
    $trx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.*, 
                                                          COALESCE(pl.nama_pelanggan, p.nama_pelanggan, 'Pelanggan Terhapus') AS nama_pelanggan_final 
                                                   FROM penjualan p 
                                                   LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                                                   WHERE p.id_penjualan = '$id_print'"));
    if ($trx):
?>
<!-- Modal Struk Pembayaran -->
<div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-sm">
        <div class="modal-content p-3 text-center">
            <h5 class="fw-bold">SKINCARE STORE</h5>
            <small>No: #TRX-<?= $trx['id_penjualan'] ?> | <?= $trx['tgl_penjualan'] ?></small><br>
            <small>Pelanggan: <?= htmlspecialchars($trx['nama_pelanggan_final']) ?></small>
            <hr>
            <table class="w-100 text-start small mb-2">
                <?php
                $q_dt = mysqli_query($conn, "SELECT dp.*, pr.nama_produk 
                                             FROM detail_penjualan dp 
                                             JOIN produk pr ON dp.id_produk = pr.id_produk 
                                             WHERE dp.id_penjualan = '$id_print'");
                while ($d = mysqli_fetch_assoc($q_dt)):
                ?>
                <tr>
                    <td colspan="2"><b><?= htmlspecialchars($d['nama_produk']) ?></b></td>
                </tr>
                <tr>
                    <td><?= $d['jumlah_produk'] ?> x</td>
                    <td class="text-end">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <hr>
            <h6>TOTAL: Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></h6>
            <hr>
            <div class="d-print-none d-flex gap-1">
                <a href="penjualan.php" class="btn btn-sm btn-secondary w-50">Tutup</a>
                <button onclick="window.print()" class="btn btn-sm btn-danger w-50">Cetak</button>
            </div>
        </div>
    </div>
</div>
<?php 
    endif;
endif; 
?>

<?php include 'includes/footer.php'; ?>