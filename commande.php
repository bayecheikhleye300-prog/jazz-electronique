<?php

session_start();

require_once "config/database.php";

if (empty($_SESSION['panier'])) {
    header("Location: panier.php");
    exit;
}

$message = "";

// Protection CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$nom = "";
$email = "";
$telephone = "";
$adresse = "";
$paiement = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $paiement = $_POST['paiement'] ?? '';

    // Vérification du formulaire
    if (!hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token'] ?? ''
    )) {

        $message = "Formulaire invalide. Veuillez réessayer.";

    } elseif (
        $nom === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $telephone === "" ||
        $adresse === "" ||
        !in_array($paiement, ['wave', 'orange_money', 'livraison'], true)
    ) {

        $message = "Veuillez remplir correctement tous les champs.";

    } else {

        try {

            $pdo->beginTransaction();

            $produits = [];
            $total = 0;

            // Vérification des produits et du stock
            foreach ($_SESSION['panier'] as $id => $quantite) {

                $id = (int)$id;
                $quantite = filter_var(
                    $quantite,
                    FILTER_VALIDATE_INT
                );

                if ($id <= 0 || $quantite === false ||
                    $quantite < 1 || $quantite > 20) {
                    throw new Exception("Quantité invalide.");
                }

                $requete = $pdo->prepare(
                    "SELECT * FROM products
                     WHERE id = ? AND active = 1
                     FOR UPDATE"
                );

                $requete->execute([$id]);

                $produit = $requete->fetch(PDO::FETCH_ASSOC);

                if (!$produit) {
                    throw new Exception("Produit introuvable.");
                }

                if ($quantite > (int)$produit['stock']) {
                    throw new Exception(
                        "Stock insuffisant pour : " . $produit['name']
                    );
                }

                $produit['quantite'] = $quantite;

                $produits[] = $produit;

                $total += (float)$produit['price'] * $quantite;
            }

            // Enregistrement de la commande
            $requete = $pdo->prepare(
                "INSERT INTO orders
                (customer_name, email, phone, address, total,
                 payment_method, status)
                VALUES (?, ?, ?, ?, ?, ?, 'en_attente')"
            );

            $mode = $paiement === 'livraison'
                ? 'cash'
                : $paiement;

            $requete->execute([
                $nom,
                $email,
                $telephone,
                $adresse,
                $total,
                $mode
            ]);

            $commande_id = $pdo->lastInsertId();

            // Enregistrement des articles et mise à jour du stock
            foreach ($produits as $produit) {

                $requete = $pdo->prepare(
                    "INSERT INTO order_items
                    (order_id, product_id, product_name,
                     unit_price, quantity)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $requete->execute([
                    $commande_id,
                    $produit['id'],
                    $produit['name'],
                    $produit['price'],
                    $produit['quantite']
                ]);

                $stock = $pdo->prepare(
                    "UPDATE products
                     SET stock = stock - ?
                     WHERE id = ? AND stock >= ?"
                );

                $stock->execute([
                    $produit['quantite'],
                    $produit['id'],
                    $produit['quantite']
                ]);

                if ($stock->rowCount() !== 1) {
                    throw new Exception("Erreur de mise à jour du stock.");
                }
            }

            // Validation définitive
            $pdo->commit();

            unset($_SESSION['panier']);

            $_SESSION['commande_id'] = $commande_id;

            header("Location: confirmation.php");
            exit;

        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = "Impossible de valider la commande : "
                . htmlspecialchars($e->getMessage());
        }
    }
}

require_once "includes/header.php";

?>

<div class="container">

    <h1>Finaliser ma commande</h1>

    <?php if ($message !== ""): ?>

        <p style="color:red; font-weight:bold;">
            <?= $message ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >

        <label>Nom complet</label>

        <input
            type="text"
            name="nom"
            value="<?= htmlspecialchars($nom) ?>"
            required
        >

        <label>Adresse email</label>

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($email) ?>"
            required
        >

        <label>Numéro de téléphone</label>

        <input
            type="tel"
            name="telephone"
            value="<?= htmlspecialchars($telephone) ?>"
            required
        >

        <label>Adresse de livraison</label>

        <textarea name="adresse" required><?= htmlspecialchars($adresse) ?></textarea>

        <label>Mode de paiement</label>

        <select name="paiement" required>

            <option value="">Choisir un moyen de paiement</option>

            <option value="wave"
                <?= $paiement === 'wave' ? 'selected' : '' ?>>
                Wave
            </option>

            <option value="orange_money"
                <?= $paiement === 'orange_money' ? 'selected' : '' ?>>
                Orange Money
            </option>

            <option value="livraison"
                <?= $paiement === 'livraison' ? 'selected' : '' ?>>
                Paiement à la livraison
            </option>

        </select>

        <br><br>

        <button type="submit" class="btn">
            Confirmer ma commande
        </button>

    </form>

</div>

<?php require_once "includes/footer.php"; ?>