<?php
include '../connection/config.php';
include '../APIs/goals_api.php'; // Include the new API file
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link rel="stylesheet" href="../Styles/custom-style.css">
    <title>Goals</title>
</head>

<body>
    <div class="container">
        <?php include('navbar.php'); ?>

        <section class="main-section">
            <div class="main-container">
                <div class="content scrollable">
                    <!-- Top bar section -->
                    <div class="top-bar space-between" id="expense">
                        <h1 class="header" id="headerText">Goals</h1>
                        <div class="custom-header">
                            <button class="New-Saving" id="newGoalBtn">+ Add a Goal</button>
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

                            <!-- Search form -->
                            <form class="search-form" id="searchForm" action="" method="GET">
                                <input type="search" name="query" placeholder="Search here ...">
                                <button type="submit">
                                    <i class="fa"><img src="../Assets/Icons/magnifying-glass.svg" alt="" width="20px"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Goals table -->
                    <table id="goalsTable" class="table-approval">
                        <thead>
                            <tr>
                                <th class="th-interact" onclick="window.location.href='?sortforsubject&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>'">SUBJECT</th>
                                <th>
                                    CATEGORY
                                </th>
                                <th class="th-interact" onclick="window.location.href='?sort=date&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>'">ACCOMPLISHMENT DATE</th>
                                <th>
                                    PROGRESS
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                if (isset($result) && $result->num_rows > 0) {
                                    foreach ($result as $row) {
                                        $percentage = 0;
                                        foreach ($goalsAndSavings as $goal) {
                                            if ($goal['goal_id'] === $row['goal_id']) {
                                                $percentage = $goal['percentage'];
                                                break;
                                            }
                                        }

                                        echo "<tr class='row-interact' onclick='goalRowClick(" . $row['goal_id'] . ")'>
                                            <td>
                                                <div class='sub'>" . htmlspecialchars($row['subject']) . "</div>
                                            </td>

                                            <td>
                                                " . htmlspecialchars($row['category']) . "
                                            </td>

                                            <td>
                                                " . htmlspecialchars($predictions[$row['subject']] ?? 'N/A') . "
                                            </td>
                                            
                                            <td>
                                                <span>" . $percentage . "%</span>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No results found</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    </div>
    <?php include('../APIs/get_goal.php') ?>
    <?php include('modals/modal-goals-row.php'); ?>
    <?php include('modals/modal-goals.php'); ?>
    <script src="../js//goals.js"></script>
    <script src="../js/modal.js"></script>
</body>
</html>