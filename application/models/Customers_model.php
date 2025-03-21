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


    public function count( $filtri = false) {
        
        $query = $this->db->where("soft_deleted = 0"); 
        
        if( $filtri ){
        
            foreach( $filtri as $k => $v ){
                if( !empty( $v ) ){
                    $query = $query->like($k, $v);
                }
            }

        }
        
        return $query ->count_all_results($this->table);
    }


    #   getAnagrafiche
    #   funzione principale
    #   per ottenere le anagrafiche
    #   con la possibilità di filtrare e ordinare
    #   e gestione della paginazione

    public function getAnagrafiche( $ipp = 10, $pagina = 0, $filtri = false, $ordinamento = [] ) {
        
        #   costruisco la query

        $query = $this->db
                    ->limit($ipp, $pagina * $ipp)
                    ->where("soft_deleted = 0");

        if( $filtri ){

            foreach( $filtri as $k => $v ){
                if( !empty( $v ) ){
                    $query = $query->like($k, $v);
                }
            }

        }
        
        
        
        if( $ordinamento ){
            
            #   organizzo la sequenza
            
            $sequenza = [];

            foreach( $ordinamento as $campo => $valori ){

                if( $valori['seq']){

                    if( $valori['dir'] == 1 )
                        $valori['dir'] = "ASC";
                    else
                        $valori['dir'] = "DESC";

                    $sequenza[ $valori['seq'] ] = ["campo" => $campo, "dir" =>  $valori['dir'] ];
                    
                }
            }

            #   ordino per priorità temporale

            ksort( $sequenza );

            foreach( $sequenza as $ordine ){
            
                $query = $query->order_by($ordine['campo'], $ordine['dir'] );

            }

        }

        #   infine lancio la query

        $query = $this->db->get($this->table);

        return $query->result();
    }


    #   get_by_id
    #   funzione per ottenere un record specifico
    
    public function get_by_id($id) {

        $query = $this->db->get_where($this->table, [
            'id' => $id,
            'soft_deleted' => 0,
        
        ]);
        
        return $query->row();
    }


    #   Gestione del dato
    
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

}