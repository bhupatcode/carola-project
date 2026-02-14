<?php
@include "include/config.php";
error_reporting(0);

// Fetch Search Query
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$search_sql = "";

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_sql = "AND (cname LIKE '%$search%' OR fual LIKE '%$search%' OR seat LIKE '%$search%')";
}

// Fetch Matching Cars
$select_car = "SELECT * FROM car_list WHERE deleted_at IS NULL $search_sql LIMIT 6";
$result = mysqli_query($conn, $select_car);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $image = explode(",", $row['image']);
        ?>
        <div class="card" data-name="<?php echo strtolower($row['cname']); ?>">
            <img src="../admin/<?php echo $image[0] ?>" alt="Car Image">
            <h2 class="card-title"> <?php echo $row['cname']; ?> </h2>
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
} else {
    echo "<p>No Data Found</p>";
}
?>
