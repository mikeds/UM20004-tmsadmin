<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_shares extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/income_groups_model', 'income_groups');
		$this->load->model('admin/income_groups_members_model', 'income_groups_members');
		$this->load->model('admin/transaction_types_model', 'transaction_types');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$actions = array(
			'update'
		);

		$select = array(
			'ig_id as id',
			'ig_id as "Group ID"',
			'ig_date_added as "Date Added"'
		);

		$where = array();

		$inner_joints = array();

		$total_rows = $this->income_groups->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->income_groups->get_data($select, $where, array(), $inner_joints, array('filter'=>'ig_date_added', 'sort'=>'DESC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);

		$this->_data['title']  = "Income Shares Groups";
		$this->set_template("income_shares/list", $this->_data);
	}

	public function update($group_id) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['form_url']		= base_url() . "income-shares/update/{$group_id}";

		$tx_type_id = isset($_GET['tx_type']) ? $_GET['tx_type'] : "";

		$results = $this->get_group_members($group_id, $tx_type_id);

		$is_type = 1; // fixed default

		// get first index
		if (isset($results[0])) {
			$is_type = $results[0]['is_type'];
		}

		if ($_POST) {
			if (isset($_POST['search'])) {
				$tx_type_id = $this->input->post("tx-types");
				$results = $this->get_group_members($group_id, $tx_type_id);

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully selected transaction type!'));
				redirect($this->_data['form_url'] . "?tx_type={$tx_type_id}");
			}

			if (isset($_POST['save'])) {
				$tx_type_id = $this->input->post('tx-type');
				$is_type = $this->input->post('income-type');

				if ($tx_type_id == "") {
					$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Please select transaction type!'));
					redirect($this->_data['form_url']);
				}

				$row_tx_type = $this->transaction_types->get_datum(
					'',
					array(
						'transaction_type_id' => $tx_type_id
					)
				)->row();

				if ($row_tx_type == "") {
					$this->session->set_flashdata('notification', $this->generate_notification('danger', 'Invalid transaction type!'));
					redirect($this->_data['form_url']);
				}

				foreach($results as $datum) {
					$id = $datum["id"];

					$row = $this->income_shares->get_datum(
						'',
						array(
							'oauth_bridge_id'		=> $datum['id'],
							'transaction_type_id'	=> $tx_type_id
						)
					)->row();
					
					if ($row == "") {
						$is_id = $this->generate_code(
							$datum,
							"crc32"
						);

						// not exist then save
						$this->income_shares->insert(
							array(
								'is_id'					=> $is_id,
								'is_date_added'			=> $this->_today,
								'oauth_bridge_id'		=> $id,
								'ig_id'					=> $group_id,
								'transaction_type_id'	=> $tx_type_id,
								'is_amount'				=> isset($_POST[$id]) ? $_POST[$id] : "0",
								'is_type'				=> $is_type
							)
						);
					} else {
						// do update
						$this->income_shares->update(
							$row->is_id,
							array(
								'is_date_added'			=> $this->_today,
								'is_amount'				=> isset($_POST[$id]) ? $_POST[$id] : "0",
								'is_type'				=> $is_type
							)
						);
					}
				}

				$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Saved!'));
				redirect($this->_data['form_url'] . "?tx_type={$tx_type_id}");
			}
		}

		$tx_types = $this->get_transaction_types();

		$this->_data['tx_types'] = $this->generate_selection(
			"tx-types", 
			$tx_types, 
			$tx_type_id, 
			"id", 
			"name", 
			false,
			"Select Transaction Type"
		);

		$this->_data['is_type']	= $is_type;
		$this->_data['tx_type']	= $tx_type_id;
		$this->_data['list']	= $this->generate_member_list($results);
		$this->_data['title']  	= "Income Share Management";
		$this->set_template("income_shares/form", $this->_data);
	}

	private function generate_member_list($data) {
		$HTML = "";

		foreach ($data as $datum) {
			$id				= $datum['id'];
			$name 			= $datum['name'];
			$email_address	= $datum['email_address'];
			$mobile_no		= $datum['mobile_no'];
			$amount			= $datum['amount'];

$HTML .= <<<HTML
<tr>
	<td>
	<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<input name="$id" class="form-control" placeholder="Income Amount" value="$amount">
				</div>
			</div>
		</div>
	</td>
	<td>$name</td>
	<td>$email_address</td>
	<td>$mobile_no</td>
</tr>
HTML;
		}

		return $HTML;
	}

	private function get_group_members($group_id, $tx_type_id) {
		$admin_account_data_results 	= $this->_admin_account_data['results'];
		$admin_oauth_bridge_id			= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->load->model("admin/income_shares_model", "income_shares");

		$data = $this->income_groups_members->get_data(
			array(
				'merchant_number as id',
				'merchant_fname as fname',
				'merchant_mname as mname',
				'merchant_lname as lname',
				'merchant_email_address as email_address',
				'merchant_mobile_no as mobile_no',
				'merchants.oauth_bridge_id as oauth_bridge_id'
			),
			array(
				'income_groups_members.ig_id'		=> $group_id
			),
			array(),
			array(
				array(
					'table_name' 	=> 'merchants',
					'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
				)
			),
			array(
				'filter'	=> 'igm_date_added',
				'sort'		=> 'DESC'
			)
		);

		$results = array();

		foreach ($data as $datum) {
			$is_type = 1;

			$row = $this->income_shares->get_datum(
				'',
				array(
					'oauth_bridge_id'		=> $datum['oauth_bridge_id'],
					'transaction_type_id'	=> $tx_type_id
				)
			)->row();

			$amount = "0";

			if ($row != "") {
				$amount 	= $row->is_amount;
				$is_type	= $row->is_type;
			}

			$results[] = array(
				'id'			=> $datum['oauth_bridge_id'],
				'name'			=> $datum['fname'] . " " . $datum['mname'] . " " . $datum['lname'],
				'email_address'	=> $datum['email_address'],
				'mobile_no'		=> $datum['mobile_no'],
				'amount'		=> $amount,
				'is_type'		=> $is_type
			);

			// if ($datum['oauth_bridge_id'] == $admin_oauth_bridge_id) {
			// 	$flag = true;
			// }
		}

		$amount = 0;
		
		$row = $this->income_shares->get_datum(
			'',
			array(
				'oauth_bridge_id'		=> $admin_oauth_bridge_id,
				'transaction_type_id'	=> $tx_type_id
			)
		)->row();

		if ($row != "") {
			$amount = $row->is_amount;
		}
		
		$results = array_merge(
			array(
				array(
					'id'			=> $admin_oauth_bridge_id,
					'name'			=> "BambuPAY",
					'email_address'	=> "",
					'mobile_no'		=> "",
					'amount'		=> $amount,
					'is_type'		=> $is_type
				)
			),
			$results
		);

		return $results;
	}

	private function get_transaction_types() {
		$this->load->model('admin/transaction_types_model', 'tx_types');

		$results = $this->tx_types->get_data(
			array(
				'transaction_type_id as id',
				'transaction_type_name as "name"'
			),
			array(
				'transaction_type_status'	=> 1
			),
			array(),
			array(),
			array(
				'filter'	=> 'transaction_type_group_id',
				'sort'		=> 'DESC'
			)
		);

		return $results;
	}
}
