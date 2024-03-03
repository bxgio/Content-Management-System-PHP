<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Pagination settings
$results_per_page = 1; // Display only one card per page
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($current_page - 1) * $results_per_page;

// Fetch contents associated with the user from the Content table
$username = $_SESSION['username'];
$sql_user = "SELECT * FROM Users WHERE username='$username'";
$result_user = $connection->query($sql_user);
$user = $result_user->fetch_assoc();

$user_id = $user['id'];

// Fetch total number of content entries associated with the user
$sql_count = "SELECT COUNT(*) AS total FROM Content WHERE user_id=$user_id";
$result_count = $connection->query($sql_count);
$row_count = $result_count->fetch_assoc();
$total_pages = ceil($row_count['total'] / $results_per_page);

// Fetch content entry associated with the user for the current page
$sql_content = "SELECT * FROM Content WHERE user_id=$user_id LIMIT $start_from, $results_per_page";
$result_content = $connection->query($sql_content);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management System</title>
    <!-- Add your CSS link here -->
    <link rel="stylesheet" href="contents.css">
</head>
<body>
    <header>
        <h1>Content Management System</h1>
    </header>

    <div class="container">
        <aside>
            <button onclick="window.location.href = 'logout.php';">Logout</button>
            <button onclick="window.location.href = 'add_content.php';">Add New Content</button>
        </aside>

        <main>
            <h2>Welcome, <span class="username"><?php echo isset($user['username']) ? $user['username'] : 'User'; ?></span>!</h2>
            <h3>Your Content:</h3>
            <?php if ($row = $result_content->fetch_assoc()) : ?>
                <div class="content-card">
                    <h4><?php echo $row['title']; ?></h4>
                    <?php if ($row['file_path']) : ?>
                        <img src="<?php echo $row['file_path']; ?>" alt="Image" style="max-width: 100%;">
                    <?php endif; ?>
                    <p><?php echo $row['description']; ?></p>
                    <p>Date: <?php echo $row['date']; ?></p>
                    <a href="edit_contents.php?id=<?php echo $row['id']; ?>" class="edit-button">Edit</a>
                    <a href="delete_contents.php?id=<?php echo $row['id']; ?>" class="delete-button">Delete</a>
                </div>
            <?php else: ?>
                <p>No content available.</p>
            <?php endif; ?>

            <!-- Pagination -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" <?php if ($i == $current_page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </main>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Company</p>
    </footer>
</body>
</html>
