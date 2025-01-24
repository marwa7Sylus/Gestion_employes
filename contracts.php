<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Gestion_employes";

// Utilisez l'ID de l'utilisateur stocké dans la session
$user_id = $_SESSION["user_id"] ?? NULL;

if (!$user_id) {
    echo "Utilisateur non connecté. Veuillez vous connecter.";
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Management</title>
    <link rel="stylesheet" href="rests.css">
    <style>
        body {
            color: white;
            background-color: black; /* Optionnel : pour un meilleur contraste */
        }
    </style>
</head>
<body>
<?php
try {
// Créer une connexion PDO
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Préparer et exécuter la requête
$stmt = $conn->prepare("SELECT employee_id, contract_type, start_date, end_date, details FROM contracts WHERE employee_id = :employee_id");
$stmt->bindParam(':employee_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
?>
<div class="content">
    <h2>Display Contract</h2>
    <?php
    // Afficher les résultats
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "Employee ID: " . htmlspecialchars($row['employee_id']) . "<br>";
        echo "Contract Type: " . htmlspecialchars($row['contract_type']) . "<br>";
        echo "Start Date: " . htmlspecialchars($row['start_date']) . "<br>";
        echo "End Date: " . htmlspecialchars($row['end_date']) . "<br>";
        echo "Details: " . htmlspecialchars($row['details']) . "<br><br>";
    } else {
        echo "No contract found for the employee.";
    }
    } catch(PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }

    // Fermer la connexion
    $conn = null;
    ?></div>
</body>
</html>
