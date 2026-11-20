```php
<?php

require_once "config/database.php";
require_once "includes/header.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$requete = $pdo->prepare(
    "SELECT * FROM products WHERE id = ? AND active = 1"
);

$requete->execute([$id ?: 0]);

$produit = $requete->fetch();

if (!$produit) {
    echo "<div class='container'>
            <h2>Produit introuvable.</h2>
            <a href='boutique.php' class='btn'>Retour à la boutique</a>
          </div>";

    require_once "includes/footer.php";
    exit;
}

?>

<div class="container">

    <div class="product-details">

        <div class="product-details-image">

            <?php if (!empty($produit['image'])): ?>

                <img
                    src="assets/images/<?= htmlspecialchars($produit['image']) ?>"
                    alt="<?= htmlspecialchars($produit['name']) ?>"
                >

            <?php else: ?>

                <p>Image non disponible</p>

            <?php endif; ?>

        </div>

        <div class="product-details-info">

            <h1><?= htmlspecialchars($produit['name']) ?></h1>

            <p class="category">
                <?= htmlspecialchars($produit['category']) ?>
            </p>

            <p>
                <?= htmlspecialchars($produit['description']) ?>
            </p>

            <p class="price">
                <?= number_format($produit['price'], 0, ',', ' ') ?> FCFA
            </p>

            <p class="stock">
                Stock disponible : <?= (int)$produit['stock'] ?>
            </p>

            <?php if ((int)$produit['stock'] > 0): ?>

                <form action="panier.php" method="POST">

                    <input type="hidden" name="id"
                           value="<?= (int)$produit['id'] ?>">

                    <label for="quantite">Quantité :</label>

                    <input
                        type="number"
                        id="quantite"
                        name="quantite"
                        value="1"
                        min="1"
                        max="<?= (int)$produit['stock'] ?>"
                        required
                    >

                    <button class="btn" type="submit" name="ajouter" value="1">
                     Ajouter au panier
                    </button>

                </form>

            <?php else: ?>

                <p class="out-of-stock">Produit indisponible</p>

            <?php endif; ?>

            <br>

            <a href="boutique.php" class="back-link">
                ← Retour à la boutique
            </a>

        </div>

    </div>

</div>

<?php require_once "includes/footer.php"; ?>
```