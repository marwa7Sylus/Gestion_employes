<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Salaire</title>
    <link rel="stylesheet" href="reste.css">
</head>
<body>
<h1>Salaire de l'Employé</h1>

<?php
// Connexion à la base de données
$host = "localhost";
$dbname = "Gestion_employes";
$username = "root";
$password = "";

// Vérification si l'ID de l'employé est passé par la méthode GET
if (isset($_GET['id'])) {
    $employe_id = $_GET['id'];

    try {
        // Connexion à la base de données
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer le salaire de l'employé
        $stmt = $conn->prepare("SELECT e.name, s.montant AS salary, s.date_paiement AS effective_date
                                FROM salaires s 
                                JOIN employés e ON s.employé_id = e.id
                                WHERE e.id = :id");
        $stmt->bindParam(':id', $employe_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Afficher le salaire de l'employé
        if ($result) {
            echo "<h2>Informations sur le salaire</h2>";
            echo "<table border='1'>
                    <tr>
                        <th>Nom</th>
                        <th>Montant du Salaire</th>
                        <th>Date de Paiement</th>
                    </tr>
                    <tr>
                        <td>" . htmlspecialchars($result['name']) . "</td>
                        <td>" . htmlspecialchars($result['salary']) . " €</td>
                        <td>" . htmlspecialchars($result['effective_date']) . "</td>
                    </tr>
                  </table>";
        } else {
            echo "<p>Aucun salaire trouvé pour cet employé.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>ID employé non fourni. Impossible d'afficher le salaire.</p>";
}
?>

</body>
</html>
