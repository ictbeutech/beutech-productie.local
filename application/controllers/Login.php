<?php
// Login Controller

class Login extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model('login_model');
	}

	function index(){
		
		$data['title'] = '<i class="fas fa-sign-in-alt"></i> Inloggen';
		
		if ($this->session->userdata('logged_in') == TRUE) {
	
			redirect('dashboard');
			
		}
		
		$this->load->view('templates/header', $data);
		$this->load->view('pages/login');
		$this->load->view('templates/footer', $data);
		
	}

	function auth(){
		
		$email    = $this->input->post('email',TRUE);
		$password = md5($this->input->post('password',TRUE));
		$validate = $this->login_model->validate($email,$password);
		
		if($validate->num_rows() > 0){
			$data  = $validate->row_array();
			$name  = $data['gebruiker_naam'];
			$email = $data['gebruiker_email'];
			$level = $data['gebruiker_level'];
			$afdelingen = explode(";", $data['gebruiker_afdelingen']);
			$schrijven = $data['gebruiker_schrijfrechten'];
			$sesdata = array(
				'gebuikersnaam' => $name,
				'email'     	=> $email,
				'level'     	=> $level,
				'afdelingen'   	=> $afdelingen,
				'schrijven'		=> $schrijven,
				'logged_in' 	=> TRUE
			);
			$this->session->set_userdata($sesdata);
			// access login for admin
			if($level === 'Admin'){
				$this->session->set_flashdata('msg_success', "U bent succesvol ingelogd als <strong>{$name} - {$level}</strong>");
				redirect('dashboard');

			// access login for other users
			}else{
				$this->session->set_flashdata('msg_success', "U bent succesvol ingelogd als <strong>{$name} - {$level}</strong>");
				redirect('dashboard');
			}
		}
		else{

			$this->session->set_flashdata('msg_error', "Gebuikersnaam of wachtwoord niet correct.");
			redirect('login/index');

		}
		
	}

	function logout(){
		
		$this->session->sess_destroy();
		redirect('login');
		
	}

}