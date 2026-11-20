<?php

session_start();

require_once "config/database.php";

// Initialiser le panier
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

$message = "";

// TRAITEMENT DES ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // AJOUTER UN PRODUIT
    if (isset($_POST['ajouter'])) {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $quantite = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);

        if ($id && $quantite && $quantite > 0) {

            $requete = $pdo->prepare(
                "SELECT id, stock FROM products WHERE id = ? AND active = 1"
            );

            $requete->execute([$id]);
            $produit = $requete->fetch();

            if ($produit) {

                $quantiteActuelle = $_SESSION['panier'][$id] ?? 0;
                $nouvelleQuantite = $quantiteActuelle + $quantite;

                if ($nouvelleQuantite <= (int)$produit['stock']) {

                    $_SESSION['panier'][$id] = $nouvelleQuantite;
                    $message = "Produit ajouté au panier avec succès !";

                } else {

                    $message = "Stock insuffisant ! Stock disponible : "
                             . (int)$produit['stock'];

                }

            } else {

                $message = "Produit introuvable.";

            }

        } else {

            $message = "Quantité invalide.";

        }
    }

    // MODIFIER LA QUANTITE
if (isset($_POST['modifier'])) {

    $id = (int)($_POST['modifier']);
    $quantite = filter_input(INPUT_POST, 'quantite', FILTER_VALIDATE_INT);

    if ($id > 0 && $quantite !== false && $quantite !== null && $quantite > 0) {

        $requete = $pdo->prepare(
            "SELECT stock FROM products WHERE id = ? AND active = 1"
        );

        $requete->execute([$id]);
        $produit = $requete->fetch();

        if ($produit && $quantite <= (int)$produit['stock']) {

            $_SESSION['panier'][$id] = $quantite;
            $message = "Quantité modifiée avec succès !";

        } else {

            $message = "Quantité supérieure au stock disponible.";

        }

    } else {

        $message = "Quantité invalide.";

    }
}

    // SUPPRIMER UN PRODUIT
    if (isset($_POST['supprimer'])) {

        $id = (int)$_POST['supprimer'];

        unset($_SESSION['panier'][$id]);

        $message = "Produit supprimé du panier.";

    }
}

require_once "includes/header.php";

?>

<div class="container">

    <h1>Mon panier 🛒</h1>

    <?php if ($message): ?>

        <div class="cart-message">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>

    <?php

    $total = 0;

    if (empty($_SESSION['panier'])):

    ?>

        <div class="empty-cart">

            <h2>Votre panier est vide.</h2>

            <p>Découvrez nos produits électroniques.</p>

            <a href="boutique.php" class="btn">
                Découvrir la boutique
            </a>

        </div>

    <?php else: ?>

        <div class="cart-products">

            <?php foreach ($_SESSION['panier'] as $id => $quantite):

                $requete = $pdo->prepare(
                    "SELECT * FROM products WHERE id = ? AND active = 1"
                );

                $requete->execute([(int)$id]);
                $produit = $requete->fetch();

                if (!$produit) {
                    unset($_SESSION['panier'][$id]);
                    continue;
                }

                $sous_total = $produit['price'] * $quantite;

                $total += $sous_total;

            ?>

                <div class="cart-product">

                    <?php if (!empty($produit['image'])): ?>

                        <img
                            src="assets/images/<?= htmlspecialchars($produit['image']) ?>"
                            alt="<?= htmlspecialchars($produit['name']) ?>"
                        >

                    <?php endif; ?>

                    <div class="cart-product-info">

                        <h3>
                            <?= htmlspecialchars($produit['name']) ?>
                        </h3>

                        <p>
                            Prix unitaire :
                            <?= number_format($produit['price'], 0, ',', ' ') ?>
                            FCFA
                        </p>

                        <form method="POST" class="quantity-form">

    <label>Quantité :</label>

    <input
        type="number"
        name="quantite"
        value="<?= (int)$quantite ?>"
        min="1"
        max="<?= (int)$produit['stock'] ?>"
        required
    >

    <button
        type="submit"
        name="modifier"
        value="<?= (int)$id ?>"
        class="btn"
    >
        Modifier
    </button>

</form>

                        <p class="price">
                            Sous-total :
                            <?= number_format($sous_total, 0, ',', ' ') ?>
                            FCFA
                        </p>

                        <form method="POST">

                            <button
                                type="submit"
                                name="supprimer"
                                value="<?= (int)$id ?>"
                                class="btn btn-delete"
                            >
                                Supprimer
                            </button>

                        </form>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="cart-total">

            <h2>
                Total :
                <?= number_format($total, 0, ',', ' ') ?> FCFA
            </h2>

            <a href="boutique.php" class="btn">
                Continuer mes achats
            </a>

            <a href="commande.php" class="btn">
                Passer commande
            </a>

        </div>

    <?php endif; ?>

</div>

<?php require_once "includes/footer.php"; ?>