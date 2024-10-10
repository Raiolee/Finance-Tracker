<?php
// Disable all error reporting
error_reporting(0);
session_start();
if(!isset($_SESSION["user"]))
{
    header("Location: ../index.php");
}
$uid = $_SESSION["ID"];
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/Interface1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Dashboard</title>
</head>
<body class="container">
    <div class="nav-bar">
        <div class="Profile"> 
            <div class="Profile_img">
                <img src="https://picsum.photos/100/100" alt="" width="110">
            </div>
        </div>

        <div class="user-name">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>

        <div class="Home-Nav <?php echo ($current_page == 'Dashboard.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/home.svg" alt="Icon" width="50px" id="icons">
            </div>
            <div>
                <p><a href="Dashboard.php">Home</a></p>
            </div>
        </div>

        <div class="Expenses-Nav <?php echo ($current_page == 'Expenses.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/expenses.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Expenses.php">Expenses</a></p>
            </div>
        </div>

        <div class="Travels-Nav <?php echo ($current_page == 'Travels.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/travels.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Travels.php">Travels</a></p>
            </div>
        </div>

        <div class="Travels-Nav <?php echo ($current_page == 'Income.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/income.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Income.php">Income</a></p>
            </div>
        </div>

        <div class="Approvals-Nav <?php echo ($current_page == 'Approvals.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/approvals.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Approvals.php">Approval</a></p>
            </div>            
        </div>

        <div class="Report-Nav <?php echo ($current_page == 'Report.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/reports.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Report.php">Report</a></p>
            </div>
        </div>

        <div class="Settings-Nav <?php echo ($current_page == 'Settings.php') ? 'active' : ''; ?>" id="Nav_Button">
            <div>
                <img src="../Assets/Icons/settings.svg" alt="Icon" width="50px">
            </div>
            <div>
                <p><a href="Settings.php">Settings</a></p>
            </div>
        </div>

        <div class="Logo-Nav" id="Nav_Side">
            <div class="Penny_Logo">
                <img src="../Assets/PENNY_WISE_Logo.png" alt="" width="200">
            </div>
        </div>

    </div>

    <div class="content">
    <div class="right-container">
        <div class="Inner-container">
            <div class="Top-container-Approval">
                <div class="Left-Top">
                    <p>Approvals</p>
                </div>
                <div class="Right-Top">
                    <img src="../Assets/Icons/home.svg" alt="Icon" width="30px" id="icons">
                    <img src="../Assets/Icons/home.svg" alt="Icon" width="30px" id="icons">
                    <img src="../Assets/Icons/home.svg" alt="Icon" width="30px" id="icons">
                </div>
            </div>

            <hr class="bottom-line">

            <?php
include '../connection/config.php'; // Include the database connection

// Fetch data from the User_Approvals_Data table based on the user UID
$sql = "SELECT Order_ID, Owner_Name, Position, Category, Amount, Frequency FROM User_Approvals_Data WHERE UID= ?"; // Use placeholder for UID
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid); // Assuming UID is an integer; change to "s" if it's a string
$stmt->execute();
$result = $stmt->get_result();
?>

<table class="table-approval">
    <thead>
        <tr>
            <th>OWNER</th>
            <th>CATEGORY</th>
            <th>AMOUNT</th>
            <th>FREQUENCY</th>
            <th>ACTION</th>
        </tr>
    </thead>
    <tbody>
    <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['Owner_Name']}<br>{$row['Position']}</td>
                        <td>{$row['Category']}</td>
                        <td>\${$row['Amount']}</td>
                        <td>{$row['Frequency']}</td>
                        <td>
                            <div class='button-container'>
                                <button>Edit</button>
                                <button onclick=\"showPopup('{$row['Owner_Name']}', '{$row['Position']}', '{$row['Category']}', '\${$row['Amount']}', '{$row['Frequency']}')\">Edit</button>
                                <button onclick=\"deleteEntry({$row['Order_ID']})\">Delete</button>
                            </div>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No results found</td></tr>";
        }
    ?>
    </tbody>    
</table>
<?php
$stmt->close(); // Close the statement
$conn->close(); // Close the connection
?>


            <div id="popup" class="popup" style="display:none;">
                <div class="popup-content">
                    <span class="close" onclick="closePopup()">&times;</span>
                    <h2 id="popup-title"></h2>
                    <p id="popup-description"></p>
                    <div class="popup-buttons">
                        <button id="confirm-btn" onclick="handleConfirm()">Decline</button>
                        <button id="cancel-btn" onclick="closePopup()">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteEntry(id) {
    if (confirm("Are you sure you want to delete this entry?")) { // Optional: This confirmation can be kept or removed
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_entry.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Handle successful deletion
                console.log(xhr.responseText);
                location.reload(); // Reload the page to see the changes
            } else {
                // Handle error
                alert("Error deleting entry.");
            }
        };
        xhr.send("id=" + id); // Send the ID to delete
    }
}
</script>


    <script>
        function showPopup(owner, position, category, amount, frequency) {
            document.getElementById('popup-title').innerText = `Expense Request`;
            document.getElementById('popup-description').innerText = `Category: ${category}\nAmount: ${amount}\nFrequency: ${frequency}`;
            document.getElementById('popup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('popup')) {
                closePopup();
            }
        }
    </script>

</body>
</html>