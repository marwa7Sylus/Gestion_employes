<!DOCTYPE html>
<html lang="kr">
<head>
    <link rel="stylesheet" href="PRJ.css">
    <meta charset="UTF-8">
    <title>Login Page</title>
</head>
<body>
<div class="content">
    <h1>Login Page</h1> <!-- Titre du formulaire -->
    <br>
    <form method="post" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
        <label for="role">Role</label>
        <input type="radio" id="admin" name="role" value="admin" required />
        <label for="admin">Admin</label><br>
        <input type="radio" id="user" name="role" value="user" required />
        <label for="user">User</label><br>
        <input type="submit" name="login" value="Login" />
    </form>

    <?php
    session_start(); // Démarre la session pour l'utilisateur connecté

    $host = "localhost";
    $dbname = "gestion_employes";
    $dbuser = "root";
    $dbpass = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
        $user = trim($_POST['username']);
        $pass = trim($_POST['password']);
        $error = "";

        if (empty($user) || empty($pass)) {
            $error = "All fields are required.";
        }

        if (!$error) {
            try {
                // Requête pour vérifier l'utilisateur avec mot de passe en texte clair
                $stmt = $pdo->prepare("SELECT id, name, password, role FROM employes WHERE name = ? AND password = ?");
                $stmt->execute([$user, $pass]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($userData) {
                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['role'] = $userData['role'];

                    // Redirige l'utilisateur vers la page appropriée
                    if ($userData['role'] === "admin") {
                        header("Location: page_acceuil.php");
                    } else {
                        header("Location: page_acceuil_user.php?id=" . $userData['id']);
                    }
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }

        if ($error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
    ?>
</div>
</body>
</html>
