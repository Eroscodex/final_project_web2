<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$code = $_GET['code'] ?? '';

if ($code == '') {
    echo "Invalid receipt!";
    exit;
}

$stmt = $conn->prepare("SELECT s.*, p.product_name, p.price 
                        FROM sales s 
                        JOIN products p ON s.product_id = p.product_id 
                        WHERE s.receipt_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Receipt not found!";
    exit;
}

$data = $result->fetch_assoc();

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <title>Receipt - " . $code . "</title>";
echo "    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap' rel='stylesheet'>";
echo "    <style>";
echo "        :root { --neon-green: #39FF14; --dark-bg: #0a0a0a; --card-bg: #151515; --text-main: #ffffff; --glow: 0 0 10px rgba(57, 255, 20, 0.5); }";
echo "        body { font-family: 'Outfit', sans-serif; background-color: var(--dark-bg); color: var(--text-main); display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }";
echo "        .receipt-card { background: var(--card-bg); padding: 40px; border-radius: 16px; border: 1px dashed var(--neon-green); width: 100%; max-width: 400px; box-shadow: 0 0 30px rgba(57, 255, 20, 0.1); position: relative; }";
echo "        .receipt-card::before, .receipt-card::after { content: ''; position: absolute; width: 20px; height: 20px; background: var(--dark-bg); border-radius: 50%; left: -10px; }";
echo "        .receipt-card::before { top: 20%; } .receipt-card::after { bottom: 20%; }";
echo "        h2 { color: var(--neon-green); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px; text-align: center; }";
echo "        .store-name { text-align: center; color: #888; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 30px; letter-spacing: 5px; }";
echo "        .line { border-bottom: 1px dashed #333; margin: 20px 0; }";
echo "        .row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.95rem; }";
echo "        .label { color: #888; }";
echo "        .value { font-weight: 600; }";
echo "        .total-row { margin-top: 20px; font-size: 1.2rem; color: var(--neon-green); }";
echo "        .receipt-code { font-family: monospace; font-size: 1.1rem; color: var(--neon-green); text-align: center; margin-top: 20px; display: block; }";
echo "        .actions { margin-top: 30px; display: flex; gap: 10px; width: 100%; max-width: 400px; }";
echo "        .btn { flex: 1; padding: 12px; border-radius: 8px; font-weight: 700; text-align: center; text-decoration: none; text-transform: uppercase; font-size: 0.85rem; cursor: pointer; transition: 0.3s; }";
echo "        .btn-print { background: var(--neon-green); color: black; border: none; }";
echo "        .btn-print:hover { box-shadow: var(--glow); transform: translateY(-2px); }";
echo "        .btn-back { background: transparent; color: #888; border: 1px solid #333; }";
echo "        .btn-back:hover { border-color: #888; color: white; }";
echo "        @media print { .actions { display: none; } body { background: white; color: black; } .receipt-card { border-color: black; box-shadow: none; } }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='receipt-card'>";
echo "        <h2>Receipt</h2>";
echo "        <div class='store-name'>Neon Grid Store</div>";
echo "        <div class='row'><span class='label'>Date:</span><span class='value'>" . date('M d, Y H:i', strtotime($data['sale_date'])) . "</span></div>";
echo "        <div class='row'><span class='label'>Operator:</span><span class='value'>" . $_SESSION['user'] . "</span></div>";
echo "        <div class='line'></div>";
echo "        <div class='row'><span class='label'>Product:</span><span class='value'>" . htmlspecialchars($data['product_name']) . "</span></div>";
echo "        <div class='row'><span class='label'>Price:</span><span class='value'>₱" . number_format($data['price'], 2) . "</span></div>";
echo "        <div class='row'><span class='label'>Quantity:</span><span class='value'>" . $data['quantity'] . "</span></div>";
echo "        <div class='line'></div>";
echo "        <div class='row total-row'><span class='label' style='color: var(--neon-green)'>TOTAL:</span><span class='value'>$" . number_format($data['total'], 2) . "</span></div>";
echo "        <span class='receipt-code'>" . $data['receipt_code'] . "</span>";
echo "    </div>";
echo "    <div class='actions'>";
echo "        <button onclick='window.print()' class='btn btn-print'>Print Receipt</button>";
echo "        <a href='index.php' class='btn btn-back'>Back to Grid</a>";
echo "    </div>";
echo "</body>";
echo "</html>";
?>