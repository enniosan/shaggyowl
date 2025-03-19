<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	public function __construct() {

		parent::__construct();
		
		$this->load->library('form_validation');
		$this->load->library('session');

		$this->load->library('UserCheck'); 
		$this->load->library('DataCheck'); 
	
		$this->load->model('Customers_model');
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

	/*	creo il crud per la tabella "anagrafica", con "nome, cognome, email, indirizzo, sesso, data_nascita"

	*/


	public function index()	{


		$this->usercheck->check_login(); 
		
		/*	creazione dell'interfaccia per gestire l'anagrafica e il crud	
			creerò la struttura e poi lancerò l'hidratation della tabella principale
			in modo da poter gestire il tutto utilizzando la minor quantità di codice possibile
		*/

		#	eseguo una count sui valori presenti
		$count = $this->Customers_model->count();

		# controllo e sanitizzo i dati in arrivo

		$ipps = [ 10, 20, 50 ];		#	elementi per pagina
		$campo = "id";				#	campo di ordinamento scelto per default


		/*	verifica dei dati
			oer testare i dati in input utilizzo delle regex
			è un metodo alternativo alla validazione dei dati di codeigniter
		
		*/

		# regole per la gestione degli input
		
		$rules = [
			"p" 	=> [ "regex" => "/^[0-9]+$/", "type" => "int", "min" => 0, "default" => 0 ],
			"o" 	=> [ "regex" => "/^[a-zA-Z_]+$/", "type" => "text", "in" => $this -> Customers_model -> get_fields(), "default" => $campo ],
			"v" 	=> [ "regex" => "/^[0-2]$/", "type" => "int",  "default" => 2 ],
			"ipp" 	=> [ "regex" => "/^(".implode("|", $ipps).")+$/", "type" => "int", "default" => 10 ]
		];

		$data = $this -> datacheck -> checkData( $this -> input -> get(), $rules );


		#	associo i dati sanificati alla sessione
		#	per poter fare le query e non doverli passare ogni volta


		foreach( $data as $k => $v ){

			$this -> session -> set_userdata( $k, $v );

		}
		
		
		#	variabili per la pagina

		$app_config = [

			"elementi_per_pagina" => $ipps,
			"elementi_totali" => $count,
			
			"default_elementi_per_pagina" => $this -> session -> userdata()['ipp'],
			"pagina_corrente" => $this -> session -> userdata()['p'],
			"ordinamento" 	=> $this -> session -> userdata()['o'],
			"verso" 		=> $this -> session -> userdata()['v'],
			"pagine_totali" => ceil($count / $this -> session -> userdata()['ipp'] ),
			"campi"			=> $this -> Customers_model -> get_fields(),

			#	campi non prettamente necessari in caso di schermi piccoli
			"hiddenables" 	=> ["email" => true, "indirizzo" => true]

		];

		#	verifico se l'utente ha il diritto di visualizzare i dati

	
		$content = [];

		if( $this -> session -> userdata()['actions']->list ){

			$content = $this -> Customers_model -> get_all( 
				$this -> session -> userdata()['ipp'],
				$this -> session -> userdata()['p'],
				$this -> session -> userdata()['o'],
				$this -> session -> userdata()['v']
			);

			//dd( $this -> session -> userdata() );

		}

		
		$data = [
			'title' 	=> 'Gestione Anagrafica',
            'user' 		=> $this -> session -> userdata,            
			'app_config'=> $app_config,
			'content'	=> $content,
		];

        // Carica il template con i dati dinamici


        $this->load->view('app/base', $data);
	}


	public function getUser($id){
		header("content-type: application/json");
		echo json_encode( $this -> Customers_model -> get_by_id( (int)$id ) ?? []	  ) ;
		die;
	}



	public function delete(  ){

		if ( $this->input->server('REQUEST_METHOD') !== 'POST') {

			error_log("tentativo di accesso con metodo non permesso");
            show_error('ES:: Invalid request method', 405);
			die;

        }

		#	cancello l'utente
		$this -> Customers_model -> delete( (int) $this -> input -> post('id') );

		redirect("/app");
		die;
	}


	public function updateUser(  ){


		if ( $this->input->server('REQUEST_METHOD') !== 'POST') {

			error_log("tentativo di accesso con metodo non permesso");

            show_error('ES:: Invalid request method', 405);
			die;
        }

		
		$formData = [
			"id" => $this->input->post('id'),
			"nome" => $this->input->post('nome'),
			"cognome" => $this->input->post('cognome'),
			"email" => $this->input->post('email'),
			"indirizzo" => $this->input->post('indirizzo'),
			"sesso" => $this->input->post('sesso')
		];
		
		$this->form_validation->set_data( $formData );

		$this->form_validation->set_rules('id', 'Id', 'required|numeric');
		$this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]|regex_match[/^([a-z ])+$/i]');
		$this->form_validation->set_rules('cognome', 'Cognome', 'required|max_length[100]|regex_match[/^([a-z ])+$/i]');
		$this->form_validation->set_rules('email', 'Email', 'required|max_length[100]|valid_email');
		$this->form_validation->set_rules('indirizzo', 'Indirizzo', 'max_length[200]');
		$this->form_validation->set_rules('sesso', 'Sesso', "required|max_length[1]|alpha");



		$out = [
			"status" => "failed",
			"message" => ""
		];

		if ( $this->form_validation->run() ) {

			$this -> Customers_model -> update( (int) $formData['id'], $formData );

			$out['message'] = "Dati modificati  correttamente";
			$out['status'] = "success";

		}else{
			
			$out['message'] = $this->form_validation->error_array();
			
		}	

		$this -> session -> set_flashdata( "esitoForm", $out );


		redirect("/app");
		die;
	}
	
}