<?php
require __DIR__ . '/includes/auth.php';
require_login();
$patient = fetch_current_patient($pdo);
$pageTitle = 'Overview';
$activeTab = 'overview';
require __DIR__ . '/includes/header.php';
?>
<div class="card"><h2>Overview</h2><p>Placeholder page.</p></div>
<?php require __DIR__ . '/includes/footer.php'; ?>