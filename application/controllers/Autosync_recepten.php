<?php
class Autosync extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('orders_model');
	}

    public function auto_sync_orders()
    {
        $this->orders_model->syncOrders_tibuplast();
		$this->orders_model->syncOrders_beutech();		
    }
}