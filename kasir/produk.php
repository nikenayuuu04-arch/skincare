<?php
require_once '../config/koneksi.php';

$page = 'produk';
include 'includes/header.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$q = mysqli_query($conn, "SELECT * FROM produk WHERE nama_produk LIKE '%$search%' OR kategori LIKE '%$search%' ORDER BY id_produk DESC");
?>

<div class="d-flex justify-content-between mb-3">
    <h3 class="fw-bold">Stok & Daftar Produk</h3>
    <form method="GET" class="d-flex gap-1">
        <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-secondary">Cari</button>
    </form>
</div>

<div class="card p-3">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">No</th>
                <th>Foto</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($r = mysqli_fetch_assoc($q)): 
            ?>
            <tr>
                <td class="text-center fw-bold"><?= $no++ ?></td>
                <td>
                    <?php 
                    $file_foto = '../uploads/' . $r['foto'];
                    if ($r['foto'] != "" && file_exists($file_foto)): 
                    ?>
                        <img src="<?= $file_foto ?>" width="50" height="50" class="rounded object-fit-cover">
                    <?php else: ?>
                        <div class="bg-secondary-subtle text-secondary rounded d-flex align-items-center justify-content-center fw-bold" style="width:50px; height:50px; font-size:10px;">
                            NO IMG
                        </div>
                    <?php endif; ?>
                </td>
                <td><b><?= htmlspecialchars($r['nama_produk']) ?></b></td>
                <td><span class="badge bg-danger-subtle text-danger"><?= htmlspecialchars($r['kategori']) ?></span></td>
                <td class="fw-bold">Rp <?= number_format($r['harga'],0,',','.') ?></td>
                <td>
                    <span class="badge <?= $r['stok'] <= 5 ? 'bg-danger' : 'bg-success' ?>">
                        <?= $r['stok'] ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>