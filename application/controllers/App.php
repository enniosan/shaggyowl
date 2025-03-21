<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	var $campi_disponibili;

	public function __construct() {

		parent::__construct();
		
		$this->load->library('form_validation');
		$this->load->library('session');

		$this->load->library('UserCheck'); 
		$this->load->library('DataCheck'); 
	
		$this->load->model('Customers_model');
	
		$this -> campi_disponibili = $this -> Customers_model -> get_fields();

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


	public function debug(){
		dd( $this -> session -> userdata() );
	}

	public function index()	{

		$this->usercheck->check_login(); 
		


		# controllo e sanitizzo i dati in arrivo

		$ipps = [ 10, 20, 50 ];		#	elementi per pagina
		$campo = "id";				#	campo di ordinamento default

		#	verifico filtri e ordinamenti
		#	in caso non li abbia, creo la struttura
		
		$this -> prepareOrdinamento();
		$this -> prepareFiltri();
		
		#	imposta l'ordinamento e i filtri in base a cosa viene passato nella get
		#	costruisco la struttura

		$this -> setupInput( $this -> input -> get() );

		#	eseguo una count sui valori presenti
		$count = $this->Customers_model->count( $this -> session -> userdata()['filtri'] );

		#	estraggo i dati
		$content = [];

		if( $this -> session -> userdata()['actions']->list ){

			$content = $this -> Customers_model -> getAnagrafiche( 
				
				$this -> session -> userdata()['ipp'],
				$this -> session -> userdata()['p'],
				$this -> session -> userdata()['filtri'],
				$this -> session -> userdata()['ordinamento'],
				//$this -> session -> userdata()['sequenza_ordinamento'],

			);
	
		}
		
		 /*	verifica dei dati
			oer testare i dati in input utilizzo delle regex
			è un metodo alternativo alla validazione dei dati di codeigniter
		
		*/

		/*

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

		*/

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

		$data = [
			'title' 	=> 'Gestione Anagrafica',
            'user' 		=> $this -> session -> userdata,            
			'app_config'=> $app_config,
			'content'	=> $content,
		];


		#	carica la vista
		
		$view_content = $this->load->view('app/base', $data, true);
		$compressed_content = strtr($view_content, [" " => " ", "\r" => "", "\n" => "", "\t" => ""]);
		echo $compressed_content;
		
		die;

	}


	public function getUser($id, $tutti_i_campi = true){
		header("content-type: application/json");

		$dati = $this -> Customers_model -> get_by_id( (int)$id ) ?? [];

		if( !$tutti_i_campi ){
			$dati = array_intersect_key( $dati, array_flip( $this -> campi_disponibili ) );
		}

		echo json_encode( $dati ) ;
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
			"id" => $this->input->post('id', true),
			"nome" => $this->input->post('nome', true),
			"cognome" => $this->input->post('cognome', true),
			"email" => $this->input->post('email', true),
			"indirizzo" => $this->input->post('indirizzo', true),
			"sesso" => $this->input->post('sesso', true)
		];
		
		$this->form_validation->set_data( $formData );

		$this->form_validation->set_rules('id', 'Id', 'required|numeric');
		$this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]|regex_match[/^([0-9a-z ])+$/i]' , ["regex_match" => "Il campo %s non può contenere caratteri speciali", "required" => "Il campo %s è obbligatorio"]);
		$this->form_validation->set_rules('cognome', 'Cognome', 'required|max_length[100]|regex_match[/^([0-9a-z ])+$/i]', ["regex_match" => "Il campo %s non può contenere caratteri speciali", "required" => "Il campo %s è obbligatorio"]);
		$this->form_validation->set_rules('email', 'Email', 'required|max_length[100]|valid_email', ["required" => "Il campo %s è obbligatorio", "valid_email" => "Il campo %s non è valido"]);
		$this->form_validation->set_rules('indirizzo', 'Indirizzo', 'max_length[200]');
		$this->form_validation->set_rules('sesso', 'Sesso', "required|max_length[1]|alpha");


		$out = [
			"status" => "failed",
			"message" => ""
		];

		if ( $this->form_validation->run() ) {

			#	ultimo controllo
			#	verifico che i dati non abbiano parole private

			$verifica = true;

			if( $verifica ){

				#	aggiungo la data di modifica
				$formData['ts_modify'] = date("Y-m-d H:i:s");

				#	aggiorno il 
				$this -> Customers_model -> update( (int) $formData['id'], $formData );

				$out['message'] = "Dati modificati  correttamente";
				$out['status'] = "success";
			}

		}else{

			foreach( $this->form_validation->error_array() as $k => $v ){
				$out['message'] .= $k . " : " . $v . "<br>";
			}
			
		}

		
		$this -> session -> set_flashdata( "esitoForm", $out );


		redirect("/app");
		die;
	}
	

	public function insertUser(  ){


		if ( $this->input->server('REQUEST_METHOD') !== 'POST') {

			error_log("tentativo di accesso con metodo non permesso");

            show_error('ES:: Invalid request method', 405);
			die;
        }

		
		$formData = [
			"nome" => $this->input->post('nome', true),
			"cognome" => $this->input->post('cognome', true),
			"email" => $this->input->post('email', true),
			"indirizzo" => $this->input->post('indirizzo', true),
			"sesso" => $this->input->post('sesso', true )
		];
		
		$this->form_validation->set_data( $formData );

		$this->form_validation->set_rules('nome', 'Nome', 'required|max_length[100]|regex_match[/^([a-z0-9 ])+$/i]', ["regex_match" => "Il campo %s non può contenere caratteri speciali", "required" => "Il campo %s è obbligatorio"]);	
		$this->form_validation->set_rules('cognome', 'Cognome', 'required|max_length[100]|regex_match[/^([a-z0-9 ])+$/i]', ["regex_match" => "Il campo %s non può contenere caratteri speciali", "required" => "Il campo %s è obbligatorio"]);
		$this->form_validation->set_rules('email', 'Email', 'required|max_length[100]|valid_email', ["required" => "Il campo %s è obbligatorio", "valid_email" => "Il campo %s non è valido"]);
		$this->form_validation->set_rules('indirizzo', 'Indirizzo', 'max_length[200]');
		$this->form_validation->set_rules('sesso', 'Sesso', "required|max_length[1]|alpha");

		$out = [
			"status" => "failed",
			"message" => ""
		];

		if ( $this->form_validation->run() ) {

			#	ultimo controllo
			#	verifico che i dati non abbiano parole private

			foreach( $this -> input -> post() as $campo => $valore ){
				if( !$this -> isSecure( $valore ) ){
					$this -> session -> set_flashdata("errors", "Rilevato tentativo di attacco");
					redirect("/error");
				}
			}


			$formData['ts_create'] = date("Y-m-d H:i:s");

			$this -> Customers_model -> insert( $formData );

			$out['message'] = "Nuova anagrafica inserita correttamente";
			$out['status'] = "success";

		}else{
			
			foreach( $this->form_validation->error_array() as $k => $v ){
				$out['message'] .= $k . " : " . $v . "<br>";
			}

		}	

		$this -> session -> set_flashdata( "esitoForm", $out );


		redirect("/app");
		die;
	}
	
	protected function prepareOrdinamento(){
		
		if( !$this -> session -> has_userdata("ordinamento")){
		
			
			$ordinamento = [];

			foreach( $this -> campi_disponibili as $v ){
				$ordinamento[$v] = ['dir' => 0, "seq" => false ];
			}

			$this -> session -> set_userdata("ordinamento", $ordinamento);
			$this -> session -> set_userdata("sequenza_ordinamento", []);
		}
	}

	protected function prepareFiltri(){
		
		if( !$this -> session -> has_userdata("filtri")){
			$filtri = [];

			foreach( $this -> campi_disponibili as $v ){

				$filtri[$v] = "";

			}

			$this -> session -> set_userdata("filtri", $filtri);
		}
	}

	protected function setupInput( $input ){

		#	sanifico input
		foreach( $input as $k => $v ){
			$input[ $k ] = $this -> security -> xss_clean( $v );
		
			if( ! $this -> isSecure( $v ) ){
				
				$this -> session -> set_flashdata("errors", "Rilevato tentativo di attacco");

				//	questo è un controllo di sicurezza, in caso di attacco, posso fare un redirect
				//	oppure fare un redirect alla pagina di login

				redirect("/logout");	
			}
		}


		#	aggiorno i dati in sessione
		if( isset( $input['o'] ) && isset( $input['v'] ) ){

			$ordinamento = $this -> session -> userdata("ordinamento");
			
			if( isset( $ordinamento[$input['o']] ) ){

				if( (int)$input['v'] !== 0 ){
					
					if( !$ordinamento[ $input['o'] ]['seq']  ){

						$ordinamento[$input['o']] = [ "dir" => (int)$input['v'], "seq" => time() ];
					
					}else{

						$ordinamento[$input['o']][ "dir" ] = (int)$input['v'];

					}
				}else{
					$ordinamento[$input['o']] = ['dir' => 0, "seq" => false ];
				}
			
				
				$this -> session -> set_userdata("ordinamento", $ordinamento);
				
				
				/*
				#	aggiorno la sequenza ordinamento
				$sequenza_ordinamento = $this -> session -> userdata("sequenza_ordinamento");

				if( $ordinamento[$input['o']] == 0 ){
				
					unset( $sequenza_ordinamento [$input['o'] ] );
				
				}else{
					
					if( !isset( $sequenza_ordinamento[$input['o']] ) )
						$sequenza_ordinamento[$input['o']] = time();
				}

				$this -> session -> set_userdata("sequenza_ordinamento", $sequenza_ordinamento);
				*/
			}

		}

		if( isset( $input['f'] ) && isset( $input['t'] ) ){

			#	il filtro deve essere obbligatoriamente composto da lettere e numeri
			#	non deve contenere caratteri speciali

			#	controllo con regex
			#	/^[a-zA-Z0-9]+$/

			if( preg_match("/^[a-z0-9 ]+$/", $input['t']) ){

				$filtri = $this -> session -> userdata("filtri");

				if( isset( $filtri[$input['f']] ) ){

					$filtri[$input['f']] = $input['t'];
					
					$this -> session -> set_userdata("filtri", $filtri);
				}
			}
		}

		if( isset( $input['ipp'] ) ){

			$ipp = (int)$input['ipp'];

			if( in_array( $ipp, [10, 20, 50] ) ){

				$this -> session -> set_userdata("ipp", $ipp);
			}
		}

		if( isset( $input['p'] ) ){

			$p = (int)$input['p'];

			if( $p >= 0 ){

				$this -> session -> set_userdata("p", $p);
			}
		}

		#	ultimo controllo per evitare di trovarmi in una pagina vuota in base ai filtri

		$count = $this->Customers_model->count( $this -> session -> userdata()['filtri'] );
		$pages = ceil($count / $this -> session -> userdata()['ipp'] );

		if( $this -> session -> userdata()['p'] >= $pages ){
			$this -> session -> set_userdata("p", 0);
		}

		return true;
	}

	protected function isSecure($value) {
	    $watchout = array();
	    $watchout[] = 'information_schema';
	    $watchout[] = '\bunion\s*all\b';
	    $watchout[] = '\bxp_cmdshell';
	    $watchout[] = '\/etc\/passwd';
	    $watchout[] = '\.\.\/\.\.\/';
	    $watchout[] = '\bchr *\(';
	    $watchout[] = '\bchar *\(';
	    $watchout[] = '\bsleep *\(';
	    $watchout[] = '\bdelay *\(';
	    $watchout[] = '\bdb_name *\(';
	    $watchout[] = '\bschema_name *\(';
	    $watchout[] = '\bbenchmark *\(';
	    $watchout[] = '@@version';
	    $watchout[] = '@@hostname';
	    $watchout[] = '@@session';
	    $watchout[] = '@@global';
	    $watchout[] = '\bhex *\(';
	    $watchout[] = '\bord *\(';
	    $watchout[] = '\bmid *\(';
	    $watchout[] = '\bmake_set *\(';
	    $watchout[] = '\belt *\(';
	    $watchout[] = '\*\/ *\(';
	    $watchout[] = 'peppino';

	    if(preg_match('/('.implode("|", $watchout).')/i', $value)) {

	        return false;
	    }
	    return true;
	}
}