<?php
include("db__connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $ime = trim($_POST['ime']);
    $prezime = trim($_POST['prezime']);
    $kontakt = isset($_POST['kontakt']) ? trim($_POST['kontakt']) : null;

   
    if (empty($ime) || empty($prezime)) {
        header("Location: korisnici.php?error=Sva obavezna polja moraju biti popunjena");
        exit();
    }

    
    $query = "INSERT INTO korisnici (ImeKorisnika, PrezimeKorisnika, KontaktKorisnika) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($db, $query);

    if (!$stmt) {
        header("Location: korisnici.php?error=Greška u pripremi upita");
        exit();
    }

  
    mysqli_stmt_bind_param($stmt, "sss", $ime, $prezime, $kontakt);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: korisnici.php?success=Korisnik uspješno dodan");
    } else {
        header("Location: korisnici.php?error=Greška pri dodavanju korisnika: " . mysqli_error($db));
    }

    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit();
}


header("Location: korisnici.php");
?>