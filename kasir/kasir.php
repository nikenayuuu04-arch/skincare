<?php
require_once '../config/koneksi.php';

if (isset($_POST['bayar'])) {
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $tgl = mysqli_real_escape_string($conn, $_POST['tgl_penjualan']);
    $items = $_POST['items'];

    $total = 0;
    // Hitung total harga transaksi
    foreach ($items as $id_p => $qty) {
        $qty = (int)$qty;
        if ($qty > 0) {
            $id_p = (int)$id_p;
            $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk='$id_p'"));
            if ($p) {
                $total += ($p['harga'] * $qty);
            }
        }
    }

    if ($total > 0 && !empty($id_pelanggan)) {
        // PERBAIKAN: Menyebutkan nama kolom secara spesifik agar sesuai dengan struktur database terbaru
        $query_insert = "INSERT INTO penjualan (tgl_penjualan, total_harga, id_pelanggan, status) 
                        VALUES ('$tgl', '$total', '$id_pelanggan', 'selesai')";
        
        if (mysqli_query($conn, $query_insert)) {
            $id_penjualan = mysqli_insert_id($conn);

            // Simpan detail produk dan kurangi stok
            foreach ($items as $id_p => $qty) {
                $qty = (int)$qty;
                if ($qty > 0) {
                    $id_p = (int)$id_p;
                    $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk='$id_p'"));
                    if ($p) {
                        $subtotal = $p['harga'] * $qty;
                        mysqli_query($conn, "INSERT INTO detail_penjualan (id_penjualan, id_produk, jumlah_produk, subtotal) 
                                             VALUES ('$id_penjualan', '$id_p', '$qty', '$subtotal')");
                        
                        mysqli_query($conn, "UPDATE produk SET stok = stok - $qty WHERE id_produk='$id_p'");
                    }
                }
            }
            header("Location: kasir.php?print=$id_penjualan");
            exit;
        } else {
            echo "<script>alert('Gagal memproses transaksi: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Pilih pelanggan dan pastikan jumlah pembelian lebih dari 0!');</script>";
    }
}

$page = 'kasir';
include 'includes/header.php';
?>

<h3 class="fw-bold mb-3 d-print-none">Transaksi Kasir</h3>

<form method="POST" class="d-print-none">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <div class="mb-2">
                    <label class="form-label">Pilih Pelanggan</label>
                    <select name="id_pelanggan" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        $qp = mysqli_query($conn, "SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
                        while ($p = mysqli_fetch_assoc($qp)):
                        ?>
                            <option value="<?= $p['id_pelanggan'] ?>"><?= htmlspecialchars($p['nama_pelanggan']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tgl_penjualan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <button type="submit" name="bayar" class="btn btn-success btn-lg w-100 fw-bold">Proses Bayar</button>
        </div>

        <div class="col-md-8">
            <div class="card p-3">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th style="width: 100px;">Beli</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $qp = mysqli_query($conn, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
                        while ($pr = mysqli_fetch_assoc($qp)):
                        ?>
                        <tr>
                            <td>
                                <b><?= htmlspecialchars($pr['nama_produk']) ?></b><br>
                                <small class="text-muted"><?= htmlspecialchars($pr['kategori']) ?></small>
                            </td>
                            <td>Rp <?= number_format($pr['harga'], 0, ',', '.') ?></td>
                            <td><?= $pr['stok'] ?></td>
                            <td>
                                <input type="number" min="0" max="<?= $pr['stok'] ?>" name="items[<?= $pr['id_produk'] ?>]" value="0" class="form-control form-control-sm text-center">
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<?php if (isset($_GET['print'])): 
    $id_print = (int)$_GET['print'];
    // COALESCE mengambil dari master pelanggan, jika sudah dihapus akan mengambil cadangan dari p.nama_pelanggan
    $trx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT p.*, 
                                                          COALESCE(pl.nama_pelanggan, p.nama_pelanggan, 'Pelanggan Terhapus') AS nama_pelanggan_final 
                                                   FROM penjualan p 
                                                   LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
                                                   WHERE p.id_penjualan = '$id_print'"));
    if ($trx):
?>
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
                <a href="kasir.php" class="btn btn-sm btn-secondary w-50">Tutup</a>
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