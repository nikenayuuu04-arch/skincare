<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($q) > 0) {
        $_SESSION['user'] = mysqli_fetch_assoc($q);
        header("Location: index.php");
        exit;
    } else {
        $error = 'Username / Password Salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SkinCare Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center">

<div class="card p-4 shadow-sm" style="width: 350px;">
    <h4 class="text-center fw-bold mb-3 text-danger">SkinCare Store</h4>
    
    <?php if ($error != ''): ?>
        <div class="alert alert-danger py-2 small"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold">USERNAME</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-bold">PASSWORD</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-danger w-100 fw-bold">LOG IN</button>
    </form>
</div>

</body>
</html>