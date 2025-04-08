<?php
include("db__connection.php");

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($db, $_GET['id']);
    $query = "SELECT 
                vozila.*, 
                karakteristike_automobila.* 
              FROM 
                vozila 
              JOIN 
                karakteristike_automobila ON vozila.IDVozilo = karakteristike_automobila.VoziloID 
              WHERE 
                vozila.IDVozilo = '$id'";
    $result = mysqli_query($db, $query);
    $vozilo = mysqli_fetch_assoc($result);
    
    if (!$vozilo) {
        header("Location: pregled_vozila.php?error=Vozilo nije pronađeno");
        exit();
    }
}


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
   
        $query1 = "UPDATE vozila SET 
                  Naziv = '$naziv',
                  Model = '$model',
                  CijenaKoristenjaDnevno = '$cijena',
                  Raspolozivost = '$raspolozivost'
                  WHERE IDVozilo = '$id'";
        
  
        $query2 = "UPDATE karakteristike_automobila SET 
                  Godiste = '$godiste',
                  Kilometraza = '$kilometraza',
                  Registracija = '$registracija'
                  WHERE VoziloID = '$id'";
        
        $result1 = mysqli_query($db, $query1);
        $result2 = mysqli_query($db, $query2);
        
        if ($result1 && $result2) {
            mysqli_commit($db);
            header("Location: pregled_vozila.php?success=Vozilo uspješno ažurirano");
            exit();
        } else {
            mysqli_rollback($db);
            header("Location: pregled_vozila.php?error=Greška pri ažuriranju vozila");
            exit();
        }
    } catch (Exception $e) {
        mysqli_rollback($db);
        header("Location: pregled_vozila.php?error=Greška u transakciji: " . $e->getMessage());
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
    <title>Uredi vozilo</title>
    <style>
        .badge-available {
            background-color: #28a745;
            color: white;
        }
        .badge-unavailable {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <?php include("navigacija.php"); ?>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">Uredi vozilo</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $vozilo['IDVozilo'] ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Naziv vozila <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="naziv" 
                                           value="<?= htmlspecialchars($vozilo['Naziv']) ?>" 
                                           maxlength="25" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Model vozila</label>
                                    <input type="text" class="form-control" name="model" 
                                           value="<?= htmlspecialchars($vozilo['Model'] ?? '') ?>" 
                                           maxlength="25">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cijena korištenja dnevno <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="cijena" 
                                           value="<?= htmlspecialchars($vozilo['CijenaKoristenjaDnevno']) ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Godina proizvodnje</label>
                                    <input type="text" class="form-control" name="godiste" 
                                           value="<?= htmlspecialchars($vozilo['Godiste'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prijeđenih kilometara</label>
                                    <input type="number" class="form-control" name="kilometraza" 
                                           value="<?= htmlspecialchars($vozilo['Kilometraza'] ?? '') ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Registracija</label>
                                    <input type="text" class="form-control" name="registracija" 
                                           value="<?= htmlspecialchars($vozilo['Registracija'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Raspoloživost <span class="text-danger">*</span></label>
                                <select class="form-control" name="raspolozivost" required>
                                    <option value="Dostupno" <?= ($vozilo['Raspolozivost'] == 'Dostupno') ? 'selected' : '' ?>>Dostupno</option>
                                    <option value="Nije dostupno" <?= ($vozilo['Raspolozivost'] != 'Dostupno') ? 'selected' : '' ?>>Nije dostupno</option>
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="pregled_vozila.php" class="btn btn-secondary">Natrag</a>
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