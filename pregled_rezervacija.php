<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="pregled_rezervacija.css">
    <title>Pregled rezervacija</title>
    
</head>
<body>
<?php include("navigacija.php"); ?>
    <div class="container-fluid mt-4">
      

        <h2 class="mb-4 text-center">Pregled rezervacija</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>


        <div class="filter-section mb-4">
            <form method="get" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Svi statusi</option>
                            <option value="Aktivna" <?= (isset($_GET['status']) && $_GET['status'] == 'Aktivna') ? 'selected' : '' ?>>Aktivna</option>
                            <option value="Zavrsena" <?= (isset($_GET['status']) && $_GET['status'] == 'Zavrsena') ? 'selected' : '' ?>>Završena</option>
                           
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Datum od</label>
                        <input type="date" class="form-control" name="date_from" value="<?= $_GET['date_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Datum do</label>
                        <input type="date" class="form-control" name="date_to" value="<?= $_GET['date_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtriraj</button>
                        <a href="pregled_rezervacija.php" class="btn btn-secondary">Resetiraj</a>
                    </div>
                </div>
            </form>
        </div>

        <?php
        include("db__connection.php");
        
 
        $currentDate = date('Y-m-d');
        
        $updateQuery = "UPDATE rezervacije r
                       JOIN vozila v ON r.VoziloID = v.IDVozilo
                       SET r.StatusRezervacije = 'Zavrsena', 
                           v.Raspolozivost = 'Dostupno'
                       WHERE r.StatusRezervacije = 'Aktivna' 
                       AND r.DatumZavrsetka < ?";
        $stmt = mysqli_prepare($db, $updateQuery);
        mysqli_stmt_bind_param($stmt, "s", $currentDate);
        mysqli_stmt_execute($stmt);
        
      
        $updateQuery2 = "UPDATE rezervacije r
                        JOIN vozila v ON r.VoziloID = v.IDVozilo
                        SET r.StatusRezervacije = 'Aktivna',
                            v.Raspolozivost = 'Rezervirano'
                        WHERE r.StatusRezervacije = 'Zavrsena' 
                        AND r.DatumZavrsetka >= ?";
        $stmt2 = mysqli_prepare($db, $updateQuery2);
        mysqli_stmt_bind_param($stmt2, "s", $currentDate);
        mysqli_stmt_execute($stmt2);
        ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Korisnik</th>
                        <th>Vozilo</th>
                        <th>Datum rezervacije</th>
                        <th>Period</th>
                        <th>Trajanje</th>
                        <th>Cijena</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                  
                    $query = "SELECT 
                        r.IDRezervacija,
                        k.ImeKorisnika,
                        k.PrezimeKorisnika,
                        v.Naziv AS VoziloNaziv,
                        v.Model AS VoziloModel,
                        r.DatumRezervacije,
                        r.DatumPocetka,
                        r.DatumZavrsetka,
                        r.UkupnaCijena,
                        r.StatusRezervacije,
                        v.Raspolozivost
                    FROM rezervacije r
                    JOIN korisnici k ON r.KorisnikID = k.IDKorisnici
                    JOIN vozila v ON r.VoziloID = v.IDVozilo";
                    
                   
                    $conditions = [];
                    $params = [];
                    $types = '';
                    
                    if (!empty($_GET['status'])) {
                        $conditions[] = "r.StatusRezervacije = ?";
                        $params[] = $_GET['status'];
                        $types .= 's';
                    }
                    
                    if (!empty($_GET['date_from'])) {
                        $conditions[] = "r.DatumPocetka >= ?";
                        $params[] = $_GET['date_from'];
                        $types .= 's';
                    }
                    
                    if (!empty($_GET['date_to'])) {
                        $conditions[] = "r.DatumZavrsetka <= ?";
                        $params[] = $_GET['date_to'];
                        $types .= 's';
                    }
                    
                    if (empty($conditions)) {
                        $conditions[] = "1=1";
                    }
                    
                    if (!empty($conditions)) {
                        $query .= " WHERE " . implode(" AND ", $conditions);
                    }
                    
                    $query .= " ORDER BY r.DatumPocetka DESC";
                    
                 
                    $stmt = mysqli_prepare($db, $query);
                    
                    if (!empty($params)) {
                        mysqli_stmt_bind_param($stmt, $types, ...$params);
                    }
                    
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $startDate = new DateTime($row['DatumPocetka']);
                            $endDate = new DateTime($row['DatumZavrsetka']);
                            $duration = $startDate->diff($endDate)->days;
                            
                            $currentDate = new DateTime();
                            $isActive = ($currentDate >= $startDate && $currentDate <= $endDate && $row['StatusRezervacije'] != 'Otkazana');
                            $isCompleted = ($currentDate > $endDate && $row['StatusRezervacije'] != 'Otkazana');
                            
                            $formattedStartDate = $startDate->format('d.m.Y');
                            $formattedEndDate = $endDate->format('d.m.Y');
                            
                            echo "<tr class='hover-shadow'>
    <td>{$row['IDRezervacija']}</td>
    <td>{$row['ImeKorisnika']} {$row['PrezimeKorisnika']}</td>
    <td>{$row['VoziloNaziv']} {$row['VoziloModel']}</td>
    <td>" . date('d.m.Y', strtotime($row['DatumRezervacije'])) . "</td>
    <td>{$formattedStartDate} - {$formattedEndDate}</td>
    <td>{$duration} dana</td>
    <td>" . number_format($row['UkupnaCijena'], 2) . " €</td>
    <td>
        <span class='status-badge status-" . strtolower($row['StatusRezervacije']) . "'>
            " . ucfirst($row['StatusRezervacije']) . "
        </span>
    </td>
   
    <td>
                                    <a href='otkazi_rezervaciju.php?id={$row['IDRezervacija']}'  class='btn btn-sm btn-outline-danger' title='Obriši' onclick='return confirm('Jeste li sigurni?')'
                                    </a>
                                    <i class='fas fa-trash-alt'> 
                                    " . ($row['StatusRezervacije'] == 'Aktivna' ? 
                                        "<a href='otkazi_rezervaciju.php?id={$row['IDRezervacija']}' class='btn btn-sm btn-danger'>Otkaži</a>" : 
                                        "") . "
                                        
                                </td>
</tr>"; 
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>Nema pronađenih rezervacija</td></tr>";
                    }
                    
                    mysqli_close($db);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include("footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

