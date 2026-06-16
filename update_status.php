<?php
include '../includes/db.php';

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->execute([$status, $order_id]);

header("Location: view_orders.php");
exit();
?>