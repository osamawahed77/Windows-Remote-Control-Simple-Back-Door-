<?php
$host = "YOUR-HOST-NAME";
$dbname = "YOUR-DATABASE-NAME";
$username = "YOUR-DATABASE-USERNAME";
$password = "YOUR-DATABASE-PASSWORD*";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
