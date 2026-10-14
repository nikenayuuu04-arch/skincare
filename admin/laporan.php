<?php
require_once '../config/koneksi.php';

// Validasi & Ambil Tanggal Filter
$tgl1 = isset($_GET['tgl1']) && !empty($_GET['tgl1']) ? mysqli_real_escape_string($conn, $_GET['tgl1']) : date('Y-m-01');
$tgl2 = isset($_GET['tgl2']) && !empty($_GET['tgl2']) ? mysqli_real_escape_string($conn, $_GET['tgl2']) : date('Y-m-d');

$page = 'laporan';
include 'includes/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
    <h3 class="fw-bold mb-0">Laporan Penjualan</h3>
    <button onclick="window.print()" class="btn btn-danger fw-bold"><i class="bi bi-printer"></i> Cetak Laporan</button>
</div>

<!-- Header Khusus Tampilan Cetak/Print -->
<div class="d-none d-print-block text-center mb-4">
    <h3 class="fw-bold mb-1">SKINCARE STORE</h3>
    <h5 class="mb-1">LAPORAN PENJUALAN</h5>
    <p class="mb-0 small">Periode: <?= date('d/m/Y', strtotime($tgl1)) ?> s/d <?= date('d/m/Y', strtotime($tgl2)) ?></p>
    <hr class="border-2 border-dark">
</div>

<!-- Form Filter Tanggal -->
<div class="card p-3 mb-3 d-print-none">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold">Dari Tanggal</label>
            <input type="date" name="tgl1" class="form-control" value="<?= $tgl1 ?>" required>
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-bold">Sampai Tanggal</label>
            <input type="date" name="tgl2" class="form-control" value="<?= $tgl2 ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark w-100 fw-bold">Filter</button>
        </div>
    </form>
</div>

<!-- Tabel Laporan -->
<div class="card p-3">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">No</th>
                <th style="width: 120px;">No. Nota</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th class="text-end">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $grand = 0;

            // Menggunakan LEFT JOIN dan COALESCE agar nama pelanggan tetap tampil walau data pelanggan terhapus
            $query_str = "SELECT p.*, 
                                 COALESCE(pl.nama_pelanggan, p.nama_pelanggan, 'Pelanggan Terhapus') AS nama_pelanggan_final 
                          FROM penjualan p 
                          LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                          WHERE p.tgl_penjualan BETWEEN '$tgl1' AND '$tgl2'
                          ORDER BY p.tgl_penjualan ASC, p.id_penjualan ASC";
            
            $q = mysqli_query($conn, $query_str);

            if (mysqli_num_rows($q) > 0):
                while ($r = mysqli_fetch_assoc($q)):
                    $grand += $r['total_harga'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td>#TRX-<?= $r['id_penjualan'] ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_penjualan'])) ?></td>
                <td><b><?= htmlspecialchars($r['nama_pelanggan_final']) ?></b></td>
                <td class="text-end">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="5" class="text-center text-muted py-3">Tidak ada data transaksi pada periode tanggal ini.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold table-light">
                <td colspan="4" class="text-end">TOTAL OMSET:</td>
                <td class="text-end text-danger">Rp <?= number_format($grand, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>
</div>

<?php include 'includes/footer.php'; ?>