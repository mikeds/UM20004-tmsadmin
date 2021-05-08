<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rejected_merchant extends Admin_Controller {
		
	public function after_init() {
		$this->set_scripts_and_styles();
		
		$this->load->model("admin/disapproval_reason_types_model", "disapproval_types");
        $this->load->model("admin/merchants_disapproval_model", "merchants_disapproval");


		$this->_admin_account_data = $this->get_account_data();
	}

	
	public function index($page = 1) {

        $select = array(
			'rejected_id as "ID"',
			'account_number as "Account No"',
			'CONCAT(account_lname, ", ", account_fname) as "Name"',
			'account_mobile_no as "Mobile No"',
			'account_email_address as "Email Address"',
			'disapproval_reason_type_description as "Reason for Rejection"',
			'rejected_date_added as "Date"'
		);

		$inner_joints = array(
			array(
				'table_name'	=> 'disapproval_reason_types',
				'condition'		=> 'disapproval_reason_types.disapproval_reason_type_id = merchant_rejected.disapproval_reason_type_id'
			)
		);
        $where = array();

		$total_rows = $this->merchants_disapproval->get_count(
			$where,
			array(),
			$inner_joints
		);

		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);

		$data = $this->merchants_disapproval->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter'	=> 'rejected_date_added',
				'sort'		=> 'DESC'
            ),
            $offset,
            $this->_limit
		);

		$this->_data['listing'] = $this->table_listing(
			'', 
			$data,  
			$total_rows, 
			$offset, 
			$this->_limit, 
			array(), 
			2,
			false,
			false,
			'',
			''
		);


		$this->_data['title']  = "Rejected Mechant";
		$this->set_template("rejected_merchant/list", $this->_data);
	}
}
