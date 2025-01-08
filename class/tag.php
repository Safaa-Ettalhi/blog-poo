<?php
class Tag {
    private $id;
    private $nom;
    private $description;
    private $cree_le;
    private $modifie_le;

    public function __construct($nom, $description, $id = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->cree_le = date("Y-m-d H:i:s");
        $this->modifie_le = date("Y-m-d H:i:s");
    }

    public function ajouter($conn) {
       
            $query = "INSERT INTO tags (nom, description, cree_le, modifie_le) VALUES (:nom, :description, :cree_le, :modifie_le)";
            $stmt = $conn->prepare($query);

            // Associer les paramètres
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':cree_le', $this->cree_le);
            $stmt->bindParam(':modifie_le', $this->modifie_le);

            // Exécuter la requête
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        
    }
    public function modifier($conn, $nom, $description) {
        $this->nom = $nom;
        $this->description = $description;
        $this->modifie_le = date("Y-m-d H:i:s");
            $query = "UPDATE tags SET nom = :nom, description = :description, modifie_le = :modifie_le WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nom', $this->nom, PDO::PARAM_STR);
            $stmt->bindParam(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindParam(':modifie_le', $this->modifie_le, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
       
    }
    public function supprimer($conn) {
   
        $query = "DELETE FROM tags WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute(); 
    
}

    public static function recupererParId($conn, $id) {
       
            $query = "SELECT * FROM tags WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                return new Tag($data['nom'], $data['description'], $data['id']);

            } else {
                return null;
            }
        
    }

    // Méthode pour récupérer tous les tags
    public static function recupererTous($conn) {
       
            $query = "SELECT * FROM tags";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            $tags = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tags[] = new Tag($data['id'], $data['nom'], $data['description']);
            }
            return $tags;
        
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCreeLe() {
        return $this->cree_le;
    }

    public function getModifieLe() {
        return $this->modifie_le;
    }

    // Setters
    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setDescription($description) {
        $this->description = $description;
    }
}
?>