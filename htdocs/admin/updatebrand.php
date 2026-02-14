<?php
@include "include/config.php";

$bid = $_GET['bid'];

// Fetch existing brand details
$sql = "SELECT * FROM brands WHERE bid = $bid";
$exsql = mysqli_query($conn, $sql);
$result = mysqli_fetch_assoc($exsql);

$existingImage = $result['bimage']; // Old Image

if (isset($_POST['update'])) {
    $bname = $_POST['brand-name'];
    $newImage = $_FILES['brand-image'];
    $removeImage = isset($_POST['remove-image']) ? true : false; // Check if user wants to remove image

    if (!empty($bname)) {
        $updateImageName = $existingImage; // Default old image
        $targetDir = "upload/brand/";

        // Handle Image Upload
        if (!empty($newImage['name'])) {
            $imageName = time() . "_" . basename($newImage["name"]);
            $targetFilePath = $targetDir . $imageName;
            $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($imageFileType, $allowedTypes)) {
                if (move_uploaded_file($newImage["tmp_name"], $targetFilePath)) {
                    $updateImageName = $imageName;

                    // Delete old image if exists
                    if (!empty($existingImage) && file_exists($targetDir . $existingImage)) {
                        unlink($targetDir . $existingImage);
                    }
                } else {
                    echo "<script>alert('Error uploading new image.');</script>";
                }
            } else {
                echo "<script>alert('Invalid image format. Only JPG, JPEG, PNG, GIF allowed.');</script>";
            }
        } elseif ($removeImage) {
            // If user selects to remove the image
            if (!empty($existingImage) && file_exists($targetDir . $existingImage)) {
                unlink($targetDir . $existingImage);
            }
            $updateImageName = ""; // Remove from DB
        }

        // Update Database
        $query = "UPDATE brands SET bname='$bname', bimage='$updateImageName' WHERE bid=$bid";
        $exquery = mysqli_query($conn, $query);

        if ($exquery) {
            echo "<script>alert('Brand Updated Successfully!');</script>";
            header("Location: managebrand.php");
        } else {
            echo "<script>alert('Brand Update Failed');</script>";
        }
    } else {
        echo "<script>alert('Brand name cannot be empty');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Update Brand</title>
    <style>
         @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }
        *
        {
            font-family: 'pop-regular';
        }
        body {
            
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
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #d23d49;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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
            display: block;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .remove-image {
            color: red;
            cursor: pointer;
            display: inline-block;
            margin-top: 5px;
        }
    </style>
</head>

<body>
   <div class="container">
        <h1>Update Brand</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="brand-name">Brand Name</label>
                <input type="text" id="brand-name" name="brand-name" value="<?php echo $result['bname']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="brand-image">Brand Image</label>
                <input type="file" id="brand-image" name="brand-image" accept="image/*" onchange="previewImage(event)">
                
                <!-- Show Existing Image -->
                <?php if (!empty($result['bimage'])): ?>
                    <img id="image-preview" class="image-preview" src="upload/brand/<?php echo $result['bimage'] ?>" alt="Current Image">
                    <div>
                        <input type="checkbox" name="remove-image" id="remove-image">
                        <label for="remove-image" class="remove-image">Remove Image</label>
                    </div>
                <?php else: ?>
                    <img id="image-preview" class="image-preview" style="display: none;" alt="New Image Preview">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn" name="update">Update</button>
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
