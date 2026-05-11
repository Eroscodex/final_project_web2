<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$products = $conn->query("SELECT * FROM products WHERE stock > 0");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = (int) $_POST['product_id'];
    $qty = (int) $_POST['quantity'];

    if ($pid <= 0 || $qty <= 0) {
        echo "<script>alert('Invalid input!'); window.location='add_sale.php';</script>";
        exit;
    }

    $res = $conn->query("SELECT * FROM products WHERE product_id=$pid");
    if ($res->num_rows == 0) {
        echo "<script>alert('Product not found!'); window.location='add_sale.php';</script>";
        exit;
    }

    $product = $res->fetch_assoc();
    if ($qty > $product['stock']) {
        echo "<script>alert('Not enough stock!'); window.location='add_sale.php';</script>";
        exit;
    }

    $total = $product['price'] * $qty;
    $receipt_code = "RCP-" . strtoupper(bin2hex(random_bytes(4)));

    // Insert sale
    $stmt = $conn->prepare("INSERT INTO sales (product_id, quantity, total, receipt_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $pid, $qty, $total, $receipt_code);

    if ($stmt->execute()) {
        $conn->query("UPDATE products SET stock = stock - $qty WHERE product_id=$pid");
        header("Location: index.php?msg=Sale+Recorded");
        exit;
    } else {
        echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
    }
}

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <title>Record Sale - Neon Store</title>";
echo "    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap' rel='stylesheet'>";
echo "    <style>";
echo "        :root { --neon-green: #39FF14; --dark-bg: #0a0a0a; --card-bg: #151515; --text-main: #ffffff; --glow: 0 0 10px rgba(57, 255, 20, 0.5); }";
echo "        body { font-family: 'Outfit', sans-serif; background-color: var(--dark-bg); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }";
echo "        .card { background: var(--card-bg); padding: 40px; border-radius: 16px; border: 1px solid #333; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }";
echo "        h2 { color: var(--neon-green); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; text-align: center; text-shadow: var(--glow); }";
echo "        .form-group { margin-bottom: 20px; }";
echo "        label { display: block; margin-bottom: 8px; color: #a0a0a0; font-size: 0.9rem; }";
echo "        select, input { width: 93%; padding: 12px; background: #222; border: 1px solid #444; border-radius: 8px; color: white; outline: none; transition: 0.3s; }";
echo "        select:focus, input:focus { border-color: var(--neon-green); box-shadow: 0 0 5px rgba(57, 255, 20, 0.3); }";
echo "        button { width: 100%; padding: 14px; background: var(--neon-green); color: black; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; text-transform: uppercase; margin-top: 10px; }";
echo "        button:hover { transform: translateY(-2px); box-shadow: var(--glow); }";
echo "        .back-link { width: 88%; display:inline-flex; justify-content:center; align-items:center; margin-top:20px; padding:12px 25px; background:var(--neon-green); color:black; text-decoration:none; border-radius:8px; font-weight:700; text-transform:uppercase; transition:0.3s; }";
echo "        .back-link:hover { transform:translateY(-3px); box-shadow:var(--glow); }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='card'>";
echo "        <h2>Record Sale</h2>";
echo "        <form method='POST'>";
echo "            <div class='form-group'>";
echo "                <label>Select Product</label>";
echo "                <select name='product_id' required>";
echo "                    <option value=''>-- Choose Product --</option>";
while ($row = $products->fetch_assoc()) {
    echo "                    <option value='" . $row['product_id'] . "'>" . htmlspecialchars($row['product_name']) . " (₱" . $row['price'] . " | Stock: " . $row['stock'] . ")</option>";
}
echo "                </select>";
echo "            </div>";
echo "            <div class='form-group'>";
echo "                <label>Quantity</label>";
echo "                <input type='number' name='quantity' min='1' placeholder='Enter quantity' required>";
echo "            </div>";
echo "            <button type='submit'>Complete Sale</button>";
echo "        </form>";
echo "        <a href='index.php' class='back-link'>Back to Dashboard</a>";
echo "    </div>";
echo "</body>";
echo "</html>";
?>