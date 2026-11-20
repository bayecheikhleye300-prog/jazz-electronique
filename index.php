<?php

require_once "config/database.php";

require_once "includes/header.php";

?>

<section class="hero">

    <h1>Bienvenue chez JAZZ ÉLECTRONIQUE</h1>

    <p>
        Découvrez les meilleurs produits électroniques
        au meilleur prix.
    </p>

    <a href="boutique.php" class="btn">
        Découvrir la boutique
    </a>

</section>

<h2>Nos produits</h2>

<div class="products">

<?php

$requete = $pdo->query(
    "SELECT * FROM products WHERE active = 1 LIMIT 6"
);

$produits = $requete->fetchAll();

foreach($produits as $produit):

?>

<div class="product">

    <?php if (!empty($produit['image'])): ?>
        <img
            src="assets/images/<?= htmlspecialchars($produit['image']) ?>"
            alt="<?= htmlspecialchars($produit['name']) ?>"
        >
    <?php endif; ?>

    <h3>
        <?= htmlspecialchars($produit['name']) ?>
    </h3>

    <p>
        <?= htmlspecialchars($produit['description']) ?>
    </p>

    <p class="price">
        <?= number_format($produit['price'], 0, ',', ' ') ?> FCFA
    </p>

    <a class="btn"
       href="produit.php?id=<?= (int)$produit['id'] ?>">
       Voir le produit
    </a>

</div>

<?php endforeach; ?>

</div>

<?php require_once "includes/footer.php"; ?>