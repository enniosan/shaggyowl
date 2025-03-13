<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('form_validation');
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	
	*/

	public function index()
	{
		$this->load->view('login');
	
	}

	public function authLogin( ){

		#	connessione al db
		
		$this->load->model('Admins_model'); // Ensure that the file application/models/Admins_model.php exists and the class is defined as Admins_model
		
		$this->load->library('auth_library');
		
		#	verifica dei dati

		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[4]');
	
		#	se i dati non sono corretti

		if ($this->form_validation->run() == false) {

			$this->load->view('login');

		} else {

			#	se i dati sono corretti

			$username = $this->input->post('username');
			$password = $this->input->post('password');

			#	verifica se l'utente esiste

			$admin = $this->Admins_model->getAdminUser($username, $password);

			if ($admin !== false) {

				#	se l'utente esiste

				$this->session->set_userdata('logged_in',	true			);
				$this->session->set_userdata('email', 		$admin->email			);
				$this->session->set_userdata('name', 		$admin->name	);
				$this->session->set_userdata('email', 		$admin->email	);
				$this->session->set_userdata('id', 			$admin->id		);

				redirect('/app/main');
				
			} 
			
			
			
			#	se l'utente non esiste

			$this->session->set_flashdata('error', 'Credenziali non corrette');
			
			redirect( '/' );
		}
	}
}
