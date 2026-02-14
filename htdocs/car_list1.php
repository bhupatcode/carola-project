<?php
@include "include/config.php";
session_start();
error_reporting(0);
@include "include/loader.php";


// Pagination Logic
$limit = 6;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch Search Query
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$search_sql = "";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_sql = "AND (cname LIKE '%$search%' OR fual LIKE '%$search%' OR seat LIKE '%$search%')";
}

// Total Entries Count
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM car_list WHERE deleted_at IS NULL $search_sql");
$total_row = mysqli_fetch_assoc($total_result);
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);

// Fetch Search Results
$select_car = "SELECT * FROM car_list WHERE deleted_at IS NULL $search_sql LIMIT $start, $limit";
$result = mysqli_query($conn, $select_car);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Car Fleet</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <style>
        @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }

        body {
            text-transform: capitalize;
            font-family: 'pop-regular', sans-serif;
            background-color: #b1d7d6;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .search-container {
            text-align: center;
            margin: 30px auto;
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 12px 15px;
            font-size: 18px;
            border: 2px solid #007bff;
            border-radius: 30px;
            outline: none;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .search-container input:focus {
            border-color: #ff4d4d;
            box-shadow: 0px 6px 15px rgba(255, 77, 77, 0.3);
            transform: scale(1.02);
        }

      
        .fleet {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            background-color: #b1d7d6;
        }

        .card {
            background-color: #fff;
            width: 309px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
            transition: transform 0.3s;
            text-align: center;
            color: #333;
        }

        /* .card:hover {
            transform: scale(1.05);
        } */

        .card img {
            width: 296px;
            height: 265px;
            object-fit: cover;
            border-radius: 10px;
            margin-top: -15px;
            margin-left: -13px;
        }

        .card-title {
            font-size: 30px;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
        }

        .description {
            font-size: 14px;
            color: #666;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }

        .order-button {
            background-color: #fff;
            color: #000;
            padding: 5px 15px;
            border: 2px solid black;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: block;
            width: 100%;
            text-decoration: none;
            font-size: 24px;
            transition: background 0.3s;
        }

        .order-button:hover {
            background-color: #cc2f39;
            color: #fff;
        }

        #header {
            margin-top: 10px;
            /* background-color: #b1d7d6; */
            width: 100%;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header h1 {
            font-size: 35px;
            margin-bottom: 15px;
            color: black;
        }

        .badge {
            background-color: rgb(214, 191, 191);
            color: #ff4d4d;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 18px;
            display: inline-block;
            margin-bottom: 10px;
            margin-top: 14px;
            padding-left: 20px;
            width: 127px;
        }

        @media (max-width: 768px) {
            .fleet {
                flex-direction: column;
                align-items: center;
            }
        }

        /* .d-flex {
             display: flex;
             justify-content: space-between;
             align-items: center;
             margin-top: 20px;
         } */
        .d-flex {
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            width: 100%;
            text-align: center;
        }

        .entries {
            color: #000;
            margin-right: 0;
            margin-bottom: 20px;
            margin-top: -15px;
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
    </style>
</head>

<?php
@include "navbar.php";
?>

<body style="background-color: #b1d7d6;">

    <div class="search-container">
        <form method="GET">
            <input type="text" id="search" name="search" placeholder="Search cars..." onkeyup="searchCars()" value="<?php echo $search; ?>">
        </form>
    </div>
    <div class="fleet" id="fleet-container">

        <div class="header" id="header">
            <span class="badge">CAR FLEET</span>
            <h1>Car Fleet-1</h1>
        </div>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $image = explode(",", $row['image']);
        ?>

                <div class="card" data-name="<?php echo strtolower($row['cname']); ?>">
                    <img src="../admin/<?php echo $image[0] ?>" alt="Car Image">
                    <h2 class="card-title"> <?php echo $row['cname']; ?> </h2>
                    <!-- <p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p> -->
                    <h3 class="price">Per Day- <i class="fa-solid fa-indian-rupee-sign"></i> <?php echo $row['price']; ?>/-</h3>
                    <h3 class="price">Per Hour- <i class="fa-solid fa-indian-rupee-sign"></i> <?php echo $row['chprice']; ?>/-</h3>
                    <h3 class="capacity"><i class="fa-solid fa-car"></i> Capacity: <?php echo $row['seat']; ?></h3>
                    <h3 class="fual"><i class="fa-solid fa-gas-pump"></i> Fuel: <?php echo $row['fual']; ?></h3>
                    <div>
                        <?php if ($_SESSION["alogin"]) { ?>
                            <a href="car_detail.php?vid=<?php echo $row['vid']; ?>" class="order-button">Rent Now</a>
                        <?php } else { ?>
                            <a href="login.php" class="order-button">Login For Book</a>
                        <?php } ?>
                    </div>
                </div>
            <?php
            }
        } else { ?>
            <p> No Data Found</p>
        <?php }
        ?>
    </div>
    <div class="d-flex">
        <div class="entries">Showing <?php echo $start + 1; ?> to <?php echo min($start + $limit, $total_entries); ?> of <?php echo $total_entries; ?> entries</div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>



    <?php
    @include "footer.php";
    ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function fetchCars() {
        let searchValue = document.getElementById("search").value;

        // AJAX Request
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "search_cars.php?search=" + encodeURIComponent(searchValue), true);
        xhr.onload = function () {
            if (this.status == 200) {
                document.getElementById("fleet-container").innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    // Live search on typing
    document.getElementById("search").addEventListener("keyup", function () {
        fetchCars();
    });

    // // Auto-refresh every 3 seconds
    // setInterval(fetchCars, 3000);
});
</script>



</body>

</html>