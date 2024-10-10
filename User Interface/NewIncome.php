<?php
// Include the database configuration file
require_once 'config.php';

function addIncomeRecord($pdo) {
    try {
        // Check if the form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the form data
            $date = $_POST['date'];
            $investment = $_POST['investment'];
            $source = $_POST['source'];
            $total = $_POST['total'];
            $currency = $_POST['currency'];
            $category = $_POST['category'];
            $description = $_POST['description'];

            // Prepare and execute the SQL statement
            $stmt = $pdo->prepare("INSERT INTO incomes (date, investment, source, total, currency, category, description) 
                                    VALUES (:date, :investment, :source, :total, :currency, :category, :description)");

            $stmt->execute([
                ':date' => $date,
                ':investment' => $investment,
                ':source' => $source,
                ':total' => $total,
                ':currency' => $currency,
                ':category' => $category,
                ':description' => $description
            ]);

            // Redirect or show a success message
            echo "Income record added successfully!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Call the function to add the income record
addIncomeRecord($pdo);
