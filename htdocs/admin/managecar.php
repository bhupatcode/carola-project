<?php
error_reporting(0);
@include "include/config.php";
// Pagination Logic
// Pagination Logic
$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search Query
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

// Base SQL Query
$sql = "SELECT * FROM car_list WHERE deleted_at IS NULL";

// If Search Exists, Modify Query
if (!empty($search)) {
    $sql .= " AND (cname LIKE '%$search%' OR no_plate LIKE '%$search%' OR brand LIKE '%$search%' OR fual LIKE '%$search%' OR chprice LIKE '%$search' OR  price LIKE '%$search')";
}

// Add Pagination
$sql .= " LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Total Cars for Pagination Count
$total_query = "SELECT COUNT(*) AS total FROM car_list WHERE deleted_at IS NULL";
if (!empty($search)) {
    $total_query .= " AND (cname LIKE '%$search%' OR no_plate LIKE '%$search%' OR brand LIKE '%$search%' OR fual LIKE '%$search%')";
}
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Manage Cars</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <style>
        @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }

        * {
            font-family: 'pop-regular';
        }

        body {

            background: rgb(211, 217, 223);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            /* overflow: hidden; */
        }

        .container {
            background: #fff;
            padding: 12px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            /* width: 100%; */

            text-align: center;
            margin-left: 170px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }



        /* Table Styling */
        /* .table-container {
             overflow-x: auto; 
             overflow-y: auto; 

        }*/

        table {
            width: 95%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            margin-left: 55px;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007BFF;
            color: white;
            text-transform: uppercase;
        }

        tbody tr:hover {
            background: #f1f1f1;
            transition: 0.3s;
        }

        /* Status Badge */
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            color: #000;
        }

        .maintenance {
            background: orange;
        }

        .notavailable {
            background: gray;
        }

        /* Action Icons */
        .edit,
        .delete,
        .view {
            margin: 0 5px;
            font-size: 16px;
            transition: 0.3s;
            padding: 5px 8px;
            border-radius: 5px;
            text-decoration: none;
        }

        .edit {
            background: #28a745;
            color: white;
        }

        .delete {
            background: #dc3545;
            color: white;
        }

        .view {
            background: rgb(45, 96, 207);
            color: white;
        }

        .view:hover {
            background: rgb(67, 97, 161);
        }

        .edit:hover {
            background: #218838;
        }

        .delete:hover {
            background: #c82333;
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

        /* Profile Image */
        td img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .btn-custom {
            margin-right: 281px;
            background-color: #dc3545;
            color: white;
            padding: 8px 27px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-custom:hover {
            background-color: #bb2d3b;
        }

        .search-container {
            margin-right: -32px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .search-container input {
            width: 325px;
            padding: 7px 14px;
            font-size: 16px;
            border: 2px solid #00a8cc;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-container input:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .search-container button {
            background: #007bff;
            color: white;
            padding: 10px 18px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-container button:hover {
            background: #0056b3;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {

            th,
            td {
                padding: 8px;
                font-size: 14px;
            }

            .search-container input {
                width: 100%;
            }

        }
    </style>
</head>

<body>
    <div class="container">
        <h1>🚗 Manage Cars</h1>
        <div class="search-container">
            <a href="add_car.php" class="btn-custom">+ Add Car</a>
            <form method="GET" action="">
                <div class="search-container">
                    <input type="text" name="search" placeholder="Search anything..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit"><i class="fa fa-search"></i> Search</button>
                </div>
            </form>
        </div>
        <div class="table-container">
            <table id="carTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>img</th>
                        <th>Car Name</th>
                        <th>Hour Price</th>
                        <th>Day Price</th>
                        <th>No Plate</th>
                        <th>Brand</th>
                        <th>Seats</th>
                        <th>Fuel</th>
                        <th>Status</th>
                        <th style="padding: 0px 50px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $n = $start + 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $image = explode(',', $row['image'])
                    ?>
                        <tr>
                            <td><?php echo $n; ?></td>
                            <td><img src="<?php echo $image[0]; ?>" alt="car"></td>
                            <td class="car-name"><?php echo $row['cname']; ?></td>
                            <td>₹<?php echo $row['chprice']; ?></td>
                            <td>₹<?php echo $row['price']; ?></td>
                            <td><?php echo $row['no_plate']; ?></td>
                            <td><?php echo $row['brand']; ?></td>
                            <td><?php echo $row['seat']; ?></td>
                            <td><?php echo $row['fual']; ?></td>
                            <td>
                                <span class="status <?php echo strtolower($row['status']); ?>">
                                    <?php
                                    if ($row['status'] == 0) echo "<span class='available'> Available</span>";
                                    elseif ($row['status'] == 1) echo "<span class='booked'>Booked </span>";
                                    elseif ($row['status'] == 2) echo "<span class='maintenance'>Maintenance </span>";
                                    else echo "Not Available";
                                    ?>
                                </span>
                            </td>
                            <td>
                                <a class="view" style="padding:5px 4px;" href="viewcar.php?vid=<?php echo $row['vid']; ?>">
                                    <i style="padding-left:4px;" class="fa-solid fa-eye"></i>
                                </a>
                                <a class="edit" style="padding:5px 4px;" href="update.php?vid=<?php echo $row['vid']; ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a class="delete" href="delete.php?vid=<?php echo $row['vid']; ?>" onclick="return confirm('Do You Really Delete Record');">
                                    <i class="fa-solid fa-trash-restore"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        $n++;
                    }
                    ?>
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
    </div>

 

</body>

</html>