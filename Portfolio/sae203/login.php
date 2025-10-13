<?php
session_start();
require_once 'config/db.php'; // Require pour ne pas appeller 2 fois le fichier

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Vérifie que la page a été accédé par un formulaire Post si oui le code va s'executer
    $mail = htmlspecialchars(trim($_POST['mail'] ?? ''));
    $mdp = $_POST['mdp'] ?? '';

    if (!empty($mail) && !empty($mdp)) { // Vérifie que mail et mdp ne sont pas vides
        $stmt = $pdo->prepare("SELECT * FROM sae203_utilisateur WHERE mail = ?");
        $stmt->execute([$mail]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mdp'])) { // Vérifie que le mot de passe rentré est le bon et que user n'est pas vide(false)
            $_SESSION['id_utilisateur'] = $user['id_utilisateur']; // Retient l'id de user pour toutes les pages
            $_SESSION['mail'] = $user['mail']; // Retient le mail de user pour toutes les pages
            $_SESSION['nom'] = $user['nom']; // Retient le nom de user pour toutes les pages
            $_SESSION['prenom'] = $user['prenom']; // Retient le prenom de user pour toutes les pages

            if ($_SESSION['mail'] === 'admin@damso.fr') { // Si le mail de l'utilisateur est admin@damso.fr renvoie directement sur l'interface d'administrateur
                header('Location: admin.php');
            } else { // sinon renvoie sur la page des réservations effectuées
                header('Location: dashboard.php');
            }
            exit();
        } else { // Si user est vide ou si le mdp rentré ne correspond pas à celui stocké dans la bdd
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else { // si mail ou mdp est/sont vide/vides
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<fieldset>
<legend><h2>Connexion</h2></legend>

<?php if ($erreur): ?>
    <p style="color:red;"><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>

<form action="login.php" method="post" autocomplete="off">
    <label for="mail">Veuillez saisir votre Email : </label>
    <input type="email" id="mail" name="mail" placeholder="your.email@exemple.com" required><br><br>

    <label for="mdp">Veuillez saisir votre Mot de passe : </label>
    <input type="password" id="mdp" name="mdp" placeholder="Mot de passe" required><br><br>

    <button type="submit">Se connecter</button>
</form>
</fieldset>
<?php include 'includes/footer.php'; ?>