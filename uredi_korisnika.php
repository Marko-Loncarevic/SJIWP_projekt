<?php
include("db__connection.php");

// Dohvaćanje podataka korisnika za uređivanje
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($db, $_GET['id']);
    $query = "SELECT * FROM korisnici WHERE IDKorisnici = '$id'";
    $result = mysqli_query($db, $query);
    $korisnik = mysqli_fetch_assoc($result);
    
    if (!$korisnik) {
        header("Location: korisnici.php?error=Korisnik nije pronađen");
        exit();
    }
}

// Obrada forme za ažuriranje
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
        header("Location: korisnici.php?success=Korisnik uspješno ažuriran");
        exit();
    } else {
        header("Location: korisnici.php?error=Greška pri ažuriranju korisnika");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Uredi korisnika</title>
</head>
<body class="bg-light">
<?php include("navigacija.php"); ?>
    <div class="container py-4">
     
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">Uredi korisnika</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $korisnik['IDKorisnici'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Ime <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ime" 
                                       value="<?= htmlspecialchars($korisnik['ImeKorisnika']) ?>" 
                                       maxlength="25" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Prezime <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="prezime" 
                                       value="<?= htmlspecialchars($korisnik['PrezimeKorisnika']) ?>" 
                                       maxlength="25" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kontakt</label>
                                <input type="text" class="form-control" name="kontakt" 
                                       value="<?= htmlspecialchars($korisnik['KontaktKorisnika'] ?? '') ?>" 
                                       maxlength="40">
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="korisnici.php" class="btn btn-secondary">Natrag</a>
                                <button type="submit" class="btn btn-primary">Spremi promjene</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>