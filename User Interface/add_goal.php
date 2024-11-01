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
                <div class="content scrollable-content">
                    <div class="top-bar">
                        <h1 class="header">Add a Goal</h1>
                    </div>
                    <form id="addExpense" class="pfp-form" action="../APIs/goals_api.php" method="POST" enctype="multipart/form-data">
                        <div class="big-divider full center">
                            <div class="row-form no-margin large">
                                <div class="column-form x-large">
                                    <!-- Subject -->
                                    <label for="name" class="form-labels row">Subject</label>
                                    <input type="text" class="var-input medium" id="name" name="name">
                                    <!-- Category -->
                                    <label for="category" class="form-labels row medium">Category</label>
                                    <select class="date-input medium" id="category" name="category">
                                        <option value="food">Food</option>
                                        <option value="transport">Transport</option>
                                        <option value="entertainment">Entertainment</option>
                                        <option value="utilities">Utilities</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="column-form x-large">
                                    <!-- Date -->
                                    <label for="name" class="form-labels row">Start Date</label>
                                    <input type="date" class="date-input" id="name" name="name">
                                    <label for="recurrence_type" class="form-labels row">Frequency</label>
                                    <select class="var-input medium pointer" name="recurrence_type" id="recurrence_type">
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>

                                <label for="name" class="form-labels">Amount</label>
                                <input type="number" class="var-input x-large" id="amount" name="amount" step="100.00">
                                <label for="name" class="form-labels">Description</label>
                                <textarea class="text-input x-large" name="description" id="description"></textarea>

                                <!-- file input/ photo input -->
                                <label for="attachment" class="file-label" id="file-label">Attach Receipt</label>
                                <input class="file-input" type="file" name="attachment" accept="image/*" id="file-input">

                                <div class="btn-options center" id="report-btns">
                                    <a href="goals.php" class="link-btn"><button type="button" class="cancel">Cancel</button></a>
                                    <button type="submit" name="save" class="save">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <!-- APIs (Put APIs below this comment)-->
</body>

</html>