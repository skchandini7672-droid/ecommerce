<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT cart.product_id,
           cart.quantity,
           products.price
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = ?
");

$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($cart_items) == 0) {
    die("Cart is empty.");
}

foreach ($cart_items as $item) {

    $product_id = $item['product_id'];
    $quantity = $item['quantity'];
    $total_price = $item['price'] * $quantity;

    $order = $conn->prepare("
        INSERT INTO orders
        (user_id, product_id, quantity, total_price)
        VALUES (?, ?, ?, ?)
    ");

    $order->execute([
        $user_id,
        $product_id,
        $quantity,
        $total_price
    ]);
}

$clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$clear->execute([$user_id]);

echo "<h2>Order Placed Successfully!</h2>";
echo "<a href='../index.php'>Continue Shopping</a>";
?>