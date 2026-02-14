<?php
@include "include/config.php";

// Fetch team members
$sql = "SELECT * FROM team_members";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Team</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    text-align: center;
}

.team-section h2 {
    margin-top: 20px;
    font-size: 2em;
}

.team-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.team-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s;
    cursor: pointer;
    width: 200px;
    text-align: center;
}

.team-card:hover {
    transform: scale(1.05);
}

.team-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 10px;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: left;
    width: 50%;
}

.close {
    float: right;
    font-size: 1.5em;
    cursor: pointer;
}

    </style>
</head>
<body>

    <section class="team-section">
        <h2>Meet Our Team</h2>
        <div class="team-container">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="team-card" onclick="showBio('<?php echo $row['name']; ?>', '<?php echo $row['bio']; ?>')">
                    <img src="admin/<?php echo $row['image']; ?>" alt="Team Member">
                    <h3><?php echo $row['name']; ?></h3>
                    <p><?php echo $row['role']; ?></p>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- Modal for Bio -->
    <div id="bioModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBio()">&times;</span>
            <h2 id="modalName"></h2>
            <p id="modalBio"></p>
        </div>
    </div>

    <script>function showBio(name, bio) {
    document.getElementById("modalName").innerText = name;
    document.getElementById("modalBio").innerText = bio;
    document.getElementById("bioModal").style.display = "flex";
}

function closeBio() {
    document.getElementById("bioModal").style.display = "none";
}
 </script>
</body>
</html>
