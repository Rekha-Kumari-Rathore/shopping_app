<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$cart = $_SESSION['cart'] ?? [];
$products = [];

if (!empty($cart)) {
    $ids = implode(',', array_map('intval', $cart));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$total = array_sum(array_column($products, 'price'));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($products)) {
    $stmt = $conn->prepare("INSERT INTO orders (user_email, total) VALUES (?, ?)");
    $stmt->bind_param("sd", $_SESSION['email'], $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    foreach ($products as $product) {
        $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id) VALUES (?, ?)");
        $stmtItem->bind_param("ii", $order_id, $product['id']);
        $stmtItem->execute();
    }

    $_SESSION['cart'] = [];

    header("Location: thankyou.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body { font-family: Arial; background: #f1f1f1; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 10px; max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #eee; }
        .total { text-align: right; margin-top: 20px; }
        .submit-btn { background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>
        <?php if (empty($products)): ?>
            <p>Your cart is empty. <a href="home.php">Go back to shop</a></p>
        <?php else: ?>
            <form method="POST">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                    </tr>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div class="total">
                    <strong>Total: $<?= number_format($total, 2) ?></strong>
                </div>
                <br>
                <button type="submit" class="submit-btn">Place Order</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
