# 🐾 Magic Brush — Document Projet
**Site e-commerce de brosses de toilettage pour chiens et chats**

---

## 1. Présentation du Projet

| Champ | Détail |
|---|---|
| **Nom du projet** | Magic Brush |
| **Type** | Site e-commerce |
| **Secteur** | Animalerie — Toilettage |
| **Cible** | Propriétaires de chiens et chats |
| **Objectif principal** | Vendre en ligne des brosses de toilettage premium pour animaux de compagnie |

### Vision
Magic Brush se positionne comme une marque de confiance pour les propriétaires soucieux du bien-être de leurs animaux. Le site doit inspirer confiance, mettre en valeur la qualité des produits et offrir une expérience d'achat simple et agréable.

---

## 2. Objectifs du Projet

### Objectifs Business
- Lancer une boutique en ligne opérationnelle dans un délai de 3 mois
- Atteindre 50 commandes/mois dans les 6 premiers mois
- Construire une base clients fidèle grâce à un programme de fidélité
- Proposer une gamme de 20 à 40 références produits au lancement

### Objectifs Techniques
- Site responsive (mobile-first)
- Temps de chargement < 3 secondes
- Score Lighthouse > 85
- SEO optimisé dès le lancement

---

## 3. Périmètre Fonctionnel

### 3.1 Fonctionnalités Essentielles (MVP)

