<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settlement_report extends Admin_Controller {
	private
		$_admin_account_data = NULL;
		
	public function after_init() {
		$this->set_scripts_and_styles();

		$this->load->model("admin/merchants_model", "merchants");
		$this->load->model("admin/ledger_data_model", "ledger");

		$this->_admin_account_data = $this->get_account_data();
	}

	public function index($page = 1) {
		/*
			- get ledger
			- list of merchants
			- search by date
			- downloadable to excel no pagination
			- if no date from to get latest for a month and last month
		*/

		$admin_account_data_results = $this->_admin_account_data['results'];
		$admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];

		$this->_data['form_url']		= base_url() . "settlement-report";
		$this->_data['notification'] 	= $this->session->flashdata('notification');

		$redirect_url					= "";

		$merchant_number	= isset($_GET['accno']) ? ($_GET['accno'] ? $_GET['accno'] : "" ) : "";

		$from 	= isset($_GET['from']) ? ($_GET['from'] ? $_GET['from'] : "" ) : "";
		$to		= isset($_GET['to']) ? ($_GET['to'] ? $_GET['to'] : "" ) : "";

		$sort	= isset($_GET['sort']) ? ($_GET['sort'] ? $_GET['sort'] : "ASC" ) : "ASC";
		$sort	= $sort == "ASC" ? $sort : "DESC";

		$post = array();
		$where = array();

		if ($_POST) {
			$merchant_number = $this->input->post("merchant");
	
			$from 	= $this->input->post('from');
			$to		= $this->input->post('to');
	
			$sort	= $this->input->post('sort');
			$sort	= $sort == "ASC" ? $sort : "DESC";
		}

		if ($merchant_number != "") {
			$row = $this->merchants->get_datum(
				'',
				array(
					'merchant_number'			=> $merchant_number,
					'oauth_bridge_parent_id'	=> $admin_oauth_bridge_id
				),
				array(),
				array(
					array(
						'table_name'	=> 'oauth_bridges',
						'condition'		=> 'oauth_bridges.oauth_bridge_id = merchants.oauth_bridge_id'
					)
				)
			)->row();
			
			if ($row != "") {
				$merchant_name = trim("{$row->merchant_fname} {$row->merchant_mname} {$row->merchant_lname}");

				$merchant_oauth_bridge_id = $row->oauth_bridge_id;
				
				$where = array(
					'ledger_datum_bridge_id'	=> $merchant_oauth_bridge_id
				);

				$redirect_url = $redirect_url == "" ? "?accno={$merchant_number}" : "";
			}
		}

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

				$redirect_url = $redirect_url == "" ? "?from={$from}&to={$to}" : "&from={$from}&to={$to}";
			}
		}
		
		$where = array_merge(
			$where,
			array(
				'transactions.transaction_type_id' => 'txtype_income_shares'
			)
		);

		$this->_data['post'] = $post;

		if ($_POST) {
			$redirect_url = $redirect_url == "" ? "?sort={$sort}" : $redirect_url . "&sort={$sort}";
			
			if ($redirect_url != "") {
				redirect($this->_data['form_url'] . $redirect_url);
			}
		}

        $select = array(
			'parent_tx.transaction_id as "TX ID"',
			'CONCAT(merchant_fname, " ", merchant_mname, " ", merchant_lname) as "Merchant Name"',
			'FORMAT(parent_tx.transaction_amount, 2) as "Amount"',
			'FORMAT(parent_tx.transaction_fee, 2) as "Fee"',
			'FORMAT((parent_tx.transaction_amount + parent_tx.transaction_fee), 2) as "Total Amount"',
			'FORMAT(ledger_datum_amount, 2) as "Income Share"',
			'parent_tx.transaction_date_created as "Date Created"'
		);

		$inner_joints = array(
			array(
				'table_name'	=> 'transactions',
				'condition'		=> 'transactions.transaction_id = ledger_data.tx_id'
			),
			array(
				'table_name'	=> 'transactions as parent_tx',
				'condition'		=> 'parent_tx.transaction_id = transactions.transaction_parent_id'
			),
			array(
				'table_name'	=> 'transaction_types',
				'condition'		=> 'transaction_types.transaction_type_id = parent_tx.transaction_type_id'
			),
			array(
				'table_name'	=> 'merchants',
				'condition'		=> 'merchants.oauth_bridge_id = ledger_data.ledger_datum_bridge_id'
			)
		);

		$total_rows = $this->ledger->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);

		$results = $this->ledger->get_data(
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

		$this->_data['listing'] = $this->table_listing(
			'', 
			$results, 
			$total_rows, 
			$offset, 
			$this->_limit, 
			array(), 
			2,
			false,
			false,
			'',
			'',
			$this->_data['form_url']
		);

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

		$merchants = $this->get_merchants();

		$this->_data['merchants'] = $this->generate_selection(
			"merchant", 
			$merchants, 
			$merchant_number, 
			"id", 
			"name", 
			false,
			"Select Merchant"
		);

		$this->_data['title']  		= "Settlement Report";
		$this->set_template("settlement_report/list", $this->_data);
	}

	private function filter_report($results) {

	}
}
