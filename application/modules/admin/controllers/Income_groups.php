<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_groups extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/income_groups_model', 'income_groups');
		$this->load->model('admin/income_groups_members_model', 'income_groups_members');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];
		
		$this->_data['add_label']= "New Group";
		$this->_data['add_url']	 = base_url() . "income-groups/new";

		$actions = array(
			'update'
		);

		$select = array(
			'ig_id as id',
			'ig_id as "Group ID"',
			'ig_date_added as "Date Added"'
		);

		$where = array();

		$inner_joints = array();

		$total_rows = $this->income_groups->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->income_groups->get_data($select, $where, array(), $inner_joints, array('filter'=>'ig_date_added', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);

		$this->_data['title']  = "Income Groups";
		$this->set_template("income_groups/list", $this->_data);
	}

	public function new() {
		$this->add_scripts("https://code.jquery.com/jquery-1.12.4.min.js", true);
		$this->add_styles(base_url() . "assets/{$this->_base_controller}/css/data-tree.css", true);
		$this->add_scripts(base_url() . "assets/{$this->_base_controller}/js/data-tree.js", true);
		$this->add_scripts(base_url() . "assets/{$this->_base_controller}/js/scripts_treeview.js", true);

		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$group_id = "";

		if (isset($_GET['group_id'])) {
			$group_id = $_GET['group_id'];
		}

		$base_form_url				= base_url() . "income-groups/new";
		$this->_data['form_url']	= $base_form_url . (($group_id != "") ? "?group_id={$group_id}" : "");

		if ($_POST) {
			$merchant_number = $this->input->post('members');

			if ($merchant_number == "") {
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Please select parent!'));
				redirect($this->_data['form_url']);
			}

			$row_group_member = $this->income_groups_members->get_datum(
				'',
				array(
					'merchant_number'	=> $merchant_number
				),
				array(),
				array(
					array(
						'table_name' 	=> 'merchants',
						'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
					)
				)
			)->row();

			if ($row_group_member == "" && $merchant_number != 0) {
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Invalid selected parent!'));
				redirect($this->_data['form_url']);
			}

			$igm_parent_id = $merchant_number == '0' ? "" : $row_group_member->igm_id;

			$email_address = $this->input->post('email-address');

			$row = $this->merchants->get_datum(
				'',
				array(
					'merchant_email_address'	=> $email_address
				)
			)->row();

			if ($row == "") {
				// invalid
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Invalid merchant email!'));
				redirect($this->_data['form_url']);
			}

			$row_member = $this->income_groups_members->get_datum(
				'',
				array(
					'oauth_bridge_id'	=> $row->oauth_bridge_id
				)
			)->row();

			if ($row_member != "") {
				// invalid
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Merchant already exist/added in the group or on the other groups!'));
				redirect($this->_data['form_url']);
			}

			if ($group_id == "") {
				$group_id = $this->generate_code(
					array(
						'ig_date_added'	=> $this->_today
					),
					"crc32"
				);

				// create group
				$this->income_groups->insert(
					array(
						'ig_id'			=> $group_id,
						'ig_name'		=> $group_id,
						'ig_date_added'	=> $this->_today
					)
				);
			}

			$row_group = $this->income_groups->get_datum(
				'',
				array(
					'ig_id'	=> $group_id
				)
			)->row();

			if ($row_group == "") {
				// invalid group
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Invalid group id!'));
				redirect($this->_data['form_url']);
			}

			$igm_id = $this->generate_code(
				array(
					'igm_date_added'	=> $this->_today
				),
				"crc32"
			);

			$insert_member = array(
				'igm_id'			=> $igm_id,
				'igm_parent_id'		=> $igm_parent_id,
				'oauth_bridge_id'	=> $row->oauth_bridge_id,
				'ig_id'				=> $group_id,
				'igm_date_added'	=> $this->_today
			);

			$this->income_groups_members->insert(
				$insert_member
			);

			$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successully saved to group!'));
			redirect($base_form_url . "?group_id={$group_id}");
		}

		$members = $this->get_group_members($group_id);

		$this->_data['members'] = $this->generate_selection(
			"members", 
			$members, 
			"", 
			"id", 
			"name", 
			false,
			"Select Parent"
		);

		$this->_data['params']	= $group_id;
		$this->_data['title']  	= "New Group";
		$this->set_template("income_groups/tree_view", $this->_data);
	}

	public function update($group_id) {
		$this->add_scripts("https://code.jquery.com/jquery-1.12.4.min.js", true);
		$this->add_styles(base_url() . "assets/{$this->_base_controller}/css/data-tree.css", true);
		$this->add_scripts(base_url() . "assets/{$this->_base_controller}/js/data-tree.js", true);
		$this->add_scripts(base_url() . "assets/{$this->_base_controller}/js/scripts_treeview.js", true);

		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['form_url']		= base_url() . "income-groups/update/{$group_id}";

		$row_group = $this->income_groups->get_datum(
			'',
			array(
				'ig_id'	=> $group_id
			)
		)->row();

		if ($row_group == "") {
			// invalid group
			redirect(base_url() . "income-groups");
		}

		if ($_POST) {
			$merchant_number = $this->input->post('members');

			if ($merchant_number == "") {
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Please select parent!'));
				redirect($this->_data['form_url']);
			}

			$row_group_member = $this->income_groups_members->get_datum(
				'',
				array(
					'merchant_number'	=> $merchant_number
				),
				array(),
				array(
					array(
						'table_name' 	=> 'merchants',
						'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
					)
				)
			)->row();

			if ($row_group_member == "" && $merchant_number != 0) {
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Invalid selected parent!'));
				redirect($this->_data['form_url']);
			}

			$igm_parent_id = $merchant_number == '0' ? "" : $row_group_member->igm_id;

			$email_address = $this->input->post('email-address');

			$row = $this->merchants->get_datum(
				'',
				array(
					'merchant_email_address'	=> $email_address
				)
			)->row();

			if ($row == "") {
				// invalid
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Invalid merchant email!'));
				redirect($this->_data['form_url']);
			}

			$row_member = $this->income_groups_members->get_datum(
				'',
				array(
					'oauth_bridge_id'	=> $row->oauth_bridge_id
				)
			)->row();

			if ($row_member != "") {
				// invalid
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Merchant already exist/added in the group or on the other groups!'));
				redirect($this->_data['form_url']);
			}

			$igm_id = $this->generate_code(
				array(
					'igm_date_added'	=> $this->_today
				),
				"crc32"
			);

			$this->income_groups_members->insert(
				array(
					'igm_id'			=> $igm_id,
					'igm_parent_id'		=> $igm_parent_id,
					'oauth_bridge_id'	=> $row->oauth_bridge_id,
					'ig_id'				=> $group_id,
					'igm_date_added'	=> $this->_today
				)
			);

			$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successully saved to group!'));
			redirect($this->_data['form_url']);
		}

		$members = $this->get_group_members($group_id);

		$this->_data['members'] = $this->generate_selection(
			"members", 
			$members, 
			"", 
			"id", 
			"name", 
			false,
			"Select Parent"
		);

		$this->_data['params']	= $group_id;
		$this->_data['title']  	= "Update Group";
		$this->set_template("income_groups/tree_view", $this->_data);
	}

	public function merchant_list($group_id = "") {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$results = array();

		if ($group_id == "") {
			goto end;
		}

		$where = array(
			"ig_id"	=> $group_id
		);

		$data = $this->income_groups_members->get_data(
			array(
				'*'
			),
			$where,
			array(),
			array(
				array(
					'table_name' 	=> 'merchants',
					'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
				)
			),
			array(
				'filter'	=> 'igm_date_added',
				'sort'		=> 'DESC'
			)
		);

		foreach ($data as $datum) {
			$acc_id 			= $datum['merchant_number'];
			$acc_name			= $datum['merchant_fname'] . " " . $datum['merchant_mname'] . " " . $datum['merchant_lname'];
			$acc_email_address 	= $datum['merchant_email_address'];
			$acc_mobile_no		= $datum['merchant_mobile_no'];

			$acc_fname			= $datum['merchant_fname'];

			$node_id	= $acc_name . "-" . $acc_id;

			$parent_id 	= $datum['igm_parent_id'];

			if ($parent_id == "") {
				if (!isset($results[$node_id])) {
					$results[$node_id] = array(
						// 'id'			=> $acc_id,
						'name'			=> $acc_name,
						'email_address'	=> $acc_email_address,
						'mobile_no'		=> $acc_mobile_no
					);
				}
				continue;
			}

			$row = $this->merchants->get_datum(
				'',
				array(
					'igm_id'	=> $parent_id
				),
				array(),
				array(
					array(
						'table_name' 	=> 'income_groups_members',
						'condition'		=> 'income_groups_members.oauth_bridge_id = merchants.oauth_bridge_id'
					)
				)
			)->row();

			if ($row != "") {
				$acc_parent_id 				= $row->merchant_number;
				$acc_parent_name			= $row->merchant_fname . " " . $row->merchant_mname . " " . $row->merchant_lname;
				$acc_parent_email_address	= $row->merchant_email_address;
				$acc_parent_mobile_no		= $row->merchant_mobile_no;

				$node_parent_id		= $acc_parent_name . "-" . $acc_parent_id ;

				$sub_name = $row->merchant_fname;

				if (isset($results[$node_parent_id])) {
					$results[$node_parent_id]['subs'][$node_id] = array(
						// 'id'			=> $acc_id,
						'name'			=> $acc_name,
						'email_address'	=> $acc_email_address,
						'mobile_no'		=> $acc_mobile_no
					);
				} else {
					$results[$node_parent_id] = array(
						// 'id'			=> $node_parent_id,
						'name'			=> $acc_parent_name,
						'email_address'	=> $acc_parent_email_address,
						'mobile_no'		=> $acc_parent_mobile_no
					);

					$results[$node_parent_id]['subs'][$node_id] = array(
						// 'id'			=> $acc_id,
						'name'			=> $acc_name,
						'email_address'	=> $acc_email_address,
						'mobile_no'		=> $acc_mobile_no
					);
				}
			}
		}

		end:

		echo json_encode($results);
	}

	private function get_group_members($group_id = "") {
		$data = array();

		$data[] = array(
			'id'	=> '0',
			'name'	=> 'BambuPAY'
		);

		if ($group_id == "") {
			goto end;
		}

		$results = $this->income_groups_members->get_data(
			array(
				'merchant_number as "id"',
				'CONCAT(merchant_fname, " ", merchant_mname, " ", merchant_lname) as "name"'
			),
			array(
				'ig_id'	=> $group_id
			),
			array(),
			array(
				array(
					'table_name' 	=> 'merchants',
					'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
				)
			)
		);

		$data = array_merge(
			$data,
			$results
		);

		end:

		return $data;
	}
}
