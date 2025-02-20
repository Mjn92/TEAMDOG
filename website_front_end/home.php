<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php");
    exit();
}
?>
<html>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1> <!-- Display logged-in username -->
    <a href="logout.php">Logout</a> <!-- Logout link -->
</body>
</html>
