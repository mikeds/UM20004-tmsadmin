<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_groups_quickpayqr extends Admin_Controller {
	private
		$_admin_account_data = NULL,
		$_transaction_type_id = "txtype_quickpayqr1";
		
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
		
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['add_label']		= "New Group";
		$this->_data['add_url']	 		= base_url() . "income-groups-quickpayqr/new";

		$actions = array(
			'update',
			'delete'
		);

		$select = array(
			'income_groups.ig_id as id',
			'income_groups.ig_id as "Group ID"',
			'CONCAT(merchant_lname, ", ", merchant_fname) as "Parent Name"',
			'ig_date_added as "Date Added"'
		);

		$where = array(
			'transaction_type_id'	=> $this->_transaction_type_id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'income_groups_members',
				'condition'		=> 'income_groups_members.igm_id = income_groups.igm_leader_id'
			),
			array(
				'table_name' 	=> 'merchants',
				'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
			)
		);

		$total_rows = $this->income_groups->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->income_groups->get_data($select, $where, array(), $inner_joints, array('filter'=>'ig_date_added', 'sort'=>'ASC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);

		$this->_data['title']  = "QuickPayQR";
		$this->set_template("income_groups/list", $this->_data);
	}

	public function new() {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$group_id = "";

		if (isset($_GET['group_id'])) {
			$group_id = $_GET['group_id'];
		}

		$base_form_url				= base_url() . "income-groups-quickpayqr/new";
		$this->_data['form_url']	= $base_form_url . (($group_id != "") ? "?group_id={$group_id}" : "");

		// check group exist
		if ($group_id != "") {
			$row_group = $this->income_groups->get_datum(
				'',
				array(
					'ig_id'					=> $group_id,
					'transaction_type_id'	=> $this->_transaction_type_id
				)
			)->row();

			if ($row_group == "") {
				// invalid group id
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Invalid Group ID!'));
				redirect($base_form_url);
			}
		}

		$igm_parent_id = "";

		$results = $this->income_groups_members->get_data(
			array(
				'igm_id as "id"',
				'CONCAT(merchant_lname, ", ", merchant_fname) as "Name"',
				'igm_date_added as "Date Added"'
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
			), 
			array('filter'=>'igm_id', 'sort'=>'ASC')
		);

		if (count($results) > 0) {
			$igm_parent_id = $results[count($results) - 1]["id"];
		}

		if ($_POST) {
			$email_address = $this->input->post("email-address");

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

			if ($group_id != "") {
				$row_member = $this->income_groups_members->get_datum(
					'',
					array(
						'oauth_bridge_id'	=> $row->oauth_bridge_id,
						'ig_id' 			=> $group_id
					)
				)->row();

				if ($row_member != "") {
					// invalid
					$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Merchant already exist/added in the group or on the other groups!'));
					redirect($this->_data['form_url']);
				}
			}

			$is_new_group = false;
			
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
						'ig_id'					=> $group_id,
						'ig_name'				=> $group_id,
						'ig_date_added'			=> $this->_today,
						'transaction_type_id'	=> $this->_transaction_type_id
					)
				);

				$is_new_group = true;
			}

			if (count($results) == 0) {
				$igm_parent_id = $this->income_groups_members->insert(
					array(
						'igm_parent_id'		=> "",
						'oauth_bridge_id'	=> $admin_oauth_bridge_id,
						'ig_id'				=> $group_id,
						'igm_date_added'	=> $this->_today
					)
				);
			}

			$insert_member = array(
				'igm_parent_id'		=> $igm_parent_id,
				'oauth_bridge_id'	=> $row->oauth_bridge_id,
				'ig_id'				=> $group_id,
				'igm_date_added'	=> $this->_today
			);

			$igm_id = $this->income_groups_members->insert(
				$insert_member
			);

			if ($is_new_group) {
				$this->income_groups->update(
					$group_id,
					array(
						'igm_leader_id' => $igm_id
					)
				);
			}

			$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successully saved new group!'));
			redirect($base_form_url . "?group_id={$group_id}");
		}

		$this->_data['listing'] = $this->table_listing('', $results, count($results));
		$this->_data['title']  	= "QuickPayQR - New Group Member";
		$this->set_template("income_groups/form", $this->_data);
	}

	public function update($group_id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['form_url']		= base_url() . "income-groups-quickpayqr/update/{$group_id}";

		$row_group = $this->income_groups->get_datum(
			'',
			array(
				'ig_id'					=> $group_id,
				'transaction_type_id'	=> $this->_transaction_type_id
			)
		)->row();

		if ($row_group == "") {
			// INVALID GROUP
			redirect(base_url());
		}

		$results = $this->income_groups_members->get_data(
			array(
				'igm_id as "id"',
				'CONCAT(merchant_lname, ", ", merchant_fname) as "Name"',
				'igm_date_added as "Date Added"'
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
			), 
			array('filter'=>'igm_id', 'sort'=>'ASC')
		);

		$igm_parent_id = "";

		if (count($results) > 0) {
			$igm_parent_id = $results[count($results) - 1]["id"];
		}

		if ($_POST) {
			$email_address = $this->input->post("email-address");

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

			// check if member is exist
			$row_member = $this->income_groups_members->get_datum(
				'',
				array(
					'oauth_bridge_id'	=> $row->oauth_bridge_id,
					'ig_id' 			=> $group_id
				)
			)->row();

			if ($row_member != "") {
				// invalid
				$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Merchant already exist/added in the group or on the other groups!'));
				redirect($this->_data['form_url']);
			}

			if (count($results) == 0) {
				$igm_parent_id = $this->income_groups_members->insert(
					array(
						'igm_parent_id'		=> "",
						'oauth_bridge_id'	=> $admin_oauth_bridge_id,
						'ig_id'				=> $group_id,
						'igm_date_added'	=> $this->_today
					)
				);
			}

			$insert_member = array(
				'igm_parent_id'		=> $igm_parent_id,
				'oauth_bridge_id'	=> $row->oauth_bridge_id,
				'ig_id'				=> $group_id,
				'igm_date_added'	=> $this->_today
			);

			$igm_id = $this->income_groups_members->insert(
				$insert_member
			);

			$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successully added new member!'));
			redirect($this->_data['form_url']);
		}

		$this->_data['listing'] = $this->table_listing('', $results, count($results));
		$this->_data['title']  	= "QuickPayQR - Update Group";
		$this->set_template("income_groups/form", $this->_data);
	}

	public function delete($group_id) {
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['form_url']		= base_url() . "income-groups-quickpayqr/delete/{$group_id}";

		$row_group = $this->income_groups->get_datum(
			'',
			array(
				'ig_id'					=> $group_id,
				'transaction_type_id'	=> $this->_transaction_type_id
			)
		)->row();

		if ($row_group == "") {
			// INVALID GROUP
			redirect(base_url());
		}

		if ($_POST) {
			$confirmation = $this->input->post("confirmation");

			$where = array(
				'ig_id'	=> $group_id
			);

			$this->income_groups->delete($where);
			$this->income_groups_members->delete($where);

			if ($confirmation == "DELETE") {
				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successully group deleted!'));
				redirect(base_url() . "income-groups-quickpayqr");
			}
		}

		$this->_data['title']  	= "QuickPayQR - DELETE GROUP - ID: {$group_id}";
		$this->set_template("income_groups/form_delete", $this->_data);
	}
}
