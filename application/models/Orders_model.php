<?php
class Orders_model extends CI_Model { // Orders Model class

	public function __construct(){
		$this->load->database();
	}		
	
	
	function getOrder($ordernr = FALSE){ // Fetch ordersdetails
	
		if (($ordernr !== FALSE) && (!empty($ordernr))){ // Fetch singel order
		
			$query = $this->db->get_where('orders', array('ordernr' => $ordernr, 'active' => 1));
			return $query->row_array();
			
		} // END OF - Fetch singel order
		
	} // END OF - Fetch orders
	
	
	function getOrderRows($ordernr = FALSE){ // Fetch ordersrows 
	
		if (($ordernr !== FALSE) && (!empty($ordernr))){ // Fetch singel order
			$this->db->order_by('orderregel_nr', 'ASC');
			$query = $this->db->get_where('orders', array('ordernr' => $ordernr));
			return $query->result_array();
			
		} // END OF - Fetch singel order
		
	} // END OF - Fetch orders


	function getNewOrders($afdeling = FALSE, $current_date = FALSE, $sub_afdeling = FALSE){ // Fetch new orders
				
		if (($afdeling !== FALSE) && (!empty($afdeling)) && ($current_date !== FALSE) ){ // Fetch new orders for afdeling
			
			if (($sub_afdeling !== FALSE) && (!empty($sub_afdeling))){
				$this->db->order_by('added', 'DESC');
				$query = $this->db->get_where('orders', array('afdeling' => $afdeling, 'sub_afdeling' => $sub_afdeling, 'active' => 1, 'added >' => $current_date));
				return $query->result();
			}else{
				$this->db->order_by('added', 'DESC');
				$query = $this->db->get_where('orders', array('afdeling' => $afdeling, 'active' => 1, 'added >' => $current_date));
				return $query->result();
			}
			
			
			
		} // END OF - Fetch new orders for afdeling
		
	} // END OF - Fetch new orders


	function getDebiteurenAfdeling($afdeling = FALSE){ // Get all debiteuren with orders for afdeling
	
		if (($afdeling !== FALSE) && (!empty($afdeling))){ 
			$this->db->select('debiteurnr, klant');
			$this->db->distinct();
			$this->db->where('afdeling', $afdeling);
			$this->db->where('active', 1);
			$this->db->order_by('klant', 'ASC');
			$query = $this->db->get('orders');
			return $query->result();
		}
		
	} // END OF - Get all debiteuren with orders for afdeling
	
	
	function get_orderregelsDebiteur($debiteurnr = FALSE, $afdeling = FALSE){ // Get all debiteuren with orders for afdeling
	
		if (($debiteurnr !== FALSE) && ($afdeling !== FALSE)){ 
			$this->db->where('debiteurnr', $debiteurnr);
			$this->db->where('afdeling', $afdeling);
			$this->db->where('active', 1);
			$this->db->order_by('volgorde', 'ASC');
			$query = $this->db->get('orders');
			return $query->result();
		}
		
	} // END OF - Get all debiteuren with orders for afdeling
	
	
	function get_contactpersonenDebiteur($debiteurnr = FALSE){ // Get all debiteuren with orders for afdeling
	
		if ($debiteurnr !== FALSE){ 
			$this->db->where('debiteurnr', $debiteurnr);
			$this->db->where('con_functie_code', "73");
			$query = $this->db->get('contactpersonen');
			return $query->result();
		}
		
	} // END OF - Get all debiteuren with orders for afdeling
	
	
	function getOrderLogs($ordernr = FALSE){ // Fetch orderrow logs 
			
		if (($ordernr !== FALSE) && (!empty($ordernr))){ // Fetch singel order log
			$this->db->order_by('orderregel_nr', 'ASC');
			$query = $this->db->get_where('order_log', array('order_nr' => $ordernr));
			return $query->result_array();			
		} // END OF - Fetch singel order log
		
	} // END OF - Fetch orderrow logs 
	
	
	function getOrders_for_week($week_nr = FALSE){ // Fetch orders for a week
		
		if (($week_nr !== FALSE) && (!empty($week_nr))){ // Fetch singel order
			
			$query = $this->db->get_where('orders', array('week_klaar' => $week_nr, 'active' => 1));
			return $query->result_array();
			
		} // END OF - Fetch singel order
		
	} // END OF - Fetch orders
	
	
	function getStatus_doorvoerbochten(){

		$this->db->select('unique_string');
		$query = $this->db->get('status_doorvoerbochten');
		return $query->result_array();
	
	}
	
	
	function update_status_doorvoerbochten($unique_string){
		
		$this->db->where('unique_string', $unique_string);
		$num_rows = $this->db->count_all_results('status_doorvoerbochten');
				
		echo $num_rows;
		
		if($num_rows >= 1){
			
			echo "Delete record";
			
			$this->db->where('unique_string', $unique_string);
			$this->db->delete('status_doorvoerbochten');
			
			
		}else{
			
			echo "Insert record";
			
			$data = array(
				'unique_string' => $unique_string
			);

			$this->db->replace('status_doorvoerbochten', $data);
			
		}		
		
	}
	
	
	function update_order_row($volgorde, $new_data_row_afdeling, $new_data_row_sub_afdeling, $new_data_row_lock_afdeling, $new_data_row_status, $new_data_row_debiteurnr, $new_data_row_klant, $new_data_row_ordernr, $new_data_row_artikelnr, 
	$new_data_row_opbrgroep, $new_data_row_soort, $new_data_row_product, $new_data_row_aantal, $new_data_row_geproduceerd, $new_data_row_prio, $new_data_row_uren, $new_data_row_bon, $new_data_row_datum_klaar, $new_data_row_week_klaar, 
	$new_data_row_dag_klaar, $new_data_row_leverdatum, $new_data_row_week, $new_data_row_dag,
	$new_data_row_toon, $new_data_row_administratie, $new_data_row_last_update_king, $new_data_row_id, $new_data_row_class){
		
		$lock_afdeling = $this->getLock_status($new_data_row_id, $orderregel_id = FALSE, $new_data_row_administratie);

		if($lock_afdeling->lock_afdeling == 1){
			$set_lock_afdeling = 1;
		}else{
			$set_lock_afdeling = $new_data_row_lock_afdeling;
		}
		
	
		$user = $this->session->userdata('gebuikersnaam');
		$order_nr = strip_tags($new_data_row_ordernr);
		log_message('debug', '-------------------- UPDATE ORDER: '. $order_nr .' -------------------- ');
		log_message('debug', 'User = ' . $user);
		log_message('debug', 'DB_ID = ' . $new_data_row_id);
		log_message('debug', 'Afdeling = ' . $new_data_row_afdeling);		
		log_message('debug', 'Sub afdeling = ' . $new_data_row_sub_afdeling);	
		log_message('debug', 'Artikelnummer = ' . $new_data_row_artikelnr);
		log_message('debug', 'Aantal = ' . $new_data_row_aantal);		
		log_message('debug', 'Geproduceerd = ' . $new_data_row_geproduceerd);					
		
		
		$data = array(
			'volgorde' => $volgorde,
			'afdeling' => $new_data_row_afdeling,
			'sub_afdeling' => $new_data_row_sub_afdeling,
			'lock_afdeling' => $set_lock_afdeling,
			'status' => $new_data_row_status,
			'productie_uren' => $new_data_row_uren,
			'geproduceerd' => $new_data_row_geproduceerd,
			'aantal_prio' => $new_data_row_prio,
			'bon' => $new_data_row_bon
		);
		$this->db->where('id', $new_data_row_id);
		$this->db->where('administratie', $new_data_row_administratie);
		$this->db->update('orders', $data);
	}
	
	
	function getOrderVolgorde($afdeling, $sub_afdeling){
				
		$this->db->select_max('volgorde');
		$this->db->where('afdeling', $afdeling);
		if(!empty($sub_afdeling)){
			$this->db->where('sub_afdeling', $sub_afdeling);
		}
		$this->db->where('active', 1);
		$this->db->where('volgorde !=', 9999);
		$query = $this->db->get('orders'); 
		
		return $query->row();
	}
	
	
	function update_order_volgorde($id, $volgorde){
				
		$data = array(
			'volgorde' => $volgorde
		);
		$this->db->where('id', $id);
		$this->db->update('orders', $data);
	
	}


	function update_order_rows($row_id){ //Update order rows 
		
		$data = array(
			'status' => "Is klaar"
		);
		$this->db->where('id', $row_id);
		$this->db->update('orders', $data);
	}


