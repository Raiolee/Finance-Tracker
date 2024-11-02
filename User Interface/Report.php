<?php
// Enable error reporting at the beginning of your PHP script
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ../Login.php");
}
$username = $_SESSION["name"];
$current_page = "report.php";

// Include database connection
include('../connection/config.php');

// Fetch only the user_dp (profile picture) from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT email, user_dp FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Prepare the profile picture for display
if ($user['user_dp']) {
    // Convert BLOB to base64-encoded image
    $profile_pic = 'data:image/jpeg;base64,' . base64_encode($user['user_dp']);
} else {
    // If no profile picture is found, use a placeholder image
    $profile_pic = 'https://picsum.photos/100/100';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../Styles/styles.scss">
    <link href='https://fonts.googleapis.com/css?family=Cabin Condensed' rel='stylesheet'>
    <title>Document</title>
</head>

<body>
    <!-- All content is stored in container -->
    <div class="container">
        <?php include('navbar.php'); ?>
        <!-- Main Section -->
        <section class="main-section">
            <div class="main-container">
                <div class="content">
                    <div class="top-bar">
                        <h1 class="header">Submit a Report</h1>
                    </div>
                    <form id="report-form" class="pfp-form" action="../APIs/send_email.php" method="POST" enctype="multipart/form-data">
                        <div class="big-divider">
                            <div class="row-form">
                                <label for="name" class="form-labels">Name</label>
                                <input type="text" class="var-input-report" id="name" name="name">

                                <label for="email" class="form-labels">Email</label>
                                <input class="var-input-report" type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                                <label for="subject" class="form-labels">Subject</label>
                                <input class="var-input-report" type="text" id="subject" name="subject" value="" required>

                                <label for="message" class="form-labels">Message</label>
                                <textarea class="text-input-report" id="message" name="message" required></textarea>

                                <label for="attachment" class="file-label-report">Attach Image</label>
                                <input class="file-input" type="file" name="attachment" accept="image/*" id="file-input">

                                <div class="btn-options" id="report-btns">
                                    <a href="Settings.php" class="link-btn"><button type="button" class="cancel">Cancel</button></a>
                                    <button type="submit" name="save" class="save">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <script src="report.js"> </script>
</body>

</html>