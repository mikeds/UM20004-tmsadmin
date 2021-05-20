<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agents extends Admin_Controller {
    private
        $_admin_account_data = NULL;

    private
        $_gender = array(
            array(
                'id'    => 1,
                'name'  => "Male"
            ),
            array(
                'id'    => 2,
                'name'  => "Female"
            )
        );
        
    public function after_init() {
        $this->set_scripts_and_styles();
        $this->load->model('admin/merchants_model', 'merchants');
        $this->load->model('admin/oauth_bridges_model', 'bridges');
        $this->load->model('admin/countries_model', 'countries');
        $this->load->model('admin/provinces_model', 'provinces');
        $this->load->model('admin/wallet_addresses_model', 'wallet_addresses');
        $this->load->model('admin/merchant_accounts_model', 'merchant_accounts');
        $this->load->model('admin/source_of_funds_model', 'source_funds');
        $this->load->model('admin/nature_of_work_model', 'nature_of_work');
        $this->load->model('admin/id_types_model', 'id_types');
        $this->load->model('admin/agent_accounts_model', 'agent_accounts');
        $this->load->model('admin/client_accounts_model', 'client_accounts');

        $this->_admin_account_data = $this->get_account_data();
    }

    public function index($page = 1) {
        $admin_account_data_results = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id      = $admin_account_data_results['admin_oauth_bridge_id'];
        
        $this->_data['add_label']= "New Agent";
        $this->_data['add_url']  = base_url() . "agents/new";

        $actions = array(
            'update'
        );

        $select = array(
            'account_number as id',
            'account_status as "Status"',
            'account_fname as "First Name"',
            'account_mname as "Middle Name"',
            'account_lname as "Last Name"',
            'account_mobile_no as "Mobile No."',
            'account_email_address as "Email Address"',
            'IF(account_gender = 2, "Female", "Male") as "Gender"',
            'account_dob as "Date of Birth"',
            'account_house_no as "House No./ Unit No. / Building"',
            'account_street as "Street"',
            'account_brgy as "Barangay"',
            'account_city as "City"',
            'province_name as "Province"',
            'country_name as "Country"',
        );

        $where = array(
            'oauth_bridge_parent_id'    => $admin_oauth_bridge_id,
            'account_status'            => 1,
            'account_role'              => 2 // agent
        );

        $inner_joints = array(
            array(
                'table_name'    => 'oauth_bridges',
                'condition'     => 'oauth_bridges.oauth_bridge_id = client_accounts.oauth_bridge_id'
            ),
            array(
                'table_name'    => 'tms_admins',
                'condition'     => 'tms_admins.oauth_bridge_id = oauth_bridges.oauth_bridge_parent_id'
            ),
            array(
                'table_name'    => 'countries',
                'condition'     => 'countries.country_id = client_accounts.country_id',
                'type'          => 'left'
            ),
            array(
                'table_name'    => 'provinces',
                'condition'     => 'provinces.province_id = client_accounts.province_id',
                'type'          => 'left'
            )
        );

        $total_rows = $this->client_accounts->get_count(
            $where,
            array(),
            $inner_joints
        );
        $offset = $this->get_pagination_offset($page, $this->_limit, $total_rows);
        $results = $this->client_accounts->get_data($select, $where, array(), $inner_joints, array('filter'=>'account_date_added', 'sort'=>'DESC'), $offset, $this->_limit);

        $this->_data['listing'] = $this->table_listing('', $results, $total_rows, $offset, $this->_limit, $actions, 2);
        $this->_data['title']  = "Agent List";
        $this->set_template("agents/list", $this->_data);
    }

    public function new() {
        $this->_data['form_url']        = base_url() . "agents/new";
        $this->_data['notification']    = $this->session->flashdata('notification');

        $admin_account_data_results = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id      = $admin_account_data_results['admin_oauth_bridge_id'];

        $country_id = 169; // PH

        $countries = $this->countries->get_data(
            array(
                'country_id as id',
                'country_name as name'
            ),
            array(
                'country_status' => 1
            ),
            array(),
            array(),
            array(
                'filter'    => "country_name",
                'sort'      => "ASC"
            )
        );

        $provinces = $this->provinces->get_data(
            array(
                'province_id as id',
                'province_name as name'
            ),
            array(
                'country_id'        => $country_id,
                'province_status'   => 1
            ),
            array(),
            array(),
            array(
                'filter'    => "province_name",
                'sort'      => "ASC"
            )
        );

        if ($_POST) {
            if ($this->form_validation->run('validate')) {
                $merchant_code  = $this->input->post("merchant-code");
                $fname          = $this->input->post("first-name");
                $mname          = $this->input->post("middle-name");
                $lname          = $this->input->post("last-name");
                $gender         = $this->input->post("gender");
                $dob            = $this->input->post("dob");
                $house_no       = $this->input->post("house-no");
                $street         = $this->input->post("street");
                $brgy           = $this->input->post("brgy");
                $city           = $this->input->post("city");
                // $country_id      = $this->input->post("country");
                $province_id    = $this->input->post("province");
                $mobile_no      = $this->input->post("mobile-no");
                $contact_no     = $this->input->post("contact-no");
                $email_address  = $this->input->post("email-address");

                $repeat_password    = $this->input->post("repeat-password");
                $password           = $this->input->post("password");

                $row_m_email_address = $this->merchants->get_datum(
                    '',
                    array(
                        'merchant_email_address'    => $email_address
                    )
                )->row();

                if ($row_m_email_address != "") {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Email address is already used!'));
                    redirect($this->_data['form_url']);
                }

                $row_ma_email_address = $this->merchant_accounts->get_datum(
                    '',
                    array(
                        'account_username'  => $email_address
                    )
                )->row();

                if ($row_ma_email_address != "") {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Email address is already used!'));
                    redirect($this->_data['form_url']);
                }

                if ($password == "" || $repeat_password == "") {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Please fill-up password fields!'));
                    redirect($this->_data['form_url']);
                }

                if ($password != $repeat_password) {
                    $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Password not the same!'));
                    redirect($this->_data['form_url']);
                }

                $merchant_number = $this->generate_code(
                    array(
                        "merchant",
                        $admin_oauth_bridge_id,
                        $this->_today
                    ),
                    "crc32"
                );

                $bridge_id = $this->generate_code(
                    array(
                        'merchant_number'       => $merchant_number,
                        'merchant_date_added'   => $this->_today,
                        'admin_oauth_bridge_id' => $admin_oauth_bridge_id
                    )
                );

                // do insert bridge id
                $this->bridges->insert(
                    array(
                        'oauth_bridge_id'           => $bridge_id,
                        'oauth_bridge_parent_id'    => $admin_oauth_bridge_id,
                        'oauth_bridge_date_added'   => $this->_today
                    )
                );

                $ref_code = substr(number_format(strtotime($this->_today) * rand(),0,'',''),0,6);
                $ref_code = substr($fname, 0, 1) . substr($lname, 0, 1) . $ref_code;

                $insert_data = array(
                    'merchant_ref_code'         => strtolower($ref_code),
                    'merchant_number'           => $merchant_number,
                    // 'merchant_code'              => $merchant_code,
                    'merchant_fname'            => $fname,
                    'merchant_mname'            => $mname,
                    'merchant_lname'            => $lname,
                    'merchant_gender'           => $gender,
                    'merchant_dob'              => $dob,
                    'merchant_house_no'         => $house_no,
                    'merchant_street'           => $street,
                    'merchant_brgy'             => $brgy,
                    'merchant_city'             => $city,
                    'country_id'                => $country_id,
                    'province_id'               => $province_id,
                    'merchant_mobile_no'        => $mobile_no,
                    'merchant_email_address'    => $email_address,
                    'merchant_date_created'     => $this->_today,
                    'merchant_status'           => 1, // activated
                    'oauth_bridge_id'           => $bridge_id,
                    'merchant_role'             => 2 // agents
                );

                $this->merchants->insert(
                    $insert_data
                );

                // create wallet address
                $this->create_wallet_address($merchant_number, $bridge_id, $admin_oauth_bridge_id);

                // // create token auth for api
                // $this->create_token_auth($merchant_number, $bridge_id);

                // create account
                $password = hash("sha256", $password);
                $account_number = $this->generate_code(
                    array(
                        "agent_account",
                        $admin_oauth_bridge_id,
                        $this->_today
                    ),
                    "crc32"
                );

                $bridge_id = $this->generate_code(
                    array(
                        'account_number'            => $account_number,
                        'account_date_added'        => $this->_today,
                        'oauth_bridge_parent_id'    => $admin_oauth_bridge_id
                    )
                );

                // do insert bridge id
                $this->bridges->insert(
                    array(
                        'oauth_bridge_id'           => $bridge_id,
                        'oauth_bridge_parent_id'    => $admin_oauth_bridge_id,
                        'oauth_bridge_date_added'   => $this->_today
                    )
                );

                $insert_data = array(
                    'merchant_number'       => $merchant_number,
                    'account_number'        => $account_number,
                    'account_fname'         => $fname,
                    'account_mname'         => $mname,
                    'account_lname'         => $lname,
                    'account_username'      => $email_address,
                    'account_password'      => $password,
                    'account_date_added'    => $this->_today,
                    'oauth_bridge_id'       => $bridge_id,
                );

                $this->merchant_accounts->insert(
                    $insert_data
                );

                $this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Added!'));
                redirect($this->_data['form_url']);
            }
        }

        $this->_data['gender'] = $this->generate_selection(
            "gender", 
            $this->_gender, 
            1, 
            "id", 
            "name", 
            true
        );

        $this->_data['country'] = $this->generate_selection(
            "country", 
            $countries, 
            $country_id, 
            "id", 
            "name", 
            true
        );
        
        $this->_data['province']    = $this->generate_selection(
            "province", 
            $provinces, 
            "", 
            "id", 
            "name", 
            false,
            "Please Select Province"
        );

        $this->_data['title']  = "New Agent";
        $this->set_template("agents/form", $this->_data);
    }

    public function update($account_number) {
        $admin_account_data_results = $this->_admin_account_data['results'];
        $admin_oauth_bridge_id      = $admin_account_data_results['admin_oauth_bridge_id'];

        $this->_data['is_update']       = true;
        $this->_data['form_url']        = base_url() . "agents/update/{$account_number}";
        $this->_data['notification']    = $this->session->flashdata('notification');

        $country_id = 169; // PH

        $row = $this->client_accounts->get_datum(
            '',
            array(
                'oauth_bridge_parent_id'    => $admin_oauth_bridge_id,
                'account_number'           => $account_number,
            ),
            array(),
            array(
                array(
                    'table_name'    => 'oauth_bridges',
                    'condition'     => 'oauth_bridges.oauth_bridge_id = client_accounts.oauth_bridge_id'
                ),
                array(
                    'table_name'    => 'tms_admins',
                    'condition'     => 'tms_admins.oauth_bridge_id = oauth_bridges.oauth_bridge_parent_id'
                ),
            )
        )->row();

        if ($row == "") {
            redirect(base_url() . "agents");
        }


        $country_id         = $row->country_id;
        $province_id        = $row->province_id;
        $gender_id          = $row->account_gender;
        $sof_id             = $row->sof_id;
        $now_id             = $row->now_id;
        $id_type_id         = $row->account_id_type;

        $countries = $this->countries->get_data(
            array(
                'country_id as id',
                'country_name as name'
            ),
            array(
                'country_status'    => 1
            ),
            array(),
            array(),
            array(
                'filter'    => "country_name",
                'sort'      => "ASC"
            )
        );

        $provinces = $this->provinces->get_data(
            array(
                'province_id as id',
                'province_name as name'
            ),
            array(
                'country_id'        => $country_id,
                'province_status'   => 1
            ),
            array(),
            array(),
            array(
                'filter'    => "province_name",
                'sort'      => "ASC"
            )
        );

        $sof = $this->source_funds->get_data(
            array(
                'sof_id as id',
                'sof_name as name'
            ),
            array(
                'sof_status'    => 1
            ),
            array(),
            array(),
            array(
                'filter'    => "sof_name",
                'sort'      => "ASC"
            )
        );

        $now = $this->nature_of_work->get_data(
            array(
                'now_id as id',
                'now_name as name'
            ),
            array(
                'now_status'    => 1
            ),
            array(),
            array(),
            array(
                'filter'    => "now_name",
                'sort'      => "ASC"
            )
        );

        $id_type = $this->id_types->get_data(
            array(
                'id_type_id as id',
                'id_type_name as name'
            ),
            array(
                'id_type_status'    => 1
            ),
            array(),
            array(),
            array(
                'filter'    => "id_type_name",
                'sort'      => "ASC"
            )
        );

        $this->_data['post'] = array(
            'first-name'    => $row->account_fname,
            'middle-name'   => $row->account_mname,
            'last-name'     => $row->account_lname,
            'dob'           => $row->account_dob,
            'house-no'      => $row->account_house_no,
            'street'        => $row->account_street,
            'brgy'          => $row->account_brgy,
            'city'          => $row->account_city,
            'mobile-no'     => $row->account_mobile_no,
            'email-address' => $row->account_email_address,
            'status'        => $row->account_status == 1 ? "checked" : "",
            'id-exp-date'   => $row->account_id_exp_date,
            'id-no'         => $row->account_id_no,
			'postal-code'	=> $row->account_postal_code
        );

        if ($_POST) {
            if ($this->form_validation->run('validate')) {
                $fname          = $this->input->post("first-name");
                $mname          = $this->input->post("middle-name");
                $lname          = $this->input->post("last-name");
                $gender         = $this->input->post("gender");
                $dob            = $this->input->post("dob");
                $house_no       = $this->input->post("house-no");
                $street         = $this->input->post("street");
                $brgy           = $this->input->post("brgy");
                $city           = $this->input->post("city");
                $country_id     = 169;
                $province_id    = $this->input->post("province");
                $mobile_no      = $this->input->post("mobile-no");
                $contact_no     = $this->input->post("contact-no");
                $email_address  = $this->input->post("email-address");
                $status         = $this->input->post("status");

                $repeat_password    = $this->input->post("repeat-password");
                $password           = $this->input->post("password");

                $sof                = $this->input->post("sof");
                $now                = $this->input->post("now");
                $id_type            = $this->input->post("id_type");
                $id_exp_date        = $this->input->post("id-exp-date");
                $id_no              = $this->input->post("id-no");
                $postal_code        = $this->input->post("postal-code");

                $row_account = $this->client_accounts->get_datum(
                    '', 
                    array(
                        'account_number'   => $account_number
                    )
                )->row();

                // if ($row_account != "") {
                //     if ($this->validate_username("merchant", $username, $row_account->account_number)) {
                //         $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Username Already Exist!'));
                //         redirect($this->_data['form_url']);
                //     }   
                // } else {
                //     if ($this->validate_username("merchant", $username)) {
                //         $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Username Already Exist!'));
                //         redirect($this->_data['form_url']);
                //     }
                // }

                if ($password != "" || $repeat_password != "") {
                    if ($password != $repeat_password) {
                        $this->session->set_flashdata('notification', $this->generate_notification('warning', 'Password not the same!'));
                        redirect($this->_data['form_url']);
                    }
                }

                $update_data = array(
                    'account_fname'             => $fname,
                    'account_mname'             => $mname,
                    'account_lname'             => $lname,
                    'account_gender'            => $gender,
                    'account_dob'               => $dob,
                    'account_house_no'          => $house_no,
                    'account_street'            => $street,
                    'account_brgy'              => $brgy,
                    'account_city'              => $city,
                    'country_id'                => $country_id,
                    'province_id'               => $province_id,
                    'account_mobile_no'         => $mobile_no,
                    'account_email_address'     => $email_address,
                    'account_postal_code'       => $postal_code,
                    'sof_id'                    => $sof,
                    'now_id'                    => $now,
                    'account_id_type'           => $id_type,
                    'account_id_no'             => $id_no,
                    'account_id_exp_date'       => $id_exp_date,
                    'account_status'            => $status == 1 ? 1 : 0,
                );

                $this->client_accounts->update(
                    $account_number,
                    $update_data
                );

                // update agent account
                $update_data = array(
                    'account_fname'     => $fname,
                    'account_mname'     => $mname,
                    'account_lname'     => $lname,
                    'account_status'    => $status == 1 ? 1 : 0
                );

                if ($password != "") {
                    $password = hash("sha256", $password);

                    $update_data = array_merge(
                        $update_data,
                        array(
                            'account_password' => $password
                        )
                    );
                }

                $row_account = $this->agent_accounts->get_datum(
                    '', 
                    array(
                        'account_number'   => $account_number
                    )
                )->row();

                if ($row_account != "") {
                    $this->agent_accounts->update(
                        $row_account->account_number,
                        $update_data
                    );
                }

                $this->session->set_flashdata('notification', $this->generate_notification('success', 'Successfully Updated!'));
                redirect($this->_data['form_url']);
            }
        }


        $this->_data['gender'] = $this->generate_selection(
            "gender", 
            $this->_gender, 
            $gender_id, 
            "id", 
            "name", 
            true
        );

        $this->_data['country'] = $this->generate_selection(
            "country", 
            $countries, 
            $country_id, 
            "id", 
            "name", 
            true
        );
        
        $this->_data['province']    = $this->generate_selection(
            "province", 
            $provinces, 
            $province_id, 
            "id", 
            "name", 
            true
        );

        $this->_data['sof'] = $this->generate_selection(
            "sof", 
            $sof, 
            $sof_id, 
            "id", 
            "name", 
            true
        );


        $this->_data['now'] = $this->generate_selection(
            "now", 
            $now, 
            $now_id,
            "id", 
            "name", 
            true
        );

        $this->_data['id_type'] = $this->generate_selection(
            "id_type", 
            $id_type, 
            $id_type_id,
            "id", 
            "name", 
            true
        );

        $this->_data['title']  = "Update Agent";
        $this->set_template("agents/form", $this->_data);
    }
}

