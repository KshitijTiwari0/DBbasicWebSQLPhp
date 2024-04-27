<?php
include 'includes/config.php';
session_start();

// Verify that the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$error = ''; 

if (isset($_POST['submit'])) {
    $allowed_types = ['pdf', 'doc', 'docx', 'mp3', 'mp4', 'jpg', 'jpeg', 'png']; 

    $file_name = $_FILES['file']['name'];
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); 

    // Validate file type
    if (!in_array($file_type, $allowed_types)) {
        $error = 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types);
    }

    // Additional validation if needed (e.g., file size limits) 

    if (empty($error)) {
        // Move uploaded file
        $target_dir = 'uploads/';
        $target_file = $target_dir . basename($file_name);

        if (move_uploaded_file($file_tmp_name, $target_file)) {
            // File uploaded successfully - Insert into database
            $stmt = $conn->prepare("INSERT INTO files (user_id, filename, filepath, filetype, filesize) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('isssi', $user_id, $file_name, $target_file, $file_type, $file_size);

            if ($stmt->execute()) { 
                header('Location: profile.php?success=1');
                exit;
            } else {
                $error = 'Error uploading file';
            }
        } else {
            $error = 'Error moving the uploaded file';
        } 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>Upload File</h2>

        <?php if (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="file">Choose a file:</label>
                <input type="file" name="file" id="file" required>
            </div>
            <input type="submit" name="submit" value="Upload" class="button">
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
