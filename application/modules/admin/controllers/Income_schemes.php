<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_schemes extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/income_scheme_types_model', 'income_scheme_types');
		$this->load->model('admin/income_scheme_merchants_model', 'income_scheme_merchants');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

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

	private function get_merchant_scheme($id, $not_scheme_merchant_id = "", $select = array()) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		if (count($select) == 0) {
			$select = array(
				'scheme_merchant_id as id',
				'scheme_merchant_index as "Level No."',
				'merchants.merchant_number as "Merchant Number"',
				'CONCAT(merchant_fname, " ", merchant_mname, " ", merchant_lname) as "Merchant Name"',
				'IF(scheme_merchant_type = 1, "Constant", "Percentage") as Type',
				'scheme_merchant_value as Value'
			);
		}

		$where = array(
			'oauth_bridge_parent_id' 					=> $admin_oauth_bridge_id,
			'income_scheme_merchants.scheme_type_id' 	=> $id
		);

		if ($not_scheme_merchant_id != "") {
			$where = array_merge(
				$where,
				array(
					'scheme_merchant_id !=' => $not_scheme_merchant_id
				)
			);
		}

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

		$results = $this->income_scheme_merchants->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter' => 'scheme_merchant_index',
				'sort' => 'ASC'
			)
		);

		return $results;
	}

	private function get_merchant_positions($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$select = array(
			'scheme_merchant_id as id',
			'CONCAT("After ", merchant_fname, " ", merchant_mname, " ", merchant_lname) as merchant_name'
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

		$results = $this->income_scheme_merchants->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter' => 'scheme_merchant_index',
				'sort' => 'ASC'
			)
		);

		return $results;
	}

	private function get_valid_merchants($data_not_in, $index_name = "merchant_number") {
		$this->load->model("admin/merchants_model", "merchants");

		$account 					= $this->get_account_data();
		$admin_account_data_results = $account['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$where = array(
			'oauth_bridge_parent_id' => $admin_oauth_bridge_id
		);

		$where_not_in = array();

		foreach ($data_not_in as $row) {
			$where_not_in[] = $row[$index_name];
		}

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = merchants.oauth_bridge_id'
			),
			array(
				'table_name' 	=> 'oauth_clients',
				'condition'		=> 'oauth_clients.client_id = oauth_bridges.oauth_bridge_id'
			)
		);

		return $this->merchants->get_data_not_in(
			array(
				'merchant_number as id',
				'CONCAT(merchant_fname, " ", merchant_mname, " ", merchant_lname) as name',
			),
			$where,
			$where_not_in,
			$inner_joints
		);
	}

	private function update_scheme_postion($scheme_type_id, $position, $data_insert) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$select = array(
			'scheme_merchant_id',
			'scheme_merchant_index'
		);

		$where = array(
			'oauth_bridge_parent_id' 					=> $admin_oauth_bridge_id,
			'income_scheme_merchants.scheme_type_id' 	=> $scheme_type_id
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

		$results = $this->income_scheme_merchants->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter' => 'scheme_merchant_index',
				'sort' => 'ASC'
			)
		);

		if ($position == 'top') {
			$this->income_scheme_merchants->insert(
				array_merge(
					$data_insert,
					array(
						'scheme_merchant_index' => 1
					)
				)
			);

			foreach ($results as $row) {
				$scheme_merchant_id 	= $row['scheme_merchant_id'];
				$scheme_merchant_index 	= $row['scheme_merchant_index'];

				$this->income_scheme_merchants->update(
					$scheme_merchant_id,
					array(
						'scheme_merchant_index' => $scheme_merchant_index + 1
					)
				);
			}

			return;
		} else {
			$flag = false;

			foreach ($results as $row) {
				$scheme_merchant_id 	= $row['scheme_merchant_id'];
				$scheme_merchant_index 	= $row['scheme_merchant_index'];

				if ($flag) {
					$this->income_scheme_merchants->update(
						$scheme_merchant_id,
						array(
							'scheme_merchant_index' => $scheme_merchant_index + 1
						)
					);
				}

				if ($position == $scheme_merchant_id && $flag == false) {
					$flag = true;
					$this->income_scheme_merchants->insert(
						array_merge(
							$data_insert,
							array(
								'scheme_merchant_index' => $scheme_merchant_index + 1
							)
						)
					);
				}
			}
		}
	}

	public function update($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];
		
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

		$results_schemes = $this->get_merchant_scheme($id);

		$results_position = $this->get_merchant_positions($id);

		$this->_data['position'] = $this->generate_selection(
			"position",
			array_merge(
				array(
					array(
						'id' 			=> "top",
						'merchant_name' => "Set as Master Merchant"
					)
				),
				$results_position
			),
			"",
			"id", 
			"merchant_name", 
			false
		);		

		$this->_data['merchants'] = $this->generate_selection(
			"merchant",
			$this->get_valid_merchants($results_schemes, "Merchant Number"),
			"",
			"id", 
			"name", 
			false
		);

		$actions = array(
			'delete'
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$merchant_number 	= $this->input->post("merchant");
				$type				= $this->input->post("type");
				$value				= $this->input->post("value");
				$position			= $this->input->post("position");

				// filter if merchant already added to list
				$row = $this->income_scheme_merchants->get_datum(
					'',
					array(
						'merchant_number' 	=> $merchant_number
					),
					array(),
					array(
						array(
							'table_name'	=> 'income_scheme_types',
							'condition'		=> 'income_scheme_types.scheme_type_id = income_scheme_merchants.scheme_type_id'
						)
					)
				)->row();

				if ($row != "") {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Merchant already added in the scheme ('. $row->scheme_type_name .')!'));
					redirect($this->_data['form_url']);
				}

				$data_insert = array(
					'merchant_number' 				=> $merchant_number,
					'scheme_type_id'				=> $id,
					'scheme_merchant_type' 			=> $type == 1 ? 1 : 2,
					'scheme_merchant_value' 		=> $value,
					'scheme_merchant_date_added'	=> $this->_today
				);

				$this->update_scheme_postion($id, $position, $data_insert);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['listing'] = $this->table_listing('', $results_schemes, count($results_schemes), 0, count($results_schemes), $actions, 5);
		$this->_data['title']  = "Update Scheme : {$datum->scheme_type_name}";
		$this->set_template("income_schemes/form", $this->_data);
	}

	public function delete($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

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

		$scheme_type_id = $datum->scheme_type_id;

		if ($_POST) {
			$proceed = $this->input->post("proceed");

			if ($proceed != "PROCEED") {
				$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Please type PROCEED to delete!'));
				redirect($this->_data['form_url']);
			}

			$this->income_scheme_merchants->delete($id);

			$select = array(
				'scheme_merchant_id',
				'scheme_merchant_index'
			);
	
			$where = array(
				'oauth_bridge_parent_id' 					=> $admin_oauth_bridge_id,
				'income_scheme_merchants.scheme_type_id' 	=> $scheme_type_id
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
	
			$results = $this->income_scheme_merchants->get_data(
				$select,
				$where,
				array(),
				$inner_joints,
				array(
					'filter' => 'scheme_merchant_index',
					'sort' => 'ASC'
				)
			);

			$index = 0;

			foreach ($results as $row) {
				$index++;

				$scheme_merchant_id = $row['scheme_merchant_id'];

				$this->income_scheme_merchants->update(
					$scheme_merchant_id,
					array(
						'scheme_merchant_index' => $index
					)
				);
			}

			$this->session->set_flashdata('notification', $this->generate_notification('info', "Merchant({$merchant_name}) is deleted from this scheme!"));
			redirect($this->_data['cancel_url']);
		}
		
		$this->_data['title']  			= "Delete merchant from scheme";
		$this->set_template("income_schemes/form_delete", $this->_data);
	}
	
	/*
	public function edit($id) {
		$this->add_scripts(base_url() . "assets/admin/js/income_scheme.js", true);

		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']	= base_url() . "income-schemes/edit/{$id}";
		$this->_data['notification']= $this->session->flashdata('notification');

		// get all schemes
		$select = array(
			'scheme_type_id as id',
			'scheme_type_name as name'
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

		$results_scheme_types = $this->income_scheme_types->get_data($select, $where, array(), $inner_joints, array('filter'=>'scheme_type_date_created', 'sort'=>'DESC'));

		$row = $this->income_scheme_merchants->get_datum(
			'',
			array(
				'scheme_merchant_id' => $id
			),
			array(),
			array(
				array(
					'table_name'	=> 'merchants',
					'condition'		=> 'merchants.merchant_number = income_scheme_merchants.merchant_number'
				)
			)
		)->row();

		if ($row == "") {
			redirect(base_url() . "income-schemes");
		}

		$merchant_name 			= trim("{$row->merchant_fname} {$row->merchant_mname} {$row->merchant_lname}");
		$scheme_type_id			= $row->scheme_type_id;

		$merchant_number		= $row->merchant_number;
		$scheme_merchant_type 	= $row->scheme_merchant_type;
		$value					= $row->scheme_merchant_value;

		if ($_POST) {
			$scheme_type_id		= $this->input->post("scheme");
			$position			= $this->input->post("position");

			$this->update_scheme_postion($scheme_type_id, $position, array(), $id);
		}

		$scheme_merchants = $this->get_merchant_scheme(
			$scheme_type_id, 
			$id,
			array(
				'scheme_merchant_id as id',
				'CONCAT("Move after: ", merchant_fname, " ", merchant_mname, " ", merchant_lname) as name',
			)
		);

		$scheme_merchants = array_merge(
			array(
				array(
					'id' 	=> "top",
					'name' 	=> "Set as Master Merchant"
				)
			),
			$scheme_merchants
		);

		$this->_data['scheme'] = $this->generate_selection(
			"scheme",
			$results_scheme_types,
			$scheme_type_id,
			"id", 
			"name", 
			true
		);

		$this->_data['position'] = $this->generate_selection(
			"position",
			$scheme_merchants,
			"",
			"id", 
			"name", 
			false
		);

		$this->_data['scheme_merchant_id'] 	= $id;
		$this->_data['title']  				= "Edit Merchant Scheme: {$merchant_name}";
		$this->set_template("income_schemes/form_edit", $this->_data);
	}

	public function get_merchants_in_scheme($scheme_type_id, $scheme_merchant_id) {
		$scheme_merchants = $this->get_merchant_scheme(
			$scheme_type_id, 
			$scheme_merchant_id,
			array(
				'scheme_merchant_id as id',
				'CONCAT("After: ", merchant_fname, " ", merchant_mname, " ", merchant_lname) as name',
			)
		);

		if (count($scheme_merchants) == 0) {
			echo '<select id="position" name="position" class="form-control"><option value="top">Please select</option></select>';
			return;
		}

		$scheme_merchants = array_merge(
			array(
				array(
					'id' 	=> "top",
					'name' 	=> "Set as Master Merchant"
				)
			),
			$scheme_merchants
		);

		echo $this->generate_selection(
			"position",
			$scheme_merchants,
			"",
			"id", 
			"name", 
			false
		);
	}
	*/
}
