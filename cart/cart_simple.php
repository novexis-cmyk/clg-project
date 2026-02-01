<?php
session_start();

/* ---------- DATABASE ---------- */
$conn = mysqli_connect("localhost", "root", "", "project");
if (!$conn) {
    die("Database connection failed");
}

/* ---------- CART INIT ---------- */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];   // product_id => quantity
}

$success_msg = "";
$error_msg   = "";

/* ---------- INCREASE ---------- */
if (isset($_GET['increase'])) {
    $pid = (int)$_GET['increase'];
    $_SESSION['cart'][$pid]++;
    header("Location: cart_simple.php");
    exit;
}

/* ---------- DECREASE ---------- */
if (isset($_GET['decrease'])) {
    $pid = (int)$_GET['decrease'];
    $_SESSION['cart'][$pid]--;
    if ($_SESSION['cart'][$pid] <= 0) {
        unset($_SESSION['cart'][$pid]);
    }
    header("Location: cart_simple.php");
    exit;
}

/* ---------- REMOVE ---------- */
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    header("Location: cart_simple.php");
    exit;
}

/* ---------- CLEAR ---------- */
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header("Location: cart_simple.php");
    exit;
}

/* ---------- BUY ALL ---------- */
if (isset($_GET['buy_all']) && !empty($_SESSION['cart'])) {
    $keys = array_keys($_SESSION['cart']);

    for ($i = 0; $i < count($keys); $i++) {
        $pid = $keys[$i];
        $qty = $_SESSION['cart'][$pid];

        $res = mysqli_query($conn,
            "SELECT quantity FROM stock WHERE product_id=$pid"
        );
        $stock = mysqli_fetch_assoc($res);

        if (!$stock || $stock['quantity'] < $qty) {
            $error_msg = "Insufficient stock!";
            break;
        }
    }

    if ($error_msg === "") {
        for ($i = 0; $i < count($keys); $i++) {
            $pid = $keys[$i];
            $qty = $_SESSION['cart'][$pid];

            mysqli_query($conn,
                "UPDATE stock SET quantity = quantity - $qty WHERE product_id=$pid"
            );
        }
        $_SESSION['cart'] = [];
        $success_msg = "Purchase successful!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Shopping Cart</title>

<style>
/* YOUR SAME DESIGN (unchanged, shortened safely) */
body{font-family:Arial;background:#f4f7f0;padding:40px;}
.cart-item{background:#fff;padding:15px;margin-bottom:10px;display:flex;justify-content:space-between;}
.quantity-btn{padding:5px 10px;background:#9bbf59;color:#fff;text-decoration:none;}
.remove-btn{background:#dc3545;color:#fff;padding:5px 10px;text-decoration:none;}
</style>
</head>
<body>

<h2>Shopping Cart</h2>

<?php if ($success_msg) echo "<p style='color:green'>$success_msg</p>"; ?>
<?php if ($error_msg) echo "<p style='color:red'>$error_msg</p>"; ?>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Cart is empty</p>
<?php else: ?>

<?php
$total = 0;
$keys = array_keys($_SESSION['cart']);

for ($i = 0; $i < count($keys); $i++):
    $pid = $keys[$i];
    $qty = $_SESSION['cart'][$pid];

    $p = mysqli_fetch_assoc(
        mysqli_query($conn,"SELECT * FROM products WHERE product_id=$pid")
    );
    if (!$p) continue;

    $price = $p['price'] * $qty;
    $total += $price;
?>
<div class="cart-item">
    <div>
        <b><?php echo htmlspecialchars($p['product_name']); ?></b><br>
        ₹<?php echo $price; ?>
    </div>
    <div>
        <a href="?decrease=<?php echo $pid; ?>" class="quantity-btn">-</a>
        <?php echo $qty; ?>
        <a href="?increase=<?php echo $pid; ?>" class="quantity-btn">+</a>
        <a href="?remove=<?php echo $pid; ?>" class="remove-btn">X</a>
    </div>
</div>
<?php endfor; ?>

<p><b>Total: ₹<?php echo $total; ?></b></p>
<a href="?clear=1">Clear Cart</a> |
<a href="?buy_all=1">Buy All</a>
<a href="../index.php">Back to Home</a>
<?php endif; ?>

</body>
</html>
