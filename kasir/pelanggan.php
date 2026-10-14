<?php
require_once '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $nama   = $_POST['nama_pelanggan'];
    $telp   = $_POST['no_telp'];
    $alamat = $_POST['alamat'];

    mysqli_query($conn, "INSERT INTO pelanggan VALUES ('', '$nama', '$alamat', '$telp')");
    header("Location: pelanggan.php");
    exit;
}

$page = 'pelanggan';
include 'includes/header.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$q = mysqli_query($conn, "SELECT * FROM pelanggan WHERE nama_pelanggan LIKE '%$search%' OR no_telp LIKE '%$search%' ORDER BY id_pelanggan DESC");
?>

<div class="d-flex justify-content-between mb-3">
    <h3 class="fw-bold">Data Pelanggan</h3>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-1">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-secondary">Cari</button>
        </form>
        <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalPelanggan">+ Tambah Pelanggan</button>
    </div>
</div>

<div class="card p-3">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">No</th>
                <th>Nama Pelanggan</th>
                <th>No Telepon</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while ($r = mysqli_fetch_assoc($q)): 
            ?>
            <tr>
                <td class="text-center fw-bold"><?= $no++ ?></td>
                <td><b><?= htmlspecialchars($r['nama_pelanggan']) ?></b></td>
                <td><?= htmlspecialchars($r['no_telp']) ?></td>
                <td><?= htmlspecialchars($r['alamat']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalPelanggan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>No Telepon</label>
                        <input type="text" name="no_telp" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Hanya angka">
                    </div>
                    <div class="mb-2">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>