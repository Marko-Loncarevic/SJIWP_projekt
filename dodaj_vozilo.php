<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("db__connection.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $naziv = $_POST['nazivVozila'];
    $model = $_POST['modelVozila'];
    $cijena = $_POST['cijenaVozila'];
    $godiste = $_POST['godiste'];
    $kilometraza = $_POST['kilometraza'];
    $registracija = $_POST['registracija'];


    $queryVozilo = "INSERT INTO vozila (Naziv, Model, CijenaKoristenjaDnevno) VALUES ('$naziv', '$model', '$cijena')";

    if (mysqli_query($db, $queryVozilo)) {
    
        $voziloID = mysqli_insert_id($db);

        $queryKarakteristike = "INSERT INTO karakteristike_automobila (Godiste, Kilometraza, Registracija, VoziloID) VALUES ('$godiste', '$kilometraza', '$registracija', '$voziloID')";

        if (mysqli_query($db, $queryKarakteristike)) {
      
            header("Location: index.php"); 
            exit();
        } else {
          
            echo "Greška pri unosu karakteristika vozila: " . mysqli_error($db);
        }
    } else {
       
        echo "Greška pri unosu vozila: " . mysqli_error($db);
    }


    mysqli_close($db);
} else {
   
    echo "Forma nije poslana.";
}
?>