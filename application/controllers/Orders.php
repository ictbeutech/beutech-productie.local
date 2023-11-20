 <?php class Orders extends MY_Controller { // Orders Controller


	public function __construct(){
		parent::__construct();
		$this->load->model('orders_model');
		$this->load->model('recepten_model');
		$this->load->model('settings_model');
		$this->load->model('voorraad_model');
		$this->load->model('productie_model');
		
	}


	public function index(){	
				
		//Check user access rights
		$afdeling = "Orderoverzicht";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
	
		//Sync orders Tibuplast
		if(isset($_POST['submit-sync-tibuplast'])){ 
			$this->syncOrders_tibuplast();
		}
		
		//Sync orders Beutech 
		if(isset($_POST['submit-sync-beutech'])){ 
			$this->syncOrders_beutech();
		}
		
		//Delete all orders
		if(isset($_POST['submit-truncate-orders'])){ 
			$this->truncateOrders();
		}
		
		//Set page title
		$data['title'] = '<i class="fas fa-th-list"></i> Orderoverzicht';
		
		//Load view templates
		$this->load->view('templates/header', $data);
		$this->load->view("orders/index.php", array());
		$this->load->view('templates/footer');
	}
	
	
	public function check_user_rights($afdeling, $user_level, $user_email){
		
		$access = $this->settings_model->check_user_rights($afdeling, $user_level, $user_email);

		return $access;
		
	}
	
	
	public function get_orderregels(){
		
		$ordernr = $this->input->post("ordernr");
		
		$orders = $this->orders_model->getOrderRows($ordernr);
				
		$data_orders = array();

		foreach($orders as $r) {
			
			//If empty afdeling show: Kies afdeling
			$afdeling = $r['afdeling'];	
			$afdeling_naam = $afdeling;
			if($afdeling_naam == "Dvb"){
				$afdeling_naam = "Doorvoerbochten";
			}
			
			if ((in_array($afdeling_naam, $this->session->userdata('afdelingen'))) || (in_array("Admin", $this->session->userdata('afdelingen'))) || ($r['soort'] == "Tekst") ) { 
			
				if(empty($afdeling)){
					$afdeling = "<small><em>kies afdeling</em></small>";
				}	

				//Only show date is field is not empty
				if($r['datum_klaar'] == "0000-00-00 00:00:00"){
					$datum_klaar = "-";
					$datum_klaar_hidden = "-";
				}else{
					$datum_klaar = system_to_euro_date($r['datum_klaar']);
					$datum_klaar_hidden = $r['datum_klaar'];
				}
				
				//Only show date is field is not empty
				if($r['leverdatum'] == "0000-00-00 00:00:00"){
					$leverdatum = "-";
					$leverdatum_hidden = "-";
				}else{
					$leverdatum = system_to_euro_date($r['leverdatum']);
					$leverdatum_hidden = $r['leverdatum'];
				}
				
				//Set active class for rows
				if($r['active'] == 1){
					$active = "show";
				}else{
					$active = "hide";
				}
				
				//Set status background color for rows
				$color = "";
				if($r['status'] == "Nieuw"){$color = "";}
				if($r['status'] == "Mee bezig"){$color = "bg-warning";}
				if($r['status'] == "Buitendienst"){$color = "bg-info";}
				if($r['status'] == "WVB"){$color = "bg-secondary";}
				if($r['status'] == "Is klaar"){$color = "bg-success";}
				
				$last_update = system_to_euro_date_time($r['last_update']);					
				
				$data_orders[] = array(
					$r['orderregel_nr'],
					$r['soort'],
					$r['status'],
					$r['artikelnr'],
					$r['product'],
					$afdeling,
					$r['sub_afdeling'],
					
					//$r['klant'],
					//"<a href='/orders/".$r['ordernr']."'>" . $r['ordernr'] . " <i class='fas fa-eye'></i></a>",	
					//$r['opbrgroep'],			
					//$r['aantal'],
					//$r['bon'], 
					//"<span class='hidden_date'>".$datum_klaar_hidden."</span>" . $datum_klaar,
					//$r['week_klaar'],
					//$r['dag_klaar'],
					//"<span class='hidden_date'>".$leverdatum_hidden."</span>" . $leverdatum,
					//$r['week_nr'],
					//$r['day'],
					//$active,
					//$r['administratie'],
					//"<small>" .$last_update . "</small>",
					"DT_RowId" => $r['id'],
					"DT_RowClass" => $active . " " .  $color				 
				);
			
			}		
		
		}
		
		$output = array(
			"data" => $data_orders
		);
		
		echo json_encode($output);
		exit();
	}


	public function get_orderregelsDebiteur(){
		
		$debiteurnr = $this->input->post("debiteurnr");
		$afdeling = $this->input->post("afdeling");
		
		$orders = $this->orders_model->get_orderregelsDebiteur($debiteurnr, $afdeling);
	
		$contactpersonen = $this->orders_model->get_contactpersonenDebiteur($debiteurnr);
	
		$order_table = "
			<strong>Orderoverzicht:</strong>
			<hr>
			<br>
		";
	
		$order_table .= "<table width='100%'>";
			$order_table .= "<tr>";
				$order_table .= "<td><strong>Ordernr</strong></td>";
				$order_table .= "<td><strong>Artikel</strong></td>";
				$order_table .= "<td><strong>Aantal besteld</strong></td>";
				$order_table .= "<td><strong>Geproduceerd</strong></td>";
				$order_table .= "<td><strong>Nog te produceren</strong></td>";
				$order_table .= "<td><strong>Verwachte leverdatum</strong></td>";
				$order_table .= "<td><strong>Referentie</strong></td>";
			$order_table .= "</tr>";
		foreach($orders as $order){
			
			$te_produceren = $order->aantal_besteld - $order->geproduceerd;
			
			$order_table .= "<tr>";
				$order_table .= "<td>". $order->ordernr ."</td>";
				$order_table .= "<td>". $order->product ."</td>";
				$order_table .= "<td>". $order->aantal_besteld ."</td>";
				$order_table .= "<td>". $order->geproduceerd ."</td>";
				$order_table .= "<td>". $te_produceren ."</td>";
				$order_table .= "<td>". $order->bon ."</td>";
				$order_table .= "<td>". $order->referentie ."</td>";
			$order_table .= "</tr>";
		}
		$order_table .= "</table>";
		
		$contact_row = "
			<br>
			<strong>Mail naar contactpersoon:</strong>
			<hr>
			<br>
		";
		
		$contact_row .= '<div id="radio_mail">';
		if(!empty($contactpersonen)){
			foreach($contactpersonen as $contactpersoon){
				$contact_row .= '
					<div class="form-check">
						<input class="form-check-input" type="radio" name="mail_to_radio" id="'.$contactpersoon->con_nummer.'" value="'. $contactpersoon->con_email .'" >
						<label class="form-check-label" for="'.$contactpersoon->con_nummer.'">
							'. $contactpersoon->con_email .' - ('. $contactpersoon->con_vol_naam .')
						</label>
					</div>
				';
			}
			$contact_row .= '
				<div class="form-check">
					<input class="form-check-input" type="radio" name="mail_to_radio" id="custom" value="custom_mail">
					<label class="form-check-label" for="custom">
						Ander e-mailadres invoeren.
					</label>
				</div>
			';
		}else{
			$contact_row .= "Deze debiteur heeft nog geen contactpersonen voor deze functie.";
			$contact_row .= '
				<div class="form-check">
					<input class="form-check-input" type="radio" name="mail_to_radio" id="custom1" value="custom_mail">
					<label class="form-check-label" for="custom">
						Ander e-mailadres invoeren.
					</label>
				</div>
			';
		}
		$contact_row .= '</div>';
		
		$contact_row .= '
			<div class="form-group" id="custom_mail_form">
				<input type="email" name="mail_to_custom" class="form-control" id="email_custom" placeholder="Voer custom e-mailadres in">
			</div>
		';
		
		
		$result = array(
			'order_table' => $order_table,
			'contact_row' => $contact_row
		);
		
				header("Content-Type: application/json");
		echo json_encode($result);
			
		exit();
	
	}
	
	
	public function view($ordernr = NULL){
		$data['order_item'] = $this->orders_model->getOrder($ordernr);
		$data['order_items'] = $this->orders_model->getOrderRows($ordernr);
		
		if (empty($data['order_item']))
		{
			show_404();
		}
		
		$order_log = $this->orders_model->getOrderLogs($ordernr);
		$data['order_log'] = $order_log;
		
		$data['title'] = $data['order_item']['ordernr'] . " - " .  $data['order_item']['klant'];

		$this->load->view('templates/header', $data);
		$this->load->view('orders/orderdetails', $data);
		$this->load->view('templates/footer');
	}
	
	
	public function view_Doorvoerbochten($week_nr = NULL){		
		
		//Check user access rights
		$afdeling = "Doorvoerbochten";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$afdeling_code = "Dvb";
		$data['title'] = '<i class="fas fa-wave-sine"></i> ' . $afdeling;
		$data['afdeling_name'] = $afdeling_code;
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
		
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling_code);
		
		//Get status Doorvoerbochten
		$data['status_doorvoerbochten'] = $this->orders_model->getStatus_doorvoerbochten();
		
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		$week_nr = $date->format("W");
		
		if(isset($_POST['submit_volgende'])){
			if($_POST['week_nr'] == 52){
				$_POST['week_nr'] = 01;
			}else{
				$_POST['week_nr'] = $_POST['week_nr'] + 01;
			}
		}elseif(isset($_POST['submit_vorige'])){
			if($_POST['week_nr'] == 01){
				$_POST['week_nr'] = 52;
			}else{
				$_POST['week_nr'] = $_POST['week_nr'] - 01;
				
			}
		}else{
			$_POST['week_nr'] = $week_nr;
		}
		
		if(strlen($_POST['week_nr']) == 1){
			$_POST['week_nr'] = 0 . $_POST['week_nr'];
		};
		
		$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");
		
		//Overzicht deze week		
		foreach($dagen as $dag){
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling_code, $_POST['week_nr'], $dag);
			$order_dag = ${'orders_' . $dag};
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			${'aantal_setje_' . $dag} = 0;	
			${'aantal_bochten_' . $dag} = 0;	
			${'aantal_tafel_' . $dag} = 0;
						
			foreach(${'orders_' . $dag} as $order){
				//Calculate aantal setjes
					if($order['sub_afdeling'] == "Setjes 4 bochten"){
							${'aantal_setje_' . $dag} = ${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 4 bochten"){
							${'aantal_setje_' . $dag} = ${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Setjes 5 bochten"){
							${'aantal_setje_' . $dag} =${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 5 bochten"){
							${'aantal_setje_' . $dag} =${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
				//Calculate totaal bochten
					if($order['sub_afdeling'] == "Losse bochten 75mm & kleiner"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Losse bochten > 6000mm"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + $order['te_produceren'];
					}
					//for Setjes -> calulate quantity set * te produceren
					if($order['sub_afdeling'] == "Setjes 4 bochten"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 4);
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 4 bochten"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 4);
					}
					if($order['sub_afdeling'] == "Setjes 5 bochten"){
							${'aantal_bochten_' . $dag} =${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 5);
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 5 bochten"){
							${'aantal_bochten_' . $dag} =${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 5);
					}
				//Calculate aantal tafelbochten
					if($order['sub_afdeling'] == "Sprongbochten"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bochten < 110 wel tafel"){
							${'aantal_tafel_' . $dag} =${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 90mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 110mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 125mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 160mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 200mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
			}
			
			
			$week_set[] = array(
				'dag' => $dag,
				'datum_klaar' => $datum_klaar,
				'aantal_setjes' => ${'aantal_setje_' . $dag},
				'aantal_bochten' => ${'aantal_bochten_' . $dag},
				'aantal_tafels' => ${'aantal_tafel_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag}
			);
		}
		$data['sub_afdelingen_list'] = $week_set;
		
		//Overzicht deze volgende week
		$week_2 = $_POST['week_nr'] + 01;
		
		if($_POST['week_nr'] == 52){
				$week_2 = 01;
		}
		if(strlen($week_2) == 1){
			$week_2 = 0 . $week_2;
		};
		
		$data['week_2'] = $week_2;
		
		foreach($dagen as $dag){
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling_code, $week_2, $dag);
			$order_dag = ${'orders_' . $dag};
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			${'aantal_setje_' . $dag} = 0;	
			${'aantal_bochten_' . $dag} = 0;	
			${'aantal_tafel_' . $dag} = 0;
			
			foreach(${'orders_' . $dag} as $order){
				
				
				//Calculate aantal setjes
					if($order['sub_afdeling'] == "Setjes 4 bochten"){
							${'aantal_setje_' . $dag} = ${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 4 bochten"){
							${'aantal_setje_' . $dag} = ${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Setjes 5 bochten"){
							${'aantal_setje_' . $dag} =${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 5 bochten"){
							${'aantal_setje_' . $dag} =${'aantal_setje_' . $dag} + $order['te_produceren'];
					}
				//Calculate totaal bochten
					if($order['sub_afdeling'] == "Losse bochten 75mm & kleiner"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Losse bochten > 6000mm"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + $order['te_produceren'];
					}
					//for Setjes -> calulate quantity set * te produceren
					if($order['sub_afdeling'] == "Setjes 4 bochten"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 4);
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 4 bochten"){
							${'aantal_bochten_' . $dag} = ${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 4);
					}
					if($order['sub_afdeling'] == "Setjes 5 bochten"){
							${'aantal_bochten_' . $dag} =${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 5);
					}
					if($order['sub_afdeling'] == "Setjes > 5000mm, 5 bochten"){
							${'aantal_bochten_' . $dag} =${'aantal_bochten_' . $dag} + ($order['te_produceren'] * 5);
					}
				//Calculate aantal tafelbochten
					if($order['sub_afdeling'] == "Sprongbochten"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bochten < 110 wel tafel"){
							${'aantal_tafel_' . $dag} =${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 90mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 110mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 125mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 160mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
					if($order['sub_afdeling'] == "Bocht 200mm"){
							${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
					}
			}
			
			$week_set_2[] = array(
				'dag' => $dag,
				'datum_klaar' => $datum_klaar,
				'aantal_setjes' => ${'aantal_setje_' . $dag},
				'aantal_bochten' => ${'aantal_bochten_' . $dag},
				'aantal_tafels' => ${'aantal_tafel_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag}
			);
		}
		$data['sub_afdelingen_list_2'] = $week_set_2;

		
		$this->load->view('templates/header', $data);
		$this->load->view('orders/doorvoerbochten', $data);
		$this->load->view('templates/footer');
			
	}


	public function view_pe($week_nr = NULL){		
		
		//Check user access rights
		$afdeling = "PE";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$data['afdeling_name'] = $afdeling;
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
		
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		//Put afdeling info in $data for view
		$data['afdeling'] = $afdeling;
		$data['title'] = '<i class="fas fa-circle"></i> ' . $afdeling;
		
		//Set all date variables for this view
		$year = date('Y');
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		$week_nr = $date->format("W");
		
		$week_nr2 = new DateTime($ddate);
		$week_nr2->add(new DateInterval('P1W'));
		$week_nr2 = $week_nr2->format("W");
		
		$week_nr3 = new DateTime($ddate);
		$week_nr3->add(new DateInterval('P2W'));
		$week_nr3 = $week_nr3->format("W");
		
		$week_nr4 = new DateTime($ddate);
		$week_nr4->add(new DateInterval('P3W'));
		$week_nr4 = $week_nr4->format("W");
				
		$_POST['week_nr'] = $week_nr;
		$_POST['week_nr_2'] = $week_nr2;
		$_POST['week_nr_3'] = $week_nr3;
		$_POST['week_nr_4'] = $week_nr4;
				
	//Week 1
		//Put all days of the week in array for week 1
		$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");
		
		//Loop through dagen week 1
		foreach($dagen as $dag){
			
			//Get distinct orders per sub-afdeling
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr'], $dag);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}	
			
			//Set dag overzicht in week array
			$week_set[] = array(
				'dag' => $dag,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);	
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_1'] = $week_set;
		$data['uren_overzicht_1'] = $this->get_uren($year, $week_nr, $afdeling);
		
	// END OF - Week 1
		
	//Week 2
		//Put all days of the week in array for week 2
		$dagen_2 = array("maandag_2","dinsdag_2","woensdag_2","donderdag_2","vrijdag_2");
		
		//Loop through dagen week 2
		foreach($dagen_2 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_2'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_2[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_2'] = $week_set_2;
		$data['uren_overzicht_2'] = $this->get_uren($year, $week_nr2, $afdeling);
		
	// END OF - Week 2
		
	//Week 3
		//Put all days of the week in array for week 3
		$dagen_3 = array("maandag_3","dinsdag_3","woensdag_3","donderdag_3","vrijdag_3");
		
		//Loop through dagen week 3
		foreach($dagen_3 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_3'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_3[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_3'] = $week_set_3;
		$data['uren_overzicht_3'] = $this->get_uren($year, $week_nr3, $afdeling);
		
	// END OF - Week 3
		
	//Week 4
		//Put all days of the week in array for week 4
		$dagen_4 = array("maandag_4","dinsdag_4","woensdag_4","donderdag_4","vrijdag_4");
		
		//Loop through dagen week 4
		foreach($dagen_4 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_4'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_4[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_4'] = $week_set_4;
		$data['uren_overzicht_4'] = $this->get_uren($year, $week_nr4, $afdeling);
		
	// END OF - Week 4
		
		
		
		//Load the view
		$this->load->view('templates/header', $data);
		$this->load->view('orders/pe', $data);
		$this->load->view('templates/footer');
	}

	
	public function view_putten($week_nr = NULL){		
		
		//Check user access rights
		$afdeling = "Putten";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		

		$data['afdeling_name'] = $afdeling;
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
		
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		//Put afdeling info in $data for view
		$data['afdeling'] = $afdeling;
		$data['title'] = '<i class="fas fa-filter"></i> ' . $afdeling;
		
		//Set all date variables for this view
		$year = date('Y');
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		$week_nr = $date->format("W");
		
		$week_nr2 = new DateTime($ddate);
		$week_nr2->add(new DateInterval('P1W'));
		$week_nr2 = $week_nr2->format("W");
		
		$week_nr3 = new DateTime($ddate);
		$week_nr3->add(new DateInterval('P2W'));
		$week_nr3 = $week_nr3->format("W");
		
		$week_nr4 = new DateTime($ddate);
		$week_nr4->add(new DateInterval('P3W'));
		$week_nr4 = $week_nr4->format("W");
				
		$_POST['week_nr'] = $week_nr;
		$_POST['week_nr_2'] = $week_nr2;
		$_POST['week_nr_3'] = $week_nr3;
		$_POST['week_nr_4'] = $week_nr4;
				
	//Week 1
		//Put all days of the week in array for week 1
		$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");
		
		//Loop through dagen week 1
		foreach($dagen as $dag){
			
			//Get distinct orders per sub-afdeling
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr'], $dag);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}	
			
			//Set dag overzicht in week array
			$week_set[] = array(
				'dag' => $dag,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);	
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_1'] = $week_set;
		$data['uren_overzicht_1'] = $this->get_uren($year, $week_nr, $afdeling);
		
	// END OF - Week 1
		
	//Week 2
		//Put all days of the week in array for week 2
		$dagen_2 = array("maandag_2","dinsdag_2","woensdag_2","donderdag_2","vrijdag_2");
		
		//Loop through dagen week 2
		foreach($dagen_2 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_2'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_2[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_2'] = $week_set_2;
		$data['uren_overzicht_2'] = $this->get_uren($year, $week_nr2, $afdeling);
		
	// END OF - Week 2
		
	//Week 3
		//Put all days of the week in array for week 3
		$dagen_3 = array("maandag_3","dinsdag_3","woensdag_3","donderdag_3","vrijdag_3");
		
		//Loop through dagen week 3
		foreach($dagen_3 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_3'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_3[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_3'] = $week_set_3;
		$data['uren_overzicht_3'] = $this->get_uren($year, $week_nr3, $afdeling);
		
	// END OF - Week 3
		
	//Week 4
		//Put all days of the week in array for week 4
		$dagen_4 = array("maandag_4","dinsdag_4","woensdag_4","donderdag_4","vrijdag_4");
		
		//Loop through dagen week 4
		foreach($dagen_4 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_4'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_4[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_4'] = $week_set_4;
		$data['uren_overzicht_4'] = $this->get_uren($year, $week_nr4, $afdeling);
		
	// END OF - Week 4
		
		//Load the view
		$this->load->view('templates/header', $data);
		$this->load->view('orders/putten', $data);
		$this->load->view('templates/footer');
	}
	
	
	public function view_montage($week_nr = NULL){		
			
		//Check user access rights
		$afdeling = "Montage";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$data['afdeling_name'] = $afdeling;
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
		
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		//Put afdeling info in $data for view
		$data['afdeling'] = $afdeling;
		$data['title'] = '<i class="fas fa-tools"></i> ' . $afdeling;
		
		//Set all date variables for this view
		$year = date('Y');
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		$week_nr = $date->format("W");
		
		$week_nr2 = new DateTime($ddate);
		$week_nr2->add(new DateInterval('P1W'));
		$week_nr2 = $week_nr2->format("W");
		
		$week_nr3 = new DateTime($ddate);
		$week_nr3->add(new DateInterval('P2W'));
		$week_nr3 = $week_nr3->format("W");
		
		$week_nr4 = new DateTime($ddate);
		$week_nr4->add(new DateInterval('P3W'));
		$week_nr4 = $week_nr4->format("W");
				
		$_POST['week_nr'] = $week_nr;
		$_POST['week_nr_2'] = $week_nr2;
		$_POST['week_nr_3'] = $week_nr3;
		$_POST['week_nr_4'] = $week_nr4;
				
	//Week 1
		//Put all days of the week in array for week 1
		$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");
		
		//Loop through dagen week 1
		foreach($dagen as $dag){
			
			//Get distinct orders per sub-afdeling
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr'], $dag);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}	
			
			//Set dag overzicht in week array
			$week_set[] = array(
				'dag' => $dag,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);	
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_1'] = $week_set;
		$data['uren_overzicht_1'] = $this->get_uren($year, $week_nr, $afdeling);
		
	// END OF - Week 1
		
	//Week 2
		//Put all days of the week in array for week 2
		$dagen_2 = array("maandag_2","dinsdag_2","woensdag_2","donderdag_2","vrijdag_2");
		
		//Loop through dagen week 2
		foreach($dagen_2 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_2'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_2[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_2'] = $week_set_2;
		$data['uren_overzicht_2'] = $this->get_uren($year, $week_nr2, $afdeling);
		
	// END OF - Week 2
		
	//Week 3
		//Put all days of the week in array for week 3
		$dagen_3 = array("maandag_3","dinsdag_3","woensdag_3","donderdag_3","vrijdag_3");
		
		//Loop through dagen week 3
		foreach($dagen_3 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_3'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_3[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_3'] = $week_set_3;
		$data['uren_overzicht_3'] = $this->get_uren($year, $week_nr3, $afdeling);
		
	// END OF - Week 3
		
	//Week 4
		//Put all days of the week in array for week 4
		$dagen_4 = array("maandag_4","dinsdag_4","woensdag_4","donderdag_4","vrijdag_4");
		
		//Loop through dagen week 4
		foreach($dagen_4 as $dag){
			
			//Get distinct orders per sub-afdeling
			$dag_naam = substr($dag, 0, -2);
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $_POST['week_nr_4'], $dag_naam);
			$order_dag = ${'orders_' . $dag};
			
			//Set datum klaar for view
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			
			//Set start value for aantal uren
			${'aantal_uren_' . $dag} = 0;	
			
			//Get all opmerkingen for orders per dag
			$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
			
			//Get all orders per dag and count productie uren
			foreach(${'orders_' . $dag} as $order){
				${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
			}
			
			//Set dag overzicht in week array
			$week_set_4[] = array(
				'dag' => $dag_naam,
				'datum_klaar' => $datum_klaar,
				'aantal_uren' => ${'aantal_uren_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag},
				'opmerkingen' => $opmerking
			);
		}
		
		//Put all info in $data for view
		$data['sub_afdelingen_list_4'] = $week_set_4;
		$data['uren_overzicht_4'] = $this->get_uren($year, $week_nr4, $afdeling);
		
	// END OF - Week 4
		
		//Load the view
		$this->load->view('templates/header', $data);
		$this->load->view('orders/montage', $data);
		$this->load->view('templates/footer');
	}


	public function view_logistiek($week_nr = NULL){		
			
		//Check user access rights
		$afdeling = "Logistiek";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$data['afdeling_name'] = $afdeling;
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
		
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		//Put afdeling info in $data for view
		$data['afdeling'] = $afdeling;
		$data['title'] = '<i class="fas fa-truck"></i> ' . $afdeling;
		
		//Set all date variables for this view
		$year = date('Y');
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		$week_nr = $date->format("W");
		
		$week_nr2 = new DateTime($ddate);
		$week_nr2->add(new DateInterval('P1W'));
		$week_nr2 = $week_nr2->format("W");
		
		$week_nr3 = new DateTime($ddate);
		$week_nr3->add(new DateInterval('P2W'));
		$week_nr3 = $week_nr3->format("W");
		
		$week_nr4 = new DateTime($ddate);
		$week_nr4->add(new DateInterval('P3W'));
		$week_nr4 = $week_nr4->format("W");
				
		$_POST['week_nr'] = $week_nr;
		$_POST['week_nr_2'] = $week_nr2;
		$_POST['week_nr_3'] = $week_nr3;
		$_POST['week_nr_4'] = $week_nr4;
				
	
		//Load the view
		$this->load->view('templates/header', $data);
		$this->load->view('orders/logistiek', $data);
		$this->load->view('templates/footer');
	}


	public function view_draaibank(){		
		
		//Check user access rights
		$afdeling = "Draaibank";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$data['title'] = '<i class="fas fa-circle-notch"></i> ' . $afdeling;
		$data['afdeling_name'] = $afdeling;
		
		//Get all debiteuren with orders for draaibank
		$data['debiteuren_orders_draaibank'] = $this->orders_model->getDebiteurenAfdeling("Draaibank");
		
		//Get all debiteuren with voorraad for draaibank
		$afdeling_deb = "art_Draaibank";
		$data['debiteuren_voorraad_draaibank'] = $this->voorraad_model->getDebiteurenVoorraad($afdeling_deb);
		
		
		//If isset submit mail to ORDER overzicht
		if(isset($_POST['submit-orders-debiteur'])){
			
			if(( isset($_POST['mail_to_radio']) && !empty($_POST['mail_to_radio'])) || (isset($_POST['mail_to_custom']) && !empty($_POST['mail_to_custom'])) ) {
				
				$this->load->library('email');
				$this->load->helper('email');
				$this->email->set_newline("\r\n");
				
				if($_POST['mail_to_radio'] == "custom_mail"){
					$to_email = $_POST['mail_to_custom'];			
				}else{
					$to_email = $_POST['mail_to_radio'];			
				}
				
				$debiteur_nr = $_POST['debiteur_nr'];						
				
				if(isset($_POST['mail_cc']) && !empty($_POST['mail_cc'])){
					$cc_mail = $_POST['mail_cc'];
				}else{
					$cc_mail = "";
				}
				
				$mail_intro = $_POST['mail_intro'];
				$mail_orders = $_POST['mail_orders'];
				
				$message = nl2br($mail_intro);
				$message .= "</br></hr></br>";
				$message .= $mail_orders;
								
				$this->email->from('orders@beutech.nl', 'Beutech BV');
				$this->email->to($to_email);
				$this->email->cc($cc_mail);
				$this->email->bcc('backupinternetdiensten@plusautomatisering.nl');
				$this->email->reply_to('orders@beutech.nl', 'Beutech BV');
				
				$this->email->subject("Beutech - Orderoverzicht " . $debiteur_nr);
				$this->email->message($message);
				
				$this->email->send();
				
				$this->session->set_flashdata('msg_success', 'Mail orderoverzicht is succesvol verzonden naar <strong>'.$to_email.'</strong>.');
				
			}else{
				
				$this->session->set_flashdata('msg_error', 'Mail orderoverzicht is niet verzonden.<br>Er is <strong>geen e-mailadres</strong> geselecteerd of ingevoerd.');						
				
			}
			
		} //END OF - If isset submit mail to ORDER overzicht
		
		
		//If isset submit mail to VOORRAAD overzicht
		if(isset($_POST['submit-voorraad-debiteur'])){
			
			if(( isset($_POST['mail_to_radio_voorraad']) && !empty($_POST['mail_to_radio_voorraad'])) || (isset($_POST['mail_to_custom_voorraad']) && !empty($_POST['mail_to_custom_voorraad'])) ) {
				
				$this->load->library('email');
				$this->load->helper('email');
				$this->email->set_newline("\r\n");
				
				
				if($_POST['mail_to_radio_voorraad'] == "custom_mail"){
					$to_email = $_POST['mail_to_custom_voorraad'];			
				}else{
					$to_email = $_POST['mail_to_radio_voorraad'];			
				}
				
				$debiteur_nr = $_POST['debiteur_nr'];												
				
				$mail_intro = $_POST['mail_intro_voorraad'];
				$mail_voorraad = $_POST['mail_voorraad'];
				
				$message = nl2br($mail_intro);
				$message .= "</br></hr></br>";
				$message .= $mail_voorraad;
								
				$this->email->from('orders@beutech.nl', 'Beutech BV');
				$this->email->to($to_email);				
				$this->email->bcc('backupinternetdiensten@plusautomatisering.nl');
				$this->email->reply_to('orders@beutech.nl', 'Beutech BV');
				
				$this->email->subject("Beutech - Voorraadoverzicht " . $debiteur_nr);
				$this->email->message($message);
				
				$this->email->send();
				
				$this->session->set_flashdata('msg_success', 'Mail voorraadoverzicht is succesvol verzonden naar <strong>'.$to_email.'</strong>.');
				
			}else{
				
				$this->session->set_flashdata('msg_error', 'Mail voorraadoverzicht is niet verzonden.<br>Er is <strong>geen e-mailadres</strong> geselecteerd of ingevoerd.');						
				
			}
			
		} //END OF - If isset submit mail to VOORRAAD overzicht
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
			
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		$this->load->view('templates/header', $data);
		$this->load->view('orders/draaibank', $data);
		$this->load->view('templates/footer');
			
	}

	
	// Handvorm afdeling voor SMANS 
	public function view_handvorm(){		
						
		//Check user access rights
		$afdeling = "Handvorm";
		$sub_afdeling = "Smans";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($sub_afdeling, $user_level, $user_email);
				
		$data['title'] = '<i class="fas fa-hand-paper"></i> Smans';
		$data['afdeling_name'] = "Smans";
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
		
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		//Get newest orders
		//$current_date = '2021-05-27 15:34:28';
		$current_date = new datetime('NOW');
		$current_date->sub(new DateInterval('PT72H'));
		$current_date = $current_date->format('Y-m-d H:i:s');
		$data['new_orders'] = $this->orders_model->getNewOrders($afdeling, $current_date, $sub_afdeling);
		
		
		//Get newest productiemutaties
		$current_date = new datetime('NOW');
		$current_date->sub(new DateInterval('P7D'));
		$current_date = $current_date->format('Y-m-d 17:00:00');
				
		$data['new_mutaties'] = $this->productie_model->get_new_productie_mutaties($afdeling, $current_date, $sub_afdeling);
		
		//if($this->session->userdata('gebuikersnaam') == "Tom Maandag"){
		//	echo "<pre>";
		//		print_r($data['new_mutaties']);
		//	echo "</pre>";
		//}
		
		$this->load->view('templates/header', $data);
		$this->load->view('orders/handvorm', $data);
		$this->load->view('templates/footer');
			
	}
	
	
	//Handvorm afdeling zonder SMANS
	public function view_handvorm_2(){		
						
		//Check user access rights
		$afdeling = "Handvorm";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$data['afdeling_name'] = $afdeling;
		
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
		
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		//Put afdeling info in $data for view
		$data['afdeling'] = $afdeling;
		$data['title'] = '<i class="fas fa-circle"></i> ' . $afdeling;
		
		//Load the view
		$this->load->view('templates/header', $data);
		$this->load->view('orders/handvorm_2', $data);
		$this->load->view('templates/footer');
			
	}
	
	
	public function view_extrusie(){		
		
		//Check user access rights
		$afdeling = "Extrusie";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$data['title'] = '<i class="fas fa-compress-arrows-alt"></i> ' . $afdeling;
		$data['afdeling_name'] = $afdeling;
				
		//Make afdeling list
		$data['afdeling_list_all'] = $this->orders_model->getAfdelingen();
			
		//Make sub afdeling list
		$data['sub_afdeling_list_all'] = $this->orders_model->getSubAfdelingen($afdeling);
		
		$this->load->view('templates/header', $data);
		$this->load->view('orders/extrusie', $data);
		$this->load->view('templates/footer');
			
	}

	
	public function afdelingen_werklijst_pe ($week_nr) {
		//Set afdeling
		$afdeling = "PE";
		$year = date('Y');
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		
		for ($i = 1; $i <= 4; $i++) {
			//Put all days of the week in array for week <num>
			$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");
			
			//Loop through dagen week 1
			foreach($dagen as $dag){
				
				//Get distinct orders per sub-afdeling
				${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $week_nr, $dag);
				$order_dag = ${'orders_' . $dag};
				
				//Set datum klaar for view
				if(!empty($order_dag)){
					$order_dag = array_shift($order_dag);
					$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
				}else{
					$datum_klaar = "";
				}
				
				//Set start value for aantal uren
				${'aantal_uren_' . $dag} = 0;	
				
				//Get all opmerkingen for orders per dag
				$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
				
				//Get all orders per dag and count productie uren
				foreach(${'orders_' . $dag} as $order){
					${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
				}	
				
				//Set dag overzicht in week array
				$week_set[] = array(
					'dag' => $dag,
					'datum_klaar' => $datum_klaar,
					'aantal_uren' => ${'aantal_uren_' . $dag},
					'sub_afdelingen_list' => ${'orders_' . $dag},
					'opmerkingen' => $opmerking
				);	
			}			
						
			//Put all info in $data for view
			$data['sub_afdelingen_list'][] = $week_set;
			$data['uren_overzicht'][] = $this->get_uren($year, $week_nr, $afdeling);
			$week_set = [];
			
			$datetime = new DateTime();
			$datetime->setISODate ((int)$datetime->format('o'), $week_nr, 1);
			$datetime->add(new DateInterval ('P1W'));
			$week_nr = $datetime->format("W");	
			
			
		}			

		print_r (json_encode ($data));
		
	}


	public function afdelingen_werklijst_montage ($week_nr) {
		//Set afdeling
		$afdeling = "Montage";
		$year = date('Y');
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		
		for ($i = 1; $i <= 4; $i++) {
			//Put all days of the week in array for week <num>
			$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");
			
			//Loop through dagen week 1
			foreach($dagen as $dag){
				
				//Get distinct orders per sub-afdeling
				${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $week_nr, $dag);
				$order_dag = ${'orders_' . $dag};
				
				//Set datum klaar for view
				if(!empty($order_dag)){
					$order_dag = array_shift($order_dag);
					$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
				}else{
					$datum_klaar = "";
				}
				
				//Set start value for aantal uren
				${'aantal_uren_' . $dag} = 0;	
				
				//Get all opmerkingen for orders per dag
				$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
				
				//Get all orders per dag and count productie uren
				foreach(${'orders_' . $dag} as $order){
					${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
				}	
				
				//Set dag overzicht in week array
				$week_set[] = array(
					'dag' => $dag,
					'datum_klaar' => $datum_klaar,
					'aantal_uren' => ${'aantal_uren_' . $dag},
					'sub_afdelingen_list' => ${'orders_' . $dag},
					'opmerkingen' => $opmerking
				);	
			}			
						
			//Put all info in $data for view
			$data['sub_afdelingen_list'][] = $week_set;
			$data['uren_overzicht'][] = $this->get_uren($year, $week_nr, $afdeling);
			$week_set = [];
			
			$datetime = new DateTime();
			$datetime->setISODate ((int)$datetime->format('o'), $week_nr, 1);
			$datetime->add(new DateInterval ('P1W'));
			$week_nr = $datetime->format("W");	
			
			
		}			

		print_r (json_encode ($data));
		
	}

	
	public function afdelingen_werklijst_putten ($week_nr) {
		//Set afdeling
		$afdeling = "Putten";
		$year = date('Y');
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		
		for ($i = 1; $i <= 4; $i++) {
			//Put all days of the week in array for week <num>
			$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");
			
			//Loop through dagen week 1
			foreach($dagen as $dag){
				
				//Get distinct orders per sub-afdeling
				${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling, $week_nr, $dag);
				$order_dag = ${'orders_' . $dag};
				
				//Set datum klaar for view
				if(!empty($order_dag)){
					$order_dag = array_shift($order_dag);
					$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
				}else{
					$datum_klaar = "";
				}
				
				//Set start value for aantal uren
				${'aantal_uren_' . $dag} = 0;	
				
				//Get all opmerkingen for orders per dag
				$opmerking = $this->orders_model->getRegelOpmerkingen($afdeling, $datum_klaar);
				
				//Get all orders per dag and count productie uren
				foreach(${'orders_' . $dag} as $order){
					${'aantal_uren_' . $dag} = ${'aantal_uren_' . $dag} + $order['productie_uren'];
				}	
				
				//Set dag overzicht in week array
				$week_set[] = array(
					'dag' => $dag,
					'datum_klaar' => $datum_klaar,
					'aantal_uren' => ${'aantal_uren_' . $dag},
					'sub_afdelingen_list' => ${'orders_' . $dag},
					'opmerkingen' => $opmerking
				);	
			}			
						
			//Put all info in $data for view
			$data['sub_afdelingen_list'][] = $week_set;
			$data['uren_overzicht'][] = $this->get_uren($year, $week_nr, $afdeling);
			$week_set = [];
			
			$datetime = new DateTime();
			$datetime->setISODate ((int)$datetime->format('o'), $week_nr, 1);
			$datetime->add(new DateInterval ('P1W'));
			$week_nr = $datetime->format("W");	
			
			
		}			

		print_r (json_encode ($data));
		
	}	
	
	
	public function afdelingen_werklijst_doorvoerbochten ($week_nr) {
		$afdeling = "Doorvoerbochten";
		$afdeling_code = "Dvb";
		$ddate = date('Y-m-d');
		$date = new DateTime($ddate);
		//$week_nr = 24;//$date->format("W");
		$dagen = array("maandag","dinsdag","woensdag","donderdag","vrijdag");

		foreach($dagen as $dag){
			${'orders_' . $dag} =  $this->orders_model->getSubAfdelingen($afdeling_code, $week_nr, $dag);
			$order_dag = ${'orders_' . $dag};
			if(!empty($order_dag)){
				$order_dag = array_shift($order_dag);
				$datum_klaar = system_to_euro_date($order_dag['datum_klaar']);
			}else{
				$datum_klaar = "";
			}
			${'aantal_tafel_' . $dag} = 0;			

			foreach(${'orders_' . $dag} as $order){
				if($order['sub_afdeling'] == "Sprongbochten"){
						${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
				}
				if($order['sub_afdeling'] == "Bochten < 110 wel tafel"){
						${'aantal_tafel_' . $dag} =${'aantal_tafel_' . $dag} + $order['te_produceren'];
				}
				if($order['sub_afdeling'] == "Bocht 90mm"){
						${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
				}
				if($order['sub_afdeling'] == "Bocht 110mm"){
						${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
				}
				if($order['sub_afdeling'] == "Bocht 125mm"){
						${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
				}
				if($order['sub_afdeling'] == "Bocht 160mm"){
						${'aantal_tafel_' . $dag} = ${'aantal_tafel_' . $dag} + $order['te_produceren'];
				}
			}
		
			$week_set[] = array(
				'dag' => $dag,
				'datum_klaar' => $datum_klaar,
				'aantal_tafels' => ${'aantal_tafel_' . $dag},
				'sub_afdelingen_list' => ${'orders_' . $dag}
			);
		}


		print_r (json_encode ($week_set));
	}
	
	
	public function update_status_doorvoerbochten(){
		
		$unique_string = $this->input->post("unique_string");	
		
		$unique_string = htmlspecialchars_decode($unique_string);
		
		$this->orders_model->update_status_doorvoerbochten($unique_string);
		
	}
		
	
	public function new_productie_mutatie($orderregel_id, $order_nr, $afdeling, $sub_afdeling, $aantal_geproduceerd, $administratie){
		
		$user = $this->session->userdata('gebuikersnaam');
		
		$this->orders_model->new_productie_mutatie($orderregel_id, $order_nr, $afdeling, $sub_afdeling, $aantal_geproduceerd, $administratie, $user);
		
	}
	
	
	public function update_order_row(){					
		
		$new_data_row 					= $this->input->post("new_data_row");
		$new_data 						= $this->input->post("new_data");
		$old_data						= $this->input->post("old_data");
		$afdeling_name 					= $this->input->post("afdeling");
		$geproduceerd 					= $this->input->post("geproduceerd");
		
		$new_data_row_afdeling 			= $new_data_row['1'];
		$new_data_row_sub_afdeling 		= $new_data_row['2'];
					
		if($new_data_row_afdeling != $afdeling_name){
			$new_data_row_sub_afdeling 		= "Verhuisd";
			$new_data_row_lock_afdeling 	= 1;
		}
		else if(($new_data_row_sub_afdeling == $new_data) && ($old_data != $new_data_row_sub_afdeling)){
			$new_data_row_lock_afdeling 	= 1;
		}
		else{
			$new_data_row_lock_afdeling 	= 0;
		}
		
		$new_data_row_volgorde			= 9999;
		$new_data_row_status 			= $new_data_row['3'];
		$new_data_row_debiteurnr		= $new_data_row['4'];
		$new_data_row_klant 			= $new_data_row['5'];
		$new_data_row_ordernr 			= $new_data_row['6'];
		$new_data_row_artikelnr 		= $new_data_row['7'];
		$new_data_row_opbrgroep 		= $new_data_row['8'];
		$new_data_row_soort 			= $new_data_row['9'];
		$new_data_row_product 			= $new_data_row['10'];
		$new_data_row_aantal 			= $new_data_row['11'];
		$new_data_row_geproduceerd		= $new_data_row['12'];
		$new_data_row_prio				= 0;
		$new_data_row_uren 				= $new_data_row['14'];
		$new_data_row_bon 				= nl2br($new_data_row['15']);
		$new_data_row_datum_klaar 		= $new_data_row['16'];
		$new_data_row_week_klaar 		= $new_data_row['17'];
		$new_data_row_dag_klaar 		= $new_data_row['18'];
		$new_data_row_leverdatum 		= $new_data_row['19'];
		$new_data_row_week 				= $new_data_row['20'];
		$new_data_row_dag 				= $new_data_row['21'];
		$new_data_row_toon 				= $new_data_row['22'];
		$new_data_row_administratie		= $new_data_row['23'];
		$new_data_row_last_update_king 	= $new_data_row['24'];
		$new_data_row_id 				= $new_data_row['DT_RowId'];
		$new_data_row_class 			= $new_data_row['DT_RowClass'];
		
		$this->orders_model->update_order_row(
			$new_data_row_volgorde,
			$new_data_row_afdeling, 
			$new_data_row_sub_afdeling,
			$new_data_row_lock_afdeling,
			$new_data_row_status,
			$new_data_row_debiteurnr,
			$new_data_row_klant,
			$new_data_row_ordernr,
			$new_data_row_artikelnr,
			$new_data_row_opbrgroep,
			$new_data_row_soort,
			$new_data_row_product,
			$new_data_row_aantal,
			$new_data_row_geproduceerd,
			$new_data_row_prio,
			$new_data_row_uren,
			$new_data_row_bon,
			$new_data_row_datum_klaar,
			$new_data_row_week_klaar,
			$new_data_row_dag_klaar,
			$new_data_row_leverdatum,
			$new_data_row_week,
			$new_data_row_dag,
			$new_data_row_toon,
			$new_data_row_administratie,
			$new_data_row_last_update_king,
			$new_data_row_id,
			$new_data_row_class
		);
		
		$this->session->set_flashdata('msg_success', 'Regel is succesvol gewijzigd naar: <strong>' . $new_data . '</strong>');
		
		// 13(te_produceren)=11(aantal)-12(geproduceerd)
		$new_data_row['13'] = $new_data_row['11'] - $new_data_row['12'];
				
		print_r(json_encode($new_data_row));
		
		exit;
	}


	public function update_order_row_draaibank(){
	
		$new_data_row 					= $this->input->post("new_data_row");
		
		$new_data 						= $this->input->post("new_data");
		$old_data						= $this->input->post("old_data");
		$afdeling_name 					= $this->input->post("afdeling");
		$geproduceerd 					= $this->input->post("geproduceerd");
		
		$new_data_row_afdeling 			= $new_data_row['1'];
		$new_data_row_sub_afdeling 		= $new_data_row['2'];
		
		//Only execute if afdeling is draaibank
		if($afdeling_name == "Draaibank"){
			//Check if aantal geproduceerd is greater than 0
			if($geproduceerd != 0){
			
				//If aantal geproduceerd is greater than 0 -> Send mail
				if($geproduceerd > 0){
					//Send mail to verkoop with order details and aantal geproduceerd 
					
					$this->load->library('email');
					$this->load->helper('email');
					$this->email->set_newline("\r\n");					
					$this->email->from('draaibank@beutech.nl', 'Beutech BV - Pascal Moes');
					//$this->email->to('t.maandag@plusautomatisering.nl');
					$this->email->to('draaibank@beutech.nl, pascalmoes@beutech.nl');
					$this->email->bcc('backupinternetdiensten@plusautomatisering.nl');
					$message = "<h3>Wijziging op order " . $new_data_row['6'] . "</h3>";
					$message .= 'Aantal geproduceerd: ' . $geproduceerd . '<br />';
					$message .= $new_data_row['5'] . '<br />';
					
					$this->email->subject('Draaibank order update');
					$this->email->message($message);
					
					$this->email->send();
					
					//END OF - Send mail to verkoop with order details and aantal geproduceerd 
				}
				
				//Check if geproduceerde artikel is eindproduct
				$recept = $this->recepten_model->getRecept($new_data_row['7']);
				
				if(isset($recept) && !empty($recept)){
					$this->orders_model->new_voorraad_mutatie($recept, $geproduceerd, $new_data_row_afdeling, $new_data_row_sub_afdeling);
				}
				//END OF - Check if geproduceerde artikel is eindproduct
				
			} //END OF - Check if aantal geproduceerd is greater than 0
		
		} //END OF - Only execute if afdeling is draaibank	
		
		
		
		if($new_data_row_afdeling != $afdeling_name){
			$new_data_row_sub_afdeling 		= "Verhuisd";
			$new_data_row_lock_afdeling 	= 1;
		}
		else if(($new_data_row_sub_afdeling == $new_data) && ($old_data != $new_data_row_sub_afdeling)){
			$new_data_row_lock_afdeling 	= 1;
		}
		else{
			$new_data_row_lock_afdeling 	= 0;
		}
		
		if( ($new_data_row['15'] == "0") || ($new_data_row['15'] == "-") || ($new_data_row['15'] == "") ){
			$gewenste_datum = "-";
		}else{
			$new_data_row_bon = $new_data_row['15'];
			$gewenste_datum = new datetime($new_data_row_bon);
			$gewenste_datum = $gewenste_datum->format('d-m-Y');
		}
		
		$new_data_row_volgorde			= $new_data_row['0'];
		$new_data_row_status 			= $new_data_row['3'];
		$new_data_row_debiteurnr		= $new_data_row['4'];
		$new_data_row_klant 			= $new_data_row['5'];
		$new_data_row_ordernr 			= $new_data_row['6'];
		$new_data_row_artikelnr 		= $new_data_row['7'];
		$new_data_row_opbrgroep 		= $new_data_row['8'];
		$new_data_row_soort 			= $new_data_row['9'];
		$new_data_row_product 			= $new_data_row['10'];
		$new_data_row_aantal 			= $new_data_row['11'];
		$new_data_row_geproduceerd		= $new_data_row['12'];
		$new_data_row_prio				= 0;
		$new_data_row_uren 				= $new_data_row['14'];
			
		$new_data_row_datum_klaar 		= $new_data_row['16'];
		$new_data_row_week_klaar 		= $new_data_row['17'];
		$new_data_row_dag_klaar 		= $new_data_row['18'];
		$new_data_row_leverdatum 		= $new_data_row['19'];
		$new_data_row_week 				= $new_data_row['20'];
		$new_data_row_dag 				= $new_data_row['21'];
		$new_data_row_toon 				= $new_data_row['22'];
		$new_data_row_administratie		= $new_data_row['23'];
		$new_data_row_last_update_king 	= $new_data_row['24'];
		$new_data_row_id 				= $new_data_row['DT_RowId'];
		$new_data_row_class 			= $new_data_row['DT_RowClass'];
			
		$this->orders_model->update_order_row(
			$new_data_row_volgorde,
			$new_data_row_afdeling, 
			$new_data_row_sub_afdeling,
			$new_data_row_lock_afdeling,
			$new_data_row_status,
			$new_data_row_debiteurnr,
			$new_data_row_klant,
			$new_data_row_ordernr,
			$new_data_row_artikelnr,
			$new_data_row_opbrgroep,
			$new_data_row_soort,
			$new_data_row_product,
			$new_data_row_aantal,
			$new_data_row_geproduceerd,
			$new_data_row_prio,
			$new_data_row_uren,
			$gewenste_datum,
			$new_data_row_datum_klaar,
			$new_data_row_week_klaar,
			$new_data_row_dag_klaar,
			$new_data_row_leverdatum,
			$new_data_row_week,
			$new_data_row_dag,
			$new_data_row_toon,
			$new_data_row_administratie,
			$new_data_row_last_update_king,
			$new_data_row_id,
			$new_data_row_class
		);
		
		$this->session->set_flashdata('msg_success', 'Regel is succesvol gewijzigd naar: <strong>' . $new_data . '</strong>');
		
		// 13(te_produceren)=11(aantal besteld)-12(aantal geproduceerd)
		$new_data_row['13'] = $new_data_row['11'] - $new_data_row['12'];
				
		print_r(json_encode($new_data_row));
				
		exit;
	}


	public function update_order_row_logistiek(){
		
	}

	public function update_order_row_handvorm(){
				
		$this->load->library('kingwebservices');
		
		$new_data_row 					= $this->input->post("new_data_row");
		
		$new_data 						= $this->input->post("new_data");
		$old_data						= $this->input->post("old_data");
		$afdeling_name 					= $this->input->post("afdeling");
		$geproduceerd 					= $this->input->post("geproduceerd");
		
		$new_data_row_afdeling 			= $new_data_row['2'];
		$new_data_row_sub_afdeling 		= $new_data_row['3'];
		$sub_afdeling = $new_data_row_sub_afdeling;
		
		$order_nr = strip_tags($new_data_row['7']);

		//Only execute if afdeling is Handvorm
		if($afdeling_name == "Handvorm"){
			
			//Check if aantal geproduceerd is not 0
			if($geproduceerd != 0){
								
				//DON'T Send mail to verkoop with order details and aantal geproduceerd 
				/*
				$this->load->library('email');
				$this->load->helper('email');
									
				$this->email->from('handvorm@beutech.nl', 'Beutech BV - Afdeling handvorm');
				$this->email->to('ict@beutech.nl');
				//$this->email->cc('ict@beutech.nl');
				$this->email->bcc('backupinternetdiensten@plusautomatisering.nl');
				$message = "<h3>Wijziging op order " . $order_nr . "</h3>";
				$message .= 'Aantal geproduceerd: ' . $geproduceerd . '<br />';
				$message .= 'Klant: ' . $new_data_row['5'] . ' - ' . $new_data_row['6'] . '<br />';
				
				$this->email->subject('Handvorm order update');
				$this->email->message($message);
				
				$this->email->send();
				*/
				//END OF - DON'T send mail to verkoop with order details and aantal geproduceerd 
					
				//Voeg voor de gewijzigde orderregel -> 'aantal geproduceerd' toe aan 'deze levering' in King via Webservices 
				$order_nr = $order_nr;
				$orderregel_id = $new_data_row['32'];;
				$afdeling = $afdeling_name;
				$aantal_geproduceerd = $geproduceerd;
				$administratie = $new_data_row['28'];								
				
				$this->new_productie_mutatie($orderregel_id, $order_nr, $afdeling, $sub_afdeling, $aantal_geproduceerd, $administratie);
			
			} //END OF - Check if aantal geproduceerd is not 0
					
		} //END OF - Only execute if afdeling is Handvorm
		
		
		if($new_data_row_afdeling != $afdeling_name){
			$new_data_row_sub_afdeling 		= "Verhuisd";
			$new_data_row_lock_afdeling 	= 1;
		}
		else if(($new_data_row_sub_afdeling == $new_data) && ($old_data != $new_data_row_sub_afdeling)){
			$new_data_row_lock_afdeling 	= 1;
		}
		else{
			$new_data_row_lock_afdeling 	= 0;
		}
		
		if( ($new_data_row['20'] == "0") || ($new_data_row['20'] == "-") || ($new_data_row['20'] == "") ){
			$gewenste_datum = "-";
		}else{
			$new_data_row_bon = $new_data_row['20'];
			$gewenste_datum = new datetime($new_data_row_bon);
			$gewenste_datum = $gewenste_datum->format('d-m-Y');
		}
		
		$new_data_row_volgorde			= $new_data_row['0'];
		$new_data_row_status 			= $new_data_row['4'];
		$new_data_row_debiteurnr		= $new_data_row['5'];
		$new_data_row_klant 			= $new_data_row['6'];
		$new_data_row_ordernr 			= $new_data_row['7'];
		$new_data_row_artikelnr 		= $new_data_row['8'];
		$new_data_row_opbrgroep 		= $new_data_row['9'];
		$new_data_row_soort 			= $new_data_row['10'];
		$new_data_row_product 			= $new_data_row['11'];
		$new_data_row_aantal 			= $new_data_row['12'];
		$new_data_row_geproduceerd		= $new_data_row['13'];
		$new_data_row_prio				= $new_data_row['14'];
		$new_data_row_uren 				= $new_data_row['19'];
		$new_data_row_datum_klaar 		= $new_data_row['21'];
		$new_data_row_week_klaar 		= $new_data_row['22'];
		$new_data_row_dag_klaar 		= $new_data_row['23'];
		$new_data_row_leverdatum 		= $new_data_row['24'];
		$new_data_row_week 				= $new_data_row['25'];
		$new_data_row_dag 				= $new_data_row['26'];
		$new_data_row_toon 				= $new_data_row['27'];
		$new_data_row_administratie		= $new_data_row['28'];
		$new_data_row_last_update_king 	= $new_data_row['29'];
		$new_data_row_id 				= $new_data_row['DT_RowId'];
		$new_data_row_class 			= $new_data_row['DT_RowClass'];
			
		$this->orders_model->update_order_row(
			$new_data_row_volgorde,
			$new_data_row_afdeling, 
			$new_data_row_sub_afdeling,
			$new_data_row_lock_afdeling,
			$new_data_row_status,
			$new_data_row_debiteurnr,
			$new_data_row_klant,
			$new_data_row_ordernr,
			$new_data_row_artikelnr,
			$new_data_row_opbrgroep,
			$new_data_row_soort,
			$new_data_row_product,
			$new_data_row_aantal,
			$new_data_row_geproduceerd,
			$new_data_row_prio,
			$new_data_row_uren,
			$gewenste_datum,
			$new_data_row_datum_klaar,
			$new_data_row_week_klaar,
			$new_data_row_dag_klaar,
			$new_data_row_leverdatum,
			$new_data_row_week,
			$new_data_row_dag,
			$new_data_row_toon,
			$new_data_row_administratie,
			$new_data_row_last_update_king,
			$new_data_row_id,
			$new_data_row_class
		);
		
		$this->session->set_flashdata('msg_success', 'Regel is succesvol gewijzigd naar: <strong>' . $new_data . '</strong>');
		
		
		
		// 14(te_produceren)=12(aantal)-14(prio)
		$prio = 0;
		if(isset($new_data_row['14']) && !empty($new_data_row['14'])){
			$prio = $new_data_row['14'];
		}
		$new_data_row['15'] = $new_data_row['12'] - $prio;
				
		print_r(json_encode($new_data_row));
				
		exit;
	}


	public function update_order_volgorde(){
			
		$id 							= $this->input->post("id");
		$volgorde						= $this->input->post("volgorde");
			
		$this->orders_model->update_order_volgorde(
			$id,
			$volgorde
		);
		
		//$this->session->set_flashdata('msg_success', 'Regel is succesvol gewijzigd naar: <strong>' . $new_data . '</strong>');
				
		exit;
	}


	public function update_order_rows(){
		
		$selected_rows = $this->input->post("selected_rows");
		
		foreach($selected_rows as $row_id){
			$this->orders_model->update_order_rows($row_id);
		}
				
		$this->session->set_flashdata('msg_success', 'Regels zijn succesvol gewijzigd');
				
		print_r(json_encode($selected_rows));
				
		exit;
	}
	
	
	public function get_uren($year, $week_nr, $afdeling){
		$uren_overzicht = $this->orders_model->get_uren($year, $week_nr, $afdeling);
		return $uren_overzicht;
	}
	
	
	public function update_uren(){
		$year = date('Y');
		$id = $this->input->post("id");
		$afdeling = $this->input->post("afdeling");
		$year = $year;
		$week = $this->input->post("week");
		$uren_maandag = $this->input->post("uren_maandag");
		$uren_dinsdag = $this->input->post("uren_dinsdag");
		$uren_woensdag = $this->input->post("uren_woensdag");
		$uren_donderdag = $this->input->post("uren_donderdag");
		$uren_vrijdag = $this->input->post("uren_vrijdag");
		
		$this->orders_model->update_uren($id, $year, $week, $afdeling, $uren_maandag, $uren_dinsdag, $uren_woensdag, $uren_donderdag, $uren_vrijdag);
		
		$this->session->set_flashdata('msg_success', 'Uren voor week <strong>' . $week . '</strong> zijn succesvol gewijzigd.');
	}
	
	
	public function orders_list(){
		// Datatables Variables
		$overzicht = $this->input->post("overzicht");
		$afdeling_filter = $this->input->post("afdeling_filter");
		$sub_afdeling_filter = $this->input->post("sub_afdeling_filter");
		$exclude_sub_afdeling_filter = $this->input->post("exclude_sub_afdeling_filter");
		
		$week_klaar = $this->input->post("week_klaar");
				
		if(empty($overzicht)) {
           $overzicht = null;
        }
		
		if(empty($afdeling_filter)) {
           $afdeling_filter = null;
        }

		if(empty($sub_afdeling_filter)) {
           $sub_afdeling_filter = null;
        }		
		
		if(empty($exclude_sub_afdeling_filter)) {
           $exclude_sub_afdeling_filter = null;
        }
		
		if(empty($week_klaar)) {
           $week_klaar = null;
        } 
				
		$orders = $this->orders_model->get_orders($afdeling_filter, $week_klaar, $sub_afdeling_filter, $exclude_sub_afdeling_filter);
				
		$data_orders = array();
				
		foreach($orders->result() as $r) {
						
			//Volgorde orderregels
			if(($afdeling_filter == "Draaibank") || ($afdeling_filter == "Handvorm")){
				if($r->volgorde == 9999){ 
					$max_volgorde = $this->orders_model->getOrderVolgorde($afdeling_filter, $sub_afdeling_filter);
					$volgorde = $max_volgorde->volgorde + 1;
					
					$this->orders_model->update_order_volgorde($r->id, $volgorde);
		
				}else{
					$volgorde = $r->volgorde;
				}	
			}else{
				$volgorde = $r->volgorde;
			}
			
			//If empty afdeling show: Kies afdeling
			$afdeling = $r->afdeling;	
			if(empty($afdeling)){
				$afdeling = "<small><em>kies afdeling</em></small>";
			}	
			
			//Only show date is field is not empty
			if($r->datum_klaar == "0000-00-00 00:00:00"){
				$datum_klaar = "-";
				$datum_klaar_hidden = "-";
			}else{
				$datum_klaar = system_to_euro_date($r->datum_klaar);
				$datum_klaar_hidden = $r->datum_klaar;
			}
			
			//Only show date is field is not empty
			if($r->leverdatum == "0000-00-00 00:00:00"){
				$leverdatum = "-";
				$leverdatum_hidden = "-";
			}else{
				$leverdatum = system_to_euro_date($r->leverdatum);
				$leverdatum_hidden = $r->leverdatum;
			}
			
			//Set active class for rows
			if($r->active == 1){
				$active = "show";
			}else{
				$active = "hide";
			}
			
			//Set subafdeling class without spaces for checkbox class
			$sub_afdeling_class = str_replace(' ', '', $sub_afdeling_filter);
			
			//Set status background color for rows
			$color = "";
			if($r->status == "Nieuw"){$color = "";}
			if($r->status == "Onderdelen aanwezig"){$color = "bg-info";}
			if($r->status == "Mee bezig"){$color = "bg-warning";}
			if($r->status == "Buitendienst"){$color = "bg-info";}
			if($r->status == "WVB"){$color = "bg-secondary";}
			if($r->status == "Is klaar"){$color = "bg-success";}
			if($r->status != "Is klaar"){
				$statuscheck = "<input type=\"checkbox\" class=\"". $sub_afdeling_class. "\" />";
			} else {
				$statuscheck = "";
			}
			
			//Set lock status for afdeling/subafdeling
			$show_lock = "";
			if($r->lock_afdeling == 1){
				$show_lock = " <i class='far fa-lock-alt'></i>";
			}
						
			$last_update = system_to_euro_date_time($r->last_update);								
			
			//Check if artikelnr is eindproduct
			$recept = $this->recepten_model->getRecept($r->artikelnr);
			if(isset($recept) && !empty($recept)){
				$recept = "<a href='/recepten/".$r->artikelnr."'>" . $r->artikelnr . " <i class='fas fa-eye'></i></a>";
			}else{
				$recept = "nvt";
			}
			if($overzicht == "All"){ //Show all orders
			
				if($r->te_produceren <= 0){
					$te_produceren = 0;
				}else{
					$te_produceren = $r->te_produceren;
				}
			
				$data_orders[] = array(					
					$statuscheck,
					$afdeling,
					$r->sub_afdeling,
					$r->status,
					$r->debiteurnr,
					$r->klant,
					"<a href='/orders/".$r->ordernr."'>" . $r->ordernr . " <i class='fas fa-eye'></i></a>" . $show_lock,
					$r->artikelnr,
					$r->opbrgroep,
					$r->soort,
					$r->product,
					$r->aantal,
					$r->geproduceerd,
					$te_produceren,
					$r->productie_uren,
					$r->bon, 
					"<span class='hidden_date'>".$datum_klaar_hidden."</span>" . $datum_klaar,
					$r->week_klaar,
					$r->dag_klaar,
					"<span class='hidden_date'>".$leverdatum_hidden."</span>" . $leverdatum,
					$r->week_nr,
					$r->day,
					$active,
					$r->administratie,
					"<small>" .$last_update . "</small>",
					$recept,
					"DT_RowId" => $r->id,
					"DT_RowClass" => $active . " " .  $color			
				);
		} elseif($afdeling == "Draaibank"){ //Show orders Draaibank
				
				$teProduceren = $r->aantal_besteld - $r->geproduceerd;
		
				if($teProduceren <= 0){
					$te_produceren = 0;
				}else{
					$te_produceren = $teProduceren;
				}
		
				$data_orders[] = array(
					$volgorde,
					//$statuscheck,
					$afdeling,
					$r->sub_afdeling,
					$r->status,
					$r->debiteurnr,
					$r->klant,
					"<a href='/orders/".$r->ordernr."'>" . $r->ordernr . " <i class='fas fa-eye'></i></a>" . $show_lock,
					$r->artikelnr,
					$r->opbrgroep,
					$r->soort,
					$r->product,
					$r->aantal_besteld,
					$r->geproduceerd,
					$te_produceren,
					//$r->aantal_backorder,
					$r->productie_uren,
					$r->bon, 
					"<span class='hidden_date'>".$datum_klaar_hidden."</span>" . $datum_klaar,
					$r->week_klaar,
					$r->dag_klaar,
					"<span class='hidden_date'>".$leverdatum_hidden."</span>" . $leverdatum,
					$r->week_nr,
					$r->day,
					$active,
					$r->administratie,
					"<small>" .$last_update . "</small>",
					$recept,
					$r->referentie,
					"DT_RowId" => $r->id,
					"DT_RowClass" => $active . " " .  $color			
				);
			} elseif($afdeling == "Handvorm" && $exclude_sub_afdeling_filter == null){ //Show orders Handvorm
								
				if($r->te_produceren <= 0){
					$te_produceren = 0;
					$status = "Is klaar";
				}elseif($r->aantal_besteld != $r->te_produceren){
					$te_produceren = $r->te_produceren;
					$status = "Mee bezig";
				}else{
					$te_produceren = $r->te_produceren;
					$status = "Nieuw";
				}
				
				if($status == "Nieuw"){$color = "";}
				if($status == "Mee bezig"){$color = "bg-warning";}
				if($status == "Is klaar"){$color = "bg-success";}
				if($status != "Is klaar"){
					$statuscheck = "<input type=\"checkbox\" class=\"". $sub_afdeling_class. "\" />";
				} else {
					$statuscheck = "";
				}
			
				$data_orders[] = array(
					$volgorde,
					$statuscheck,
					$afdeling,
					$r->sub_afdeling,
					$status,
					$r->debiteurnr,
					$r->klant,
					"<a href='/orders/".$r->ordernr."'>" . $r->ordernr . " <i class='fas fa-eye'></i></a>" . $show_lock,
					$r->artikelnr,
					$r->opbrgroep,
					$r->soort,
					$r->product,
					$r->aantal_besteld,
					$r->geproduceerd,
					$r->aantal_prio,
					$te_produceren,
					$r->aantal,
					$r->aantal_geleverd,
					$r->aantal_backorder,
					$r->productie_uren,
					$r->bon, 
					"<span class='hidden_date'>".$datum_klaar_hidden."</span>" . $datum_klaar,
					$r->week_klaar,
					$r->dag_klaar,
					"<span class='hidden_date'>".$leverdatum_hidden."</span>" . $leverdatum,
					$r->week_nr,
					$r->day,
					$active,
					$r->administratie,
					"<small>" .$last_update . "</small>",
					$recept,
					$r->referentie,
					$r->orderregel_id,
					"DT_RowId" => $r->id,
					"DT_RowClass" => $active . " " .  $color			
				);
			} elseif($afdeling == "Putten"){ //Show orders Putten
				
				$teProduceren = $r->aantal_besteld - $r->geproduceerd;
			
				$te_produceren = $teProduceren;
			
				
				if($teProduceren <= 0){
					$te_produceren = 0;
					$status = "Is klaar";
				}elseif($teProduceren != $r->aantal_besteld){
					$te_produceren = $teProduceren;
					if(($r->status != "Buitendienst") && ($r->status != "WVB")){
						$status = "Mee bezig";
					}else{
						$status = $r->status;
					}
				}else{
					$te_produceren = $teProduceren;
					if(($r->status != "Buitendienst") && ($r->status != "WVB")){
						$status = "Nieuw";
					}else{
						$status = $r->status;
					}
				}
				
				
				if($status == "Nieuw"){$color = "";}
				if($status == "Mee bezig"){$color = "bg-warning";}
				if($status == "Is klaar"){$color = "bg-success";}
				if($status != "Is klaar"){
					$statuscheck = "<input type=\"checkbox\" class=\"". $sub_afdeling_class. "\" />";
				} else {
					$statuscheck = "";
				}
				
				$data_orders[] = array(
					$statuscheck,
					$afdeling,
					$r->sub_afdeling,
					$status,
					$r->debiteurnr,
					$r->klant,
					"<a href='/orders/".$r->ordernr."'>" . $r->ordernr . " <i class='fas fa-eye'></i></a>" . $show_lock,
					$r->artikelnr,
					$r->opbrgroep,
					$r->soort,
					$r->product,
					//$r->aantal,
					$r->aantal_besteld,
					$r->geproduceerd,
					$te_produceren,
					$r->productie_uren,
					$r->bon, 
					"<span class='hidden_date'>".$datum_klaar_hidden."</span>" . $datum_klaar,
					$r->week_klaar,
					$r->dag_klaar,
					"<span class='hidden_date'>".$leverdatum_hidden."</span>" . $leverdatum,
					$r->week_nr,
					$r->day,
					$active,
					$r->administratie,
					"<small>" .$last_update . "</small>",
					$recept,
					"DT_RowId" => $r->id,
					"DT_RowClass" => $active . " " .  $color			
				);
			} elseif($afdeling == "Logistiek"){ //Show orders Putten
				
				if($r->te_produceren <= 0){
					$te_produceren = 0;
				}else{
					$te_produceren = $r->te_produceren;
				}
				
				$data_orders[] = array(
					$statuscheck,
					$afdeling,
					$r->sub_afdeling,
					$r->status,
					$r->debiteurnr,
					$r->klant,
					"<a href='/orders/".$r->ordernr."'>" . $r->ordernr . " <i class='fas fa-eye'></i></a>" . $show_lock,
					$r->artikelnr,
					$r->opbrgroep,
					$r->soort,
					$r->product,
					$r->aantal_besteld,
					$r->aantal,
					$r->aantal_geleverd,
					$r->aantal_backorder,
					$r->geproduceerd,
					$te_produceren,
					$r->productie_uren,
					$r->bon, 
					"<span class='hidden_date'>".$datum_klaar_hidden."</span>" . $datum_klaar,
					$r->week_klaar,
					$r->dag_klaar,
					"<span class='hidden_date'>".$leverdatum_hidden."</span>" . $leverdatum,
					$r->week_nr,
					$r->day,
					$active,
					$r->administratie,
					"<small>" .$last_update . "</small>",
					$recept,
					"DT_RowId" => $r->id,
					"DT_RowClass" => $active . " " .  $color			
				);
			} else{ //Show orders for all the other afdelingen
				
				if($r->te_produceren <= 0){
					$te_produceren = 0;
				}else{
					$te_produceren = $r->te_produceren;
				}
				
				$data_orders[] = array(
					$statuscheck,
					$afdeling,
					$r->sub_afdeling,
					$r->status,
					$r->debiteurnr,
					$r->klant,
					"<a href='/orders/".$r->ordernr."'>" . $r->ordernr . " <i class='fas fa-eye'></i></a>" . $show_lock,
					$r->artikelnr,
					$r->opbrgroep,
					$r->soort,
					$r->product,
					$r->aantal,
					$r->geproduceerd,
					$te_produceren,
					$r->productie_uren,
					$r->bon, 
					"<span class='hidden_date'>".$datum_klaar_hidden."</span>" . $datum_klaar,
					$r->week_klaar,
					$r->dag_klaar,
					"<span class='hidden_date'>".$leverdatum_hidden."</span>" . $leverdatum,
					$r->week_nr,
					$r->day,
					$active,
					$r->administratie,
					"<small>" .$last_update . "</small>",
					$recept,
					"DT_RowId" => $r->id,
					"DT_RowClass" => $active . " " .  $color			
				);
			}
		}
		$total_orders = $this->orders_model->get_total_orders();
		
		$output = array(
			"data" => $data_orders
		);
		
		echo json_encode($output);
		exit();
    }
	
	
	public function syncOrders_tibuplast(){
		$this->orders_model->syncOrders_tibuplast();	
	}
	
	
	public function syncOrders_beutech(){
		$this->orders_model->syncOrders_beutech();	
	}
	
	
	public function truncateOrders(){
		$this->orders_model->truncateOrders();	
	}


} //END OF - Orders Controller