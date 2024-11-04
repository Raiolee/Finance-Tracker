<?php
include '../connection/config.php';
include '../APIs/income_api.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income List</title>
    <link rel="stylesheet" href="../Styles/styles.css">
    <link rel="stylesheet" href="../Styles/custom-style.css">
    <link rel="stylesheet" href="../Styles/ViewIncome.css">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <div class="container">
        <?php include '../User Interface/navbar.php'; ?>
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <!-- Top bar section -->
                    <div class="top-bar space-between" id="expense">
                        <h1 class="header">Income</h1>
                        <div class="custom-header">
                            <button class="New-Saving" id="newIncomeBtn">+ Add Income</button>
                            <!-- Filter form -->
                            <form class="filter-form" id="filterForm" action="" method="GET">
                                <select class="var-input medium pointer" id="FilterGoalsCategory"
                                    name="FilterGoalsCategory">
                                    <option value="" disabled selected>Category</option>
                                    <option value="Travels">Option 1</option>
                                </select>
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/filter.svg" alt=""></i>
                                </button>
                            </form>
                            <!-- Search Form -->
                            <form class="search-form" action="" method="GET">
                                <input type="search" name="Incomequery" placeholder="Search here ..." style="text-transform: capitalize;">
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/magnifying-glass.svg" alt="" width="20px"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Put main code here -->

                    <table class="table-approval">
                        <thead>
                            <tr class="header-row">
                                <th class="table-header">Source of Income</th>
                                <th class="table-header">Bank</th>
                                <th class="table-header">Amount</th>
                                <th class="th-interact" onclick="document.querySelector('.SortIncomeDate').submit();">
                                    Reccurence
                                    <form class="SortIncomeDate" action="" method="GET" style="display: inline;">
                                        <input type="hidden" name="sortIncomeDate" value="<?php echo htmlspecialchars($nextSortOrderDate); ?>">
                                    </form>
                                </th>
                                <th class="table-header">Date</th>
                            </tr>
                        <tbody>
                            <?php
                            $userId = $_SESSION["user_id"];

                            // Check if a search query is provided
                            if (isset($_GET['Incomequery'])) {
                                $IncomesearchQuery = $_GET['Incomequery'];
                                try {
                                    $result = searchIncome($conn, $userId, $IncomesearchQuery);
                                } catch (Exception $e) {
                                    $error_message = $e->getMessage();
                                }
                            } elseif (isset($_GET['sortIncomeDate'])) {
                                $sortOrder = $_GET['sortIncomeDate'];
                                $nextSortOrderDate = $sortOrder === 'asc' ? 'desc' : 'asc';
                                try {
                                    $result = sortIncomeByDate($conn, $userId, $sortOrder);
                                } catch (Exception $e) {
                                    $error_message = $e->getMessage();
                                }
                            } else {
                                $nextSortOrderDate = 'asc';
                                $sql = "SELECT * FROM income WHERE user_id = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $userId);
                                $stmt->execute();
                                $result = $stmt->get_result();
                            }

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='row-interact' onclick='incomeRowClick(" . $row['income_id'] . ")'>";
                                    echo "<td>" . htmlspecialchars($row['source']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['bank']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['total']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No income records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>


    <?php include("modals/modal-income.php"); ?>
    <?php include("../APIs/get_income.php") ?>
    <?php include("modals/modal-income-row.php") ?>
    <script src="../js/modal.js"></script>
    <script src="../js/income.js"></script>
</body>

</html>