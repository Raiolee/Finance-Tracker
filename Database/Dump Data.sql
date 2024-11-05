use user_db;

-- Insert sample data into income table
INSERT INTO `income` (`user_id`, `date`, `source`, `total`, `bank`, `category`, `description`) VALUES
(1, '2024-01-10', 'Salary', 2500.00, 'Bank A', 'Monthly', 'Monthly salary payment.'),
(1, '2024-01-15', 'Freelance Work', 800.00, 'Bank B', 'Weekly', 'Payment for freelance project.'),
(1, '2024-01-20', 'Investment', 1500.00, 'Bank C', 'Yearly', 'Annual investment return.');

-- Insert sample data into expenses table
INSERT INTO `expenses` (`user_id`, `subject`, `date`, `next_occurrence`, `bank`, `recurrence_type`, `category`, `reimbursable`, `merchant`, `amount`, `description`) VALUES
(1, 'Groceries', '2024-01-05', NULL, 'BDO', 'Monthly', 'Food', 'no', 'Supermarket', 150.00, 'Monthly grocery shopping.'),
(1, 'Bus Fare', '2024-01-02', '2024-01-09', 'Security Bank', 'weekly', 'Transport', 'no', 'Bus Company', 20.00, 'Weekly bus fare.'),
(1, 'Movie Night', '2024-01-10', NULL, 'BDO', 'monthly', 'Entertainment', 'no', 'Cinema', 30.00, 'Watching a movie.'),
(1, 'Utilities Bill', '2024-01-15', NULL, 'BDO', 'monthly', 'Utilities', 'no', 'Utility Provider', 100.00, 'Monthly utilities payment.');

-- Insert sample data into goals table
INSERT INTO `goals` (`user_id`, `subject`, `start_date`, `date`, `category`, `budget_limit`, `description`) VALUES
(1, 'Trip to Japan', '2024-02-01', '2024-06-01', 'Travels', 1500.00, 'Saving for a trip to Japan.'),
(1, 'New Laptop', '2024-03-01', '2024-07-01', 'Miscellaneous', 800.00, 'Goal to buy a new laptop.'),
(1, 'Emergency Fund', '2024-01-01', '2024-12-31', 'Others', 1000.00, 'Building an emergency savings fund.');

-- Insert sample data into savings table
INSERT INTO `savings` (`user_id`, `subject`, `category`, `savings_amount`, `date`, `bank`) VALUES
(1, 'Travel Fund', 'Savings', 1200.00, '2024-01-15', 'Bank A'),
(1, 'Gadget Fund', 'Savings', 600.00, '2024-01-20', 'Bank B');

-- Insert sample data into bank table
INSERT INTO `bank` (`user_id`, `user_bank_id`, `purpose`, `bank`, `balance`, `date`) VALUES
(1, 101, 'Primary Account', 'Security Bank', 3000.00, '2024-01-01'),
(1, 102, 'Savings Account', 'BDO', 1500.00, '2024-01-01');
