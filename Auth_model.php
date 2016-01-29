<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth_Model extends MY_Model {

	private $_adminsTable	= 'admin_accounts';

	public function validateLogin(){

		$this->form_validation->set_rules('email', $this->lang->line('emailLogin'), 'required|trim|valid_email|htmlspecialchars');
		$this->form_validation->set_rules('password', $this->lang->line('password'), 'required|trim|htmlspecialchars');
		if($this->form_validation->run() == TRUE){

			$whereArray = array( 'admin_email' => $this->input->post('email', TRUE) );
			$selectArray = array( 'admin_password', 'admin_id', 'admin_roles' );

			$query = $this->db->select($selectArray)
				->where($whereArray)
				->limit(1)
				->get($this->_adminsTable);

			if($query->num_rows() > 0){
				foreach ($query->result_array() as $key) {
					if (password_verify($this->input->post('password', TRUE), $key['admin_password'])) {

						$sessionArray = array(
							'AID' => $key['admin_id'],
							);

						$this->session->set_userdata($sessionArray);
						redirect(base_url($key['admin_roles']), 'refresh');

					}	
					else{
						$this->session->set_flashdata('error', $this->lang->line('loginNotCorrect'));
						redirect(base_url('auth/authLogin'), 'refresh');
					}
				}
			}
			else{
				$this->session->set_flashdata('error', $this->lang->line('loginNotCorrect'));
				redirect(base_url('auth/authLogin'), 'refresh');
			}
		}
		else{
			$this->session->set_flashdata('error', validation_errors());
			redirect(base_url('auth/authLogin'), 'refresh');
		}

	}

	public function selectAdminWhere(Array $selectArray, Array $whereArray){
		$query = $this->db->select($selectArray)
			->where($whereArray)
			->get($this->_adminsTable);

		if($query->num_rows() > 0){
			return $query->result_array();
		}
		else{
			return FALSE;
		}
	}

	public function countUserBoolWhere(Array $whereArray){
		return (bool) $this->db->select('admin_id')
			->where($whereArray)
			->get($this->_adminsTable)
			->num_rows();
	}

}
