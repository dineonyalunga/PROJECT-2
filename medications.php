<?php
// medications.php
require 'config.php';

$active_page = "medications";
require 'includes/navbar.php';

$patient_id = $_SESSION["patient_id"];
$refill_message = "";

// Handle "Request Refill" button submissions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["refill_medication_id"])) {
    $med_id = $_POST["refill_medication_id"];

    // For now, a refill request simply tops the pills back up to the full amount.
    // (In a real system this would notify a doctor/pharmacist instead of refilling instantly.)
    $sql = "UPDATE medications SET pills_remaining = pills_total WHERE id = ? AND patient_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $med_id, $patient_id);
    mysqli_stmt_execute($stmt);

    $refill_message = "Refill requested successfully.";
}

// Get current (active) medications
$sql = "SELECT * FROM medications WHERE patient_id = ? AND status = 'active'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$current_medications = mysqli_stmt_get_result($stmt);

// Get medication history (anything not active)
$sql = "SELECT * FROM medications WHERE patient_id = ? AND status != 'active'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$medication_history = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediVault - Medications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main class="dashboard-content">

        <?php if ($refill_message) { ?>
            <p class="success-message"><?php echo $refill_message; ?></p>
        <?php } ?>

        <section class="current-medications">
            <h2>Current Medications</h2>
            <p class="section-subtitle">Your active prescriptions and dosage information</p>

            <?php while ($med = mysqli_fetch_assoc($current_medications)) {
                $percent = $med["pills_total"] > 0
                    ? round(($med["pills_remaining"] / $med["pills_total"]) * 100)
                    : 0;
            ?>
                <div class="medication-card">
                    <h3><?php echo htmlspecialchars($med["name"]); ?></h3>
                    <p class="dosage"><?php echo htmlspecialchars($med["dosage"]); ?></p>

                    <p>Frequency: <span><?php echo htmlspecialchars($med["frequency"]); ?></span></p>
                    <p>Time: <span><?php echo htmlspecialchars($med["time_of_day"]); ?></span></p>

                    <p>Pills Remaining: <?php echo $med["pills_remaining"]; ?>/<?php echo $med["pills_total"]; ?></p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $percent; ?>%;"></div>
                    </div>

                    <form method="POST" action="medications.php">
                        <input type="hidden" name="refill_medication_id" value="<?php echo $med["id"]; ?>">
                        <button type="submit" class="request-refill-button">Request Refill</button>
                    </form>
                </div>
            <?php } ?>
        </section>

        <section class="medication-history">
            <h2>Medication History</h2>

            <?php while ($med = mysqli_fetch_assoc($medication_history)) { ?>
                <div class="history-item">
                    <p class="history-name"><?php echo htmlspecialchars($med["name"] . " " . $med["dosage"]); ?></p>
                    <span class="status-badge"><?php echo htmlspecialchars(ucfirst($med["status"])); ?></span>
                </div>
            <?php } ?>
        </section>

    </main>

</body>
</html>