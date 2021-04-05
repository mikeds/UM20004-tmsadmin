<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchants extends Admin_Controller {
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

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];
		
		$this->_data['add_label']= "New Merchant";
		$this->_data['add_url']	 = base_url() . "merchants/new";

		$actions = array(
			'update'
		);

		$select = array(
			'merchant_number as id',
			'merchant_status as "Status"',
			'merchant_number as "Merchant Number"',
			'merchant_code as Code',
			'merchant_fname as "First Name"',
			'merchant_mname as "Middle Name"',
			'merchant_lname as "Last Name"',
			'merchant_mobile_no as "Mobile No."',
			'merchant_contact_no as "Contact No."',
			'merchant_email_address as "Email Address"',
			'IF(merchant_gender = 2, "Female", "Male") as "Gender"',
			'merchant_dob as "Date of Birth"',
			'merchant_address as "House No./ Unit No. / Building"',
			'merchant_street as "Street"',
			'merchant_brgy as "Barangay"',
			'merchant_city as "City"',
			'province_name as "Province"',
			'country_name as "Country"',
		);

		$where = array(
			'oauth_bridge_parent_id' 	=> $admin_oauth_bridge_id,
			'merchant_role'				=> 1 // merchant
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
		$this->_data['title']  = "Merchants";
		$this->set_template("merchants/list", $this->_data);
	}

	public function new() {
		$this->_data['form_url']		= base_url() . "merchants/new";
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
				$address		= $this->input->post("address");
				$street			= $this->input->post("street");
				$brgy			= $this->input->post("brgy");
				$city			= $this->input->post("city");
				$country_id		= $this->input->post("country");
				$province_id	= $this->input->post("province");
				$mobile_no		= $this->input->post("mobile-no");
				$contact_no		= $this->input->post("contact-no");
				$email_address	= $this->input->post("email-address");

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

				$insert_data = array(
					'merchant_number'			=> $merchant_number,
					'merchant_code'				=> $merchant_code,
					'merchant_fname'			=> $fname,
					'merchant_mname'			=> $mname,
					'merchant_lname'			=> $lname,
					'merchant_gender'			=> $gender,
					'merchant_dob'				=> $dob,
					'merchant_address'			=> $address,
					'merchant_street'			=> $street,
					'merchant_brgy'				=> $brgy,
					'merchant_city'				=> $city,
					'country_id'				=> $country_id,
					'province_id'				=> $province_id,
					'merchant_mobile_no'		=> $mobile_no,
					'merchant_contact_no'		=> $contact_no,
					'merchant_email_address'	=> $email_address,
					'merchant_date_created'		=> $this->_today,
					'merchant_status'			=> 1, // activated
					'oauth_bridge_id'			=> $bridge_id
				);

				$this->merchants->insert(
					$insert_data
				);

				// create wallet address
				$this->create_wallet_address($merchant_number, $bridge_id, $admin_oauth_bridge_id);

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

		$this->_data['title']  = "New Merchant";
		$this->set_template("merchants/form", $this->_data);
	}

	public function update($merchant_number) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['is_update']		= true;
		$this->_data['form_url']		= base_url() . "merchants/update/{$merchant_number}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$country_id = 169; // PH

		$row = $this->merchants->get_datum(
			'',
			array(
				'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
				'merchant_number'			=> $merchant_number
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
			redirect(base_url() . "merchants");
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
			'address'		=> $row->merchant_address,
			'street'		=> $row->merchant_street,
			'brgy'			=> $row->merchant_brgy,
			'city'			=> $row->merchant_city,
			'mobile-no'		=> $row->merchant_mobile_no,
			'contact-no'	=> $row->merchant_contact_no,
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
				$address		= $this->input->post("address");
				$street			= $this->input->post("street");
				$brgy			= $this->input->post("brgy");
				$city			= $this->input->post("city");
				$country_id		= $this->input->post("country");
				$province_id	= $this->input->post("province");
				$mobile_no		= $this->input->post("mobile-no");
				$contact_no		= $this->input->post("contact-no");
				$email_address	= $this->input->post("email-address");
				$status			= $this->input->post("status");

				$update_data = array(
					'merchant_code'				=> $merchant_code,
					'merchant_fname'			=> $fname,
					'merchant_mname'			=> $mname,
					'merchant_lname'			=> $lname,
					'merchant_gender'			=> $gender,
					'merchant_dob'				=> $dob,
					'merchant_address'			=> $address,
					'merchant_street'			=> $street,
					'merchant_brgy'				=> $brgy,
					'merchant_city'				=> $city,
					'country_id'				=> $country_id,
					'province_id'				=> $province_id,
					'merchant_mobile_no'		=> $mobile_no,
					'merchant_contact_no'		=> $contact_no,
					'merchant_email_address'	=> $email_address,
					'merchant_status'			=> $status == 1 ? 1 : 0,
				);

				$this->merchants->update(
					$merchant_number,
					$update_data
				);

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

		$this->_data['title']  = "Update Merchant";
		$this->set_template("merchants/form", $this->_data);
	}
}
