<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthHook {

    /*  enniosan
        #   creo un hook adatto per la verifica del login
        
    */


    public function check_login() {
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->helper('url');

        // Controlla se l'utente è loggato
        if (!$CI->session->userdata('logged_in')) {
            // Se l'utente non è loggato e sta cercando di accedere a una pagina protetta
            $protected_routes = array('app', 'app/create', 'app/store', 'app/edit', 'app/update', 'app/delete');
            $current_route = $CI->uri->segment(1);

            if (in_array($current_route, $protected_routes)) {
                redirect('login');
            }
        }
    }
}