<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "project");

// Handle registration
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    
    // Check if email already exists
    $check_sql = "SELECT * FROM users WHERE email='$email'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        echo "Email already exists! <a href='registration.html'>Try again</a>";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, password, phone) VALUES ('$name', '$email', '$password', '$phone')";
        
        if(mysqli_query($conn, $sql)) {
            echo "Registration successful! <a href='../login/login.html'>Login here</a>";
        } else {
            echo "Registration failed! <a href='registration.html'>Try again</a>";
        }
    }
    
    mysqli_close($conn);
?>
