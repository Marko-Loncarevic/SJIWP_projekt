<?php
include("db__connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $naziv = mysqli_real_escape_string($db, $_POST['naziv']);
    $model = mysqli_real_escape_string($db, $_POST['model']);
    $cijena = mysqli_real_escape_string($db, $_POST['cijena']);
    $godiste = mysqli_real_escape_string($db, $_POST['godiste']);
    $kilometraza = mysqli_real_escape_string($db, $_POST['kilometraza']);
    $registracija = mysqli_real_escape_string($db, $_POST['registracija']);
    $raspolozivost = mysqli_real_escape_string($db, $_POST['raspolozivost']);

    mysqli_begin_transaction($db);
    
    try {
        // Update vehicle table
        $query1 = "UPDATE vozila SET 
                  Naziv = '$naziv',
                  Model = '$model',
                  CijenaKoristenjaDnevno = '$cijena',
                  Raspolozivost = '$raspolozivost'
                  WHERE IDVozilo = '$id'";
        
        // Update characteristics table
        $query2 = "UPDATE karakteristike_automobila SET 
                  Godiste = '$godiste',
                  Kilometraza = '$kilometraza',
                  Registracija = '$registracija'
                  WHERE VoziloID = '$id'";
        
        $result1 = mysqli_query($db, $query1);
        $result2 = mysqli_query($db, $query2);
        
        if ($result1 && $result2) {
            mysqli_commit($db);
            echo json_encode(['success' => 'Vozilo uspješno ažurirano']);
        } else {
            mysqli_rollback($db);
            echo json_encode(['error' => 'Greška pri ažuriranju vozila: ' . mysqli_error($db)]);
        }
    } catch (Exception $e) {
        mysqli_rollback($db);
        echo json_encode(['error' => 'Greška u transakciji: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Nevažeći zahtjev']);
}
?>