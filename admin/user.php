<?php
require_once '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $id   = $_POST['id'];
    $nama = $_POST['nama_user'];
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];

    if ($id == "") {
        mysqli_query($conn, "INSERT INTO user VALUES ('', '$user', '$nama', '$pass', '$role')");
    } else {
        mysqli_query($conn, "UPDATE user SET nama_user='$nama', username='$user', password='$pass', role='$role' WHERE id='$id'");
    }
    header("Location: user.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM user WHERE id='$id'");
    header("Location: user.php");
    exit;
}

$page = 'user';
include 'includes/header.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$q = mysqli_query($conn, "SELECT * FROM user WHERE username LIKE '%$search%' OR nama_user LIKE '%$search%' ORDER BY id DESC");
?>

<div class="d-flex justify-content-between mb-3">
    <h3 class="fw-bold">Kelola User</h3>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-1">
            <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-secondary">Cari</button>
        </form>
        <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="resetForm()">+ Tambah User</button>
    </div>
</div>

<div class="card p-3">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">No</th>
                <th>Username</th>
                <th>Nama User</th>
                <th>Role</th>
                <th>Password</th>
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
                <td><b><?= htmlspecialchars($r['username']) ?></b></td>
                <td><?= htmlspecialchars($r['nama_user']) ?></td>
                <td><span class="badge bg-secondary text-capitalize"><?= htmlspecialchars($r['role']) ?></span></td>
                <td><code><?= htmlspecialchars($r['password']) ?></code></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editData(<?= json_encode($r) ?>)'>Edit</button>
                    <a href="user.php?hapus=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Form User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="u_id">
                    <div class="mb-2">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_user" id="u_nama" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Username</label>
                        <input type="text" name="username" id="u_user" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Password</label>
                        <input type="text" name="password" id="u_pass" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Role</label>
                        <select name="role" id="u_role" class="form-select" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
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
    document.getElementById('u_id').value = '';
    document.getElementById('u_nama').value = '';
    document.getElementById('u_user').value = '';
    document.getElementById('u_pass').value = '';
    document.getElementById('u_role').value = 'kasir';
}

function editData(data) {
    document.getElementById('u_id').value = data.id;
    document.getElementById('u_nama').value = data.nama_user;
    document.getElementById('u_user').value = data.username;
    document.getElementById('u_pass').value = data.password;
    document.getElementById('u_role').value = data.role;
    new bootstrap.Modal(document.getElementById('modalUser')).show();
}
</script>

<?php include 'includes/footer.php'; ?>