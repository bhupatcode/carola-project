<?php
@include "include/config.php";
$msg = "";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $bio = $_POST['bio'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];
    $facebook = $_POST['facebook'];

    // Image Upload Handling
    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "upload/team/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    if ($name && $role && $bio && $imagePath) {
        $sql = "INSERT INTO team_members (name, role, image, bio, instagram, linkedin, facebook) 
                VALUES ('$name', '$role', '$imagePath', '$bio', '$instagram', '$linkedin', '$facebook')";
        if ($conn->query($sql)) {
            $msg = "Team Member Added Successfully!";
        } else {
            $msg = "Something went wrong!";
        }
    } else {
        $msg = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Team Member</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 550px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .message {
            color: green;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
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

        .social-inputs {
            display: flex;
            gap: 10px;
        }

        .social-inputs input {
            flex: 1;
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

        .image-preview {
            display: none;
            max-width: 150px;
            margin-top: 10px;
            border-radius: 5px;
        }

        @media (max-width: 600px) {
            .container {
                width: 90%;
            }

            .social-inputs {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add Team Member</h2>
        <p class="message"><?php echo $msg; ?></p>
        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="role">Role:</label>
            <input type="text" id="role" name="role" required>

            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" required></textarea>

            <label for="image">Upload Image:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)" required>
            <img id="imagePreview" class="image-preview">

            <label>Social Media Links:</label>
            <div class="social-inputs">
                <input type="url" id="instagram" name="instagram" placeholder="Instagram">
                <input type="url" id="linkedin" name="linkedin" placeholder="LinkedIn">
                <input type="url" id="facebook" name="facebook" placeholder="Facebook">
            </div>

            <button type="submit" name="submit">Add Member</button>
        </form>
    </div>

    <script>
        function validateForm() {
            let name = document.getElementById("name").value;
            let role = document.getElementById("role").value;
            let bio = document.getElementById("bio").value;
            let image = document.getElementById("image").value;
            
            if (name == "" || role == "" || bio == "" || image == "") {
                alert("All fields are required!");
                return false;
            }
            return true;
        }

        function previewImage(event) {
            let reader = new FileReader();
            reader.onload = function() {
                let preview = document.getElementById("imagePreview");
                preview.src = reader.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</body>
</html>