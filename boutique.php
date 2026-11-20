<?php
require_once "config/database.php";
require_once "includes/header.php";

$requete = $pdo->query(
    "SELECT * FROM products WHERE active = 1 ORDER BY id DESC LIMIT 6"
);

$produits = $requete->fetchAll();
?>

<section class="hero">
    <h1>Bienvenue chez JAZZ ÉLECTRONIQUE</h1>

    <p>
        Découvrez les meilleurs smartphones, ordinateurs
        et accessoires électroniques au meilleur prix.
    </p>

    <a href="boutique.php" class="btn">
        Découvrir la boutique
    </a>
</section>

<section class="home-products">

    <h2>Nos produits populaires</h2>

    <div class="products">

        <?php foreach ($produits as $produit): ?>

            <div class="product">

                <?php if (!empty($produit['image'])): ?>
                    <img
                        src="assets/images/<?= htmlspecialchars($produit['image']) ?>"
                        alt="<?= htmlspecialchars($produit['name']) ?>"
                        class="product-image"
                    >
                <?php endif; ?>

                <h3>
                    <?= htmlspecialchars($produit['name']) ?>
                </h3>

                <p>
                    <?= htmlspecialchars($produit['category']) ?>
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

    <div class="home-button">
        <a href="boutique.php" class="btn">
            Voir tous les produits
        </a>
    </div>

</section>

<?php require_once "includes/footer.php"; ?>