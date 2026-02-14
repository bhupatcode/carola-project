<?php
@include "include/config.php";

// // Delete Functionality
// if (isset($_GET['delete'])) {
//     $id = $_GET['delete'];
//     $deleteQuery = "DELETE FROM team_members WHERE id=$id";
//     if ($conn->query($deleteQuery)) {
//         header("Location: manage_team.php?msg=Member Deleted Successfully!");
//     } else {
//         echo "Error deleting member.";
//     }
// }



$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch Cars with Limit and Offset
$sql = "SELECT * FROM team_members WHERE deleted_at IS NULL LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Total Cars for Pagination Count
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM team_members WHERE deleted_at IS NULL");
$total_row = mysqli_fetch_assoc($total_result);
$total_entries = $total_row['total'];
$total_pages = ceil($total_entries / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team</title>
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome.min.css">
<style>
     @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }
    body {
        font-family: 'pop-regular';
        background-color: #f4f4f4;
        text-align: center;
    }

    .container {
        width: 90%;
        margin: 30px auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    h2 {
        margin-bottom: 20px;
    }

    .message {
        color: green;
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        margin-left: -10px;
    }

    th, td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: center;
    }

    th {
        background-color: #28a745;
        color: white;
    }

    .actions a {
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
        margin: 0 5px;
    }

    .edit {
        background-color: #007bff;
        color: white;
    }

    .delete {
        background-color: #dc3545;
        color: white;
    }

    .delete:hover {
        background-color: #c82333;
    }

    .edit:hover {
        background-color: #0056b3;
    }
    .btn-custom {
        background-color: #dc3545;
    color: white;
    padding: 5px 11px;
    text-decoration: none;
    border-radius: 5px;
    margin-right:440px;
    font-size: 15px;
        }

        .btn-custom:hover {
            background-color: #bb2d3b;
        }
       td img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }  
        .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            margin-left: 35px;
        }

        .pagination a {
            padding: 8px 12px;
            margin: 0 4px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            background-color: white;
            transition: background-color 0.3s ease;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }
</style>
</head>
<body>

    <div class="container">
        <h2>Manage Team Members</h2>
        <a href="add_team_member.php" class="btn-custom">+ Add Team Member</a>
        <p class="message"><?php if (isset($_GET['msg'])) echo $_GET['msg']; ?></p>
        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Role</th>
                <th>Instagram</th>
                <th>LinkedIn</th>
                <th>Facebook</th>
                <th style="padding: 0px 30px;">Actions</th>
            </tr>
            <?php $n=$start+1; 
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $n; ?></td>
                    <td><img src="<?php echo $row['image']; ?>" width="50" height="50"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td><a href="<?php echo $row['instagram']; ?>" target="_blank">Instagram</a></td>
                    <td><a href="<?php echo $row['linkedin']; ?>" target="_blank">LinkedIn</a></td>
                    <td><a href="<?php echo $row['facebook']; ?>" target="_blank">Facebook</a></td>
                    <td class="actions">
                        <a href="edit_team.php?id=<?php echo $row['id']; ?>" class="edit"><i class="fa-solid fa-pen"></i></a>
                        <a href="delete.php?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this member?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php $n++;} ?>
        </table>
        <div class="d-flex">
                <div>Showing <?php echo $start + 1; ?> to <?php echo min($start + $limit, $total_entries); ?> of <?php echo $total_entries; ?> entries</div>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next</a>
                    <?php endif; ?>
                </div>
            </div>
    </div>

</body>
</html>
