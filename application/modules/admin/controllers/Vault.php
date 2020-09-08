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

        $fee = 0;
        $amount = 0;
        $total_amount = 0;

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

                $total_amount = $amount + $fee; // total amount

                // computation
                $wallet_data_results    = $wallet_data['results'];
                $wallet_balance         = $wallet_data_results['balance'];

                $old_balance            = $wallet_balance;
                $encryted_old_balance   = $this->encrypt_wallet_balance($old_balance);

                $new_balance            = $old_balance + $total_amount;
                $encryted_new_balance   = $this->encrypt_wallet_balance($new_balance);

                $wallet_data = array(
                    'wallet_balance'                => $encryted_new_balance,
                    'wallet_address_date_updated'   => $this->_today
                );

                // update wallet balances
                $this->wallet_addresses->update(
                    $wallet_address,
                    $wallet_data
                );

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

                // add new ledger data
                $ledger_data = array(
                    'tx_id'                         => $transaction_id,
                    'ledger_datum_desc'             => 'add_vault_balance',
                    'ledger_from_wallet_address'    => getenv("SYSADD"),
                    'ledger_to_wallet_address'      => $wallet_address,
                    'ledger_from_oauth_bridge_id'   => getenv("SYSADD"),
                    'ledger_to_oauth_bridge_id'     => $account_oauth_bridge_id,
                    'ledger_datum_old_balance'      => $old_balance,
                    'ledger_datum_new_balance'      => $new_balance,
                    'ledger_datum_amount'           => $total_amount,
                    'ledger_datum_date_added'       => $this->_today
                );

                $ledger_datum_id = $this->generate_code(
                    $ledger_data,
                    "crc32"
                );

                $ledger_data = array_merge(
                    $ledger_data,
                    array(
                        'ledger_datum_id'   => $ledger_datum_id,
                    )
                );

                $ledger_datum_checking_data = $this->generate_code($ledger_data);

                $this->ledger->insert(
                    array_merge(
                        $ledger_data,
                        array(
                            'ledger_datum_checking_data' => $ledger_datum_checking_data
                        )
                    )
                );

                $this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully added balance!'));
                redirect($this->_data['form_url']);
            }
        }

        $this->_data['title']  = "Add Balance";
		$this->set_template("vault/form", $this->_data);
    }
}


