<?php
require_once "auth.php";
require_once "../config/database.php";

$message = "";

$message = "";

// Modification du statut
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['modifier_statut'])) {

    $id = (int)($_POST['id'] ?? 0);
    $statut = $_POST['status'] ?? '';

    $statutsAutorises = [
        'en_attente',
        'confirmee',
        'expediee',
        'livree',
        'annulee'
    ];

    if ($id > 0 && in_array($statut, $statutsAutorises, true)) {

        $requete = $pdo->prepare(
            "UPDATE orders SET status = ? WHERE id = ?"
        );

        $requete->execute([$statut, $id]);

        $message = "Statut de la commande mis à jour.";
    }
}

// Récupération des commandes
$requete = $pdo->query(
    "SELECT * FROM orders ORDER BY created_at DESC"
);

$commandes = $requete->fetchAll();

require_once "../includes/header.php";

?>

<style>
.orders-container {
    width: 90%;
    max-width: 1200px;
    margin: 40px auto;
}

.order-card {
    background: white;
    padding: 25px;
    margin-bottom: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.07);
}

.order-card h2 {
    color: #1769aa;
}

.order-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.order-details p {
    margin: 5px 0;
}

.status-form {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
    margin-top: 20px;
}

.status-form select {
    max-width: 250px;
}

@media(max-width: 600px) {
    .order-details {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="orders-container">

    <h1>Gestion des commandes</h1>

    <p>
        Consultez les commandes et mettez à jour leur statut.
    </p>

    <?php if ($message !== ""): ?>
        <p style="color:green;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <?php if (empty($commandes)): ?>

        <div class="order-card">
            <h2>Aucune commande</h2>
            <p>Les commandes des clients apparaîtront ici.</p>
        </div>

    <?php else: ?>

        <?php foreach ($commandes as $commande): ?>

            <div class="order-card">

                <h2>
                    Commande #<?= (int)$commande['id'] ?>
                </h2>

                <div class="order-details">

                    <p>
                        <strong>Client :</strong>
                        <?= htmlspecialchars($commande['customer_name']) ?>
                    </p>

                    <p>
                        <strong>Email :</strong>
                        <?= htmlspecialchars($commande['email']) ?>
                    </p>

                    <p>
                        <strong>Téléphone :</strong>
                        <?= htmlspecialchars($commande['phone']) ?>
                    </p>

                    <p>
                        <strong>Adresse :</strong>
                        <?= htmlspecialchars($commande['address']) ?>
                    </p>

                    <p>
                        <strong>Total :</strong>
                        <?= number_format($commande['total'], 0, ',', ' ') ?> FCFA
                    </p>

                    <p>
                        <strong>Paiement :</strong>
                        <?= htmlspecialchars($commande['payment_method']) ?>
                    </p>

                    <p>
                        <strong>Date :</strong>
                        <?= htmlspecialchars($commande['created_at']) ?>
                    </p>

                    <p>
                        <strong>Statut actuel :</strong>
                        <?= htmlspecialchars($commande['status']) ?>
                    </p>

                </div>

                <!-- Détail des produits commandés -->

<h3 style="margin-top: 25px; color: #10243a;">
    Produits commandés
</h3>

<?php

$requeteProduits = $pdo->prepare(
    "SELECT product_name, unit_price, quantity
     FROM order_items
     WHERE order_id = ?"
);

$requeteProduits->execute([$commande['id']]);

$articles = $requeteProduits->fetchAll();

?>

<div style="overflow-x: auto; margin-top: 15px;">

    <table>

        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Sous-total</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($articles as $article): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($article['product_name']) ?>
                    </td>

                    <td>
                        <?= number_format($article['unit_price'], 0, ',', ' ') ?> FCFA
                    </td>

                    <td>
                        <?= (int)$article['quantity'] ?>
                    </td>

                    <td>
                        <?= number_format(
                            $article['unit_price'] * $article['quantity'],
                            0,
                            ',',
                            ' '
                        ) ?> FCFA
                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <?php if (empty($articles)): ?>
        <p>Aucun produit associé à cette commande.</p>
    <?php endif; ?>

</div>

                <form method="POST" class="status-form">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int)$commande['id'] ?>"
                    >

                    <select name="status" required>

                        <option value="en_attente"
                            <?= $commande['status'] === 'en_attente' ? 'selected' : '' ?>>
                            En attente
                        </option>

                        <option value="confirmee"
                            <?= $commande['status'] === 'confirmee' ? 'selected' : '' ?>>
                            Confirmée
                        </option>

                        <option value="expediee"
                            <?= $commande['status'] === 'expediee' ? 'selected' : '' ?>>
                            Expédiée
                        </option>

                        <option value="livree"
                            <?= $commande['status'] === 'livree' ? 'selected' : '' ?>>
                            Livrée
                        </option>

                        <option value="annulee"
                            <?= $commande['status'] === 'annulee' ? 'selected' : '' ?>>
                            Annulée
                        </option>

                    </select>

                    <button
                        type="submit"
                        name="modifier_statut"
                        class="btn"
                    >
                        Modifier le statut
                    </button>

                </form>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

    <a href="dashboard.php" class="btn">
        Retour au tableau de bord
    </a>

</div>

<?php require_once "../includes/footer.php"; ?>