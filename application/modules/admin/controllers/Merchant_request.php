<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Merchant_request extends Admin_Controller {
	private
		$_admin_account_data = NULL;

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/merchant_pre_registration_model', 'pre_registration');
		$this->load->model('admin/merchant_accounts_model', 'merchant_accounts');
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/wallet_addresses_model', 'wallet_addresses');
		$this->load->model('admin/Merchants_disapproval_model', 'disapproval');
		$this->load->model('admin/disapproval_reason_types_model', 'disapproval_types');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
        $admin_account_data_results = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id      = $admin_account_data_results['admin_oauth_bridge_id'];
		
        $this->_data['notification']	= $this->session->flashdata('notification');
		$this->_data['form_url']        = base_url() . "merchant-request";

		if($_POST){
			$search_term = $_POST['search_term'];
			$this->_data['post'] = $_POST;
		}else{
			$search_term = "";
		}
        
        $actions = array(
            'update'
        );


        $total_rows = $this->db->query("SELECT count(account_number) as count FROM merchant_pre_registration merchant_pre_registration where concat(account_fname,' ', account_mname,'',account_lname,account_email_address,account_mobile_no) like '%$search_term%' ORDER BY account_date_added DESC");
		$total_rows = $total_rows->num_rows();
		

		$offset 	= $this->get_pagination_offset($page, $this->_limit, $total_rows);
		$query 		= $this->db->query("SELECT account_number as id, account_fname as 'First Name', account_lname as 'Last Name', account_email_address as 'Email Address', account_mobile_no as 'Mobile No.', account_date_added as 'Date Registered' FROM merchant_pre_registration merchant_pre_registration where concat(account_fname,' ',account_lname,account_email_address,account_mobile_no) like '%$search_term%' ORDER BY account_date_added DESC LIMIT $offset, $this->_limit");
		$results 	= $query->result_array();

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
		$this->_data['title']  = "Merchant Request";
		$this->set_template("pre_registration_merchant/list", $this->_data);
	}

	public function update($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']				= base_url() . "merchant-request/update/{$id}";
		$this->_data['reject_request_url']		= base_url() . "merchant-request/reject/{$id}";
		$this->_data['notification'] 			= $this->session->flashdata('notification');

		$row = $this->pre_registration->_datum(
			array(
				'*',
				'merchant_pre_registration.province_id as province_id',
				'merchant_pre_registration.sof_id as sof_id',
				'merchant_pre_registration.now_id as now_id',
				'merchant_pre_registration.account_id_type as account_id_type',
				'merchant_pre_registration.biz_type_id as biz_type_id'
			),
			array(
				array(
					'table_name'	=> 'provinces',
					'condition'		=> 'provinces.province_id = merchant_pre_registration.province_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'source_of_funds',
					'condition'		=> 'source_of_funds.sof_id = merchant_pre_registration.sof_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'nature_of_work',
					'condition'		=> 'nature_of_work.now_id = merchant_pre_registration.now_id',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'id_types',
					'condition'		=> 'id_types.id_type_id = merchant_pre_registration.account_id_type',
					'type'			=> 'left'
				),
				array(
					'table_name'	=> 'biz_types',
					'condition'		=> 'biz_types.biz_type_id = merchant_pre_registration.biz_type_id',
					'type'			=> 'left'
				)
			),
			array(
				'account_number' => $id
			)
		)->row();

		if ($row == "") {
			$this->session->set_flashdata('notification', $this->generate_notification('warning', 'Cannot find merchant request!'));
			redirect(base_url() . "merchant-request");
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
			'biz-type'		=> $row->biz_type_name,
			'id-type'		=> $row->id_type_name,
			'id-no'			=> $row->account_id_no,
			'exp-date'		=> $row->account_id_exp_date
		);

		$path = "{$this->_upload_path}/" . ENVIRONMENT . "/uploads/merchants/{$row->account_number}";
		if (file_exists($path)) {
			$files = scandir($path);
			
			if (count($files) != 0) {
				$items = "";
				$files = array_diff(scandir($path), array('.', '..'));

				foreach($files as $file) {
					$items .= '<span><a href="'. base_url() . "downloads/merchants/". $id ."?link=" . $file .'" target="_blank">'. $file .'</a></span><br>';
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
						'merchant_number' 		=> $merchant_number,
						'merchant_date_created'	=> $this->_today,
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
					'merchant_number'		=> $row->account_number,
					'oauth_bridge_id'		=> $bridge_id,
					'merchant_otp_number'	=> $row->account_otp_number,
					'merchant_sms_status'	=> $row->account_sms_status,
					'merchant_fname'		=> $row->account_fname,
					'merchant_mname'		=> $row->account_mname,
					'merchant_lname'		=> $row->account_lname,
					'merchant_email_address'=> $row->account_email_address,
					'merchant_mobile_no'	=> $row->account_mobile_no,
					'merchant_dob'			=> $row->account_dob,
					'merchant_pob'			=> $row->account_pob,
					'merchant_gender'		=> $row->account_gender,
					'merchant_house_no'		=> $row->account_house_no,
					'merchant_street'		=> $row->account_street,
					'merchant_brgy'			=> $row->account_brgy,
					'merchant_city'			=> $row->account_city,
					'province_id'			=> $row->province_id,
					'country_id'			=> $row->country_id,
					'merchant_postal_code'	=> $row->account_postal_code,
					'sof_id'				=> $row->sof_id,
					'now_id'				=> $row->now_id,
					'biz_type_id'			=> $row->biz_type_id,
					'merchant_id_type'		=> $row->account_id_type,
					'merchant_id_no'		=> $row->account_id_no,
					'merchant_id_exp_date'	=> $row->account_id_exp_date,
					'merchant_date_created'	=> $this->_today,
					'merchant_date_registered' => $row->account_date_added,
					'merchant_status'		=> 1 // activated
				);

				// do insert
				$this->merchants->insert(
					$insert_data
				);

				// generate merchant account number
				$ma_account_number = $this->generate_code(
					array(
						"merchant_accounts",
						$admin_oauth_bridge_id,
						$this->_today
					),
					"crc32"
				);

				// generate merchant account bridge
				$ma_bridge_id = $this->generate_code(
					array(
						'account_number' 		=> $ma_account_number,
						'account_date_added'	=> $this->_today,
						'admin_oauth_bridge_id'	=> $admin_oauth_bridge_id
					)
				);

				// do insert merchant account bridge id
				$this->bridges->insert(
					array(
						'oauth_bridge_id' 			=> $ma_bridge_id,
						'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id,
						'oauth_bridge_date_added'	=> $this->_today
					)
				);

				// do insert merchant account
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

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Merchant account successfully approved!'));
				redirect(base_url() . "merchant-request");				
			}

			redirect(base_url() . "merchant-request");	
		}

		$this->_data['title']  = "Merchant Request - Approval";
		$this->set_template("pre_registration_merchant/form", $this->_data);
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
							'disapproval_reason_type_id'		=> $reason_for_disapproval,
							'account_disapproval_message'	=> $disapproval_desc
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
							'account_disapproval_message'        => $disapproval_desc,
							'rejected_by_oauth_bridge_id'		=> $admin_oauth_bridge_id,
							'disapproval_reason_type_id'			=> $reason_for_disapproval,
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

					$this->session->set_flashdata('notification', $this->generate_notification('success', 'Merchant account successfully rejected!'));
					redirect(base_url() . "merchant-request");	
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
		


		$this->_data['form_url']		= base_url() . "merchant-request/reject/{$id}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['title']  = "Merchant Request - Disapproval";
		$this->set_template("pre_registration_merchant/form_disapproval", $this->_data);
	}
}