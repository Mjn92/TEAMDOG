<?php
// Start the session to keep track of logged-in users
session_start();

// Database connection details
$host = "100.93.130.48"; //IP for DB
$dbname = "moveDB";  //DB Name 
$user = "TeamDog123";  // Username for DB
$pass = "TeamDog123";      // Password for DB

// Connect to MySQL database
$conn = new mysqli($host, $user, $pass, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve username and password from the form
    $uname = $_POST['uname'];
    $pword = $_POST['pword'];

    // Prepare an SQL statement to prevent SQL injection
    //Looked this up. Found solution on stack overflow
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $uname);
    $stmt->execute();
    $stmt->store_result();
    
    // If a matching username is found
    if ($stmt->num_rows > 0) {
        // Fetch the hashed password from the database
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        
        // Verify the entered password against the stored hash
        if (password_verify($pword, $hashed_password)) {
            // Store the username in the session to keep the user logged in
            $_SESSION['username'] = $uname;
            echo json_encode(["success" => "Login successful", "redirect" => "home.php"]);
        } else {
            // Password is incorrect
            echo json_encode(["error" => "Invalid credentials"]);
        }
    } else {
        // Username is not found
        echo json_encode(["error" => "User not found"]);
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>
