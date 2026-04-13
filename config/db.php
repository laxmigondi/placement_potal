<?php
$host = 'localhost';
$dbname = 'placement_portal';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

date_default_timezone_set('Asia/Kolkata');

function clean_input(?string $value): string {
    return trim((string)$value);
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit();
}

function password_matches(string $plainPassword, string $storedPassword): bool {
    if (password_verify($plainPassword, $storedPassword)) {
        return true;
    }

    return md5($plainPassword) === $storedPassword;
}
?>
