<?php
class Productie_model extends CI_Model { // Productie Model class


	public function __construct(){
		$this->load->database();
	}		
	
	
	function get_productie_mutaties($afdeling = FALSE){ // Fetch productie mutaties
		
		if (($afdeling !== FALSE) && (!empty($afdeling)) && ($afdeling != 'alles')){ // Fetch productie mutaties for afdeling
							
			$query = $this->db->get_where('productie_mutaties', array('afdeling' => $afdeling));
			return $query->result();
		} // END OF - // Fetch productie mutaties for afdeling
		
		else { // Fetch all productie mutaties
			$query = $this->db->get('productie_mutaties');
			return $query->result();
		} // END OF - Fetch all productie mutaties
		
	} // END OF - // Fetch productie mutaties
	
	
	function get_new_productie_mutaties($afdeling = FALSE, $current_date = FALSE, $sub_afdeling = FALSE){ // Fetch new productie mutaties
	
		if (($afdeling !== FALSE) && (!empty($afdeling)) && ($afdeling != 'alles')){ // Fetch  new productie mutaties for afdeling
			
			if (($sub_afdeling !== FALSE) && (!empty($sub_afdeling))){
				$this->db->select('productie_mutaties.*, orders.product');
				$this->db->join('orders', 'orders.orderregel_id = productie_mutaties.orderregel_id', 'left');
				$this->db->select_sum('productie_mutaties.aantal_geproduceerd');	
				$this->db->group_by(array('productie_mutaties.orderregel_id'));
				$this->db->order_by('productie_mutaties.order_nr');
				
				$query = $this->db->get_where('productie_mutaties', array('productie_mutaties.afdeling' => $afdeling, 'productie_mutaties.sub_afdeling' => $sub_afdeling, 'productie_mutaties.added >=' => $current_date));
				
				return $query->result();
			}else{			
				$this->db->select('*');
				$this->db->select_sum('aantal_geproduceerd');	
				$this->db->group_by(array('order_nr'));
				$query = $this->db->get_where('productie_mutaties', array('afdeling' => $afdeling, 'added >=' => $current_date));
				return $query->result();
			}
		} // END OF - // Fetch new productie mutaties for afdeling
		
		else { // Fetch all new productie mutaties
			$query = $this->db->get('productie_mutaties');
			return $query->result();
		} // END OF - Fetch all new productie mutaties
		
	} // END OF - // Fetch new productie mutaties
		
	
	function syncProductieMutaties($administratie){ //Sync productie mutaties Beutech
		
		$query = $this->db->get_where('productie_mutaties', array('administratie' => $administratie, 'gesynct' => 0));
		return $query->result();
		
	} //END OF - Sync productie mutaties Beutech
	
	
	function update_status($id, $status_message, $gesynct){ //Update productie mutaties status
		
		$data = array(
			'status_message' => $status_message,
			'gesynct' => $gesynct
		);
		$this->db->where('id', $id);
		$this->db->update('productie_mutaties', $data);
		
	} //END OF - Update productie mutaties status
	
	function update_status_lock($id, $status_message_lock){ //Update productie mutaties lock status
		
		$data = array(
			'lock_status_message' => $status_message_lock
		);
		$this->db->where('id', $id);
		$this->db->update('productie_mutaties', $data);
		
	} //END OF - Update productie mutaties lock status
	
	
	function get_aantal_deze_levering_king($administratie, $order_regel_id){
		
		if($administratie == "Beutech"){
			
			$odbc = new ODBC_class_beutech(); //Connect to King ODBC
			
			$sql="
			SELECT		
				orr.OrrAantalLeveringVrrdEenh	
			FROM 
				KingSystem.tabOrderRegel orr
			WHERE
				orr.OrrGid = '{$order_regel_id}'
			";
			
			$result = $odbc->result($sql);
			return $result;
		}
		
	}
	
	
} // END OF Productie Model class


