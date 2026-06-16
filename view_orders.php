<?php
include '../includes/db.php';

$stmt = $conn->prepare("
    SELECT orders.*,
           products.name
    FROM orders
    JOIN products ON orders.product_id = products.id
    ORDER BY orders.order_date DESC
");

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>All Orders</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Product</th>
    <th>Quantity</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php foreach($orders as $order): ?>
<tr>
    <td><?= $order['name']; ?></td>
    <td><?= $order['quantity']; ?></td>
    <td><?= $order['total_price']; ?></td>
    <td>
    <form method="POST" action="update_status.php">

        <input type="hidden" name="order_id" value="<?= $order['id']; ?>">

        <select name="status">
            <option value="Pending" <?= $order['status']=="Pending"?"selected":""; ?>>Pending</option>
            <option value="Shipped" <?= $order['status']=="Shipped"?"selected":""; ?>>Shipped</option>
            <option value="Delivered" <?= $order['status']=="Delivered"?"selected":""; ?>>Delivered</option>
        </select>

        <button type="submit">Update</button>

    </form>
</td>
    <td><?= $order['order_date']; ?></td>
</tr>
<?php endforeach; ?>

</table>