<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_schemes extends Admin_Controller {

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/income_scheme_types_model', 'income_scheme_types');
		$this->load->model('admin/income_scheme_merchants_model', 'income_scheme_merchants');

		$this->_admin_account_data = $this->get_account_data();
	}

	private function get_merchants() {
		$this->load->model('admin/merchants_model', 'merchants');

		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['oauth_bridge_id'];

		$where = array(
			'oauth_bridge_parent_id' => $admin_oauth_bridge_id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = merchants.oauth_bridge_id'
			)
		);

		return $this->merchants->get_data(
			array(
				'merchant_number as id',
				'CONCAT(merchant_fname, " ", merchant_mname, " ", merchant_lname) as name',
			),
			$where,
			array(), 
			$inner_joints
		);
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['oauth_bridge_id'];

		$actions = array(
			'update'
		);

		$select = array(
			'scheme_type_id as id',
			'scheme_type_status as Status',
			'scheme_type_code as Code',
			'scheme_type_name as Name',
			'scheme_type_date_created as "Date Created"',
		);

		$where = array(
			'oauth_bridge_parent_id' => $admin_oauth_bridge_id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = income_scheme_types.oauth_bridge_id'
			)
		);

		$total_rows = $this->income_scheme_types->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->income_scheme_types->get_data($select, $where, array(), $inner_joints, array('filter'=>'scheme_type_date_created', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 4);
		$this->_data['title']  = "Income Schemes";
		$this->set_template("income_schemes/list", $this->_data);
	}

	public function update($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['oauth_bridge_id'];
		
		$this->_data['form_url']	= base_url() . "income-schemes/update/{$id}";
		$this->_data['notification']= $this->session->flashdata('notification');
		
		$this->_data['type'] = $this->generate_selection(
			"type",
			array(
				array(
					'id' 	=> 1,
					'name'	=> "Constant"
				),
				array(
					'id' 	=> 2,
					'name'	=> "Percentage"
				)
			),
			1,
			"id", 
			"name", 
			true
		);

		$select = array(
			'scheme_merchant_id as id',
			'merchants.merchant_number as "Merchant Number"',
			'CONCAT(merchant_fname, " ", merchant_mname, " ", merchant_lname) as "Merchant Name"',
			'IF(scheme_merchant_type = 1, "Constant", "Percentage") as Type',
			'scheme_merchant_value as Value'
		);

		$actions = array(
			'delete'
		);

		$where = array(
			'oauth_bridge_parent_id' 					=> $admin_oauth_bridge_id,
			'income_scheme_merchants.scheme_type_id' 	=> $id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'income_scheme_types',
				'condition'		=> 'income_scheme_types.scheme_type_id = income_scheme_merchants.scheme_type_id'
			),
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = income_scheme_types.oauth_bridge_id'
			),
			array(
				'table_name' 	=> 'merchants',
				'condition'		=> 'merchants.merchant_number = income_scheme_merchants.merchant_number',
				'type'			=> 'left'
			)
		);

		// check if income type is exist
		
		$datum = $this->income_scheme_types->get_datum(
			'',
			array(
				'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
				'scheme_type_id' 			=> $id
			),
			array(),
			array(
				array(
					'table_name' 	=> 'oauth_bridges',
					'condition'		=> 'oauth_bridges.oauth_bridge_id = income_scheme_types.oauth_bridge_id'
				)
			)
		)->row();

		if ($datum == "") {
			redirect(base_url() . "income-schemes");
		}

		$results = $this->income_scheme_merchants->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter' => 'merchant_fname',
				'sort' => 'ASC'
			)
		);

		$this->_data['merchants'] = $this->generate_selection(
			"merchant",
			$this->get_merchants(),
			"",
			"id", 
			"name", 
			false
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$merchant_number 	= $this->input->post("merchant");
				$type				= $this->input->post("type");
				$value				= $this->input->post("value");

				// filter if merchant already added to list
				$row = $this->income_scheme_merchants->get_datum(
					'',
					array(
						'merchant_number' 	=> $merchant_number,
						'scheme_type_id'	=> $id
					)
				)->row();

				if ($row != "") {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Merchant already in the list!'));
					redirect($this->_data['form_url']);
				}

				$this->income_scheme_merchants->insert(
					array(
						'merchant_number' 				=> $merchant_number,
						'scheme_type_id'				=> $id,
						'scheme_merchant_type' 			=> $type == 1 ? 1 : 2,
						'scheme_merchant_value' 		=> $value,
						'scheme_merchant_date_added'	=> $this->_today
					)
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['listing'] = $this->table_listing('', $results, count($results), 0, count($results), $actions, 5);
		$this->_data['title']  = "Update Scheme : {$datum->scheme_type_name}";
		$this->set_template("income_schemes/form", $this->_data);
	}

	public function delete($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['oauth_bridge_id'];

		$this->_data['form_url']	= base_url() . "income-schemes/delete/{$id}";
		$this->_data['notification']= $this->session->flashdata('notification');

		// check if income type is exist
		$datum = $this->income_scheme_merchants->get_datum(
			'',
			array(
				'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
				'scheme_merchant_id' 		=> $id
			),
			array(),
			array(
				array(
					'table_name'	=> 'income_scheme_types',
					'condition'		=> 'income_scheme_types.scheme_type_id = income_scheme_merchants.scheme_type_id'
				),
				array(
					'table_name' 	=> 'oauth_bridges',
					'condition'		=> 'oauth_bridges.oauth_bridge_id = income_scheme_types.oauth_bridge_id'
				),
				array(
					'table_name' 	=> 'merchants',
					'condition'		=> 'merchants.merchant_number = income_scheme_merchants.merchant_number'
				)
			)
		)->row();

		if ($datum == "") {
			redirect(base_url() . "income-schemes");
		}

		$this->_data['cancel_url']		= base_url() . "income-schemes/update/" . $datum->scheme_type_id;
		$this->_data['scheme_name']		= "{$datum->scheme_type_name}";

		$merchant_name					= "{$datum->merchant_fname} {$datum->merchant_mname} {$datum->merchant_lname}";
		$this->_data['merchant_name']	= $merchant_name;

		if ($_POST) {
			$proceed = $this->input->post("proceed");

			if ($proceed != "PROCEED") {
				$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Please type PROCEED to delete!'));
				redirect($this->_data['form_url']);
			}

			$this->income_scheme_merchants->delete($id);

			$this->session->set_flashdata('notification', $this->generate_notification('info', "Merchant({$merchant_name}) is deleted from this scheme!"));
			redirect($this->_data['cancel_url']);
		}
		
		$this->_data['title']  			= "Delete merchant from scheme";
		$this->set_template("income_schemes/form_delete", $this->_data);
	}
}
