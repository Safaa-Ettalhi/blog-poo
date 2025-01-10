<?php
session_start();
require '../../db.php';
require '../../class/article.php'; 

$database = new Database();
$conn = $database->getConnection();


$article = new Article(0);
$tags = $article->getAllTags($conn); 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['titre'];
    $contenu = $_POST['contenu'];
    $tags_id = isset($_POST['tags']) ? $_POST['tags'] : null; 
    $Url_image = $_POST['Url_image'];
    
   
    if (empty($titre) || empty($contenu)) {
        $error_message = "Le titre et le contenu sont obligatoires.";
    } else {
        try {
            $article = new Article(null, $titre, $contenu, $Url_image, $_SESSION['id']);
            $article->creer($conn); 

            
            $article_id = $conn->lastInsertId();

            if ($tags_id) {
                $stmt_tags = $conn->prepare("INSERT INTO article_tags (article_id, tags_id) VALUES (:article_id, :tags_id)");
                $stmt_tags->bindParam(':article_id', $article_id, PDO::PARAM_INT);
                $stmt_tags->bindParam(':tags_id', $tags_id, PDO::PARAM_INT);
                $stmt_tags->execute();
            }

            header("Location: ../articles.php");
            exit();
        } catch (Exception $e) {
            $error_message = "Erreur lors de l'ajout de l'article : " . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Article</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 justify-center items-center flex flex-col md:flex-row">


    <!-- Main Content -->
    <body class="bg-gray-100 h-screen flex justify-center items-center">

    <div class="w-full max-w-lg border-2 border-[#cb6ce6] bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-3xl font-bold text-center text-[#cb6ce6] mb-6">Ajouter un Article</h2>

        <!-- Affichage des erreurs -->
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout d'article -->
        <form action="ajouter_article.php" method="POST">
            
            <!-- Titre -->
            <div class="mb-6">
                <label for="titre" class="block text-gray-700 text-lg font-medium">Titre</label>
                <input type="text" id="titre" name="titre" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>

            <!-- Contenu -->
            <div class="mb-6">
                <label for="contenu" class="block text-gray-700 text-lg font-medium">Contenu</label>
                <textarea id="contenu" name="contenu" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" rows="6" required></textarea>
            </div>

            <!-- Tags -->
            <div class="mb-6">
                <label for="tags_id" class="block text-gray-700 text-lg font-medium">Tags</label>
                <select name="tags" id="tags_id" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">Sélectionner un tag</option>
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?php echo $tag['id']; ?>"><?php echo $tag['nom']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- URL de l'image -->
            <div class="mb-6">
                <label for="Url_image" class="block text-gray-700 text-lg font-medium">Image URL</label>
                <input type="text" id="Url_image" name="Url_image" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            
            <div class="mt-6" >
                <button type="submit" class="w-full bg-[#cb6ce6] text-white font-semibold py-3 rounded-lg hover:bg-[#fbd8d5] transition ease-in-out duration-200 mb-6 ">
                    valider
                </button>
                <a href="../articles.php" class="font-bold text-[#e0a8a3] underline  ">Retour à la liste des articles </a>
            </div>
        </form>

    </div>
    </main>
</body>

</html>
