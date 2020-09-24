<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger_client extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();
		
		$this->load->model("admin/ledger_data_model", "ledger");
		$this->load->model("admin/client_accounts_model", "client_accounts");

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index() {
		$this->_data['form_url']		= base_url() . "ledger-client";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$redirect_url					= base_url() . "ledger-client/search";

		$clients = $this->get_clients();

		$this->_data['clients'] = $this->generate_selection(
			"client", 
			$clients, 
			"", 
			"id", 
			"name", 
			false,
			"Select Client"
		);

		if ($_POST) {
			if ($this->form_validation->run('validate')) {
				$account_number = $this->input->post("client");

				$query = "/{$account_number}";

				$redirect_url .= $query;

				redirect($redirect_url);
			}
		}

		$this->_data['title']  = "Ledger - Search Client";
		$this->set_template("ledger_client/form", $this->_data);
	}

	public function search($account_number, $page = 1) {
		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "ledger-client/search/{$account_number}";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$redirect_url					= "";

		$txid	= isset($_GET['txid']) ? ($_GET['txid'] ? $_GET['txid'] : "" ) : "";
		$refid	= isset($_GET['refid']) ? ($_GET['refid'] ? $_GET['refid'] : "" ) : "";

		$from 	= isset($_GET['from']) ? ($_GET['from'] ? $_GET['from'] : "" ) : "";
		$to		= isset($_GET['to']) ? ($_GET['to'] ? $_GET['to'] : "" ) : "";

		$txamount = isset($_GET['txamount']) ? (is_numeric($_GET['txamount']) ? $_GET['txamount'] : "" ) : "";

		$sort	= isset($_GET['sort']) ? ($_GET['sort'] ? $_GET['sort'] : "ASC" ) : "ASC";
		$sort	= $sort == "ASC" ? $sort : "DESC";

		$post = array();

		if ($_POST) {
			$txid	= $this->input->post('tx-id');
			$refid	= $this->input->post('ref-id');
	
			$from 	= $this->input->post('from');
			$to		= $this->input->post('to');
	
			$txamount = $this->input->post('amount');
	
			$sort	= $this->input->post('sort');
			$sort	= $sort == "ASC" ? $sort : "DESC";
		}

		$this->_data['sort'] = $this->generate_selection(
			"sort", 
			array(
				array(
					'id'	=> 'ASC',
					'name'	=> 'Ascending'
				),
				array(
					'id'	=> 'DESC',
					'name'	=> 'Decending'
				)
			), 
			$sort, 
			"id", 
			"name", 
			true
		);

		$row = $this->client_accounts->get_datum(
			'',
			array(
				'account_number'			=> $account_number,
				'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id
			),
			array(),
			array(
				array(
					'table_name'	=> 'oauth_bridges',
					'condition'		=> 'oauth_bridges.oauth_bridge_id = client_accounts.oauth_bridge_id'
				)
			)
		)->row();

		if ($row == "") {
			redirect(base_url() . "ledger-client");
		}

		$client_name = trim("{$row->account_fname} {$row->account_mname} {$row->account_lname}");

		$client_oauth_bridge_id = $row->oauth_bridge_id;

		$where = array(
			'ledger_datum_bridge_id'	=> $client_oauth_bridge_id
		);

		if ($from != "") {
			if ($from != "" && $to == "" && validate_date($from)) {
				$where = array_merge(
					$where,
					array(
						'DATE(ledger_datum_date_added) >=' => $from
					)
				);

				$post = array_merge(
					$post,
					array(
						'from'	=> $from
					)
				);

				$redirect_url = $redirect_url == "" ? "?from={$from}" : "";
			} else if ($from != "" && $to != "" && validate_date($from) && validate_date($to) && (strtotime($from) <= strtotime($to))) {
				$where = array_merge(
					$where,
					array(
						'DATE(ledger_datum_date_added) >=' => $from,
						'DATE(ledger_datum_date_added) <=' => $to,
					)
				);

				$post = array_merge(
					$post,
					array(
						'from'	=> $from,
						'to'	=> $to,
					)
				);

				$redirect_url = $redirect_url == "" ? "?from={$from}&to={$to}" : "";
			}
		}

		if ($txid != "") {
			$where = array_merge(
				$where,
				array(
					'tx_id'	=> $txid
				)
			);

			$post = array_merge(
				$post,
				array(
					'tx-id'	=> $txid
				)
			);

			$redirect_url = $redirect_url == "" ? "?txid={$txid}" : $redirect_url . "&txid={$txid}";
		}

		if ($refid != "") {
			$where = array_merge(
				$where,
				array(
					'transaction_sender_ref_id'	=> $refid
				)
			);

			$post = array_merge(
				$post,
				array(
					'ref-id'	=> $refid
				)
			);

			$redirect_url = $redirect_url == "" ? "?refid={$refid}" : $redirect_url . "&refid={$refid}";
		}

		if ($txamount != "") {
			$where = array_merge(
				$where,
				array(
					'transaction_amount'	=> $txamount
				)
			);

			$post = array_merge(
				$post,
				array(
					'amount'	=> $txamount
				)
			);

			$redirect_url = $redirect_url == "" ? "?txamount={$txamount}" : $redirect_url . "&txamount={$txamount}";
		}

		$this->_data['post'] = $post;

		if ($_POST) {
			$redirect_url = $redirect_url == "" ? "?sort={$sort}" : $redirect_url . "&sort={$sort}";

			if ($redirect_url != "") {
				redirect($this->_data['form_url'] . $redirect_url);
			}
		}

        $select = array(
			'transaction_id as "TX ID"',
			'transaction_sender_ref_id as "Sender Ref ID"',
			'transaction_type_name as "TX Type"',
			'transaction_requested_by as "Requested By"',
			'FORMAT(transaction_amount, 2) as "TX Amount"',
			'FORMAT(transaction_fee, 2) as "Fee"',
			'FORMAT(ledger_datum_old_balance, 2) as "Old Balance"',
			'FORMAT(ledger_datum_amount, 2) as "Debit/Credit Amount"',
			'FORMAT(ledger_datum_new_balance, 2) as "New Balance"',
			'ledger_datum_date_added as "Date Added"'
		);

		$inner_joints = array(
			array(
				'table_name'	=> 'transactions',
				'condition'		=> 'transactions.transaction_id = ledger_data.tx_id'
			),
			array(
				'table_name'	=> 'transaction_types',
				'condition'		=> 'transaction_types.transaction_type_id = transactions.transaction_type_id'
			)
		);

		$total_rows = $this->ledger->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);

		$data = $this->ledger->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter'	=> 'ledger_datum_date_added',
				'sort'		=> $sort
            ),
            $offset,
            $this->_limit
		);
		
		$ledger_data = $this->filter_ledger($data);

		$this->_data['listing'] = $this->table_listing(
			'', 
			$ledger_data, 
			$total_rows, 
			$offset, 
			$this->_limit, 
			array(), 
			4,
			false,
			false,
			'',
			'',
			$this->_data['form_url']
		);
		$this->_data['title']  = "Client Ledger ({$client_name})";
		$this->set_template("ledger_client/list", $this->_data);
	}
}
