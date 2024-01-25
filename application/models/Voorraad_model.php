<?php
class Voorraad_model extends CI_Model { // Voorraad Model class

	public function __construct(){
		$this->load->database();
	}		
	
	
	function getVoorraad($afdeling = FALSE){ // Fetch voorraadartikelen
		
		if (($afdeling !== FALSE) && (!empty($afdeling)) && ($afdeling != 'alles')){ // Fetch vooraad for afdeling
				
			$art_afdeling = "art_" . $afdeling;	
			
			$query = $this->db->get_where('voorraad', array($art_afdeling => 1));
			return $query->result_array();
		} // END OF - // Fetch vooraad for afdeling
		
		else { // Fetch all voorraad
			$query = $this->db->get('voorraad');
			return $query->result_array();
		} // END OF - Fetch all voorraad
		
	} // END OF - Fetch voorraadartikelen	
	
	
	function getArtikel_status($artikelnr){ // Check if artikel is eindproduct
		
		if (!empty($artikelnr)){ 
				
			$query = $this->db->get_where('recepten', array('eindproduct_artnr' => $artikelnr));
			if ($query->num_rows() > 0) {
				//record exists               
				return $result = true;               
			} else {
				return $result = false;
			}
		} 
				
	} // END OF - Check if artikel is eindproduct	
	
	
	function getCompnentVoorraad($art_gid){

		$this->db->select('art_vrije_voorraad');
		
		$this->db->where('art_gid', $art_gid);
		$this->db->where('art_gid', $art_gid);
		
		$query = $this->db->get('voorraad');
		return $query->row()->art_vrije_voorraad;
	}

	
	function getDebiteurenVoorraad($afdeling = FALSE){ // Get all debiteuren with voorraad for afdeling
	
		if (($afdeling !== FALSE) && (!empty($afdeling))){ 
			$this->db->select('voorraad.art_debiteur, orders.klant');
			$this->db->distinct();
			$this->db->from('voorraad');
			$this->db->join('orders', 'orders.debiteurnr = voorraad.art_debiteur');
			$this->db->where('voorraad.art_debiteur !=', 0);
			$this->db->where($afdeling . '=', 1);
			$this->db->order_by('voorraad.art_debiteur', 'ASC');
			$query = $this->db->get();
			return $query->result();
		}
		
	} // END OF - Get all debiteuren with voorraad for afdeling
		
		
	function get_voorraadregelsDebiteur($debiteurnr = FALSE, $afdeling = FALSE){ // Get all voorraad with debiteur for afdeling
	
		if (($debiteurnr !== FALSE) && ($afdeling !== FALSE)){ 
			$this->db->where('art_debiteur', $debiteurnr);
			$this->db->where($afdeling .'=', 1);
			$query = $this->db->get('voorraad');
			return $query->result();
		}
		
	} // END OF - Get all voorraad with debiteur for afdeling
	
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
	
