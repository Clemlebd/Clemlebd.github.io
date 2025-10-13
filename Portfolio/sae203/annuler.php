<?php
session_start();
require_once 'config/db.php'; // Require pour ne pas appeller 2 fois le fichier

if (!isset($_SESSION['id_utilisateur'])) { //Vérifie si l'utilisateur est connecté
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_billet'])) { // Vérifie si la page a été accédée par le biais d'un formulaire Post et que le champ id_billet existe
    $id_billet = (int) $_POST['id_billet'];
    $id_user = $_SESSION['id_utilisateur'];

    // Récupère le billet
    $stmt = $pdo->prepare("SELECT id_concert FROM sae203_billet WHERE id_billet = ? AND id_utilisateur = ?");
    $stmt->execute([$id_billet, $id_user]);
    $data = $stmt->fetch();

    if ($data) {// Si Data contient des données
        $id_concert = $data['id_concert'];

        $pdo->beginTransaction();// Commence une transaction ( sert à effectuer tout le bloc ou ne rien faire)

        try {
            // Supprimer le billet
            $stmt = $pdo->prepare("DELETE FROM sae203_billet WHERE id_billet = ?");
            $stmt->execute([$id_billet]);

            // Réajoute une place au concert
            $stmt = $pdo->prepare("UPDATE sae203_concert SET nb_places_restantes = nb_places_restantes + 1 WHERE id_concert = ?");
            $stmt->execute([$id_concert]);

            $pdo->commit(); // Confirme l'execution de toutes les requêtes 
            header("Location: dashboard.php");
            exit();

        } catch (Exception $e) { // Si une erreur est commise
            $pdo->rollBack();//Annule les opérations faites dans la transaction
            echo "Erreur lors de l’annulation : " . $e->getMessage();
        }
    } else {
        echo "Réservation introuvable.";
    }
} else {
    echo "Requête invalide.";
}
?>