<?php
// api/place-order.php
require_once '../config/config.php';
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

$prenom  = trim($_POST['prenom'] ?? '');
$nom     = trim($_POST['nom'] ?? '');
$email   = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$adresse = trim($_POST['adresse'] ?? '');
$cp      = trim($_POST['cp'] ?? '');
$ville   = trim($_POST['ville'] ?? '');

if (!$prenom || !$nom || !$email || !$adresse || !$cp || !$ville) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
    exit;
}

$cart_data = json_decode($_POST['cart_data'] ?? '[]', true);
if (empty($cart_data)) {
    echo json_encode(['success' => false, 'message' => 'Votre panier est vide.']);
    exit;
}

// Vérifier les stocks et calculer le total
$total = 0;
$verified_items = [];
foreach ($cart_data as $item) {
    $product_id = (int)($item['id'] ?? 0);
    $quantity   = (int)($item['quantity'] ?? 1);
    if ($product_id <= 0 || $quantity <= 0) continue;

    $stmt = $pdo->prepare("SELECT id, nom, prix, stock FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();
    if (!$product || $product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => "Stock insuffisant pour : {$product['nom']}"]);
        exit;
    }
    $total += $product['prix'] * $quantity;
    $verified_items[] = ['product' => $product, 'quantity' => $quantity];
}

$total += 4.90; // Frais de livraison
$adresse_livraison = "$prenom $nom, $adresse, $cp $ville";
$user_id = $_SESSION['user_id'] ?? null;

try {
    $pdo->beginTransaction();

    // Créer la commande
    $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, total, adresse_livraison, mode_paiement, statut) VALUES (:uid, :total, :adresse, 'stripe', 'en_attente')");
    $order_stmt->execute([':uid' => $user_id, ':total' => $total, ':adresse' => $adresse_livraison]);
    $order_id = $pdo->lastInsertId();

    // Insérer les lignes de commande et décrémenter les stocks
    $item_stmt  = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantite, prix_unitaire) VALUES (:order_id, :product_id, :qty, :prix)");
    $stock_stmt = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id");

    foreach ($verified_items as $vi) {
        $item_stmt->execute([':order_id' => $order_id, ':product_id' => $vi['product']['id'], ':qty' => $vi['quantity'], ':prix' => $vi['product']['prix']]);
        $stock_stmt->execute([':qty' => $vi['quantity'], ':id' => $vi['product']['id']]);
    }

    $pdo->commit();

    // Succès : redirection vers page de confirmation
    echo json_encode(['success' => true, 'order_id' => $order_id, 'redirect' => '../confirmation.php?order_id=' . $order_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur, veuillez réessayer.']);
}
