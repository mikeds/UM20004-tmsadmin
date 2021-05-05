<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_request extends Admin_Controller {
	private
		$_admin_account_data = NULL;

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/client_pre_registration_model', 'pre_registration');
		$this->load->model('admin/client_accounts_model', 'client_accounts');
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/wallet_addresses_model', 'wallet_addresses');
		$this->load->model('admin/client_disapproval_model', 'disapproval');
		$this->load->model('admin/disapproval_reason_types_model', 'disapproval_types');

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

		$where = array(
			'account_status'	=> 0
		);

		$inner_joints = array();

		$total_rows = $this->pre_registration->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->pre_registration->get_data($select, $where, array(), $inner_joints, array('filter'=>'account_date_added', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
		$this->_data['title']  = "Client Request";
		$this->set_template("pre_registration/list", $this->_data);
	}

	public function update($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']				= base_url() . "client-request/update/{$id}";
		$this->_data['reject_request_url']		= base_url() . "client-request/reject/{$id}";
		$this->_data['notification'] 			= $this->session->flashdata('notification');

		$row = $this->pre_registration->_datum(
			array(
				'*',
				'client_pre_registration.province_id as province_id',
				'client_pre_registration.sof_id as sof_id',
				'client_pre_registration.now_id as now_id',
				'client_pre_registration.account_id_type as account_id_type'
			),
			array(
				array(
					'table_name'	=> 'provinces',
					'condition'		=> 'provinces.province_id = client_pre_registration.province_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'source_of_funds',
					'condition'		=> 'source_of_funds.sof_id = client_pre_registration.sof_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'nature_of_work',
					'condition'		=> 'nature_of_work.now_id = client_pre_registration.now_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'id_types',
					'condition'		=> 'id_types.id_type_id = client_pre_registration.account_id_type',
					'type'			=> 'left'
				)
			),
			array(
				'account_number' => $id
			)
		)->row();

		if ($row == "") {
			$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Cannot find client request!'));
			redirect(base_url() . "client-request");
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
				$account_number = $row->account_number;

				$bridge_id = $this->generate_code(
					array(
						'account_number' 		=> $account_number,
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

				$agent_number = "";
				
				$row_agent = $this->merchants->get_datum(
					'',
					array(
						'merchant_ref_code'	=> $row->account_agent_code,
						'merchant_role'		=> 2 // agent
					)
				)->row();
				
				if ($row_agent != "") {
					$agent_number = $row_agent->merchant_number;
				}

				$insert_data = array(
					'account_number'		=> $row->account_number,
					'oauth_bridge_id'		=> $bridge_id,
					'account_otp_number'	=> $row->account_otp_number,
					'account_sms_status'	=> $row->account_sms_status,
					'account_avatar_base64'	=> $row->account_avatar_base64,
					'account_fname'			=> $row->account_fname,
					'account_mname'			=> $row->account_mname,
					'account_lname'			=> $row->account_lname,
					'account_email_address'	=> $row->account_email_address,
					'account_password'		=> $row->account_password,
					'account_mobile_no'		=> $row->account_mobile_no,
					'account_dob'			=> $row->account_dob,
					'account_pob'			=> $row->account_pob,
					'account_gender'		=> $row->account_gender,
					'account_house_no'		=> $row->account_house_no,
					'account_street'		=> $row->account_street,
					'account_brgy'			=> $row->account_brgy,
					'account_city'			=> $row->account_city,
					'province_id'			=> $row->province_id,
					'country_id'			=> $row->country_id,
					'account_postal_code'	=> $row->account_postal_code,
					'sof_id'				=> $row->sof_id,
					'now_id'				=> $row->now_id,
					'account_id_type'		=> $row->account_id_type,
					'account_id_no'			=> $row->account_id_no,
					'account_id_exp_date'	=> $row->account_id_exp_date,
					'account_agent_code'	=> $row->account_agent_code,
					'agent_number'			=> $agent_number,
					'account_date_added'	=> $this->_today,
					'account_date_registered' => $row->account_date_added
				);

				// do insert
				$this->client_accounts->insert(
					$insert_data
				);

				// create wallet address
				$this->create_wallet_address($account_number, $bridge_id, $admin_oauth_bridge_id);

				// create token auth for api
				$this->create_token_auth($account_number, $bridge_id);

				// delete account from pre-registration
				$this->pre_registration->delete(
					$account_number
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Client account successfully approved!'));
				redirect(base_url() . "client-request");				
			}

			redirect(base_url() . "client-request");	
		}

		$this->_data['title']  = "Client Request - Approval";
		$this->set_template("pre_registration/form", $this->_data);
	}

	public function reject_request($id){
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];
		
		$row = $this->pre_registration->_datum(
			array('*'),
			array(),
			array(
				'account_number'	=> $id
			)
		)->row();

		$type_of_dissapproval = $this->disapproval_types->get_data(
			array(
				'disapproval_reason_type_id as id',
				'disapproval_reason_type_description as name'
			),
			array(
				'disapproval_reason_type_status' => 1
			),
			array(),
			array(),
			array()
		);
		$type_of_disapproval_selected = ($_POST ? $_POST['reason-for-disapproval'] : '');
		$this->_data['reason_for_disapproval']	= $this->generate_selection(
			"reason-for-disapproval", 
			$type_of_dissapproval, 
			$type_of_disapproval_selected,  
			"id", 
			"name", 
			false,
			"Please Select Reason for Disapproval"
		);

		if($_POST){
			if ($this->form_validation->run('validate')) {
				$disapproval_desc				= $this->input->post('disapproval-desc');	
				$reason_for_disapproval			= $this->input->post('reason-for-disapproval');	
				$confirm_text					= strtoupper($this->input->post('confirm-text'));	
				if($confirm_text == 'CONFIRM'){	
					// update status from pre-registration
					$this->pre_registration->update(
						$id,
						array(
							'account_status'				=> 1,
							'disaproval_reason_type_id'		=> $reason_for_disapproval,
							'account_disaproval_message'	=> $disapproval_desc
						)
					);
					// Insert data to client_rejected table
					$this->disapproval->insert(
						array(
							'account_number'        			=> $row->account_number,
							'account_fname'        			 	=> $row->account_fname,
							'account_mname'         			=> $row->account_mname,
							'account_lname'         			=> $row->account_lname,
							'account_mobile_no'     			=> $row->account_mobile_no,
							'account_email_address' 			=> $row->account_email_address,
							'account_disaproval_message'        => $disapproval_desc,
							'rejected_by_oauth_bridge_id'		=> $admin_oauth_bridge_id,
							'disaproval_reason_type_id'			=> $reason_for_disapproval,
							'rejected_date_added'     			=> $this->_today
						)
					);

					// Send rejection email
					$email_from = getenv("SMTPUSER", true);
					$send_to	= $row->account_email_address;
					$title	= "Application Declined";
					if($reason_for_disapproval == "1"){
						$message = "Your application has been rejected. You did not submit a valid government ID. Please resubmit your application with a clear and full photo of your valid government ID. Thank you! <br/> - BambuPay Team";
						
					}else if($reason_for_disapproval == "2"){
						$message = "Your application has been rejected. You did not submit a clear and full Selfie. Kindly re-submit your application with a clear and full Selfie. Thank you! <br/> - BambuPay Team";
						//$message .= "BambuPay Team"; 
					}else if($reason_for_disapproval == "3"){
						$message = "Your application has been rejected. You did not submit a clear attachment. Kindly re-submit your application with a clear attachment. Thank you! <br/> - BambuPay Team";
						//$message .= "BambuPay Team";  
					}else{
						$message = "Your application has been rejected. You did not submit a valid attachment. Kindly re-submit your application with a valid attachment. Thank you! <br/> - BambuPay Team";
						//$message .= "BambuPay Team";  
					}

					send_email(
						$email_from,
						$send_to,
						$title,
						$message
					);

					$this->session->set_flashdata('notification', $this->generate_notification('success', 'Client account successfully rejected!'));
					redirect(base_url() . "client-request");	
				}else{

					$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Please type CONFIRM to procceed!'));

				}	

			}
		}

		$this->_data['post'] = array(
			'first-name' 		=> $row->account_fname,
			'last-name' 		=> $row->account_lname,
			'email-address' 	=> $row->account_email_address,
			'mobile-no' 		=> $row->account_mobile_no,
			'disapproval-desc'	=> ($_POST ? $_POST['disapproval-desc'] : ''),
			'confirm-text'		=> ($_POST ? $_POST['confirm-text'] : ''),
		);
		$this->_data['form_url']		= base_url() . "client-request/reject/{$id}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['title']  = "Client Request - Disapproval";
		$this->set_template("pre_registration/form_disapproval", $this->_data);
	}
}
