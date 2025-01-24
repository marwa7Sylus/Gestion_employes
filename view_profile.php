<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reste.css">
    <title>Profil de l'employé</title>
</head>
<body>
<div class="content">
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Gestion_employes";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Préparer la requête pour récupérer les informations de l'employé
            $stmt = $conn->prepare("SELECT id, name, birthday, adresse, number, email, position, department, hire_date FROM employes WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $employe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($employe) {
                echo "<h1>Profil de l'employé</h1>";
                echo "<p><strong>ID :</strong> " . htmlspecialchars($employe["id"]) . "</p>";
                echo "<p><strong>Nom :</strong> " . htmlspecialchars($employe["name"]) . "</p>";
                echo "<p><strong>Date de naissance :</strong> " . htmlspecialchars($employe["birthday"]) . "</p>";
                echo "<p><strong>Adresse :</strong> " . htmlspecialchars($employe["adresse"]) . "</p>";
                echo "<p><strong>Numéro de téléphone :</strong> " . htmlspecialchars($employe["number"]) . "</p>";
                echo "<p><strong>Email :</strong> " . htmlspecialchars($employe["email"]) . "</p>";
                echo "<p><strong>Poste :</strong> " . htmlspecialchars($employe["position"]) . "</p>";
                echo "<p><strong>Département :</strong> " . htmlspecialchars($employe["department"]) . "</p>";
                echo "<p><strong>Date d'embauche :</strong> " . htmlspecialchars($employe["hire_date"]) . "</p>";
            } else {
                echo "<p>Aucun employé trouvé avec cet ID.</p>";
            }
        } catch(PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }
        $conn = null;
    } else {
        echo "<p>ID non spécifié.</p>";
    }
    ?>
</div>
</body>
</html>