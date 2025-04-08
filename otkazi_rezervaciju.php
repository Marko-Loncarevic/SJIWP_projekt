<?php
include("db__connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $reservationId = $_GET['id'];
    
  
    $checkQuery = "SELECT IDRezervacija FROM rezervacije WHERE IDRezervacija = ?";
    $stmt = mysqli_prepare($db, $checkQuery);
    mysqli_stmt_bind_param($stmt, "i", $reservationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
      
        $deleteQuery = "DELETE FROM rezervacije WHERE IDRezervacija = ?";
        $stmt = mysqli_prepare($db, $deleteQuery);
        mysqli_stmt_bind_param($stmt, "i", $reservationId);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: pregled_rezervacija.php?success=Rezervacija je uspješno obrisana");
        } else {
            header("Location: pregled_rezervacija.php?error=Došlo je do greške prilikom brisanja");
        }
    } else {
        header("Location: pregled_rezervacija.php?error=Rezervacija nije pronađena");
    }
} else {
    header("Location: pregled_rezervacija.php");
}
exit();
?>