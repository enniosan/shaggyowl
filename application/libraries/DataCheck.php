<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class DataCheck {

    protected $CI;

    public function __construct() {
        
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->CI->load->helper('url');
        
    }

    public function checkData($data, $rules) {

        #   inizializzo l'array di output
        #   qui ci andaranno i dati validati
        $out = [];

        foreach( $data as $campo => $valore ){

            #   prendo la regola
            $regola = ( isset( $rules[$campo]) ) ? $rules[$campo] : false;

            #   se ho la regola procedo con il check
            if( $regola ){

                if( !preg_match( $regola['regex'], $valore ) ){

					#	il campo non è valido, lo sostituisco con il default
					$out[$campo] = $regola['default'];
                     
				}else{

                    # verifico il tipo e il range

                    if( $regola['type'] == "int" ){

                        $valore = (int) $valore;

                        if( isset( $regola['min'] ) ){
                            if( $valore < $regola['min'] ){
                                $valore = $regola['min'];
                            }
                        }

                        if( isset( $regola['max'] ) ){
                            if( $valore < $regola['max'] ){
                                $valore = $regola['max'];
                            }
                        }


                        $out[$campo] = $valore;
                    
                    }
                
                    #   verifco la condizione in
                    if( isset( $regola['in'] ) ){

                        if( !in_array( $valore, $regola['in'] ) ){
                            $out[$campo] = $regola['default'];
                        }
                        else{
                            
                            $out[ $campo ] = $valore;
                        
                        }

                    }
                }

            }
            else{

                $out[ $campo ] = $valore;
            
            }

        }


        return $out;

    
    
    }
    
}