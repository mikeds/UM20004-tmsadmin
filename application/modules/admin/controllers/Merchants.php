<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchants extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/oauth_bridges_model', 'bridges');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$this->_data['add_label']= "New Merchant";
		$this->_data['add_url']	 = base_url() . "merchants/new";

		$actions = array(
			'update'
		);

		$select = array(
			array(
				'merchant_number as id',
				'merchant_number as "Merchant No."',
				'merchant_code as "Merchant Code"',
				'merchant_biz_name as "Biz Name"',
				'merchant_address as "Address"',
				'merchant_email_address as "Email Address"',
				'merchant_contact_person as "Contact Person"',
				'merchant_contact_no as "Contact No."',
				'merchant_date_created as "Date Added"',
				'merchant_status as "Status"',
			)
		);

		$where = array(
			'tms_admin_id' => $this->_tms_admin
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
		);

		$total_rows = $this->merchants->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->merchants->get_data($select, $where, array(), $inner_joints, array('filter'=>'merchant_date_created', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 4);
		$this->_data['title']  = "Merchants";
		$this->set_template("merchants/list", $this->_data);
	}

	public function new() {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "merchants/new";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$merchant_code	= $this->input->post("merchant-code");
				$biz_name		= $this->input->post("business-name");
				$address		= $this->input->post("address");
				$contact_person	= $this->input->post("contact-person");
				$contact_no		= $this->input->post("contact-no");
				$email_address	= $this->input->post("email-address");
				
				$merchant_number = $this->generate_account_number("M");

				$bridge_id = $this->generate_code(
					array(
						'merchant_number' 		=> $merchant_number,
						'merchant_date_added'	=> "{$this->_today}",
						'tms_admin_id'			=> "{$this->_tms_admin}"
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
					'merchant_biz_name'			=> $biz_name,
					'merchant_address'			=> $address,
					'merchant_contact_person'	=> $contact_person,
					'merchant_contact_no'		=> $contact_no,
					'merchant_email_address'	=> $email_address,
					'merchant_date_created'		=> $this->_today,
					'merchant_status'			=> 1, // activated
					'oauth_bridge_id'			=> $bridge_id
				);

				$this->merchants->insert(
					$insert_data
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['title']  = "New Merchant";
		$this->set_template("merchants/form", $this->_data);
	}

	public function update($merchant_number) {
		$this->_data['is_update']		= true;
		$this->_data['form_url']		= base_url() . "merchants/update/{$merchant_number}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$row = $this->merchants->get_datum(
			'',
			array(
				'tms_admin_id' 		=> $this->_tms_admin,
				'merchant_number'	=> $merchant_number
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

		$this->_data['post'] = array(
			'merchant-code' 	=> $row->merchant_code,
			'business-name'		=> $row->merchant_biz_name,
			'address'			=> $row->merchant_address,
			'contact-person'	=> $row->merchant_contact_person,
			'contact-no'		=> $row->merchant_contact_no,
			'email-address'		=> $row->merchant_email_address,
			'status'			=> $row->merchant_status == 1 ? "checked" : ""
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$merchant_code	= $this->input->post("merchant-code");
				$biz_name		= $this->input->post("business-name");
				$address		= $this->input->post("address");
				$contact_person	= $this->input->post("contact-person");
				$contact_no		= $this->input->post("contact-no");
				$email_address	= $this->input->post("email-address");
				$status			= $this->input->post("status");

				$update_data = array(
					'merchant_code'				=> $merchant_code,
					'merchant_biz_name'			=> $biz_name,
					'merchant_address'			=> $address,
					'merchant_contact_person'	=> $contact_person,
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

		$this->_data['title']  = "Update Merchant";
		$this->set_template("merchants/form", $this->_data);
	}
}
