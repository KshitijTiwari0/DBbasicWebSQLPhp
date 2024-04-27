<?php
include 'includes/config.php';

// Handle form submission
if (isset($_POST['username'], $_POST['password'], $_POST['name'], $_POST['mobile_num'], $_POST['whatsapp'])) {

    // Sanitize basic inputs (more robust validation needed in real-world use)
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $mobile_num = filter_var($_POST['mobile_num'], FILTER_SANITIZE_STRING);
    $whatsapp = filter_var($_POST['whatsapp'], FILTER_SANITIZE_STRING);

    // Password Hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); 

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param('s', $username); 
    $stmt->execute();
    $stmt->store_result(); 

    if ($stmt->num_rows > 0) {
        $error = 'Username already exists';
    } else {
        // Attempt to insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, password, name, mobile_num, whatsapp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $username, $hashed_password, $name, $mobile_num, $whatsapp);

        if ($stmt->execute()) {
            // Success!
            header('Location: login.php?success=1'); 
            exit;
        } else {
            $error = 'Error creating the account';
        }
    }
    $stmt->close(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>Registration</h2>

        <?php if (isset($error)) { ?> 
            <p class="error"><?php echo $error; ?></p> 
        <?php } ?>

        <form action="register.php" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div>
                <label for="mobile_num">Mobile Number:</label>
                <input type="text" name="mobile_num" id="mobile_num" required>
            </div>
            <div>
                <label for="whatsapp">Whatsapp:</label>
                <input type="text" name="whatsapp" id="whatsapp" required>
            </div>
            <input type="submit" value="Register" class="button">
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
