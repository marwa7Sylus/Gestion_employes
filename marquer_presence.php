<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'Gestion_employes';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['marquer_presence'])) {
        $id_employe = $_POST['id'];
        $nom_employe = htmlspecialchars($_POST['name']);
        $date = date('Y-m-d');
        $statut_presence = 'Présent';

        $checkStmt = $conn->prepare("SELECT * FROM presences WHERE employee_id = :employee_id AND date_ = :date_");
        $checkStmt->bindParam(':employee_id', $id_employe, PDO::PARAM_INT);
        $checkStmt->bindParam(':date_', $date, PDO::PARAM_STR);
        $checkStmt->execute();

        if ($checkStmt->rowCount() == 0) {
            $insertStmt = $conn->prepare("INSERT INTO presences (employee_id, date_, statut_presence) VALUES (:employee_id, :date_, :statut_presence)");
            $insertStmt->bindParam(':employee_id', $id_employe, PDO::PARAM_INT);
            $insertStmt->bindParam(':date_', $date, PDO::PARAM_STR);
            $insertStmt->bindParam(':statut_presence', $statut_presence, PDO::PARAM_STR);
            $insertStmt->execute();
            echo "Présence marquée avec succès pour $nom_employe.<br>";
        } else {
            echo "L'employé $nom_employe est déjà marqué présent aujourd'hui.<br>";
        }
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<br><a href="voir_presences.php">Retour à la liste des employés</a>
