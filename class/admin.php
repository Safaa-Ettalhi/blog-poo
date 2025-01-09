<?php
require_once 'utilisateur.php';
class Admin extends Utilisateur {

    public function __construct($id, $nom_utilisateur, $email, $mot_de_passe_hash) {
        parent::__construct($id, $nom_utilisateur, $email, $mot_de_passe_hash, 'admin');
    }

    
    public function afficherUtilisateurs($conn, $user_id = null) {
        try {
            $query = "SELECT utilisateurs.id, nom_utilisateur, email, roles.nom_role, role_id
                      FROM utilisateurs
                      LEFT JOIN roles ON utilisateurs.role_id = roles.id";
            if ($user_id !== null) {
                $query .= " WHERE utilisateurs.id = :id";
            }

            $stmt = $conn->prepare($query);

            if ($user_id !== null) {
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $user_id !== null ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        }
    }

    public function modifierUtilisateur($conn, $user_id, $new_role_id) {
        try {
            $update_query = $conn->prepare("UPDATE utilisateurs SET role_id = :role_id WHERE id = :id");
            $update_query->bindParam(':role_id', $new_role_id, PDO::PARAM_INT);
            $update_query->bindParam(':id', $user_id, PDO::PARAM_INT);
            $update_query->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }
    public function supprimerUtilisateur($conn, $user_id) {
    try {
        // Vérifier si l'utilisateur existe avant de tenter la suppression
        $check_user = $conn->prepare("SELECT id FROM utilisateurs WHERE id = :id");
        $check_user->bindParam(':id', $user_id, PDO::PARAM_INT);
        $check_user->execute();

        if ($check_user->rowCount() == 0) {
            throw new Exception("Utilisateur introuvable.");
        }

        // Supprimer l'utilisateur
        $delete_user = $conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $delete_user->bindParam(':id', $user_id, PDO::PARAM_INT);
        $delete_user->execute();
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la suppression de l'utilisateur : " . $e->getMessage());
    }
}

}
?>
