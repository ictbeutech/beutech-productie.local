<?php
class Recepten_model extends CI_Model { // Recepten Model class

	public function __construct(){
		$this->load->database();
	}		
	
	function getRecept($eindproduct = FALSE){ // Fetch recepten
	
		if (($eindproduct !== FALSE) && (!empty($eindproduct))){ // Fetch singel recept
		
			$query = $this->db->get_where('recepten', array('eindproduct_artnr' => $eindproduct));
			return $query->result_array();
			
		} // END OF - Fetch singel recept
		
	} // END OF - Fetch recepten	
	
	function syncRecepten_beutech(){ //Sync recepten Beutech
		
		$odbc = new ODBC_class_beutech(); //Connect to King ODBC

		//Get all Recepten/Eindproducten/Componenten from King
		$sql="
			SELECT 
				rec.RcptGid AS recept_gid,
				rec.RcptCode AS recept_code,
				rec.RcptOmschrijving AS recept_omschr,
				rep.RcpeArtGid AS eindproduct_gid,
				art_e.ArtCode AS eindproduct_artnr,
				rep.RcpeAantal AS eindproduct_aantal,
				rco.RcpcArtGid AS component_gid,
				rco.RcpcRegelNr AS component_regelnr,
				rco.RcpcRegelsoort AS component_soort,
				art_c.ArtCode AS component_artnr,
				art_c.ArtOms AS component_artomschr,
				rco.RcpcAantal AS component_aantal,
				rco.RcpcVerbruikAantal AS component_verbruik
			FROM 
				kingsystem.tabReceptuur rec
			LEFT JOIN 
				kingsystem.tabReceptuurEindproduct rep ON rep.RcpeRcptGid = rec.RcptGid
			LEFT JOIN 
				kingsystem.tabArtikel art_e on art_e.ArtGid = rep.RcpeArtGid
			LEFT JOIN 
				kingsystem.tabReceptuurComponent rco ON rco.RcpcRcptGid = rec.RcptGid
			LEFT JOIN 
				kingsystem.tabArtikel art_c on art_c.ArtGid = rco.RcpcArtGid
			ORDER BY
				recept_code ASC, 
				eindproduct_artnr ASC,
				component_regelnr ASC
		";
		
		if($recepten = $odbc->results($sql)){ //If ODBC results: proceed
			
			//Delete all recepten for fresh info from King
			$this->db->truncate('recepten');
			
			$count_add = 0;
				
			foreach($recepten as $recept){ //Loop through recepten from OBDC result
			
				$administratie = "Beutech";	
			
				//INSERT Recepten											
				$recept_gid = $recept->recept_gid;
				$recept_code = $recept->recept_code;
				$recept_omschr = $recept->recept_omschr;
				$eindproduct_gid = $recept->eindproduct_gid;
				$eindproduct_artnr = $recept->eindproduct_artnr;
				$eindproduct_aantal = $recept->eindproduct_aantal;
				$component_gid = $recept->component_gid;
				$component_regelnr = $recept->component_regelnr;
				$component_soort = $recept->component_soort;
				$component_artnr = $recept->component_artnr;
				$component_artomschr = $recept->component_artomschr;
				$component_aantal = $recept->component_aantal;
				$component_verbruik = $recept->component_verbruik;
								
				//Set receptregel soort
				if($component_soort == '0'){ //Soort 0 = tekst
					$regel_soort = "Tekst";	
				}
				elseif($component_soort == '2'){ //Soort 2 = Artikel	
					$regel_soort = "Artikel";		
				}
				else{ //all other = tarief	
					$regel_soort = "Tarief";
				} //END OF - Set receptregel soort

				
				//Add recepten from King into database
				$sql = "
					INSERT INTO 
					recepten 
						(
							recept_gid,
							recept_code,
							recept_omschr,
							eindproduct_gid, 
							eindproduct_artnr, 
							eindproduct_aantal,
							component_gid,								
							component_regelnr,
							component_soort,
							component_artnr,
							component_artomschr,
							component_aantal,
							component_verbruik,
							administratie
						) 
					VALUES 
						(
							".$this->db->escape($recept_gid).",
							".$this->db->escape($recept_code).", 
							".$this->db->escape($recept_omschr).", 
							".$this->db->escape($eindproduct_gid).", 
							".$this->db->escape($eindproduct_artnr).", 
							".$this->db->escape($eindproduct_aantal).", 
							".$this->db->escape($component_gid).",
							".$this->db->escape($component_regelnr).", 
							".$this->db->escape($regel_soort).", 
							".$this->db->escape($component_artnr).",
							".$this->db->escape($component_artomschr).",
							".$this->db->escape($component_aantal).",	
							".$this->db->escape($component_verbruik).",
							".$this->db->escape($administratie)."
						)
				";  
				$this->db->query($sql);
				$count_add++;
						
			}//END OF - Loop through recepten from OBDC result
						
			//Make receptensync info message for frontuser
			if($count_add == 1){
				$count_add_msg = "Er is <strong>" . $count_add . "</strong> recept toegevoegd.<br />";
			} elseif($count_add > 1){
				$count_add_msg = "Er zijn <strong>" . $count_add . "</strong> recepten toegevoegd.<br />";
			} else{
				$count_add_msg = "Er zijn <strong>" . $count_add . "</strong> recepten toegevoegd.<br />";
			}
		
			$this->session->set_flashdata('msg_success',  $count_add_msg);
			
			if(is_cli()){
				echo "Beutech recepten nieuw - " . $count_add . "\n";
			}
			
		} //END OF If ODBC results: proceed
		
		else{ //If no ODBC results: set error message
			
			$this->session->set_flashdata('msg_error', "Er zijn geen resultaten gevonden tijdens de ODBC synchronisatie.<br />Er zijn <strong>geen</strong> recepten geüpdatet of toegevoegd.");
			
		} //END OF - If no ODBC results: set error message

		
	} //END OF - Sync recepten Beutech
	
	function create_xml_voorraadmutatie(){ //Create XML voorraadmutatie for import King
	
		$query = $this->db->get_where('voorraad_mutaties', array('geexporteerd' => 0));
		return $query->result();
			
	} //END OF - Create XML voorraadmutatie for import King
	
	function update_voorraadmutatie($id){ 
	
		$this->db->set('geexporteerd', 1);
		$this->db->where('id', $id);
		$this->db->update('voorraad_mutaties');
			
	} //END OF - 
	
	

} // END OF Recepten Model class