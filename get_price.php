<?php
include("db__connection.php");

$voziloID = $_GET['id'];
$query = "SELECT CijenaKoristenjaDnevno FROM vozila WHERE IDVozilo = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $voziloID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode(['price' => $row['CijenaKoristenjaDnevno']]);
?>