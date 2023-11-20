<?php class Productie extends MY_Controller { // Productie Controller

	
	// King Webservices
	// King Webservices
	public $king_webservices_beutech = array(
		'webservices_protocol'			=> WEBSERVICES_PROTOCOL,
		'webservices_host'				=> WEBSERVICES_HOST,
		'webservices_poort'				=> WEBSERVICES_POORT,
		'webservices_administratie'		=> WEBSERVICES_ADMINISTRATIE
	);

	public function __construct(){
		parent::__construct();
		$this->load->model('productie_model');
		$this->load->model('settings_model');
		$this->load->library('kingwebservices');
	}


	public function index(){	
		
		//Check user access rights
		$afdeling = "Productie";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		//Sync voorraad Beutech 
		if(isset($_POST['submit-sync-productie-mutaties-beutech'])){ 
			$administratie = "Beutech";
			$this->syncProductieMutaties();
		}
				
		//Set page title
		$data['title'] = '<i class="fas fa-tools"></i> Productie mutatie overzicht';
		
		//Load view templates
		$this->load->view('templates/header', $data);
		$this->load->view("productie/index.php", array());
		$this->load->view('templates/footer');
	}
	
	
	public function check_user_rights($afdeling, $user_level, $user_email){
		
		$access = $this->settings_model->check_user_rights($afdeling, $user_level, $user_email);

		return $access;
		
	}
	
		
	public function get_productie_mutaties(){
		
		$afdeling = $this->input->post("afdeling");
		
		$productie_mutaties = $this->productie_model->get_productie_mutaties($afdeling);
				
		$data_productie_mutaties = array();

		foreach($productie_mutaties as $r) {			
								
			$data_productie_mutaties[] = array(
				$r->afdeling,
				$r->order_nr,
				$r->aantal_geproduceerd,
				$r->added,
				$r->user,
				$r->gesynct,
				$r->administratie,
				$r->orderregel_id,
				$r->status_message,
				$r->lock_status_message,
				"DT_RowId" => $r->orderregel_id				 
			);
		}		
		
		$output = array(
			"data" => $data_productie_mutaties
		);
		
		echo json_encode($output);
		exit();
	}
	
	
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
			
			if(is_cli()){
			
			}
			
		} else{ //END OF - Execute if there are new productie mutaties
		
			$this->session->set_flashdata('msg_success', '- Er zijn momenteel <strong>geen</strong> nieuwe productie mutaties die moeten worden gesycnhroniseerd naar King');
		
		}
		
	}
		
	
} //END OF - Productie Controller