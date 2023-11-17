<?php class Voorraad extends MY_Controller { // Voorraad Controller


	public function __construct(){
		parent::__construct();
		$this->load->model('voorraad_model');
		$this->load->model('settings_model');
		$this->load->model('orders_model');
	}


	public function index(){	
		
		//Check user access rights
		$afdeling = "Voorraad";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		//Sync voorraad Beutech 
		if(isset($_POST['submit-sync-voorraad-beutech'])){ 
			$this->syncVoorraad_beutech();
		}
				
		//Set page title
		$data['title'] = '<i class="fas fa-th-list"></i> Voorraad overzicht';
		
		//Load view templates
		$this->load->view('templates/header', $data);
		$this->load->view("voorraad/index.php", array());
		$this->load->view('templates/footer');
	}
	
	
	public function check_user_rights($afdeling, $user_level, $user_email){
		
		$access = $this->settings_model->check_user_rights($afdeling, $user_level, $user_email);

		return $access;
		
	}
	
	
	public function view($afdeling = NULL){
		
		//Check user access rights
		$afdeling_rights = "Voorraad";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling_rights, $user_level, $user_email);
		
		$vooraad_artikelen = $this->voorraad_model->getVoorraad($afdeling);
		
		if (!empty($vooraad_artikelen)){
			$data['voorraad_artikelen'] = $vooraad_artikelen;
		}else{
			$data['voorraad_artikelen'] = null;
		}
		
		$data['title'] = "Voorraad voor afdeling - " . $afdeling;
		
		$data['afdeling'] = $afdeling;

		$this->load->view('templates/header', $data);
		$this->load->view('voorraad/voorraad_details', $data);
		$this->load->view('templates/footer');
	}
	
	
	public function get_voorraad(){
		$afdeling = $this->input->post("afdeling");
		
		$vooraad_artikelen = $this->voorraad_model->getVoorraad($afdeling);
				
		$data_voorraad = array();

		foreach($vooraad_artikelen as $r) {			
			
			//Check if artikel is eindproduct
			$eindproduct = $this->voorraad_model->getArtikel_status($r['art_code']);
			
			if($eindproduct == true){
				$artikel_status = "Eindproduct";
			}else{
				$artikel_status = "Component";
			}
			
			$data_voorraad[] = array(
				$r['art_code'],
				$artikel_status,
				$r['art_oms'],
				$r['art_vrije_voorraad'],
				$r['art_in_bestelling'],
				$r['art_gereserveerd'],
				$afdeling,
				$r['art_debiteur'],
				"DT_RowId" => $r['id']				 
			);
		}		
		
		$output = array(
			"data" => $data_voorraad
		);
		
		echo json_encode($output);
		exit();
	}
	
	
	public function get_voorraadDebiteur(){
		
		$debiteurnr = $this->input->post("debiteurnr");
		$afdeling = $this->input->post("afdeling");
		
		$voorraad = $this->voorraad_model->get_voorraadregelsDebiteur($debiteurnr, $afdeling);
	
		$contactpersonen = $this->orders_model->get_contactpersonenDebiteur($debiteurnr);
	
		$voorraad_table = "
			<strong>Orderoverzicht:</strong>
			<hr>
			<br>
		";
	
		$voorraad_table .= "<table width='100%'>";
			$voorraad_table .= "<tr>";
				$voorraad_table .= "<td><strong>Artikelnummer</strong></td>";
				$voorraad_table .= "<td><strong>Omschrijving</strong></td>";
				$voorraad_table .= "<td><strong>Vrijevoorraad</strong></td>";
				$voorraad_table .= "<td><strong>In bestelling</strong></td>";
				$voorraad_table .= "<td><strong>Gereserveerd</strong></td>";
			$voorraad_table .= "</tr>";
		
		foreach($voorraad as $voorraad_regel){	
			
			$voorraad_table .= "<tr>";
				$voorraad_table .= "<td>". $voorraad_regel->art_code ."</td>";
				$voorraad_table .= "<td>". $voorraad_regel->art_oms ."</td>";
				$voorraad_table .= "<td>". $voorraad_regel->art_vrije_voorraad ."</td>";
				$voorraad_table .= "<td>". $voorraad_regel->art_in_bestelling ."</td>";
				$voorraad_table .= "<td>". $voorraad_regel->art_gereserveerd ."</td>";
			$voorraad_table .= "</tr>";
		}
		
		$voorraad_table .= "</table>";
		
		$contact_row_voorraad = "
			<br>
			<strong>Mail naar contactpersoon:</strong>
			<hr>
			<br>
		";
		
		$contact_row_voorraad .= '<div id="radio_mail">';
		if(!empty($contactpersonen)){
			foreach($contactpersonen as $contactpersoon){
				$contact_row_voorraad .= '
					<div class="form-check">
						<input class="form-check-input" type="radio" name="mail_to_radio_voorraad" id="'.$contactpersoon->con_nummer.'" value="'. $contactpersoon->con_email .'" >
						<label class="form-check-label" for="'.$contactpersoon->con_nummer.'">
							'. $contactpersoon->con_email .' - ('. $contactpersoon->con_vol_naam .')
						</label>
					</div>
				';
			}
			$contact_row_voorraad .= '
				<div class="form-check">
					<input class="form-check-input" type="radio" name="mail_to_radio_voorraad" id="custom" value="custom_mail">
					<label class="form-check-label" for="custom">
						Ander e-mailadres invoeren.
					</label>
				</div>
			';
		}else{
			$contact_row_voorraad .= "Deze debiteur heeft nog geen contactpersonen voor deze functie.";
			$contact_row_voorraad .= '
				<div class="form-check">
					<input class="form-check-input" type="radio" name="mail_to_radio_voorraad" id="custom1" value="custom_mail">
					<label class="form-check-label" for="custom">
						Ander e-mailadres invoeren.
					</label>
				</div>
			';
		}
		$contact_row_voorraad .= '</div>';
		
		$contact_row_voorraad .= '
			<div class="form-group" id="custom_mail_form_voorraad">
				<input type="email" name="mail_to_custom_voorraad" class="form-control" id="email_custom" placeholder="Voer custom e-mailadres in">
			</div>
		';
		
		
		$result = array(
			'voorraad_table' => $voorraad_table,
			'contact_row_voorraad' => $contact_row_voorraad
		);
		
				header("Content-Type: application/json");
		echo json_encode($result);
			
		exit();
	
	}
	
	
	public function syncVoorraad_beutech(){
		$this->voorraad_model->syncVoorraad_beutech();	
	}
	
	
} //END OF - Voorraad Controller