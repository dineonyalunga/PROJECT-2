<?php
// dashboard.php (Overview page)
require 'config.php';

$active_page = "overview"; // tells navbar.php which tab to highlight
require 'includes/navbar.php';

$patient_id = $_SESSION["patient_id"];

// Count active medications
$sql = "SELECT COUNT(*) AS total FROM medications WHERE patient_id = ? AND status = 'active'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$active_meds = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))["total"];

// Count consultation messages
$sql = "SELECT COUNT(*) AS total FROM consultation_messages WHERE patient_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$message_count = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))["total"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediVault - Overview</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main class="dashboard-content">

        <section class="overview-cards">
            <div class="card">
                <h3>Active Medications</h3>
                <p class="card-number"><?php echo $active_meds; ?></p>
            </div>
            <div class="card">
                <h3>Messages from Doctor</h3>
                <p class="card-number"><?php echo $message_count; ?></p>
            </div>
        </section>

        <section class="quick-actions">
            <h2>Quick Actions</h2>
            <a href="medications.php" class="action-button">Request Prescription Refill</a>
            <a href="consultation.php" class="action-button">Start a Consultation</a>
            <a href="profile.php" class="action-button">Update Profile</a>
        </section>

    </main>

</body>
</html>