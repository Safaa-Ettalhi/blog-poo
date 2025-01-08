<?php
class Utilisateur {
    protected $id;
    protected $nom_utilisateur;
    protected $email;
    protected $mot_de_passe_hash;
    protected $role;

    public function __construct($id, $nom_utilisateur, $email, $mot_de_passe_hash, $role) {
        $this->id = $id;
        $this->nom_utilisateur = $nom_utilisateur;
        $this->email = $email;
        $this->mot_de_passe_hash = $mot_de_passe_hash;
        $this->role = $role;
    }

    public static function connecter($email, $mot_de_passe, $conn) {
        $stmt = $conn->prepare("SELECT id, nom_utilisateur, mot_de_passe_hash, role_id FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($mot_de_passe, $row['mot_de_passe_hash'])) {
                return new self($row['id'], $row['nom_utilisateur'], $email, $row['mot_de_passe_hash'], $row['role_id']);
            }
        }
        return null;
    }
    public static function deconnecter() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit();
    }

    public function getRole() {
        return $this->role;
    }

    public function getNomUtilisateur() {
        return $this->nom_utilisateur;
    }

    public function getId() {
        return $this->id;
    }
}
?>
