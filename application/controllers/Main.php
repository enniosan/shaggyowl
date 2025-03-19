<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct() {
		parent::__construct();

		#	setto le librerie necessarie
		$this->load->library('form_validation');
		$this->load->library('session'); 
	
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

	public function index(){
	
		$this->load->view('login');
	
	}


	/**
	 * Funzione per l'autenticazione dell'utente
	 *	-	verifica se l'utente esiste
	 *	-	se l'utente esiste, setta la sessione
	 * 	-	se l'utente esiste, restituisce anche la location di destinazione
	 *	-	se l'utente non esiste, restituisce un errore

	 * @return json dell'esito dell'operazione
	 */

	
	public function authLogin( ){
		
		if ($this->input->server('REQUEST_METHOD') !== 'POST') {

			error_log("tentativo di accesso con metodo non permesso");

            show_error('ES:: Invalid request method', 405);
			die;
        }

		#	leggo i dati in arrivo

		$body = [];
		$body['username'] = $this->input->post()['username'];
		$body['password'] = $this->input->post()['password'];

		#	gestione della validazione

		$this->form_validation->set_data($body);
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric|max_length[30]');
		$this->form_validation->set_rules('password', 'Password', 'required|max_length[30]');


		if ( $this->form_validation->run() ) {

			#	modelli e librerie
			
			$this->load->model('Admins_model'); 
			
			#	se i dati sono corretti
			$username = $body['username'];
            $password = $body['password'];

			#	verifica se l'utente esiste

			$admin = $this->Admins_model->getAdminUser($username, $password);


			#	se ho un utente 
			if ( !$admin ) {
				
				$loginError = true;

			}else{
				
				#	se l'utente esiste

				$last_access = date('Y-m-d H:i:s');
				
				$this->Admins_model->updateLastAccessTime ($admin -> id, $last_access);


				#	azioni
				if( json_decode( $admin->roleattributes, 1 ) ){
					$admin->roleattributes = json_decode( $admin->roleattributes );
				}else{

					error_log("Errore nel json_decode delle azioni");

					$admin->roleattributes = ["list" => "true"];
				}

				$this->session->set_userdata('logged_in',	true			);
				
				$this->session->set_userdata('id', 			$admin->id		);
				$this->session->set_userdata('name', 		$admin->name	);
				$this->session->set_userdata('email', 		$admin->email			);
				$this->session->set_userdata('username', 	$admin->username	);
				$this->session->set_userdata('last_access', $last_access	);
				
				$this->session->set_userdata('level', 		$admin->level 		);
				$this->session->set_userdata('role', 		$admin->rolename );
				$this->session->set_userdata('actions', 	$admin->roleattributes );
				
				
				#	default per l'anagrafica

				$this->session->set_userdata('p', 0 );
				$this->session->set_userdata('o', "id" );
				$this->session->set_userdata('v', "DESC" );
				$this->session->set_userdata('ipp', 10 );


				error_log("redirect verso app");
				redirect( "/app");
				die;
			}

		}


		#	uscita con errore
	
		if( $loginError ){

			$this->session->set_flashdata('errors', "Credenziali non corrette");
			$this->session->set_flashdata('username', $this->input->post('username'));
			
		}else{
			$this->session->set_flashdata('errors', "Errore generico");
		}

		redirect('/');	
		die;

	}

	public function logout() {

		$this->session->sess_destroy();
		redirect('/');
	}	
}
