<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Gestion_employes";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("UPDATE employes SET name = :name, birthday = :birthday, adresse = :adresse, number = :number, email = :email WHERE id = :id");
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->bindParam(':name', $_POST['name']);
    $stmt->bindParam(':birthday', $_POST['birthday']);
    $stmt->bindParam(':adresse', $_POST['adresse']);
    $stmt->bindParam(':number', $_POST['number']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->execute();

    // Redirection vers la page view_profile
    header("Location:infos_perso?id=" . $_POST['id']);
    exit();
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

$conn = null;
?>