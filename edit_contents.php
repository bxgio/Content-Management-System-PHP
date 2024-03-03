<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

include 'db.php';

// Variable to store alert message
$alert_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Check if a file is uploaded
    if ($_FILES['file']['size'] > 0) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_path = "uploads/" . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        
        // Update the content entry in the database with the new file path
        $sql = "UPDATE Content SET title='$title', description='$description', file_path='$file_path' WHERE id=$id";
    } else {
        // If no file is uploaded, update the content entry without changing the file path
        $sql = "UPDATE Content SET title='$title', description='$description' WHERE id=$id";
    }

    if ($connection->query($sql) === TRUE) {
        // Set alert message
        $alert_message = "Content updated successfully!";
        header("Location: contents.php"); // Redirect to contents page after successful update
        exit();
    } else {
        echo "Error updating record: " . $connection->error;
    }
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the content entry to be edited
    $sql = "SELECT * FROM Content WHERE id=$id";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();
} else {
    header("Location: contents.php"); // Redirect to contents page if no content id is provided
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content</title>
    <link rel="stylesheet" href="edit_contents.css"> <!-- Add your CSS link here -->
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Edit Content</h2>
            <?php if (!empty($alert_message)) : ?>
                <div class="alert"><?php echo $alert_message; ?></div>
            <?php endif; ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo $row['title']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required><?php echo $row['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="file">File:</label>
                    <input type="file" id="file" name="file">
                </div>
                <button type="submit">Update Content</button>
                <a href="contents.php">Cancel</a> <!-- Link to cancel editing and go back to contents page -->
            </form>
        </div>
    </div>
</body>
</html>

