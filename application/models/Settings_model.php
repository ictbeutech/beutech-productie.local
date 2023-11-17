<?php
class Settings_model extends CI_Model { // Orders Model class

	public function __construct(){
		$this->load->database();
	}		
	
	function check_user_rights($afdeling, $user_level, $user_email){ // Check user access rights
	
		$this->db->where('gebruiker_level', $user_level);
		$this->db->where('gebruiker_email', $user_email);
		$this->db->group_start();
			$this->db->like('gebruiker_afdelingen', $afdeling, 'both');
			$this->db->or_like('gebruiker_afdelingen', $user_level, 'both');
		$this->db->group_end();
		
		$query = $this->db->get('gebruikers');  
							
		if ($query->num_rows()) {
			return 1;
		} else {
			return 0;
		}
			
	} // END OF - Check user access rights
	
	function get_users(){ // Get all users
		
		$this->db->order_by('gebruiker_level', 'ASC');
		$query = $this->db->get('gebruikers');  
				
		return $query->result();
		
	} // END OF - Get all users
	
	
	function create_user($gebruiker_naam, $gebruiker_level, $gebruiker_email, $gebruiker_password, $gebruiker_afdelingen, $gebruiker_schrijfrechten){ // Create user
		
		$data = array(
			'gebruiker_naam'  => $gebruiker_naam,
			'gebruiker_level'  => $gebruiker_level,
			'gebruiker_email'  => $gebruiker_email,
			'gebruiker_password'  => md5($gebruiker_password),
			'gebruiker_afdelingen'  => $gebruiker_afdelingen,
			'gebruiker_schrijfrechten'  => $gebruiker_schrijfrechten
		);
		
		if($this->db->insert('gebruikers', $data)){
			return TRUE;
		};
		
	} // END OF - Create user
	
	
	function update_user($gebruiker_id, $gebruiker_naam, $gebruiker_level, $gebruiker_email, $gebruiker_password, $gebruiker_afdelingen, $gebruiker_schrijfrechten){ // Update user
		
		if(!empty($gebruiker_password)){
			$data = array(
				'gebruiker_naam'  => $gebruiker_naam,
				'gebruiker_level'  => $gebruiker_level,
				'gebruiker_email'  => $gebruiker_email,
				'gebruiker_password'  => md5($gebruiker_password),
				'gebruiker_afdelingen'  => $gebruiker_afdelingen,
				'gebruiker_schrijfrechten'  => $gebruiker_schrijfrechten
			);
		}else{
			$data = array(
				'gebruiker_naam'  => $gebruiker_naam,
				'gebruiker_level'  => $gebruiker_level,
				'gebruiker_email'  => $gebruiker_email,
				'gebruiker_afdelingen'  => $gebruiker_afdelingen,
				'gebruiker_schrijfrechten'  => $gebruiker_schrijfrechten
			);
		}

		$this->db->where('gebruiker_id', $gebruiker_id);
		if($this->db->update('gebruikers', $data)){
			return TRUE;
		};
		
	} // END OF - // Update user
	
	
	function delete_user($gebruiker_id){ // Delete user
		
		$this->db->where('gebruiker_id', $gebruiker_id);
		if($this->db->delete('gebruikers')){
			return TRUE;
		};
		
	} // END OF - Delete user
	
	
} // END OF Orders Model class