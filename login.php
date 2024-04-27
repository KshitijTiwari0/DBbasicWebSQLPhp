<?php
include 'includes/config.php';
session_start(); // Start session management

// Redirect if the user is already logged in
if (isset($_SESSION['loggedin'])) { 
    header('Location: profile.php');
    exit;
}

// Handle form submission
if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; 

    // Prepare a SELECT statement (prevents SQL injection)
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");

    // Bind parameters ('s' specifies the variable type - string)
    $stmt->bind_param('s', $username);
    $stmt->execute();

    // Store result to get later
    $stmt->store_result(); 

    // Check if the user exists in the database
    if ($stmt->num_rows > 0) {
        // Bind result variables to fetch them
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Account exists, now verify the password
        if (password_verify($password, $hashed_password)) {
            // Success! 
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            header('Location: profile.php'); 
        } else {
            $error = 'Incorrect username or password';
        }
    } else {
        $error = 'Incorrect username or password';
    }

    $stmt->close(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>Login</h2>

        <?php if (isset($error)) { ?> 
            <p class="error"><?php echo $error; ?></p> 
        <?php } ?>

        <form action="login.php" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <input type="submit" value="Login" class="button">
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
