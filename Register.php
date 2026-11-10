<?php
// register.php
require __DIR__ . '/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {

        // Check if this email is already registered
        $sql = "SELECT id FROM patients WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $existing = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($existing) > 0) {
            $error = "An account with that email already exists.";
        } else {
            // Hash the password before storing it - never store plain text passwords
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO patients (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $first_name, $last_name, $email, $hashed_password);
            mysqli_stmt_execute($stmt);

            // Log the new patient straight in
            $new_id = mysqli_insert_id($conn);
            $_SESSION["patient_id"] = $new_id;
            $_SESSION["patient_name"] = $first_name . " " . $last_name;

            header("Location: dashboard.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediVault - Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-box">
        <h2>MediVault</h2>
        <p>Create your account</p>

        <?php if ($error) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <form method="POST" action="register.php">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Sign Up</button>
        </form>

        <p class="switch-link">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</body>
</html>