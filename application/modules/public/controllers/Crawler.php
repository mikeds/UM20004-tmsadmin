<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crawler extends Crawler_Controller {
	public function after_init() {}

	public function dragonpay() {
		$this->load->model("admin/transactions_dragonpay_model", "tx_dragonpay");

		$select = array(
			'*'
		);

		$where = array(
			'drp_status'	=> "PENDING"
		);

		$or_where = array(
			'drp_status'	=> "P"
		);

		$results = $this->tx_dragonpay->get_data_or_where(
			$select,
			$where,
			$or_where
		);

		$this->check_dragonpay_updates($results);
	}

	/*
		S Success
		F Failure
		P Pending
		U Unknown
		R Refund
		K Chargeback
		V Void
		A Authorized
	*/

	private function check_dragonpay_updates($results) {
		$this->load->model("admin/transactions_dragonpay_model", "tx_dragonpay");
		$this->load->model("admin/transactions_model", "transactions");

		$drp_base_url	= DRP_BASE_URL . "api/collect/v1/txnid/";

		$username = DRP_USERNAME;
		$password = DRP_PASSWORD;

		$items = array();

        // set up multiple curl request
        $mh = curl_multi_init();
		$handles = array();
		
		foreach($results as $row) {
			$drp_update_lookup_url = $drp_base_url . $row['tx_id'];

			$curl = curl_init();
			$handles[] = $curl;
			
			curl_setopt_array($curl, array(
				CURLOPT_URL => $drp_update_lookup_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_HEADER => 0,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
				CURLOPT_USERPWD	=> $username . ":" . $password
			));

			curl_multi_add_handle($mh, $curl);
		}

		// execute multi curl request
		$running = null;
		do {
			curl_multi_exec($mh, $running);
		} while ($running);

		// get multi curl content
		foreach($handles as $ch) {
			$result = curl_multi_getcontent($ch);

			$items[] = json_decode($result, true);
			
			curl_multi_remove_handle($mh, $ch);
			curl_close($ch);
		}

		foreach($items as $item) {
			if (!isset($item['RefNo']) || !isset($item['Status']) || !isset($item['TxnId'])) {
				continue;
			}

			// do update
			$tx_id 			= $item['TxnId'];
			$ref_no			= $item['RefNo'];
			$status 		= $item['Status'];
			$email			= isset($item['Email']) ? $item['Email'] : "";
			$proc_id		= isset($item['ProcId']) ? $item['ProcId'] : "";
			$settle_date	= isset($item['SettleDate']) ? date("Y-m-d H:i:s", strtotime($item['SettleDate'])) : "";
			$currency		= isset($item['Currency']) ? $item['Currency'] : "";
			$amount			= isset($item['Amount']) ? $item['Amount'] : "";
			$description	= isset($item['Description']) ? $item['Description']: "";

			$data_update = array(
				'drp_ref_no'		=> $ref_no,
				'drp_status'		=> $status,
				'drp_settle_date'	=> $settle_date,
				'drp_currency'		=> $currency,
				'drp_amount'		=> $amount,
				'drp_description'	=> $description,
				'drp_email'			=> $email,
				'drp_proc_id'		=> $proc_id
			);

			$inner_joints = array(
				array(
					'table_name'	=> 'transactions',
					'condition'		=> 'transactions.transaction_id = transactions_dragonpay.tx_id'
				)
			);

			$row = $this->tx_dragonpay->get_datum(
				'',
				array(
					'tx_id'	=> $tx_id
				),
				array(),
				$inner_joints
			)->row();

			if ($row == "") {
				continue;
			}

			$drp_id = $row->drp_id;

			$this->tx_dragonpay->update(
				$drp_id,
				$data_update
			);

			// success
			if ($status == "S") {
				// do credit debit
				$admin_oauth_bridge_id 	= $row->transaction_requested_to;
				$client_oauth_bridge_id	= $row->transaction_requested_by;

				$tx_amount 	= $row->transaction_amount;
				$tx_fee		= $row->transaction_fee;
				
				$total_amount 	= 0;

				$amount = $row->transaction_amount;
				$fee 	= $row->transaction_fee;

				$sender_amount		= $tx_amount  + $tx_fee;
				$receiver_amount 	= $tx_amount;
				$fee_amount			= $tx_fee;
				
				$sender_total_amount 	= 0 - $sender_amount; // make it negative
				$receiver_total_amount	= $receiver_amount;

				$sender_wallet_address		= $this->get_wallet_address($admin_oauth_bridge_id);
				$receiver_wallet_address	= $this->get_wallet_address($client_oauth_bridge_id);
				
				if ($sender_wallet_address == "" || $receiver_wallet_address == "") {
					continue;
				}

				$sender_new_balances = $this->update_wallet($sender_wallet_address, $sender_total_amount);
				if ($sender_new_balances) {
					// record to ledger
					$this->new_ledger_datum(
						"cash_in_drp_debit", 
						$tx_id, 
						$receiver_wallet_address, // from
						$sender_wallet_address, // to
						$sender_new_balances
					);
				}

				$receiver_new_balances = $this->update_wallet($receiver_wallet_address, $receiver_total_amount);
				if ($receiver_new_balances) {
					// record to ledger
					$this->new_ledger_datum(
						"cash_in_drp_credit", 
						$tx_id, 
						$sender_wallet_address,  // from
						$receiver_wallet_address, // to
						$receiver_new_balances
					);
				}

				$this->transactions->update(
					$tx_id,
					array(
						'transaction_status' 		=> 1,
						'transaction_date_approved'	=> $this->_today
					)
				);
			}
		}
	}
}

