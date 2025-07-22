<?php
// Helper: File paths
define('EMAILS_FILE', __DIR__ . '/registered_emails.txt');
define('VERIF_FILE', __DIR__ . '/pending_verifications.json');
define('UNSUB_VERIF_FILE', __DIR__ . '/pending_unsubs.json');

// 1. Generate a 6-digit numeric code
function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

// 2. Register email
function registerEmail($email) {
    $email = strtolower(trim($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    $emails = file_exists(EMAILS_FILE) ? file(EMAILS_FILE, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) : [];
    if (!in_array($email, $emails)) {
        file_put_contents(EMAILS_FILE, $email.PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    return true;
}

// 3. Unsubscribe email
function unsubscribeEmail($email) {
    $email = strtolower(trim($email));
    if (!file_exists(EMAILS_FILE)) return false;
    $emails = file(EMAILS_FILE, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => strtolower($e) !== $email);
    file_put_contents(EMAILS_FILE, implode(PHP_EOL, $emails).PHP_EOL, LOCK_EX);
    return true;
}

// 4. Send verification email
function sendVerificationEmail($email, $code) {
    $subject = 'Your Verification Code';
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html; charset=UTF-8";
    $body = "<p>Your verification code is: <strong>{$code}</strong></p>";
    mail($email, $subject, $body, $headers);
}

// 5. Verify code (subscribe)
function verifyCode($email, $code) {
    $email = strtolower(trim($email));
    $pending = file_exists(VERIF_FILE) ? json_decode(file_get_contents(VERIF_FILE), true) : [];
    if (isset($pending[$email]) && $pending[$email] === $code) {
        unset($pending[$email]);
        file_put_contents(VERIF_FILE, json_encode($pending));
        return true;
    }
    return false;
}

// 6. Send unsubscribe verification email
function sendUnsubVerificationEmail($email, $code) {
    $subject = 'Confirm Un-subscription';
    $headers = "From: no-reply@example.com\r\nContent-Type: text/html; charset=UTF-8";
    $body = "<p>To confirm un-subscription, use this code: <strong>{$code}</strong></p>";
    mail($email, $subject, $body, $headers);
}

// 7. Verify code (unsubscribe)
function verifyUnsubCode($email, $code) {
    $email = strtolower(trim($email));
    $pending = file_exists(UNSUB_VERIF_FILE) ? json_decode(file_get_contents(UNSUB_VERIF_FILE), true) : [];
    if (isset($pending[$email]) && $pending[$email] === $code) {
        unset($pending[$email]);
        file_put_contents(UNSUB_VERIF_FILE, json_encode($pending));
        return true;
    }
    return false;
}

// 8. Fetch and format XKCD data as HTML
function fetchAndFormatXKCDData($forEmail = '') {
    // Get latest comic number
    $latest = json_decode(@file_get_contents('https://xkcd.com/info.0.json'), true);
    if (!$latest || !isset($latest['num'])) return false;
    $max = $latest['num'];
    $rand = random_int(1, $max);
    $comic = json_decode(@file_get_contents("https://xkcd.com/{$rand}/info.0.json"), true);
    if (!$comic) return false;
    $img = htmlspecialchars($comic['img']);
    $alt = htmlspecialchars($comic['alt']);
    $unsubscribe = $forEmail ? "unsubscribe.php?email=" . urlencode($forEmail) : "#";
    $html = <<<HTML
<h2>XKCD Comic</h2>
<img src="{$img}" alt="XKCD Comic">
<p><a href="{$unsubscribe}" id="unsubscribe-button">Unsubscribe</a></p>
HTML;
    return $html;
}

// 9. Send XKCD updates to all subscribers
function sendXKCDUpdatesToSubscribers() {
    if (!file_exists(EMAILS_FILE)) return;
    $emails = file(EMAILS_FILE, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    foreach ($emails as $email) {
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
        $body = fetchAndFormatXKCDData($email);
        if (!$body) continue;
        $subject = 'Your XKCD Comic';
        $headers = "From: no-reply@example.com\r\nContent-Type: text/html; charset=UTF-8";
        mail($email, $subject, $body, $headers);
    }
}
?>
