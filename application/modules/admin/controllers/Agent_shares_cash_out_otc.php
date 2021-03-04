<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_shares_cash_out_otc extends Admin_Controller {
	private
		$_admin_account_data = NULL,
		$_as_id = "cash_out_otc";
		
	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/agent_shares_model', 'agent_shares');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index() {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "agent-shares-cash-out-otc";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$row = $this->agent_shares->get_datum(
			'',
			array(
				'as_id'	=> $this->_as_id
			)
		)->row();

		if ($row == "") {
			redirect(base_url());
		}

		$this->_data['post'] = array(
			'amount' => $row->as_amount	
		);

		if ($_POST) {
			$amount = $this->input->post("amount");

			$this->agent_shares->update(
				$this->_as_id,
				array(
					'as_amount'	=> $amount
				)
			);

			$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Saved!'));
			redirect($this->_data['form_url']);
		}

		$this->_data['title']  = "Cash Out (OTC) - Agent Shares";
		$this->set_template("agent_shares/form", $this->_data);
	}
}
