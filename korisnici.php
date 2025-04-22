<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
    <title>Pregled korisnika</title>
    <style>
        .action-btns .btn {
            margin-right: 5px;
        }
        .action-btns .btn:last-child {
            margin-right: 0;
        }
        .badge-rental {
            background-color: #17a2b8;
            color: white;
        }
        .badge-payment {
            background-color: #6f42c1;
            color: white;
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Pregled korisnika</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i>Dodaj korisnika
            </button>
        </div>

        <div class="row mb-4">
            <?php
            include("db__connection.php");
            
            $topRentalQuery = "SELECT k.IDKorisnici, k.ImeKorisnika, k.PrezimeKorisnika, 
                             SUM(DATEDIFF(r.DatumZavrsetka, r.DatumPocetka)) AS UkupnoDana
                             FROM korisnici k
                             LEFT JOIN rezervacije r ON k.IDKorisnici = r.KorisnikID
                             GROUP BY k.IDKorisnici
                             ORDER BY UkupnoDana DESC
                             LIMIT 1";
            $topRentalResult = mysqli_query($db, $topRentalQuery);
            $topRentalUser = mysqli_fetch_assoc($topRentalResult);
            
            $topPaymentQuery = "SELECT k.IDKorisnici, k.ImeKorisnika, k.PrezimeKorisnika, 
                              SUM(r.UkupnaCijena) AS UkupnoPlatio
                              FROM korisnici k
                              LEFT JOIN rezervacije r ON k.IDKorisnici = r.KorisnikID
                              GROUP BY k.IDKorisnici
                              ORDER BY UkupnoPlatio DESC
                              LIMIT 1";
            $topPaymentResult = mysqli_query($db, $topPaymentQuery);
            $topPaymentUser = mysqli_fetch_assoc($topPaymentResult);
            
            $statsQuery = "SELECT 
                          SUM(DATEDIFF(r.DatumZavrsetka, r.DatumPocetka)) AS UkupnoDana,
                          SUM(r.UkupnaCijena) AS UkupnoPlatio
                          FROM rezervacije r";
            $statsResult = mysqli_query($db, $statsQuery);
            $stats = mysqli_fetch_assoc($statsResult);
            ?>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 border-left-primary">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Najaktivniji korisnik</h6>
                        <h4 class="card-text">
                            <?= $topRentalUser ? htmlspecialchars($topRentalUser['ImeKorisnika'].' '.$topRentalUser['PrezimeKorisnika']) : 'Nema podataka' ?>
                        </h4>
                        <span class="badge badge-rental">
                            <?= $topRentalUser ? $topRentalUser['UkupnoDana'].' dana' : '0 dana' ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 border-left-success">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Najveći kupac</h6>
                        <h4 class="card-text">
                            <?= $topPaymentUser ? htmlspecialchars($topPaymentUser['ImeKorisnika'].' '.$topPaymentUser['PrezimeKorisnika']) : 'Nema podataka' ?>
                        </h4>
                        <span class="badge badge-payment">
                            <?= $topPaymentUser ? number_format($topPaymentUser['UkupnoPlatio'], 2).' €' : '0.00 €' ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 border-left-info">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Ukupno dana iznajmljivanja</h6>
                        <h2 class="card-text"><?= $stats['UkupnoDana'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 border-left-warning">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Ukupna naplaćena vrijednost</h6>
                        <h2 class="card-text"><?= isset($stats['UkupnoPlatio']) ? number_format($stats['UkupnoPlatio'], 2).' €' : '0.00 €' ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="dodaj_korisnika.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Dodaj novog korisnika</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Ime <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ime" maxlength="25" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prezime <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="prezime" maxlength="25" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kontakt</label>
                                <input type="text" class="form-control" name="kontakt" maxlength="40">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                            <button type="submit" class="btn btn-primary">Spremi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editUserForm" method="POST">
                        <input type="hidden" name="id" id="editUserId">
                        <div class="modal-header">
                            <h5 class="modal-title">Uredi korisnika</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Ime <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ime" id="editIme" maxlength="25" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prezime <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="prezime" id="editPrezime" maxlength="25" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kontakt</label>
                                <input type="text" class="form-control" name="kontakt" id="editKontakt" maxlength="40">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
                            <button type="submit" class="btn btn-primary">Spremi promjene</button>
                        </div>
                    </form>
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
                                <th>Ime</th>
                                <th>Prezime</th>
                                <th>Kontakt</th>
                                <th>Dana iznajmljivanja</th>
                                <th>Ukupno plaćeno</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT 
                                    k.IDKorisnici, 
                                    k.ImeKorisnika, 
                                    k.PrezimeKorisnika, 
                                    k.KontaktKorisnika,
                                    COALESCE(SUM(DATEDIFF(r.DatumZavrsetka, r.DatumPocetka)), 0) AS UkupnoDana,
                                    COALESCE(SUM(r.UkupnaCijena), 0) AS UkupnoPlatio
                                  FROM 
                                    korisnici k
                                  LEFT JOIN 
                                    rezervacije r ON k.IDKorisnici = r.KorisnikID
                                  GROUP BY 
                                    k.IDKorisnici, k.ImeKorisnika, k.PrezimeKorisnika, k.KontaktKorisnika
                                  ORDER BY 
                                    k.PrezimeKorisnika, k.ImeKorisnika";
                            
                            $result = mysqli_query($db, $query) or die("Greška u SQL upitu: " . mysqli_error($db));
                            
                            while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover-shadow">
                                    <td><?= $row['IDKorisnici'] ?></td>
                                    <td><?= htmlspecialchars($row['ImeKorisnika']) ?></td>
                                    <td><?= htmlspecialchars($row['PrezimeKorisnika']) ?></td>
                                    <td><?= htmlspecialchars($row['KontaktKorisnika'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge badge-rental">
                                            <?= $row['UkupnoDana'] ?> dana
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-payment">
                                            <?= number_format($row['UkupnoPlatio'], 2) ?> €
                                        </span>
                                    </td>
                                    <td class="action-btns">
                                        <button class="btn btn-sm btn-outline-primary btn-edit" 
                                                data-id="<?= $row['IDKorisnici'] ?>"
                                                data-ime="<?= htmlspecialchars($row['ImeKorisnika']) ?>"
                                                data-prezime="<?= htmlspecialchars($row['PrezimeKorisnika']) ?>"
                                                data-kontakt="<?= htmlspecialchars($row['KontaktKorisnika'] ?? '') ?>"
                                                title="Uredi">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="obrisi_korisnika.php?id=<?= $row['IDKorisnici'] ?>" class="btn btn-sm btn-outline-danger" title="Obriši" onclick="return confirm('Jeste li sigurni?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);

        // Handle edit button clicks
        document.addEventListener('DOMContentLoaded', function() {
            // Set up edit modal
            const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            const editForm = document.getElementById('editUserForm');
            
            // Handle edit button clicks
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('editUserId').value = this.getAttribute('data-id');
                    document.getElementById('editIme').value = this.getAttribute('data-ime');
                    document.getElementById('editPrezime').value = this.getAttribute('data-prezime');
                    document.getElementById('editKontakt').value = this.getAttribute('data-kontakt') || '';
                    
                    editUserModal.show();
                });
            });
            
            // Handle edit form submission
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('obrada_uredi_korisnika.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'korisnici.php?success=' + encodeURIComponent(data.success);
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