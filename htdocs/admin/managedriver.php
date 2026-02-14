<?php
@include "include/config.php";


// Pagination Logic
$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search Query Handling
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$search_query = "";

if (!empty($search)) {
    $search_query = "AND (dfname LIKE '%$search%' OR city LIKE '%$search%' OR fnumber LIKE '%$search%')";
}

// Fetch Drivers with Search and Pagination
$sql = "SELECT * FROM driver WHERE deleted_at IS NULL $search_query LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Total Count for Pagination
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM driver WHERE deleted_at IS NULL $search_query");
$total_row = mysqli_fetch_assoc($total_result);
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Manage Drivers</title>
 
    <link rel="stylesheet" href="styles.css">
    <style>
       
          @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }

        body {
            font-family: 'pop-regular';
            background: rgb(211, 217, 223);
            margin: 0;
            padding: 10px;
        }

        .container {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 1400px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
        }

        .search-container {
            margin-bottom: 10px;
            text-align: right;
        }

        .search-container input {
            padding: 8px;
            width: 200px;
            font-family: 'pop-regular';
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
            /* padding: 0px 45px; */
        }

       /* Profile Image */
       td img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .edit, .delete ,.view {
            text-decoration: none;
            padding: 4px 5px;
            color: white;
            border-radius: 4px;
        }

        .edit { background-color: #28a745;
            margin-right: 5px; }
        .delete { background-color: #dc3545; }
        .view { background-color:rgb(45, 96, 207); }

        .pagination {
            margin-top: 15px;
            text-align: center;
        }

        .pagination a {
            margin: 0 4px;
            padding: 6px 12px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: black;
        }

        .pagination a.active { background-color: #007bff; color: white; }

        .btn-custom:hover {
            background-color: #bb2d3b;
        }
        .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            margin-left: 35px;
        }
        .btn-custom {
            margin-right: 720px;
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
            margin-right: 12px;
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
    </style>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
</head>
<body>
    <div class="container">
        <h1>Manage Drivers</h1>
        <div class="search-container">
        <a href="add_driver.php" class="btn-custom">+ Add driver</a>
        <form method="GET" action="">
                <div class="search-container">
                    <input type="text" name="search" placeholder="Search anything..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit"><i class="fa fa-search"></i> Search</button>
                </div>
            </form>        
        </div>
        <table id="driverTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Hour Price</th>
                    <th>Day Price</th>
                    <th>Licence Type</th>
                    <th>Aadhar</th>
                    <th>Licence</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $n = $start + 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $n++; ?></td>
                        <td><img src="<?php echo $row['profile']; ?>" alt="Driver"></td>
                        <td><?php echo $row['dfname']; ?></td>
                        <td><?php echo $row['fnumber']; ?></td>
                        <td>₹<?php echo $row['hprice']; ?></td>
                        <td>₹<?php echo $row['dprice']; ?></td>
                        <td><?php echo $row['type_licence']; ?></td>
                        <td><?php echo (!empty($row['adhar_pdf'])) ? 'Yes' : 'No'; ?></td>
                        <td><?php echo (!empty($row['licence_pdf'])) ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $row['city']; ?></td>
                        <td><?php echo $row['status'] == 0 ? 'Available' : 'Not Available'; ?></td>
                        <td>
                            <a href="viewdriver.php?did=<?php echo $row['did']; ?>" class="view"><i class="fa-solid fa-eye"></i></a>
                            <a href="updatedriver.php?did=<?php echo $row['did']; ?>" class="edit"><i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="delete.php?did=<?php echo $row['did']; ?>" class="delete" onclick="return confirm('Do You Really Delete Record');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="d-flex">
                <div>Showing <?php echo $start + 1; ?> to <?php echo min($start + $limit, $total_entries); ?> of <?php echo $total_entries; ?> entries</div>
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
