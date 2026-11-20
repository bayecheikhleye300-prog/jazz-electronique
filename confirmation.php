<?php

session_start();

require_once "includes/header.php";

$commande_id = $_SESSION['commande_id'] ?? null;

?>

<h1>Merci pour votre commande !</h1>

<?php if ($commande_id): ?>

    <p>
        Votre commande a bien été enregistrée.
    </p>

    <p>
        Numéro de commande :
        <strong>#<?= (int)$commande_id ?></strong>
    </p>

    <p>
        Notre équipe vous contactera pour confirmer
        votre commande et les modalités de paiement.
    </p>

<?php else: ?>

    <p>Aucune commande récente à afficher.</p>

<?php endif; ?>

<a href="boutique.php" class="btn">
    Retour à la boutique
</a>

<?php require_once "includes/footer.php"; ?>