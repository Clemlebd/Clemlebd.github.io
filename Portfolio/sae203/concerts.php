<?php
session_start();
include 'includes/header.php'; 
require_once 'config/db.php'; // Require pour ne pas appeller 2 fois le fichier

echo "<h2>Concerts Disponibles</h2>";

$stmt = $pdo->query("SELECT * FROM sae203_concert WHERE nb_places_restantes > 0 ORDER BY date ASC");
$concerts = $stmt->fetchAll();

if (count($concerts) === 0) { // Vérifie si la vartiable concert contient des données
    echo "<p>Aucun concert disponible pour le moment.</p>";
} else {
    echo "<table border='1' cellpadding='10'>
            <tr>
                <th>Date</th>
                <th>Lieu</th>
                <th>Prix (€)</th>
                <th>Places restantes</th>
                <th>Action</th>
            </tr>";

    foreach ($concerts as $concert) { //affichage des concerts sous forme de tableau html et contient la fonctionnalitée réserver 
        echo "<tr>
                <td>{$concert['date']}</td>
                <td>{$concert['lieu']}</td>
                <td>{$concert['prix']}</td>
                <td>{$concert['nb_places_restantes']}</td>
                <td>";

        if (isset($_SESSION['id_utilisateur'])) { // contient un boutopn caché qui permet d'envoyer l'id du concert lors de la réservation
            echo "<form method='post' action='reserver.php'>
                    <input type='hidden' name='id_concert' value='{$concert['id_concert']}'> 
                    <button type='submit'>Réserver</button>
                  </form>";
        } else {
            echo "Connectez-vous"; // Si l'utilisateur n'est pas connecté
        }

        echo "</td></tr>";
    }

    echo "</table>";
}

include 'includes/footer.php';
?>