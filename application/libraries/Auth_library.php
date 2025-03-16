<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_library {

    protected $CI;

    public function __construct() {
        
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->CI->load->helper('url');
    }

    public function check_login() {
        if (!$this->CI->session->userdata('logged_in')) {
            
            dd("ECCACHIO");
            redirect('login');
        }
    }
}