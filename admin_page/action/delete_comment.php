<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role_id'] != 2) {
    header('Location: ../login.php');
    exit();
}

require '../../db.php';
require '../../class/commentaire.php'; // Assurez-vous que le chemin est correct
$database = new Database();
$conn = $database->getConnection();
if (isset($_GET['id'])) {
    $commentId = $_GET['id'];

    // Utilisation de la méthode supprimer de la classe Commentaire
    $commentaire = new Commentaire($commentId);
    $commentaire->supprimer($conn);

    header('Location: ../comments.php');
    exit();
} else {
    die("ID du commentaire manquant.");
}
?>
