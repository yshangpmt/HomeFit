<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userinfo";

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve workout name, link, and image from POST request
    $workoutName = isset($_POST['workout']) ? trim($_POST['workout']) : '';
    $workoutLink = isset($_POST['link']) ? trim($_POST['link']) : '';
    $workoutImage = isset($_POST['image']) ? trim($_POST['image']) : '';

    // Validate workout name, link, and image
    if (empty($workoutName) || empty($workoutLink) || empty($workoutImage)) {
        echo 'error';
        exit;
    }

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        error_log('Connection failed: ' . $conn->connect_error); // Log connection error
        echo 'error';
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare('INSERT INTO user_exercise (user_id, workout_name, workout_page_link, workout_image, date_added) VALUES (?, ?, ?, ?, NOW())');
    if ($stmt === false) {
        error_log('Prepare failed: ' . $conn->error); // Log prepare error
        echo 'error';
        exit;
    }

    $stmt->bind_param('isss', $_SESSION['user_id'], $workoutName, $workoutLink, $workoutImage);

    // Execute the statement
    if ($stmt->execute()) {
        echo 'success';
    } else {
        error_log('Execute failed: ' . $stmt->error); // Log execute error
        echo 'error';
    }

    // Debugging: Log the values
    error_log("User ID: " . $_SESSION['user_id']);
    error_log("Workout Name: " . $workoutName);
    error_log("Workout Link: " . $workoutLink);
    error_log("Workout Image: " . $workoutImage);

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
