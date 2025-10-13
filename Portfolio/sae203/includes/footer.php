<?php
// Si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<hr>
<?php if (isset($_SESSION['id_utilisateur'])): ?>
    <form action="./logout.php" method="post" style="margin-top: 10px;">
        <button type="submit">Se déconnecter</button>
    </form>
<?php endif; ?></body></html>