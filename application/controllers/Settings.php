<?php
// Settings Controller

class Settings extends MY_Controller {
		
	public function __construct(){
		parent::__construct();
		$this->load->model('settings_model');
	}
	
	public function index(){			
		
		//Check user access rights
		$afdeling = "Instellingen";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);
		
		$data['title'] = '<i class="fas fa-cogs"></i> Instellingen';
		
		$this->load->view('templates/header', $data);
		$this->load->view("settings/index.php", $data);
		$this->load->view('templates/footer');
	}
	
	public function check_user_rights($afdeling, $user_level, $user_email){
		
		$access = $this->settings_model->check_user_rights($afdeling, $user_level, $user_email);

		return $access;
		
	}
	
	public function view_Gebruikers(){
		
		//Check user access rights
		$afdeling = "Gebruikers";
		$user_level = $this->session->userdata('level');
		$user_email = $this->session->userdata('email');
		$data['user_access'] = $this->check_user_rights($afdeling, $user_level, $user_email);

		$data['title'] = "Instellingen - <i class='fas fa-users-cog'></i> Gebruikers";

		$data['gebruikers'] = $this->settings_model->get_users();
		
		//List all afdelingen/pages
		$afdelingen = array('Admin','Instellingen','Gebruikers','Recepten','Voorraad','Productie','Orderoverzicht','Doorvoerbochten','PE','Putten','Montage','Draaibank','Smans','Handvorm','Extrusie','Logistiek');
		$data['afdelingen'] = $afdelingen;
		
		//Create user -> If isset submit new user
		if(isset($_POST["gebruiker_toevoegen"])){
								
			//Gebruiker Naam
			if(!empty($_POST['gebruiker_naam'])){
				$gebruiker_naam = $_POST["gebruiker_naam"];
			}else{
				$gebruiker_naam = "";
			}
				
			//Gebruiker Level
			if(!empty($_POST['gebruiker_level'])){
				$gebruiker_level = $_POST["gebruiker_level"];
			}else{
				$gebruiker_level = "";
			}
			
			//Gebruiker E-mail
			if(!empty($_POST['gebruiker_email'])){
				$gebruiker_email = $_POST["gebruiker_email"];
			}else{
				$gebruiker_email = "";
			}
			
			//Gebruiker Wachtwoord
			if(!empty($_POST['gebruiker_password'])){
				$gebruiker_password = $_POST["gebruiker_password"];
			}else{
				$gebruiker_password = "";
			}
			
			//Gebruiker Afdelingen
			$gebruiker_afdelingen = "";
			foreach($afdelingen as $afdeling){
				if(isset($_POST["afdeling_$afdeling"])){
					$gebruiker_afdelingen .= $_POST["afdeling_$afdeling"] . ";";
				}
			}
			
			//Gebruiker schrijfrechten
			if(!empty($_POST['schrijfrechten'])){
				$gebruiker_schrijfrechten = $_POST["schrijfrechten"];
			}else{
				$gebruiker_schrijfrechten = 0;
			}
			
						
			if($this->settings_model->create_user($gebruiker_naam, $gebruiker_level, $gebruiker_email, $gebruiker_password, $gebruiker_afdelingen, $gebruiker_schrijfrechten)){
				
				if($gebruiker_schrijfrechten == 1){
					$schrijfrechten = "Ja";
				}else{
					$schrijfrechten = "Nee";
				}
				
				$message = "<strong>Gebruiker succesvol toegevoegd!</strong><br />"; 
				$message .= " Naam: " . $gebruiker_naam . "<br />";
				$message .= " Afdeling: " . $gebruiker_level . "<br />";
				$message .= " Rechten: " . $gebruiker_afdelingen . "<br />";
				$message .= " Schrijfrechten: " . $schrijfrechten . "<br />";
				$message .= " Email: " . $gebruiker_email . "<br />";
				$message .= " Wachtwoord: " . $gebruiker_password . "<br />";
				
				$this->session->set_flashdata('msg_success', $message);
				redirect($this->uri->uri_string());
			}
			
		} //END OF - Create user -> If isset submit new user
		
		//Update user -> If isset submit user changes
		if(isset($_POST["gebruiker_wijzigen"])){
					
			//Gebruiker ID
			if(!empty($_POST['gebruiker_id'])){
				$gebruiker_id = $_POST["gebruiker_id"];
			}else{
				$gebruiker_id = "";
			}
			
			//Gebruiker Naam
			if(!empty($_POST['gebruiker_naam'])){
				$gebruiker_naam = $_POST["gebruiker_naam"];
			}else{
				$gebruiker_naam = "";
			}
			
			//Gebruiker Level
			if(!empty($_POST['gebruiker_level'])){
				$gebruiker_level = $_POST["gebruiker_level"];
			}else{
				$gebruiker_level = "";
			}
						
			//Gebruiker E-mail
			if(!empty($_POST['gebruiker_email'])){
				$gebruiker_email = $_POST["gebruiker_email"];
			}else{
				$gebruiker_email = "";
			}
			
			//Gebruiker Wachtwoord
			if(!empty($_POST['gebruiker_password'])){
				$gebruiker_password = $_POST["gebruiker_password"];
			}else{
				$gebruiker_password = "";
			}
			
			//Gebruiker Afdelingen
			$gebruiker_afdelingen = "";
			foreach($afdelingen as $afdeling){
				if(isset($_POST["afdeling_$afdeling"])){
					$gebruiker_afdelingen .= $_POST["afdeling_$afdeling"] . ";";
				}
			}
			
			//Gebruiker schrijfrechten
			if(!empty($_POST['schrijfrechten'])){
				$gebruiker_schrijfrechten = $_POST["schrijfrechten"];
			}else{
				$gebruiker_schrijfrechten = 0;
			}
						
			if($this->settings_model->update_user($gebruiker_id, $gebruiker_naam, $gebruiker_level, $gebruiker_email, $gebruiker_password, $gebruiker_afdelingen, $gebruiker_schrijfrechten)){
				
				if($gebruiker_schrijfrechten == 1){
					$schrijfrechten = "Ja";
				}else{
					$schrijfrechten = "Nee";
				}
				
				$message = "<strong>Gebruiker succesvol gewijzigd!</strong><br />"; 
				$message .= " Naam: " . $gebruiker_naam . "<br />";
				$message .= " Afdeling: " . $gebruiker_level . "<br />";
				$message .= " Rechten: " . $gebruiker_afdelingen . "<br />";
				$message .= " Schrijfrechten: " . $schrijfrechten . "<br />";
				$message .= " Email: " . $gebruiker_email . "<br />";
				if(!empty($gebruiker_password)){
					$message .= " Wachtwoord: " . $gebruiker_password . "<br />";
				}else{
					$message .= " Wachtwoord: <i>Niet gewijzigd.</i><br />";
				}
				
				$this->session->set_flashdata('msg_success', $message);
				redirect($this->uri->uri_string());
			}
			
		} //END OF - Update user -> If isset submit user changes

		//Delete user -> If isset submit delete user
		if(isset($_POST["gebruiker_verwijderen"])){
			
			//Gebruiker ID
			if(!empty($_POST['gebruiker_id'])){
				$gebruiker_id = $_POST["gebruiker_id"];
			}else{
				$gebruiker_id = "";
			}
			
			//Gebruiker Naam
			if(!empty($_POST['gebruiker_naam'])){
				$gebruiker_naam = $_POST["gebruiker_naam"];
			}else{
				$gebruiker_naam = "";
			}
			
			if($this->settings_model->delete_user($gebruiker_id)){
				
				$message = "<strong>Gebruiker succesvol verwijderd!</strong><br />"; 
				$message .= " Naam: " . $gebruiker_naam . "<br />";
						
				$this->session->set_flashdata('msg_success', $message);
				redirect($this->uri->uri_string());
			}
			
		} //END OF - Update user -> If isset submit user changes
		
		
		$this->load->view('templates/header', $data);
		$this->load->view('settings/gebruikers', $data);
		$this->load->view('templates/footer');
	}
		
}
?>