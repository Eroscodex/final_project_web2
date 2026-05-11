<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM products WHERE product_id=$id");
}

header("Location: index.php?msg=Product+Deleted");
exit;
?>