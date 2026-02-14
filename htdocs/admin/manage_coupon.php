<?php
include 'include/config.php'; // Database Connection

// // Fetch All Coupons
// $query = "SELECT * FROM coupons WHERE deleted_at IS NULL ORDER BY cpid DESC";
// $result = mysqli_query($conn, $query);

// Pagination Logic
$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch Cars with Limit and Offset
$sql = "SELECT * FROM coupons WHERE deleted_at IS NULL  ORDER BY cpid  DESC  LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Total Cars for Pagination Count
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM coupons where deleted_at IS NULL");
$total_row = mysqli_fetch_assoc($total_result);
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            width: 100%;
            max-width: 900px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #28a745;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
            transition: 0.3s ease-in-out;
        }

        .action-btn {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: 0.3s;
        }

        .edit-btn {
            background: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background: #0056b3;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #a71d2a;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-custom:hover {
            background-color:rgb(38, 207, 77);
        }
        .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            margin-left: 35px;
        }

       

        .pagination a {
            padding: 8px 12px;
            margin: 0 4px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            background-color: white;
            transition: background-color 0.3s ease;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 14px;
                padding: 10px;
            }

            .action-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
    </style>
     <link rel="stylesheet" href="css/all.min.css">
     <link rel="stylesheet" href="css/fontawesome.min.css">
</head>

<body>

    <div class="container">
        <h2>Manage Coupons</h2>
        <a href="add_coupon.php" class="btn-custom">+ Add Coupon</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Coupon Code</th>
                    <th>Discount</th>
                    <th>Type</th>
                    <th>Expiry Date</th>
                    <th>Usage Limit</th>
                    <th style="padding: 0px 30px;width:123px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $n=$start+1; while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $n; ?></td>
                        <td><strong><?php echo $row['code']; ?></strong></td>
                        <td><?php echo $row['discount']; ?></td>
                        <td><?php echo ucfirst($row['discount_type']); ?></td>
                        <td><?php echo $row['expiry_date']; ?></td>
                        <td><?php echo $row['usage_limit']; ?></td>
                        <td>
                            <a href="edit_coupon.php?cpid=<?php echo $row['cpid']; ?>" class="action-btn edit-btn"> <i class="fa-solid fa-pen"></i></a>
                            <a href="delete.php?delete_id=<?php echo $row['cpid']; ?>" onclick="return confirm('Are you sure you want to delete this coupon?')" class="action-btn delete-btn"> <i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php $n++ ;} ?>
            </tbody>
        </table>
        <div class="d-flex">
                <div style="margin-left: 40px;">Showing <?php echo $start + 1; ?> to <?php echo min($start + $limit, $total_entries); ?> of <?php echo $total_entries; ?> entries</div>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next</a>
                    <?php endif; ?>
                </div>
            </div>

    </div>

</body>

</html>
