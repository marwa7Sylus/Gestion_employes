<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Gestion_employes";

// Démarrer la session pour accéder à l'ID de l'utilisateur connecté
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Utilisateur non connecté.";
    exit();
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $employe_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

    // Requête pour récupérer les informations de l'employé
    $stmt = $conn->prepare("SELECT * FROM employes WHERE id = :id");
    $stmt->bindParam(':id', $employe_id, PDO::PARAM_INT);
    $stmt->execute();

    $employe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employe) {
        echo "Employé introuvable.";
        exit();
    }

    // Récupération des présences
    $stmt_presences = $conn->prepare("SELECT * FROM presences WHERE employee_id = :employee_id ORDER BY date_ DESC");
    $stmt_presences->bindParam(':employee_id', $employe_id, PDO::PARAM_INT);
    $stmt_presences->execute();
    $presences = $stmt_presences->fetchAll(PDO::FETCH_ASSOC);

    $nb_presences = count($presences);

} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
    // Journalisation de l'erreur (recommandé pour débogage)
    error_log("Erreur de connexion : " . $e->getMessage());
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="reste.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Présences de l'employé</title>

</head>
<body>
<div class="container">
    <h1>Liste des présences de <?php echo htmlspecialchars($employe['name']) . " " ; ?></h1>
    <p>Nombre total de présences : <?php echo $nb_presences; ?></p>

    <?php if (!empty($presences)): ?>
        <table>
            <thead>
            <tr>
                <th>ID Présence</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($presences as $presence): ?>
                <tr>
                    <td><?php echo htmlspecialchars($presence['presence_id']); ?></td>
                    <td><?php echo htmlspecialchars($presence['date_']); ?></td>
                    <td><?php echo htmlspecialchars($presence['statut_presence']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune présence enregistrée pour cet employé.</p>
    <?php endif; ?>
</div>
</body>
</html>
