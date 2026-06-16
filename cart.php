<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
// Add to Cart
if (isset($_POST['add_to_cart'])) {

    $product_id = $_POST['product_id'];

    $check = $conn->prepare(
        "SELECT * FROM cart WHERE user_id = ? AND product_id = ?"
    );
    $check->execute([$user_id, $product_id]);

    if ($check->rowCount() > 0) {

        $update = $conn->prepare(
            "UPDATE cart
             SET quantity = quantity + 1
             WHERE user_id = ? AND product_id = ?"
        );
        $update->execute([$user_id, $product_id]);

    } else {

        $insert = $conn->prepare(
            "INSERT INTO cart (user_id, product_id, quantity)
             VALUES (?, ?, 1)"
        );
        $insert->execute([$user_id, $product_id]);
    }
    header("Location: cart.php");
exit();
}

// Update Quantity
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $user_id, $product_id]);
}
// Remove Item
if (isset($_POST['remove_from_cart'])) {
    $cart_id = $_POST['product_id'];

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND id = ?");
    $stmt->execute([$user_id, $cart_id]);
}

// Fetch Cart Items
$stmt = $conn->prepare("
    SELECT cart.id AS cart_id,
           products.name,
           products.price,
           cart.quantity
    FROM cart
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = ?
");

$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_cost = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Cart</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f4f4f4;
            margin:0;
            padding:20px;
        }

        .cart-container{
            max-width:900px;
            margin:auto;
            background:#fff;
            padding:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h2{
            text-align:center;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th,td{
            border:1px solid #ddd;
            padding:12px;
            text-align:center;
        }

        th{
            background:#007bff;
            color:white;
        }

        input[type="number"]{
            width:60px;
            padding:5px;
        }

        .btn{
            padding:8px 15px;
            border:none;
            cursor:pointer;
            color:white;
            border-radius:5px;
        }

        .update-btn{
            background:green;
        }

        .remove-btn{
            background:red;
        }

        .total{
            margin-top:20px;
            text-align:right;
            font-size:22px;
            font-weight:bold;
        }

        .shop-btn{
            display:inline-block;
            margin-top:20px;
            background:#007bff;
            color:white;
            padding:10px 20px;
            text-decoration:none;
            border-radius:5px;
        }
    </style>
</head>

<body>

<div class="cart-container">

    <h2>My Shopping Cart</h2>

    <?php if(count($cart_items) > 0): ?>

    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Actions</th>
        </tr>

        <?php foreach($cart_items as $item): ?>

        <?php
        $subtotal = $item['price'] * $item['quantity'];
        $total_cost += $subtotal;
        ?>

        <tr>
            <td><?= htmlspecialchars($item['name']); ?></td>

            <td>₹<?= number_format($item['price'], 2); ?></td>

            <td>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?= $item['cart_id']; ?>">

                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        value="<?= $item['quantity']; ?>"
                    >

                    <button
                        type="submit"
                        name="update_quantity"
                        class="btn update-btn">
                        Update
                    </button>
                </form>
            </td>

            <td>₹<?= number_format($subtotal, 2); ?></td>

            <td>
                <form method="POST">
                    <input type="hidden"
                           name="product_id"
                           value="<?= $item['cart_id']; ?>">

                    <button
                        type="submit"
                        name="remove_from_cart"
                        class="btn remove-btn">
                        Remove
                    </button>
                </form>
            </td>
        </tr>

        <?php endforeach; ?>

    </table>

    <div class="total">
        Total: ₹<?= number_format($total_cost, 2); ?>
    </div>

    <form action="checkout.php" method="POST">
    <button type="submit" class="btn" style="background:orange; margin-top:15px;">
        Place Order
    </button>
</form>

    <?php else: ?>

        <h3 style="text-align:center;">Your Cart is Empty</h3>

    <?php endif; ?>

    <a href="../index.php" class="shop-btn">
    Continue Shopping
</a>

<a href="my_orders.php" class="shop-btn">
    My Orders
</a>

</div>

</body>
</html>