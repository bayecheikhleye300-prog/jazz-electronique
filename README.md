# 🎧 JAZZ ÉLECTRONIQUE

### Site e-commerce de vente de produits électroniques

Bienvenue sur **JAZZ ÉLECTRONIQUE**, un projet de site web e-commerce développé dans le cadre de mon apprentissage en développement web.

L'objectif de ce projet est de concevoir une boutique en ligne permettant aux clients de découvrir des produits électroniques, de consulter leurs caractéristiques, de les ajouter au panier et de passer des commandes.

---

## 📌 Présentation du projet

JAZZ ÉLECTRONIQUE est une plateforme de commerce électronique spécialisée dans la vente de produits électroniques.

Le site propose une interface permettant aux visiteurs de parcourir les produits disponibles et un espace d'administration permettant de gérer les produits et les commandes.

Ce projet m'a permis de mettre en pratique mes connaissances en développement web, en programmation PHP et en gestion de bases de données MySQL.

---

## 🚀 Fonctionnalités principales

### 🛍️ Espace client

- Page d'accueil présentant la boutique.
- Catalogue des produits électroniques.
- Consultation des détails de chaque produit.
- Affichage des prix et des stocks disponibles.
- Ajout des produits au panier.
- Modification des quantités dans le panier.
- Suppression des articles du panier.
- Calcul automatique du montant total.
- Formulaire de commande.
- Choix du mode de paiement.
- Page de confirmation de commande.

### 🔐 Espace administrateur

- Connexion sécurisée à l'administration.
- Tableau de bord avec statistiques.
- Consultation du nombre de produits.
- Consultation du nombre de commandes.
- Affichage du chiffre d'affaires enregistré.
- Ajout de nouveaux produits.
- Modification des produits.
- Activation et désactivation des produits.
- Suppression des produits.
- Consultation des commandes.
- Consultation des informations des clients.
- Consultation des produits commandés.
- Modification du statut des commandes.
- Déconnexion de l'administrateur.

---

## 💻 Technologies utilisées

| Technologie | Utilisation |
|---|---|
| HTML5 | Structure des pages web |
| CSS3 | Mise en forme et design |
| PHP | Développement des fonctionnalités |
| MySQL | Gestion de la base de données |
| PDO | Connexion et requêtes SQL |
| JavaScript | Technologies complémentaires si utilisées |
| Git | Gestion des versions |
| GitHub | Hébergement du code source |

---

## 🗂️ Structure du projet

```text
JAZZ-ELECTRONIQUE/
│
├── admin/
│   ├── database/
│   ├── auth.php
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── orders.php
│   └── product.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── images/
│
├── config/
│   └── database.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
│
├── boutique.php
├── commande.php
├── confirmation.php
├── contact.php
├── index.php
├── panier.php
├── produit.php
└── README.md
```

---

## ⚙️ Installation du projet

Pour installer et exécuter ce projet localement, suivez les étapes suivantes.

### 1. Prérequis

Installer les outils suivants :

- XAMPP
- PHP
- MySQL
- Un navigateur web

### 2. Cloner le projet

Ouvrir un terminal et exécuter :

```bash
git clone https://github.com/bayecheikhleye300-prog/jazz-electronique.git
```

### 3. Placer le projet

Déplacer le dossier dans le répertoire :

```text
C:\xampp\htdocs\
```

### 4. Configurer la base de données

- Démarrer Apache et MySQL depuis XAMPP.
- Ouvrir phpMyAdmin.
- Créer une base de données nommée `jazz_electronique`.
- Importer le fichier SQL fourni dans `admin/database/`, s'il est présent dans le dépôt.

### 5. Configurer la connexion

Créer ou adapter le fichier :

```text
config/database.php
```

Renseigner les paramètres de connexion à MySQL selon votre environnement local.

### 6. Lancer le site

Ouvrir un navigateur et accéder à :

```text
http://localhost/JAZZ%20ELECTRONIQUE/
```

---

## 🛠️ Gestion des commandes

Le système permet d'enregistrer les commandes des clients et de consulter leurs détails depuis l'espace administrateur.

Les différents statuts de commande disponibles sont :

- En attente
- Confirmée
- Expédiée
- Livrée
- Annulée

Les modes de paiement proposés sont Wave, Orange Money et paiement à la livraison.

**Important :** les modes de paiement sont enregistrés dans le système. L'intégration technique des services de paiement en ligne reste à développer.

---

## 🎯 Objectifs pédagogiques

Ce projet m'a permis de travailler sur plusieurs compétences :

- Création d'un site web dynamique.
- Développement avec PHP.
- Connexion à une base de données MySQL.
- Réalisation d'opérations CRUD.
- Gestion des sessions PHP.
- Gestion d'un panier d'achat.
- Enregistrement et suivi des commandes.
- Création d'un espace administrateur.
- Utilisation de Git et GitHub.

---

## 🔮 Améliorations futures

Plusieurs améliorations pourront être apportées :

- Intégration réelle des paiements Wave et Orange Money.
- Mise en place d'un système de recherche avancée.
- Ajout de filtres par catégories et par prix.
- Création d'un espace client.
- Suivi des commandes par les clients.
- Amélioration de la sécurité.
- Mise en ligne du site sur un hébergement web.

---

## 👨‍💻 Auteur

**Cheikh**

Projet réalisé dans le cadre de mon apprentissage en développement web.

### 📂 Retrouvez le code source

[Consulter le dépôt GitHub – JAZZ ÉLECTRONIQUE](https://github.com/bayecheikhleye300-prog/jazz-electronique)

---

⭐ Merci de visiter mon projet !

N'hésitez pas à découvrir le code source et à suivre son évolution.