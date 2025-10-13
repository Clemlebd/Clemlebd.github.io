<?php
session_start();
include 'includes/header.php';
require_once 'config/db.php'; // Require pour ne pas appeller 2 fois le fichier

if (!isset($_SESSION['id_utilisateur'])) { // Vérifie si l'utilisateur est connecté
    echo "Vous devez être connecté pour accéder à vos réservations.";
    include 'includes/footer.php';
    exit;
}

$id_user = $_SESSION['id_utilisateur'];

// Récupère les réservations de l'utilisateur
$stmt = $pdo->prepare( // Utilisation d'alias b pour remplacer sae203_billet et c pour remplacer sae203_concert
    "
    SELECT b.id_billet, c.date, c.lieu, c.prix 
    FROM sae203_billet b
    JOIN sae203_concert c ON b.id_concert = c.id_concert
    WHERE b.id_utilisateur = ?
");
$stmt->execute([$id_user]);
$billets = $stmt->fetchAll(); // Récupère les données sous forme de tableau associatif

echo "<h2>Mes Réservations</h2>";

if (count($billets) === 0) {
    echo "<p>Aucune réservation pour l’instant.</p>";
} else {
    echo "<table border='1' cellpadding='10'>
            <tr>
                <th>Date</th>
                <th>Lieu</th>
                <th>Prix</th>
                <th>Action</th>
            </tr>";

    foreach ($billets as $billet) {
        echo "<tr>
                <td>{$billet['date']}</td>
                <td>{$billet['lieu']}</td>
                <td>{$billet['prix']}</td>
                <td>
                    <form method='post' action='annuler.php' onsubmit=\"return confirm('Annuler cette réservation ?');\">
                        <input type='hidden' name='id_billet' value='{$billet['id_billet']}'>
                        <button type='submit'>Annuler</button>
                    </form>
                </td>
              </tr>";
    }

    echo "</table>";
}

include 'includes/footer.php';
?>