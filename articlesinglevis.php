<?php
session_start();
include("./db.php");
include("./class/article.php");
include("./class/commentaire.php");
include("./class/like.php");
// Vérification si 'id' est bien présent dans l'URL et n'est pas vide
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $articleId = intval($_GET['id']); 
    
    $db = new Database();
    $conn = $db->getConnection();

    // Récupérer l'article
    $article = new Article($articleId);
    $articleData = $article->getArticleById($conn, $articleId);
    if (!$article) {
 
        header("Location: ./index.php");
        exit();
    }

    // Gestion des likes
    $like = new Like(null, null, $articleId);
    $likes_count = Like::getNombreLikes($conn, $articleId);
    //commentaire
    $comments_result = Commentaire::getComments($conn, $articleId);

    

} else {
    
    header("Location: ./index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($articleData['titre_article']); ?> - Article</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-6 py-12">
        <!-- Retour à la liste -->
        <a href="./index.php" class="text-[#cb6ce6] hover:underline mb-6 inline-block text-lg font-semibold">← Retour aux articles</a>

        <!-- Contenu de l'article -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Image de l'article -->
            <img src="<?php echo htmlspecialchars($articleData['image_article']); ?>" alt="Image de l'article" class="w-full h-64 object-cover rounded-lg mb-6">

            <!-- Tags -->
            <p class="text-sm text-gray-500 mb-2"><?php echo $articleData['tags']; ?></p>

            <!-- Titre -->
            <h1 class="text-3xl font-semibold text-[#cb6ce6] mb-4"><?php echo htmlspecialchars($articleData['titre_article']); ?></h1>

            <!-- Contenu -->
            <div class="text-gray-700 leading-relaxed mb-6"><?php echo $articleData['contenu_article']; ?></div>

            <!-- Auteur et date -->
            <p class="text-gray-500 text-sm mb-6">Publié par <span class="font-semibold"><?php echo htmlspecialchars($articleData['nom_utilisateur']); ?></span> le <?php echo date('d/m/Y', strtotime($articleData['cree_le'])); ?></p>

            <!-- Likes -->
            <form method="POST" action="" class="mb-6">
                <button type="submit" name="like" class="flex items-center space-x-2 text-2xl">
                    <i class="fas <?php echo 'fa-heart text-gray-300'; ?> hover:text-red-600 transition-colors"></i>
                    <span class="text-sm text-gray-500"><?php echo $likes_count; ?> J'aime</span>
                </button>
            </form>

         

            <!-- Commentaires -->
            <h2 class="text-xl font-semibold mb-4">Commentaires</h2>
            <?php foreach ($comments_result as $comment): ?>
                        <div class="bg-white p-6 rounded-lg shadow-md mb-6 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm text-gray-500">
                                    <span class="font-semibold text-[#cb6ce6]"><?php echo htmlspecialchars($comment['nom_utilisateur']); ?></span> 
                                    le <?php echo date('d/m/Y', strtotime($comment['cree_le'])); ?>
                                </p>

                            </div>

                            <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($comment['contenu'])); ?></p>
                        </div>
                        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
