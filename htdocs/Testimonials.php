<?php
session_start();
@include "include/config.php";

$sqlfeed = "SELECT f.rating, f.comment, f.created_at, c.cname, u.name, u.profile_picture 
FROM feedback f 
JOIN car_list c ON f.vid = c.vid 
JOIN reguser u ON f.uid = u.uid 
ORDER BY f.created_at DESC";

$resultfeed = mysqli_query($conn, $sqlfeed);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <title>Testimonials</title>
    <style>
        /* styles.css */
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .testimonials {
            text-align: center;
            padding: 50px 20px;
        }

        .section-title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #222;
        }

        .testimonial-card {
            background: #fff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            text-align: left;
        }

        .testimonial-header {
            display: flex;
            /* align-items: center; */
            margin-bottom: 15px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .name-and-stars {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .name {
            color: black;
            margin: 0;
            font-size: 23px;
            font-weight: bold;
        }

        .role {
            margin: 0;
            color: #777;
            font-size: 18px;
            line-height: 29px;
        }

        .testimonial-text {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .stars {
            color: #f5c518;
            font-size: 27px;
        }

        #bg_img {
            width: 100%;
            margin-top: 5px;
        }

        .banner-text {
            position: absolute;
            top: 170px;
            color: #fff;
            padding: 10px;
            font-size: 45px;
            left: 23%;

        }
        .date{
            color: #000;
        }
       

        .badge {
            background-color: #ffdddd;
            color: #ff4d4d;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 18px;
            display: inline-block;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <?php
    @include "navbar.php";
    ?>
    <div class="banner-card">
        <img id="bg_img" src="image/our_team_bg1.png" alt="" srcset="">
        <div class="banner-text">
            <h1>Testimonials</h1>
        </div>


        <section class="testimonials">
            <span class="badge">TESTIMONIALS</span>
            <h2 class="section-title">Love From Clients</h2>
            <div class="detail">
            <?php if (mysqli_num_rows($resultfeed) > 0) {
                while ($rowfeed = mysqli_fetch_assoc($resultfeed)) {
            ?>
                    <div class="testimonial-card">
                        <div class="testimonial-header">
                            <img src="<?php echo $rowfeed['profile_picture']; ?>" alt="<?php echo $rowfeed['name']; ?>" class="avatar">
                            <div>
                                <div class="name-and-stars">
                                    <h3 class="name"><?php echo $rowfeed['name']; ?></h3>
                                    <span class="stars"><?php echo str_repeat("⭐", $rowfeed['rating']); ?></span>
                                </div>
                                <p class="role">For <?php echo $rowfeed['cname']; ?></p>
                            </div>
                        </div>
                        <p class="testimonial-text"><span style="color:#000;">Message:</span> “<?php echo $rowfeed['comment']; ?>”</p>
                        <p class="date">Submitted on: <?php echo date("d M Y", strtotime($rowfeed['created_at'])); ?></p>
                    </div>
            <?php }
            } ?>
            </div>
        </section>
</body>
<?php
@include "footer.php";
?>

</html>