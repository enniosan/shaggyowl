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


	/**
	 * Funzione per l'autenticazione dell'utente
	 *
	 * @return void
	 */


	public function authLogin( ){

		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            show_error('ES:: Invalid request method', 405);
        }

		#	out
		

		$out = [
			"success" 	=> false,
			"message" 	=> "Operazione non conclusa",
			"location" 	=> false,
			"w" 		=> false,
		];


		#	leggo i dati in arrivo
		$body = json_decode( file_get_contents("php://input"), 1 );

		#	gestione della validazione

		$this->form_validation->set_data($body);
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'Password', 'required');

		#	imposto qui i dati dell'eventuale errore per due motivi
		#	il primo è che mi segnala se le operazioni sono andate avanti e non si è bloccato il codice prima
		#	la seconda è per evitare l'else e lasciare solo la condizione pulita
		#	tanto non devo segnalare all'utente informazioni che possano compromettere la sicurezza della login

		

		#	se i dati sono corretti...

		if ( $this->form_validation->run() ) {
			
			#	modelli e librerie
			
			$this->load->model('Admins_model'); 
			$this->load->library('auth_library');
			
			
			#	se i dati sono corretti
			$username = $body['username'];
            $password = $body['password'];

			#	verifica se l'utente esiste

			$admin = $this->Admins_model->getAdminUser($username, $password);


			if ($admin !== false) {

				#	se l'utente esiste

				$this->session->set_userdata('logged_in',	true			);
				$this->session->set_userdata('email', 		$admin->email			);
				$this->session->set_userdata('name', 		$admin->name	);
				$this->session->set_userdata('email', 		$admin->email	);
				$this->session->set_userdata('id', 			$admin->id		);

				$out['success'] = true;
				$out['message'] = "Credenziali corrette";
				$out['location'] = "/app";
				
				
				
			}else{
				
				$out['w'] 		= __LINE__;
			}
			
		}else{
			
			$out['w'] 		= __LINE__;

		}

		$out['message'] = "Credenziali non corrette";

		header('Content-Type: application/json');
		echo json_encode($out);
		die;
	}
}
