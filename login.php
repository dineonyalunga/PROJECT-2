<?php
// login.php
require __DIR__ . '/config.php';

$error = "";

// This runs only when the form below is submitted (i.e. the user clicked "Login")
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // Look up the patient by email
    $sql = "SELECT * FROM patients WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $patient = mysqli_fetch_assoc($result);

    if ($patient && password_verify($password, $patient["password"])) {
        // Correct email + password -> remember this patient in the session
        $_SESSION["patient_id"] = $patient["id"];
        $_SESSION["patient_name"] = $patient["first_name"] . " " . $patient["last_name"];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Incorrect email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediVault - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-box">
        <h2>MediVault</h2>
        <p>e-clinic system</p>

        <?php if ($error) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>