<?php
session_start();
include 'includes/header.php';
?>

<h1>Bienvenue sur la billetterie de Damso</h1>

<?php if (!isset($_SESSION['id_utilisateur'])): ?>
    <p style="color: red; font-weight: bold; font-size: 2em;">
        Veuillez créer un compte ou vous connecter pour accéder à la totalité du site.
    </p>
<?php else: ?>
    <p>Bonjour <?= htmlspecialchars($_SESSION['prenom'])?></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>