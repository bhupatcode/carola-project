<?php
@include "include/config.php";

$bid=$_GET["bid"];
// Brands fetch karne ka query
$query = "SELECT * FROM brands  where bid='$bid'"; // Latest brands first
$exquery = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Brands</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(221, 224, 227);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .brand-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .b-card {
            width: 250px;
            background: #f9f9f9;
            margin: 15px;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .b-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .b-card h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .b-card p {
            font-size: 14px;
            color: gray;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Brand</h1>
        <div class="brand-list">
            <?php while ($result = mysqli_fetch_assoc($exquery)) { ?>
                <div class="b-card">
                    <img src="upload/brand/<?php echo $result['bimage']?>" alt="Brand Image">
                    <h3><?= htmlspecialchars($result['bname']) ?></h3>
                    <p>Created on: <?= date("d M Y", strtotime($result['created_at'])) ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
