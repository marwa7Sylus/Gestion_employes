<?php
$host = "localhost";
$dbname = "Gestion_employes";
$username = "root";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Suppression des informations de l'employé
        $stmt = $conn->prepare("DELETE FROM employes WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "Employé supprimé avec succès !";

        // Redirection vers la page d'affichage des employés
        header("Location: consulter_profil.php");
        exit(); // Toujours arrêter l'exécution après une redirection
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Requête invalide.";
}
?>