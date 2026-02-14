<?php
@include "include/config.php";

if (isset($_POST['add'])) {
    $bname = $_POST['brand-name'];
    $image = $_FILES['brand-image'];

    if ($bname != "" && !empty($image['name'])) {
        $targetDir = "upload/brand/";

        // Directory check & create
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = time() . "_" . basename($image["name"]);
        $targetFilePath = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($image["tmp_name"], $targetFilePath)) {
                $query = "INSERT INTO brands (bname, bimage) VALUES ('$bname', '$imageName')";
                $exquery = mysqli_query($conn, $query);

                if ($exquery) {
                    echo "<script>alert('Brand inserted successfully!');</script>";
                } else {
                    echo "<script>alert('Brand not inserted.');</script>";
                }
            } else {
                echo "<script>alert('Error uploading image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image format. Only JPG, JPEG, PNG, GIF allowed.');</script>";
        }
    } else {
        echo "<script>alert('Brand name and image are required.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Create Brand</title>
    <style>
        @font-face {
    font-family: 'pop-regular';
    src: url('../font/Poppins-Regular.ttf');
}

        body {
            font-family: 'pop-regular';
            background-color: rgb(221, 224, 227);
        }
        .container {
            width: 70%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 2px solid black;
        }
        h1 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-size: 18px;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="file"] {
            width: 90%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid black;
            border-radius: 3px;
            font-family: 'pop-regular';
        }
        .btn {
            width: 94%;
            padding: 10px;
            background-color: #d23d49;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'pop-regular';
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
            font-weight: bold;
            text-align: center;
        }
        .success { background-color: green; color: #fff; }
        .danger { background-color: red; color: #fff; }
        
        /* Image Preview */
        .image-preview {
            width: 100%;
            max-width: 250px;
            height: auto;
            display: none;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>

<body>
   <div class="container">
        <h1>Create Brand</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="brand-name">Brand Name</label>
                <input type="text" id="brand-name" name="brand-name" placeholder="Enter brand name" required>
            </div>
            <div class="form-group">
                <label for="brand-image">Brand Image</label>
                <input type="file" id="brand-image" name="brand-image" accept="image/*" required onchange="previewImage(event)">
                <img id="image-preview" class="image-preview" alt="Image Preview">
            </div>
            <button type="submit" class="btn" name="add">Submit</button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            var input = event.target;
            var preview = document.getElementById("image-preview");

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
