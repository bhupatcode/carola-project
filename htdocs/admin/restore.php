<?php
@include "include/config.php";
// error_reporting(0);

// Get all tables
$allowed_tables = ["reguser", "car_list","admin","brands","contactusquery","coupons","driver","feedback","team_members"]; // Yeh sirf yeh tables show karega
$tables = [];
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($result)) {
    if (in_array($row[0], $allowed_tables)) {
        $tables[] = $row[0];
    }
}


// Get selected table
$records = [];
$selected_table = $_GET['table'] ?? '';
$primary_key = 'id'; // Default ID field

if ($selected_table) {
    // Find primary key dynamically
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $selected_table");
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Key'] == 'PRI') {
            $primary_key = $row['Field'];
            break;
        }
    }

    // Fetch soft-deleted records
    $sql = "SELECT * FROM $selected_table WHERE deleted_at IS NOT NULL";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = $row;
    }
}

// Restore record
if (isset($_GET['restore'])) {
    $id = $_GET['restore'];
    $sql = "UPDATE $selected_table SET deleted_at = NULL WHERE $primary_key = '$id'";
    mysqli_query($conn, $sql);
    // header("Location: restore.php?table=$selected_table");
}

// Permanently delete record
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM $selected_table WHERE $primary_key = '$id'";
    mysqli_query($conn, $sql);
    header("Location: restore.php?table=$selected_table");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Soft Delete Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Scrollbar ko hide karega (Chrome, Safari) */


         @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }
        *
        {
            font-family: 'pop-regular';
        }
        body {
            
            margin: 0;
            padding: 0;
            background: #eef2f7;
            text-align: center;
            overflow-x: hidden;
        }
        .container {
    width: 100%;
    min-height: 100px; /* Minimum height set kar raha hai */
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: auto; /* Scroll bar aayega agar content jyada ho */
}

        h2, h3 {
            color: #333;
        }
        .dropdown-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .dropdown {
            width: 250px;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #007BFF;
            border-radius: 8px;
            outline: none;
        }
        .table-select-btn {
            padding: 12px 18px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .table-select-btn:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #e0e0e0;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }
    

        .edit, .delete ,.view {
            text-decoration: none;
            padding: 4px 5px;
            color: white;
            border-radius: 4px;
        }

        .edit { background-color: #28a745;
            margin-right: 5px; }
        .delete { background-color: #dc3545; }
        .view { background-color:rgb(45, 96, 207); }
    </style>
     <link rel="stylesheet" href="css/all.min.css">
     <link rel="stylesheet" href="css/fontawesome.min.css">
</head>
<body>
    <div class="container">
        <h2>Soft Deleted Records Management</h2>
        
        <h3>Select Table</h3>
        <div class="dropdown-container">
            <select id="tableDropdown" class="dropdown">
                <option value="" disabled selected>Choose a table...</option>
                <?php foreach ($tables as $table): ?>
                    <option value="<?php echo $table; ?>"><?php echo ucfirst($table); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="table-select-btn" onclick="goToTable()">Go</button>
        </div>
        
        <?php if ($selected_table): ?>
    <h3>Soft Deleted Records in <?php echo ucfirst($selected_table) ?></h3>
    <?php if (!empty($records)): ?>
        <table>
            <tr>
                <?php foreach ($records[0] as $key => $value): ?>
                    <th><?php echo $key; ?></th>
                <?php endforeach; ?>
                <th style="padding: 0PX 22PX;">Actions</th>
            </tr>
            <?php foreach ($records as $row): ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                        <td><?php echo (strlen($value) > 30) ? 'Yes' : $value; ?></td>
                        <?php endforeach; ?>
                    <td>
                        <!-- <a href="restore.php?table=<?php echo $selected_table ?>&restore=<?php echo $row[$primary_key] ?>" class="btn restore">Restore</a> -->
                        <button onclick="confirmres(<?php echo $row[$primary_key] ?>)" class="edit"><i class="fas fa-undo"></i></button>
                        <button onclick="confirmDelete(<?php echo $row[$primary_key] ?>)" class="delete"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="color: red; font-size: 18px; margin-top: 20px;">No records found in this table.</p>
    <?php endif; ?>
<?php endif; ?>

        
    </div>
    
    <script>
        function goToTable() {
            var selectedTable = document.getElementById("tableDropdown").value;
            if (selectedTable) {
                window.location.href = "restore.php?table=" + selectedTable;
            }
        }
        function confirmDelete(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "This record will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "restore.php?table=<?php echo $selected_table ?>&delete=" + id;
                }
            });
        }

        function confirmres(id) {
    // Pehle record restore karega
    window.location.href = "restore.php?table=<?php echo $selected_table ?>&restore=" + id + "&restored=true";
}

// Agar URL me `restored=true` mila to success alert show karega
window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("restored") === "true") {
        Swal.fire({
            title: "Success!",
            text: "Record restored successfully.",
            icon: "success",
            confirmButtonColor: "green",
            confirmButtonText: "Ok!"
        }).then(() => {
            window.location.href = "restore.php?table=<?php echo $selected_table ?>";
        });
    }
};

    </script>
    
</body>
</html>