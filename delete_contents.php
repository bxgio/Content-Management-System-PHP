<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

include 'db.php';

// Check if content id is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: contents.php"); // Redirect back to contents page if id is not provided
    exit();
}

$content_id = $_GET['id'];

// Fetch content details
$sql_content = "SELECT * FROM Content WHERE id=$content_id";
$result_content = $connection->query($sql_content);
$content = $result_content->fetch_assoc();

if (!$content) {
    echo "Content not found!";
    exit();
}

// Check if the user has permission to delete this content
$username = $_SESSION['username'];
$sql_user = "SELECT * FROM Users WHERE username='$username'";
$result_user = $connection->query($sql_user);
$user = $result_user->fetch_assoc();

if ($content['user_id'] != $user['id']) {
    echo "You don't have permission to delete this content!";
    exit();
}

// Delete content if confirmed
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql_delete = "DELETE FROM Content WHERE id=$content_id";
    if ($connection->query($sql_delete) === TRUE) {
        header("Location: contents.php"); // Redirect to contents page after successful deletion
        exit();
    } else {
        echo "Error deleting content: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Content</title>
    <link rel="stylesheet" href="delete_contents.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Delete Content</h2>
            <p>Are you sure you want to delete this content?</p>
            <p><strong>Title:</strong> <?php echo $content['title']; ?></p>
            <p><strong>Description:</strong> <?php echo $content['description']; ?></p>
            <form method="post">
                <button type="submit">Delete</button>
                <a href="contents.php">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>

