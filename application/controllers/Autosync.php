<?php
class Autosync extends CI_Controller {

	// King Webservices
	public $king_webservices_beutech = array(
		'webservices_protocol'			=> WEBSERVICES_PROTOCOL,
		'webservices_host'				=> WEBSERVICES_HOST,
		'webservices_poort'				=> WEBSERVICES_POORT,
		'webservices_administratie'		=> WEBSERVICES_ADMINISTRATIE
	);
	
	public function __construct(){
		parent::__construct();
		$this->load->model('orders_model');
		$this->load->model('voorraad_model');
		$this->load->model('recepten_model');
		$this->load->model('productie_model');
		$this->load->library('kingwebservices');
	}


	// Sync orders Beutech & Tibuplast
    public function auto_sync_orders()
    {
        $this->orders_model->syncOrders_tibuplast();
		$this->orders_model->syncOrders_beutech();		
    }
	// END OF - Sync orders
	
	
	// Sync voorraad Beutech
	public function syncVoorraad_beutech(){
		$this->voorraad_model->syncVoorraad_beutech();	
	}
	// END OF - Sync voorraad Beutech
	
	
	//Create XML voorraadmutatie for import King
	function create_xml_voorraadmutatie(){ 
	
		$voorraadmutaties = $this->recepten_model->create_xml_voorraadmutatie();	
		$count = 0;
		
		//Check if file 'voorraad_mutatie.xml' exists
		$filename = "voorraad_correcties/voorraad_mutatie.xml";

		if (file_exists($filename)) {
			
			//Send mail to verkoop with order details and aantal geproduceerd 
			$this->load->library('email');
			
			$config['mailtype'] = 'html';
			$config['charset'] = 'utf-8';
			$config['wordwrap'] = TRUE;
			
			$this->email->initialize($config);
			
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
				$myFile = "voorraad_correcties/voorraad_mutatie.xml";
				$fh = fopen($myFile, 'w');
				fwrite($fh, $voorraad_mutatie_xml);
				fclose($fh); 
				
				// Save XML for log
				$date = new DateTime();
				$date = $date->format('Ymd-His');
				
				$myFile_log = "voorraad_correcties/xml_logs/voorraad_mutatie" . $date .  ".xml";
				$fl = fopen($myFile_log, 'w');
				fwrite($fl, $voorraad_mutatie_xml);
				fclose($fl); 
				//END OF - Create XML Voorraad mutatie		
				
			} //END OF - If export voorraadmutaties results
		
		} //END OF - If file not exists -> Create XML
		
