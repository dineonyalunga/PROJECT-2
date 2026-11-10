<?php
// consultation.php
require 'config.php';

$active_page = "consultation";
require 'includes/navbar.php';

$patient_id = $_SESSION["patient_id"];

// Handle sending a new message
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["message"])) {
    $message = trim($_POST["message"]);
    $sender_name = $_SESSION["patient_name"];

    $sql = "INSERT INTO consultation_messages (patient_id, sender, sender_name, message) VALUES (?, 'patient', ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $patient_id, $sender_name, $message);
    mysqli_stmt_execute($stmt);

    // Redirect after posting so refreshing the page doesn't resend the message
    header("Location: consultation.php");
    exit();
}

// Get all messages for this patient, oldest first
$sql = "SELECT * FROM consultation_messages WHERE patient_id = ? ORDER BY sent_at ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $patient_id);
mysqli_stmt_execute($stmt);
$messages = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MediVault - Consultation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main class="dashboard-content">

        <section class="chat-with-doctor">
            <h2>Chat with Doctor</h2>
            <p class="section-subtitle">Send messages to your healthcare provider</p>

            <div class="chat-window">
                <?php while ($msg = mysqli_fetch_assoc($messages)) {
                    $bubble_class = $msg["sender"] === "patient" ? "chat-bubble patient" : "chat-bubble doctor";
                ?>
                    <div class="<?php echo $bubble_class; ?>">
                        <p class="chat-sender"><?php echo htmlspecialchars($msg["sender_name"]); ?></p>
                        <p class="chat-text"><?php echo htmlspecialchars($msg["message"]); ?></p>
                        <p class="chat-time"><?php echo date("g:i A", strtotime($msg["sent_at"])); ?></p>
                    </div>
                <?php } ?>
            </div>

            <form method="POST" action="consultation.php" class="chat-input-form">
                <input type="text" name="message" placeholder="Type your message..." required>
                <button type="submit">Send</button>
            </form>
        </section>

    </main>

</body>
</html>