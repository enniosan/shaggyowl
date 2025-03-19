<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers_model extends CI_Model {

    private $table = 'customers';

    # determino alcuni campi che non devono essere comunicati all'utente

    private $campi_da_ignorare = [
        'soft_deleted' ,
        'ts_create' ,
        'ts_modify' ,
        'ts_delete' ,        
    ];

    

    public function __construct() {
        $this->load->database();
    }


    public function count() {
        return $this->db->where("soft_deleted = 0") ->count_all_results($this->table);
    }


    #   ritorna tutti i dati in base ai parametri configurati in sessione

    public function get_all( $ipp = 10, $pagina = 0, $campo = 'id', $dir = 1) {
        
        $verso = "ASC";
        if( $dir == 2 )
            $verso = "DESC";

        $query = $this->db
                    ->order_by($campo, $verso)
                    ->limit($ipp, $pagina * $ipp)
                    ->where("soft_deleted = 0");

        $query = $this->db->get($this->table);


        return $query->result();
    }

    public function get_by_id($id) {

        $query = $this->db->get_where($this->table, [
            'id' => $id,
            'soft_deleted' => 0,
        
        ]);
        
        return $query->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
    
    #   imposta il flag soft_delete in modo da togliere il record dalla vista ma mantenere il dato nel db

    public function delete($id) {
        $this->db->where('id', $id);
        $data = [
            'soft_deleted' => "1",
             "ts_delete" => date("Y-m-d H:i:s") 
        ];
        
        return $this->db->update($this->table, $data);
    }

    #   prende i campi
    public function get_fields() {

        $fields = $this->db->list_fields($this->table);

        return array_diff( $fields, $this->campi_da_ignorare );

    }

    
    // generato automaticamente
    public function fields() {
        return array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'nome' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'cognome' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'sesso' => array(
                'type' => 'CHAR',
                'constraint' => '1',
            ),
            'indirizzo' => array(
                'type' => 'TEXT',
            ),
            'soft_deleted' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
            ),
        );
    }


}