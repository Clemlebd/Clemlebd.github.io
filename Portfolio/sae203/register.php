<?php
session_start();
include 'includes/header.php';
require_once 'config/db.php'; // Require pour ne pas appeller 2 fois le fichier

$erreur = ''; // Initialisation pour message d'erreur
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = htmlspecialchars(trim($_POST['mail'] ?? ''));
    $mdp = $_POST['mdp'] ?? '';
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));

    if (!empty($mail) && !empty($mdp) && !empty($nom) && !empty($prenom)) { // Vérifie que tt les champs ont été remplis
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) { // Vérifie la validité de l'email
            $erreur = "Adresse email invalide.";
        } elseif (!preg_match("/^[A-Za-zÀ-ÿ\- ]{2,50}$/", $nom) || !preg_match("/^[A-Za-zÀ-ÿ\- ]{2,50}$/", $prenom)) {// Vérifie que le nom est un nom valide pas de caractère autre que lettres, espaces et tirets et longueur raisonnable
            $erreur = "Le nom et le prénom doivent contenir uniquement des lettres.";
        } elseif (strlen($mdp) < 6) { // Vérifie la longueur du mdp
            $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
        } else {
            $stmt = $pdo->prepare("SELECT id_utilisateur FROM sae203_utilisateur WHERE mail = ?"); 
            $stmt->execute([$mail]);

            if ($stmt->rowCount() > 0) {// Vérifie que aucun compte existe avec cette email
                $erreur = "Un compte existe déjà avec cet email.";
            } else {
                $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT); // Hash le mdp avant de le stocker

                $stmt = $pdo->prepare("INSERT INTO sae203_utilisateur (mail, mdp, nom, prenom) VALUES (?, ?, ?, ?)");
                $stmt->execute([$mail, $mdp_hash, $nom, $prenom]); // stocke l'utilisateur dans la bdd

                // Envoi du mail
                $to = $mail;// mail du compte
                $subject = "Confirmation de votre compte"; // objet du msg
                $message = "Bonjour $prenom $nom,\n\nVotre compte a bien été créé.\n\n <br>L'équipe Damso.";
                $entete="MIME-Version : 1.0 \r\n";
                $entete.='Content-type: text/html; charset="utf-8" '."\r\n";
                $entete.="From: 'no-reply@damso.fr' \r\n";
                $entete.="X-Mailer: PHP/".phpversion();
                
                if (mail($to, $subject, $message, $entete)) {// essaye d'envoyer le mail si oui :
                    $success = "Inscription réussie. Un email de confirmation a été envoyé.";
                } else { //si non :
                    $success = "Inscription réussie. L'email n'a pas pu être envoyé.";
                }
            }
        }
    } else {
        $erreur = "Tous les champs sont requis.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<h2>Inscription</h2>

<?php if ($erreur) { ?>
    <p style="color:red;"><?= htmlspecialchars($erreur) ?></p> <!-- Affiche la variable Erreur -->
<?php } elseif ($success) { ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p> <!-- Affiche la variable success -->
<?php } ?>

<a href="index.php">Retour à l'accueil</a>

<?php include 'includes/footer.php'; ?>