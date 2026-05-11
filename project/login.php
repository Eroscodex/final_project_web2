<?php
session_start();
include 'db.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $conn->real_escape_string($_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <title>Login - The Original Pititwiw Store</title>";
echo "    <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap' rel='stylesheet'>";
echo "    <style>";
echo "        :root { --neon-green: #39FF14; --dark-bg: #0a0a0a; --card-bg: #151515; --text-main: #ffffff; --glow: 0 0 10px rgba(57, 255, 20, 0.5); }";
echo "        body { font-family: 'Outfit', sans-serif; background-color: var(--dark-bg); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }";
echo "        .card { background: var(--card-bg); padding: 50px; border-radius: 20px; border: 1px solid #333; width: 100%; max-width: 450px; box-shadow: 0 20px 50px rgba(0,0,0,0.8); text-align: center; }";
echo "        h1 { color: var(--neon-green); text-transform: uppercase; letter-spacing: 4px; margin-bottom: 10px; text-shadow: var(--glow); font-size: 2.5rem; }";
echo "        p { color: #888; margin-bottom: 40px; font-weight: 300; }";
echo "        .form-group { margin-bottom: 25px; text-align: left; }";
echo "        input { width: 93%; padding: 15px; background: #222; border: 1px solid #444; border-radius: 10px; color: white; outline: none; transition: 0.3s; font-size: 1rem; }";
echo "        input:focus { border-color: var(--neon-green); box-shadow: 0 0 15px rgba(57, 255, 20, 0.2); }";
echo "        button { width: 100%; padding: 16px; background: var(--neon-green); color: black; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; text-transform: uppercase; margin-top: 10px; font-size: 1rem; }";
echo "        button:hover { transform: scale(1.02); box-shadow: var(--glow); }";
echo "        .error { color: #ff3e3e; margin-bottom: 20px; font-size: 0.9rem; }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "    <div class='card'>";
echo "        <h1>Welcome to The Original Pititwiw Store </h1>";
echo "        <p>Enter your credentials to access the grid.</p>";
if ($error)
    echo "        <div class='error'>$error</div>";
echo "        <form method='POST'>";
echo "            <div class='form-group'>";
echo "                <input type='text' name='username' placeholder='Username' required>";
echo "            </div>";
echo "            <div class='form-group'>";
echo "                <input type='password' name='password' placeholder='Password' required>";
echo "            </div>";
echo "            <button type='submit'>Enter</button>";
echo "        </form>";
echo "    </div>";
echo "</body>";
echo "</html>";
?>