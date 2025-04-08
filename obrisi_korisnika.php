<?php
include("db__connection.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM korisnici WHERE IDKorisnici = $id";
    if (mysqli_query($db, $query)) {
        echo "<script>alert('Korisnik je uspješno obrisan!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Greška pri brisanju!'); window.location.href='index.php';</script>";
    }
}
?>
