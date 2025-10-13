<?php
session_start();
require_once 'config/db.php'; // Require pour ne pas appeller 2 fois le fichier
include 'includes/header.php';

// Vérification d'accès administrateur
if (!isset($_SESSION['mail']) || $_SESSION['mail'] !== 'admin@damso.fr') {
    echo "<p>Accès réservé à l’administrateur.</p>"; // Si un utilisateur autre que l'admin parvient à atterir sur cette page
    include 'includes/footer.php';
    exit();//arrête le script
}

$message = '';

// Ajouter un concert
if (isset($_POST['ajouter'])) {
    $date = $_POST['date'];
    $lieu = $_POST['lieu'];
    $prix = $_POST['prix'];
    $places = $_POST['nb_places_restantes'];

    $stmt = $pdo->prepare("INSERT INTO sae203_concert (date, lieu, prix, nb_places_restantes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$date, $lieu, $prix, $places]); // donne les valeurs des "?"
    header("Location: admin.php?success=ajout"); // Confirme dans l'URL l'ajout
    exit();
}

// Supprimer un concert
if (isset($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer']; // Converti la valeur du parametre supprimer en entier
    $stmt = $pdo->prepare("DELETE FROM sae203_concert WHERE id_concert = ?");
    $stmt->execute([$id]);
    header("Location: admin.php?success=suppression"); // Confirme dans l'URL la suppression
    exit();
}

// Modifier un concert
if (isset($_POST['modifier'])) {
    $id = $_POST['id_concert'];
    $date = $_POST['date'];
    $lieu = $_POST['lieu'];
    $prix = $_POST['prix'];
    $places = $_POST['nb_places_restantes'];

    $stmt = $pdo->prepare("UPDATE sae203_concert SET date = ?, lieu = ?, prix = ?, nb_places_restantes = ? WHERE id_concert = ?");
    $stmt->execute([$date, $lieu, $prix, $places, $id]);
    header("Location: admin.php?success=modification"); // Confirme dans l'URL la modifaction
    exit();
}

// Message à afficher en fonction du paramètre GET
if (isset($_GET['success'])) { // Vérifie si le parametre success est défini
    if ($_GET['success'] === 'ajout') {
        $message = "<p style='color:green; font-size: 2em;'>Concert ajouté avec succès.</p>";
    } elseif ($_GET['success'] === 'suppression') {
        $message = "<p style='color:red; font-size: 2em;'>Concert supprimé.</p>";
    } elseif ($_GET['success'] === 'modification') {
        $message = "<p style='color:orange; font-size: 2em;'>Concert modifié.</p>";
    }
}

// Liste des concerts
$stmt = $pdo->query("SELECT * FROM sae203_concert ORDER BY date ASC");
$concerts = $stmt->fetchAll();
?>

<h2>Interface administrateur – Gestion des concerts</h2>

<?php if (!empty($message)) echo $message; ?> <!-- Vérifie que le message ne soit pas vide -->

<!-- Formulaire d'ajout -->
<h3>Ajouter un concert</h3>
<form method="post">
    <input type="date" name="date" required>
    <input type="text" name="lieu" placeholder="Lieu" required>
    <input type="number" step="0.01" name="prix" placeholder="Prix (€)" required>
    <input type="number" name="nb_places_restantes" placeholder="Places restantes" required>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<hr>

<!-- Liste des concerts -->
<h3>Concerts existants</h3>

<?php if (count($concerts) === 0): ?>
    <p>Aucun concert pour le moment.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr> <!-- Formulaire dans un tableau (permet de modifier directement et d'envoyer qu'une seule ligne au serveur puisque qu'un formulaire est créé par ligne) -->
            <th>Date</th>
            <th>Lieu</th>
            <th>Prix</th>
            <th>Places</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($concerts as $c): ?>
    <tr>
        <form method="post">
            <input type="hidden" name="id_concert" value="<?= $c['id_concert'] ?>">
            <td><input type="date" name="date" value="<?= $c['date'] ?>" required></td>
            <td><input type="text" name="lieu" value="<?= htmlspecialchars($c['lieu']) ?>" required></td>
            <td><input type="number" step="0.01" name="prix" value="<?= $c['prix'] ?>" required></td>
            <td><input type="number" name="nb_places_restantes" value="<?= $c['nb_places_restantes'] ?>" required></td>
            <td>
                <button type="submit" name="modifier">Modifier</button>
        </form>
        <form method="get" action="admin.php" onsubmit="return confirm('Supprimer ce concert ?');" style="display:inline;">
            <input type="hidden" name="supprimer" value="<?= $c['id_concert'] ?>">
            <button type="submit">Supprimer</button>
        </form>
            </td>
    </tr>
<?php endforeach; ?>
    </table>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>