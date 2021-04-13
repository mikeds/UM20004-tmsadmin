<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends Admin_Controller {

	public function after_init() {
        $this->set_scripts_and_styles();
		
		$this->load->model('admin/transactions_model', 'transactions');

		$this->_admin_account_data = $this->get_account_data();
    }

    public function index($page = 1) {
        $admin_account_data_results = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id		= $admin_account_data_results['admin_oauth_bridge_id'];
        
        $select = array(
            'IF(transaction_parent_id = "", transaction_id, CONCAT("(", transaction_id, ")<br/>",transaction_parent_id)) as transaction_id',
            'CONCAT("'. base_url() . "qr-code/transactions/" .'", transaction_sender_ref_id) as qr_code',
            'transaction_sender_ref_id',
            'transaction_amount',
            'transaction_fee',
            'transaction_type_name',
            'transaction_type_code',
            'transaction_type_group_id',
            'transaction_status',
            'transaction_date_expiration',
            'transaction_date_created',
            'transaction_requested_by',
            'transaction_requested_to',
            'transaction_created_by',
            'transaction_message'
        );

        $where = array(
            'transactions.transaction_type_id !=' => "txtype_income_shares"
        );
        
		$inner_joints = array(
			array(
				'table_name'	=> 'transaction_types',
				'condition'		=> 'transaction_types.transaction_type_id = transactions.transaction_type_id'
			)
		);
		$total_rows = $this->transactions->get_count(
			$where,
			array(),
			$inner_joints
		);
		$offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);

		$results = $this->transactions->get_data(
			$select,
			$where,
			array(),
			$inner_joints,
			array(
				'filter'	=> 'transaction_date_micro',
				'sort'		=> 'DESC'
			)
        );
        
        $data = $this->filter_tx($results);

        $this->_data['listing'] = $this->table_listing('', $data, $total_rows, $offset, $this->_limit, array(), 2);
		$this->_data['title']  = "Transactions";
		$this->set_template("tx/list", $this->_data);
    }

    /*
            'transaction_id',
            'CONCAT("'. base_url() . "qr-code/transactions/" .'", transaction_sender_ref_id) as qr_code',
            'transaction_sender_ref_id',
            'transaction_amount',
            'transaction_fee',
            'transaction_type_name',
            'transaction_type_code',
            'transaction_type_group_id',
            'transaction_status',
            'transaction_date_expiration',
            'transaction_date_created',
            'transaction_requested_by',
            'transaction_requested_to',
            'transaction_created_by',
            'transaction_message'
    */

    private function filter_tx($results) {
        $data = array();

        foreach($results as $row) {
            $from   = $row['transaction_requested_by'];
            $to     = $row['transaction_requested_to'];

            $tx_from_data  = $this->get_oauth_account_info($from);
            $tx_to_data    = $this->get_oauth_account_info($to);

            $tx_from    = "Admin";
            $tx_to      = "Admin";

            if ($tx_from_data) {
                $tx_from    = "({$tx_from_data['account_number']})<br>{$tx_from_data['account_fname']} {$tx_from_data['account_mname']} {$tx_from_data['account_lname']}";
            }

            if ($tx_to_data) {
                $tx_to      = "({$tx_to_data['account_number']})<br>{$tx_to_data['account_fname']} {$tx_to_data['account_mname']} {$tx_to_data['account_lname']}";
            }


            $status = $row['transaction_status'];

            $tx_status = "Cancelled";

            if ($status == "0") {
                $tx_status = "Pending";
            } else if ($status == "1") {
                $tx_status = "Approved";
            } else if ($status == "2") {
                $tx_status = "Cancelled";
            }

            $data[] = array(
                'id'                => $row['transaction_id'],
                'TX ID'             => $row['transaction_id'],
                'Ref No.'           => $row['transaction_sender_ref_id'],
                'TX By'             => $tx_from,
                'TX To'             => $tx_to,
                'Amount'            => $row['transaction_amount'],
                'Fee'               => $row['transaction_fee'],
                'TX Type'           => $row['transaction_type_name'],
                'TX Status'         => $tx_status,
                'TX Date Created'   => $row['transaction_date_created']
            );
        }

        return $data;
    }
}



        /*
        $query_1 = "(tx.transaction_requested_by = '{$account_oauth_bridge_id}')";
        $query_2 = "(tx.transaction_requested_to = '{$account_oauth_bridge_id}')";

        $select = ARRtoSTR($select);

$sql = <<<SQL
SELECT $select FROM `transactions` as tx 
inner join transaction_types
on transaction_types.transaction_type_id = tx.transaction_type_id
where 
$query_1
or 
$query_2
ORDER BY transaction_date_created DESC
LIMIT $this->_limit
SQL;
        
        $query = $this->db->query($sql);
        $results = $query->result_array();
        */