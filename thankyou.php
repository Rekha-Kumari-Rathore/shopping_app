<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thank You</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; text-align: center; padding: 100px; }
        h2 { color: #28a745; }
    </style>
</head>
<body>
    <h2>Thank you for your order!</h2>
    <p>Your order has been placed successfully.</p>
    <a href="home.php">Continue Shopping</a>
</body>
</html>
