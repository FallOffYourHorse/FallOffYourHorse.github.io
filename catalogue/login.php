<?php
session_start();
require_once 'db_connection.php';    // Include the database connection file (only once)

$error = null;    // Initialize error variable to store potential error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {    // Check if the form was submitted using POST method
    $username = $_POST["username"] ?? '';    // Get username from form, use empty string if not set
    $password = $_POST["password"] ?? '';    // Get password from form, use empty string if not set

    try {    // Start error handling block
        $sql = "SELECT * FROM users WHERE username = :username";    // SQL query to find user, using placeholder
        $stmt = $pdo->prepare($sql);    // Prepare the SQL statement to prevent SQL injection
        $stmt->bindParam(':username', $username);    // Bind the username value to the placeholder
        $stmt->execute();    // Execute the prepared statement
        $user = $stmt->fetch(PDO::FETCH_ASSOC);    // Fetch the user data as an associative array

        if ($user && password_verify($password, $user["password"])) {    // Check if user exists and password matches
            $_SESSION["user"] = $username;    // Store username in session variable
            header("Location: index.php");    // Redirect to index page
            exit();    // Stop script execution after redirect
        } else {
            $error = "Invalid username or password";    // Set error message if login fails
        }
    } catch (PDOException $e) {    // Catch any database errors
        $error = "Login error occurred";    // Set generic error message for users
    }
}
$title = 'Login';
include 'includes/header.php'; 
?>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h3 class="mb-4 text-center">Login</h3>

            <?php if ($error): ?> <!-- Check if there's an error to display -->
                <div class="error"><?= htmlspecialchars($error) ?></div> <!-- Display error safely escaped -->
            <?php endif; ?>

            <form method="POST" action="login.php"> <!-- Create form that submits to itself -->
                <div class="form-group mb-3"> <!-- Group for username field -->
                    <label for="username">Username:</label> <!-- Label for username input -->
                    <input type="text" id="username" name="username" required> <!-- Username input field -->
                </div>

                <div class="form-group mb-3"> <!-- Group for password field -->
                    <label for="password">Password:</label> <!-- Label for password input -->
                    <input type="password" id="password" name="password" required> <!-- Password input field -->
                </div>

                <div class="d-grid"><button type="submit">Login</button></div> <!-- Submit button for the form -->
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?> <!-- Include the footer template -->