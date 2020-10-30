<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_fees extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();
		
		$this->load->model('admin/transaction_fees_model', 'fees');
		$this->load->model('admin/transaction_types_model', 'types');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$actions = array(
			'update'
		);

		$select = array(
			'transaction_type_id as id',
			'transaction_type_code as Code',
			'transaction_type_name as Name'
		);

		$where = array(
			'transaction_type_user !=' => 1
		);
		
		$inner_joints = array();

		$total_rows = $this->types->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->types->get_data($select, $where, array(), $inner_joints, array('filter'=>'transaction_type_user', 'sort'=>'ASC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
		$this->_data['title']  = "Transaction Types";
		$this->set_template("fees/list", $this->_data);
	}

	public function update($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "transaction-fees/update/{$id}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$row = $this->types->get_datum(
			'',
			array(
				'transaction_type_id'	=> $id
			)
		)->row();

		if ($row == "") {
			redirect(base_url() . "transaction-fees");
		}

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$from			= $this->input->post("from");
				$to			= $this->input->post("to");
				$fee_amount	= $this->input->post("fee-amount");

				if (is_decimal($from) || is_decimal($to) || is_decimal($fee_amount)) {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Separator and/or centavo values not allowed. Please remove comma or dot/ decimal/s to continue.'));
					redirect($this->_data['form_url']);
				}

				if ($from > $to) {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'To Value must be greater than to From Value!'));
					redirect($this->_data['form_url']);
				}

				$this->fees->insert(
					array(
						'transaction_fee_from'		=> $from,
						'transaction_fee_to'		=> $to,
						'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
						'transaction_type_id'		=> $id,
						'transaction_fee_amount'	=> $fee_amount
					)
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added Fee.'));
				redirect($this->_data['form_url']);
			}
		}

		$actions = array(
			'delete'
		);

		$results = $this->fees->get_data(
			array(
				'transaction_fee_id as id',
				'transaction_type_name as "Transaction Type"',
				'transaction_fee_from as "From Value"',
				'transaction_fee_to as "To Value"',
				'transaction_fee_amount as "Fee Amount"'
			),
			array(
				'transaction_fees.transaction_type_id'	=> $id,
				'oauth_bridge_parent_id'				=> $admin_oauth_bridge_id
			),
			array(),
			array(
				array(
					'table_name'	=> 'transaction_types',
					'condition'		=> 'transaction_types.transaction_type_id = transaction_fees.transaction_type_id'
				)
			)
		);	

		$this->_data['listing'] = $this->table_listing('', $results, count($results), 0, count($results), $actions, 4);
		$this->_data['title']  = "Add Fees to {$row->transaction_type_name}";
		$this->set_template("fees/form", $this->_data);
	}


	public function delete($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']	= base_url() . "transaction-fees/delete/{$id}";
		$this->_data['notification']= $this->session->flashdata('notification');

		// check if income type is exist
		$datum = $this->fees->get_datum(
			'',
			array(
				'transaction_fee_id'		=> $id,
				'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id
			),
			array(),
			array(
				array(
					'table_name'	=> 'transaction_types',
					'condition'		=> 'transaction_types.transaction_type_id = transaction_fees.transaction_type_id'
				)
			)
		)->row();

		if ($datum == "") {
			redirect(base_url() . "transact-fees");
		}

		$this->_data['cancel_url']		= base_url() . "transaction-fees/update/" . $datum->transaction_type_id;
		$this->_data['transaction_fee']	= "{$datum->transaction_type_name}, range: {$datum->transaction_fee_from}-{$datum->transaction_fee_to}, fee_amount: {$datum->transaction_fee_amount}";

		if ($_POST) {
			$proceed = $this->input->post("proceed");

			if ($proceed != "PROCEED") {
				$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Please type PROCEED to delete!'));
				redirect($this->_data['form_url']);
			}

			$this->fees->delete($id);

			$this->session->set_flashdata('notification', $this->generate_notification('info', "Successfully delete transaction fee!"));
			redirect($this->_data['cancel_url']);
		}
		
		$this->_data['title']	= "Delete TX Fee";
		$this->set_template("fees/form_delete", $this->_data);
	}
}
