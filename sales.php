<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventorydb";

$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => '❌ Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

// Query to get top 5 shipped products by quantity
$query = "SELECT name, quantity FROM products WHERE status = 'shipped' ORDER BY quantity DESC LIMIT 5";
$result = mysqli_query($conn, $query);

// Result check
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => '❌ Query failed: ' . mysqli_error($conn)]);
    exit;
}

// Prepare data
$sales = [];
while ($row = mysqli_fetch_assoc($result)) {
    $sales[] = [
        'name'     => $row['name'],
        'quantity' => (int) $row['quantity']
    ];
}

// Return data
echo json_encode($sales);
?>
