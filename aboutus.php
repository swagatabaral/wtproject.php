<?php

$provider_name = "Smart Inventory Provider";
$about_text = "
We are a reliable Inventory Management Service Provider, helping small and large businesses efficiently manage their stock.

Our system allows you to easily track inventory, view sales reports, reorder products, and update data in real-time.

We focus on saving your time, simplifying operations, and helping your business grow. With our smart inventory system, you can scale your operations with confidence.
";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About | <?php echo $provider_name; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            background: white;
            margin: 50px auto;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h1 {
            color: #333;
        }
        p {
            font-size: 1.1em;
            color: #444;
            line-height: 1.6em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>About <?php echo $provider_name; ?></h1>
        <p><?php echo nl2br($about_text); ?></p>
    </div>
</body>
</html>
