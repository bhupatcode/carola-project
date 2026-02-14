<?php  
session_start();

@include "include/config.php";

// Fetch team members
$sql = "SELECT * FROM team_members WHERE deleted_at IS NULL";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>ot team</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        #bg_img {
            width: 100%;
            margin-top: 5px;
        }

        .banner-text {
            position: absolute;
            top: 180px;
            color: #fff;
            padding: 10px;
            font-size: 45px;
            left: 28%;

        }

        .team-section {
            text-align: center;
            padding: 50px 20px;
        }

        .team-title {
            font-size: 2rem;
            margin-bottom: 40px;
            color: black;
        }

        .team-grid {
            display: flex;
           flex-wrap: wrap;
           justify-content: center;
            gap: 30px;
            padding: 0 10%;
        }

        .team-member {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
            margin-bottom: 25px;
        }

        .team-member img {
            border-radius: 50%;
            width: 145px;
            height: 175px;
            margin-bottom: 15px;
        }

        .name {
            font-size: 1.2rem;
            margin: 10px 0;
            color: black;
        }

        .team-member p {
            color: #777;
            margin-bottom: 15px;
        }

        .social-icons #icon {
            color: #555;
            margin: 0 5px;
            text-decoration: none;
            font-size: 24px;
            color: black;
            border-radius: 50%;

        }

        .social-icons a:hover {
            color: red;
            transition: all 0.5s;
        }
        .red{
            background-color: red;
            position: relative;
        }
    </style>
</head>
<?php
@include "navbar.php";
?>

<body>
    <div class="banner-card">
        <img id="bg_img" src="image/our_team_bg1.png" alt="" srcset="">
        <div class="banner-text">
            <h1>Our Team</h1>
        </div>
        <section class="team-section">
            <h1 class="team-title">The Amazing Team Behind Our Development</h1>
            <div class="team-grid">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="team-member">
                    <img src="admin/<?php echo  $row['image']; ?>" alt="Rahul Bavaliya">
                    <h3 class="name"><?php echo $row['name']; ?></h3>
                    <p><?php echo $row['role']; ?></p>
                    <div class="social-icons">
                        <a id="icon" href="<?php echo $row['facebook']; ?>"><i class="fab fa-facebook"></i></a>
                        <a id="icon" href="<?php echo $row['linkedin'];?>"><i class="fab fa-linkedin"></i></a>
                        <a id="icon" href="<?php  echo $row['instagram']; ?>"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                    <div class="red">

                    </div>
                </div>
                <?php } ?>
                <!-- Add more team members as needed -->
            </div>
        </section>
        <?php
        @include "footer.php";
        ?>
</body>


</html>