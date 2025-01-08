<?php
class Article {
    private $id;
    private $titre;
    private $contenu;
    private $url_image;
    private $cree_le;
    private $modifie_le;
    private $utilisateur_id; 
    private $commentaires; 
    private $likes;       

    public function __construct($id, $titre = "", $contenu = "", $url_image = "", $utilisateur_id = null) {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->url_image = $url_image;
        $this->cree_le = date("Y-m-d H:i:s");
        $this->modifie_le = date("Y-m-d H:i:s");
        $this->utilisateur_id = $utilisateur_id;
        $this->commentaires = [];
        $this->likes = [];
    }

    public function creer($conn) {
        $query = "INSERT INTO articles (utilisateur_id, titre, contenu, url_image, cree_le, modifie_le) 
                  VALUES (:utilisateur_id, :titre, :contenu, :url_image, :cree_le, :modifie_le)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(':titre', $this->titre, PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $this->contenu, PDO::PARAM_STR);
        $stmt->bindParam(':url_image', $this->url_image, PDO::PARAM_STR);
        $stmt->bindParam(':cree_le', $this->cree_le, PDO::PARAM_STR);
        $stmt->bindParam(':modifie_le', $this->modifie_le, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function modifier($conn, $titre, $contenu, $url_image, $tags_id) {
    $sql = "UPDATE articles SET titre = :titre, contenu = :contenu, url_image = :url_image WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
    $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
    $stmt->bindParam(':url_image', $url_image, PDO::PARAM_STR);
   // $stmt->bindParam(':tags_id', $tags_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
    $stmt->execute();

    $sql_tag = "UPDATE article_tags SET tags_id = :tags_id WHERE article_id = :id";
    $stmt = $conn->prepare($sql_tag);
    $stmt->bindParam(':tags_id', $tags_id);
    $stmt->bindParam(':id', $this->id);
    $stmt->execute();


}

    public function supprimer($conn) {
        $query = "DELETE FROM articles WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }
    

    public function getArticleById($conn, $articleId) {
    $sql = "
        SELECT 
            a.id AS article_id,
            a.titre AS titre_article,
            a.contenu AS contenu_article,
            a.Url_image AS image_article,
            a.cree_le AS cree_le,
            u.nom_utilisateur AS nom_utilisateur,
            GROUP_CONCAT(t.nom SEPARATOR ', ') AS tags
        FROM articles a
        LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id
        LEFT JOIN article_tags at ON a.id = at.article_id
        LEFT JOIN tags t ON at.tags_id = t.id
        WHERE a.id = :id
        GROUP BY a.id, u.nom_utilisateur
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $articleId, PDO::PARAM_INT);
    $stmt->execute();

    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        echo "Aucun article trouvé pour l'ID " . $articleId . "<br>";
        return false; // Aucun article trouvé
    }

    return $article;
}

public function ajouterCommentaire($conn, $utilisateur_id, $contenu) {
        $commentaire = new Commentaire(null, $this->id, $utilisateur_id, $contenu);
        $commentaire->ajouter($conn);
        $this->commentaires[] = $commentaire; // Ajout à la collection
    }

    public function getCommentaires($conn) {
        if (empty($this->commentaires)) {
            $this->commentaires = Commentaire::getComments($conn, $this->id);
        }
        return $this->commentaires;
    }

    public function ajouterLike($conn, $utilisateur_id) {
        $like = new Like(null, $utilisateur_id, $this->id);
        if (!$like->aLike($conn)) {
            $like->ajouter($conn);
            $this->likes[] = $like; // Ajout à la collection
        }
    }

    public function getLikes($conn) {
        if (empty($this->likes)) {
            $this->likes = Like::getNombreLikes($conn, $this->id);
        }
        return $this->likes;
    }

    //  récupérer tous les tags
    public function getAllTags($conn) {
        $query = "SELECT * FROM tags";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllArticles($conn, $utilisateur_id) {
    $query = "SELECT a.id, a.titre, a.contenu, a.Url_image, u.nom_utilisateur, 
                     GROUP_CONCAT(t.nom SEPARATOR ', ') AS tags
              FROM articles a
              LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id
              LEFT JOIN article_tags at ON a.id = at.article_id
              LEFT JOIN tags t ON at.tags_id = t.id
              WHERE a.utilisateur_id = :utilisateur_id
              GROUP BY a.id, a.titre, a.contenu, a.Url_image, u.nom_utilisateur";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
public function getAllArticlesDisponibles($conn) {
        $query = "SELECT a.id, a.titre, a.contenu, a.Url_image, u.nom_utilisateur, 
                     GROUP_CONCAT(t.nom SEPARATOR ', ') AS tags
              FROM articles a
              LEFT JOIN utilisateurs u ON a.utilisateur_id = u.id
              LEFT JOIN article_tags at ON a.id = at.article_id
              LEFT JOIN tags t ON at.tags_id = t.id
              GROUP BY a.id, a.titre, a.contenu, a.Url_image, u.nom_utilisateur";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}


?>
