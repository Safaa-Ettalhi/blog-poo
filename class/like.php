<?php
class Like {
    private $id;
    private $utilisateur_id;
    private $article_id;

    public function __construct($id = null, $utilisateur_id = null, $article_id = null) {
        $this->id = $id;
        $this->utilisateur_id = $utilisateur_id;
        $this->article_id = $article_id;
    }

    // Ajouter un like
    public function ajouter($conn) {
        $query = "INSERT INTO likes (article_id, utilisateur_id) VALUES (:article_id, :utilisateur_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Supprimer un like
    public function supprimer($conn) {
        $query = "DELETE FROM likes WHERE article_id = :article_id AND utilisateur_id = :utilisateur_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Vérifier si un utilisateur a aimé un article
    public function aLike($conn) {
        $query = "SELECT * FROM likes WHERE article_id = :article_id AND utilisateur_id = :utilisateur_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':article_id', $this->article_id, PDO::PARAM_INT);
        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Compter le nombre de likes pour un article
    public static function getNombreLikes($conn, $article_id) {
        $query = "SELECT COUNT(*) AS likes_count FROM likes WHERE article_id = :article_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['likes_count'];
    }
}
?>
