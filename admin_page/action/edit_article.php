<?php
require '../../db.php';
require '../../class/article.php';
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}

$utilisateur_id = intval($_SESSION['id']); 
$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id'])) {
    header('Location: ../articles.php');
    exit();
} else {
    $id_article = intval($_GET['id']);


    $article = new Article($id_article);

    $article_data = $article->getArticleById($conn, $id_article);

    $query_tag = "SELECT tags_id, nom FROM article_tags at 
                  JOIN tags t ON at.tags_id = t.id 
                  WHERE at.article_id = :id_article LIMIT 1";
    $stmt_tag = $conn->prepare($query_tag);
    $stmt_tag->execute(['id_article' => $id_article]);
    $current_tag = $stmt_tag->fetch(PDO::FETCH_ASSOC);

    
    if (!$article_data || !$current_tag) {
        header('Location: ../articles.php');
        exit();
    }

    $result_tags = $article->getAllTags($conn);

  
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titre = $_POST['titre'];
        $contenu = $_POST['contenu'];
        $image = $_POST['image'];
        $tag_id = $_POST['tag_id'];

     
        $article->modifier($conn, $titre, $contenu, $image, $tag_id);

        header('Location: ../articles.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'Article</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>
<body class="bg-gray-100 flex flex-col md:flex-row">
    <!-- Main Content -->
    <main class="w-full flex items-center justify-center h-screen bg-gray-100">
        <div class="w-1/3 bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-[#cb6ce6] mb-6 text-center">Modifier l'Article</h2>
            
            <form action="edit_article.php?id=<?php echo $id_article; ?>" method="POST" class="space-y-6">
                <!-- Titre -->
                <div>
                    <label for="titre" class="block text-lg font-medium text-gray-700">Titre</label>
                    <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($article_data['titre_article']); ?>"
                        class="w-full p-3 mt-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cb6ce6] transition ease-in-out duration-300" required>
                </div>

                <!-- Contenu -->
                <div>
                    <label for="contenu" class="block text-lg font-medium text-gray-700">Contenu</label>
                    <textarea id="contenu" name="contenu"
                        class="w-full p-3 mt-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cb6ce6] transition ease-in-out duration-300" required><?php echo htmlspecialchars($article_data['contenu_article']); ?></textarea>
                </div>

                <!-- URL de l'image -->
                <div>
                    <label for="image" class="block text-lg font-medium text-gray-700">URL de l'image</label>
                    <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($article_data['image_article']); ?>"
                        class="w-full p-3 mt-2 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cb6ce6] transition ease-in-out duration-300">
                </div>

                <!-- Tag -->
                <div>
                    <label for="tag_id" class="block text-lg font-medium text-gray-700">Tag</label>
                    <select name="tag_id" id="tags" 
                        class="w-full p-3 mt-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#cb6ce6] transition ease-in-out duration-300" required>
                        <!-- Tag actuel -->
                        <option value="<?php echo $current_tag['tags_id']; ?>"><?php echo htmlspecialchars($current_tag['nom']); ?></option>

                        <!-- Autres tags -->
                        <?php foreach ($result_tags as $tag): ?>
                            <option value="<?php echo $tag['id']; ?>" <?php echo ($tag['id'] == $current_tag['tags_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tag['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Bouton de soumission -->
                <div class="mt-6">
                    <button type="submit" class="w-full bg-[#cb6ce6] text-white font-semibold py-3 rounded-lg hover:bg-[#fbd8d5] transition ease-in-out duration-200 mb-6">
                        Mettre à jour l'article
                    </button>
                    <a href="../articles.php" class="font-bold text-[#e0a8a3] underline">Retour à la liste des articles</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
