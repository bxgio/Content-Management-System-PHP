<?php
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if all fields are filled
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } else {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        } else {
            // Check if the username is unique
            $check_username_sql = "SELECT * FROM Users WHERE username='$username'";
            $check_username_result = $connection->query($check_username_sql);
            if ($check_username_result->num_rows > 0) {
                $message = "Username already taken. Please choose a different one.";
            } else {
                // Check if passwords match
                if ($password !== $confirm_password) {
                    $message = "Passwords do not match.";
                } else {
                    // Hash password for security
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert user into the database
                    $sql = "INSERT INTO Users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
                    if ($connection->query($sql) === TRUE) {
                        header("Location: login.php"); // Redirect to login page after successful registration
                        exit();
                    } else {
                        $message = "Error: " . $sql . "<br>" . $connection->error;
                    }
                }
            }
        }
    }

    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="register.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        <?php if (!empty($message)): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button class="reg-button" type="submit">Register</button>
        </form>
        <a class="register-link" href="login.php">Already Have an account? Sign In</a>
    </div>
</body>
</html>
