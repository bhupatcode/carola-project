<?php
@include "include/config.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM team_members WHERE id=$id";
    $result = $conn->query($query);
    $member = $result->fetch_assoc();
}

// Update Process
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $bio = $_POST['bio'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];
    $facebook = $_POST['facebook'];

    $imagePath = $member['image']; // Old Image Path

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "upload/team/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $sql = "UPDATE team_members SET 
                name='$name', role='$role', bio='$bio', 
                instagram='$instagram', linkedin='$linkedin', 
                facebook='$facebook', image='$imagePath' 
            WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: manage_team.php?msg=Member Updated Successfully!");
    } else {
        echo "Error updating record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Team Member</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            overflow-y: auto;
            max-height: 90vh;
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        label {
            text-align: left;
            font-weight: bold;
            color: #555;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            resize: none;
            height: 80px;
        }

        .image-preview {
            display: block;
            max-width: 100px;
            margin: 10px auto;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            body {
                height: auto;
                align-items: flex-start;
            }
            .container {
                width: 90%;
                max-height: none;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit Team Member</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required>

            <label>Role:</label>
            <input type="text" name="role" value="<?php echo htmlspecialchars($member['role']); ?>" required>

            <label>Bio:</label>
            <textarea name="bio" required><?php echo htmlspecialchars($member['bio']); ?></textarea>

            <label>Instagram:</label>
            <input type="url" name="instagram" value="<?php echo htmlspecialchars($member['instagram']); ?>">

            <label>LinkedIn:</label>
            <input type="url" name="linkedin" value="<?php echo htmlspecialchars($member['linkedin']); ?>">

            <label>Facebook:</label>
            <input type="url" name="facebook" value="<?php echo htmlspecialchars($member['facebook']); ?>">

            <!-- Old Image Preview -->
            <?php if (!empty($member['image'])): ?>
                <label>Current Image:</label>
                <img id="oldImagePreview" class="image-preview" src="<?php echo $member['image']; ?>" alt="Old Image">
            <?php endif; ?>

            <!-- New Image Upload -->
            <label>Upload New Image:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewNewImage(event)">

            <!-- New Image Preview -->
            <img id="newImagePreview" class="image-preview" style="display: none;">

            <button type="submit" name="update">Update Member</button>
        </form>
        <a href="manage_team.php" class="back-link">← Back to Manage Team</a>
    </div>

    <script>
        function previewNewImage(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let newPreview = document.getElementById("newImagePreview");
                newPreview.src = reader.result;
                newPreview.style.display = "block";

                let oldImage = document.getElementById("oldImagePreview");
                if (oldImage) {
                    oldImage.style.display = "none";
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</body>
</html>
