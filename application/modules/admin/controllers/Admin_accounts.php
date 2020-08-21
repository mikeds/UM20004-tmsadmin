<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_accounts extends Admin_Controller {

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/tms_admin_accounts_model', 'admin_accounts');
		$this->load->model('admin/oauth_bridges_model', 'bridges');
	}

	public function index($page = 1) {
		$this->_data['add_label']= "New Account";
		$this->_data['add_url']	 = base_url() . "admin-accounts/new";

		$actions = array(
			'update'
		);

		$select = array(
			array(
				'account_number as id',
				'account_number as "Account No."',
				'account_username as "Username"',
				'account_fname as "First Name"',
				'account_mname as "Middle Name"',
				'account_lname as "Last Name"',
				'account_date_added as "Date Added"',
				'account_status as "Status"',
			)
		);

		$where = array(
			'tms_admin_id' => $this->_tms_admin
		);

		$total_rows = $this->admin_accounts->get_count(
			$where
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->admin_accounts->get_data($select, $where, array(), array('filter'=>'account_number', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 4);
		$this->_data['title']  = "Admin Accounts";
		$this->set_template("admin_accounts/list", $this->_data);
	}

	public function new() {
		$this->_data['form_url']		= base_url() . "admin-accounts/new";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$username 			= $this->input->post("username");
				$password 			= $this->input->post("password");
				$repeat_password 	= $this->input->post("repeat-password");
				$fname				= $this->input->post("first-name");
				$mname				= $this->input->post("middle-name");
				$lname				= $this->input->post("last-name");

				if ($this->validate_username("tms_admin", $username)) {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Username Already Exist!'));
					redirect($this->_data['form_url']);
				}

				if ($password == "" || $repeat_password == "") {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Please fill-up password fields!'));
					redirect($this->_data['form_url']);
				}

				if ($password != $repeat_password) {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Password not the same!'));
					redirect($this->_data['form_url']);
				}

				$password = hash("sha256", $password);
				$account_number = $this->generate_account_number("BP");

				$bridge_id = $this->generate_code(
					array(
						'account_number' 		=> $account_number,
						'account_date_added'	=> "{$this->_today}",
						'tms_admin_id'			=> "{$this->_tms_admin}"
					)
				);

				// do insert bridge id
				$this->bridges->insert(
					array(
						'oauth_bridge_id' 			=> $bridge_id,
						'oauth_bridge_date_added'	=> $this->_today
					)
				);

				$inset_data = array(
					'account_number'		=> $account_number,
					'account_fname'			=> $fname,
					'account_mname'			=> $mname,
					'account_lname'			=> $lname,
					'account_username'		=> $username,
					'account_password'		=> $password,
					'tms_admin_id'			=> $this->_tms_admin,
					'account_date_added'	=> $this->_today,
					'oauth_bridge_id'		=> $bridge_id,
				);

				$this->admin_accounts->insert(
					$inset_data
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['title']  = "New Account";
		$this->set_template("admin_accounts/form", $this->_data);
	}

	public function update($account_number) {
		$this->_data['form_url']		= base_url() . "admin-accounts/update/{$account_number}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['is_update'] 		= true;

		$account_row = $this->admin_accounts->get_datum(
			'',
			array(
				'account_number' => $account_number
			)
		)->row();

		if ($account_row == "") {
			redirect(base_url() . "admin-accounts");
		}

		$this->_data['post'] = array(
			'first-name' 	=> $account_row->account_fname,
			'middle-name' 	=> $account_row->account_mname,
			'last-name' 	=> $account_row->account_lname,
			'username' 		=> $account_row->account_username,
			'status'		=> $account_row->account_status == 1 ? "checked" : ""
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$username 			= $this->input->post("username");
				$password 			= $this->input->post("password");
				$repeat_password 	= $this->input->post("repeat-password");
				$fname				= $this->input->post("first-name");
				$mname				= $this->input->post("middle-name");
				$lname				= $this->input->post("last-name");
				$status				= $this->input->post("status");

				if ($this->validate_username("tms_admin", $username, $account_number)) {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Username Already Exist!'));
					redirect($this->_data['form_url']);
				}

				if ($password != "" || $repeat_password != "") {
					if ($password != $repeat_password) {
						$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Password not the same!'));
						redirect($this->_data['form_url']);
					}
				}

				$update_data = array(
					'account_fname'		=> $fname,
					'account_mname'		=> $mname,
					'account_lname'		=> $lname,
					'account_username'	=> $username,
					'account_status'	=> $status == 1 ? 1 : 0
				);

				if ($password != "") {
					$password = hash("sha256", $password);

					$update_data = array_merge(
						$update_data,
						array(
							'account_password' => $password
						)
					);
				}

				$this->admin_accounts->update(
					$account_number,
					$update_data
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Updated!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['title']  = "Update Account";
		$this->set_template("admin_accounts/form", $this->_data);
	}
}
