<?php
require_once 'config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin');

// SIMPAN / EDIT PRODUK
if (isset($_POST['save_produk']) && $is_admin) {
    $id_produk   = $_POST['id_produk'];
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $harga       = $_POST['harga'];
    $stok        = $_POST['stok'];
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $foto_lama   = $_POST['foto_lama'];

    $nama_foto = $foto_lama;

    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != "") {
        $filename = $_FILES['foto']['name'];
        $tmp_name = $_FILES['foto']['tmp_name'];
        $ext      = pathinfo($filename, PATHINFO_EXTENSION);
        $allowed  = array('jpg', 'jpeg', 'png', 'webp');

        if (in_array(strtolower($ext), $allowed)) {
            $nama_foto = time() . '_' . rand(100, 999) . '.' . $ext;
            $target    = 'uploads/' . $nama_foto;

            if (move_uploaded_file($tmp_name, $target)) {
                if ($foto_lama != "" && file_exists('uploads/' . $foto_lama)) {
                    unlink('uploads/' . $foto_lama);
                }
            }
        }
    }

    if ($id_produk == "") {
        $sql = "INSERT INTO produk (nama_produk, harga, stok, kategori, foto) VALUES ('$nama_produk', '$harga', '$stok', '$kategori', '$nama_foto')";
    } else {
        $sql = "UPDATE produk SET nama_produk='$nama_produk', harga='$harga', stok='$stok', kategori='$kategori', foto='$nama_foto' WHERE id_produk='$id_produk'";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: produk.php");
        exit;
    }
}

// HAPUS PRODUK
if (isset($_GET['delete']) && $is_admin) {
    $id = $_GET['delete'];
    $q_foto = mysqli_query($conn, "SELECT foto FROM produk WHERE id_produk='$id'");
    $dt = mysqli_fetch_assoc($q_foto);
    
    if ($dt['foto'] != "" && file_exists('uploads/' . $dt['foto'])) {
        unlink('uploads/' . $dt['foto']);
    }

    mysqli_query($conn, "DELETE FROM produk WHERE id_produk='$id'");
    header("Location: produk.php");
    exit;
}

$page = 'produk';
include 'includes/header.php';

// FITUR PENCARIAN PRODUK
$keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_search = $keyword != '' ? "WHERE nama_produk LIKE '%$keyword%' OR kategori LIKE '%$keyword%'" : '';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <h3 class="fw-bold text-dark mb-0"><i class="bi bi-box2-heart-fill text-danger me-2"></i> Data Produk</h3>
    
    <div class="d-flex gap-2">
        <!-- FITUR CARI PRODUK -->
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control rounded-3 me-2" placeholder="Cari nama / kategori..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn btn-outline-danger rounded-3"><i class="bi bi-search"></i></button>
        </form>

        <?php if ($is_admin): ?>
            <button class="btn btn-danger rounded-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalProduk" onclick="resetFormProduk()">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 rounded-4 shadow-sm bg-white">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conn, "SELECT * FROM produk $where_search ORDER BY id_produk DESC");
                    if (mysqli_num_rows($q) > 0):
                        while ($r = mysqli_fetch_assoc($q)):
                            $img_src = ($r['foto'] != "" && file_exists('uploads/' . $r['foto'])) 
                                       ? 'uploads/' . $r['foto'] 
                                       : 'https://via.placeholder.com/60?text=No+Img';
                    ?>
                    <tr>
                        <td><?= $r['id_produk'] ?></td>
                        <td>
                            <img src="<?= $img_src ?>" alt="Foto" width="50" height="50" class="rounded-3 object-fit-cover shadow-sm">
                        </td>
                        <td><strong class="text-dark"><?= htmlspecialchars($r['nama_produk']) ?></strong></td>
                        <td><span class="badge bg-danger-subtle text-danger rounded-pill"><?= htmlspecialchars($r['kategori']) ?></span></td>
                        <td class="fw-semibold">Rp <?= number_format($r['harga'], 0, ',', '.') ?></td>
                        <td>
                            <span class="badge <?= $r['stok'] <= 5 ? 'bg-danger text-white' : 'bg-success-subtle text-success' ?> rounded-pill px-3">
                                <?= $r['stok'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($is_admin): ?>
                                <button class="btn btn-sm btn-outline-warning me-1 rounded-2" onclick='editProduk(<?= json_encode($r) ?>)'>
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <a href="produk.php?delete=<?= $r['id_produk'] ?>" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Hapus produk ini?')">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Read Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Data produk tidak ditemukan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($is_admin): ?>
<div class="modal fade" id="modalProduk" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Form Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_produk" id="p_id">
                    <input type="hidden" name="foto_lama" id="p_foto_lama">

                    <div class="mb-3">
                        <label class="form-label text-body-secondary small fw-semibold">NAMA PRODUK</label>
                        <input type="text" name="nama_produk" id="p_nama" class="form-control rounded-3" required>
                    </div>

                    <!-- KATEGORI PILIHAN DROPDOWN -->
                    <div class="mb-3">
                        <label class="form-label text-body-secondary small fw-semibold">KATEGORI</label>
                        <select name="kategori" id="p_kategori" class="form-select rounded-3" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Moisturizer">Moisturizer (Pelembab)</option>
                            <option value="Serum">Serum</option>
                            <option value="Facial Wash">Facial Wash (Pembersih)</option>
                            <option value="Sunscreen">Sunscreen</option>
                            <option value="Toner">Toner</option>
                            <option value="Masker">Masker / Exfoliator</option>
                            <option value="Lip Care">Lip / Lip Care</option>
                            <option value="Make Up">Make Up / Dekoratif</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-body-secondary small fw-semibold">HARGA (RP)</label>
                            <input type="number" step="0.01" name="harga" id="p_harga" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-body-secondary small fw-semibold">STOK</label>
                            <input type="number" name="stok" id="p_stok" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-body-secondary small fw-semibold">FOTO PRODUK</label>
                        <input type="file" name="foto" id="p_foto" class="form-control rounded-3" accept="image/*">
                        <small class="text-muted d-block mt-1" id="foto_note"></small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_produk" class="btn btn-danger rounded-3 fw-bold">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetFormProduk() {
    document.getElementById('p_id').value = '';
    document.getElementById('p_foto_lama').value = '';
    document.getElementById('p_nama').value = '';
    document.getElementById('p_kategori').value = '';
    document.getElementById('p_harga').value = '';
    document.getElementById('p_stok').value = '';
    document.getElementById('p_foto').value = '';
    document.getElementById('foto_note').innerText = '';
}

function editProduk(data) {
    document.getElementById('p_id').value = data.id_produk;
    document.getElementById('p_foto_lama').value = data.foto || '';
    document.getElementById('p_nama').value = data.nama_produk;
    document.getElementById('p_kategori').value = data.kategori;
    document.getElementById('p_harga').value = data.harga;
    document.getElementById('p_stok').value = data.stok;
    document.getElementById('p_foto').value = '';
    document.getElementById('foto_note').innerText = data.foto ? 'Kosongkan jika tidak ingin mengubah foto.' : '';

    var modal = new bootstrap.Modal(document.getElementById('modalProduk'));
    modal.show();
}
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>