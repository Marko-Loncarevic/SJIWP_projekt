<!doctype html>
<html lang="en">
<head>
   
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="index.css">

    <title>Pregled vozila</title>
</head>
<body>

<?php
include("navigacija.php"); 
?>

<?php
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">Vozilo je uspješno dodano!</div>';
}
if (isset($_GET['deleted'])) {
    echo '<div class="alert alert-success">Vozilo je uspješno obrisano!</div>';
}
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>



<div class="container mt-4">
    <div class="row" id="vehicleCards">
        <?php
        include("db__connection.php");

        $query = "SELECT 
            v.IDVozilo,
            v.Naziv,
            v.Model,
            v.CijenaKoristenjaDnevno,
            v.Raspolozivost,
            ka.Godiste,
            ka.Kilometraza,
            ka.Registracija,
            CASE WHEN EXISTS (
                SELECT 1 FROM rezervacije r 
                WHERE r.VoziloID = v.IDVozilo 
                AND (
                    (r.DatumPocetka <= NOW() AND r.DatumZavrsetka >= NOW()) OR
                    (r.DatumPocetka >= NOW())
                )
            ) THEN 1 ELSE 0 END AS ImaAktivnuRezervaciju
        FROM 
            vozila v
        JOIN 
            karakteristike_automobila ka ON v.IDVozilo = ka.VoziloID;";

        $result = mysqli_query($db, $query) or die("Greška u SQL upitu: " . mysqli_error($db));

        while ($row = mysqli_fetch_array($result)) {
            $isAvailable = $row["Raspolozivost"] && !$row["ImaAktivnuRezervaciju"];
            echo '
            <div class="col-md-4 mb-4">
                <div class="card vehicle-card" data-id="' . $row["IDVozilo"] . '" 
                     data-name="' . $row["Naziv"] . ' ' . $row["Model"] . '" 
                     data-price="' . $row["CijenaKoristenjaDnevno"] . '"
                     data-available="' . ($isAvailable ? '1' : '0') . '">
                    <div class="card-body">
                        <h5 class="card-title">' . $row["Naziv"] . ' ' . $row["Model"] . '</h5>
                        <p class="card-text">
                            <strong>Cijena:</strong> ' . $row["CijenaKoristenjaDnevno"] . ' €/dan<br>
                            <strong>Godište:</strong> ' . $row["Godiste"] . '<br>
                            <strong>Kilometraža:</strong> ' . $row["Kilometraza"] . ' km<br>
                            <strong>Registracija:</strong> ' . $row["Registracija"] . '<br>
                            <strong>Status:</strong> ' . 
                            ($isAvailable ? '<span class="text-success">Dostupno</span>' : 
                            '<span class="text-danger">Nije dostupno</span>') . '
                        </p>
                        <button class="btn btn-primary btn-reserve" ' . 
                        (!$isAvailable ? 'disabled title="Vozilo nije dostupno za rezervaciju"' : '') . '>
                            Rezerviraj
                        </button>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addReservationModalLabel">Dodaj novu rezervaciju</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addReservationForm" action="dodaj_rezervaciju.php" method="POST">
      <div class="modal-body">
          <input type="hidden" id="selectedVehicleId" name="voziloID">
          <div class="mb-3">
            <label for="korisnikID" class="form-label">Korisnik</label>
            <select class="form-select" id="korisnikID" name="korisnikID" required>
              <?php
              include("db__connection.php");
              $query = "SELECT IDKorisnici, ImeKorisnika, PrezimeKorisnika FROM korisnici";
              $result = mysqli_query($db, $query);
              while ($row = mysqli_fetch_array($result)) {
                  echo '<option value="' . $row["IDKorisnici"] . '">' . $row["ImeKorisnika"] . ' ' . $row["PrezimeKorisnika"] . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="cijenaKoristenjaDnevno" class="form-label">Cijena (€/dan)</label>
            <input type="text" class="form-control" id="cijenaKoristenjaDnevno" name="cijenaKoristenjaDnevno" readonly>
          </div>
          <div class="mb-3">
            <label for="odKada" class="form-label">Od kada</label>
            <input type="datetime-local" class="form-control" id="odKada" name="odKada" required>
          </div>
          <div class="mb-3">
            <label for="doKada" class="form-label">Do kada</label>
            <input type="datetime-local" class="form-control" id="doKada" name="doKada" required>
          </div>
          <div class="mb-3">
  <label for="ukupnaCijena" class="form-label">Ukupna cijena (€)</label>
  <input type="text" class="form-control" id="ukupnaCijena" name="ukupnaCijena" readonly>
</div>
      </div>
     

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
        <button type="submit" class="btn btn-primary">Spremi rezervaciju</button>
      </div>
      
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Handle reservation button clicks
    document.querySelectorAll(".btn-reserve").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.stopPropagation();
            const card = this.closest(".vehicle-card");
            const isAvailable = card.getAttribute("data-available") === "1";
            
            if (!isAvailable) {
                alert("Ovo vozilo nije dostupno za rezervaciju!");
                return;
            }
            
            const vehicleId = card.getAttribute("data-id");
            const vehicleName = card.getAttribute("data-name");
            const vehiclePrice = card.getAttribute("data-price");
            
            // Set values in reservation modal
            document.getElementById("selectedVehicleId").value = vehicleId;
            document.getElementById("cijenaKoristenjaDnevno").value = vehiclePrice;
            
            // Show reservation modal
            const reservationModal = new bootstrap.Modal(document.getElementById("addReservationModal"));
            reservationModal.show();
        });
    });

    // Date validation
    document.getElementById('addReservationForm').addEventListener('submit', function(e) {
        const vehicleId = document.getElementById("selectedVehicleId").value;
        const odKada = new Date(document.getElementById('odKada').value);
        const doKada = new Date(document.getElementById('doKada').value);
        
        if (odKada >= doKada) {
            alert('Datum završetka mora biti nakon datuma početka!');
            e.preventDefault();
            return;
        }
    });
});
function izracunajUkupnuCijenu() {
    const cijenaPoDanu = parseFloat(document.getElementById("cijenaKoristenjaDnevno").value);
    const odKada = new Date(document.getElementById("odKada").value);
    const doKada = new Date(document.getElementById("doKada").value);
    
    if (!isNaN(cijenaPoDanu) && odKada && doKada && doKada > odKada) {
      const razlikaUDanima = Math.ceil((doKada - odKada) / (1000 * 60 * 60 * 24));
      const ukupnaCijena = cijenaPoDanu * razlikaUDanima;
      document.getElementById("ukupnaCijena").value = ukupnaCijena.toFixed(2);
    } else {
      document.getElementById("ukupnaCijena").value = '';
    }
  }

  document.getElementById("odKada").addEventListener("change", izracunajUkupnuCijenu);
  document.getElementById("doKada").addEventListener("change", izracunajUkupnuCijenu);
  document.getElementById("cijenaKoristenjaDnevno").addEventListener("input", izracunajUkupnuCijenu);
</script>

<?php
include("footer.php"); 
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>