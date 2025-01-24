<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Gestion_employes";

session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Utilisateur non connecté.";
    exit();
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération de toutes les présences
    $stmt_presences = $conn->prepare("
        SELECT p.*, e.name as employee_name
        FROM presences p
        JOIN employes e ON p.employee_id = e.id
        ORDER BY p.date_ DESC
    ");
    $stmt_presences->execute();
    $presences = $stmt_presences->fetchAll(PDO::FETCH_ASSOC);

    $nb_presences = count($presences);

    // Statistiques globales pour le tableau de bord
    $stmt_stats = $conn->prepare("
        SELECT 
            COUNT(*) as total_presences,
            COUNT(DISTINCT employee_id) as total_employees,
            SUM(CASE WHEN statut_presence = 'Present' THEN 1 ELSE 0 END) as total_present,
            SUM(CASE WHEN statut_presence = 'Absent' THEN 1 ELSE 0 END) as total_absent
        FROM presences
    ");
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

    // Données pour le graphique par mois (6 derniers mois)
    $stmt_monthly = $conn->prepare("
        SELECT 
            DATE_FORMAT(date_, '%Y-%m') as mois,
            COUNT(*) as total,
            SUM(CASE WHEN statut_presence = 'Present' THEN 1 ELSE 0 END) as presents,
            COUNT(DISTINCT employee_id) as nb_employees
        FROM presences 
        GROUP BY DATE_FORMAT(date_, '%Y-%m')
        ORDER BY mois DESC
        LIMIT 6
    ");
    $stmt_monthly->execute();
    $monthly_data = $stmt_monthly->fetchAll(PDO::FETCH_ASSOC);

    // Top 5 des employés les plus présents
    $stmt_top_employees = $conn->prepare("
        SELECT 
            e.name,
            COUNT(*) as total_presences,
            SUM(CASE WHEN p.statut_presence = 'Present' THEN 1 ELSE 0 END) as jours_present
        FROM presences p
        JOIN employes e ON p.employee_id = e.id
        GROUP BY e.id, e.name
        ORDER BY jours_present DESC
        LIMIT 5
    ");
    $stmt_top_employees->execute();
    $top_employees = $stmt_top_employees->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
    error_log("Erreur de connexion : " . $e->getMessage());
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord des présences</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="reste.css">
</head>
<body>
<div class="container">
    <h1>Tableau de bord global des présences</h1>

    <button class="btn-dashboard" onclick="toggleDashboard()">Voir les statistiques détaillées</button>

    <div id="dashboardSection">
        <div class="dashboard-cards">
            <div class="card">
                <h3>Total des employés</h3>
                <div class="number"><?php echo $stats['total_employees']; ?></div>
            </div>
            <div class="card">
                <h3>Total des présences</h3>
                <div class="number"><?php echo $stats['total_presences']; ?></div>
            </div>
            <div class="card">
                <h3>Jours présent</h3>
                <div class="number"><?php echo $stats['total_present']; ?></div>
            </div>
            <div class="card">
                <h3>Jours absent</h3>
                <div class="number"><?php echo $stats['total_absent']; ?></div>
            </div>
            <div class="card">
                <h3>Taux de présence global</h3>
                <div class="number">
                    <?php
                    $taux_presence = $stats['total_presences'] > 0
                        ? round(($stats['total_present'] / $stats['total_presences']) * 100, 1)
                        : 0;
                    echo $taux_presence . '%';
                    ?>
                </div>
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <canvas id="presenceChart"></canvas>
            </div>
            <div class="chart-card">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <div class="card top-employees">
            <h3>Top 5 des employés les plus présents</h3>
            <?php foreach ($top_employees as $employee): ?>
                <div class="employee-stats">
                    <span><?php echo htmlspecialchars($employee['name']); ?></span>
                    <span><?php echo $employee['jours_present']; ?> jours présent</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!empty($presences)): ?>
        <table>
            <thead>
            <tr>
                <th>ID Présence</th>
                <th>Employé</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>

            </tr>
            </thead>
            <tbody>
            <?php foreach ($presences as $presence): ?>
                <tr>
                    <td><?php echo htmlspecialchars($presence['presence_id']); ?></td>
                    <td><?php echo htmlspecialchars($presence['employee_name']); ?></td>
                    <td><?php echo htmlspecialchars($presence['date_']); ?></td>
                    <td><?php echo htmlspecialchars($presence['statut_presence']); ?></td>
                    <td>
                        <a href="marquer_presence.php?employee_id=<?php echo $presence['employee_id']; ?>" class="btn-dashboard">
                            Marquer présence
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune présence enregistrée.</p>
    <?php endif; ?>
</div>

<script>
    function toggleDashboard() {
        const dashboard = document.getElementById('dashboardSection');
        if (dashboard.style.display === 'none' || dashboard.style.display === '') {
            dashboard.style.display = 'block';
            initializeCharts();
        } else {
            dashboard.style.display = 'none';
        }
    }

    function initializeCharts() {
        // Graphique circulaire des présences/absences
        const presenceCtx = document.getElementById('presenceChart').getContext('2d');
        new Chart(presenceCtx, {
            type: 'pie',
            data: {
                labels: ['Présent', 'Absent'],
                datasets: [{
                    data: [<?php echo $stats['total_present']; ?>, <?php echo $stats['total_absent']; ?>]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Répartition globale des présences'
                    }
                }
            }
        });

        // Graphique des présences mensuelles
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) {
                    return "'" . $item['mois'] . "'";
                }, array_reverse($monthly_data))); ?>],
                datasets: [{
                    label: 'Total des présences',
                    data: [<?php echo implode(',', array_map(function($item) {
                        return $item['total'];
                    }, array_reverse($monthly_data))); ?>],

                    yAxisID: 'y'
                }, {
                    label: 'Nombre d\'employés',
                    data: [<?php echo implode(',', array_map(function($item) {
                        return $item['nb_employees'];
                    }, array_reverse($monthly_data))); ?>],

                    type: 'line',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nombre de présences'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Nombre d\'employés'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution mensuelle des présences'
                    }
                }
            }
        });
    }
</script>
</body>
</html>