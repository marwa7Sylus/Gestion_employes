<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Employés</title>
    <link rel="stylesheet" href="reste.css">
</head>
<body>
<?php
session_start();

// Utilisez l'ID de l'utilisateur stocké dans la session
$user_id = $_SESSION["user_id"] ?? NULL;

if ($user_id) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Gestion_employes";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT id, name, birthday, adresse, number, email FROM employes WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo "<div class='content'>";
            echo "<h1>Informations personnelles</h1>";
            echo "<table>
                    <tr><th>ID</th><th>Nom</th><th>Date de naissance</th><th>Adresse</th><th>Numéro de téléphone</th><th>Email</th></tr>";
            echo "<tr>
                    <td>" . htmlspecialchars($result["id"] ?? '') . "</td>
                    <td>" . htmlspecialchars($result["name"] ?? '') . "</td>
                    <td>" . htmlspecialchars($result["birthday"] ?? '') . "</td>
                    <td>" . htmlspecialchars($result["adresse"] ?? '') . "</td>
                    <td>" . htmlspecialchars($result["number"] ?? '') . "</td>
                    <td>" . htmlspecialchars($result["email"] ?? '') . "</td>
                     <td><a class='button' href='modifier_infos_personn.php" . "'>modifier</a></td>
                    
                  </tr>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p>Aucune information trouvée pour l'utilisateur connecté.</p>";
        }
    } catch(PDOException $e) {
        echo "<p>Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    $conn = null;
} else {
    echo "<p>Utilisateur non connecté. Veuillez vous connecter.</p>";
}
?>
</body>
</html>
