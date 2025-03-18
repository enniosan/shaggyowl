<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	public function __construct() {

		parent::__construct();
		
		$this->load->library('form_validation');
		$this->load->library('session'); 
		$this->load->library('UserCheck'); 
	
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

		#	faccio un controllo sui dati
		#	carico il model della tabella utenti
		//$this->load->model('Customers_model');

		#	eseguo una count sui valori presenti
		$count = $this->Customers_model->count();

		#	$customers = $this->Customers_model->g\et_all();

		# controllo e sanitizzo i dati in arrivo

		$ipps = [ 10, 20, 50 ];		#	elementi per pagina
		$campo = "id";				#	campo di ordinasmento

		#	la pagina non dev'essere inferiore a 0

		if( $this -> input -> get('p') ){

			$pagina = (int) $this -> input -> get('p') -1;
			
			if( $pagina < 0 ){

				$pagina = 0;
			}

			$this -> session -> set_userdata('pagina', $pagina );
		}

		
		#	il campo per cui si richiede l'ordinamento deve essere presente nella tabella

		if( $this -> input -> get('o') ){
			
			#	verifica se il campo è nella tabella
			$campi = $this -> Customers_model -> get_fields();
			
			$campo = "id";	#	default

			if( in_array( $this -> input -> get('o'), $campi ) ){

				$campo = $this -> input -> get('o');
			
			}

			$this -> session -> set_userdata('campo', $campo);
		}


		#	il verso dell'ordinamento deve essere ASC o DESC

		if( $this -> input -> get('v') ){

			$verso = (int)strtoupper( $this -> input -> get('v') );
			
			$this -> session -> set_userdata('verso', $verso );
			
		}

		#	il numero di elementi per pagina deve essere uno dei valori possibili

		if( $this -> input -> get('ipp') ){

			$ipp = 10;	#	default

			if( in_array( (int) $this -> input -> get('ipp') , $ipps ) ){

				$ipp = (int) $this -> input -> get('ipp');
				
			}		
			
			$this -> session -> set_userdata('ipp', $ipp );
		}


		#	la vista è pronta, manca solo il contenuto

		$app_config = [

			"elementi_per_pagina" => $ipps,
			"elementi_totali" => $count,
			
			"default_elementi_per_pagina" => $this -> session -> userdata()['ipp'],
			"pagina_corrente" => $this -> session -> userdata()['pagina'],
			"pagine_totali" => ceil($count / $this -> session -> userdata()['ipp'] ),
			"ordinamento" 	=> $this -> session -> userdata()['campo'],
			"verso" 		=> $this -> session -> userdata()['verso'],
			"campi"			=> $this -> Customers_model -> get_fields(),

			#	campi non prettamente necessari in caso di schermi piccoli
			"hiddenables" 	=> ["email" => true, "indirizzo" => true]

		];


		#	verifico se l'utente ha il diritto di visualizzare i dati

		$content = [];

		if( $this -> session -> userdata()['actions']->list ){

			$content = $this -> Customers_model -> get_all( 
				$this -> session -> userdata()['ipp'],
				$this -> session -> userdata()['pagina'],
				$this -> session -> userdata()['campo'],
				$this -> session -> userdata()['verso']
			);

		}

		#	prendo il contenuto

		#	creo la tabellagenero i dati per la vista
		$data = [
			'title' 	=> 'Gestione Anagrafica',
            'user' 		=> $this -> session -> userdata,            
			'app_config'=> $app_config,
			'content'	=> $content,
		];

        // Carica il template con i dati dinamici

        $this->load->view('app/base', $data);
	}


	public function delete(  ){

		dd( $this->input->server('REQUEST_METHOD'), $this -> input );



		if ( $this->input->server('REQUEST_METHOD') !== 'POST') {

			error_log("tentativo di accesso con metodo non permesso");

            show_error('ES:: Invalid request method', 405);
			die;
        }


		dd( $id );


		#	cancello l'utente
		$this -> Customers_model -> delete( (int) $id );

		redirect("/app");
		die;
	}
}