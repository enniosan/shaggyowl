<?php
class Admins_model extends CI_Model {

public function __construct() {
    $this->load->database(); 
}

public function getAdminUser($username, $password){
    
    $this->db->where('username', $username);
    $this->db->where('flag_enabled', 1);
    $this->db->limit(1);
    $query = $this->db->get('admins');

    // Check if the query returned any rows
    if ($query->num_rows() > 0) {
        $x = $query->first_row();  

        $verifica = password_verify($password, $x->password);
        
        if ($verifica === true) {
            return $x;
        }
    }

    return false;
}

// Metodo per aggiungere un nuovo utente
public function insert_utente($data) {
    return $this->db->insert('utenti', $data);
}
}