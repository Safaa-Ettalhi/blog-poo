<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../../login.php');
    exit();
}

require '../../db.php';
require '../../class/article.php'; 
$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];

    try {
        
        $article = new Article($article_id);
        $articleData = $article->getArticleById($conn, $article_id);

        if (!$articleData) {
            echo "L'article n'existe pas dans la base de données.";
            exit();
        }

        $article->supprimer($conn);

        header("Location: ../articles.php");
        exit();

    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . $e->getMessage();
        exit();
    }

} else {
    echo "ID de l'article manquant.";
    exit();
}
?>