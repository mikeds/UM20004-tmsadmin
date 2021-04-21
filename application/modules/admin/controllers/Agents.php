<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agents extends Admin_Controller {
	private
		$_admin_account_data = NULL;

	private
		$_gender = array(
			array(
				'id'	=> 1,
				'name' 	=> "Male"
			),
			array(
				'id'	=> 2,
				'name' 	=> "Female"
			)
		);
		
	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/countries_model', 'countries');
		$this->load->model('admin/provinces_model', 'provinces');
		$this->load->model('admin/wallet_addresses_model', 'wallet_addresses');
		$this->load->model('admin/merchant_accounts_model', 'merchant_accounts');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];
		
		$this->_data['add_label']= "New Agent";
		$this->_data['add_url']	 = base_url() . "agents/new";

		$actions = array(
			'update'
		);

		$select = array(
			'merchant_number as id',
			'merchant_status as "Status"',
			// 'IF(merchant_email_status = 1, "Verified", "Unverified") as "Email Status"',
			'merchant_number as "Agent Number"',
			'merchant_ref_code as "Code"',
			// 'merchant_code as Code',
			'merchant_fname as "First Name"',
			'merchant_mname as "Middle Name"',
			'merchant_lname as "Last Name"',
			'merchant_mobile_no as "Mobile No."',
			'merchant_email_address as "Email Address"',
			'IF(merchant_gender = 2, "Female", "Male") as "Gender"',
			'merchant_dob as "Date of Birth"',
			'merchant_house_no as "House No./ Unit No. / Building"',
			'merchant_street as "Street"',
			'merchant_brgy as "Barangay"',
			'merchant_city as "City"',
			'province_name as "Province"',
			'country_name as "Country"',
		);

		$where = array(
			'oauth_bridge_parent_id' 	=> $admin_oauth_bridge_id,
			'merchant_role'				=> 2 // agents
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = merchants.oauth_bridge_id'
			),
			array(
				'table_name' 	=> 'tms_admins',
				'condition'		=> 'tms_admins.oauth_bridge_id = oauth_bridges.oauth_bridge_parent_id'
			),
			array(
				'table_name' 	=> 'countries',
				'condition'		=> 'countries.country_id = merchants.country_id',
				'type'			=> 'left'
			),
			array(
				'table_name' 	=> 'provinces',
				'condition'		=> 'provinces.province_id = merchants.province_id',
				'type'			=> 'left'
			)
		);

		$total_rows = $this->merchants->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->merchants->get_data($select, $where, array(), $inner_joints, array('filter'=>'merchant_date_created', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
		$this->_data['title']  = "Agent List";
		$this->set_template("agents/list", $this->_data);
	}

	public function new() {
		$this->_data['form_url']		= base_url() . "agents/new";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$country_id = 169; // PH

		$countries = $this->countries->get_data(
			array(
				'country_id as id',
				'country_name as name'
			),
			array(
				'country_status' => 1
			),
			array(),
			array(),
			array(
				'filter' 	=> "country_name",
				'sort'		=> "ASC"
			)
		);

		$provinces = $this->provinces->get_data(
			array(
				'province_id as id',
				'province_name as name'
			),
			array(
				'country_id' 		=> $country_id,
				'province_status' 	=> 1
			),
			array(),
			array(),
			array(
				'filter' 	=> "province_name",
				'sort'		=> "ASC"
			)
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$merchant_code	= $this->input->post("merchant-code");
				$fname			= $this->input->post("first-name");
				$mname			= $this->input->post("middle-name");
				$lname			= $this->input->post("last-name");
				$gender			= $this->input->post("gender");
				$dob			= $this->input->post("dob");
				$house_no		= $this->input->post("house-no");
				$street			= $this->input->post("street");
				$brgy			= $this->input->post("brgy");
				$city			= $this->input->post("city");
				// $country_id		= $this->input->post("country");
				$province_id	= $this->input->post("province");
				$mobile_no		= $this->input->post("mobile-no");
				$contact_no		= $this->input->post("contact-no");
				$email_address	= $this->input->post("email-address");

				$repeat_password	= $this->input->post("repeat-password");
				$password			= $this->input->post("password");

				$row_m_email_address = $this->merchants->get_datum(
					'',
					array(
						'merchant_email_address'	=> $email_address
					)
				)->row();

				if ($row_m_email_address != "") {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Email address is already used!'));
					redirect($this->_data['form_url']);
				}

				$row_ma_email_address = $this->merchant_accounts->get_datum(
					'',
					array(
						'account_username'	=> $email_address
					)
				)->row();

				if ($row_ma_email_address != "") {
					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Email address is already used!'));
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

				$merchant_number = $this->generate_code(
					array(
						"merchant",
						$admin_oauth_bridge_id,
						$this->_today
					),
					"crc32"
				);

				$bridge_id = $this->generate_code(
					array(
						'merchant_number' 		=> $merchant_number,
						'merchant_date_added'	=> $this->_today,
						'admin_oauth_bridge_id'	=> $admin_oauth_bridge_id
					)
				);

				// do insert bridge id
				$this->bridges->insert(
					array(
						'oauth_bridge_id' 			=> $bridge_id,
						'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
						'oauth_bridge_date_added'	=> $this->_today
					)
				);

				$ref_code = substr(number_format(strtotime($this->_today) * rand(),0,'',''),0,6);
				$ref_code = substr($fname, 0, 1) . substr($lname, 0, 1) . $ref_code;

				$insert_data = array(
					'merchant_ref_code'			=> strtolower($ref_code),
					'merchant_number'			=> $merchant_number,
					// 'merchant_code'				=> $merchant_code,
					'merchant_fname'			=> $fname,
					'merchant_mname'			=> $mname,
					'merchant_lname'			=> $lname,
					'merchant_gender'			=> $gender,
					'merchant_dob'				=> $dob,
					'merchant_house_no'			=> $house_no,
					'merchant_street'			=> $street,
					'merchant_brgy'				=> $brgy,
					'merchant_city'				=> $city,
					'country_id'				=> $country_id,
					'province_id'				=> $province_id,
					'merchant_mobile_no'		=> $mobile_no,
					'merchant_email_address'	=> $email_address,
					'merchant_date_created'		=> $this->_today,
					'merchant_status'			=> 1, // activated
					'oauth_bridge_id'			=> $bridge_id,
					'merchant_role'				=> 2 // agents
				);

				$this->merchants->insert(
					$insert_data
				);

				// create wallet address
				$this->create_wallet_address($merchant_number, $bridge_id, $admin_oauth_bridge_id);

				// // create token auth for api
				// $this->create_token_auth($merchant_number, $bridge_id);

				// create account
				$password = hash("sha256", $password);
				$account_number = $this->generate_code(
					array(
						"agent_account",
						$admin_oauth_bridge_id,
						$this->_today
					),
					"crc32"
				);

				$bridge_id = $this->generate_code(
					array(
						'account_number' 			=> $account_number,
						'account_date_added'		=> $this->_today,
						'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id
					)
				);

				// do insert bridge id
				$this->bridges->insert(
					array(
						'oauth_bridge_id' 			=> $bridge_id,
						'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
						'oauth_bridge_date_added'	=> $this->_today
					)
				);

				$insert_data = array(
					'merchant_number'		=> $merchant_number,
					'account_number'		=> $account_number,
					'account_fname'			=> $fname,
					'account_mname'			=> $mname,
					'account_lname'			=> $lname,
					'account_username'		=> $email_address,
					'account_password'		=> $password,
					'account_date_added'	=> $this->_today,
					'oauth_bridge_id'		=> $bridge_id,
				);

				$this->merchant_accounts->insert(
					$insert_data
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['gender'] = $this->generate_selection(
			"gender", 
			$this->_gender, 
			1, 
			"id", 
			"name", 
			true
		);

		$this->_data['country']	= $this->generate_selection(
			"country", 
			$countries, 
			$country_id, 
			"id", 
			"name", 
			true
		);
		
		$this->_data['province']	= $this->generate_selection(
			"province", 
			$provinces, 
			"", 
			"id", 
			"name", 
			false,
			"Please Select Province"
		);

		$this->_data['title']  = "New Agent";
		$this->set_template("agents/form", $this->_data);
	}

	public function update($merchant_number) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['is_update']		= true;
		$this->_data['form_url']		= base_url() . "agents/update/{$merchant_number}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$country_id = 169; // PH

		$row = $this->merchants->get_datum(
			'',
			array(
				'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
				'merchant_number'			=> $merchant_number,
				'merchant_role'				=> 2
			),
			array(),
			array(
				array(
					'table_name' 	=> 'oauth_bridges',
					'condition'		=> 'oauth_bridges.oauth_bridge_id = merchants.oauth_bridge_id'
				),
				array(
					'table_name' 	=> 'tms_admins',
					'condition'		=> 'tms_admins.oauth_bridge_id = oauth_bridges.oauth_bridge_parent_id'
				),
			)
		)->row();

		if ($row == "") {
			redirect(base_url() . "agents");
		}


		$country_id 	= $row->country_id;
		$province_id	= $row->province_id;
		$gender_id		= $row->merchant_gender;

		$countries = $this->countries->get_data(
			array(
				'country_id as id',
				'country_name as name'
			),
			array(
				'country_status'	=> 1
			),
			array(),
			array(),
			array(
				'filter' 	=> "country_name",
				'sort'		=> "ASC"
			)
		);

		$provinces = $this->provinces->get_data(
			array(
				'province_id as id',
				'province_name as name'
			),
			array(
				'country_id' 		=> $country_id,
				'province_status' 	=> 1
			),
			array(),
			array(),
			array(
				'filter' 	=> "province_name",
				'sort'		=> "ASC"
			)
		);

		$this->_data['post'] = array(
			'merchant-code'	=> $row->merchant_code,
			'first-name'	=> $row->merchant_fname,
			'middle-name'	=> $row->merchant_mname,
			'last-name'		=> $row->merchant_lname,
			'dob'			=> $row->merchant_dob,
			'house-no'		=> $row->merchant_house_no,
			'street'		=> $row->merchant_street,
			'brgy'			=> $row->merchant_brgy,
			'city'			=> $row->merchant_city,
			'mobile-no'		=> $row->merchant_mobile_no,
			'email-address'	=> $row->merchant_email_address,
			'status'		=> $row->merchant_status == 1 ? "checked" : ""
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$merchant_code	= $this->input->post("merchant-code");
				$fname			= $this->input->post("first-name");
				$mname			= $this->input->post("middle-name");
				$lname			= $this->input->post("last-name");
				$gender			= $this->input->post("gender");
				$dob			= $this->input->post("dob");
				$house_no		= $this->input->post("house-no");
				$street			= $this->input->post("street");
				$brgy			= $this->input->post("brgy");
				$city			= $this->input->post("city");
				$country_id		= 169;
				$province_id	= $this->input->post("province");
				$mobile_no		= $this->input->post("mobile-no");
				$contact_no		= $this->input->post("contact-no");
				$email_address	= $this->input->post("email-address");
				$status			= $this->input->post("status");

				$repeat_password	= $this->input->post("repeat-password");
				$password			= $this->input->post("password");

				$row_account = $this->merchant_accounts->get_datum(
					'', 
					array(
						'merchant_number'	=> $merchant_number
					)
				)->row();

				if ($row_account != "") {
					if ($this->validate_username("merchant", $username, $row_account->account_number)) {
						$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Username Already Exist!'));
						redirect($this->_data['form_url']);
					}	
				} else {
					if ($this->validate_username("merchant", $username)) {
						$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Username Already Exist!'));
						redirect($this->_data['form_url']);
					}
				}

				if ($password != "" || $repeat_password != "") {
					if ($password != $repeat_password) {
						$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Password not the same!'));
						redirect($this->_data['form_url']);
					}
				}

				$update_data = array(
					// 'merchant_code'				=> $merchant_code,
					'merchant_fname'			=> $fname,
					'merchant_mname'			=> $mname,
					'merchant_lname'			=> $lname,
					'merchant_gender'			=> $gender,
					'merchant_dob'				=> $dob,
					'merchant_house_no'			=> $house_no,
					'merchant_street'			=> $street,
					'merchant_brgy'				=> $brgy,
					'merchant_city'				=> $city,
					'country_id'				=> $country_id,
					'province_id'				=> $province_id,
					'merchant_mobile_no'		=> $mobile_no,
					'merchant_email_address'	=> $email_address,
					'merchant_status'			=> $status == 1 ? 1 : 0,
				);

				if ($fname != $row->merchant_fname || $lname != $row->merchant_lname) {
					$ref_code = substr(number_format(strtotime($this->_today) * rand(),0,'',''),0,6);
					$ref_code = substr($fname, 0, 1) . substr($lname, 0, 1) . $ref_code;

					$update_data = array_merge(
						$update_data,
						array(
							'merchant_ref_code' => strtolower($ref_code)
						)
					);
				}

				$this->merchants->update(
					$merchant_number,
					$update_data
				);

				// update agent account
				$update_data = array(
					'account_fname'		=> $fname,
					'account_mname'		=> $mname,
					'account_lname'		=> $lname,
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

				$row_account = $this->merchant_accounts->get_datum(
					'', 
					array(
						'merchant_number'	=> $merchant_number
					)
				)->row();

				if ($row_account != "") {
					$this->merchant_accounts->update(
						$row_account->account_number,
						$update_data
					);
				}

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Updated!'));
				redirect($this->_data['form_url']);
			}
		}


		$this->_data['gender'] = $this->generate_selection(
			"gender", 
			$this->_gender, 
			$gender_id, 
			"id", 
			"name", 
			true
		);

		$this->_data['country']	= $this->generate_selection(
			"country", 
			$countries, 
			$country_id, 
			"id", 
			"name", 
			true
		);
		
		$this->_data['province']	= $this->generate_selection(
			"province", 
			$provinces, 
			$province_id, 
			"id", 
			"name", 
			true
		);

		$this->_data['title']  = "Update Agent";
		$this->set_template("agents/form", $this->_data);
	}
}
