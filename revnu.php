<?php
$conn = mysqli_connect("localhost", "root", "", "inventorydb");


if (!$conn) {
    http_response_code(500);
    die("❌ Connection failed: " . mysqli_connect_error());
}

$query = "SELECT SUM(revenue) AS total FROM products";
$result = mysqli_query($conn, $query);

if (!$result) {
    http_response_code(500);
    die("❌ Query failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
$total = $row['total'] ?? 0;

//Output formatted total (plain text)
header("Content-Type: text/plain");
echo number_format($total, 2); // Example: 52,420.00
?>
