<?php
/* 
 * Plus Automatisering
 */
defined('BASEPATH') OR exit('No direct script access allowed');
#[AllowDynamicProperties]
Class KingWebservices {
		
		public $id;
		public $webservicenaam;
		public $webservicekey;
		public $webservices_protocol = 'http';
		public $webservices_host = 'localhost';
		public $webservices_poort = '80';
		public $webservices_administratie = '';
		
		public $errormessages = [];
		public $debug = false;
		
		public function __construct($args = []) {
			// Controleren of Apicall library is geladen.
			// Is dit niet het geval, dan wordt het alsnog geladen.
			//
			if(!class_exists('Apicall')) {
				// $this werkt niet in een custom library
				// Daarom gebruiken we de CI instance
				//				
				$CI =& get_instance();
				$CI->load->library('Apicall');
			}
			
			// Argumenten verwerken
			//
			if(isset($args['webservicenaam']) && !empty($args['webservicenaam'])) { $this->webservicenaam = $args['webservicenaam']; }
			if(isset($args['webservicekey']) && !empty($args['webservicekey'])) { $this->webservicekey = $args['webservicekey']; }
			if(isset($args['webservices_protocol']) && !empty($args['webservices_protocol'])) { $this->webservices_protocol = $args['webservices_protocol']; }
			if(isset($args['webservices_host']) && !empty($args['webservices_host'])) { $this->webservices_host = $args['webservices_host']; }
			if(isset($args['webservices_poort']) && !empty($args['webservices_poort'])) { $this->webservices_poort = $args['webservices_poort']; }
			if(isset($args['webservices_administratie']) && !empty($args['webservices_administratie'])) { $this->webservices_administratie = $args['webservices_administratie']; }
		}
		
		// Een Webservice-call uitvoeren
		//
		public function roepWebserviceAan($request_body) {
		
			// Foutmeldingen leegmaken
			//
			$this->errormessages = [];
			
			// URL opmaken
			//
			$request_uri = $this->webservices_protocol.'://'.$this->webservices_host.':'.$this->webservices_poort.'/'.$this->webservices_administratie.'/'.$this->webservicenaam;
			
			// We communiceren met King Webservices via JSON
			$content_type = 'application/json';
			
			// Op dit moment gaan alle webservices via POST
			$request_method = "POST";

			// HTTP Headers instellen
			$headers = [
				'ACCESS-TOKEN: '.$this->webservicekey,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($request_body)
			];
			
			// Curl opties instellen
			Apicall::$options = array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_CUSTOMREQUEST => $request_method,
					CURLOPT_URL => $request_uri,
					CURLOPT_POSTFIELDS => $request_body,
					CURLOPT_HEADER => 0,
					CURLOPT_SSL_VERIFYPEER => FALSE,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTPHEADER => $headers
			);
			
			// Curl request uitvoeren
			//
			$res = Apicall::SendRequest();
			if($res === true) {
				// Curl call ging goed, nu controleren of de verwerking ook goed is.
				// We verwachten JSON terug, dus decoderen we de data die terug komt
				//
				$data_obj = json_decode(Apicall::$data);
				if($data_obj === null) {
					// Geen geldige JSON terug ontvangen
					//
					$this->errormessages[] = "Ongeldige JSON ontvangen: ".Apicall::$data;
				} else {
					// Geldige JSON terug ontvangen
					// Als we status: 0 terug krijgen, dan is de call correct uitgevoerd
					if($data_obj[0]->Status != 0) {
						if($this->debug) {
							$this->errormessages[] = "Fout bij uitvoeren van de webservice (Foutcode: ".$data_obj->FoutCode." - ".$data_obj->FoutMelding.")";
						} else {
							//$this->errormessages[] = $data_obj->FoutMelding;
						}
						
						
						if(isset($data_obj[0]->FoutMelding)) {
							$this->errormessages[] = $data_obj->FoutMelding;
						}
						elseif(isset($data_obj[0]->Error)) {
							$this->errormessages[] = $data_obj->Error;
						}
						else {
							$this->errormessages[] = print_r($data_obj, true);
						}
						
					}
					else {
						$this->response = $data_obj[0];
					}
				}
			} else {
				// CURL Call mislukt
				$data_obj = json_decode(Apicall::$errormessage);
				if($data_obj !== null) {
					if(is_array($data_obj)) {
						foreach($data_obj as $data_line) {
							if($this->debug) {
								$this->errormessages[] = "Fout bij uitvoeren van de webservice (Foutcode: ".$data_line->ErrorCode." - ".$data_line->Error.") - RequestURL: ".Apicall::$request_url." - ".print_r(Apicall::$options, true);
							} else {
								$this->errormessages[] = $data_line->Error;
							}
						}
					} else {
						if($this->debug) {
							$this->errormessages[] = "Fout bij uitvoeren van de curl request: ".$data_obj->Message." - RequestURL: ".Apicall::$request_url." - ".$request_body;
						} else {
							$this->errormessages[] = $data_line->Message;
						}
					}
				} else {
					if($this->debug) {
						$this->errormessages[] = "Fout bij uitvoeren van de curl request: ".Apicall::$errormessage." - RequestURL: ".Apicall::$request_url;
					} else {
						$this->errormessages[] = Apicall::$errormessage;
					}
				}
			}
		
			// Object terugsturen
			$return_object = new stdClass();
			if(empty($this->errormessages)) { 
				$return_object->error = false;
				$return_object->response = $this->response;
			} else {
				$return_object->error = true;
				$return_object->errormessages = $this->errormessages;
			}
			return $return_object;
		}
		
		
	}
?>