<?php
// profile.php
require __DIR__ . '/config.php';

$active_page = "profile";
require __DIR__ . '/includes/navbar.php';

$patient_id = $_SESSION["patient_id"];
$success_message = "";
$error_message = "";

// --- Handle "Update Profile" form (personal info) ---
if (isset($_POST["update_profile"])) {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $phone = trim($_POST["phone"]);
    $date_of_birth = $_POST["date_of_birth"];
    $gender = $_POST["gender"];
    $email = trim($_POST["email"]);

    $sql = "UPDATE patients SET first_name=?, last_name=?, phone=?, date_of_birth=?, gender=?, email=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $first_name, $last_name, $phone, $date_of_birth, $gender, $email, $patient_id);
    mysqli_stmt_execute($stmt);

    $_SESSION["patient_name"] = $first_name . " " . $last_name;
    $success_message = "Profile updated successfully.";
}

// --- Handle "Update Medical Info" form ---
if (isset($_POST["update_medical"])) {
    $blood_type = $_POST["blood_type"];
    $allergies = trim($_POST["allergies"]);
    $chronic_conditions = trim($_POST["chronic_conditions"]);
    $emergency_contact_name = trim($_POST["emergency_contact_name"]);
    $emergency_contact_phone = trim($_POST["emergency_contact_phone"]);

    $sql = "UPDATE patients SET blood_type=?, allergies=?, chronic_conditions=?, emergency_contact_name=?, emergency_contact_phone=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $blood_type, $allergies, $chronic_conditions, $emergency_contact_name, $emergency_contact_phone, $patient_id);
    mysqli_stmt_execute($stmt);

    $success_message = "Medical information updated successfully.";
}

// --- Handle "Change Password" form ---
if (isset($_POST["change_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_new_password"];

    $sql = "SELECT password FROM patients WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!password_verify($current_password, $row["password"])) {
        $error_message = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New password and confirmation do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_message = "New password must be at least 6 characters.";
    } else {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE patients SET password=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_hash, $patient_id);
        mysqli_stmt_execute($stmt);
        $success_message = "Password changed successfully.";
    }
}

// --- Get current patient info to fill the form fields ---
$sql = "SELECT * FROM patients WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$patient = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediVault - Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main class="dashboard-content">

        <?php if ($success_message) { ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php } ?>
        <?php if ($error_message) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>

        <section class="personal-information">
            <h2>Personal Information</h2>
            <form method="POST" action="profile.php">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($patient["first_name"]); ?>" required>

                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($patient["last_name"]); ?>" required>

                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" value="<?php echo htmlspecialchars($patient["date_of_birth"]); ?>">

                <label>Gender</label>
                <select name="gender">
                    <option value="Male" <?php if ($patient["gender"] == "Male") echo "selected"; ?>>Male</option>
                    <option value="Female" <?php if ($patient["gender"] == "Female") echo "selected"; ?>>Female</option>
                    <option value="Other" <?php if ($patient["gender"] == "Other") echo "selected"; ?>>Other</option>
                </select>

                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($patient["phone"]); ?>">

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($patient["email"]); ?>" required>

                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </section>

        <section class="medical-information">
            <h2>Medical Information</h2>
            <form method="POST" action="profile.php">
                <label>Blood Type</label>
                <select name="blood_type">
                    <?php foreach (["A+","A-","B+","B-","AB+","AB-","O+","O-"] as $type) { ?>
                        <option value="<?php echo $type; ?>" <?php if ($patient["blood_type"] == $type) echo "selected"; ?>><?php echo $type; ?></option>
                    <?php } ?>
                </select>

                <label>Allergies</label>
                <textarea name="allergies"><?php echo htmlspecialchars($patient["allergies"]); ?></textarea>

                <label>Chronic Conditions</label>
                <textarea name="chronic_conditions"><?php echo htmlspecialchars($patient["chronic_conditions"]); ?></textarea>

                <label>Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name" value="<?php echo htmlspecialchars($patient["emergency_contact_name"]); ?>">

                <label>Emergency Contact Phone</label>
                <input type="text" name="emergency_contact_phone" value="<?php echo htmlspecialchars($patient["emergency_contact_phone"]); ?>">

                <button type="submit" name="update_medical">Update Medical Info</button>
            </form>
        </section>

        <section class="account-settings">
            <h2>Account Settings</h2>
            <form method="POST" action="profile.php">
                <label>Current Password</label>
                <input type="password" name="current_password" required>

                <label>New Password</label>
                <input type="password" name="new_password" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_new_password" required>

                <button type="submit" name="change_password">Change Password</button>
            </form>
        </section>

    </main>

</body>
</html>