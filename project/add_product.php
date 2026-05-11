<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    if ($name == '' || $price == '' || $stock == '') {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (product_name, price, stock) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $name, $price, $stock);
        if ($stmt->execute()) {
            header("Location: index.php?msg=Product+Added");
            exit;
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
        }
    }
}

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <title>Add Product - Neon Store</title>";
echo "    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap' rel='stylesheet'>";
echo "    <style>";
echo "        :root { --neon-green: #39FF14; --dark-bg: #0a0a0a; --card-bg: #151515; --text-main: #ffffff; --glow: 0 0 10px rgba(57, 255, 20, 0.5); }";
echo "        body { font-family: 'Outfit', sans-serif; background-color: var(--dark-bg); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }";
echo "        .card { background: var(--card-bg); padding: 40px; border-radius: 16px; border: 1px solid #333; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }";
echo "        h2 { color: var(--neon-green); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; text-align: center; text-shadow: var(--glow); }";
echo "        .form-group { margin-bottom: 20px; }";
echo "        label { display: block; margin-bottom: 8px; color: #a0a0a0; font-size: 0.9rem; }";
echo "        input { width: 93%; padding: 12px; background: #222; border: 1px solid #444; border-radius: 8px; color: white; outline: none; transition: 0.3s; }";
echo "        input:focus { border-color: var(--neon-green); box-shadow: 0 0 5px rgba(57, 255, 20, 0.3); }";
echo "        button { width: 100%; padding: 14px; background: var(--neon-green); color: black; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; text-transform: uppercase; margin-top: 10px; }";
echo "        button:hover { transform: translateY(-2px); box-shadow: var(--glow); }";
echo "        .back-link { width: 88%; display:inline-flex; justify-content:center; align-items:center; margin-top:20px; padding:12px 25px; background:var(--neon-green); color:black; text-decoration:none; border-radius:8px; font-weight:700; text-transform:uppercase; transition:0.3s; }";
echo "        .back-link:hover { transform:translateY(-3px); box-shadow:var(--glow); }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='card'>";
echo "        <h2>Add Product</h2>";
echo "        <form method='POST'>";
echo "            <div class='form-group'>";
echo "                <label>Product Name</label>";
echo "                <input type='text' name='product_name' placeholder='Enter Product Name' required>";
echo "            </div>";
echo "            <div class='form-group'>";
echo "                <label>Price (₱)</label>";
echo "                <input type='number' step='0.01' name='price' placeholder='49.99' required>";
echo "            </div>";
echo "            <div class='form-group'>";
echo "                <label>Initial Stock</label>";
echo "                <input type='number' name='stock' placeholder='50' required>";
echo "            </div>";
echo "            <button type='submit'>Create Product</button>";
echo "        </form>";
echo "        <a href='index.php' class='back-link'>Back to Dashboard</a>";
echo "    </div>";
echo "</body>";
echo "</html>";
?>