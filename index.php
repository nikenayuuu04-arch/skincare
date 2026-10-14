<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['user']['role'] == 'admin') {
    header("Location: admin/index.php");
} else {
    header("Location: kasir/index.php");
}
exit;
?>