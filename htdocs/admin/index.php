<?php
session_start();

if (!$_SESSION['adlogin']) {
    header("location:admin_login.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Car Rental Portal | Admin Panel</title>
    <!-- <link rel="stylesheet" href="css/dashstyle.css"> -->
    <style>
        @font-face {

            font-family: 'pop-regular';
            src: url('../font/Poppins-Light.ttf');
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'pop-regular';
            display: flex;
            overflow: hidden;
            height: 100vh;
        }

        .container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .sidebar {
            width: 235px;
            background: white;
            color: black;
            height: 100vh;
            padding-top: 20px;
            transition: transform 0.3s ease;
            position: fixed;
            /* Fixed sidebar */
            left: 0;
            top: 0;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none;
        }

        .main-content {
            margin-left: 250px;
            /* Iframe ko adjust karne ke liye */
            width: calc(100% - 250px);
            /* Sidebar ke hisaab se width set karein */
            transition: margin-left 0.3s ease;
        }

        .sidebar h2 {
            padding-top: 15px;
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            margin-bottom: 10px;
            margin-left: 3px;
        }


        .sidebar ul {
            list-style: none;
            padding: 10px;
            font-weight: 600;
        }

        .sidebar ul li {
            padding: 10px;
        }

        .sidebar ul li a {
            color: black;

            text-decoration: none;

            /* font-weight: bold; */
            font-size: 16px;
            display: block;
            padding: 5px 12px;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }


        .sidebar li a:hover {

            background-color: rgb(199, 54, 54);
            color: #FFF;

        }

        /* Close button styling */
        .close-btn {
            position: absolute;
            top: 10px;
            left: 200px;
            /* Right side pe place karne ke liye */
            background: #e63946;
            color: white;
            border: none;
            font-size: 17px;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 50%;
            transition: background 0.3s ease;
            margin-top: -3px;
        }

        .close-btn:hover {
            background: #cc2f39;
        }

        /* Toggle button for opening sidebar */
        .toggle-btn {
            position: absolute;
            top: 13px;
            left: 5px;
            background: #e63946;
            color: #fff;
            border: 0px solid black;
            border-radius: 3px;
            font-size: 26px;
            height: 35px;
            width: 30px;
            cursor: pointer;
            display: none;
        }

        .toggle-btn:hover {
            background-color: #cc2f39;
            color:#fff;
        }

        .sidebar .menu li {
            margin-bottom: -10px;
            color: #000;
        }


        iframe {
            width: 100%;
            height: 100vh;
            border: none;
        }


        /* Jab sidebar hide ho, to iframe full width ho jaye */
        /* Dropdowns ko by default hide karne ke liye */
        .dropdown {
            display: none;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar.hidden+.main-content {
            margin-left: 0;
            width: 100%;
        }

        .down-icon {

            display: inline-block;


            transition: transform 0.3s ease;
            margin-left: 93px;
        }

        .down-icon2 {
            display: inline-block;

            transition: transform 0.3s ease;
            margin-left: 113px;
        }

        .down-icon3 {
            display: inline-block;

            transition: transform 0.3s ease;
            margin-left: 78px;
        }

        .down-icon4 {
            display: inline-block;

            transition: transform 0.3s ease;
            margin-left: 104px;
        }

        .down-icon,
        .down-icon2,
        .down-icon3,
        .down-icon4 {
            transition: transform 0.3s ease;
            display: inline-block;
            /* Ensure inline elements can be rotated */
        }

        .rotate {
            transform: rotate(90deg);
        }
    </style>
</head>

<body>


    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Close Button -->
        <button class="close-btn" onclick="toggleSidebar()">✖</button>
        <h2>Car Rental Portal</h2>

        <ul class="menu">
            <li><a href="index.php">Dashboard</a></li>
            <li>
                <a href="#" id="br-drop">Brands <span class="down-icon">▶</span></a>
                <ul class="dropdown" id="br-dropdown">
                    <li><a href="createbrand.php" target="second">Add Brands</a></li>
                    <li><a href="managebrand.php" target="second">Manage Brands</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="cr-drop">Cars <span class="down-icon2">▶</span></a>
                <ul class="dropdown" id="cr-dropdown">
                    <li><a href="add_car.php" target="second">Add Cars</a></li>
                    <li><a href="managecar.php" target="second">Manage Cars</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="dr-drop">Driver <span class="down-icon4">▶</span></a>
                <ul class="dropdown" id="dr-dropdown">
                    <li><a href="add_driver.php" target="second">Add Driver</a></li>
                    <li><a href="managedriver.php" target="second">Manage Drivers</a></li>
                </ul>
            </li>
            <li>
                <a href="#" id="bk-drop">Bookings <span class="down-icon3">▶</span></a>
                <ul class="dropdown" id="bk-dropdown">
                    <li><a href="new-booking.php" target="second">New Bookings</a></li>
                    <li><a href="confirmed-booking.php" target="second">Confirm Bookings</a></li>
                    <li><a href="canceled-booking.php" target="second">Cancelled Bookings</a></li>
                    <li><a href="return-booking.php" target="second">Returned Bookings</a></li>
                    <li><a href="admin_modify_requests.php" target="second">Change Date Request</a></li>
                </ul>
            </li>
            <li><a href="manage_feedback.php" target="second">Manage Feedback</a></li>
            <li><a href="manage_contactus_query.php" target="second">Manage Contact Us</a></li>
            <li><a href="reguser.php" target="second">Reg Users</a></li>
            <li><a href="report_generate.php" target="second">Report Generate</a></li>
            <li><a href="manage_team.php" target="second">Manage Team</a></li>
            <li><a href="manage_coupon.php" target="second">Manage Coupon</a></li>
            <li><a href="restore.php" target="second">Restore Data</a></li>
        </ul>
    </div>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <iframe name="second" src="main.php" id="iframe"></iframe>
    </div>


    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var mainContent = document.getElementById("main-content");
            var toggleBtn = document.getElementById("toggle-btn");

            if (sidebar.classList.contains("hidden")) {
                sidebar.classList.remove("hidden");
                mainContent.style.marginLeft = "250px";
                mainContent.style.width = "calc(100% - 250px)";
                toggleBtn.style.display = "none";
            } else {
                sidebar.classList.add("hidden");
                mainContent.style.marginLeft = "0";
                mainContent.style.width = "100%";
                toggleBtn.style.display = "block";
            }
        }
    </script>

    <script>
        function toggleDropdown(triggerId, dropdownId) {
            const trigger = document.getElementById(triggerId);
            const dropdown = document.getElementById(dropdownId);
            const icon = trigger.querySelector("span");

            trigger.addEventListener("click", (e) => {
                e.preventDefault();
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
                icon.classList.toggle("rotate");
            });
        }

        // Sabhi dropdowns ke liye function ko call karein
        toggleDropdown("br-drop", "br-dropdown");
        toggleDropdown("cr-drop", "cr-dropdown");
        toggleDropdown("bk-drop", "bk-dropdown");
        toggleDropdown("dr-drop", "dr-dropdown");
    </script>


</body>

</html>