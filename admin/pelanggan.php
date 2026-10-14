<?php
require_once '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $id     = $_POST['id_pelanggan'];
    $nama   = $_POST['nama_pelanggan'];
    $telp   = $_POST['no_telp'];
    $alamat = $_POST['alamat'];

    if ($id == "") {
        mysqli_query($conn, "INSERT INTO pelanggan VALUES ('', '$nama', '$alamat', '$telp')");
    } else {
        mysqli_query($conn, "UPDATE pelanggan SET nama_pelanggan='$nama', no_telp='$telp', alamat='$alamat' WHERE id_pelanggan='$id'");
    }
    header("Location: pelanggan.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pelanggan WHERE id_pelanggan='$id'");
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
        <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalPelanggan" onclick="resetForm()">+ Tambah Pelanggan</button>
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
                <td><b><?= htmlspecialchars($r['nama_pelanggan']) ?></b></td>
                <td><?= htmlspecialchars($r['no_telp']) ?></td>
                <td><?= htmlspecialchars($r['alamat']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editData(<?= json_encode($r) ?>)'>Edit</button>
                    <a href="pelanggan.php?hapus=<?= $r['id_pelanggan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pelanggan ini?')">Hapus</a>
                </td>
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
                    <h5 class="modal-title">Form Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_pelanggan" id="pel_id">
                    <div class="mb-2">
                        <label>Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" id="pel_nama" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>No Telepon</label>
                        <input type="text" name="no_telp" id="pel_telp" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Hanya angka">
                    </div>
                    <div class="mb-2">
                        <label>Alamat</label>
                        <textarea name="alamat" id="pel_alamat" class="form-control"></textarea>
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
    document.getElementById('pel_id').value = '';
    document.getElementById('pel_nama').value = '';
    document.getElementById('pel_telp').value = '';
    document.getElementById('pel_alamat').value = '';
}

function editData(data) {
    document.getElementById('pel_id').value = data.id_pelanggan;
    document.getElementById('pel_nama').value = data.nama_pelanggan;
    document.getElementById('pel_telp').value = data.no_telp;
    document.getElementById('pel_alamat').value = data.alamat;
    new bootstrap.Modal(document.getElementById('modalPelanggan')).show();
}
</script>

<?php include 'includes/footer.php'; ?>