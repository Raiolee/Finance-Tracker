<?php include("../APIS/savings_api.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    </style>
</head>
<body>
    <div class="container">
        <?php include("navbar.php") ?>
        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content scrollable">
                    <div class="top-bar space-between">
                        <h1 class="header">Bank</h1>
                        <button class="New-Saving" id="BankButton">+ New Bank</button>
                    </div>
                    <table class="table-approval" id="Bank-Content">
                        <thead>
                            <tr>
                                <th class="th-interact" onclick="sortTable('subject')">
                                    Number
                                </th>
                                <th>
                                    Bank
                                </th>
                                <th class="th-interact" onclick="sortTable('accomplishment_date')">
                                    Balance
                                </th>
                                <th>
                                    Manage
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($result) && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Use the correct variables from the current row
                                    echo "<tr>
                                                <td>" . htmlspecialchars($row['user_bank_id']) . "</td>
                                                <td>" . htmlspecialchars($row['bank']) . "</td>
                                                <td>" . htmlspecialchars($row['balance']) . "</td>
                                                <td>
                                                    <button onclick=\"BankForm('" . htmlspecialchars($row['bank']) . "', '" . htmlspecialchars($row['balance']) . "')\">Allocate</button>
                                                </td>
                                            </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No results found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </section> <!--Section End-->
    </div>

    <script>
        function getCategoryOptions() {
            // Fetch categories dynamically from PHP
            const categories = [
                <?php
                if (isset($result3) && $result3->num_rows > 0) {
                    while ($row3 = $result3->fetch_assoc()) {
                        echo "'" . addslashes($row3['subject']) . "',"; // Corrected to fetch category
                    }
                    echo rtrim(',', ' '); // Remove the trailing comma
                } else {
                    echo "'No categories found'"; // Provide a default value
                }
                ?>
            ];

            return categories.map(subject => `<option value="${subject}">${subject}</option>`).join('');
        }
    </script>

    <?php include("modals/modal-allocate.php"); ?>
    <?php include("modals/modal-savings.php"); ?>
    <script src="../js/modal.js"></script>
</body>

</html>