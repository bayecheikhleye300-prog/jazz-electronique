

<?php

session_start();

require_once "../config/database.php";

$message = "";
$erreur = "";

$dossierImages = "../assets/images/";

if (!is_dir($dossierImages)) {
    mkdir($dossierImages, 0775, true);
}

// TRAITEMENT DES FORMULAIRES

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    // AJOUTER OU MODIFIER UN PRODUIT

    if ($action === "enregistrer") {

        $id = (int)($_POST["id"] ?? 0);

        $nom = trim($_POST["name"] ?? "");
        $categorie = trim($_POST["category"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $prix = (float)($_POST["price"] ?? 0);
        $stock = (int)($_POST["stock"] ?? 0);

        $image = trim($_POST["ancienne_image"] ?? "");

        if (
            $nom === "" ||
            $categorie === "" ||
            $prix <= 0 ||
            $stock < 0
        ) {

            $erreur = "Veuillez remplir correctement les champs.";

        } else {

            // GESTION DE L'IMAGE

            if (
                isset($_FILES["image"]) &&
                $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {

                    $erreur = "Erreur lors de l'envoi de l'image.";

                } else {

                    $extension = strtolower(
                        pathinfo(
                            $_FILES["image"]["name"],
                            PATHINFO_EXTENSION
                        )
                    );

                    $extensionsAutorisees = [
                        "jpg", "jpeg", "png", "webp"
                    ];

                    $tailleMax = 3 * 1024 * 1024;

                    $mime = mime_content_type(
                        $_FILES["image"]["tmp_name"]
                    );

                    $mimesAutorises = [
                        "image/jpeg",
                        "image/png",
                        "image/webp"
                    ];

                    if (
                        !in_array($extension, $extensionsAutorisees, true) ||
                        !in_array($mime, $mimesAutorises, true) ||
                        $_FILES["image"]["size"] > $tailleMax
                    ) {

                        $erreur = "Image invalide. Formats acceptés : JPG, PNG, WEBP (3 Mo maximum).";

                    } else {

                        $nomImage = uniqid("produit_", true) . "." . $extension;

                        if (
                            move_uploaded_file(
                                $_FILES["image"]["tmp_name"],
                                $dossierImages . $nomImage
                            )
                        ) {

                            $image = $nomImage;

                        } else {

                            $erreur = "Impossible d'enregistrer l'image.";

                        }
                    }
                }
            }

            if ($erreur === "") {

                if ($id > 0) {

                    $requete = $pdo->prepare(
                        "UPDATE products
                         SET name = ?, category = ?, description = ?,
                             price = ?, stock = ?, image = ?
                         WHERE id = ?"
                    );

                    $requete->execute([
                        $nom,
                        $categorie,
                        $description,
                        $prix,
                        $stock,
                        $image,
                        $id
                    ]);

                    $message = "Produit modifié avec succès.";

                } else {

                    $requete = $pdo->prepare(
                        "INSERT INTO products
                         (name, category, description, price, stock, image, active)
                         VALUES (?, ?, ?, ?, ?, ?, 1)"
                    );

                    $requete->execute([
                        $nom,
                        $categorie,
                        $description,
                        $prix,
                        $stock,
                        $image
                    ]);

                    $message = "Produit ajouté avec succès.";
                }
            }
        }
    }

    // ACTIVER OU DESACTIVER UN PRODUIT

    if ($action === "statut") {

        $id = (int)($_POST["id"] ?? 0);

        $requete = $pdo->prepare(
            "UPDATE products
             SET active = IF(active = 1, 0, 1)
             WHERE id = ?"
        );

        $requete->execute([$id]);

        $message = "Statut du produit modifié.";
    }

    // SUPPRIMER UN PRODUIT

    if ($action === "supprimer") {

        $id = (int)($_POST["id"] ?? 0);

        $requete = $pdo->prepare(
            "DELETE FROM products WHERE id = ?"
        );

        $requete->execute([$id]);

        $message = "Produit supprimé définitivement.";
    }
}

// PRODUIT A MODIFIER

$produitAModifier = null;

if (isset($_GET["modifier"])) {

    $id = (int)$_GET["modifier"];

    $requete = $pdo->prepare(
        "SELECT * FROM products WHERE id = ?"
    );

    $requete->execute([$id]);

    $produitAModifier = $requete->fetch();
}

// LISTE DES PRODUITS

$requete = $pdo->query(
    "SELECT * FROM products ORDER BY created_at DESC"
);

$produits = $requete->fetchAll();

require_once "../includes/header.php";

?>

<div class="container">

    <h1>Gestion des produits</h1>

    <?php if ($message !== ""): ?>

        <p style="color:green; font-weight:bold;">
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>

    <?php if ($erreur !== ""): ?>

        <p style="color:red; font-weight:bold;">
            <?= htmlspecialchars($erreur) ?>
        </p>

    <?php endif; ?>


    <!-- FORMULAIRE PRODUIT -->

    <h2>
        <?= $produitAModifier ? "Modifier un produit" : "Ajouter un produit" ?>
    </h2>

    <form method="POST" enctype="multipart/form-data">

        <input type="hidden" name="action" value="enregistrer">

        <input
            type="hidden"
            name="id"
            value="<?= (int)($produitAModifier["id"] ?? 0) ?>"
        >

        <input
            type="hidden"
            name="ancienne_image"
            value="<?= htmlspecialchars($produitAModifier["image"] ?? "") ?>"
        >

        <label>Nom du produit</label>

        <input
            type="text"
            name="name"
            required
            value="<?= htmlspecialchars($produitAModifier["name"] ?? "") ?>"
        >

        <label>Catégorie</label>

        <input
            type="text"
            name="category"
            required
            value="<?= htmlspecialchars($produitAModifier["category"] ?? "") ?>"
        >

        <label>Description</label>

        <textarea name="description"><?= htmlspecialchars($produitAModifier["description"] ?? "") ?></textarea>

        <label>Prix (FCFA)</label>

        <input
            type="number"
            name="price"
            min="1"
            step="1"
            required
            value="<?= htmlspecialchars($produitAModifier["price"] ?? "") ?>"
        >

        <label>Stock</label>

        <input
            type="number"
            name="stock"
            min="0"
            required
            value="<?= htmlspecialchars($produitAModifier["stock"] ?? "0") ?>"
        >

        <label>Image du produit</label>

        <input
            type="file"
            name="image"
            accept=".jpg,.jpeg,.png,.webp"
        >

        <?php if (!empty($produitAModifier["image"])): ?>

            <p>Image actuelle :</p>

            <img
                src="../assets/images/<?= htmlspecialchars($produitAModifier["image"]) ?>"
                width="120"
                alt="Image actuelle du produit"
            >

        <?php endif; ?>

        <br><br>

        <button type="submit" class="btn">
            <?= $produitAModifier ? "Enregistrer les modifications" : "Ajouter le produit" ?>
        </button>

        <?php if ($produitAModifier): ?>

            <a href="product.php" class="btn">
                Annuler
            </a>

        <?php endif; ?>

    </form>


    <!-- LISTE DES PRODUITS -->

    <h2 style="margin-top:50px;">Produits de la boutique</h2>

    <div style="overflow-x:auto;">

        <table>

            <thead>

                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($produits as $produit): ?>

                    <tr>

                        <td>

                            <?php if (!empty($produit["image"])): ?>

                                <img
                                    src="../assets/images/<?= htmlspecialchars($produit["image"]) ?>"
                                    width="65"
                                    height="65"
                                    style="object-fit:contain;"
                                    alt="Produit"
                                >

                            <?php else: ?>

                                Aucune image

                            <?php endif; ?>

                        </td>

                        <td>
                            <?= htmlspecialchars($produit["name"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($produit["category"]) ?>
                        </td>

                        <td>
                            <?= number_format($produit["price"], 0, ",", " ") ?> FCFA
                        </td>

                        <td>
                            <?= (int)$produit["stock"] ?>
                        </td>

                        <td>

                            <?= $produit["active"] ? "Actif" : "Désactivé" ?>

                        </td>

                        <td>

                            <a
                                href="product.php?modifier=<?= (int)$produit["id"] ?>"
                                class="btn"
                            >
                                Modifier
                            </a>

                            <form method="POST" style="display:inline; padding:0; box-shadow:none;">

                                <input type="hidden" name="action" value="statut">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$produit["id"] ?>"
                                >

                                <button type="submit" class="btn">
                                    <?= $produit["active"] ? "Désactiver" : "Activer" ?>
                                </button>

                            </form>

                            <form
                                method="POST"
                                style="display:inline; padding:0; box-shadow:none;"
                                onsubmit="return confirm('Supprimer définitivement ce produit ?');"
                            >

                                <input type="hidden" name="action" value="supprimer">

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$produit["id"] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn"
                                    style="background:#dc3545; color:white;"
                                >
                                    Supprimer
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <br>

    <a href="dashboard.php" class="btn">
        Retour au tableau de bord
    </a>

</div>

<?php require_once "../includes/footer.php"; ?>