<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table des Employés</title>
    <link rel="stylesheet" href="reste.css">
</head>
<body>
<h1>Table des Employés</h1>
<?php
$host = "localhost";
$dbname = "Gestion_employes";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la table des employés si elle n'existe pas
    $conn->exec("CREATE TABLE IF NOT EXISTS employes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        )");

    // Récupérer la liste des employés
    $stmt = $conn->query("SELECT id, name FROM employes");
    $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($employes) > 0) {
        echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Nom de l'Employé</th>
                        <th>Actions</th>
                    </tr>";
        foreach ($employes as $employe) {
            echo "<tr>
                        <td>" . htmlspecialchars($employe['id']) . "</td>
                        <td>" . htmlspecialchars($employe['name']) . "</td>
                        <td>
                            <form action='view_profile.php' method='get'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($employe['id']) . "'>
                                <button type='submit'>Consulter Profil</button>
                            </form>
                            <form action='delete_empl.php' method='post'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($employe['id']) . "'>
                                <button type='submit'>Supprimer</button>
                            </form>
                        </td>
                      </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Aucun employé trouvé dans la base de données.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Erreur : " . $e->getMessage() . "</p>";
}
?>
</body>
</html>