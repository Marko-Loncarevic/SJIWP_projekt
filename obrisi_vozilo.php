<?php
include("db__connection.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM vozila WHERE IDVozilo = $id";
    if (mysqli_query($db, $query)) {
        echo "<script>alert('Vozilo je uspješno obrisano!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Greška pri brisanju!'); window.location.href='index.php';</script>";
    }
}
?>
