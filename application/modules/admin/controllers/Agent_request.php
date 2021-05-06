<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_request extends Admin_Controller {
	private
		$_admin_account_data = NULL;

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/agent_pre_registration_model', 'pre_registration');
		$this->load->model('admin/merchant_accounts_model', 'merchant_accounts');
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/wallet_addresses_model', 'wallet_addresses');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['notification']= $this->session->flashdata('notification');

		$actions = array(
			'update'
		);

		$select = array(
			'account_number as id',
			'account_fname as "First Name"',
			'account_mname as "Middle Name"',
			'account_lname as "Last Name"',
			'account_email_address as "Email Address"',
			'account_mobile_no as "Mobile No."',
			'account_date_added as "Date Registered"'
		);

		$where = array();

		$inner_joints = array();

		$total_rows = $this->pre_registration->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->pre_registration->get_data($select, $where, array(), $inner_joints, array('filter'=>'account_date_added', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
		$this->_data['title']  = "Agent Request";
		$this->set_template("pre_registration_agent/list", $this->_data);
	}

	public function update($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']				= base_url() . "agent-request/update/{$id}";
		$this->_data['notification'] 			= $this->session->flashdata('notification');

		$row = $this->pre_registration->_datum(
			array(
				'*',
				'agent_pre_registration.province_id as province_id',
				'agent_pre_registration.sof_id as sof_id',
				'agent_pre_registration.now_id as now_id',
				'agent_pre_registration.account_id_type as account_id_type'
			),
			array(
				array(
					'table_name'	=> 'provinces',
					'condition'		=> 'provinces.province_id = agent_pre_registration.province_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'source_of_funds',
					'condition'		=> 'source_of_funds.sof_id = agent_pre_registration.sof_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'nature_of_work',
					'condition'		=> 'nature_of_work.now_id = agent_pre_registration.now_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'id_types',
					'condition'		=> 'id_types.id_type_id = agent_pre_registration.account_id_type',
					'type'			=> 'left'
				)
			),
			array(
				'account_number' => $id
			)
		)->row();

		if ($row == "") {
			$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Cannot find agent request!'));
			redirect(base_url() . "agent-request");
		}

		$this->_data['post'] = array(
			'first-name' 	=> $row->account_fname,
			'middle-name' 	=> $row->account_mname,
			'last-name' 	=> $row->account_lname,
			'email-address' => $row->account_email_address,
			'mobile-no' 	=> $row->account_mobile_no,
			'dob'			=> $row->account_dob,
			'pob'			=> $row->account_pob,
			'gender'		=> ($row->account_gender == 1 ? "Male" : "Female"),
			'house-no'		=> $row->account_house_no,
			'street'		=> $row->account_street,
			'barangay'		=> $row->account_brgy,
			'city'			=> $row->account_city,
			'province'		=> $row->province_name,
			'postal-code'	=> $row->account_postal_code,
			'sof'			=> $row->sof_name,
			'now'			=> $row->now_name,
			'id-type'		=> $row->id_type_name,
			'id-no'			=> $row->account_id_no,
			'exp-date'		=> $row->account_id_exp_date
		);

		$path = "{$this->_upload_path}/" . ENVIRONMENT . "/uploads/agents/{$row->account_number}";
		if (file_exists($path)) {
			$files = scandir($path);
			
			if (count($files) != 0) {
				$items = "";
				$files = array_diff(scandir($path), array('.', '..'));

				foreach($files as $file) {
					$items .= '<span><a href="'. base_url() . "downloads/agents/". $id ."?link=" . $file .'" target="_blank">'. $file .'</a></span><br>';
				}

				$this->_data['post'] = array_merge(
					$this->_data['post'],
					array(
						'files' => $items
					)
				);
			}
		}
		
		if (!empty($row->account_avatar_base64)) {
			$this->_data['post'] = array_merge(
				$this->_data['post'],
				array(
					'profile-picture'=> base_url() . "image-viewer/profile-picture/" . $row->account_number 
				)
			);
		}

		if (!empty($row->account_id_front_base64)) {
			$this->_data['post'] = array_merge(
				$this->_data['post'],
				array(
					'id-front'=> base_url() . "image-viewer/id-front/" . $row->account_number 
				)
			);
		}

		if (!empty($row->account_id_back_base64)) {
			$this->_data['post'] = array_merge(
				$this->_data['post'],
				array(
					'id-back'=> base_url() . "image-viewer/id-back/" . $row->account_number 
				)
			);
		}

		if ($_POST) {
			$status	= $this->input->post("status");

			if ($status == 1) {
				$merchant_number = $row->account_number;

				$bridge_id = $this->generate_code(
					array(
						'merchant_number' 		    => $merchant_number,
						'merchant_date_created'		=> $this->_today,
						'merchant_oauth_bridge_id'	=> $admin_oauth_bridge_id
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
					'merchant_number'		    => $row->account_number,
					'merchant_role'				=> 2,
					'oauth_bridge_id'			=> $bridge_id,
					'merchant_otp_number'	    => $row->account_otp_number,
					'merchant_sms_status'	    => $row->account_sms_status,
					'merchant_fname'		    => $row->account_fname,
					'merchant_mname'		    => $row->account_mname,
					'merchant_lname'		    => $row->account_lname,
					'merchant_email_address'   	=> $row->account_email_address,
					'merchant_mobile_no'	    => $row->account_mobile_no,
					'merchant_dob'			    => $row->account_dob,
					'merchant_pob'			    => $row->account_pob,
					'merchant_gender'		    => $row->account_gender,
					'merchant_house_no'			=> $row->account_house_no,
					'merchant_street'		    => $row->account_street,
					'merchant_brgy'				=> $row->account_brgy,
					'merchant_city'				=> $row->account_city,
					'province_id'				=> $row->province_id,
					'country_id'				=> $row->country_id,
					'merchant_postal_code'	    => $row->account_postal_code,
					'sof_id'					=> $row->sof_id,
					'now_id'					=> $row->now_id,
					'merchant_id_type'		    => $row->account_id_type,
					'merchant_id_no'		    => $row->account_id_no,
					'merchant_id_exp_date'	    => $row->account_id_exp_date,
					'merchant_date_created'		=> $this->_today,
					'merchant_date_registered' 	=> $row->account_date_added,
					'merchant_status'		    => 1 // activated
				);

				// do insert
				$this->merchants->insert(
					$insert_data
				);

				// generate agent account number
				$ma_account_number = $this->generate_code(
					array(
						"merchant_accounts",
						$admin_oauth_bridge_id,
						$this->_today
					),
					"crc32"
				);

				// generate agent account bridge
				$ma_bridge_id = $this->generate_code(
					array(
						'account_number' 		=> $ma_account_number,
						'account_date_added'	=> $this->_today,
						'admin_oauth_bridge_id'	=> $admin_oauth_bridge_id
					)
				);

				// do insert agent account bridge id
				$this->bridges->insert(
					array(
						'oauth_bridge_id' 			=> $ma_bridge_id,
						'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
						'oauth_bridge_date_added'	=> $this->_today
					)
				);

				// do insert agent account
				$this->merchant_accounts->insert(
					array(
						'account_number'		=> $ma_account_number,
						'merchant_number'		=> $merchant_number,
						'oauth_bridge_id'		=> $ma_bridge_id,
						'account_avatar_base64'	=> $row->account_avatar_base64,
						'account_username'		=> $row->account_email_address,
						'account_password'		=> $row->account_password,
						'account_fname'			=> $row->account_fname,
						'account_mname'			=> $row->account_mname,
						'account_lname'			=> $row->account_lname,
						'account_date_added'	=> $this->_today
					)
				);

				// create wallet address
				$this->create_wallet_address($merchant_number, $bridge_id, $admin_oauth_bridge_id);

				// create token auth for api
				$this->create_token_auth($ma_account_number, $ma_bridge_id);

				// delete account from pre-registration
				$this->pre_registration->delete(
					$merchant_number
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Agent account successfully approved!'));
				redirect(base_url() . "agent-request");				
			}

			redirect(base_url() . "agent-request");	
		}

		$this->_data['title']  = "Agent Request - Approval";
		$this->set_template("pre_registration_agent/form", $this->_data);
	}

}
