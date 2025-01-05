<?php
require 'db.php';
require 'class/utilisateur.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);

    if (!empty($email) && !empty($mot_de_passe)) {
        $database = new Database();
        $conn = $database->getConnection();

        // Utilisation de la méthode statique pour connecter l'utilisateur
        $utilisateur = Utilisateur::connecter($email, $mot_de_passe, $conn);

        if ($utilisateur) {
            session_start();
            $_SESSION['id'] = $utilisateur->getId();
            $_SESSION['email'] = $email;
            $_SESSION['nom_utilisateur'] = $utilisateur->getNomUtilisateur();
            $_SESSION['role_id'] = $utilisateur->getRole();

            if ($utilisateur->getRole() == 2) {
                header('Location: ./admin_page/dashboard.php');
            } else {
                header('Location: ../page/article.php');
            }
            exit();
        } else {
            $message = "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>Email ou mot de passe incorrect.</div>";
        }
    } else {
        $message = "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>Veuillez remplir tous les champs.</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<body class="bg-cover bg-center bg-[url('/assets/bg.jpg')] py-16 py-12 flex justify-center items-center" >

    <div class="w-full max-w-lg border-2 border-[#cb6ce6] bg-white\50 p-8 rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-center text-[#cb6ce6] mb-6">Se Connecter</h2>

        <!-- Afficher les messages -->
        <?php if (!empty($message)): ?>
            <div class="mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="#" method="POST">
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-[#cb6ce6] font-medium">Email</label>
                <input type="email" id="email" name="email" placeholder="Votre email" class="w-full px-4 py-2 mt-2 border border-[#cb6ce6] bg-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cb6ce6]" required>
            </div>

            <!-- Mot de passe -->
            <div class="mb-4">
                <label for="mot_de_passe" class="block text-[#cb6ce6] font-medium">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Votre mot de passe" class="w-full px-4 py-2 mt-2 border border-[#cb6ce6] bg-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cb6ce6]" required>
            </div>

            <!-- Bouton de connexion -->
            <button type="submit" name="submit" class="w-full py-3 bg-[#cb6ce6] text-white font-semibold rounded-lg shadow-md hover:bg-[#fbd8d5] ">
                Se Connecter
            </button>
            <div class="flex items-center justify-between mt-8">
                <p class="text-gray-600">Don't have an account?</p>
                <a href="inscription.php" class="text-[#cb6ce6] font-medium hover:underline">
                    Register
                </a>
            </div>

        </form>
    </div>
</body>
</html>