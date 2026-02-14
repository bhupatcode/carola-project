<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands</title>
    <link rel="stylesheet" href="fontawesome-free-6.4.0-web/css/all.min.css">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <style>
        @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }

        body {
            font-family: 'pop-regular', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;

        }

        .btn-custom:hover {
            background-color: #bb2d3b;
        }

        input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'pop-regular';
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-warning {
            color: orange;
            text-decoration: none;
        }

        .text-danger {
            color: red;
            text-decoration: none;
        }

        .pagination a {
            margin: 0 4px;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ddd;
            color: black;
        }

        .pagination a.active {
            background-color: rgb(60, 165, 179);
            color: white;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
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

        .delete {
            background: #dc3545;
            color: white;
        }

        .delete:hover {
            background: #c82333;
        }

        .edit:hover {
            background: #218838;
        }

        .edit {
            background: #28a745;
            color: white;
        }

        .view {
            background: rgb(45, 96, 207);
            color: white;
        }

        .view:hover {
            background: rgb(67, 97, 161);
        }

        td img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .search-container input {
            width:180px;
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
</head>

<body>
    <div class="container">
        <h1>Manage Brands</h1>
        <div class="d-flex">
            <a href="createbrand.php" class="btn-custom">+ Add Brand</a>
            <form method="GET" action="">
                <div class="search-container">
                    <input type="text" name="search" placeholder="Search brands..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit"><i class="fa fa-search"></i> Search</button>
                </div>
            </form>

        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Brand Name</th>
                    <th>Creation Date</th>
                    <th>Updation Date</th>
                    <th style="padding: 0px 53px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                @include 'include/config.php';
                // Pagination setup
                $limit = 5;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // Default Query
                $query = "SELECT * FROM brands WHERE deleted_at IS NULL";

                // Search functionality
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = mysqli_real_escape_string($conn, $_GET['search']);
                    $query .= " AND bname LIKE '%$search%'";
                }

                // Count total entries
                $countQuery = str_replace("SELECT *", "SELECT COUNT(*) AS total", $query);
                $result = mysqli_query($conn, $countQuery);
                $row = mysqli_fetch_assoc($result);
                $total_entries = $row['total'];

                // Pagination logic
                $query .= " LIMIT $start, $limit";
                $exquery = mysqli_query($conn, $query);

                $n = $start + 1;
                while ($row = mysqli_fetch_assoc($exquery)) {
                ?>
                    <tr>
                        <td><?php echo $n; ?></td>
                        <td><img src="upload/brand/<?php echo $row['bimage']; ?>" alt="bimage"></td>
                        <td><?php echo $row['bname']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['updated_at']; ?></td>
                        <td>
                            <a class="view" href=viewbrand.php?bid=<?php echo $row['bid']; ?>">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a class="edit" style="padding:5px 4px;" href="updatebrand.php?bid=<?php echo $row['bid']; ?>">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a class="delete" href="delete.php?bid=<?php echo $row['bid']; ?>">
                                <i class="fa-solid fa-trash"></i>
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
            <span>Showing <?php echo $start + 1; ?> to <?php echo min($start + $limit, $total_entries); ?> of <?php echo $total_entries; ?> entries</span>
            <div class="pagination">
                <?php if ($page > 1) : ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= ceil($total_entries / $limit); $i++) : ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < ceil($total_entries / $limit)) : ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>

</body>

</html>