<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="pregled_vozila.css">
    <title>Pregled vozila</title>
    <style>
        .badge-available {
            background-color: #28a745;
            color: white;
        }
        .badge-unavailable {
            background-color: #dc3545;
            color: white;
        }
        .badge-reserved {
            background-color: #ffc107;
            color: black;
        }
        .action-btns .btn {
            margin-right: 5px;
        }
        .action-btns .btn:last-child {
            margin-right: 0;
        }
        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: box-shadow 0.3s ease;
        }
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-card-1 { border-left-color: #28a745; }
        .stat-card-2 { border-left-color: #17a2b8; }
        .stat-card-3 { border-left-color: #6f42c1; }
        .stat-card-4 { border-left-color: #fd7e14; }
    </style>
</head>
<body class="bg-light">
<?php include("navigacija.php"); ?>
    <div class="container-fluid py-4">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Pregled vozila</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                <i class="fas fa-plus"></i> Dodaj novo vozilo
            </button>
        </div>

        <div class="row mb-4">
            <?php
            include("db__connection.php");
            
            $mostRentedQuery = "SELECT v.IDVozilo, v.Naziv, v.Model, 
                              COUNT(r.IDRezervacija) AS BrojRezervacija,
                              SUM(DATEDIFF(r.DatumZavrsetka, r.DatumPocetka)) AS UkupnoDana
                              FROM vozila v
                              LEFT JOIN rezervacije r ON v.IDVozilo = r.VoziloID
                              GROUP BY v.IDVozilo, v.Naziv, v.Model
                              ORDER BY BrojRezervacija DESC, UkupnoDana DESC
                              LIMIT 1";
            $mostRentedResult = mysqli_query($db, $mostRentedQuery);
            $mostRented = mysqli_fetch_assoc($mostRentedResult);
            
            $highestEarningQuery = "SELECT v.IDVozilo, v.Naziv, v.Model, 
                                   SUM(r.UkupnaCijena) AS UkupnaZarada
                                   FROM vozila v
                                   LEFT JOIN rezervacije r ON v.IDVozilo = r.VoziloID
                                   GROUP BY v.IDVozilo, v.Naziv, v.Model
                                   ORDER BY UkupnaZarada DESC
                                   LIMIT 1";
            $highestEarningResult = mysqli_query($db, $highestEarningQuery);
            $highestEarning = mysqli_fetch_assoc($highestEarningResult);
            
            $statsQuery = "SELECT 
                          SUM(DATEDIFF(r.DatumZavrsetka, r.DatumPocetka)) AS UkupnoDana,
                          SUM(r.UkupnaCijena) AS UkupnaZarada
                          FROM rezervacije r";
            $statsResult = mysqli_query($db, $statsQuery);
            $stats = mysqli_fetch_assoc($statsResult);
            
            $currentDate = date('Y-m-d');
            $rentedQuery = "SELECT COUNT(DISTINCT VoziloID) AS TrenutnoIznajmljeno
                           FROM rezervacije
                           WHERE StatusRezervacije = 'aktivna'";
            $stmt = mysqli_prepare($db, $rentedQuery);
            mysqli_stmt_execute($stmt);
            $rentedResult = mysqli_stmt_get_result($stmt);
            $rentedCount = mysqli_fetch_assoc($rentedResult);
            ?>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 stat-card-1">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Najiznajmljivanije vozilo</h6>
                        <h4 class="card-text">
                            <?= $mostRented ? htmlspecialchars($mostRented['Naziv'].' '.$mostRented['Model']) : 'Nema podataka' ?>
                        </h4>
                        <div>
                            <span class="badge bg-primary me-2">
                                <?= $mostRented ? $mostRented['BrojRezervacija'].' rez.' : '0 rez.' ?>
                            </span>
                            <span class="badge bg-info">
                                <?= $mostRented ? $mostRented['UkupnoDana'].' dana' : '0 dana' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 stat-card-2">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Najveća zarada od vozila</h6>
                        <h4 class="card-text">
                            <?= $highestEarning ? htmlspecialchars($highestEarning['Naziv'].' '.$highestEarning['Model']) : 'Nema podataka' ?>
                        </h4>
                        <span class="badge bg-success">
                            <?= $highestEarning ? number_format($highestEarning['UkupnaZarada'], 2).' €' : '0.00 €' ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 stat-card-3">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Ukupno iznajmljivanja</h6>
                        <h2 class="card-text"><?= $stats['UkupnoDana'] ?? 0 ?> dana</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 stat-card-4">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Trenutno iznajmljeno</h6>
                        <h2 class="card-text"><?= $rentedCount['TrenutnoIznajmljeno'] ?? 0 ?> vozila</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Vehicle Modal -->
        <div class="modal fade" id="addVehicleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addVehicleModalLabel">Dodaj novo vozilo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addVehicleForm" action="dodaj_vozilo.php" method="POST">
                            <div class="mb-3">
                                <label for="nazivVozila" class="form-label">Naziv vozila</label>
                                <input type="text" class="form-control" id="nazivVozila" name="nazivVozila" maxlength="25" required>
                            </div>
                            <div class="mb-3">
                                <label for="modelVozila" class="form-label">Model vozila</label>
                                <input type="text" class="form-control" id="modelVozila" name="modelVozila" maxlength="25">
                            </div>
                            <div class="mb-3">
                                <label for="cijenaVozila" class="form-label">Cijena korištenja dnevno</label>
                                <input type="number" step="0.01" class="form-control" id="cijenaVozila" name="cijenaVozila" required>
                            </div>
                            <div class="mb-3">
                                <label for="godiste" class="form-label">Godina proizvodnje</label>
                                <input type="text" class="form-control" id="godiste" name="godiste">
                            </div>
                            <div class="mb-3">
                                <label for="kilometraza" class="form-label">Prijeđenih kilometara</label>
                                <input type="number" class="form-control" id="kilometraza" name="kilometraza">
                            </div>
                            <div class="mb-3">
                                <label for="registracija" class="form-label">Registracija</label>
                                <input type="text" class="form-control" id="registracija" name="registracija">
                            </div>
                            <div class="mb-3">
                                <label for="raspolozivost" class="form-label">Raspoloživost</label>
                                <select class="form-control" id="raspolozivost" name="raspolozivost" required>
                                    <option value="Dostupno" selected>Dostupno</option>
                                    <option value="Nije dostupno">Nije dostupno</option>
                                    <option value="Rezervirano">Rezervirano</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
                        <button type="submit" form="addVehicleForm" class="btn btn-primary">Spremi vozilo</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Vehicle Modal -->
        <div class="modal fade" id="editVehicleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVehicleModalLabel">Uredi vozilo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editVehicleForm" method="POST">
                            <input type="hidden" name="id" id="editVehicleId">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Naziv vozila <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="naziv" id="editNaziv" maxlength="25" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Model vozila</label>
                                    <input type="text" class="form-control" name="model" id="editModel" maxlength="25">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cijena korištenja dnevno <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="cijena" id="editCijena" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Godina proizvodnje</label>
                                    <input type="text" class="form-control" name="godiste" id="editGodiste">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prijeđenih kilometara</label>
                                    <input type="number" class="form-control" name="kilometraza" id="editKilometraza">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Registracija</label>
                                    <input type="text" class="form-control" name="registracija" id="editRegistracija">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Raspoloživost <span class="text-danger">*</span></label>
                                <select class="form-control" name="raspolozivost" id="editRaspolozivost" required>
                                    <option value="Dostupno">Dostupno</option>
                                    <option value="Nije dostupno">Nije dostupno</option>
                                    <option value="Rezervirano">Rezervirano</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
                        <button type="submit" form="editVehicleForm" class="btn btn-primary">Spremi promjene</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Naziv</th>
                                <th>Model</th>
                                <th>Cijena/dan</th>
                                <th>Godište</th>
                                <th>Kilometraža</th>
                                <th>Registracija</th>
                                <th>Status</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Update vehicle availability statuses
                            $resetStatusQuery = "UPDATE vozila SET Raspolozivost = 'Dostupno'";
                            mysqli_query($db, $resetStatusQuery);

                            $unavailableQuery = "UPDATE vozila v
                                             JOIN rezervacije r ON v.IDVozilo = r.VoziloID 
                                             SET v.Raspolozivost = 'Nije dostupno'
                                             WHERE r.StatusRezervacije = 'aktivna'
                                               AND CURDATE() BETWEEN r.DatumPocetka AND r.DatumZavrsetka";
                            mysqli_query($db, $unavailableQuery);

                            $reservedQuery = "UPDATE vozila v
                                          JOIN rezervacije r ON v.IDVozilo = r.VoziloID
                                          SET v.Raspolozivost = 'Rezervirano'
                                          WHERE r.StatusRezervacije = 'aktivna'
                                            AND CURDATE() < r.DatumPocetka";
                            mysqli_query($db, $reservedQuery);

                            $query = "SELECT 
                                v.IDVozilo,
                                v.Naziv,
                                v.Model,
                                v.CijenaKoristenjaDnevno,
                                v.Raspolozivost,
                                ka.Godiste,
                                ka.Kilometraza,
                                ka.Registracija,
                                COUNT(r.IDRezervacija) AS BrojRezervacija,
                                SUM(DATEDIFF(r.DatumZavrsetka, r.DatumPocetka)) AS UkupnoDana,
                                SUM(r.UkupnaCijena) AS UkupnaZarada
                            FROM 
                                vozila v
                            LEFT JOIN 
                                karakteristike_automobila ka ON v.IDVozilo = ka.VoziloID
                            LEFT JOIN
                                rezervacije r ON v.IDVozilo = r.VoziloID
                            GROUP BY
                                v.IDVozilo, v.Naziv, v.Model, v.CijenaKoristenjaDnevno, 
                                v.Raspolozivost, ka.Godiste, ka.Kilometraza, ka.Registracija
                            ORDER BY 
                                v.Naziv, v.Model";

                            $result = mysqli_query($db, $query) or die("Greška u SQL upitu: " . mysqli_error($db));
                            
                            while ($row = mysqli_fetch_assoc($result)): 
                                $status = $row['Raspolozivost'] ?? 'Nije dostupno';
                                $statusClass = '';
                                $statusText = '';
                                
                                if ($status == 'Dostupno') {
                                    $statusClass = 'badge-available';
                                    $statusText = 'Dostupno';
                                } elseif ($status == 'Rezervirano') {
                                    $statusClass = 'badge-reserved';
                                    $statusText = 'Rezervirano';
                                } else {
                                    $statusClass = 'badge-unavailable';
                                    $statusText = 'Nije dostupno';
                                }
                            ?>
                                <tr class="hover-shadow">
                                    <td><?= $row['IDVozilo'] ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['Naziv']) ?>
                                        <?php if ($row['BrojRezervacija'] > 0): ?>
                                            <span class="badge bg-primary ms-2" title="Broj rezervacija">
                                                <?= $row['BrojRezervacija'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['Model'] ?? 'N/A') ?></td>
                                    <td><?= number_format($row['CijenaKoristenjaDnevno'], 2) ?> €</td>
                                    <td><?= htmlspecialchars($row['Godiste'] ?? 'N/A') ?></td>
                                    <td><?= isset($row['Kilometraza']) ? number_format($row['Kilometraza'], 0, ',', '.').' km' : 'N/A' ?></td>
                                    <td><?= htmlspecialchars($row['Registracija'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge <?= $statusClass ?>" title="<?= $statusText ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td class="action-btns">
                                        <button class="btn btn-sm btn-outline-primary btn-edit" 
                                                data-id="<?= $row['IDVozilo'] ?>"
                                                data-naziv="<?= htmlspecialchars($row['Naziv']) ?>"
                                                data-model="<?= htmlspecialchars($row['Model'] ?? '') ?>"
                                                data-cijena="<?= htmlspecialchars($row['CijenaKoristenjaDnevno']) ?>"
                                                data-godiste="<?= htmlspecialchars($row['Godiste'] ?? '') ?>"
                                                data-kilometraza="<?= htmlspecialchars($row['Kilometraza'] ?? '') ?>"
                                                data-registracija="<?= htmlspecialchars($row['Registracija'] ?? '') ?>"
                                                data-raspolozivost="<?= $status ?>"
                                                title="Uredi">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="obrisi_vozilo.php?id=<?= $row['IDVozilo'] ?>" class="btn btn-sm btn-outline-danger" title="Obriši" onclick="return confirm('Jeste li sigurni da želite obrisati ovo vozilo?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                        <?php if ($row['BrojRezervacija'] > 0): ?>
                                            <button class="btn btn-sm btn-outline-info" 
                                                    title="Statistika iznajmljivanja"
                                                    data-bs-toggle="popover"
                                                    data-bs-html="true"
                                                    data-bs-content="<div><small>Ukupno dana:</small> <?= $row['UkupnoDana'] ?? 0 ?></div>
                                                                  <div><small>Ukupna zarada:</small> <?= number_format($row['UkupnaZarada'] ?? 0, 2) ?> €</div>">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);

        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'hover focus'
            });
        });

        // Handle edit button clicks
        document.addEventListener('DOMContentLoaded', function() {
            // Set up edit modal
            const editVehicleModal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
            const editForm = document.getElementById('editVehicleForm');
            
            // Handle edit button clicks
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Fill the form with vehicle data
                    document.getElementById('editVehicleId').value = this.getAttribute('data-id');
                    document.getElementById('editNaziv').value = this.getAttribute('data-naziv');
                    document.getElementById('editModel').value = this.getAttribute('data-model');
                    document.getElementById('editCijena').value = this.getAttribute('data-cijena');
                    document.getElementById('editGodiste').value = this.getAttribute('data-godiste') || '';
                    document.getElementById('editKilometraza').value = this.getAttribute('data-kilometraza') || '';
                    document.getElementById('editRegistracija').value = this.getAttribute('data-registracija') || '';
                    
                    // Set availability status
                    const status = this.getAttribute('data-raspolozivost');
                    const select = document.getElementById('editRaspolozivost');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value === status) {
                            select.selectedIndex = i;
                            break;
                        }
                    }
                    
                    editVehicleModal.show();
                });
            });
            
            // Handle edit form submission
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('obrada_uredi_vozilo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'pregled_vozila.php?success=' + encodeURIComponent(data.success);
                    } else {
                        alert(data.error || 'Došlo je do greške');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Došlo je do greške prilikom komunikacije sa serverom');
                });
            });
        });
    </script>
</body>
</html>