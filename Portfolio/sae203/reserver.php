<?php
session_start();
require_once 'config/db.php'; // Require pour ne pas appeller 2 fois le fichier

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_concert'])) {
    $id_concert = (int) $_POST['id_concert'];
    $id_user = $_SESSION['id_utilisateur'];

    // Vérifier si des places sont disponibles
    $stmt = $pdo->prepare("SELECT nb_places_restantes FROM sae203_concert WHERE id_concert = ?");
    $stmt->execute([$id_concert]); // Cherche le nb de places restantes pour le concert avec l'id concert 
    $concert = $stmt->fetch(); // Prend la valeur du nb de place

    if ($concert && $concert['nb_places_restantes'] > 0) {// Vérifie que concert existe (donc qu'un concert a été trouvé) et que le nb de place n'est pas égal à 0
        // Enregistrement du billet
        $pdo->beginTransaction(); // début transaction (permet de rollback)

        try {
            // Insérer le billet
            $stmt = $pdo->prepare("INSERT INTO sae203_billet (id_utilisateur, id_concert) VALUES (?, ?)");
            $stmt->execute([$id_user, $id_concert]); // insert le billet dans la bdd

            // Décrémenter les places
            $stmt = $pdo->prepare("UPDATE sae203_concert SET nb_places_restantes = nb_places_restantes - 1 WHERE id_concert = ?");
            $stmt->execute([$id_concert]); // retire une place au concert

            $pdo->commit(); //confirme la transaction
            header('Location: dashboard.php');
            exit();

        } catch (Exception $e) {// si il y a une erreur
            $pdo->rollBack(); // annule toute la transaction retouir a l'état d'avant
            echo "Erreur lors de la réservation : " . $e->getMessage();
        }

    } else {
        echo "Plus de places disponibles.";
    }
} else {
    echo "Requête invalide.";
}
?>