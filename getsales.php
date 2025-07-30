<?php
$conn = mysqli_connect("localhost", "root", "", "inventorydb");

if (!$conn) {
    die(json_encode(["error" => "Connection failed: " . mysqli_connect_error()]));
}

// Get monthly sales (past 6 months)
$query = "
  SELECT DATE_FORMAT(sale_date, '%b') AS month, SUM(total_price) AS total
  FROM sales
  WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
  GROUP BY YEAR(sale_date), MONTH(sale_date)
  ORDER BY YEAR(sale_date), MONTH(sale_date)
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die(json_encode(["error" => "Query failed: " . mysqli_error($conn)]));
}

// Prepare arrays
$labels = [];
$sales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row["month"];
    $sales[] = (float) $row["total"];
}

// Output JSON
header('Content-Type: application/json');
echo json_encode(["labels" => $labels, "sales" => $sales]);
?>
