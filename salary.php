<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Gestion_employes";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT e.name, s.salary_amount, s.payment_date FROM salaries s JOIN employes e ON s.employee_id = e.id WHERE s.id = :id");
    $stmt->bindParam(':id', $salary_id); $salary_id = 1;
   $stmt->execute(); $result = $stmt->fetch(PDO::FETCH_ASSOC);
   if ($result) { echo "<h1>Bulletin de Paie</h1>";
       echo "<p>Nom: " . $result['name'] . "</p>";
       echo "<p>Montant du Salaire: " . $result['salary_amount'] . " €</p>";
       echo "<p>Date de Paiement: " . $result['payment_date'] . "</p>";
   }
   else { echo "Aucun résultat trouvé.";
   } } catch(PDOException $e) { echo "Erreur: " . $e->getMessage(); } $conn = null; ?>