	function syncVoorraad_beutech(){ //Sync voorraad Beutech
		
		$odbc = new ODBC_class_beutech(); //Connect to King ODBC
		$vrije_rubrieken = $this->get_vrije_rubriek_naam_en_labels( $odbc );

		// vrART000000Veld28	Voorraad sync
		// vrART000000Veld29	Voorraad tonen
		// vrART000000Veld32	Voorraad dvb
		// vrART000000Veld31	Voorraad draaibank
		// vrART000000Veld33	Voorraad extrusie
		// vrART000000Veld34	Voorraad freesbank
		// vrART000000Veld35	Voorraad handvorm
		// vrART000000Veld36	Voorraad inkoop-verkoop
		// vrART000000Veld37	Voorraad logistiek
		// vrART000000Veld38	Voorraad montage
		// vrART000000Veld39	Voorraad pe
		// vrART000000Veld40	Voorraad phytobac
		// vrART000000Veld41	Voorraad putten	
		// vrART000000Veld43	Voorraad debiteur
		
		//Get all voorraad artikelen from King
		$sql="
			SELECT 
				vrr.ArtGid,
				vrr.ArtCode,
				vrr.ArtOms,
				vrr.VrijeVoorraad,
				vrr.ArtInBestelling,
				vrr.Gereserveerd,
				vru." . $vrije_rubrieken['Voorraad sync'] . " AS voorraad_sync,
				vru." . $vrije_rubrieken['Voorraad tonen'] . " AS voorraad_tonen,
				vru." . $vrije_rubrieken['Voorraad dvb'] . " AS voorraad_dvb,
				vru." . $vrije_rubrieken['Voorraad draaibank'] . " AS voorraad_draaibank,
				vru." . $vrije_rubrieken['Voorraad extrusie'] . " AS voorraad_extrusie,
				vru." . $vrije_rubrieken['Voorraad freesbank'] . " AS voorraad_freesbank,
				vru." . $vrije_rubrieken['Voorraad handvorm'] . " AS voorraad_handvorm,
				vru." . $vrije_rubrieken['Voorraad inkoop-verkoop'] . " AS voorraad_inkoop_verkoop,
				vru." . $vrije_rubrieken['Voorraad logistiek'] . " AS voorraad_logistiek,
				vru." . $vrije_rubrieken['Voorraad montage'] . " AS voorraad_montage,
				vru." . $vrije_rubrieken['Voorraad pe'] . " AS voorraad_pe,
				vru." . $vrije_rubrieken['Voorraad phytobac'] . " AS voorraad_phytobac,
				vru." . $vrije_rubrieken['Voorraad putten'] . " AS voorraad_putten,
				vru." . $vrije_rubrieken['Voorraad dvb'] . " AS voorraad_spuitgiet,
				vru." . $vrije_rubrieken['Voorraad debiteur'] . " AS voorraad_debiteur
			FROM 
				kingsystem.vwKMBVoorraad vrr
			LEFT JOIN
				kingsystem.tabVrART000000 vru ON vru.vrART000000RecordId = vrr.ArtGid
			WHERE
				voorraad_sync = 1
		";
		
		if($voorraad = $odbc->results($sql)){ //If ODBC results: proceed
			
			//Delete all voorraad for fresh info from King
			$this->db->truncate('voorraad');
			
			$count_add = 0;
				
			foreach($voorraad as $art_voorraad){ //Loop through voorraad from OBDC result
			
				//INSERT voorraden											
				$art_gid = $art_voorraad->ArtGid;
				$art_code = $art_voorraad->ArtCode;
				$art_oms = $art_voorraad->ArtOms;
				$art_vrije_voorraad = $art_voorraad->VrijeVoorraad;
				$art_in_bestelling = $art_voorraad->ArtInBestelling;
				$art_gereserveerd = $art_voorraad->Gereserveerd;
				
				if(!empty($art_voorraad->voorraad_sync)){
					$art_voorraad_sync = $art_voorraad->voorraad_sync;
				}else{
					$art_voorraad_sync = 0;
				}
				if(!empty($art_voorraad->voorraad_tonen)){
					$art_voorraad_tonen = $art_voorraad->voorraad_tonen;
				}else{
					$art_voorraad_tonen = 0;
				}
				if(!empty($art_voorraad->voorraad_dvb)){
					$art_Dvb = $art_voorraad->voorraad_dvb;
				}else{
					$art_Dvb = 0;
				}
				if(!empty($art_voorraad->voorraad_draaibank)){
					$art_Draaibank = $art_voorraad->voorraad_draaibank;
				}else{
					$art_Draaibank = 0;
				}
				if(!empty($art_voorraad->voorraad_extrusie)){
					$art_Extrusie = $art_voorraad->voorraad_extrusie;
				}else{
					$art_Extrusie = 0;
				}
				if(!empty($art_voorraad->voorraad_freesbank)){
					$art_Freesbank = $art_voorraad->voorraad_freesbank;
				}else{
					$art_Freesbank = 0;
				}
				if(!empty($art_voorraad->voorraad_handvorm)){
					$art_Handvorm = $art_voorraad->voorraad_handvorm;
				}else{
					$art_Handvorm = 0;
				}
				if(!empty($art_voorraad->voorraad_inkoop_verkoop)){
					$art_Inkoop_verkoop = $art_voorraad->voorraad_inkoop_verkoop;
				}else{
					$art_Inkoop_verkoop = 0;
				}
				if(!empty($art_voorraad->voorraad_logistiek)){
					$art_Logistiek = $art_voorraad->voorraad_logistiek;
				}else{
					$art_Logistiek = 0;
				}
				if(!empty($art_voorraad->voorraad_montage)){
					$art_Montage = $art_voorraad->voorraad_montage;
				}else{
					$art_Montage = 0;
				}
				if(!empty($art_voorraad->voorraad_pe)){
					$art_PE = $art_voorraad->voorraad_pe;
				}else{
					$art_PE = 0;
				}
				if(!empty($art_voorraad->voorraad_phytobac)){
					$art_Phytobac = $art_voorraad->voorraad_phytobac;
				}else{
					$art_Phytobac = 0;
				}
				if(!empty($art_voorraad->voorraad_putten)){
					$art_Putten = $art_voorraad->voorraad_putten;
				}else{
					$art_Putten = 0;
				}
				if(!empty($art_voorraad->voorraad_spuitgiet)){
					$art_Spuitgiet = $art_voorraad->voorraad_spuitgiet;
				}else{
					$art_Spuitgiet = 0;
				}	

				if(!empty($art_voorraad->voorraad_debiteur)){
					$art_debiteur = $art_voorraad->voorraad_debiteur;
					$art_debiteur = strtok($art_debiteur, ';');
				}else{
					$art_debiteur = 0;
				}	
				
				//Add voorraad artikelen from King into database
				$sql = "
					INSERT INTO 
					voorraad 
						(
							art_gid,
							art_code,	
							art_oms,
							art_vrije_voorraad,
							art_in_bestelling,
							art_gereserveerd,
							art_voorraad_sync,	
							art_voorraad_tonen,		
							art_Dvb,
							art_Draaibank,
							art_Extrusie,
							art_Freesbank,
							art_Handvorm,
							art_Inkoop_verkoop,
							art_Logistiek,
							art_Montage,
							art_PE,
							art_Phytobac,
							art_Putten,
							art_Spuitgiet,
							art_debiteur
						) 
					VALUES 
						(
							".$this->db->escape($art_gid).",
							".$this->db->escape($art_code).", 
							".$this->db->escape($art_oms).", 
							".$this->db->escape($art_vrije_voorraad).", 
							".$this->db->escape($art_in_bestelling).", 
							".$this->db->escape($art_gereserveerd).", 
							".$this->db->escape($art_voorraad_sync).",
							".$this->db->escape($art_voorraad_tonen).", 
							".$this->db->escape($art_Dvb).", 
							".$this->db->escape($art_Draaibank).",
							".$this->db->escape($art_Extrusie).",
							".$this->db->escape($art_Freesbank).",	
							".$this->db->escape($art_Handvorm).",
							".$this->db->escape($art_Inkoop_verkoop).",
							".$this->db->escape($art_Logistiek).",
							".$this->db->escape($art_Montage).",
							".$this->db->escape($art_PE).",
							".$this->db->escape($art_Phytobac).",
							".$this->db->escape($art_Putten).",
							".$this->db->escape($art_Spuitgiet).",
							".$this->db->escape($art_debiteur)."
						)
				";  
				$this->db->query($sql);
				$count_add++;
						
			}//END OF - Loop through voorraad from OBDC result
						
			//Make voorraadnsync info message for frontuser
			if($count_add == 1){
				$count_add_msg = "Er is <strong>" . $count_add . "</strong> voorraadartikel toegevoegd.<br />";
			} elseif($count_add > 1){
				$count_add_msg = "Er zijn <strong>" . $count_add . "</strong> voorraadartikelen toegevoegd.<br />";
			} else{
				$count_add_msg = "Er zijn <strong>" . $count_add . "</strong> voorraadartikelen toegevoegd.<br />";
			}
		
			$this->session->set_flashdata('msg_success',  $count_add_msg);
			
			if(is_cli()){
				echo "Beutech voorraad nieuw - " . $count_add . "\n";
			}
			
		} //END OF If ODBC results: proceed
		
		else{ //If no ODBC results: set error message
			
			$this->session->set_flashdata('msg_error', "Er zijn geen resultaten gevonden tijdens de ODBC synchronisatie.<br />Er zijn <strong>geen</strong> voorraadartikelen geüpdatet of toegevoegd.");
			
		} //END OF - If no ODBC results: set error message

		
	} //END OF - Sync voorraad Beutech
	
	
} // END OF Voorraad Model class