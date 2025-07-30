<?php
// 1. Connect to the MySQL database
$conn = mysqli_connect("localhost", "root", "", "inventorydb");

// 2. Check if the connection was successful
if (!$conn) {
    // Send a 500 (server error) response code and return error as JSON
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

// 3. SQL query to get monthly sales totals for the last 6 months
$query = "
    SELECT 
        DATE_FORMAT(sale_date, '%b') AS month, -- e.g., Jan, Feb
        SUM(total_price) AS total                -- total sales per month
    FROM sales
    WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) -- last 6 months
    GROUP BY YEAR(sale_date), MONTH(sale_date)  -- group by month & year
    ORDER BY YEAR(sale_date), MONTH(sale_date)  -- sort in correct order
";

// 4. Run the SQL query
$result = mysqli_query($conn, $query);

// 5. Check if query was successful
if (!$result) {
    http_response_code(500); // server error
    echo json_encode(["error" => mysqli_error($conn)]); // show DB error
    exit;
}

// 6. Initialize arrays to hold the chart data
$labels = []; // to store month names
$totals = []; // to store sales amounts

// 7. Loop through each row of the result and store data into arrays
while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row["month"];           // e.g., Jan, Feb
    $totals[] = (float)$row["total"];    // total sales as float
}

// 8. Set the response type to JSON so frontend knows what to expect
header('Content-Type: application/json');

// 9. Send the data as JSON
echo json_encode([
    "labels" => $labels, 
    "sales" => $totals   
]);
?>
