<?php
require_once __DIR__ . '/functions.php';

$msg = '';

// If accessed via unsubscribe link with ?email=...
$prefillEmail = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

// Handle unsubscribe code verification FIRST (before generating new one)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code']) && isset($_POST['unsubscribe_email'])) {
    $email = strtolower(trim($_POST['unsubscribe_email']));
    $code = trim($_POST['verification_code']);
    if (verifyUnsubCode($email, $code)) {
        unsubscribeEmail($email);
        $msg = "You have been unsubscribed.";
    } else {
        $msg = "Invalid verification code.";
    }
}

// Handle unsubscribe email submission (new OTP request)
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsubscribe_email'])) {
    $email = strtolower(trim($_POST['unsubscribe_email']));
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        $pending = file_exists(UNSUB_VERIF_FILE) ? json_decode(file_get_contents(UNSUB_VERIF_FILE), true) : [];
        $pending[$email] = $code;
        file_put_contents(UNSUB_VERIF_FILE, json_encode($pending));
        sendUnsubVerificationEmail($email, $code);
        $msg = "Unsubscribe verification code sent to your email.";
    } else {
        $msg = "Invalid email address.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe from XKCD</title>
</head>
<body>
    <h1>Unsubscribe from XKCD Comics</h1>
    <?php if ($msg) echo "<p><strong>$msg</strong></p>"; ?>

    <form method="POST" autocomplete="off">
        <h2>Enter your email to unsubscribe</h2>
        <input type="email" name="unsubscribe_email" required placeholder="Enter your email" value="<?php echo $prefillEmail; ?>">
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>

    <form method="POST" autocomplete="off">
        <h2>Enter code to confirm unsubscription</h2>
        <input type="email" name="unsubscribe_email" required placeholder="Your email" value="<?php echo $prefillEmail; ?>">
        <input type="text" name="verification_code" maxlength="6" required placeholder="Verification code">
        <button id="submit-verification">Verify</button>
    </form>

    <p><a href="index.php">Back to subscribe</a></p>
</body>
</html>
