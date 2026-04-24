<?php
$pdo = new PDO("mysql:host=localhost;dbname=ton_projet;charset=utf8", "root", "");

// Stats globales
$nbClients = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();
$nbMedecins = $pdo->query("SELECT COUNT(*) FROM medecins")->fetchColumn();
$nbRDV = $pdo->query("SELECT COUNT(*) FROM rendez_vous")->fetchColumn();

// RDV par mois (graphique)
$data = $pdo->query("
    SELECT MONTH(date_heure) as mois, COUNT(*) as total
    FROM rendez_vous
    GROUP BY mois
")->fetchAll(PDO::FETCH_ASSOC);

$mois = [];
$totaux = [];

foreach ($data as $row) {
    $mois[] = $row['mois'];
    $totaux[] = $row['total'];
}

// Prochains rendez-vous
$rdvs = $pdo->query("
    SELECT r.date_heure, c.nom, m.nom as medecin
    FROM rendez_vous r
    JOIN clients c ON r.client_id = c.id
    JOIN medecins m ON r.medecin_id = m.id
    ORDER BY r.date_heure ASC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard Médical</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    background: #f5f7fb;
}

/* NAVBAR */
.navbar {
    background: #0d6efd;
}
.navbar-brand {
    color: white;
    font-weight: bold;
}

/* CARDS */
.card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.stat {
    font-size: 2rem;
    font-weight: bold;
    color: #0d6efd;
}

/* TABLE */
.table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar p-3">
    <div class="container">
        <span class="navbar-brand">🏥 MedApp</span>
        <a href="login.php" class="btn btn-light">Connexion</a>
    </div>
</nav>

<div class="container mt-4">

    <!-- STATS -->
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h5>Clients</h5>
                <div class="stat"><?= $nbClients ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h5>Médecins</h5>
                <div class="stat"><?= $nbMedecins ?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 text-center">
                <h5>Rendez-vous</h5>
                <div class="stat"><?= $nbRDV ?></div>
            </div>
        </div>

    </div>

    <!-- GRAPHIQUE -->
    <div class="card mt-5 p-4">
        <h4>📊 Rendez-vous par mois</h4>
        <canvas id="chart"></canvas>
    </div>

    <!-- TABLE RDV -->
    <div class="card mt-5 p-4">
        <h4>📅 Prochains rendez-vous</h4>

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Médecin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rdvs as $r): ?>
                    <tr>
                        <td><?= $r['date_heure'] ?></td>
                        <td><?= $r['nom'] ?></td>
                        <td><?= $r['medecin'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
const ctx = document.getElementById('chart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($mois) ?>,
        datasets: [{
            label: 'Rendez-vous',
            data: <?= json_encode($totaux) ?>,
            borderWidth: 2,
            fill: true
        }]
    }
});
</script>

</body>
</html>