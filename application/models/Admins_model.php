<?php
class Admins_model extends CI_Model {

public function __construct() {
    $this->load->database(); 
}

public function getAdminUser($username, $password){
    
    $this->db->where('username', $username);
    $this->db->where('flag_enabled', 1);
    $this->db->join('roles', 'roles.id = admins.id_role');
    
    $this->db->select('
        admins.*, 
        roles.id as level, 
        roles.name as rolename, 
        roles.attributes as roleattributes
    ');

    $this->db->limit(1);

    $query = $this->db->get('admins');

    #   controllo risultato

    if ($query->num_rows() > 0) {
        $x = $query->first_row();  

        $verifica = password_verify($password, $x->password);
        
        if ($verifica === true) {
            return $x;
        }
    }

    return false;
}


public function updateLastAccessTime( $id , $last_access ) {
    $this->db->where('id', $id);
    $this->db->update('admins', array('last_access' => $last_access));
}

// Metodo per aggiungere un nuovo utente
public function insert_utente($data) {
    return $this->db->insert('utenti', $data);
}
}