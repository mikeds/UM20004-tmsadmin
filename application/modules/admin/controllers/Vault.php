<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vault extends Admin_Controller {
    private
        $_admin_account_data = NULL;

    public function after_init() {
        $this->set_scripts_and_styles();
        
        $this->load->model("admin/transactions_model", "transactions");
        $this->load->model("admin/ledger_data_model", "ledger");
        $this->load->model("admin/wallet_addresses_model", "wallet_addresses");

        $this->_admin_account_data = $this->get_account_data();
    }

	public function add() {
        $this->load->model("admin/tms_accounts_model", "accounts");

        $account_results            = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id		= $account_results['admin_oauth_bridge_id'];
        $account_oauth_bridge_id    = $account_results['oauth_bridge_id'];
        $wallet_address             = $account_results['wallet_address'];

        $this->_data['form_url']		= base_url() . "vault";
		$this->_data['notification'] 	= $this->session->flashdata('notification');
            
        $transaction_type_id = "TXTYPE_100101"; // vault

        if ($_POST) {
            if ($this->form_validation->run('validate')) {
                $amount     = $this->input->post("amount");
                $password   = $this->input->post("password");

                if ($amount < 1000) {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Amount must be greater than to 1,000!'));
                    redirect($this->_data['form_url']);
                }

                if ($amount > 500000) {
                    $this->session->set_flashdata('notification', $this->generate_notification('danger', 'Amount is not more than 500,000 limit per add balance!'));
                    redirect($this->_data['form_url']);
                }

                if (is_decimal($amount)) {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Separator and/or centavo values not allowed. Please remove comma or dot/ decimal/s to continue.'));
                    redirect($this->_data['form_url']);                   
                }

                $wallet_data = $this->get_wallet_data($wallet_address);
                
                if (!$wallet_data['status']) {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Error fetching account balance!'));
                    redirect($this->_data['form_url']);
                }

                $account_row = $this->accounts->get_datum(
                    '',
                    array(
                        'oauth_bridge_id'   => $account_oauth_bridge_id,
                        'account_password'  => hash("sha256", $password)
                    )
                )->row();

                if ($account_row == "") {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Incorrect Password!'));
                    redirect($this->_data['form_url']);
                }

                // add new transaction
                $transaction_id = $this->generate_code(
                    array(
                        'transaction_amount' 		=> $amount,
                        'transaction_fee'		    => $fee,
                        'transaction_type_id'       => $transaction_type_id,
                        'transaction_requested_by'  => $account_oauth_bridge_id,
                        'transaction_requested_to'	=> $admin_oauth_bridge_id,
                        'transaction_date_created'  => $this->_today
                    ),
                    "crc32"
                );

                $total_amount = $amount;
                $fee = 0;

                $this->transactions->insert(
                    array(
                        'transaction_id'            => $transaction_id,
                        'transaction_fee'           => $fee,
                        'transaction_amount'        => $amount,
                        'transaction_total_amount'  => $total_amount,
                        'transaction_type_id'       => $transaction_type_id,
                        'transaction_requested_by'  => $account_oauth_bridge_id,
                        'transaction_requested_to'  => getenv("SYSADD"),
                        'transaction_created_by'    => $account_oauth_bridge_id,
                        'transaction_date_created'  => $this->_today,
                        'transaction_date_approved' => $this->_today,
                        'transaction_status'        => 1 // approved
                    )
                );

				$receiver_amount 	    = $amount;
				$receiver_total_amount	= $receiver_amount;
                $receiver_wallet_address    = $account_results['wallet_address'];
                
                $receiver_new_balances = $this->update_wallet($receiver_wallet_address, $receiver_total_amount);
				if ($receiver_new_balances) {
					// record to ledger
					$this->new_ledger_datum(
						"top_up_otc_credit", 
						$transaction_id, 
						getenv("SYSADD"),
						$receiver_wallet_address,
						$receiver_new_balances
					);
				}

                $this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully added balance!'));
                redirect($this->_data['form_url']);
            }
        }

        $this->_data['title']  = "Add Balance";
		$this->set_template("vault/form", $this->_data);
    }
}


