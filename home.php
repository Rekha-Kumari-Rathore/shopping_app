<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['add'])) {
    $id = $_GET['add'];
    if (!in_array($id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $id;
    }
    header("Location: home.php");
    exit();
}

if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($item) => $item != $id);
    header("Location: home.php");
    exit();
}

$products = $conn->query("SELECT * FROM products");

?>
<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <style>
        body {
            font-family: Arial;
            background: #e0f7fa;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px gray;
            max-width: 900px;
            margin: auto;
        }

        .product {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
            text-align: left;
        }

        .actions {
            float: right;
        }

        button {
            padding: 5px 10px;
            margin-left: 10px;
        }

        .logout-btn {
            float: right;
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            margin-right: 15px;
        }

        .cart-count {
            float: right;
            margin-right: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="post" action="logout.php">
            <span class="cart-count">Cart: <?= count($_SESSION['cart']) ?></span>
            <a class="cart-count" style="color: white; background:green; padding: 4px; border-radius:2px;" href="checkout.php">Go to Checkout</a>
            <button class="logout-btn" type="submit">Logout</button>
        </form>
        <h2>Welcome, <?php echo $_SESSION['full_name']; ?>!</h2>
        <h3>Product List</h3>
        <?php while ($row = $products->fetch_assoc()): ?>
            <div class="product">
                <div style="display: flex; align-items: center;">
                    <img src="<?= $row['image'] ?>" alt="<?= $row['name'] ?>" style="width: 50px; height: 50px; margin-right: 10px;">
                    <div>
                        <strong><?= $row['name'] ?></strong><br>
                        <small><?= $row['description'] ?></small>,
                        Price: $<strong><?= $row['price'] ?></strong><br>
                    </div>
                </div>
                <div class="actions">
                    <?php if (!in_array($row['id'], $_SESSION['cart'])): ?>
                        <a href="?add=<?= $row['id'] ?>"><button>Add to Cart</button></a>
                    <?php else: ?>
                        <a href="?remove=<?= $row['id'] ?>"><button style="background: #f44336; color: white;">Remove</button></a>
                    <?php endif; ?>
                </div>
                <div style="clear: both;"></div>
            </div>
        <?php endwhile; ?>

    </div>
</body>

</html>