<?php
  session_start();

  // Αν είναι ήδη συνδεδεμένος πάει απευθείας στο index
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
      header("Location: index.php"); 
      exit();
  }
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Metropolitan College - Campus Ηρακλείου</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    /* CSS */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .navbar-brand img { height: 50px; }

    .hero-section {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://akmi-international.com/wp-content/uploads/2022/01/metropolitan.png');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        text-align: center;
        margin-bottom: 30px;
    }

    .map-container {
        height: 400px; 
        width: 100%;
        border-radius: 10px;
        z-index: 1;
    }

    .content-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    footer {
        margin-top: auto;
        background-color: #343a40;
        color: white;
        padding: 20px 0;
    }
    
    .info-list li { margin-bottom: 8px; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="https://akmi-international.com/wp-content/uploads/2022/01/metropolitan.png" alt="Logo">
        Metropolitan College
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
              <a class="btn btn-outline-light me-2" href="login.php">Σύνδεση</a>
          </li>
          <li class="nav-item">
              <a class="btn btn-warning" href="register.php">Εγγραφή</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <header class="hero-section">
    <div class="container">
      <h1 class="display-4 fw-bold">Campus Ηρακλείου Κρήτης</h1>
      <p class="lead">Σπουδές αριστείας σε ένα σύγχρονο περιβάλλον εκπαίδευσης.</p>
    </div>
  </header>

  <div class="container mb-5">
    <div class="row g-4">
      
      <div class="col-lg-6">
        <div class="content-card">
          <h2 class="text-primary mb-3"><i class="fas fa-university me-2"></i>Το Κολλέγιο</h2>
          
          <p>
            Το Metropolitan College στο Ηράκλειο Κρήτης αποτελεί το νεότερο μέλος του δικτύου μας. 
            Στεγάζεται σε ένα ιστορικό, πλήρως ανακαινισμένο κτίριο στο κέντρο της πόλης.
          </p>

          <h5 class="mt-3 text-secondary"><i class="fas fa-laptop-code me-2"></i>Εγκαταστάσεις</h5>
          <p>Το Campus διαθέτει υποδομές υψηλών προδιαγραφών που περιλαμβάνουν:</p>
          <ul class="info-list text-muted">
             <li>Σύγχρονα εργαστήρια Πληροφορικής και Τουρισμού.</li>
             <li>Εξειδικευμένα εργαστήρια Υγείας και Φυσικοθεραπείας.</li>
             <li>Πλήρως εξοπλισμένη Βιβλιοθήκη με ηλεκτρονικές βάσεις δεδομένων.</li>
             <li>Χώρους αναψυχής (Lounge Area) για τους φοιτητές.</li>
          </ul>

          <h5 class="mt-3 text-secondary"><i class="fas fa-graduation-cap me-2"></i>Σχολές & Προγράμματα</h5>
          <p>Στο Campus Ηρακλείου λειτουργούν τμήματα από τις εξής σχολές:</p>
          <div class="row">
              <div class="col-6">
                  <ul class="info-list small fw-bold">
                    <li>Σχολή Τουρισμού</li>
                    <li>Σχολή Πληροφορικής</li>
                    <li>Σχολή Διοίκησης</li>
                  </ul>
              </div>
              <div class="col-6">
                  <ul class="info-list small fw-bold">
                    <li>Σχολή Υγείας</li>
                    <li>Σχολή Ναυτιλιακών</li>
                    <li>Σχολή Ψυχολογίας</li>
                  </ul>
              </div>
          </div>

          <hr class="mt-auto">
          <div class="mt-2">
              <strong><i class="fas fa-map-pin text-danger"></i> Διεύθυνση:</strong> Θαλήτα 15, Ηράκλειο<br>
              <strong><i class="fas fa-phone text-success"></i> Τηλέφωνο:</strong> 2814 400800
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="content-card p-0 overflow-hidden d-flex align-items-center justify-content-center bg-dark">
            <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" 
                 alt="Campus Interior" 
                 style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9;">
        </div>
      </div>

      <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h3 class="mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Η Τοποθεσία μας</h3>
            </div>
            <div class="card-body p-0">
                <div id="map" class="map-container"></div>
            </div>
        </div>
      </div>

    </div>
  </div>

  <footer class="text-center">
    <div class="container">
      <p class="mb-0">&copy; <?php echo date("Y"); ?> Metropolitan College - Campus Ηρακλείου. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  
  <script>
      // Χάρτης Leaflet
      var map = L.map('map').setView([35.3418839, 25.1335153], 17);

      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
      }).addTo(map);

      L.marker([35.3418839, 25.1335153]).addTo(map)
          .bindPopup('<b>Metropolitan College</b><br>Campus Ηρακλείου<br>Θαλήτα 15')
          .openPopup();
  </script>

</body>
</html>