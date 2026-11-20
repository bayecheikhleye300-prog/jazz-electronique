<?php

session_start();

require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $requete = $pdo->prepare(
        "SELECT * FROM admins WHERE email = ?"
    );

    $requete->execute([$email]);

    $admin = $requete->fetch();

    if ($admin && password_verify($password, $admin["password_hash"])) {

        session_regenerate_id(true);

        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_email"] = $admin["email"];

        header("Location: dashboard.php");
        exit;

    } else {

        $message = "Email ou mot de passe incorrect.";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion administrateur</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="container">

    <h1>Administration JAZZ ÉLECTRONIQUE</h1>

    <h2>Connexion</h2>

    <?php if ($message !== ""): ?>
        <p style="color:red;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Adresse email</label>
        <input type="email" name="email" required>

        <label>Mot de passe</label>
        <input type="password" name="password" required>

        <br><br>

        <button type="submit" class="btn">
            Se connecter
        </button>

    </form>

</div>

</body>
</html>