		$this->session->set_flashdata('msg_success', 'Er zijn <strong>' . $count . '</strong> regels klaargezet voor import King');

	
	} //END OF - Create XML voorraadmutatie for import King
	

	public function syncProductieMutaties(){
		
		$administratie = "Beutech";
		
		//Fetch new prodcutie mutaties
		$productie_mutaties = $this->productie_model->syncProductieMutaties($administratie);
		
		
		// Execute if there are new productie mutaties
		if(isset($productie_mutaties) && !empty($productie_mutaties)){
			
			$count = 0;
			$count_error = 0;
			
			// Loop through new productie mutaties
			foreach($productie_mutaties as $mutatie){
			
				$id = $mutatie->id;
				$order_nr = $mutatie->order_nr;
				$order_regel_id = $mutatie->orderregel_id;
				$aantal_geproduceerd = $mutatie->aantal_geproduceerd;
				
				$huidig_aantal_deze_levering = $this->productie_model->get_aantal_deze_levering_king($administratie, $order_regel_id);
				
				$nieuw_aantal_deze_levering = $aantal_geproduceerd + $huidig_aantal_deze_levering;
				
				//Set data Order_OrderRegel_SpecificatieRegels_Verwijderen
				$order_spec_data = new \stdClass();
				$order_spec_data->OrderNummer = $order_nr;
				$order_spec_data->RegelNummer = 001;
			
				// Initialize Webservice Order_OrderRegel_SpecificatieRegels_Verwijderen
				$spec_regel_verwijderen = new KingWebservices(array(
					'webservicenaam' 				=> 'Order_OrderRegel_SpecificatieRegels_Verwijderen',
					'webservicekey'					=> 'Planning',
					'webservices_protocol'			=> $this->king_webservices_beutech['webservices_protocol'],
					'webservices_host'				=> $this->king_webservices_beutech['webservices_host'],
					'webservices_poort'				=> $this->king_webservices_beutech['webservices_poort'],
					'webservices_administratie'		=> $this->king_webservices_beutech['webservices_administratie']
				));
				
				// Send data to webservice Order_OrderRegel_Wijzigen
				$verwijder_spec_regel = $spec_regel_verwijderen->roepWebserviceAan(json_encode($order_spec_data));
			
				//Set data Order_OrderRegel_Wijzigen
				$order_data = new \stdClass();
				$order_data->OrderNummer = $order_nr;
				$order_data->RegelID = $order_regel_id;
				$order_data->AantalDezeLevering = $nieuw_aantal_deze_levering;
				$order_data->AantalDezeFacturering = $nieuw_aantal_deze_levering;
				
				// Initialize Webservice Order_OrderRegel_Wijzigen
				$orderregel_wijzigen = new KingWebservices(array(
					'webservicenaam' 				=> 'Order_OrderRegel_Wijzigen',
					'webservicekey'					=> 'Planning',
					'webservices_protocol'			=> $this->king_webservices_beutech['webservices_protocol'],
					'webservices_host'				=> $this->king_webservices_beutech['webservices_host'],
					'webservices_poort'				=> $this->king_webservices_beutech['webservices_poort'],
					'webservices_administratie'		=> $this->king_webservices_beutech['webservices_administratie']
				));
				
				// Send data to webservice Order_OrderRegel_Wijzigen
				$wijzig_orderregel = $orderregel_wijzigen->roepWebserviceAan(json_encode($order_data));
				
				// Set intern status Order_OrderRegel_Wijzigen
				if($wijzig_orderregel->error === false) {
					$status_message = "Productie mutatie succesvol gesynchroniseerd naar King";
					$gesynct = 1;
					$count++;
				} else {					
					$status_message = json_encode($wijzig_orderregel->errormessages);
					$gesynct = 0;
					$count_error++;
				}
				
				// Update intern status Order_OrderRegel_Wijzigen
				$this->productie_model->update_status($id, $status_message, $gesynct);
				
			
				//Set data Order_LockVrijgeven
				$order_lock_data = new \stdClass();
				$order_lock_data->OrderNummer = $order_nr;

				// Initialize Webservice Order_LockVrijgeven
				$order_lockvrijgeven = new KingWebservices(array(
					'webservicenaam' 				=> 'Order_LockVrijgeven',
					'webservicekey'					=> 'Planning',
					'webservices_protocol'			=> $this->king_webservices_beutech['webservices_protocol'],
					'webservices_host'				=> $this->king_webservices_beutech['webservices_host'],
					'webservices_poort'				=> $this->king_webservices_beutech['webservices_poort'],
					'webservices_administratie'		=> $this->king_webservices_beutech['webservices_administratie']
				));
					
				// Send data to webservice Order_LockVrijgeven
				$orderlock = $order_lockvrijgeven->roepWebserviceAan(json_encode($order_lock_data));
				
				// Set intern status Order_LockVrijgeven
				if($orderlock->error === false) {
					$status_message_lock = "Orderlock succesvol vrijgegeven";
				} else {
					$status_message_lock = json_encode($orderlock->errormessages);
				}
				
				// Update intern status Order_LockVrijgeven
				$this->productie_model->update_status_lock($id, $status_message_lock);				
			
			
				//Set flash message for frontend
				$this->session->set_flashdata('msg_success', '- Er zijn <strong>' . $count . '</strong> gesynchroniseerd naar King<br>- Er zijn <strong>' . $count_error . '</strong> fouten opgetreden. ');
			
			} // END OF - Loop through new productie mutaties
						
		} else{ //END OF - Execute if there are new productie mutaties
		
			$this->session->set_flashdata('msg_success', '- Er zijn momenteel <strong>geen</strong> nieuwe productie mutaties die moeten worden gesycnhroniseerd naar King');
		
		}
		
	}
	
}