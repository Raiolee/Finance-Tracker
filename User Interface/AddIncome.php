<?php
// Include the database configuration file
require_once 'config.php';

// Create a database connection using the function from config.php
$pdo = getDatabaseConnection();

function addIncome($pdo) {
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $date = $_POST['date'];
        $investment = $_POST['investment'];
        $source = $_POST['source'];
        $total = $_POST['total'];
        $currency = $_POST['currency'];
        $category = $_POST['category'];
        $description = $_POST['description'];

        // Prepare the SQL statement
        $sql = "INSERT INTO income (date, investment, source, total, currency, category, description) 
                VALUES (:date, :investment, :source, :total, :currency, :category, :description)";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':date' => $date,
                ':investment' => $investment,
                ':source' => $source,
                ':total' => $total,
                ':currency' => $currency,
                ':category' => $category,
                ':description' => $description
            ]);
            echo "Income record added successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Call the function
addIncome($pdo);
?>