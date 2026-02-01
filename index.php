<?php
session_start();

/* ---------- DATABASE ---------- */
$conn = mysqli_connect("localhost", "root", "", "project");
if (!$conn) 
    {
    die("Database connection failed");
}

/* ---------- CART INIT ---------- */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) 
    {
    $_SESSION['cart'] = [];
}

/* ---------- ADD TO CART ---------- */
if (isset($_GET['add'])) 
    {
    $pid = (int)$_GET['add'];
    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Appna Fruit</title>
<style>
body{font-family:Arial;padding:40px;background:#f4f7f0;}
.product{background:#fff;padding:15px;margin-bottom:15px;border-radius:8px;}
.product a{display:inline-block;margin-top:10px;padding:8px 14px;background:#9bbf59;color:#fff;text-decoration:none;border-radius:4px;}
.top{margin-bottom:30px;}
</style>
</head>
<body>

<div class="top">
    <h1>🍎 Appna Fruit Store</h1>
    <a href="cart/cart_simple.php">Go to Cart (<?php echo count($_SESSION['cart']); ?>)</a>
</div>

<?php
$res = mysqli_query($conn, "SELECT * FROM products");
while ($row = mysqli_fetch_assoc($res)):
?>
<div class="product">
    <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
    <p>₹<?php echo $row['price']; ?></p>

    <!-- IMPORTANT: product_id is dynamic -->
    <a href="index.php?add=<?php echo $row['product_id']; ?>">
        Add to Cart
    </a>
</div>
<?php endwhile; ?>

</body>
</html>