<?php
	/* 
	 * Plus Automatisering
	 */
	defined('BASEPATH') OR exit('No direct script access allowed');

	Class Apicall {
		static $options;
		static $request_url;
		static $statuscode;
		static $data;
		static $error;
		static $errormessage;
		
		
		public static function SendRequest()
		{
			$ch = curl_init();
			
			self::$errormessage = '';
			self::$error = false;
			
			// We willen binnen 5 seconden antwoord hebben
			self::$options[CURLOPT_TIMEOUT] = 25;
			
			// Stel de opties in voor de CURL request
			curl_setopt_array($ch, self::$options);
			
			// De URL stellen we ook in als static variabele
			self::$request_url = self::$options[CURLOPT_URL];
			
			// De CURL request uitvoeren
			$output = curl_exec($ch);
			
			// Krijgen we een goede waarde terug?
			if ($output !== FALSE) {
				
				// Wat is de statuscode
				self::$statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if (self::$statuscode == 200) {

					// Statuscode 200 = goed!
					// Geeft de output door
					self::$data = $output;
				} else {
					
					// Geen statuscode 200 = fout
					// Stel de errormessage in
					self::$error = true;
					self::$errormessage = $output;
				}
			} else {
				
				// De uitvoer is niet goed
				self::$error = true;
				
				// Kijken of we een CURL foutmelding terug krijgen
				if ($errno = curl_errno($ch)) {
					
					// Probeer de foutmelding als tekst binnen te krijgen
					if(function_exists("curl_strerror")) {
						$error_message = curl_strerror($errno);
					} else {
						
						// Geen tekst? Dan maar een foutcode
						$error_message = curl_error($ch);
					}
					
					// Foutmelding doorgeven
					self::$errormessage = "cURL error ({$errno}):\n {$error_message}";
				}
			}
			
			// CURL sluiten
			curl_close($ch);
			
			if(self::$error === true) {
				return false;
			} else {
				return true;
			}
		}		
		
	}
?>