<?php
class Admin extends Utilisateur {

    public function __construct($id, $nom_utilisateur, $email, $mot_de_passe_hash) {
        parent::__construct($id, $nom_utilisateur, $email, $mot_de_passe_hash, 'admin');
    }

    
    public function gererUtilisateurs() {
        // Logique pour gérer les utilisateurs
    }
}
?>
