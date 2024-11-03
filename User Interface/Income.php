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
                                <th class="table-header">Amount</th>
                                <th class="table-header">Category</th>
                                <th class="th-interact" onclick="document.querySelector('.SortIncomeDate').submit();">
                                    Date
                                    <form class="SortIncomeDate" action="" method="GET" style="display: inline;">
                                        <input type="hidden" name="sortIncomeDate" value="<?php echo htmlspecialchars($nextSortOrderDate); ?>">
                                    </form>
                                </th>
                                <th class="table-header">Bank</th>
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
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['source']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['total']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['bank']) . "</td>";
                                    echo "<td><button class='btn btn-outline-light' data-id='" . htmlspecialchars($row['income_id']) . "' data-source='" . htmlspecialchars($row['source']) . "' data-total='" . htmlspecialchars($row['total']) . "' data-category='" . htmlspecialchars($row['category']) . "' data-bank='" . htmlspecialchars($row['bank']) . "'><i class='fas fa-ellipsis-v'></i></button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No income records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div id="editDeleteModal" class="modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Show Income Record</h3>
                                <span class="close">&times;</span>
                            </div>
                            <div class="modal-body">
                                <form id="editDeleteForm" method="POST" action="Income.php">
                                    <input type="hidden" id="incomeId" name="incomeId">
                                    <div class="form-group">
                                        <label for="incomeSource">Source of Income</label>
                                        <input type="text" class="form-control" id="incomeSource" name="incomeSource" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="incomeTotal">Amount</label>
                                        <input type="text" class="form-control" id="incomeTotal" name="incomeTotal" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="incomeCategory">Category</label>
                                        <select class="form-control" id="incomeCategory" name="incomeCategory" readonly>
                                            <option value="Monthly">Monthly</option>
                                            <option value="Weekly">Weekly</option>
                                            <option value="Yearly">Yearly</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="incomeBank">Bank</label>
                                        <input type="text" class="form-control" id="incomeBank" name="incomeBank" readonly>
                                    </div>
                                    <button type="submit" class="btn btn-danger" id="deleteBtn">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("modals/modal-income.php"); ?>
    <script src="../js/modal.js"></script>
    <script src="../js/income.js"></script>
</body>

</html>