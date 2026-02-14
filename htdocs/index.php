<?php
$index = 0;
//@include "./connection.php";
@include "include/config.php";
session_start();
error_reporting(0);
@include "include/loader.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Product Card</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
    <style>
        @font-face {
            font-family: 'pop-regular';
            src: url('font/Poppins-Regular.ttf');
        }

        body {
            text-transform: capitalize;
            font-family: 'pop-regular';
            /* background-color: #f5f5f5; */
            /* display: flex; */
            justify-content: center;
            align-items: center;
            /* height: 100vh; */
            margin: 0;
        }

        .fleet {
            display: flex;
            flex-wrap: wrap;
            background-color: rgb(203, 231, 230);
            margin-top: 20px;
            justify-content: center;
            margin-left: -28px;
            margin-right: -28px;
        }

        .card {
            background-color: white;
            width: 315px;
            max-width: 1050px;
            border-radius: 10px;
            box-shadow: 0 4px 12px -5px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            padding: 20px;
            margin: 17px 10px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: -20px;
            /* margin-bottom: -25px; */
        }

        .card-title {
            font-size: 27px;
            color: #333;
            font-weight: bold;
        }

        .card-image img {
            width: 300px;
            margin: 20px 0px;
            border-radius: 5px; 
             margin-top: 0px; 
            height: 280px;
            align-items: center;
            margin-left: -13px;
            margin-top: -12px;
            object-fit:cover ;
        }

        .description {
            font-size: 0.875rem;
            color: #666;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .card-footer {
            /* display: flex; */
            align-items: center;
            justify-content: space-between;
            /* margin-top: -14px; */
            /* flex-wrap: wrap; */
        }

        .price {
            font-size: 20px;
            color: #333;
            font-weight: bold;
            margin-top: 10px;
        }

        .order-button {
            background-color: #fff;
            color: #000;
            padding: 7px 15px;
            font-size: 0.875rem;
            border: 1px solid black;
            border-radius: 5px;
            cursor: pointer;
            width: 267px;
        }

        .capacity {
            margin-top: -7px;
            color: #333;
        }

        .fual {
            color: #333;
        }

        .button {
            text-decoration: none;
            color: #000;
            font-size: 20px;
        }

        #header {
            margin-top: 10px;
            /* background-color: #b1d7d6; */
            width: 100%;
        }

        .badge {
            background-color: #ddbebe;
            color: #ff4d4d;

            border-radius: 15px;
            font-size: 18px;
            display: inline-block;
            margin-bottom: 10px;
            margin-top: 14px;
            padding-left: 24px;

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
                .how-it-works {
      text-align: center;
      padding: 2rem 1rem;
      margin-bottom: 40px;
    }

    .head .tag {
      display: inline-block;
      padding: 0.3rem 0.6rem;
      font-size: 18px;
      background-color: #ffdddd;
      color: #ff4d4d;
      border-radius: 20px;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
    }

    .head h1 {
      font-size: 3rem;
      font-weight: bold;
      margin: 0.5rem 0 2rem;
      color: #000;
    }

    /* Steps container */
    .steps {
      display: flex;
      justify-content: center;
      gap: 3rem;
      flex-wrap: wrap;
      
    }

    /* Individual step */
    .step {
      text-align: center;
      /* max-width: 150px; */
    }

    .step .icon {
      position: relative;
      width: 90px;
      height: 87px;
      margin: 0 auto;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
    }

    .step .icon img {
      width: 45px;
      height: 45px;
      cursor: pointer;
    }

    .step .icon .number {
      position: absolute;
      top: -15px;
      left: -16px;
      width: 20px;
      height: 20px;
      /* background: #ff7755; */
      color: #fff;
      font-size: 26px;
      border-radius: 50%;
      display: flex;
      padding: 20px 17px;
      border: 3px solid white;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    .step .name {

      font-family: system-ui;
      margin-top: 1rem;
      font-size: 1.5rem;
      font-weight: 500;
      color: black;
    }

    </style>
</head>
<?php
@include "navbar.php";
?>

<body>
    <?php
    @include "advertisement.php";
    ?>
    <?php
    @include "explore_car.php"
    ?>

    <div class="fleet">
        <div class="header" id="header">
            <span class="badge" style="padding: 4px 11px; width: 178px;">Most Popular Car</span>
            <h1>Most Popular Car</h1>

        </div>
        <?php

$select_car = mysqli_query($conn, "SELECT * from car_list WHERE deleted_at IS NULL  order by vid  desc  LIMIT 3");
if (mysqli_num_rows($select_car) > 0) {
    while ($row = mysqli_fetch_array($select_car)) {
        $image=explode(",",$row['image']);
        //print_r($image); 
?>
<a href="car_detail.php?vid=<?php echo $row['vid']; ?>" >
        <div class="card">
            <div class="card-image">
           <img src="admin/<?php echo $image[0]; ?>">
            </div>
            <div class="card-header">
                <h2 class="card-title"><?php echo ucwords($row['cname']); ?></h2>
            </div>
            <div class="card-body">
                <!-- <p class="description">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>-->
                <hr> 
                <div class="card-footer">
                    <h3 class="price"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo $row['price']; ?>/-(per day)</h3>
                    <h3 class="price" style="margin-top: -10px;"><i class="fa-solid fa-indian-rupee-sign"></i> <?php echo $row['chprice']; ?>/-(per hour)</h3>
                </div>
                <h3 class="capacity"><i class="fa-solid fa-car"></i> Capacity: <?php echo $row['seat']; ?></h3>
                <h3 class="fual"><i class="fa-solid fa-gas-pump"></i> fual: <?php echo $row['fual']; ?></h3>
                <div>
                            <?php if ($_SESSION["alogin"]) { ?>
                                <button class="order-button button" type="submit" name="rent-now">Rent Now</button></a>
                            <?php } else { ?>

                                <button class="order-button"><a href="login.php" class="button">Login For Book</a></button>
                            <?php } ?>
                        </div>
            </div>
        </div> 
        <?php
            };
        };
        ?> 
    </div> 
    <?php
    @include "explore_brand.php";
    ?>
    
    <?php
    @include "we_best.php";
    ?>
<div class="how-it-works">
    <div class="head">
      <h1>How It Works</h1>
    </div>
    <div class="steps">
      <div class="step">
        <div class="icon" style="background-color:#F5A77E;">
          <span class="number" style="background-color:#F5A77E;">1</span>
          <img src="image/profile.svg" alt="User Icon">
        </div>
        <p class="name">Sign up Account</p>
      </div>
      <div class="step">
        <div class="icon" style="background-color:#8462EF;">
          <span class="number" style="background-color:#8462EF;">2</span>
          <img src="image/search.svg" alt="Search Icon">
        </div>
        <p class="name">Search your Vehicle</p>
      </div>
      <div class="step">
        <div class="icon" style="background-color: #84DDB1;">
          <span class="number" style="background-color: #84DDB1;">3</span>
          <img src="image/coin.svg" alt="Payment Icon">
        </div>
        <p class="name">Pay the Car Rent</p>
      </div>
      <div class="step">
        <div class="icon" style="background-color:#F8CB76;">
          <span class="number" style="background-color:#F8CB76;">4</span>
          <img src="image/car.svg" alt="Car Icon">
        </div>
        <p class="name">Take Car to Road</p>
      </div>
    </div>
  </div>
    <?php
    @include "slider.php";
    ?>
    <?php
    @include "footer.php";
    ?>

</body>

</html>