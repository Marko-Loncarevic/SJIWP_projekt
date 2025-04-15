<?php
include("db__connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $ime = mysqli_real_escape_string($db, $_POST['ime']);
    $prezime = mysqli_real_escape_string($db, $_POST['prezime']);
    $kontakt = mysqli_real_escape_string($db, $_POST['kontakt']);

    $query = "UPDATE korisnici SET 
              ImeKorisnika = '$ime',
              PrezimeKorisnika = '$prezime',
              KontaktKorisnika = '$kontakt'
              WHERE IDKorisnici = '$id'";

    if (mysqli_query($db, $query)) {
        echo json_encode(['success' => 'Korisnik uspješno ažuriran']);
    } else {
        echo json_encode(['error' => 'Greška pri ažuriranju korisnika: ' . mysqli_error($db)]);
    }
} else {
    echo json_encode(['error' => 'Nevažeći zahtjev']);
}
?>