<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Gestion_employes";

// Utilisez l'ID de l'utilisateur stocké dans la session
$user_id = $_SESSION["user_id"] ?? NULL;

if ($user_id) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" href="reste.css">
        <title>Employee Management</title>
        <style>
            body {
                color: white;
                background-color: black; /* Optionnel : pour un meilleur contraste */
            }
        </style>
    </head>
    <body>
    <h2>Display Salary</h2>
    <?php
    try {
        // Créer une connexion PDO
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Préparer et exécuter la requête pour le salaire
        $stmt = $conn->prepare("SELECT salary, effective_date FROM salaries WHERE employee_id = :employee_id ORDER BY effective_date DESC LIMIT 1");
        $stmt->bindParam(':employee_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Afficher les résultats du salaire sous forme de tableau
        echo "<table border='1'>
                <tr>
                    <th>Salary</th>
                    <th>Effective Date</th>
                </tr>";
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['salary']) . "</td>
                    <td>" . htmlspecialchars($row['effective_date']) . "</td>
                  </tr>";
        } else {
            echo "<tr><td colspan='2'>No salary information found for the employee.</td></tr>";
        }
        echo "</table>";
    } catch(PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }

    // Fermer la connexion
    $conn = null;
    ?>
    </body>
    </html>
    <?php
} else {
    echo "Utilisateur non connecté. Veuillez vous connecter.";
}
?>
