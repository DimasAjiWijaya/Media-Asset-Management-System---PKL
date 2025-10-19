<?php
// koneksi.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "media_asset_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>
