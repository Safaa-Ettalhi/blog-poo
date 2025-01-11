<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header('Location: login.php');
    exit();
}

require '../../db.php';
require_once '../../class/admin.php'; 

$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['id'])) {
    header('Location: ../users.php');
    exit();
}

$user_id = (int) $_GET['id'];

try {
    $admin = new Admin($_SESSION['id'], $_SESSION['nom_utilisateur'], $_SESSION['email'], ''); // Créer une instance d'Admin
    $admin->supprimerUtilisateur($conn, $user_id); // Utiliser la méthode de suppression


    header('Location: ../users.php');
    exit();
} catch (Exception $e) {
    
    echo $e->getMessage();
    exit();
}
?>
