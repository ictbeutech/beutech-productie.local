<?php class Recepten extends MY_Controller { // Recepten Controller

	public function __construct(){
		parent::__construct();
		$this->load->model('recepten_model');
		$this->load->model('settings_model');
		$this->load->model('voorraad_model');
	}


	public function index(){	

		//Check user access rights
		$afdeling = "Recepten";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		//Sync recepten Beutech 
		if(isset($_POST['submit-sync-recepten-beutech'])){ 
			$this->syncRecepten_beutech();
		}
		
		//Sync recepten Beutech 
		if(isset($_POST['submit-create-xml-voorraadmutatie-beutech'])){ 
			$this->create_xml_voorraadmutatie();
		}
		
		//Set page title
		$data['title'] = '<i class="fas fa-th-list"></i> Receptenoverzicht';
		
		//Load view templates
		$this->load->view('templates/header', $data);
		$this->load->view("recepten/index.php", array());
		$this->load->view('templates/footer');
	}
	
	
	public function check_user_rights($afdeling, $user_level, $user_email){
		
		$access = $this->settings_model->check_user_rights($afdeling, $user_level, $user_email);

		return $access;
		
	}
	
	
	public function view($eindproduct = NULL){
		
		//Check user access rights
		$afdeling = "Recepten";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$recept_items = $this->recepten_model->getRecept($eindproduct);
				
		$componenten_details = array();
		$vrije_voorraad = 0;
		$max_aantal_productie = 0;
		
		$i = 0;
		foreach($recept_items as $component){
			
			if($component['component_soort'] == "Artikel"){
				$vrije_voorraad = $this->voorraad_model->getCompnentVoorraad($component['component_gid']);
				
				if($vrije_voorraad > 0){
					if($component['component_verbruik'] > 0){
						$max_aantal_productie = ($vrije_voorraad / $component['component_aantal']) * $component['component_verbruik'];
						$max_aantal_productie = floor($max_aantal_productie);
					}else{
						$max_aantal_productie = $vrije_voorraad / $component['component_aantal'];
						$max_aantal_productie = floor($max_aantal_productie);
					}
				}else{
					$vrije_voorraad = 0;
					$max_aantal_productie = 0;
				}
				
			}else{
				$vrije_voorraad = "n.v.t.";
				$max_aantal_productie = 99999999999;
			}
			
			
			$componenten_details[$i]['id'] = $component['id'];
            $componenten_details[$i]['recept_gid'] = $component['recept_gid'];
            $componenten_details[$i]['recept_code'] = $component['recept_code'];
            $componenten_details[$i]['recept_omschr'] = $component['recept_omschr'];
			$componenten_details[$i]['eindproduct_gid'] = $component['eindproduct_gid'];
            $componenten_details[$i]['eindproduct_artnr'] = $component['eindproduct_artnr'];
			$componenten_details[$i]['eindproduct_aantal'] = $component['eindproduct_aantal'];
            $componenten_details[$i]['component_gid'] = $component['component_gid'];
            $componenten_details[$i]['component_regelnr'] = $component['component_regelnr'];
			$componenten_details[$i]['component_soort'] =  $component['component_soort'];
            $componenten_details[$i]['component_artnr'] = $component['component_artnr'];
            $componenten_details[$i]['component_artomschr'] = $component['component_artomschr'];
            $componenten_details[$i]['component_aantal'] = $component['component_aantal'];
            $componenten_details[$i]['component_verbruik'] = $component['component_verbruik'];
            $componenten_details[$i]['administratie'] = $component['administratie'];
			$componenten_details[$i]['vrije_voorraad'] = $vrije_voorraad;
			$componenten_details[$i]['max_aantal_productie'] = $max_aantal_productie;
			
			$i++;
		}
		
		$data['recept_items'] = $componenten_details;
		
		if (empty($data['recept_items']))
		{
			show_404();
		}
		
		$data['title'] = "Recept voor artikel - " . $data['recept_items'][0]['eindproduct_artnr'] . " - " . $data['recept_items'][0]['recept_omschr'];

		$this->load->view('templates/header', $data);
		$this->load->view('recepten/receptdetails', $data);
		$this->load->view('templates/footer');
	}

	
	public function syncRecepten_beutech(){
		$this->recepten_model->syncRecepten_beutech();	
	}


	function create_xml_voorraadmutatie(){ //Create XML voorraadmutatie for import King
	
		$voorraadmutaties = $this->recepten_model->create_xml_voorraadmutatie();	
		$count = 0;
		
		$filename = "voorraad_correcties/voorraad_mutatie.xml";
		
					
		//Check if file 'voorraad_mutatie.xml' exists
		
		$filename = EXTERNAL_WRITE_PATH . "/Voorraadcorrecties/voorraad_mutatie.xml";
		echo $filename . "<br>";	
		exit();
		/*	
		if (is_readable($filename)) {
			echo "Kan het pad lezen";
		}else{
			echo "Kan het pad niet lezen";
		}
		exit();
		*/
		
		// Check if the file exists
		if (file_exists($filename)) {
			// Attempt to load the XML file
			$xml = simplexml_load_file($filename);

			// Check if XML file is loaded successfully
			if ($xml !== false) {
				// XML file is loaded successfully, you can now work with $xml
				// For example, you can access elements and attributes like $xml->elementName or $xml['attributeName']
				// Process the XML data here
			} else {
				// Failed to load XML file
				echo "Failed to load XML file.";
			}
		} else {
			// File doesn't exist
			echo "File does not exist.";
		}
		
		exit();

		if (file_exists($filename)) {
			
			//Send mail to verkoop with order details and aantal geproduceerd 
			$this->load->library('email');
			$this->load->helper('email');
			$this->email->set_newline("\r\n");
						
			$this->email->from('productie@beutech.nl','Beutech BV - Voorraadmutatie');
			//$this->email->to('webdiensten@plusautomatisering.nl');
			$this->email->to('pascalmoes@beutech.nl, ict@beutech.nl');
			//$this->email->cc('ict@beutech.nl');
			$this->email->bcc('backupinternetdiensten@plusautomatisering.nl');
			$message = "<h3>De automatische taak voor het genereren van de XML is mislukt.</h3>";
			$message .= '<p>Het bestand voorraad_mutatie.xml bestaat al.</p>';
			$message .= '<p>Normaal gesproken zou het bestand na inlezen in King verwijderd moeten worden.</p>';
			$message .= '<p><strong>Controleer of het bestand door King is ingelezen</strong></p>';
			
			$this->email->subject('Foutmelding genereren voorraadmutatie XML');
			$this->email->message($message);
			
			$this->email->send();
			//END OF - Send mail to verkoop with order details and aantal geproduceerd
			
		} else { //If file not exists -> Create XML
							
			if(isset($voorraadmutaties)&& !empty($voorraadmutaties)){ //If export voorraadmutaties results
		
				//Create XML Voorraad mutatie
				//XML Voorraad mutatie head
				$voorraad_mutatie_xml = "
<KING_VOORRAADCORRECTIES>
	<VOORRAADCORRECTIES>
		<VOORRAADCORRECTIE>
			<VOORRAADCORRECTIE_KOP>
				<VCK_CORRECTIESOORT>010</VCK_CORRECTIESOORT>
				<VCK_DIRECTVERWERKEN>TRUE</VCK_DIRECTVERWERKEN>
			</VOORRAADCORRECTIE_KOP>
			<VOORRAADCORRECTIE_REGELS>
				";
		
				foreach($voorraadmutaties as $voorraadmutatie){ //Loop through voorraadmutaties
			
					$aantal_geproduceerd = number_format($voorraadmutatie->geproduceerd ,2 ,'.' ,'');
					$eindproduct = $voorraadmutatie->eindproduct_artnr;
					$recept = json_decode($voorraadmutatie->recept);
			
					//XML Voorraad mutatie regels eindproduct
					$voorraad_mutatie_xml .= "
				<VOORRAADCORRECTIEREGEL>
					<VCR_ARTIKEL>{$eindproduct}</VCR_ARTIKEL>	
					<VCR_LOCATIE>(Standaard)</VCR_LOCATIE>					
					<VCR_AANTAL>{$aantal_geproduceerd}</VCR_AANTAL>
				</VOORRAADCORRECTIEREGEL>
					";
		
					//XML Voorraad mutatie regels componenten
					foreach($recept as $row){ //Loop through recepten
						if($row->component_verbruik > 0){
							$aantal_component = $row->component_aantal / $row->component_verbruik;
						}else{
							$aantal_component = $row->component_aantal;	
						}
						$aantal_component = -$aantal_component;
											
						$aantal_afboeken = $aantal_geproduceerd * $aantal_component;
						
						$aantal_afboeken = number_format($aantal_afboeken, 2, '.' ,'');
						
						if($row->component_soort != "Tarief"){
							$voorraad_mutatie_xml .= "
				<VOORRAADCORRECTIEREGEL>
					<VCR_ARTIKEL>{$row->component_artnr}</VCR_ARTIKEL>
					<VCR_LOCATIE>(Standaard)</VCR_LOCATIE>					
					<VCR_AANTAL>{$aantal_afboeken}</VCR_AANTAL>
				</VOORRAADCORRECTIEREGEL>
							";
						}
				
					} //END OF - Loop through recepten
			
					//Set exported = 1
					$this->recepten_model->update_voorraadmutatie($voorraadmutatie->id);
			
					$count++;
			
				} //END OF - Loop through voorraadmutaties
		
				//XML Voorraad mutatie foot
				$voorraad_mutatie_xml .= "	
			</VOORRAADCORRECTIE_REGELS>
		</VOORRAADCORRECTIE>
	</VOORRAADCORRECTIES>
</KING_VOORRAADCORRECTIES>
				";
		
				// Save XML for import King
				$xml_save_file = EXTERNAL_WRITE_PATH . "/Voorraadcorrecties/voorraad_mutatie.xml";
				
				
				$fh = fopen($xml_save_file, 'w');
				fwrite($fh, $voorraad_mutatie_xml);
				fclose($fh); 
				
				// Save XML for log
				$date = new DateTime();
				$date = $date->format('Ymd-His');
								
				$xml_save_file_log = EXTERNAL_WRITE_PATH . "/Voorraadcorrecties/XML-logs/voorraad_mutatie" . $date .  ".xml";
			
				$fl = fopen($xml_save_file_log, 'w');
				fwrite($fl, $voorraad_mutatie_xml);
				fclose($fl); 
				//END OF - Create XML Voorraad mutatie		
				
			} //END OF - If export voorraadmutaties results
		
		} //END OF - If file not exists -> Create XML
		
		$this->session->set_flashdata('msg_success', 'Er zijn <strong>' . $count . '</strong> regels klaargezet voor import King');

	
	} //END OF - Create XML voorraadmutatie for import King
	
	
} //END OF - Recepten Controller