<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->_getAccountVars();
	}

	/*
	*	Pobranie wszystkich danych admina
	*/	

	protected function _getAccountVars(){
		$this->load->model('Auth_model');
		$whereArray = array( 
				'admin_id' => $this->session->userdata('AID'), 
				);

		$selectArray = array( 'admin_email', 'admin_name', 'admin_surname', 'admin_avatar', 'admin_roles', 'admin_start_module', 'admin_default_lang' );

		$result = $this->Auth_model->selectAdminWhere($selectArray, $whereArray);
		if($result === FALSE){
			return FALSE;
		}
		else{
			$array = array();
			foreach ($result as $key => $value) {
				$array[$key] = $value;
			}

			return $this->load->vars($array[0]);
		}
	}

	/*
	*	Metoda sprawdzająca czy zalogowany user ma przypisane admin do admin_roles
	*/

	protected function _checkUserIsAdmin(){
		if(is_numeric($this->session->userdata('AID')) AND (int) $this->session->userdata('AID') > 0){

			if($this->load->get_var('admin_roles') !== 'admin'){
				redirect(base_url('auth'), 'refresh');
				die();
			}
			else{
				$this->lang->load('admin', $this->load->get_var('admin_default_lang'));
				$this->session->set_flashdata('success', $this->lang->line('redirect_start_module_success'));
				redirect(base_url($this->load->get_var('admin_start_module')), 'refresh');
				die();
			}
		}
		else{
			redirect(base_url('auth'), 'refresh');
			die();
		}
	}

	
}
