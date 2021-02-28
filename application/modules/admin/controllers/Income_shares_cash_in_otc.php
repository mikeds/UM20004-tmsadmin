<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_shares_cash_in_otc extends Admin_Controller {
	private
		$_admin_account_data = NULL,
		$_transaction_type_id = "cash_in_otc";
		
	public function after_init() {
		$this->set_scripts_and_styles();
		$this->load->model('admin/oauth_bridges_model', 'bridges');
		$this->load->model('admin/merchants_model', 'merchants');
		$this->load->model('admin/income_groups_model', 'income_groups');
		$this->load->model('admin/income_groups_members_model', 'income_groups_members');
		$this->load->model('admin/tms_admins_model', 'admins');

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];
		
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['add_label']		= "New Group";
		$this->_data['add_url']	 		= base_url() . "income-groups-cash-in-otc/new";

		$actions = array(
			'update'
		);

		$select = array(
			'income_groups.ig_id as id',
			'income_groups.ig_id as "Group ID"',
			'CONCAT(merchant_lname, ", ", merchant_fname) as "Parent Name"',
			'ig_date_added as "Date Added"'
		);

		$where = array(
			'transaction_type_id'	=> $this->_transaction_type_id
		);

		$inner_joints = array(
			array(
				'table_name' 	=> 'income_groups_members',
				'condition'		=> 'income_groups_members.igm_id = income_groups.igm_leader_id'
			),
			array(
				'table_name' 	=> 'merchants',
				'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
			)
		);

		$total_rows = $this->income_groups->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
	    $results = $this->income_groups->get_data($select, $where, array(), $inner_joints, array('filter'=>'ig_date_added', 'sort'=>'ASC'), $offset, $this->_limit);

		$this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);

		$this->_data['title']  = "Cash In (OTC) - Income Shares";
		$this->set_template("income_shares/list", $this->_data);
	}

	public function update($group_id) {
		$this->_data['notification'] 	= $this->session->flashdata('notification');
		$this->_data['form_url']		= base_url() . "income-shares-cash-in-otc/update/{$group_id}";

		$row_group = $this->income_groups->get_datum(
			'',
			array(
				'ig_id'					=> $group_id,
				'transaction_type_id'	=> $this->_transaction_type_id
			)
		)->row();

		if ($row_group == "") {
			// INVALID GROUP
			redirect(base_url());
		}

		$ig_mode = $row_group->ig_mode;

		$data = $this->get_group_members($group_id);

		if ($_POST) {
			$ig_mode = $this->input->post('income-mode');

			$this->income_groups->update(
				$group_id,
				array(
					'ig_mode'	=> $ig_mode == "1" ? "1" : "2"
				)
			);
			
			foreach($data as $datum) {
				$id 		= $datum['id'];
				$id_limit	= "{$id}-limit";

				$amount = isset($_POST[$id]) ? $_POST[$id] : "0";
				$limit	= isset($_POST[$id_limit]) ? $_POST[$id_limit] : "";

				$this->income_groups_members->update(
					$id,
					array(
						'igm_fees_amount'	=> $amount,
						'igm_fees_limit'	=> $limit
					)
				);
			}

			$this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Saved!'));
			redirect($this->_data['form_url']);
		}
		
		$this->_data['ig_mode']	= $ig_mode;
		$this->_data['list']	= $this->generate_member_list($data);
		$this->_data['title']  	= "Cash In (OTC) - Income Shares - Update";
		$this->set_template("income_shares/form", $this->_data);
	}

	private function get_group_members($group_id) {
		$results = array();

		$data = $this->income_groups_members->get_data(
			array(
				'*'
			), 
			array(
				'ig_id'	=> $group_id
			), 
			array(), 
			array(), 
			array('filter'=>'igm_id', 'sort'=>'ASC')
		);

		foreach ($data as $datum) {
			$id				= $datum['igm_id'];
			$name			= "";
			$email_address	= "";
			$mobile_no		= "";
			$amount 		= $datum['igm_fees_amount'];
			$limit			= $datum['igm_fees_limit'];

			$row = $this->income_groups_members->get_datum(
				'',
				array(
					'income_groups_members.oauth_bridge_id'		=> $datum['oauth_bridge_id']
				),
				array(),
				array(
					array(
						'table_name' 	=> 'merchants',
						'condition'		=> 'merchants.oauth_bridge_id = income_groups_members.oauth_bridge_id'
					)
				)
			)->row();

			if ($row == "") {
				$row = $this->admins->get_datum(
					'',
					array(
						'oauth_bridge_id' => $datum['oauth_bridge_id']
					)
				)->row();

				$name = $row->tms_admin_name;
			} else {
				$name 			= "{$row->merchant_lname}, {$row->merchant_fname}";
				$email_address	= $row->merchant_email_address;
				$mobile_no		= $row->merchant_mobile_no;
			}

			if ($row != "") {
				$results[] = array(
					'id'			=> $id,
					'name'			=> $name,
					'email_address'	=> $email_address,
					'mobile_no'		=> $mobile_no,
					'amount'		=> $amount,
					'limit'			=> $limit
				);
			}
		}

		return $results;
	}

	private function generate_member_list($data) {
		$HTML 			= "";
		$total_amount 	= 0;

		foreach ($data as $datum) {
			$id				= $datum['id'];
			$id_limit		= "{$id}-limit";
			$name 			= $datum['name'];
			$email_address	= $datum['email_address'];
			$mobile_no		= $datum['mobile_no'];
			$amount			= $datum['amount'];
			$limit			= $datum['limit'];

			if (is_numeric($amount)) {
				$total_amount += $amount;
			}

$HTML .= <<<HTML
<tr>
	<td>$name</td>
	<td>$email_address</td>
	<td>$mobile_no</td>
	<td>
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<input name="$id_limit" class="form-control" placeholder="Income Limit" value="$limit">
				</div>
			</div>
		</div>
	</td>
	<td>
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<input name="$id" class="form-control" placeholder="Income Amount" value="$amount">
				</div>
			</div>
		</div>
	</td>
</tr>
HTML;
		}

$HTML .= <<<HTML
<tr>
	<td colspan="4"><h3>Total Income Shares (FEES)</h3></td>
	<td><h3>$total_amount</h3></td>
</tr>
HTML;

		return $HTML;
	}
}
