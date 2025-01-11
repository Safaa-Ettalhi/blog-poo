<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}

require '../../db.php';
require '../../class/tag.php'; 

$database = new Database();
$conn = $database->getConnection();

if (isset($_GET['id'])) {
    $tag_id = $_GET['id'];

    try {
   
        $tag = Tag::recupererParId($conn, $tag_id);

        if ($tag !== null) {
          
            if ($tag->supprimer($conn)) {
                header("Location: ../tags.php");
                exit();
            } else {
                echo "Erreur lors de la suppression du tag !";
            }
        } else {
            echo "Tag introuvable !";
        }
    } catch (Exception $e) {
        echo "Erreur : " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "ID de tag manquant !";
}
?>
