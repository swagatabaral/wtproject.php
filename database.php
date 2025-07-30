<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "inventorydb");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sell'])) {
    $product_id = intval($_POST['product']);
    $sell_qty = intval($_POST['quantity']);

    if ($product_id > 0 && $sell_qty > 0) {
        // Get product info using prepared statement
        $stmt = $conn->prepare("SELECT quantity, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $current_qty = $row['quantity'];
            $price = $row['price'];

            if ($sell_qty <= $current_qty) {
                $new_qty = $current_qty - $sell_qty;
                $total_price = $price * $sell_qty;

                // Update product quantity
                $update_stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
                $update_stmt->bind_param("ii", $new_qty, $product_id);
                if ($update_stmt->execute()) {
                    // Insert into sales table
                    $insert_stmt = $conn->prepare("INSERT INTO sales (product_id, quantity, total_price, status) VALUES (?, ?, ?, 'to_ship')");
                    $insert_stmt->bind_param("iid", $product_id, $sell_qty, $total_price);
                    if ($insert_stmt->execute()) {
                        $message = "✅ Product sold and recorded successfully!";
                    } else {
                        $message = "❌ Failed to insert sale record.";
                    }
                    $insert_stmt->close();
                } else {
                    $message = "❌ Failed to update product quantity.";
                }
                $update_stmt->close();
            } else {
                $message = "❌ Not enough stock!";
            }
        } else {
            $message = "❌ Product not found!";
        }
        $stmt->close();
    } else {
        $message = "❌ Invalid product or quantity.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sell Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 2px 5px #aaa;
        }
        select, input[type="number"] {
            width: 95%;
            padding: 8px;
            margin: 8px 0 12px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #2ecc71;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
        }
        .success {
            color: #27ae60;
        }
        .error {
            color: #e74c3c;
        }
    </style>
</head>
<body>

<h2>Sell Product</h2>
<a href="homepagehtml.html">Go to Home Page</a>

<form method="POST">
    <label>Select Product:</label><br>
    <select name="product" required>
        <option value="">--Choose--</option>
        <?php
        $products = mysqli_query($conn, "SELECT id, name, quantity FROM products");
        while ($p = mysqli_fetch_assoc($products)) {
            $product_name = htmlspecialchars($p['name']);
            $product_qty = intval($p['quantity']);
            echo "<option value='{$p['id']}'>{$product_name} (Available: {$product_qty})</option>";
        }
        ?>
    </select><br>

    <label>Quantity to Sell:</label><br>
    <input type="number" name="quantity" min="1" required><br>

    <input type="submit" name="sell" value="Sell">
</form>

<?php if (!empty($message)): ?>
    <div class="message <?php echo (strpos($message, '✅') !== false) ? 'success' : 'error'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

</body>
</html>
