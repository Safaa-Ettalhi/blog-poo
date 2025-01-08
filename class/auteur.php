<?php
require_once 'utilisateur.php';
class Auteur extends Utilisateur {

    public function __construct($id, $nom_utilisateur, $email, $mot_de_passe_hash) {
        parent::__construct($id, $nom_utilisateur, $email, $mot_de_passe_hash, 'auteur');
    }

    public static function inscription($nom_utilisateur, $email, $mot_de_passe) {
        $role_id = 1; 
     
        $database = new Database();
        $conn = $database->getConnection();

       
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email OR nom_utilisateur = :nom_utilisateur");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>L'email ou le nom d'utilisateur existe déjà.</div>";
        } else {
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            
            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe_hash, role_id) VALUES (:nom_utilisateur, :email, :mot_de_passe_hash, :role_id)");
            $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mot_de_passe_hash', $mot_de_passe_hash);
            $stmt->bindParam(':role_id', $role_id);

            if ($stmt->execute()) {
                session_start();
                $_SESSION['id'] = $conn->lastInsertId();
                $_SESSION['email'] = $email;
                $_SESSION['nom_utilisateur'] = $nom_utilisateur;
                $_SESSION['role_id'] = $role_id;

                if ($role_id == 2) {
                    header('Location: ./admin_page/dashboard.php');
                } else {
                    header('Location: ../page/article.php');
                }
                exit();
            } else {
                return "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>Erreur d'inscription : " . $stmt->errorInfo()[2] . "</div>";
            }
        }
    }
// Méthode pour modifier le profil
public function modifierProfil($nouveau_nom_utilisateur, $nouveau_email) {
        $database = new Database();
        $conn = $database->getConnection();

        // Vérifier si le nouveau nom d'utilisateur ou email existe déjà pour un autre utilisateur
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE (email = :email OR nom_utilisateur = :nom_utilisateur) AND id != :id");
        $stmt->bindParam(':email', $nouveau_email);
        $stmt->bindParam(':nom_utilisateur', $nouveau_nom_utilisateur);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>L'email ou le nom d'utilisateur existe déjà.</div>";
        } else {
            // Mettre à jour le profil
            $stmt = $conn->prepare("UPDATE utilisateurs SET nom_utilisateur = :nom_utilisateur, email = :email WHERE id = :id");
            $stmt->bindParam(':nom_utilisateur', $nouveau_nom_utilisateur);
            $stmt->bindParam(':email', $nouveau_email);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Mettre à jour les propriétés de l'objet
                $this->nom_utilisateur = $nouveau_nom_utilisateur;
                $this->email = $nouveau_email;

                return "<div class='text-green-500 p-3 mb-4 border border-green-300 bg-green-100 rounded'>Profil mis à jour avec succès.</div>";
            } else {
                return "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>Erreur lors de la mise à jour : " . $stmt->errorInfo()[2] . "</div>";
            }
        }
    }

    // Méthode pour modifier le mot de passe
    public function modifierMotDePasse($ancien_mot_de_passe, $nouveau_mot_de_passe) {
        $database = new Database();
        $conn = $database->getConnection();

        // Récupérer le mot de passe actuel de l'utilisateur
        $stmt = $conn->prepare("SELECT mot_de_passe_hash FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $mot_de_passe_hash = $stmt->fetchColumn();

        // Vérifier si l'ancien mot de passe est correct
        if (!password_verify($ancien_mot_de_passe, $mot_de_passe_hash)) {
            return "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>L'ancien mot de passe est incorrect.</div>";
        }

        // Mettre à jour le mot de passe avec un nouveau hash
        $nouveau_mot_de_passe_hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe_hash = :mot_de_passe_hash WHERE id = :id");
        $stmt->bindParam(':mot_de_passe_hash', $nouveau_mot_de_passe_hash);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "<div class='text-green-500 p-3 mb-4 border border-green-300 bg-green-100 rounded'>Mot de passe mis à jour avec succès.</div>";
        } else {
            return "<div class='text-red-500 p-3 mb-4 border border-red-300 bg-red-100 rounded'>Erreur lors de la mise à jour : " . $stmt->errorInfo()[2] . "</div>";
        }
    }
}
    
    


?>
