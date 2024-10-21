<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "incomeDB";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT * FROM incomes";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="card-title">Income List</h2>
                <div>
                    <a href="Income.html">
                        <button class="btn btn-outline-light me-2">+ New Income</button>
                    </a>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr class="header-row">
                        <th class="table-header">Source of Income</th>
                        <th class="table-header">Amount</th>
                        <th class="table-header">Category</th>
                        <th class="table-header">Investment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data for each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['source'] . "</td>";
                            echo "<td>" . $row['total'] . " " . $row['currency'] . "</td>";
                            echo "<td>" . $row['category'] . "</td>";
                            echo "<td>" . $row['investment'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No income records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
?>
