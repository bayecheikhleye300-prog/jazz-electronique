<?php

require_once "auth.php";
require_once "../config/database.php";

// Statistiques
// Statistiques
$totalProduits = $pdo->query(
    "SELECT COUNT(*) FROM products"
)->fetchColumn();

$totalCommandes = $pdo->query(
    "SELECT COUNT(*) FROM orders"
)->fetchColumn();

$chiffreAffaires = $pdo->query(
    "SELECT COALESCE(SUM(total), 0) FROM orders"
)->fetchColumn();

// Dernières commandes
$commandes = $pdo->query(
    "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administration - JAZZ ÉLECTRONIQUE</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .admin-container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin: 30px 0;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            text-align: center;
        }

        .stat-card h3 {
            color: #1769aa;
            font-size: 28px;
        }

        .admin-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 30px 0;
        }

        .table-container {
            overflow-x: auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
        }

        @media(max-width: 768px) {
            .admin-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="admin-container">

    <h1>Administration JAZZ ÉLECTRONIQUE</h1>

    <p>Bienvenue dans votre espace administrateur.</p>

    <div class="admin-stats">

        <div class="stat-card">
            <h2>Produits</h2>
            <h3><?= (int)$totalProduits ?></h3>
        </div>

        <div class="stat-card">
            <h2>Commandes</h2>
            <h3><?= (int)$totalCommandes ?></h3>
        </div>

        <div class="stat-card">
            <h2>Chiffre d'affaires</h2>
            <h3><?= number_format((float)$chiffreAffaires, 0, ',', ' ') ?> FCFA</h3>
        </div>

    </div>

    <h2>Gestion de la boutique</h2>

    <div class="admin-actions">

        <a href="product.php" class="btn">
            Gérer les produits
        </a>

        <a href="orders.php" class="btn">
            Gérer les commandes
        </a>

        <a href="logout.php" class="btn">
            Déconnexion
        </a>

    </div>

    <h2>Les 5 dernières commandes</h2>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($commandes as $commande): ?>

                <tr>
                    <td>#<?= (int)$commande['id'] ?></td>

                    <td>
                        <?= htmlspecialchars($commande['customer_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($commande['email']) ?>
                    </td>

                    <td>
                        <?= number_format($commande['total'], 0, ',', ' ') ?> FCFA
                    </td>

                    <td>
                        <?= htmlspecialchars($commande['status']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($commande['created_at']) ?>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

        <?php if (empty($commandes)): ?>
            <p>Aucune commande enregistrée.</p>
        <?php endif; ?>

    </div>

</div>

</body>
</html>