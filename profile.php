<?php
include 'includes/config.php';
session_start();

// Verify that the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Get user data
$stmt = $conn->prepare("SELECT name, mobile_num, whatsapp FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($name, $mobile_num, $whatsapp);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h2>User Profile</h2>
        <p>Welcome back, <?php echo $_SESSION['username']; ?>!</p>

        <h3>Profile Details</h3>
        <div>
            <p><strong>Name:</strong> <?php echo $name; ?></p>
            <p><strong>Mobile Number:</strong> <?php echo $mobile_num; ?></p>
            <p><strong>WhatsApp:</strong> <?php echo $whatsapp; ?></p>
        </div>

        <h3>Uploaded Files</h3>
        <?php
        $sql = "SELECT id, filename, filetype, filesize, upload_date FROM files WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>File Name</th><th>File Type</th><th>File Size</th><th>Upload Date</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['filename'] . "</td>";
                echo "<td>" . $row['filetype'] . "</td>";
                echo "<td>" . $row['filesize'] . " bytes</td>"; 
                echo "<td>" . $row['upload_date'] . "</td>"; 
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You haven't uploaded any files yet.</p>";
        }
        ?>

        <a href="upload.php" class="button">Upload File</a>

    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
