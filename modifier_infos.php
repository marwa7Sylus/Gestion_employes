<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="rests.css">
</head>
<body>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Gestion_employes";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT id, name, birthday, adresse, number, email, position, department, hire_date FROM employes WHERE id = :id");
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "<h1>Modifier les informations</h1>";
        echo "<form action='update.php' method='post'>
                <input type='hidden' name='id' value='" . $result['id'] . "'>
                <label for='name'>Nom:</label>
                <input type='text' id='name' name='name' value='" . $result['name'] . "'>
                <label for='birthday'>Date de naissance:</label>
                <input type='date' id='birthday' name='birthday' value='" . $result['birthday'] . "'>
                <label for='adresse'>Adresse:</label>
                <input type='text' id='adresse' name='adresse' value='" . $result['adresse'] . "'>
                <label for='number'>Numéro de téléphone:</label>
                <input type='text' id='number' name='number' value='" . $result['number'] . "'>
                <label for='email'>Email:</label>
                <input type='email' id='email' name='email' value='" . $result['email'] . "'>
                <label for='position'>Poste:</label>
                <select id='position' name='position'>
                    <option value='Poste 1'" . ($result['position'] == 'Poste 1' ? ' selected' : '') . ">Poste 1</option>
                    <option value='Poste 2'" . ($result['position'] == 'Poste 2' ? ' selected' : '') . ">Poste 2</option>
                    <option value='Poste 3'" . ($result['position'] == 'Poste 3' ? ' selected' : '') . ">Poste 3</option>
                </select>
                <label for='department'>Département:</label>
                <select id='department' name='department'>
                    <option value='Département 1'" . ($result['department'] == 'Département 1' ? ' selected' : '') . ">Département 1</option>
                    <option value='Département 2'" . ($result['department'] == 'Département 2' ? ' selected' : '') . ">Département 2</option>
                    <option value='Département 3'" . ($result['department'] == 'Département 3' ? ' selected' : '') . ">Département 3</option>
                </select>
                <label for='hire_date'>Date d'embauche:</label>
                <input type='date' id='hire_date' name='hire_date' value='" . $result['hire_date'] . "'>
                <input type='submit' value='Mettre à jour'>
              </form>";
    } else {
        echo "Aucun résultat trouvé.";
    }
} catch(PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}

$conn = null;
?>
</body>
</html>