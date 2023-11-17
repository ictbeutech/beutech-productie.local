<?php
// Pages Controller

class Pages extends MY_Controller {
		
	public function view($page = 'dashboard')
	{
		if ( ! file_exists(APPPATH.'views/pages/'.$page.'.php'))
		{
			// This page was not found!
			show_404();
		}
		
		$data['title'] = ucfirst($page); // Capitalize the first letter

		$this->load->view('templates/header', $data);
		$this->load->view('pages/'.$page, $data);
		$this->load->view('templates/footer', $data);
	}	
	
}
?>