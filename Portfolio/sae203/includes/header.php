<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Billetterie Damso</title>
</head>
<body>

<nav style="margin-bottom: 20px;">
    <a href="index.php">Accueil</a> |

    <?php
    if (!isset($_SESSION['id_utilisateur'])) {
        echo '<a href="login.php">Connexion</a> | ';
        echo '<a href="formulaire.html">Inscription</a>';
    } else {
        // Si utilisateur non admin
        if ($_SESSION['mail'] !== 'admin@damso.fr') {
            echo '<a href="concerts.php">Réserver</a> | ';
        }

        echo '<a href="dashboard.php">Mes Réservations</a> | ';

        // Si admin
        if ($_SESSION['mail'] === 'admin@damso.fr') {
            echo '<a href="concerts.php">Réserver</a> | ';
            echo '<a href="admin.php">Admin</a> | ';
        }

        echo '<a href="logout.php">Déconnexion</a>';
    }
    ?>
</nav>
<hr>