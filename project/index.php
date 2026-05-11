<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

$sales_query = "SELECT s.*, p.product_name 
                    FROM sales s 
                    JOIN products p ON s.product_id = p.product_id 
                    ORDER BY s.sale_date ASC";
$sales_result = $conn->query($sales_query);

$products_query = "SELECT * FROM products ORDER BY product_id ASC";
$products_result = $conn->query($products_query);


echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>The Original Pititwiw Store</title>";
echo "    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap' rel='stylesheet'>";
echo "    <style>";
echo "        :root {";
echo "            --neon-green: #39FF14;";
echo "            --dark-bg: #0a0a0a;";
echo "            --card-bg: #151515;";
echo "            --text-main: #ffffff;";
echo "            --text-dim: #a0a0a0;";
echo "            --glow: 0 0 10px rgba(57, 255, 20, 0.5), 0 0 20px rgba(57, 255, 20, 0.2);";
echo "        }";
echo "        * { margin: 0; padding: 0; box-sizing: border-box; }";
echo "        body { font-family: 'Outfit', sans-serif; background-color: var(--dark-bg); color: var(--text-main); min-height: 100vh; padding: 40px 20px; }";
echo "        .container { max-width: 1200px; margin: 0 auto; }";
echo "        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #333; }";
echo "        h1 { font-size: 2.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--neon-green); text-shadow: var(--glow); }";
echo "        .user-info { display: flex; align-items: center; gap: 20px; }";
echo "        .logout-btn { background: transparent; border: 1px solid var(--neon-green); color: var(--neon-green); padding: 8px 20px; text-decoration: none; border-radius: 4px; transition: 0.3s; font-weight: 600; }";
echo "        .logout-btn:hover { background: var(--neon-green); color: black; box-shadow: var(--glow); }";
echo "        .action-bar { margin-bottom: 30px; display: flex; gap: 15px; }";
echo "        .action-btn { background: var(--neon-green); color: black; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 700; transition: 0.3s; display: inline-flex; align-items: center; text-transform: uppercase; }";
echo "        .action-btn:hover { transform: translateY(-3px); box-shadow: var(--glow); }";
echo "        .section-title { font-size: 1.5rem; margin-bottom: 20px; color: var(--neon-green); border-left: 4px solid var(--neon-green); padding-left: 15px; }";
echo "        .table-container { background: var(--card-bg); border-radius: 12px; padding: 20px; border: 1px solid #333; margin-bottom: 40px; overflow-x: auto; }";
echo "        table { width: 100%; border-collapse: collapse; text-align: left; }";
echo "        th { color: var(--neon-green); font-weight: 600; padding: 15px; border-bottom: 2px solid #222; text-transform: uppercase; font-size: 0.85rem; }";
echo "        td { padding: 15px; border-bottom: 1px solid #222; color: var(--text-dim); }";
echo "        tr:hover td { color: var(--text-main); background: #1a1a1a; }";
echo "        .badge { padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; }";
echo "        .badge-green { background: rgba(57, 255, 20, 0.1); color: var(--neon-green); border: 1px solid var(--neon-green); }";
echo "        .receipt-code { font-family: 'Courier New', Courier, monospace; color: var(--neon-green); font-weight: bold; }";
echo "        ::-webkit-scrollbar { width: 8px; }";
echo "        ::-webkit-scrollbar-track { background: var(--dark-bg); }";
echo "        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }";
echo "        ::-webkit-scrollbar-thumb:hover { background: var(--neon-green); }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='container'>";
echo "        <header>";
echo "            <h1>The Original Pititwiw Store</h1>";
echo "            <div class='user-info'>";
echo "                <span>Logged in as: <strong style='color: var(--neon-green)'>$user</strong> ($role)</span>";
echo "                <a href='logout.php' class='logout-btn'>LOGOUT</a>";
echo "            </div>";
echo "        </header>";

echo "        <div class='action-bar'>";
echo "            <a href='add_sale.php' class='action-btn'>+ Record New Sale</a>";
if ($role == 'admin') {
    echo "            <a href='add_product.php' class='action-btn'>+ Add New Product</a>";
}
echo "        </div>";

echo "        <h2 class='section-title'>Sales Records</h2>";
echo "        <div class='table-container'>";
echo "            <table>";
echo "                <thead>";
echo "                    <tr>";
echo "                        <th>Sale ID</th>";
echo "                        <th>Product</th>";
echo "                        <th>Quantity</th>";
echo "                        <th>Total</th>";
echo "                        <th>Date</th>";
echo "                        <th>Receipt Code</th>";
echo "                        <th>Action</th>";
echo "                    </tr>";
echo "                </thead>";
echo "                <tbody>";

if ($sales_result && $sales_result->num_rows > 0) {
    while ($row = $sales_result->fetch_assoc()) {
        echo "                    <tr>";
        echo "                        <td>" . $row['sale_id'] . "</td>";
        echo "                        <td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "                        <td>" . $row['quantity'] . "</td>";
        echo "                        <td style='color: white;'>₱" . number_format($row['total'], 2) . "</td>";
        echo "                        <td>" . date('M d, Y h:i A', strtotime($row['sale_date'])) . "</td>";
        echo "                        <td><span class='receipt-code'>" . $row['receipt_code'] . "</span></td>";
        echo "                        <td>
                                        <a href='receipt.php?code=" . $row['receipt_code'] . "' 
                                        style='color: var(--neon-green); text-decoration:none; font-weight:bold;'>
                                        View Receipt
                                        </a>
                                    </td>";
        echo "                    </tr>";
    }
} else {
    echo "                    <tr><td colspan='6' style='text-align: center;'>No sales recorded yet.</td></tr>";
}

echo "                </tbody>";
echo "            </table>";
echo "        </div>";

echo "        <h2 class='section-title'>Inventory Status</h2>";
echo "        <div class='table-container'>";
echo "            <table>";
echo "                <thead>";
echo "                    <tr>";
echo "                        <th>ID</th>";
echo "                        <th>Product Name</th>";
echo "                        <th>Price</th>";
echo "                        <th>Stock</th>";
if ($role == 'admin') {
    echo "                        <th>Actions</th>";
}
echo "                    </tr>";
echo "                </thead>";
echo "                <tbody>";

if ($products_result && $products_result->num_rows > 0) {
    while ($row = $products_result->fetch_assoc()) {
        $stock_class = $row['stock'] < 10 ? "style='color: #ff3e3e;'" : "";
        echo "                    <tr>";
        echo "                        <td>" . $row['product_id'] . "</td>";
        echo "                        <td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "                        <td style='color: white;'>₱" . number_format($row['price'], 2) . "</td>";
        echo "                        <td $stock_class>" . $row['stock'] . "</td>";
        if ($role == 'admin') {
            echo "                        <td>";
            echo " <a href='edit.php?id=" . $row['product_id'] . "' style='display:inline-block; padding:6px 12px; background:var(--neon-green); color:black; text-decoration:none; border-radius:6px; font-weight:700; margin-right:10px;'>Edit</a>";
            echo " <a href='delete.php?id=" . $row['product_id'] . "' style='display:inline-block; padding:6px 12px; background:#ff3e3e; color:white; text-decoration:none; border-radius:6px; font-weight:700;' onclick=\"return confirm('Delete this product?')\">Delete</a>";
            echo "                        </td>";
        }
        echo "                    </tr>";
    }
} else {
    echo "                    <tr><td colspan='5' style='text-align: center;'>Inventory is empty.</td></tr>";
}

echo "                </tbody>";
echo "            </table>";
echo "        </div>";

echo "    </div>";
echo "</body>";
echo "</html>";
?>