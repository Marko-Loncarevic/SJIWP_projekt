<?php
include("db__connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voziloID = $_POST['voziloID'];
    $korisnikID = $_POST['korisnikID'];
    $cijenaKoristenjaDnevno = $_POST['cijenaKoristenjaDnevno'];
    $odKada = $_POST['odKada'];
    $doKada = $_POST['doKada'];

   
    $checkQuery = "SELECT 1 FROM rezervacije 
                  WHERE VoziloID = ? 
                  AND (
                      (DatumPocetka <= ? AND DatumZavrsetka >= ?) OR
                      (DatumPocetka <= ? AND DatumZavrsetka >= ?) OR
                      (DatumPocetka >= ? AND DatumZavrsetka <= ?)
                  )";
    
    $stmt = mysqli_prepare($db, $checkQuery);
    mysqli_stmt_bind_param($stmt, "issssss", $voziloID, $doKada, $odKada, $odKada, $doKada, $odKada, $doKada);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        header("Location: pregled_vozila.php?error=" . urlencode("Vozilo je već rezervirano u odabranom periodu!"));
        exit();
    }

    
    $start = new DateTime($odKada);
    $end = new DateTime($doKada);
    $interval = $start->diff($end);
    $days = $interval->days;
    $ukupnaCijena = $days * $cijenaKoristenjaDnevno;

    $query = "INSERT INTO rezervacije (VoziloID, KorisnikID, DatumPocetka, DatumZavrsetka, UkupnaCijena) 
              VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "iissd", $voziloID, $korisnikID, $odKada, $doKada, $ukupnaCijena);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: pregled_rezervacija.php?success=Uspješna rezervacija");
    } else {
        header("Location: pregled_rezervacija.php?error=" . urlencode("Greška pri rezervaciji: " . mysqli_error($db)));
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit();
}
?>