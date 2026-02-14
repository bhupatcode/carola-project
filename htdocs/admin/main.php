<?php
session_start();
@include "include/config.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/dashstyle.css">
    <style>
        /* Dropdown container */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Dropdown content (hidden by default) */
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 5px;
            overflow: hidden;
        }

        /* Dropdown links */
        .dropdown-content a {
            color: black;
            padding: 10px;
            display: block;
            text-decoration: none;
            transition: background 0.3s;
        }

        .dropbtn {
            text-decoration: none;
            color: #000;
            font-size: 17px;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show dropdown when active */
        .show {
            display: block;
            margin-top: 13px;
        }
    </style>
</head>

<body>
    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <h1>Dashboard</h1>
            <div class="account dropdown">
                <a href="#" class="dropbtn" onclick="toggleDropdown()">Account <i id="down" class="fa-solid fa-angle-down"></i></a>
                <div id="dropdownMenu" class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="change_password.php">Change Password</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>


        </header>
        <section class="dashboard-cards">
            <!-- regester user -->
            <?php
            $sql = "select * from reguser";
            $ex = mysqli_query($conn, $sql);

            $no1 = mysqli_num_rows($ex);
            ?>
            <div class="card" style="background-color: #3B82F6;">
                <h2><?php echo $no1; ?></h2>
                <p>Reg Users</p>
                <a href="reguser.php">Full Detail →</a>
            </div>

            <!-- listed car -->
            <?php
            $sql1 = "select * from car_list";
            $ex1 = mysqli_query($conn, $sql1);

            $no2 = mysqli_num_rows($ex1);
            ?>
            <div class="card" style="background-color: #22C55E;">
                <h2><?php echo $no2; ?></h2>
                <p>Listed Vehicles</p>
                <a href="managecar.php">Full Detail →</a>
            </div>

            <!-- total booking -->
            <?php
            $newsql = "select  * from booking";
            $exnew = mysqli_query($conn, $newsql);

            $no4 = mysqli_num_rows($exnew);
            ?>
            <div class="card" style="background-color: #60A5FA;">
                <h2><?php echo $no4; ?></h2>
                <p>Total Bookings</p>
                <a href="total_booking.php">Full Detail →</a>
            </div>

            <!--total brand -->
            <?php
            $sbrand = "select * from brands";
            $exsbrand = mysqli_query($conn, $sbrand);

            $no5 = mysqli_num_rows($exsbrand);
            ?>
            <div class="card" style="background-color: #FB923C;">
                <h2><?php echo $no5; ?></h2>
                <p>Listed Brands</p>
                <a href="managebrand.php">Full Detail →</a>
            </div>
            <div class="card" style="background-color: #3B82F6;">
                <h2>2</h2>
                <p>Subscribers</p>
                <a href="#">Full Detail →</a>
            </div>

            <!-- total query -->
            <?php
            $contactquery = "select * from contactusquery";
            $excontactquery = mysqli_query($conn, $contactquery);

            $no6 = mysqli_num_rows($excontactquery);
            ?>
            <div class="card" style="background-color: #22C55E;">
                <h2><?php echo $no6; ?></h2>
                <p>Queries</p>
                <a href="manage_contactus_query.php">Full Detail →</a>
            </div>

            <!-- today total pickup -->
            <?php
            $todaydate = date('Y-m-d');
            $todaypick = "SELECT * FROM booking WHERE Date(FromDate) = '$todaydate'";

            $extodaypick = mysqli_query($conn, $todaypick);

            $no7 = mysqli_num_rows($extodaypick);
            ?>
            <div class="card" style="background-color: #60A5FA;">
                <h2><?php echo $no7; ?></h2>
                <p>Today pickup</p>
                <a href="todaypick.php">Full Detail →</a>
            </div>

            <!-- today total drop-->
            <?php
            $todaydate = date('Y-m-d');
            $todaydrop = "SELECT * FROM booking WHERE Date(ToDate) = '$todaydate'";

            $extodaydrop = mysqli_query($conn, $todaydrop);

            $no8 = mysqli_num_rows($extodaydrop);
            ?>
            <div class="card" style="background-color: #60A5FA;">
                <h2><?php echo $no8; ?></h2>
                <p>Today drop</p>
                <a href="todaydrop.php">Full Detail →</a>
            </div>

            <!-- today total earning -->
            <?php
            $todaydate = date('Y-m-d');
            $todayearn = "SELECT SUM(amount) AS total_earning FROM booking WHERE DATE(PostingDate) = '$todaydate'";

            $extodayearn = mysqli_query($conn, $todayearn);

            if ($extodayearn) {
                $row = mysqli_fetch_assoc($extodayearn);
                $totalEarnings = $row['total_earning'] ?? 0; // Agar NULL ho to 0 dikhe
            } else {
                $totalEarnings = 0;
            }

            ?>

            <div class="card" style="background-color: #60A5FA;">
                <h2><?php echo "₹" . number_format($totalEarnings, 2); ?></h2>
                <p>Total Earning</p>
                <a href="todayearn.php">Full Detail →</a>
            </div>


        </section>
    </main>

</body>
<script>
    function toggleDropdown() {
        document.getElementById("dropdownMenu").classList.toggle("show");
    }

    // Close dropdown if user clicks outside
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>

</html>