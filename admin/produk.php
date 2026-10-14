<?php
require_once '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $id        = $_POST['id_produk'];
    $nama      = $_POST['nama_produk'];
    $kategori  = $_POST['kategori'];
    $harga     = $_POST['harga'];
    $stok      = $_POST['stok'];
    $foto_lama = $_POST['foto_lama'];

    $foto = $foto_lama;
    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != "") {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], '../uploads/' . $foto);
    }

    if ($id == "") {
        mysqli_query($conn, "INSERT INTO produk VALUES ('', '$nama', '$harga', '$stok', '$kategori', '$foto')");
    } else {
        mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok', kategori='$kategori', foto='$foto' WHERE id_produk='$id'");
    }
    header("Location: produk.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Ambil foto lama
    $q_f = mysqli_query($conn, "SELECT foto FROM produk WHERE id_produk='$id'");
    $dt  = mysqli_fetch_assoc($q_f);
    if ($dt['foto'] != "" && file_exists('../uploads/' . $dt['foto'])) {
        unlink('../uploads/' . $dt['foto']);
    }

    // Hapus relasi detail_penjualan
    mysqli_query($conn, "DELETE FROM detail_penjualan WHERE id_produk='$id'");

    // Hapus produk
    mysqli_query($conn, "DELETE FROM produk WHERE id_produk='$id'");

    header("Location: produk.php");
    exit;
}

$page = 'produk';
include 'includes/header.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$q = mysqli_query($conn, "SELECT * FROM produk WHERE nama_produk LIKE '%$search%' OR kategori LIKE '%$search%' ORDER BY id_produk DESC");
?>

<div class="d-flex justify-content-between mb-3">
    <h3 class="fw-bold">Data Produk</h3>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-1">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-secondary">Cari</button>
        </form>
        <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalProduk" onclick="resetForm()">+ Tambah Produk</button>
    </div>
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
                <th>Aksi</th>
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
                <td><?= htmlspecialchars($r['kategori']) ?></td>
                <td>Rp <?= number_format($r['harga'],0,',','.') ?></td>
                <td><?= $r['stok'] ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editData(<?= json_encode($r) ?>)'>Edit</button>
                    <a href="produk.php?hapus=<?= $r['id_produk'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalProduk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Form Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_produk" id="p_id">
                    <input type="hidden" name="foto_lama" id="p_foto_lama">

                    <div class="mb-2">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" id="p_nama" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Kategori</label>
                        <select name="kategori" id="p_kategori" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="Moisturizer">Moisturizer</option>
                            <option value="Serum">Serum</option>
                            <option value="Facial Wash">Facial Wash</option>
                            <option value="Sunscreen">Sunscreen</option>
                            <option value="Toner">Toner</option>
                            <option value="Masker">Masker</option>
                            <option value="Lip Care">Lip Care</option>
                            <option value="Make Up">Make Up</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Harga</label>
                        <input type="number" name="harga" id="p_harga" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Stok</label>
                        <input type="number" name="stok" id="p_stok" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Foto Produk</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('p_id').value = '';
    document.getElementById('p_foto_lama').value = '';
    document.getElementById('p_nama').value = '';
    document.getElementById('p_kategori').value = '';
    document.getElementById('p_harga').value = '';
    document.getElementById('p_stok').value = '';
}

function editData(data) {
    document.getElementById('p_id').value = data.id_produk;
    document.getElementById('p_foto_lama').value = data.foto || '';
    document.getElementById('p_nama').value = data.nama_produk;
    document.getElementById('p_kategori').value = data.kategori;
    document.getElementById('p_harga').value = data.harga;
    document.getElementById('p_stok').value = data.stok;
    new bootstrap.Modal(document.getElementById('modalProduk')).show();
}
</script>

<?php include 'includes/footer.php'; ?>