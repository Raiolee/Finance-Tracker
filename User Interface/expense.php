<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- All content is stored in container -->
    <div class="container">
        <!-- Include the Navbar -->
        <?php include('navbar.php'); ?>

        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <!-- Top bar section -->
                    <div class="top-bar space-between" id="expense">
                        <h1 class="header">Expenses</h1>
                        <a href="add_expense.php"><button class="New-Saving" id="NewExpenseButton">+ Add an Expense</button></a>
                    </div>
                    <!-- Put main code here -->

                    <table class="table-approval">
                        <thead>
                            <tr>
                                <th>DETAILS</th>
                                <th>MERCHANT</th>
                                <th>BANK</th>
                                <th>AMOUNT</th>
                                <th>REIMBURSABLE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once "../connection/config.php";

                            // Query to fetch the required details from the expenses table
                            $query = "SELECT subject AS details, merchant, 'Bank Name' AS bank, amount, reimbursable FROM expenses";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['details']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['merchant']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['bank']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['reimbursable']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No expenses found.</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>



                </div>
            </div>
        </section>
    </div>
    <!-- APIs (Put APIs below this comment)-->
    <script src="../js/expense.js"></script>
</body>

</html>