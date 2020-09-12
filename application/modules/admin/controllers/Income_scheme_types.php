<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_scheme_types extends Admin_Controller {
	private
		$_admin_account_data = NULL;

	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/income_scheme_types_model', 'income_scheme_types');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['add_label']= "New Scheme Type";
		$this->_data['add_url']	 = base_url() . "income-scheme-types/new";

		$actions = array(
			'update'
		);

		$select = array(
			'scheme_type_id as id',
			'scheme_type_status as Status',
			'scheme_type_code as Code',
			'scheme_type_name as Name',
			'scheme_type_date_created as "Date Created"',
		);

		$where = array(
			'oauth_bridge_parent_id' => $admin_oauth_bridge_id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = income_scheme_types.oauth_bridge_id'
			)
		);

		$total_rows = $this->income_scheme_types->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->income_scheme_types->get_data($select, $where, array(), $inner_joints, array('filter'=>'scheme_type_date_created', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 4);
		$this->_data['title']  = "Income Scheme Types";
		$this->set_template("income_scheme_types/list", $this->_data);
	}

	public function new() {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "income-scheme-types/new";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$code	= $this->input->post("code");
				$name	= $this->input->post("name");

				$bridge_id = $this->generate_code(
					array(
						'income_scheme_date_created'	=> "{$this->_today}",
						'admin_oauth_bridge_id'			=> $admin_oauth_bridge_id
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
					'scheme_type_code'			=> $code,
					'scheme_type_name'			=> $name,
					'scheme_type_date_created'	=> $this->_today,
					'oauth_bridge_id'			=> $bridge_id,
				);

				$this->income_scheme_types->insert(
					$insert_data
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['title']  = "New Scheme Type";
		$this->set_template("income_scheme_types/form", $this->_data);
	}

	public function update($id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "income-scheme-types/update/{$id}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['is_update'] 		= true;


		$where = array(
			'oauth_bridge_parent_id' 	=> $admin_oauth_bridge_id,
			'scheme_type_id'			=> $id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'oauth_bridges',
				'condition'		=> 'oauth_bridges.oauth_bridge_id = income_scheme_types.oauth_bridge_id'
			)
		);

		$row = $this->income_scheme_types->get_datum(
			'',
			$where,
			array(),
			$inner_joints
		)->row();

		if ($row == "") {
			redirect(base_url() . "income-scheme-types");
		}

		$this->_data['post'] = array(
			'code' 		=> $row->scheme_type_code,
			'name' 		=> $row->scheme_type_name,
			'status'	=> $row->scheme_type_status == 1 ? "checked" : ""
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$code	= $this->input->post("code");
				$name	= $this->input->post("name");
				$status = $this->input->post("status");

				$update_data = array(
					'scheme_type_code'			=> $code,
					'scheme_type_name'			=> $name,
					'scheme_type_status'		=> $status == 1 ? 1 : 0
				);

				$this->income_scheme_types->update(
					$id,
					$update_data
				);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Updated!'));
				redirect($this->_data['form_url']);
			}
		}

		$this->_data['title']  = "Update Scheme Type";
		$this->set_template("income_scheme_types/form", $this->_data);
	}
}