	function new_voorraad_mutatie($recept, $geproduceerd, $new_data_row_afdeling, $new_data_row_sub_afdeling){ //Insert new voorraad mutatie in database
		
		//Make default values
		$recept_eindproduct = $recept[0]['eindproduct_artnr'];
		
		$recept = json_encode($recept);
		$datum = new DateTime();
		$datum = $datum->format('Y-m-d H:i:s');
		
		//Add mutatie details to DB
		$data = array(
			'eindproduct_artnr' => $recept_eindproduct,
			'recept' => $recept,
			'afdeling' => $new_data_row_afdeling,
			'sub_afdeling' => $new_data_row_sub_afdeling,
			'datum_toegevoegd' => $datum, 
			'geproduceerd' => $geproduceerd
		);
		
		$this->db->insert('voorraad_mutaties', $data);
		// END OF - Add mutatie details to DB	
		
	}
	
	
	function new_productie_mutatie($orderregel_id, $order_nr, $afdeling, $sub_afdeling, $aantal_geproduceerd, $administratie, $user){ //Insert new productie mutatie in database
		
		//Default values
		$gesynct = 0;
		
		//Add productie mutatie details to DB
		$data = array(
			'orderregel_id' => $orderregel_id,
			'order_nr' => $order_nr,
			'afdeling' => $afdeling,
			'sub_afdeling' => $sub_afdeling,
			'aantal_geproduceerd' => $aantal_geproduceerd,
			'administratie' => $administratie,
			'gesynct' => $gesynct,
			'user' => $user
		);
		
		$this->db->insert('productie_mutaties', $data);
		// END OF - Add productie mutatie details to DB	
		
	}
	
	
	function getLock_status($order_id, $orderregel_id, $administratie){ // Get orderregel lock status
		
		$this->db->select('afdeling, sub_afdeling, lock_afdeling');
		$this->db->where('id', $order_id);
		$this->db->or_where('orderregel_id', $orderregel_id);
		$this->db->where('administratie', $administratie);
		$query = $this->db->get('orders');
		
		return $query->row();
		
	}
	
	
	function getAfdelingen(){ // Fetch all afdelingen

			$this->db->distinct();
			$this->db->select("afdeling");
			$this->db->group_by("afdeling");
			$query = $this->db->get("orders_tp");
			return $query->result_array();
		
	}
	
	
	function getSubAfdelingen($afdeling = FALSE, $week_nr = FALSE, $dag_klaar = FALSE){ // Fetch Sub afdelingen for afdeling
		
		if (($afdeling !== FALSE) && (!empty($afdeling)) && ($week_nr !== FALSE) && (!empty($week_nr))){
		
			// Custom DB view for Putten(Te produceren = aantal besteld - aantal geproduceerd)
			if($afdeling == "Putten"){
				$this->db->distinct();
				$this->db->select("datum_klaar");
				$this->db->select("sub_afdeling");
				$this->db->select("status");
				$this->db->select_sum('te_produceren');
				$this->db->select_sum('productie_uren');
				$this->db->group_by("sub_afdeling");
				$query = $this->db->get_where("orders_putten", array(
					"afdeling" => $afdeling,
					"week_klaar" => $week_nr,
					"dag_klaar" => $dag_klaar,
					"status !=" => "Is klaar",
					"active" => 1 
				));
				
				return $query->result_array();
			}
			// Default DB view (te produceren - aantal deze levering - aantal geproduceerd) 
			else{	
				$this->db->distinct();
				$this->db->select("datum_klaar");
				$this->db->select("sub_afdeling");
				$this->db->select("status");
				$this->db->select_sum('te_produceren');
				$this->db->select_sum('productie_uren');
				$this->db->where('te_produceren >', 0);
				$this->db->group_by("sub_afdeling");
				$query = $this->db->get_where("orders_tp", array(
					"afdeling" => $afdeling,
					"week_klaar" => $week_nr,
					"dag_klaar" => $dag_klaar,
					"status !=" => "Is klaar",
					"active" => 1 
				));
				
				return $query->result_array();
			}
			
		}else if (($afdeling !== FALSE) && (!empty($afdeling))){
			
			
			
			$this->db->distinct();
			$this->db->select("sub_afdeling");
			$this->db->group_by("sub_afdeling");
			$query = $this->db->get_where("orders_tp", array(
				"afdeling" => $afdeling
			));
			
			return $query->result_array();
			
		}
		
	}
	
	
	function getRegelOpmerkingen($afdeling, $datum_klaar){ // Fetch Sub afdelingen for afdeling
		if (($afdeling !== FALSE) && (!empty($afdeling)) && ($datum_klaar !== FALSE) && (!empty($datum_klaar))){
			
			$db_datum_klaar = euro_to_system_date_time($datum_klaar);
			
			$this->db->select("bon");
			$this->db->select("sub_afdeling");
			$this->db->select("artikelnr");
			$this->db->select("ordernr");
			$query = $this->db->get_where("orders", array(
				"afdeling" => $afdeling,
				"datum_klaar" => $db_datum_klaar,
				"status !=" => "Is klaar",
				"bon !=" => "-"
			));
			return $query->result_array();
			
		}
	}
	
	
	public function get_uren($year, $week_nr, $afdeling){ // Get user input productie uren per week
		$query = $this->db->get_where('uren_overzicht', array(
			'jaar' => $year,
			'week' => $week_nr,
			'afdeling' => $afdeling,
		));
		return $query->row_array();
	}
	
	
	public function update_uren($id, $year, $week, $afdeling, $uren_maandag, $uren_dinsdag, $uren_woensdag, $uren_donderdag, $uren_vrijdag){ // Update user input productie uren per week
		$data = array(
			'id' => $id,
			'jaar' => $year,
			'week' => $week,
			'afdeling' => $afdeling,
			'uren_maandag' => $uren_maandag,
			'uren_dinsdag' => $uren_dinsdag,
			'uren_woensdag' => $uren_woensdag,
			'uren_donderdag' => $uren_donderdag,
			'uren_vrijdag' => $uren_vrijdag
		);
		$this->db->replace('uren_overzicht', $data);
	}
	
	
	public function get_orders($afdeling_filter, $week_klaar, $sub_afdeling_filter, $exclude_sub_afdeling_filter){ //Get all orders for datatables
		
		if($afdeling_filter !=null) { //Get order for afdeling
			$this->db->where('afdeling', $afdeling_filter);
			$this->db->where('active', 1);
        } //END OF - Get order for afdeling
		
		if($sub_afdeling_filter != "<small><em>kies subafdeling</em></small>"){
			if($sub_afdeling_filter !=null) { //Get order for sub_afdeling
				$this->db->where('sub_afdeling', $sub_afdeling_filter);
			} //END OF - Get order for sub_afdeling
		}
		
		if($exclude_sub_afdeling_filter !=null) { //Get order for afdeling
			$this->db->where('sub_afdeling !=', $exclude_sub_afdeling_filter);			
        } //END OF - Get order for afdeling
		
		if($week_klaar !=null) { //Get order for afdeling
			$weken = explode(',', $week_klaar);
			$this->db->where_in('week_klaar', $weken);
        } //END OF - Get order for afdeling
		
		if( ($afdeling_filter != "Draaibank") && ($afdeling_filter != "Handvorm") && ($afdeling_filter != "Putten") ){
			//Only get orderregels from (x) weeks in the past. 
			$this->db->where("leverdatum >= DATE_SUB(NOW(),INTERVAL 1 WEEK)", NULL, FALSE);
		}
		
		if( ($afdeling_filter != "Handvorm") && ($afdeling_filter != "Putten") && ($afdeling_filter != "Draaibank") && ($afdeling_filter != "Logistiek") ){
			//Only get orders with aantal greater than 0
			$this->db->group_start();
				$this->db->where('aantal >', 0);
				$this->db->or_group_start();
					$this->db->where('te_produceren >', 0);
					$this->db->or_where('aantal_backorder >', 0);		
				$this->db->group_end();
			$this->db->group_end();
			//!!!OLD $this->db->where('aantal >', 0);
		}
	
		//Get data form different DB view for handvorm
		if($afdeling_filter == "Handvorm"){
			return $this->db
				->get("orders_handvorm");
		}else{
			return $this->db
				->get("orders_tp");
		}
			   
    } //END OF - Get all orders for datatables
	
	
	public function get_total_orders(){ //Get total orders for pagination
	
		$query = $this->db->select("COUNT(*) as num")->get("orders");
		$result = $query->row();
		if(isset($result)){ //If result -> Return result 
			
			return $result->num;
			
		} //END OF - If result -> Return result 
		else{ //If no result -> Return 0
		
			return 0;
			
		} //END OF - If no result -> Return 0
		
	} //END OF - Get total orders for pagination
	
	
	function in_array_r($orderregel_id , $local_orderregel_ids){ //Check if King orderregel already exists in local db
	
		return preg_match('/"'.preg_quote($orderregel_id, '/').'"/i' , json_encode($local_orderregel_ids));
		
	} //END OF - Check if orderregel already exists in local db
	
	
	function syncOrders_tibuplast(){ //Sync orders Tibuplast
		
		$odbc = new ODBC_class_tibuplast(); //Connect to King ODBC
		$vrije_rubrieken = $this->get_vrije_rubriek_naam_en_labels( $odbc );
		
		//Get today date
		$datum_start = new datetime();
		$datum_start = $datum_start->format('Y-m-d');
		
		//Get today date + 4 weeks
		$datum_eind = new datetime();
		$datum_eind->add(new DateInterval('P4W'));
		$datum_eind = $datum_eind->format('Y-m-d');
		
		//Get date for lock order sync
		$date_lock = new datetime();
		$date_lock = $date_lock->format('Y-12-31');
		
		//Get all 'openstaande orders" from King
		//
		// VrART000000Veld119	Afdeling
		// VrART000000Veld120	Subafdeling
		// VrART000000Veld121	Productietijd
		$sql="
			SELECT
				ork.OrkNawGid,
				ork.OrkGid,
				ork.OrkNummer,
				ork.Orkleverdatum,
				ork.OrkLastModified,
				ork.OrkReferentie,
				naw.NawFilNummer,
				naw.VAdrNaam1,
				art.ArtCode,
				art.ArtOpbrGrpGId,
				avr." . $vrije_rubrieken['Afdeling'] . ",
				avr." . $vrije_rubrieken['Subafdeling'] . ",
				avr." . $vrije_rubrieken['Productietijd'] . ",
				opb.OpbrGrpNummer,
				orr.OrrGid,
				orr.OrrArtGid,
				orr.OrrRegelnr,
				orr.OrrRegelsoort,
				orr.OrrTekstOpFactuur,
				orr.OrrAantalBesteldVrrdEenh,
				orr.OrrAantalLeveringVrrdEenh,
				orr.OrrAantalBackorderVrrdEenh,
				orr.OrrAantalGeleverdVrrdEenh,
				orr.OrrLastModified
			FROM 
				KingSystem.tabOrderKop ork
				LEFT JOIN KingSystem.vwKMBDebstam naw ON ork.OrkNawGid = naw.NawGidje
				LEFT JOIN KingSystem.tabOrderRegel orr ON ork.OrkGid = orr.OrrOrkGid
				LEFT JOIN KingSystem.tabArtikel art ON orr.OrrArtGid = art.ArtGid
				LEFT JOIN KingSystem.tabOpbrengstGroep opb ON art.ArtOpbrGrpGId = opb.OpbrGrpGId
				Left JOIN KingSystem.tabVrART000000 avr ON art.ArtGid = avr.vrART000000RecordId
			WHERE
				CAST(ork.OrkGid as CHAR(50))
			NOT IN
				(
					SELECT 
						qLockPkey 
					FROM 
						KingSystem.qtabLock
				)
			AND
				orr.OrrTekstOpFactuur != ''
			AND
				ork.Orkleverdatum >= 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2000-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2000-01-01'	
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Putten' THEN '2000-01-01'								
							ELSE '{$datum_start}' 
						END
					)	
			AND
				ork.Orkleverdatum <= 	
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01'
							ELSE '{$datum_eind}' 
						END
					)
			AND
				ork.Orkleverdatum != 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01'
							ELSE '{$date_lock}' 
						END
					)
			AND
				ork.Orkleverdatum IS NOT NULL	
			AND
				((avr." . $vrije_rubrieken['Afdeling'] . " != 'Niet synchroniseren' AND orr.OrrRegelsoort = 2) OR orr.OrrRegelsoort != 2)		
			ORDER BY
				ork.Orkleverdatum ASC, ork.OrkNummer ASC, orr.OrrRegelnr ASC
		";
		
		$sql_2_tibuplast="
			SELECT
				orr.OrrGid,
				avr." . $vrije_rubrieken['Afdeling'] . "
			FROM 
				KingSystem.tabOrderKop ork
				LEFT JOIN KingSystem.tabOrderRegel orr ON ork.OrkGid = orr.OrrOrkGid
				LEFT JOIN KingSystem.tabArtikel art ON orr.OrrArtGid = art.ArtGid
				Left JOIN KingSystem.tabVrART000000 avr ON art.ArtGid = avr.vrART000000RecordId
			WHERE
				ork.Orkleverdatum >= 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2000-01-01' 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2000-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Putten' THEN '2000-01-01'
							ELSE '{$datum_start}' 
						END
					)
			AND
				ork.Orkleverdatum <= 	
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01' 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01' 
							ELSE '{$datum_eind}' 
						END
					)
			AND
				ork.Orkleverdatum != 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01'							
							ELSE '{$date_lock}' 
						END
					)					
			ORDER BY
				orr.OrrGid ASC
		";
		
		if($orders = $odbc->results($sql)){ //If ODBC results: proceed

			

			//Get array -> unique orderregel ID for all rows from local database
			$administratie = "Tibuplast";
			$this->db->select('orderregel_id');
			$this->db->where('administratie', $administratie);
			$fetched_records = $this->db->get('orders');
			$local_orderregel_ids = $fetched_records->result_array();
			
			//Set counts for add / update / do nothing
			$count_add_tibuplast = 0;
			$count_update_tibuplast = 0;
			$count_nothing_tibuplast = 0;
			
			foreach($orders as $order){ //Loop through orderregels from OBDC result

				//Set administratie & vrijerubrieken
				$administratie = "Tibuplast";
				
				$key = $vrije_rubrieken['Afdeling'];
				$afdeling_code = $order->$key;

				$key = $vrije_rubrieken['Subafdeling'];
				$sub_afdeling_code = $order->$key;
				$debiteur_gid = $order->OrkNawGid;
				$debiteurnr = $order->NawFilNummer;
				
				//Set productie uren
				$key = $vrije_rubrieken['Productietijd'];
				$productie_minuten = $order->$key;
				$aantal_orderregel = $order->OrrAantalLeveringVrrdEenh;

				if(!empty($productie_minuten)){
					$productie_minuten = $aantal_orderregel * $productie_minuten;
					$productie_uren = round(($productie_minuten / 60), 2) ;
				}else{
					$productie_uren = 0;
				}
				
				//SET Unique orderregel ID AND last modified King variables
				$orderregel_id = $order->OrrGid;
				$orderregel_nr = $order->OrrRegelnr;
				
				$order_last_modified_king = $order->OrkLastModified;
				$orderregel_last_modified_king = $order->OrrLastModified;
				
				//Set add to db variables to 0
				$add_to_db = 0;
				$update_db = 0;
				
				//Check if orderregel excists in DB
				$orr_exists = $this->in_array_r($orderregel_id , $local_orderregel_ids);
				 
				if($orr_exists){ //If orderregel exists no need to add
					
					$add_to_db = 0;
					
					
					
				} //END OF - If orderregel exists no need to add
				else{ //If orderregel not exists then add to DB
				
					$add_to_db = 1;
					
				} //END OF - If orderregel not exists then add to local DB
				
				//SET last_update_king variable
				$lastupdate_king = "-";
				$lastupdate_regel_king = "-";
				
				if(isset($orderregel_id) && !empty($orderregel_id) && $add_to_db == 0){ //Get last_update_king from local database if orderregel exists 
					
					$this->db->select('last_update_king');
					$this->db->where('orderregel_id', $orderregel_id);
					$this->db->where('administratie', $administratie);
					$lastupdate_king = $this->db->get('orders')->row()->last_update_king;
					
					//Convert timestamp
					$lastupdate_king = system_to_euro_date_time($lastupdate_king);
					
					$this->db->select('last_update_regel_king');
					$this->db->where('orderregel_id', $orderregel_id);
					$this->db->where('administratie', $administratie);
					$lastupdate_regel_king = $this->db->get('orders')->row()->last_update_regel_king;
					
					//Convert timestamp
					$lastupdate_regel_king = system_to_euro_date_time($lastupdate_regel_king);

				} //END OF - Get last_update_king from local database if orderregel exists 
				
				//Convert timestamp
				$order_last_modified_king = system_to_euro_date_time($order_last_modified_king);
				$orderregel_last_modified_king = system_to_euro_date_time($orderregel_last_modified_king);
				
				//if($add_to_db == 0){ //IF orderregel exists -> UPDATE ALL
				if(($add_to_db == 0) && (($order_last_modified_king != $lastupdate_king) || ($orderregel_last_modified_king != $lastupdate_regel_king))){ //IF orderregel exists but last update is not equal then update row
				
					$update_db = 1;
					
				} //END OF - IF orderregel exists but last update is not equal then update row
				
				
				
				//INSERT ORDERS								
				if($add_to_db == 1){ //Add King orderregels to local DB
				
					$action = "Toegevoegd";
					$order_last_modified_king = $order->OrkLastModified;
					$orderregel_last_modified_king = $order->OrrLastModified;
					$debiteurnr = $order->NawFilNummer;
					$klant = utf8_encode($order->VAdrNaam1);
					$artikelnr = $order->ArtCode;
					$opbrgroep = $order->OpbrGrpNummer;
					$product = utf8_encode($order->OrrTekstOpFactuur);
					$aantal = str_replace(".000", "" ,$order->OrrAantalLeveringVrrdEenh);
					$aantal_besteld = str_replace(".000", "" ,$order->OrrAantalBesteldVrrdEenh);
					$aantal_backorder = str_replace(".000", "" ,$order->OrrAantalBackorderVrrdEenh);
					$aantal_geleverd = str_replace(".000", "" ,$order->OrrAantalGeleverdVrrdEenh);
					$ordernr = $order->OrkNummer;
					$referentie = $order->OrkReferentie;
					$datum_klaar = "0000-00-00 00:00:00";
					$opmerking = "-";
					
					if(!empty($order->Orkleverdatum)){ //Set leverdatum from King
					
						$leverdatum = $order->Orkleverdatum;
						
						$date = new DateTime($leverdatum);
						//Get weeknr from leverdatum
						$week_nr = $date->format("W");
						
						//Get day from leverdatum and translate to NL
						$day = $date->format("l");
						if($day == "Monday"){$day = "maandag";}
						elseif($day == "Tuesday"){$day = "dinsdag";}
						elseif($day == "Wednesday"){$day = "woensdag";}
						elseif($day == "Thursday"){$day = "donderdag";}
						elseif($day == "Friday"){$day = "vrijdag";}
						elseif($day == "Saterday"){$day = "zaterdag";}
						elseif($day == "Sunday"){$day = "zondag";}
						else{$day = "-";}
			
					} // END OF - Set leverdatum from King 
					else{ //Set standard leverdatum if King leverdatum is empty
					
						$leverdatum = "0000-00-00 00:00:00";
						$week_nr = "-";
						$day = "-";
							
					} //END OF - Set standard leverdatum if King leverdatum is empty
					
					//Set order soort
					if($order->OrrRegelsoort == '0'){ //Soort 0 = tekst
					
						$soort = "Tekst";
						$opbrgroep = "";						
						
					}
					elseif($order->OrrRegelsoort == '2'){ //Soort 2 = Artikel	
					
						$soort = "Artikel";	
						
					}
					else{ //all other = tarief	
					
						$soort = "Tarief";
						
					} //END OF - Set order soort
		
					//Set afdeling o.b.v. opbrengstgroep
					//Set standard values
					$active = 0;
					$afdeling = "";
					$sub_afdeling = "";
					
					//Only execute if soort = artikel
					if(isset($soort) && !empty($soort) && $soort == "Artikel"){ 
													
						$afdeling = $afdeling_code;																			
						if(!empty($afdeling) && $afdeling != "Nvt"){ //Set all afdeling options and make active 		
						
							$active = 1;
														
							if(!empty($order->Orkleverdatum) && !empty($day)){ //Set datum klaar for afdeling doorvoerbochten if King leverdatum and day are not empty
								
								if($day == "maandag"){ //Set datum klaar on friday if leverdatum is on monday
									
									$datum_klaar = new DateTime($order->Orkleverdatum);
									$datum_klaar->sub(new DateInterval('P3D'));
									$datum_klaar = $datum_klaar->format('Y-m-d');
									
								} //END OF - Set datum klaar on friday if leverdatum is on monday
								else{ //Set datum klaar 1 day before leverdatum
									
									$datum_klaar = new DateTime($order->Orkleverdatum);
									$datum_klaar->sub(new DateInterval('P1D'));
									$datum_klaar = $datum_klaar->format('Y-m-d');
									
								} //END OF - Set datum klaar 1 day before leverdatum						
								
							} //END OF - Set datum klaar for afdeling doorvoerbochten if King leverdatum is not empty
							
						} else //END OF - Set all afdeling options and make active 	
						{
							$afdeling = "<small><em>kies afdeling</em></small>";
						}
						
						$sub_afdeling = $sub_afdeling_code;
						if(!empty($sub_afdeling) && $sub_afdeling != "Nvt"){
							$sub_afdeling = $sub_afdeling_code;
						}else{
							$sub_afdeling = "<small><em>kies subafdeling</em></small>";
						}
						
						if($sub_afdeling == "Nog toewijzen"){
								$sub_afdeling = "<strong>Nog toewijzen</strong>";
						}
						
					}//END OF - Only execute if soort = artikel
					else{
						$afdeling = "Nvt";
						$sub_afdeling = "Nvt";
					}	
					
					if(!empty($datum_klaar) && $datum_klaar != "0000-00-00 00:00:00"){ //Set dag en week klaar
													
						$date_ready = new DateTime($datum_klaar);
						//Get weeknr from leverdatum
						$week_ready = $date_ready->format("W");
						
						//Get day from leverdatum and translate to NL
						$day_ready = $date_ready->format("l");
						if($day_ready == "Monday"){$day_ready = "maandag";}
						elseif($day_ready == "Tuesday"){$day_ready = "dinsdag";}
						elseif($day_ready == "Wednesday"){$day_ready = "woensdag";}
						elseif($day_ready == "Thursday"){$day_ready = "donderdag";}
						elseif($day_ready == "Friday"){$day_ready = "vrijdag";}
						elseif($day_ready == "Saterday"){$day_ready = "zaterdag";}
						elseif($day_ready == "Sunday"){$day_ready = "zondag";}
						else{$day_ready = "-";}
			
					} // END OF - Set dag en week klaar
					else{ //Set standard dag en week klaar if datum klaar is empty
					
						$week_ready = "-";
						$day_ready = "-";
							
					} //END OF - Set standard dag en week klaar if datum klaar is empty	
					
					$volgorde = 9999;
					
					//Add orders details from King into database
					$sql = "
						INSERT INTO 
						orders 
							(
								orderregel_id,
								debiteurnr,
								klant,
								ordernr,
								artikelnr,
								opbrgroep,
								orderregel_nr,					
								soort,
								product,
								aantal,
								productie_uren,
								bon,
								datum_klaar,
								week_klaar,
								dag_klaar,
								leverdatum,
								week_nr,
								day,
								afdeling,
								sub_afdeling,
								active,
								administratie,
								last_update_king,
								last_update_regel_king,
								volgorde,
								aantal_backorder,
								aantal_besteld,
								aantal_geleverd,
								referentie
							) 
						VALUES 
							(
								".$this->db->escape($orderregel_id).",
								".$this->db->escape($debiteurnr).",
								".$this->db->escape($klant).",
								".$this->db->escape($ordernr).",
								".$this->db->escape($artikelnr).",
								".$this->db->escape($opbrgroep).",
								".$this->db->escape($orderregel_nr).",							
								".$this->db->escape($soort).",
								".$this->db->escape($product).",
								".$this->db->escape($aantal).",
								".$this->db->escape($productie_uren).",
								".$this->db->escape($opmerking).",
								".$this->db->escape($datum_klaar).",
								".$this->db->escape($week_ready).",							
								".$this->db->escape($day_ready).",
								".$this->db->escape($leverdatum).",
								".$this->db->escape($week_nr).",	
								".$this->db->escape($day).",
								".$this->db->escape($afdeling).",
								".$this->db->escape($sub_afdeling).",
								".$this->db->escape($active).",
								".$this->db->escape($administratie).",
								".$this->db->escape($order_last_modified_king).",
								".$this->db->escape($orderregel_last_modified_king).",
								".$this->db->escape($volgorde).",
								".$this->db->escape($aantal_backorder).",
								".$this->db->escape($aantal_besteld).",
								".$this->db->escape($aantal_geleverd).",
								".$this->db->escape($referentie)."						
							)
					";  
					$this->db->query($sql);
					$count_add_tibuplast++;
					
					//Insert action in Order log
					//$order_info = array($orderregel_id,$debiteurnr,$klant,$ordernr,$artikelnr,$opbrgroep,$orderregel_nr,$soort,$product,$aantal,$productie_uren,$datum_klaar,$week_ready,$day_ready,$leverdatum,$week_nr,$day,$afdeling,$sub_afdeling,$sub_afdeling,$active,$administratie,$order_last_modified_king,$orderregel_last_modified_king);
					//$order_string = serialize($order_info);
					//$order_log = array(
					//	'order_nr' => $ordernr,
					//	'orderregel_nr' => $orderregel_nr,
					//	'action' => $action,
					//	'administratie' => $administratie,
					//	'order_details' => $order_string
					//);
					//$this->db->insert('order_log', $order_log);
					//END OF - Insert action in Order log
					
					//END OF - Add orders details from King into database
					
				} //END OF - Add King orderregels to local DB			
		//UPDATE ORDERS
				elseif($update_db == 1){ //Update King orderregels to local DB 
					
					$action = "Gewijzigd";	
					$order_last_modified_king = $order->OrkLastModified;
					$orderregel_last_modified_king = $order->OrrLastModified;
					$debiteurnr = $order->NawFilNummer;
					$klant = utf8_encode($order->VAdrNaam1);
					$artikelnr = $order->ArtCode;
					$opbrgroep = $order->OpbrGrpNummer;
					$product = utf8_encode($order->OrrTekstOpFactuur);
					$aantal = str_replace(".000", "" ,$order->OrrAantalLeveringVrrdEenh);
					$aantal_backorder = str_replace(".000", "" ,$order->OrrAantalBackorderVrrdEenh);
					$aantal_besteld = str_replace(".000", "" ,$order->OrrAantalBesteldVrrdEenh);
					$aantal_geleverd = str_replace(".000", "" ,$order->OrrAantalGeleverdVrrdEenh);
					$ordernr = $order->OrkNummer;
					$referentie = $order->OrkReferentie;
					$datum_klaar = "0000-00-00 00:00:00";
					
					if(!empty($order->Orkleverdatum)){ //Set leverdatum from King
					
						$leverdatum = $order->Orkleverdatum;
						
						$date = new DateTime($leverdatum);
						//Get weeknr from leverdatum
						$week_nr = $date->format("W");
						
						//Get day from leverdatum and translate to NL
						$day = $date->format("l");
						if($day == "Monday"){$day = "maandag";}
						elseif($day == "Tuesday"){$day = "dinsdag";}
						elseif($day == "Wednesday"){$day = "woensdag";}
						elseif($day == "Thursday"){$day = "donderdag";}
						elseif($day == "Friday"){$day = "vrijdag";}
						elseif($day == "Saterday"){$day = "zaterdag";}
						elseif($day == "Sunday"){$day = "zondag";}
						else{$day = "-";}
			
					} // END OF - Set leverdatum from King 
					else{ //Set standard leverdatum if King leverdatum is empty
					
						$leverdatum = "0000-00-00 00:00:00";
						$week_nr = "-";
						$day = "-";
							
					} //END OF - Set standard leverdatum if King leverdatum is empty
					
					// Set order soort 
					if($order->OrrRegelsoort == '0'){
						$soort = "Tekst";
					}elseif($order->OrrRegelsoort == '2'){
						$soort = "Artikel";
					}else{
						$soort = "Tarief";
					}
		
					//Set afdeling o.b.v. opbrengstgroep
					//Set standard values
					$active = 0;
					$afdeling = "";
					$sub_afdeling = "";
					$lock_afdeling = 0;
					
					//Only execute if soort = artikel
					if(isset($soort) && !empty($soort) && $soort == "Artikel"){ 
						
						$lock_afdeling = $this->getLock_status($new_data_row_id = FALSE, $orderregel_id, $administratie); // Get lockstatus for orderregel
						
						if($lock_afdeling->lock_afdeling == 1){ //Dont update afdeling and sub_afdeling if lock status = 1
						
							$afdeling = $lock_afdeling->afdeling;	
							$sub_afdeling = $lock_afdeling->sub_afdeling;
							
							if(!empty($afdeling) && $afdeling != "Nvt"){ //Set all afdeling options and make active 		
							
								$active = 1;
															
								if(!empty($order->Orkleverdatum) && !empty($day)){ //Set datum klaar for afdeling doorvoerbochten if King leverdatum and day are not empty
									
									if($day == "maandag"){ //Set datum klaar on friday if leverdatum is on monday
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P3D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar on friday if leverdatum is on monday
									else{ //Set datum klaar 1 day before leverdatum
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P1D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar 1 day before leverdatum						
									
								} //END OF - Set datum klaar for afdeling doorvoerbochten if King leverdatum is not empty
								
							} else //END OF - Set all afdeling options and make active 	
							{
								$afdeling = "<small><em>kies afdeling</em></small>";
							}
																			
						} //END OF - Dont update afdeling and sub_afdeling if lock status = 1
						
						else
						{ //Update afdeling and sub_afdeling if lock status != 1
													
							$afdeling = $afdeling_code;											
							
							if(!empty($afdeling) && $afdeling != "Nvt"){ //Set all afdeling options and make active 		
							
								$active = 1;
															
								if(!empty($order->Orkleverdatum) && !empty($day)){ //Set datum klaar for afdeling doorvoerbochten if King leverdatum and day are not empty
									
									if($day == "maandag"){ //Set datum klaar on friday if leverdatum is on monday
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P3D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar on friday if leverdatum is on monday
									else{ //Set datum klaar 1 day before leverdatum
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P1D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar 1 day before leverdatum						
									
								} //END OF - Set datum klaar for afdeling doorvoerbochten if King leverdatum is not empty
								
							} else //END OF - Set all afdeling options and make active 	
							{
								$afdeling = "<small><em>kies afdeling</em></small>";
							}
							
							$sub_afdeling = $sub_afdeling_code;
							if(!empty($sub_afdeling) && $sub_afdeling != "Nvt"){
								$sub_afdeling = $sub_afdeling_code;
							}else{
								$sub_afdeling = "<small><em>kies subafdeling</em></small>";
							}
							
							if($sub_afdeling == "Nog toewijzen"){
									$sub_afdeling = "<strong>Nog toewijzen</strong>";
							}
							
						} // END OF - Update afdeling and sub_afdeling if lock status != 1		


						
						
					}//END OF - Only execute if soort = artikel
					else{ // SET default afdeling is artikel soort is not artikel
						$afdeling = "Nvt";
						$sub_afdeling = "Nvt";
					} // END OF - SET default afdeling is artikel soort is not artikel
					
					if(!empty($datum_klaar)){ //Set dag en week klaar
													
						$date_ready = new DateTime($datum_klaar);
						//Get weeknr from leverdatum
						$week_ready = $date_ready->format("W");
						
						//Get day from leverdatum and translate to NL
						$day_ready = $date_ready->format("l");
						if($day_ready == "Monday"){$day_ready = "maandag";}
						elseif($day_ready == "Tuesday"){$day_ready = "dinsdag";}
						elseif($day_ready == "Wednesday"){$day_ready = "woensdag";}
						elseif($day_ready == "Thursday"){$day_ready = "donderdag";}
						elseif($day_ready == "Friday"){$day_ready = "vrijdag";}
						elseif($day_ready == "Saterday"){$day_ready = "zaterdag";}
						elseif($day_ready == "Sunday"){$day_ready = "zondag";}
						else{$day_ready = "-";}
			
					} // END OF - Set dag en week klaar
					else{ //Set standard dag en week klaar if datum klaar is empty
					
						$week_ready = "-";
						$day_ready = "-";
							
					} //END OF - Set standard dag en week klaar if datum klaar is empty
					
					//UPDATE orders details from King into database
					$sql = "
						UPDATE 
							orders 
						SET								
								debiteurnr = ".$this->db->escape($debiteurnr).",
								klant = ".$this->db->escape($klant).", 
								ordernr = ".$this->db->escape($ordernr).",
								artikelnr = ".$this->db->escape($artikelnr).", 
								opbrgroep = ".$this->db->escape($opbrgroep).",
								orderregel_nr = ".$this->db->escape($orderregel_nr).",
								soort = ".$this->db->escape($soort).", 
								product = ".$this->db->escape($product).", 
								aantal = ".$this->db->escape($aantal).",
								aantal_backorder = ".$this->db->escape($aantal_backorder).", 
								aantal_besteld = ".$this->db->escape($aantal_besteld).",
								aantal_geleverd = ".$this->db->escape($aantal_geleverd).",								
								productie_uren = ".$this->db->escape($productie_uren).",
								datum_klaar = ".$this->db->escape($datum_klaar).", 
								week_klaar = ".$this->db->escape($week_ready).", 
								dag_klaar = ".$this->db->escape($day_ready).", 
								leverdatum = ".$this->db->escape($leverdatum).",
								week_nr = ".$this->db->escape($week_nr).", 								
								day = ".$this->db->escape($day).", 
								afdeling = ".$this->db->escape($afdeling).",
								sub_afdeling = ".$this->db->escape($sub_afdeling).",
								active = ".$this->db->escape($active).",
								administratie = ".$this->db->escape($administratie).",
								last_update_king = ".$this->db->escape($order_last_modified_king).",
								last_update_regel_king = ".$this->db->escape($orderregel_last_modified_king).",
								referentie = ".$this->db->escape($referentie)."
						WHERE
							orderregel_id = ".$this->db->escape($orderregel_id)."
						AND
							administratie = 'Tibuplast'
					";  
					$this->db->query($sql);
					$count_update_tibuplast++;
					
					//$order_info = array($orderregel_id,$debiteurnr,$klant,$ordernr,$artikelnr,$opbrgroep,$orderregel_nr,$soort,$product,$aantal,$productie_uren,$datum_klaar,$week_ready,$day_ready,$leverdatum,$week_nr,$day,$afdeling,$sub_afdeling,$sub_afdeling,$active,$administratie,$order_last_modified_king,$orderregel_last_modified_king);
					//$order_string = serialize($order_info);

					//$order_log = array(
					//	'order_nr' => $ordernr,
					//	'orderregel_nr' => $orderregel_nr,
					//	'action' => $action,
					//	'administratie' => $administratie,
					//	'order_details' => $order_string
					//);

					//$this->db->insert('order_log', $order_log);
					
				}//END OF - Update King orderregels to local DB
				else{ //All orders exist in local DB and no update from King
				
					$count_nothing_tibuplast++;
					
				}//END OF - All orders exist in local DB and no update from King
				
			}//END OF - Loop through orderregels from OBDC result
			
			//Check for deleted orderregel IDs in King
			if($orders_2_tibuplast = $odbc->results($sql_2_tibuplast)){
				
				$administratie = "Tibuplast";
				
				$this->db->select('orderregel_id');
				$this->db->where('administratie', $administratie);
				$this->db->where('active', 1);
				$fetched_records = $this->db->get('orders');
				$local_orderregel_ids = $fetched_records->result_array();
				$count_delete_tibuplast = 0;
				
				//Check if local order still exicts in King
				foreach($local_orderregel_ids as $local_order){ //Loop through local orderregels
					
					//Check if orderregel excists in King
					$local_orr_exists = $this->in_array_r($local_order['orderregel_id'] , $orders_2_tibuplast);
					
					if($local_orr_exists){ //If orderregel exists in King no need to set inactive in local DB
						
						// Do nothing
					
					} //END OF - If orderregel exists in King no need to set inactive in local DB
					else{ //If orderregel not exists in King then set orderregel inactive in local DB
						
						$set_active = 0;
						$sql = "
							UPDATE 
								orders 
							SET				
								active = ".$this->db->escape($set_active)."			
							WHERE 
								orderregel_id = ".$this->db->escape($local_order['orderregel_id'])."
							AND 
								administratie = 'Tibuplast'
						";  
						if($this->db->query($sql)){
							$count_delete_tibuplast++;
						};
						
					} //END OF - If orderregel not exists in King then set orderregel inactive in local DB
				}
			}
			//END OF - Check for deleted orderregel IDs

			
			//Make ordersync info message for frontuser
			if($count_nothing_tibuplast == 1){
				$count_nothing_msg = "Er is <strong>" . $count_nothing_tibuplast . "</strong> orderregel up-to-date.<br />";
			} else{
				$count_nothing_msg = "Er zijn <strong>" . $count_nothing_tibuplast . "</strong> orderregels up-to-date.<br />";
			}
			if($count_add_tibuplast == 1){
				$count_add_msg = "Er is <strong>" . $count_add_tibuplast . "</strong> orderregel toegevoegd.<br />";
			} else{
				$count_add_msg = "Er zijn <strong>" . $count_add_tibuplast . "</strong> orderregels toegevoegd.<br />";
			}
			if($count_update_tibuplast == 1){
				$count_update_msg = "Er is <strong>" . $count_update_tibuplast . "</strong> orderregel geüpdatet.<br />";
			} else{
				$count_update_msg = "Er zijn <strong>" . $count_update_tibuplast . "</strong> orderregels geüpdatet.<br />";
			}
			if($count_delete_tibuplast == 1){
				$count_delete_msg = "Er is <strong>" . $count_delete_tibuplast . "</strong> orderregel verwijderd.<br />";
			} else{
				$count_delete_msg = "Er zijn <strong>" . $count_delete_tibuplast . "</strong> orderregels verwijderd.<br />";
			}
			$this->session->set_flashdata('msg_success', $count_update_msg . $count_add_msg . $count_delete_msg . $count_nothing_msg);
			
			if(is_cli()){
				echo "Tibuplast Up-to-date - " . $count_nothing_tibuplast . "\n";
				echo "Tibuplast Nieuw - " . $count_add_tibuplast . "\n";
				echo "Tibuplast Update - " . $count_update_tibuplast . "\n";
				echo "Tibuplast Delete - " . $count_delete_tibuplast . "\n";
			}
	
		} //END OF If ODBC results: proceed
		else{ //If no ODBC results: set error message
			
			$this->session->set_flashdata('msg_error', "Er zijn geen resultaten gevonden tijdens de ODBC synchronisatie.<br />Er zijn <strong>geen</strong> orders geüpdatet of toegevoegd.");
			
		} //END OF - If no ODBC results: set error message
		
	} //END OF - Sync orders Tibuplast
	
	function get_vrije_rubriek_naam_en_labels( $odbc, $bestandOms = 'ART' ) {
		$vr_indexed = [];
		
		$sql = "SELECT 
					vr.VrLabel, 
					vr.VrVeldnaam 
				FROM 
					KingSystem.tabVrijeRubrieken vr 
				WHERE 
					vr.VrBestandOms = '" . $bestandOms . "';";
		$vrije_rubrieken = $odbc->results($sql);
		if( !empty( $vrije_rubrieken ) ) {
			foreach( $vrije_rubrieken as $vrije_rubriek ) {
				$vr_indexed[ $vrije_rubriek->VrLabel ] = $vrije_rubriek->VrVeldnaam;
			}
		}
		
		return $vr_indexed;
	}
	
	function syncOrders_beutech(){ //Sync orders Beutech
		
		$odbc = new ODBC_class_beutech(); //Connect to King ODBC
		$vrije_rubrieken = $this->get_vrije_rubriek_naam_en_labels( $odbc );
		
		//Get today date
		$datum_start = new datetime();
		$datum_start = $datum_start->format('Y-m-d');
		
		//Get today date + 4 weeks
		$datum_eind = new datetime();
		$datum_eind->add(new DateInterval('P4W'));
		$datum_eind = $datum_eind->format('Y-m-d');
		
		//Get date for lock order sync
		$date_lock = new datetime();
		$date_lock = $date_lock->format('Y-12-31');
		
		//Get all 'openstaande orders" from King
		// 	avr.vrART000000Veld19		Vrije rubriek 'Afdeling'
		// 	avr.vrART000000Veld20		Vrije rubriek 'Subafdeling'
		// 	avr.vrART000000Veld21		Vrije rubriek 'Productietijd'
		$sql="
			SELECT
				ork.OrkNawGid,
				ork.OrkGid,
				ork.OrkNummer,
				ork.Orkleverdatum,
				ork.OrkLastModified,
				ork.OrkReferentie,
				naw.NawFilNummer,
				naw.VAdrNaam1,
				art.ArtCode,
				art.ArtOpbrGrpGId,
				avr." . $vrije_rubrieken['Afdeling'] . ",
				avr." . $vrije_rubrieken['Subafdeling'] . ",
				avr." . $vrije_rubrieken['Productietijd'] . ",
				opb.OpbrGrpNummer,
				orr.OrrGid,
				orr.OrrArtGid,
				orr.OrrRegelnr,
				orr.OrrRegelsoort,
				orr.OrrTekstOpFactuur,
				orr.OrrAantalBesteldVrrdEenh,
				orr.OrrAantalLeveringVrrdEenh,
				orr.OrrAantalBackorderVrrdEenh,
				orr.OrrAantalGeleverdVrrdEenh,
				orr.OrrLastModified
			FROM 
				KingSystem.tabOrderKop ork
				LEFT JOIN KingSystem.vwKMBDebstam naw ON ork.OrkNawGid = naw.NawGidje
				LEFT JOIN KingSystem.tabOrderRegel orr ON ork.OrkGid = orr.OrrOrkGid
				LEFT JOIN KingSystem.tabArtikel art ON orr.OrrArtGid = art.ArtGid
				LEFT JOIN KingSystem.tabOpbrengstGroep opb ON art.ArtOpbrGrpGId = opb.OpbrGrpGId
				Left JOIN KingSystem.tabVrART000000 avr ON art.ArtGid = avr.vrART000000RecordId
			WHERE
				CAST(ork.OrkGid as CHAR(50))
			NOT IN
				(
					SELECT 
						qLockPkey 
					FROM 
						KingSystem.qtabLock
				)
			AND
				orr.OrrTekstOpFactuur != ''
			AND
				ork.Orkleverdatum >= 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2000-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2000-01-01'	
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Putten' THEN '2000-01-01'								
							ELSE '{$datum_start}' 
						END
					)	
			AND
				ork.Orkleverdatum <= 	
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01'
							ELSE '{$datum_eind}' 
						END
					)
			AND
				ork.Orkleverdatum != 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01'
							ELSE '{$date_lock}' 
						END
					)
			AND
				ork.Orkleverdatum IS NOT NULL	
			AND
				((avr." . $vrije_rubrieken['Afdeling'] . " != 'Niet synchroniseren' AND orr.OrrRegelsoort = 2) OR orr.OrrRegelsoort != 2)		
			ORDER BY
				ork.Orkleverdatum ASC, ork.OrkNummer ASC, orr.OrrRegelnr ASC
		";
		
		$sql_2_beutech="
			SELECT
				orr.OrrGid,
				avr." . $vrije_rubrieken['Afdeling'] . "
			FROM 
				KingSystem.tabOrderKop ork
				LEFT JOIN KingSystem.tabOrderRegel orr ON ork.OrkGid = orr.OrrOrkGid
				LEFT JOIN KingSystem.tabArtikel art ON orr.OrrArtGid = art.ArtGid
				Left JOIN KingSystem.tabVrART000000 avr ON art.ArtGid = avr.vrART000000RecordId
			WHERE
				ork.Orkleverdatum >= 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2000-01-01' 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2000-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Putten' THEN '2000-01-01'
							ELSE '{$datum_start}' 
						END
					)
			AND
				ork.Orkleverdatum <= 	
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01' 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01' 
							ELSE '{$datum_eind}' 
						END
					)
			AND
				ork.Orkleverdatum != 
					(                       
						CASE 
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Draaibank' THEN '2080-01-01'
							WHEN avr." . $vrije_rubrieken['Afdeling'] . " = 'Handvorm' THEN '2080-01-01'							
							ELSE '{$date_lock}' 
						END
					)					
			ORDER BY
				orr.OrrGid ASC
		";

		if($orders = $odbc->results($sql)){ //If ODBC results: proceed

			//Get array -> unique orderregel ID for all rows from local database
			$administratie = "Beutech";
			$this->db->select('orderregel_id');
			$this->db->where('administratie', $administratie);
			$fetched_records = $this->db->get('orders');
			$local_orderregel_ids = $fetched_records->result_array();
			
			//Set counts for add / update / do nothing
			$count_add = 0;
			$count_update = 0;
			$count_nothing = 0;
			
			foreach($orders as $order){ //Loop through orderregels from OBDC result
				
				//Set administratie & vrijerubrieken
				$administratie = "Beutech";
				
				$key = $vrije_rubrieken['Afdeling'];
				$afdeling_code = $order->$key;

				$key = $vrije_rubrieken['Subafdeling'];
				$sub_afdeling_code = $order->$key;
				$debiteur_gid = $order->OrkNawGid;
				$debiteurnr = $order->NawFilNummer;
				
				//Set productie uren
				$key = $vrije_rubrieken['Productietijd'];
				$productie_minuten = $order->$key;
				$aantal_orderregel = $order->OrrAantalLeveringVrrdEenh;
				
				if(!empty($productie_minuten)){
					$productie_minuten = $aantal_orderregel * $productie_minuten;
					$productie_uren = round(($productie_minuten / 60), 2) ;
				}else{
					$productie_uren = 0;
				}
				
				//SET Unique orderregel ID AND last modified King variables
				$orderregel_id = $order->OrrGid;
				$orderregel_nr = $order->OrrRegelnr;
				
				$order_last_modified_king = $order->OrkLastModified;
				$orderregel_last_modified_king = $order->OrrLastModified;
				
				//Set add to db variables to 0
				$add_to_db = 0;
				$update_db = 0;
				
				//Check if orderregel excists in DB
				$orr_exists = $this->in_array_r($orderregel_id , $local_orderregel_ids);
				
				if($orr_exists){ //If orderregel exists no need to add
				
					$add_to_db = 0;
					
				} //END OF - If orderregel exists no need to add
				else{ //If orderregel not exists then add to DB

					$add_to_db = 1;
					
				} //END OF - If orderregel not exists then add to local DB
					
				//SET last_update_king variable
				$lastupdate_king = "-";
				$lastupdate_regel_king = "-";
				
				if(isset($orderregel_id) && !empty($orderregel_id) && $add_to_db == 0){ //Get last_update_king from local database if orderregel exists 
					
					$this->db->select('last_update_king');
					$this->db->where('orderregel_id', $orderregel_id);
					$this->db->where('administratie', $administratie);
					$lastupdate_king = $this->db->get('orders')->row()->last_update_king;
					
					//Convert timestamp
					$lastupdate_king = system_to_euro_date_time($lastupdate_king);
					
					$this->db->select('last_update_regel_king');
					$this->db->where('orderregel_id', $orderregel_id);
					$this->db->where('administratie', $administratie);
					$lastupdate_regel_king = $this->db->get('orders')->row()->last_update_regel_king;
					
					//Convert timestamp
					$lastupdate_regel_king = system_to_euro_date_time($lastupdate_regel_king);
										
				} //END OF - Get last_update_king from local database if orderregel exists 
				
				//Convert timestamp
				$order_last_modified_king = system_to_euro_date_time($order_last_modified_king);
				$orderregel_last_modified_king = system_to_euro_date_time($orderregel_last_modified_king);
				
				// if($add_to_db == 0){ //IF orderregel exists -> UPDATE ALL
				if(($add_to_db == 0) && (($order_last_modified_king != $lastupdate_king) || ($orderregel_last_modified_king != $lastupdate_regel_king))){ //IF orderregel exists but last update is not equal then update row
				
					$update_db = 1;
					
					
				} //END OF - IF orderregel exists but last update is not equal then update row

				//INSERT ORDERS								
				if($add_to_db == 1){ //Add King orderregels to local DB
					
					$action = "Toegevoegd";
					$order_last_modified_king = $order->OrkLastModified;
					$orderregel_last_modified_king = $order->OrrLastModified;
					$debiteurnr = $order->NawFilNummer;
					$klant = utf8_encode($order->VAdrNaam1);
					$artikelnr = $order->ArtCode;
					$opbrgroep = $order->OpbrGrpNummer;
					$product = utf8_encode($order->OrrTekstOpFactuur);
					$aantal = str_replace(".000", "" ,$order->OrrAantalLeveringVrrdEenh);
					$aantal_besteld = str_replace(".000", "" ,$order->OrrAantalBesteldVrrdEenh);
					$aantal_backorder = str_replace(".000", "" ,$order->OrrAantalBackorderVrrdEenh);
					$aantal_geleverd = str_replace(".000", "" ,$order->OrrAantalGeleverdVrrdEenh);
					$ordernr = $order->OrkNummer;
					$referentie = $order->OrkReferentie;
					$datum_klaar = "0000-00-00 00:00:00";
					$opmerking = "-";
					
					if(!empty($order->Orkleverdatum)){ //Set leverdatum from King
					
						$leverdatum = $order->Orkleverdatum;
						
						$date = new DateTime($leverdatum);
						//Get weeknr from leverdatum
						$week_nr = $date->format("W");
						
						//Get day from leverdatum and translate to NL
						$day = $date->format("l");
						if($day == "Monday"){$day = "maandag";}
						elseif($day == "Tuesday"){$day = "dinsdag";}
						elseif($day == "Wednesday"){$day = "woensdag";}
						elseif($day == "Thursday"){$day = "donderdag";}
						elseif($day == "Friday"){$day = "vrijdag";}
						elseif($day == "Saterday"){$day = "zaterdag";}
						elseif($day == "Sunday"){$day = "zondag";}
						else{$day = "-";}
			
					} // END OF - Set leverdatum from King 
					else{ //Set standard leverdatum if King leverdatum is empty
					
						$leverdatum = "0000-00-00 00:00:00";
						$week_nr = "-";
						$day = "-";
							
					} //END OF - Set standard leverdatum if King leverdatum is empty
					
					//Set order soort
					if($order->OrrRegelsoort == '0'){ //Soort 0 = tekst
					
						$soort = "Tekst";	
						$opbrgroep = "";
						
					}
					elseif($order->OrrRegelsoort == '2'){ //Soort 2 = Artikel	
					
						$soort = "Artikel";	
						
					}
					else{ //all other = tarief	
					
						$soort = "Tarief";
						
					} //END OF - Set order soort
		
					//Set afdeling o.b.v. opbrengstgroep
					//Set standard values
					$active = 0;
					$afdeling = "";
					$sub_afdeling = "";
					
					//Only execute if soort = artikel
					if(isset($soort) && !empty($soort) && $soort == "Artikel"){ 
													
						$afdeling = $afdeling_code;																			
						if(!empty($afdeling) && $afdeling != "Nvt"){ //Set all afdeling options and make active 		
						
							$active = 1;
														
							if(!empty($order->Orkleverdatum) && !empty($day)){ //Set datum klaar for afdeling doorvoerbochten if King leverdatum and day are not empty
								
								if($day == "maandag"){ //Set datum klaar on friday if leverdatum is on monday
									
									$datum_klaar = new DateTime($order->Orkleverdatum);
									$datum_klaar->sub(new DateInterval('P3D'));
									$datum_klaar = $datum_klaar->format('Y-m-d');
									
								} //END OF - Set datum klaar on friday if leverdatum is on monday
								else{ //Set datum klaar 1 day before leverdatum
									
									$datum_klaar = new DateTime($order->Orkleverdatum);
									$datum_klaar->sub(new DateInterval('P1D'));
									$datum_klaar = $datum_klaar->format('Y-m-d');
									
								} //END OF - Set datum klaar 1 day before leverdatum						
								
							} //END OF - Set datum klaar for afdeling doorvoerbochten if King leverdatum is not empty
							
						} else //END OF - Set all afdeling options and make active 	
						{
							$afdeling = "<small><em>kies afdeling</em></small>";
						}
						
						$sub_afdeling = $sub_afdeling_code;
						if(!empty($sub_afdeling) && $sub_afdeling != "Nvt"){
							$sub_afdeling = $sub_afdeling_code;
						}else{
							$sub_afdeling = "<small><em>kies subafdeling</em></small>";
						}
						
						if($sub_afdeling == "Nog toewijzen"){
								$sub_afdeling = "<strong>Nog toewijzen</strong>";
						}
						
					}//END OF - Only execute if soort = artikel
					else{
						$afdeling = "Nvt";
						$sub_afdeling = "Nvt";
					} 	

					if(!empty($datum_klaar) && $datum_klaar != "0000-00-00 00:00:00"){ //Set dag en week klaar
													
						$date_ready = new DateTime($datum_klaar);
						//Get weeknr from leverdatum
						$week_ready = $date_ready->format("W");
						
						//Get day from leverdatum and translate to NL
						$day_ready = $date_ready->format("l");
						if($day_ready == "Monday"){$day_ready = "maandag";}
						elseif($day_ready == "Tuesday"){$day_ready = "dinsdag";}
						elseif($day_ready == "Wednesday"){$day_ready = "woensdag";}
						elseif($day_ready == "Thursday"){$day_ready = "donderdag";}
						elseif($day_ready == "Friday"){$day_ready = "vrijdag";}
						elseif($day_ready == "Saterday"){$day_ready = "zaterdag";}
						elseif($day_ready == "Sunday"){$day_ready = "zondag";}
						else{$day_ready = "-";}
			
					} // END OF - Set dag en week klaar
					else{ //Set standard dag en week klaar if datum klaar is empty
					
						$week_ready = "-";
						$day_ready = "-";
							
					} //END OF - Set standard dag en week klaar if datum klaar is empty	
					
					$volgorde = 9999;
					
					//Add orders details from King into database
					$sql = "
						INSERT INTO
						orders
							(
								orderregel_id,
								debiteurnr,
								klant,
								ordernr,
								artikelnr,
								opbrgroep,
								orderregel_nr,
								soort,
								product,
								aantal,
								productie_uren,
								bon,
								datum_klaar,
								week_klaar,
								dag_klaar,
								leverdatum,
								week_nr,
								day,
								afdeling,
								sub_afdeling,
								active,
								administratie,
								last_update_king,
								last_update_regel_king,
								volgorde,
								aantal_backorder,
								aantal_besteld,
								aantal_geleverd,
								referentie
							)
						VALUES
							(
								".$this->db->escape($orderregel_id).",
								".$this->db->escape($debiteurnr).",
								".$this->db->escape($klant).",
								".$this->db->escape($ordernr).",
								".$this->db->escape($artikelnr).",
								".$this->db->escape($opbrgroep).",
								".$this->db->escape($orderregel_nr).",
								".$this->db->escape($soort).",
								".$this->db->escape($product).",
								".$this->db->escape($aantal).",
								".$this->db->escape($productie_uren	).",
								".$this->db->escape($opmerking).",
								".$this->db->escape($datum_klaar).",
								".$this->db->escape($week_ready).",
								".$this->db->escape($day_ready).",
								".$this->db->escape($leverdatum).",
								".$this->db->escape($week_nr).",
								".$this->db->escape($day).",
								".$this->db->escape($afdeling).",
								".$this->db->escape($sub_afdeling).",
								".$this->db->escape($active).",
								".$this->db->escape($administratie).",
								".$this->db->escape($order_last_modified_king).",
								".$this->db->escape($orderregel_last_modified_king).",
								".$this->db->escape($volgorde).",
								".$this->db->escape($aantal_backorder).",
								".$this->db->escape($aantal_besteld).",
								".$this->db->escape($aantal_geleverd).",
								".$this->db->escape($referentie)."
							)
					";  
					$this->db->query($sql);
					$count_add++;
					
					//Insert action in Order log
					//$order_info = array($orderregel_id,$debiteurnr,$klant,$ordernr,$artikelnr,$opbrgroep,$orderregel_nr,$soort,$product,$aantal,$productie_uren,$datum_klaar,$week_ready,$day_ready,$leverdatum,$week_nr,$day,$afdeling,$sub_afdeling,$sub_afdeling,$active,$administratie,$order_last_modified_king,$orderregel_last_modified_king);
					//$order_string = serialize($order_info);
					//$order_log = array(
					//	'order_nr' => $ordernr,
					//	'orderregel_nr' => $orderregel_nr,
					//	'action' => $action,
					//	'administratie' => $administratie,
					//	'order_details' => $order_string
					//);
					//$this->db->insert('order_log', $order_log);
					//END OF - Insert action in Order log
					
					//END OF - Add orders details from King into database
					
				} //END OF - Add King orderregels to local DB			
		//UPDATE ORDERS
				elseif($update_db == 1){ //Update King orderregels to local DB 
					$action = "Gewijzigd";											
					$order_last_modified_king = $order->OrkLastModified;
					$orderregel_last_modified_king = $order->OrrLastModified;
					$debiteurnr = $order->NawFilNummer;
					$klant = utf8_encode($order->VAdrNaam1);
					$artikelnr = $order->ArtCode;
					$opbrgroep = $order->OpbrGrpNummer;
					$product = utf8_encode($order->OrrTekstOpFactuur);
					$aantal = str_replace(".000", "" ,$order->OrrAantalLeveringVrrdEenh);
					$aantal_backorder = str_replace(".000", "" ,$order->OrrAantalBackorderVrrdEenh);
					$aantal_besteld = str_replace(".000", "" ,$order->OrrAantalBesteldVrrdEenh);
					$aantal_geleverd = str_replace(".000", "" ,$order->OrrAantalGeleverdVrrdEenh);
					$ordernr = $order->OrkNummer;
					$referentie = $order->OrkReferentie;
					$datum_klaar = "0000-00-00 00:00:00";
					
					if(!empty($order->Orkleverdatum)){ //Set leverdatum from King
					
						$leverdatum = $order->Orkleverdatum;
						
						$date = new DateTime($leverdatum);
						//Get weeknr from leverdatum
						$week_nr = $date->format("W");
						
						//Get day from leverdatum and translate to NL
						$day = $date->format("l");
						if($day == "Monday"){$day = "maandag";}
						elseif($day == "Tuesday"){$day = "dinsdag";}
						elseif($day == "Wednesday"){$day = "woensdag";}
						elseif($day == "Thursday"){$day = "donderdag";}
						elseif($day == "Friday"){$day = "vrijdag";}
						elseif($day == "Saterday"){$day = "zaterdag";}
						elseif($day == "Sunday"){$day = "zondag";}
						else{$day = "-";}
			
					} // END OF - Set leverdatum from King 
					else{ //Set standard leverdatum if King leverdatum is empty
					
						$leverdatum = "0000-00-00 00:00:00";
						$week_nr = "-";
						$day = "-";
							
					} //END OF - Set standard leverdatum if King leverdatum is empty
					
					// Set order soort 
					if($order->OrrRegelsoort == '0'){
						$soort = "Tekst";
					}elseif($order->OrrRegelsoort == '2'){
						$soort = "Artikel";
					}else{
						$soort = "Tarief";
					}
		
					//Set afdeling o.b.v. opbrengstgroep
					//Set standard values
					$active = 0;		
					$afdeling = "";
					$sub_afdeling = "";
					
					//Only execute if soort = artikel
					if(isset($soort) && !empty($soort) && $soort == "Artikel"){ 
						
						$lock_afdeling = $this->getLock_status($new_data_row_id = FALSE, $orderregel_id, $administratie); // Get lockstatus for orderregel
						
						if($lock_afdeling->lock_afdeling == 1){ //Dont update afdeling and sub_afdeling if lock status = 1
													
							$afdeling = $lock_afdeling->afdeling;	
							$sub_afdeling = $lock_afdeling->sub_afdeling;
							
							if(!empty($afdeling) && $afdeling != "Nvt"){ //Set all afdeling options and make active 		
							
								$active = 1;
															
								if(!empty($order->Orkleverdatum) && !empty($day)){ //Set datum klaar for afdeling doorvoerbochten if King leverdatum and day are not empty
									
									if($day == "maandag"){ //Set datum klaar on friday if leverdatum is on monday
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P3D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar on friday if leverdatum is on monday
									else{ //Set datum klaar 1 day before leverdatum
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P1D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar 1 day before leverdatum						
									
								} //END OF - Set datum klaar for afdeling doorvoerbochten if King leverdatum is not empty
								
							} else //END OF - Set all afdeling options and make active 	
							{
								$afdeling = "<small><em>kies afdeling</em></small>";
							}
							
						} //END OF - Dont update afdeling and sub_afdeling if lock status = 1
						
						else
						{ //Update afdeling and sub_afdeling if lock status != 1
						
							$afdeling = $afdeling_code;																			
							if(!empty($afdeling) && $afdeling != "Nvt"){ //Set all afdeling options and make active 		
							
								$active = 1;
															
								if(!empty($order->Orkleverdatum) && !empty($day)){ //Set datum klaar for afdeling doorvoerbochten if King leverdatum and day are not empty
									
									if($day == "maandag"){ //Set datum klaar on friday if leverdatum is on monday
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P3D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar on friday if leverdatum is on monday
									else{ //Set datum klaar 1 day before leverdatum
										
										$datum_klaar = new DateTime($order->Orkleverdatum);
										$datum_klaar->sub(new DateInterval('P1D'));
										$datum_klaar = $datum_klaar->format('Y-m-d');
										
									} //END OF - Set datum klaar 1 day before leverdatum						
									
								} //END OF - Set datum klaar for afdeling doorvoerbochten if King leverdatum is not empty
								
							} else //END OF - Set all afdeling options and make active 	
							{
								$afdeling = "<small><em>kies afdeling</em></small>";
							}
							
							$sub_afdeling = $sub_afdeling_code;
							if(!empty($sub_afdeling) && $sub_afdeling != "Nvt"){
								$sub_afdeling = $sub_afdeling_code;
							}else{
								$sub_afdeling = "<small><em>kies subafdeling</em></small>";
							}
							
							if($sub_afdeling == "Nog toewijzen"){
									$sub_afdeling = "<strong>Nog toewijzen</strong>";
							}
							
						} // END OF - Update afdeling and sub_afdeling if lock status != 1
						
					}//END OF - Only execute if soort = artikel
					else{
						$afdeling = "Nvt";
						$sub_afdeling = "Nvt";
					}
					
					if(!empty($datum_klaar)){ //Set dag en week klaar
													
						$date_ready = new DateTime($datum_klaar);
						//Get weeknr from leverdatum
						$week_ready = $date_ready->format("W");
						
						//Get day from leverdatum and translate to NL
						$day_ready = $date_ready->format("l");
						if($day_ready == "Monday"){$day_ready = "maandag";}
						elseif($day_ready == "Tuesday"){$day_ready = "dinsdag";}
						elseif($day_ready == "Wednesday"){$day_ready = "woensdag";}
						elseif($day_ready == "Thursday"){$day_ready = "donderdag";}
						elseif($day_ready == "Friday"){$day_ready = "vrijdag";}
						elseif($day_ready == "Saterday"){$day_ready = "zaterdag";}
						elseif($day_ready == "Sunday"){$day_ready = "zondag";}
						else{$day_ready = "-";}
			
					} // END OF - Set dag en week klaar
					else{ //Set standard dag en week klaar if datum klaar is empty
					
						$week_ready = "-";
						$day_ready = "-";
							
					} //END OF - Set standard dag en week klaar if datum klaar is empty
					
					//UPDATE orders details from King into database
					$sql = "
						UPDATE
							orders
						SET
								debiteurnr = ".$this->db->escape($debiteurnr).",
								klant = ".$this->db->escape($klant).",
								ordernr = ".$this->db->escape($ordernr).",
								artikelnr = ".$this->db->escape($artikelnr).",
								opbrgroep = ".$this->db->escape($opbrgroep).",
								orderregel_nr = ".$this->db->escape($orderregel_nr).",
								soort = ".$this->db->escape($soort).",
								product = ".$this->db->escape($product).",
								aantal = ".$this->db->escape($aantal).",
								aantal_backorder = ".$this->db->escape($aantal_backorder).",
								aantal_besteld = ".$this->db->escape($aantal_besteld).",
								aantal_geleverd = ".$this->db->escape($aantal_geleverd).",
								productie_uren = ".$this->db->escape($productie_uren).",
								datum_klaar = ".$this->db->escape($datum_klaar).",
								week_klaar = ".$this->db->escape($week_ready).",
								dag_klaar = ".$this->db->escape($day_ready).",
								leverdatum = ".$this->db->escape($leverdatum).",
								week_nr = ".$this->db->escape($week_nr).", 						
								day = ".$this->db->escape($day).",
								afdeling = ".$this->db->escape($afdeling).",
								sub_afdeling = ".$this->db->escape($sub_afdeling).",
								active = ".$this->db->escape($active).",
								administratie = ".$this->db->escape($administratie).",
								last_update_king = ".$this->db->escape($order_last_modified_king).",
								last_update_regel_king = ".$this->db->escape($orderregel_last_modified_king).",
								referentie = ".$this->db->escape($referentie)."
						WHERE
							orderregel_id = ".$this->db->escape($orderregel_id)."
						AND
							administratie = 'Beutech'
					";  
					$this->db->query($sql);
					$count_update++;
					
					//Insert action in Order log
					//$order_info = array($orderregel_id,$debiteurnr,$klant,$ordernr,$artikelnr,$opbrgroep,$orderregel_nr,$soort,$product,$aantal,$productie_uren,$datum_klaar,$week_ready,$day_ready,$leverdatum,$week_nr,$day,$afdeling,$sub_afdeling,$sub_afdeling,$active,$administratie,$order_last_modified_king,$orderregel_last_modified_king);
					//$order_string = serialize($order_info);
					//$order_log = array(
					//	'order_nr' => $ordernr,
					//	'orderregel_nr' => $orderregel_nr,
					//	'action' => $action,
					//	'administratie' => $administratie,
					//	'order_details' => $order_string
					//);
					//$this->db->insert('order_log', $order_log);
					//END OF - Insert action in Order log
					
				}//END OF - Update King orderregels to local DB
				else{ //All orders exist in local DB and no update from King
				
					$count_nothing++;
					
				}//END OF - All orders exist in local DB and no update from King
				
				
				//Get all contactpersonen for order debiteur
				$sql_contact_beutech="						
					SELECT
						con.*
					FROM
						KingSystem.vwKMBContactPersonen con
					WHERE
						con.NawGid = ".$debiteur_gid."
					AND
						con.RelEmail != ''
					AND
						con.RelFNCode = '73'
					ORDER BY
						con.RelNummer
				";	
	
				if($contactpersonen = $odbc->results($sql_contact_beutech)){  //If second ODBC results: proceed	
					
					foreach($contactpersonen as $contactpersoon){ //Loop through contactpersonen for this debiteur from OBDC result	
					
						$con_nummer = $contactpersoon->NawGid . $contactpersoon->RelNummer;
						$con_debiteurnr = $debiteurnr;
						$con_voornaam = utf8_encode($contactpersoon->RelVoornaam);
						$con_achternaam = utf8_encode($contactpersoon->RelAchternaam);
						$con_vol_naam = utf8_encode($contactpersoon->RelVolledigenaam);
						$con_geslacht = $contactpersoon->Geslacht;
						$con_telefoon = $contactpersoon->RelTelefoon;
						$con_email = utf8_encode($contactpersoon->RelEmail);
						if(isset($contactpersoon->RelFnCode) && !empty($contactpersoon->RelFnCode)){
							$con_functie_code = $contactpersoon->RelFnCode;
							$con_functie = $contactpersoon->OmsFunctie;
						}else{
							$con_functie_code = 0;
							$con_functie = '';
						}
						
						$data = array(
							'con_nummer' => $con_nummer,
							'debiteurnr' => $con_debiteurnr,
							'con_voornaam' => $con_voornaam,
							'con_achternaam' => $con_achternaam,
							'con_vol_naam' => $con_vol_naam,
							'con_geslacht' => $con_geslacht,
							'con_telefoon' => $con_telefoon,
							'con_email' => $con_email,
							'con_functie_code' => $con_functie_code,
							'con_functie' => $con_functie,
							'administratie' => $administratie
						);
						$this->db->replace('contactpersonen', $data);				
						
					}
					
				} //END OF If second ODBC results: proceed	
				
				
			}//END OF - Loop through orderregels from OBDC result
			
			
			//Check for deleted orderregel IDs in King
			if($orders_2_beutech = $odbc->results($sql_2_beutech)){
				
				$administratie = "Beutech";
				
				$this->db->select('orderregel_id');
				$this->db->where('administratie', $administratie);
				$this->db->where('active', 1);
				$fetched_records = $this->db->get('orders');
				$local_orderregel_ids = $fetched_records->result_array();
				$count_delete = 0;
				
				//Check if local order still exicts in King
				foreach($local_orderregel_ids as $local_order){ //Loop through local orderregels
					
					//Check if orderregel excists in King
					$local_orr_exists = $this->in_array_r($local_order['orderregel_id'] , $orders_2_beutech);
					
					if($local_orr_exists){ //If orderregel exists in King no need to set inactive in local DB
						
						// Do nothing
					
					} //END OF - If orderregel exists in King no need to set inactive in local DB
					else{ //If orderregel not exists in King then set orderregel inactive in local DB
						
						$set_active = 0;
						$sql = "
							UPDATE
								orders
							SET
								active = ".$this->db->escape($set_active)."
							WHERE
								orderregel_id = ".$this->db->escape($local_order['orderregel_id'])."
							AND
								administratie = 'Beutech'
						";  
						if($this->db->query($sql)){
							$count_delete++;
						};
						
					} //END OF - If orderregel not exists in King then set orderregel inactive in local DB
				}
			}
			//END OF - Check for deleted orderregel IDs
			
			//Make ordersync info message for frontuser
			if($count_nothing == 1){
				$count_nothing_msg = "Er is <strong>" . $count_nothing . "</strong> orderregel up-to-date.<br />";
			} else{
				$count_nothing_msg = "Er zijn <strong>" . $count_nothing . "</strong> orderregels up-to-date.<br />";
			}
			if($count_add == 1){
				$count_add_msg = "Er is <strong>" . $count_add . "</strong> orderregel toegevoegd.<br />";
			} else{
				$count_add_msg = "Er zijn <strong>" . $count_add . "</strong> orderregels toegevoegd.<br />";
			}
			if($count_update == 1){
				$count_update_msg = "Er is <strong>" . $count_update . "</strong> orderregel geüpdatet.<br />";
			} else{
				$count_update_msg = "Er zijn <strong>" . $count_update . "</strong> orderregels geüpdatet.<br />";
			}
			if($count_delete == 1){
				$count_delete_msg = "Er is <strong>" . $count_delete . "</strong> orderregel verwijderd.<br />";
			} else{
				$count_delete_msg = "Er zijn <strong>" . $count_delete . "</strong> orderregels verwijderd.<br />";
			}
			$this->session->set_flashdata('msg_success', $count_update_msg . $count_add_msg . $count_delete_msg . $count_nothing_msg);
			
			if(is_cli()){
				echo "Beutech Up-to-date - " . $count_nothing . "\n";
				echo "Beutech Nieuw - " . $count_add . "\n";
				echo "Beutech Update - " . $count_update . "\n";
				echo "Beutech Delete - " . $count_delete . "\n";
			}
			
		} //END OF If ODBC results: proceed
		else{ //If no ODBC results: set error message
			
			$this->session->set_flashdata('msg_error', "Er zijn geen resultaten gevonden tijdens de ODBC synchronisatie.<br />Er zijn <strong>geen</strong> orders geüpdatet of toegevoegd.");
			
		} //END OF - If no ODBC results: set error message

		
	} //END OF - Sync orders Beutech
	
	
	public function update_afdelingen($administratie, $afdeling_code, $sub_afdeling_code){
		
	}
	
	
	function truncateOrders(){ //Sync orders Beutech
		//$action = "Verwijderd";
		//$this->db->truncate('orders');
		//$this->session->set_flashdata('msg_success', "Alle orders zijn succesvol verwijderd.");
	}
	
} // END OF Orders Model class