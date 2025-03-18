<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthHook {

    /*  enniosan
        #   creo un hook adatto per la verifica del login ad ogni chiamata
        
    */

    public function check_login() {

        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->helper('url');

        // Controlla se l'utente è loggato

        if (!$CI->session->userdata('logged_in')) {

            // Se l'utente non è loggato e sta cercando di accedere a una pagina protetta
            
            $current_route = $CI->uri->segment(1);

            if (strpos($current_route, 'app') === 0) {
                
                error_log("utente non loggato tenta di accedere a path protetto:  redirect verso home da AuthHook");
                redirect('/');
            }
        }
        
        error_log("auth hook ok  " );

        return false;
    }
}