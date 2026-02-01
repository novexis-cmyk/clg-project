<html>
    <head>
        <title>login from</title>
    </head>
<?php
session_start();

// Get user input (form has required, so fields will not be empty)
$email = $_POST['email'];
$password = $_POST['password'];

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "project");

// Check if user exists
$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = mysqli_query($conn, $sql);

// If user found
if(mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    
    // Go to home page
    header("Location: ../index.php");
    exit();
    
} else {
    // Show error message
    echo "Wrong email or password! <a href='login.html'>Try again</a>";
}

mysqli_close($conn);
?>