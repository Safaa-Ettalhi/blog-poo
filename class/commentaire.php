<?php 

class Commentaire {
    private $id;
    private $article_id;
    private $utilisateur_id;
    private $contenu;
    private $cree_le;
    private $modifie_le;

    public function __construct($id = null, $article_id = null, $utilisateur_id = null, $contenu = "") {
        $this->id = $id;
        $this->article_id = $article_id;
        $this->utilisateur_id = $utilisateur_id;
        $this->contenu = $contenu;
        $this->cree_le = date("Y-m-d H:i:s");
        $this->modifie_le = date("Y-m-d H:i:s");
    }

    // Ajouter un commentaire
    public function ajouter($conn) {
        $query = "INSERT INTO commentaires (article_id, utilisateur_id, contenu, cree_le, modifie_le) 
                  VALUES (:article_id, :utilisateur_id, :contenu, :cree_le, :modifie_le)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':contenu', $this->contenu, PDO::PARAM_STR);
        $stmt->bindParam(':cree_le', $this->cree_le, PDO::PARAM_STR);
        $stmt->bindParam(':modifie_le', $this->modifie_le, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Modifier un commentaire
    public function modifier($conn, $commentaire_content) {
        $this->contenu = $commentaire_content;
        $this->modifie_le = date("Y-m-d H:i:s");
        $query = "UPDATE commentaires SET contenu = :contenu, modifie_le = :modifie_le WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':contenu', $this->contenu, PDO::PARAM_STR);
        $stmt->bindParam(':modifie_le', $this->modifie_le, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Supprimer un commentaire
    public function supprimer($conn) {
        $query = "DELETE FROM commentaires WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Récupérer tous les commentaires d'un article
    public static function getComments($conn, $article_id) {
        $query = "SELECT c.*, u.nom_utilisateur FROM commentaires c LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id WHERE c.article_id = :article_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
