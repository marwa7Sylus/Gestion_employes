<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Employés</title>
    <link rel="stylesheet" href="rests.css">
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
            echo "<form action='update2.php' method='post'>
                    <input type='hidden' name='id' value='" . htmlspecialchars($result['id'] ?? '') . "'>
                    <label for='name'>Nom:</label>
                    <input type='text' id='name' name='name' value='" . htmlspecialchars($result['name'] ?? '') . "'>
                    <label for='birthday'>Date de naissance:</label>
                    <input type='date' id='birthday' name='birthday' value='" . htmlspecialchars($result['birthday'] ?? '') . "'>
                    <label for='adresse'>Adresse:</label>
                    <input type='text' id='adresse' name='adresse' value='" . htmlspecialchars($result['adresse'] ?? '') . "'>
                    <label for='number'>Numéro de téléphone:</label>
                    <input type='text' id='number' name='number' value='" . htmlspecialchars($result['number'] ?? '') . "'>
                    <label for='email'>Email:</label>
                    <input type='email' id='email' name='email' value='" . htmlspecialchars($result['email'] ?? '') . "'>
                    <input type='submit' value='Mettre à jour'>
                  </form>";
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
