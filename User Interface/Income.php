<?php
include '../connection/config.php';
session_start();
$user_id = $_SESSION['user_id'];
$username = $_SESSION["name"];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch only the user_dp (profile picture) from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && $user['user_dp']) {
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    $profile_pic = '../Assets/blank-profile.webp';
}

function searchIncome($conn, $userId, $IncomesearchQuery)
{
    $sql = "SELECT * FROM income WHERE user_id = ? AND (source LIKE ? OR date LIKE ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $likeQuery = "%{$IncomesearchQuery}%";
        $stmt->bind_param("iss", $userId, $likeQuery, $likeQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

$userId = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incomeId'])) {
    $incomeId = $_POST['incomeId'];

    $sql = "DELETE FROM income WHERE income_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("i", $incomeId);

        // Start the transaction
        $conn->begin_transaction();

        // Execute the first statement
        if ($stmt->execute()) {
            $total = $_POST['incomeTotal']; // Assuming the total amount is passed in the POST request
            $uid = $_SESSION['user_id'];
            $bank = $_POST['incomeBank']; // Assuming the bank name is passed in the POST request

            echo "Total: $total, User ID: $uid, Bank: $bank";

            // Prepare the update statement
            $updateStmt = $conn->prepare("UPDATE bank SET balance = balance - ? WHERE user_id = ? AND bank = ?");
            if ($updateStmt === false) {
                $conn->rollback();
                throw new Exception("Prepare failed: {$conn->error}");
            }

            // Bind parameters to the update statement
            $updateStmt->bind_param("dis", $total, $uid, $bank);

            // Execute the update statement
            if (!$updateStmt->execute()) {
                $conn->rollback();
                throw new Exception("Execute failed: {$updateStmt->error}");
            }

            // Commit transaction
            $conn->commit();
        } else {
            $conn->rollback();
            throw new Exception("Execute failed for the first statement: {$stmt->error}");
        }
    } else {
        echo "Error preparing statement: {$conn->error}";
    }
}

function sortIncomeByDate($conn, $userId, $sortOrder)
{
    $sql = "SELECT * FROM income WHERE user_id = ? ORDER BY date " . ($sortOrder === 'asc' ? 'ASC' : 'DESC');
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        throw new Exception("Error preparing statement: {$conn->error}");
    }
}

if (isset($_GET['sortIncomeDate'])) {
    $sortOrder = $_GET['sortIncomeDate'];
    $nextSortOrderDate = $sortOrder === 'asc' ? 'desc' : 'asc';
    try {
        $result = sortIncomeByDate($conn, $user_id, $sortOrder);
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
} else {
    $nextSortOrderDate = 'asc';
}
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
                                <select class="var-input medium pointer" id="FilterGoalsCategory" name="FilterGoalsCategory">
                                    <option value="" disabled selected>Category</option>
                                    <option value="Travels">Travels</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                    <option value="Others">Others</option>
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

    <script>
        var editDeleteModal = document.getElementById("editDeleteModal");
        var editDeleteClose = editDeleteModal.getElementsByClassName("close")[0];

        document.querySelectorAll('.btn-outline-light[data-id]').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var source = this.getAttribute('data-source');
                var total = this.getAttribute('data-total');
                var category = this.getAttribute('data-category');
                var bank = this.getAttribute('data-bank');

                document.getElementById('incomeId').value = id;
                document.getElementById('incomeSource').value = source;
                document.getElementById('incomeTotal').value = total;
                document.getElementById('incomeCategory').value = category;
                document.getElementById('incomeBank').value = bank;

                editDeleteModal.style.display = "block";
            });
        });

        editDeleteClose.onclick = function() {
            editDeleteModal.style.display = "none";
        };

        window.onclick = function(event) {
            if (event.target == editDeleteModal) {
                editDeleteModal.style.display = "none";
            }
        };
    </script>

    <?php include("modals/modal-income.php"); ?>
    <script src="../js/modal.js"></script>
</body>

</html>