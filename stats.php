<?php

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "inventorydb";


$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "❌ DB connection failed: " . mysqli_connect_error()]);
    exit;
}


$response = [
    'to_ship'         => 0,
    'to_arrive'       => 0,
    'shipped_products'=> 0,
    'total_quantity'  => 0,
    'total_revenue'   => 0.00,
    'sales_data'      => []
];

$status_query = "SELECT status, SUM(quantity) AS total FROM products GROUP BY status";
$status_result = mysqli_query($conn, $status_query);

if ($status_result) {
    while ($row = mysqli_fetch_assoc($status_result)) {
        $status = $row['status'];
        $qty    = (int)$row['total'];

        switch ($status) {
            case 'to_ship':
                $response['to_ship'] = $qty;
                break;
            case 'to_arrive':
                $response['to_arrive'] = $qty;
                break;
            case 'shipped':
                $response['shipped_products'] = $qty;
                break;
            case 'store':
                $response['total_quantity'] = $qty;
                break;
        }
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => "❌ Failed to fetch status quantities: " . mysqli_error($conn)]);
    exit;
}

// ─────────────────────────────
// 2. Get total revenue from shipped sales
// ─────────────────────────────
$revenue_query = "SELECT IFNULL(SUM(total_price), 0) AS revenue FROM sales WHERE status = 'shipped'";
$revenue_result = mysqli_query($conn, $revenue_query);

if ($revenue_result) {
    $row = mysqli_fetch_assoc($revenue_result);
    $response['total_revenue'] = floatval($row['revenue']);
} else {
    http_response_code(500);
    echo json_encode(["error" => "❌ Revenue query failed: " . mysqli_error($conn)]);
    exit;
}

$last6Months = date("Y-m-01", strtotime("-5 months")); // Start from 5 months ago
$sales_query = "
    SELECT DATE_FORMAT(date, '%b') AS month, IFNULL(SUM(total_price), 0) AS sales
    FROM sales
    WHERE date >= '$last6Months' AND status = 'shipped'
    GROUP BY YEAR(date), MONTH(date)
    ORDER BY YEAR(date), MONTH(date)
";
$sales_result = mysqli_query($conn, $sales_query);

if ($sales_result) {
    while ($row = mysqli_fetch_assoc($sales_result)) {
        $response['sales_data'][] = [
            "month" => $row['month'],
            "sales" => floatval($row['sales'])
        ];
    }
} else {
    http_response_code(500);
    echo json_encode(["error" => " Sales chart data fetch failed: " . mysqli_error($conn)]);
    exit;
}

// Close connection and return response
mysqli_close($conn);
echo json_encode($response);
