<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle file upload if a file is selected
    $file_path = '';
    if ($_FILES['file']['name']) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_path = "uploads/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
    }

    $username = $_SESSION['username'];
    $sql_user = "SELECT * FROM Users WHERE username='$username'";
    $result_user = $connection->query($sql_user);
    $user = $result_user->fetch_assoc();
    $user_id = $user['id'];

    // Insert new content entry into the database
    $sql = "INSERT INTO Content (title, description, date, file_path, user_id) VALUES ('$title', '$description', NOW(), '$file_path', '$user_id')";
    if ($connection->query($sql) === TRUE) {
        $message = "Content added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Content</title>
    <link rel="stylesheet" href="add_content.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="container">
        <h2>Add Content</h2>
        <?php if (!empty($message)): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div>
                <label for="file">File:</label>
                <input type="file" id="file" name="file">
            </div>
            <button type="submit">Add Content</button>
        </form>

        <!-- Back button to redirect to dashboard.php -->
        <a class="back-link" href="contents.php">Back to Dashboard</a>
    </div>
</body>
</html>

