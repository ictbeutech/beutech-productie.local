<?php
// Login Model

class Login_model extends CI_Model{

	function validate($email,$password){
		$this->db->where('gebruiker_email',$email);
		$this->db->where('gebruiker_password',$password);
		$result = $this->db->get('gebruikers',1);
		return $result;
	}

}