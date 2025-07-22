<?php
require_once __DIR__ . '/functions.php';

// Messages to user
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Case 1: Verification
    if (isset($_POST['verification_code']) && isset($_POST['email'])) {
        $email = strtolower(trim($_POST['email']));
        $code = trim($_POST['verification_code']);
        if (verifyCode($email, $code)) {
            registerEmail($email);
            $msg = "Email verified and subscribed!";
        } else {
            $msg = "Invalid verification code.";
        }

    // Case 2: Email submission
    } elseif (isset($_POST['email'])) {
        $email = strtolower(trim($_POST['email']));
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $pending = file_exists(VERIF_FILE) ? json_decode(file_get_contents(VERIF_FILE), true) : [];
            $pending[$email] = $code;
            file_put_contents(VERIF_FILE, json_encode($pending));
            sendVerificationEmail($email, $code);
            $msg = "Verification code sent to your email.";
        } else {
            $msg = "Invalid email address.";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>XKCD Email Subscription</title>
</head>
<body>
    <h1>XKCD Email Subscription</h1>
    <?php if ($msg) echo "<p><strong>$msg</strong></p>"; ?>

    <form method="POST" autocomplete="off">
        <h2>Subscribe</h2>
        <input type="email" name="email" required placeholder="Enter your email">
        <button id="submit-email">Submit</button>
    </form>

    <form method="POST" autocomplete="off">
        <h2>Verify your Email</h2>
        <input type="email" name="email" required placeholder="Your email">
        <input type="text" name="verification_code" maxlength="6" required placeholder="Verification code">
        <button id="submit-verification">Verify</button>
    </form>

    <p>Want to unsubscribe? <a href="unsubscribe.php">Click here</a></p>
</body>
</html>
