<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT orders.*,
           products.name
    FROM orders
    JOIN products ON orders.product_id = products.id
    WHERE orders.user_id = ?
    ORDER BY orders.order_date DESC
");

$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
</head>
<body>

<h2>My Orders</h2>

<table border="1" cellpadding="10">
    <tr>
    <th>Product</th>
    <th>Quantity</th>
    <th>Total Price</th>
    <th>Order Date</th>
    <th>Status</th>
</tr>

    <?php foreach($orders as $order): ?>
    <tr>
        <td><?= htmlspecialchars($order['name']); ?></td>
        <td><?= $order['quantity']; ?></td>
        <td>₹<?= $order['total_price']; ?></td>
        <td><?= $order['order_date']; ?></td>
        <td><?= $order['status']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>