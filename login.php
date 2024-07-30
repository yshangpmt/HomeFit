<?php
session_start(); // Start session to store user data

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "userinfo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die('{"success": false, "message": "Database connection failed"}');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) { // Direct comparison since passwords are not hashed
            // Store user data in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['year_of_birth'] = $row['year_of_birth'];
            $_SESSION['password'] = $row['password'];
            $_SESSION['profile_image'] = $row['profile_image'];

            // Fetch survey data
            $survey_sql = "SELECT * FROM survey WHERE user_id=?";
            $survey_stmt = $conn->prepare($survey_sql);
            $survey_stmt->bind_param("i", $row['id']);
            $survey_stmt->execute();
            $survey_result = $survey_stmt->get_result();

            if ($survey_result->num_rows > 0) {
                $survey_row = $survey_result->fetch_assoc();
                $_SESSION['survey_id'] = $survey_row['survey_id'];
                $_SESSION['age'] = $survey_row['age'];
                $_SESSION['weight'] = $survey_row['weight'];
                $_SESSION['height'] = $survey_row['height'];
                $_SESSION['fitness_goal'] = $survey_row['fitness_goal'];
                $_SESSION['health'] = $survey_row['health'];
                $_SESSION['gym'] = $survey_row['gym'];
            }

            header("Location: Home.html");
            exit();
        } else {
            echo "<script>
                    alert('Invalid username or password!');
                    window.location.href = 'index.html';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Username not found!');
                window.location.href = 'index.html';
              </script>";
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
