<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class UserCheck {

    protected $CI;

    public function __construct() {
        
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->CI->load->helper('url');
        
    }

    public function check_login() {

        #   ulteriore controllo
        #   se non è loggato, lo rimando alla pagina di login

        if (!$this->CI->session->userdata('logged_in')) {

            error_log("redirect a main da UserCheck " );
            redirect('/');
        }

        error_log("check_login ok" );
    }


    
}