#### Catalogue Produits
- Affichage des produits avec photos, descriptions, prix
- Filtres par catégorie (chien / chat), type de poil (court, long, épais), marque
- Moteur de recherche interne
- Fiches produits détaillées (composition, conseils d'utilisation, avis clients)

#### Gestion du Panier & Commande
- Ajout/suppression au panier
- Récapitulatif de commande
- Tunnel d'achat en 3 étapes (panier → livraison → paiement)
- Confirmation de commande par e-mail

#### Paiement
- Paiement par carte bancaire (Stripe)
- Paiement Mobile Money (si ciblage Afrique de l'Ouest)
- Paiement à la livraison (optionnel)

#### Gestion des Utilisateurs
- Création de compte / connexion
- Historique des commandes
- Gestion des adresses de livraison
- Liste de souhaits (wishlist)

#### Back-office (Administration)
- Gestion des produits (CRUD)
- Gestion des commandes et des statuts
- Gestion des stocks
- Tableau de bord avec statistiques clés

### 3.2 Fonctionnalités Complémentaires (V2)

- Blog / conseils toilettage
- Programme de fidélité (points, récompenses)
- Avis et notes produits vérifiés
- Comparateur de produits
- Chat en ligne / support client
- Newsletter avec promotions
- Vente par abonnement (réapprovisionnement automatique)

---

## 4. Catalogue Produits — Structure Initiale

### Catégories Principales

```
Magic Brush
├── 🐕 Pour Chiens
│   ├── Brosses à poils courts
│   ├── Brosses à poils longs
│   ├── Brosses démêlantes
│   ├── Gants de toilettage
│   └── Accessoires (peigne, furminator, etc.)
└── 🐈 Pour Chats
    ├── Brosses douces
    ├── Brosses anti-poils
    ├── Brosses autonettoyantes
    ├── Gants de massage
    └── Accessoires (peigne, brosse anti-statique, etc.)
```

### Attributs Produits
- Nom, référence SKU
- Description courte & description longue
- Prix (HT / TTC), prix barré (promotion)
- Photos (min. 3 par produit)
- Catégorie & sous-catégorie
- Espèce cible (chien / chat / universel)
- Type de pelage compatible
- Matériaux
- Dimensions & poids
- Stock disponible
- Tags SEO

---

## 5. Architecture Technique

### 5.1 Stack Technologique Retenue

| Couche | Technologie | Rôle |
|---|---|---|
| **Frontend** | HTML5 + CSS3 (custom) | Structure et mise en page des pages |
| **Styling** | CSS personnalisé (variables, Flexbox, Grid) | Charte visuelle Magic Brush |
| **Interactivité** | JavaScript Vanilla | Panier, filtres, sliders, validations |
| **Backend** | PHP 8+ | Logique serveur, routing, gestion sessions |
| **Base de données** | MySQL 8 | Produits, commandes, utilisateurs |
| **Templating** | PHP natif (include/require) | Pages dynamiques sans framework |
| **Paiement** | Stripe (PHP SDK) | Paiement carte bancaire sécurisé |
| **Emails** | PHPMailer + SMTP | Confirmations de commande, alertes |
| **Stockage images** | Serveur local / dossier `/uploads` | Photos produits uploadées via admin |
| **Hébergement** | Serveur mutualisé ou VPS (cPanel/Apache) | Déploiement PHP + MySQL |
| **Nom de domaine** | `.com` ou `.bj` | Identité en ligne Magic Brush |

> ✅ **Stack choisie** : HTML / CSS / JS Vanilla en frontend + PHP natif en backend — solution maîtrisée, légère, compatible avec la majorité des hébergeurs mutualisés.

### 5.2 Structure des Fichiers du Projet

```
magic-brush/
├── index.php                  # Page d'accueil
├── catalogue.php              # Liste des produits
├── produit.php                # Fiche produit détaillée
├── panier.php                 # Panier
├── commande.php               # Tunnel de commande
├── confirmation.php           # Page de confirmation
├── connexion.php              # Login / Inscription
├── compte.php                 # Espace utilisateur
│
├── admin/                     # Back-office (accès restreint)
│   ├── index.php              # Dashboard admin
│   ├── produits.php           # Gestion produits
│   ├── commandes.php          # Gestion commandes
│   └── stocks.php             # Gestion stocks
│
├── includes/                  # Composants PHP réutilisables
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── db.php                 # Connexion MySQL (PDO)
│
├── assets/
│   ├── css/
│   │   ├── style.css          # Styles globaux
│   │   ├── catalogue.css
│   │   ├── produit.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js            # Scripts généraux
│   │   ├── panier.js          # Logique panier (localStorage)
│   │   └── filtres.js         # Filtres catalogue
│   └── img/
│       └── uploads/           # Photos produits
│
├── api/                       # Endpoints PHP (AJAX)
│   ├── add-to-cart.php
│   ├── get-cart.php
│   ├── place-order.php
│   └── stripe-webhook.php
│
└── config/
    ├── config.php             # Variables globales (DB, Stripe keys…)
    └── .htaccess              # Réécriture d'URL, sécurité
```

### 5.3 Architecture Simplifiée

```
┌─────────────────────────────────────────────────┐
│              NAVIGATEUR (Client)                 │
│         HTML + CSS + JavaScript Vanilla          │
│   Panier en localStorage │ Requêtes AJAX (fetch) │
└────────────────────┬────────────────────────────┘
                     │ HTTPS (Apache)
┌────────────────────▼────────────────────────────┐
│              SERVEUR PHP (Backend)               │
│  ┌──────────┐  ┌──────────┐  ┌───────────────┐  │
│  │  Pages   │  │ Sessions │  │  API AJAX     │  │
│  │  PHP     │  │  Auth    │  │  (add-cart,   │  │
│  │          │  │          │  │  order, etc.) │  │
│  └──────────┘  └──────────┘  └───────────────┘  │
└────────────────────┬────────────────────────────┘
                     │ PDO
┌────────────────────▼────────────────────────────┐
│              MySQL + SERVICES EXTERNES           │
│   MySQL 8  │  Stripe PHP SDK  │  PHPMailer      │
└─────────────────────────────────────────────────┘
```

### 5.4 Modèle de Base de Données (Schéma Principal)

```sql
-- Utilisateurs
users (id, nom, prenom, email, password_hash, role, created_at)

-- Produits
products (id, nom, slug, description, prix, prix_barre, stock,
          categorie_id, espece, type_poil, sku, image_principale, created_at)

-- Catégories
categories (id, nom, slug, espece, parent_id)

-- Images produits
product_images (id, product_id, url, ordre)

-- Commandes
orders (id, user_id, statut, total, adresse_livraison,
        mode_paiement, stripe_id, created_at)

-- Lignes de commande
order_items (id, order_id, product_id, quantite, prix_unitaire)

-- Avis clients
reviews (id, product_id, user_id, note, commentaire, created_at)
```

---

## 6. Design & Identité Visuelle

### Charte Graphique

| Élément | Choix |
|---|---|
| **Palette principale** | Vert sauge `#7A9E7E` + Crème `#F5F0E8` + Brun chaud `#8B5E3C` |
| **Police titres** | Playfair Display (élégance, nature) |
| **Police corps** | DM Sans (lisibilité, modernité) |
| **Ton** | Chaleureux, bienveillant, expert |
| **Style visuel** | Naturel, organique, premium |

### Pages Clés à Designer

1. **Page d'accueil** — Hero, produits vedettes, catégories, témoignages
2. **Page catalogue** — Grille produits + filtres latéraux
3. **Fiche produit** — Photos, infos, avis, produits similaires
4. **Panier** — Récap + recommandations
5. **Tunnel de commande** — Livraison + paiement
6. **Compte utilisateur** — Tableau de bord personnel
7. **Back-office** — Interface admin

---

## 7. Plan de Développement

### Phase 1 — Cadrage & Design (Semaines 1–3)
- [ ] Finalisation du cahier des charges
- [ ] Création de la charte graphique
- [ ] Maquettes Figma des pages principales (desktop + mobile)
- [ ] Validation par le client
- [ ] Mise en place de l'environnement de développement

### Phase 2 — Développement Core Frontend (Semaines 4–7)
- [ ] Intégration HTML/CSS de toutes les pages (maquettes → code)
- [ ] Mise en place de la charte CSS (variables, typographie, couleurs)
- [ ] Navigation responsive (menu hamburger mobile)
- [ ] Page catalogue avec grille produits
- [ ] Fiche produit (galerie photos, onglets description/avis)
- [ ] Panier en JavaScript (localStorage)
- [ ] Filtres dynamiques catalogue (JS Vanilla)

### Phase 3 — Développement Backend PHP (Semaines 8–11)
- [ ] Connexion MySQL via PDO (`includes/db.php`)
- [ ] Système d'authentification PHP (inscription, connexion, sessions)
- [ ] Pages dynamiques PHP (catalogue, fiche produit depuis MySQL)
- [ ] Gestion du panier côté serveur (synchronisation session)
- [ ] Tunnel de commande (formulaire livraison + validation)
- [ ] Intégration Stripe PHP SDK (paiement carte)
- [ ] Envoi d'emails avec PHPMailer (confirmation commande)
- [ ] Endpoints AJAX (`api/`) pour les interactions dynamiques

### Phase 4 — Back-office Admin PHP (Semaines 12–13)
- [ ] Authentification admin sécurisée (rôle `admin` en base)
- [ ] Dashboard avec statistiques (CA, commandes, stocks)
- [ ] CRUD produits (ajout, édition, suppression + upload images)
- [ ] Gestion des commandes (liste + changement de statut)
- [ ] Gestion des stocks avec alertes seuil bas

### Phase 5 — Tests, SEO & Mise en Production (Semaines 14–16)
- [ ] Tests fonctionnels (parcours utilisateur complet)
- [ ] Tests sécurité PHP (injections SQL via PDO, XSS, CSRF)
- [ ] Configuration `.htaccess` (réécriture URL propres, protection dossier admin)
- [ ] SEO on-page (balises meta, sitemap.xml, robots.txt)
- [ ] Déploiement sur hébergeur (cPanel, FTP ou Git deploy)
- [ ] Migration base de données en production
- [ ] Recette client + corrections
- [ ] Lancement officiel 🚀

---

## 8. Livrables

| Livrable | Description | Deadline |
|---|---|---|
| Maquettes Figma | Pages principales desktop + mobile | Semaine 3 |
| MVP Frontend | Catalogue + panier fonctionnels | Semaine 8 |
| Tunnel paiement | Commande complète avec Stripe | Semaine 11 |
| Back-office | Interface admin complète | Semaine 13 |
| Site en production | Version finale déployée | Semaine 16 |
| Documentation | Guide utilisateur + documentation technique | Semaine 16 |

---

## 9. Budget Estimatif

### Coûts de Développement (Estimation)

| Poste | Estimation |
|---|---|
| Design UI/UX (maquettes) | 500 000 – 800 000 FCFA |
| Développement frontend | 700 000 – 1 200 000 FCFA |
| Développement backend + API | 600 000 – 1 000 000 FCFA |
| Intégrations (Stripe, emails…) | 200 000 – 400 000 FCFA |
| Tests + déploiement | 150 000 – 300 000 FCFA |
| **Total développement** | **2 150 000 – 3 700 000 FCFA** |

### Coûts de Fonctionnement Mensuels

| Service | Coût mensuel estimé |
|---|---|
| Hébergement mutualisé (PHP + MySQL inclus) | 3 000 – 10 000 FCFA |
| Stripe (commission) | 1,4% + 0,25€ / transaction |
| Emails SMTP (Gmail SMTP ou Mailtrap) | Gratuit → 3 000 FCFA |
| Nom de domaine | ~5 000 FCFA/an |

---

## 10. Indicateurs de Succès (KPIs)

| KPI | Objectif 6 mois |
|---|---|
| Nombre de visiteurs uniques/mois | > 3 000 |
| Taux de conversion | > 2% |
| Nombre de commandes/mois | > 50 |
| Panier moyen | > 15 000 FCFA |
| Taux de retour clients | > 25% |
| Taux d'abandon panier | < 70% |
| Score satisfaction client | > 4,2/5 |

---

## 11. Risques & Mitigation

| Risque | Probabilité | Impact | Mitigation |
|---|---|---|---|
| Retard de développement | Moyen | Élevé | Planning avec marges de sécurité |
| Problème paiement en ligne | Faible | Élevé | Tests approfondis en environnement sandbox |
| Faible trafic au lancement | Élevé | Moyen | Stratégie SEO + réseaux sociaux préparée |
| Ruptures de stock | Moyen | Moyen | Alertes stock bas dans le back-office |
| Problèmes de sécurité | Faible | Élevé | HTTPS, validation des entrées, authentification sécurisée |

---

## 12. Équipe Projet

| Rôle | Responsabilité |
|---|---|
| **Chef de projet** | Coordination, planning, relation client |
| **Designer UI/UX** | Maquettes, charte graphique, expérience utilisateur |
| **Développeur Frontend** | HTML, CSS custom, JavaScript Vanilla, intégration maquettes |
| **Développeur Backend** | PHP 8, MySQL, Stripe SDK, PHPMailer, sécurité |
| **Testeur QA** | Tests fonctionnels, performance, sécurité |
| **Client (Magic Brush)** | Validation des livrables, fourniture des contenus |

---

## 13. Prochaines Étapes Immédiates

1. ✅ Validation du document projet par le client
2. 📋 Signature du contrat de prestation
3. 🎨 Kick-off design — brief charte graphique
4. 🗂️ Collecte des contenus (photos produits, textes, logo)
5. 🛠️ Mise en place des outils de collaboration (Figma, GitHub, Notion/Trello)

---

*Document rédigé le — Avril 2026*
*Version 1.1 — Magic Brush E-commerce — Stack : HTML/CSS/JS + PHP/MySQL*
