<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route["default_controller"]        = "admin/Dashboard";

$route["vault"] = "admin/Vault/add";

$route["transactions/qr-code/(:any)"] = "admin/Qr_code/index/$1";

$route["transactions"]          = "admin/Transactions";
$route["transactions/(:num)"]   = "admin/Transactions/index/$1";

$route['transaction-fees']                  = "admin/Transaction_fees";
$route['transaction-fees/(:num)']           = "admin/Transaction_fees/index/$1";
$route['transaction-fees/update/(:any)']    = "admin/Transaction_fees/update/$1";
$route['transaction-fees/delete/(:num)']    = "admin/Transaction_fees/delete/$1";

$route["admin-accounts"]                = "admin/Admin_accounts";
$route["admin-accounts/(:num)"]         = "admin/Admin_accounts/index/$1";
$route["admin-accounts/new"]            = "admin/Admin_accounts/new";
$route["admin-accounts/update/(:any)"]  = "admin/Admin_accounts/update/$1";

$route["merchant-accounts"]                = "admin/Merchant_accounts";
$route["merchant-accounts/(:num)"]         = "admin/Merchant_accounts/index/$1";
$route["merchant-accounts/new"]            = "admin/Merchant_accounts/new";
$route["merchant-accounts/update/(:any)"]  = "admin/Merchant_accounts/update/$1";

# CLIENT REQUEST
$route["client-request"]                = "admin/Client_request";
$route["client-request/(:num)"]         = "admin/Client_request/index/$1";
$route["client-request/update/(:any)"]  = "admin/Client_request/update/$1";

# Merchant REQUEST
$route["merchant-request"]                = "admin/Merchant_request";
$route["merchant-request/(:num)"]         = "admin/Merchant_request/index/$1";
$route["merchant-request/update/(:any)"]  = "admin/Merchant_request/update/$1";

# Agent REQUEST
$route["agent-request"]                   = "admin/Agent_request";
$route["agent-request/(:num)"]            = "admin/Agent_request/index/$1";
$route["agent-request/update/(:any)"]     = "admin/Agent_request/update/$1";

# INCOME GROUPS - CASH IN OTC
$route["income-groups-cash-in-otc"]                 = "admin/Income_groups_cash_in_otc";
$route["income-groups-cash-in-otc/(:num)"]          = "admin/Income_groups_cash_in_otc/index/$1";
$route["income-groups-cash-in-otc/new"]             = "admin/Income_groups_cash_in_otc/new";
$route["income-groups-cash-in-otc/update/(:any)"]   = "admin/Income_groups_cash_in_otc/update/$1";
$route["income-groups-cash-in-otc/delete/(:any)"]   = "admin/Income_groups_cash_in_otc/delete/$1";

# INCOME SHARES - CASH IN OTC
$route["income-shares-cash-in-otc"]                 = "admin/Income_shares_cash_in_otc";
$route["income-shares-cash-in-otc/(:num)"]          = "admin/Income_shares_cash_in_otc/index/$1";
$route["income-shares-cash-in-otc/update/(:any)"]   = "admin/Income_shares_cash_in_otc/update/$1";

# INCOME GROUPS - CASH OUT OTC
$route["income-groups-cash-out-otc"]                 = "admin/Income_groups_cash_out_otc";
$route["income-groups-cash-out-otc/(:num)"]          = "admin/Income_groups_cash_out_otc/index/$1";
$route["income-groups-cash-out-otc/new"]             = "admin/Income_groups_cash_out_otc/new";
$route["income-groups-cash-out-otc/update/(:any)"]   = "admin/Income_groups_cash_out_otc/update/$1";
$route["income-groups-cash-out-otc/delete/(:any)"]   = "admin/Income_groups_cash_out_otc/delete/$1";

# INCOME SHARES - CASH OUT OTC
$route["income-shares-cash-out-otc"]                 = "admin/Income_shares_cash_out_otc";
$route["income-shares-cash-out-otc/(:num)"]          = "admin/Income_shares_cash_out_otc/index/$1";
$route["income-shares-cash-out-otc/update/(:any)"]   = "admin/Income_shares_cash_out_otc/update/$1";

# INCOME GROUPS - CASH OUT UBP
$route["income-groups-cash-out-ubp"]                 = "admin/Income_groups_cash_out_ubp";
$route["income-groups-cash-out-ubp/(:num)"]          = "admin/Income_groups_cash_out_ubp/index/$1";
$route["income-groups-cash-out-ubp/new"]             = "admin/Income_groups_cash_out_ubp/new";
$route["income-groups-cash-out-ubp/update/(:any)"]   = "admin/Income_groups_cash_out_ubp/update/$1";
$route["income-groups-cash-out-ubp/delete/(:any)"]   = "admin/Income_groups_cash_out_ubp/delete/$1";

# INCOME SHARES - CASH OUT UBP
$route["income-shares-cash-out-ubp"]                 = "admin/Income_shares_cash_out_ubp";
$route["income-shares-cash-out-ubp/(:num)"]          = "admin/Income_shares_cash_out_ubp/index/$1";
$route["income-shares-cash-out-ubp/update/(:any)"]   = "admin/Income_shares_cash_out_ubp/update/$1";

# INCOME GROUPS - CreateScanQR
$route["income-groups-createscanqr"]                 = "admin/Income_groups_createscanqr";
$route["income-groups-createscanqr/(:num)"]          = "admin/Income_groups_createscanqr/index/$1";
$route["income-groups-createscanqr/new"]             = "admin/Income_groups_createscanqr/new";
$route["income-groups-createscanqr/update/(:any)"]   = "admin/Income_groups_createscanqr/update/$1";
$route["income-groups-createscanqr/delete/(:any)"]   = "admin/Income_groups_createscanqr/delete/$1";

# INCOME SHARES - CreateScanQR
$route["income-shares-createscanqr"]                 = "admin/Income_shares_createscanqr";
$route["income-shares-createscanqr/(:num)"]          = "admin/Income_shares_createscanqr/index/$1";
$route["income-shares-createscanqr/update/(:any)"]   = "admin/Income_shares_createscanqr/update/$1";

# INCOME GROUPS - QuickPayQR
$route["income-groups-quickpayqr"]                  = "admin/Income_groups_quickpayqr";
$route["income-groups-quickpayqr/(:num)"]           = "admin/Income_groups_quickpayqr/index/$1";
$route["income-groups-quickpayqr/new"]              = "admin/Income_groups_quickpayqr/new";
$route["income-groups-quickpayqr/update/(:any)"]    = "admin/Income_groups_quickpayqr/update/$1";
$route["income-groups-quickpayqr/delete/(:any)"]    = "admin/Income_groups_quickpayqr/delete/$1";

# INCOME SHARES - QuickPayQR
$route["income-shares-quickpayqr"]                  = "admin/Income_shares_quickpayqr";
$route["income-shares-quickpayqr/(:num)"]           = "admin/Income_shares_quickpayqr/index/$1";
$route["income-shares-quickpayqr/update/(:any)"]    = "admin/Income_shares_quickpayqr/update/$1";

# INCOME GROUPS - ScanPayQR
$route["income-groups-scanpayqr"]                   = "admin/Income_groups_scanpayqr";
$route["income-groups-scanpayqr/(:num)"]            = "admin/Income_groups_scanpayqr/index/$1";
$route["income-groups-scanpayqr/new"]               = "admin/Income_groups_scanpayqr/new";
$route["income-groups-scanpayqr/update/(:any)"]     = "admin/Income_groups_scanpayqr/update/$1";
$route["income-groups-scanpayqr/delete/(:any)"]     = "admin/Income_groups_scanpayqr/delete/$1";

# INCOME SHARES - ScanPayQR
$route["income-shares-scanpayqr"]                   = "admin/Income_shares_scanpayqr";
$route["income-shares-scanpayqr/(:num)"]            = "admin/Income_shares_scanpayqr/index/$1";
$route["income-shares-scanpayqr/update/(:any)"]     = "admin/Income_shares_scanpayqr/update/$1";

# BASE64 Image Viewer
$route["image-viewer/profile-picture/(:any)"]       = "admin/Base64_image_viewer/profile_picture/$1";
$route["image-viewer/id-front/(:any)"]              = "admin/Base64_image_viewer/id_front/$1";
$route["image-viewer/id-back/(:any)"]               = "admin/Base64_image_viewer/id_back/$1";

# DOWNLOAD
$route["downloads/merchants/(:any)"]                = "admin/Downloads/merchants/$1";

$route["income-schemes/get-merchants-in-scheme/(:num)/(:num)"]   = "admin/Income_schemes/get_merchants_in_scheme/$1/$2";


$route['ledger-merchant']                       = "admin/Ledger_merchant";
$route['ledger-merchant/(:num)']                = "admin/Ledger_merchant/index/$1";


$route['ledger-client']                         = "admin/Ledger_client";
$route['ledger-client/(:num)']                  = "admin/Ledger_client/index/$1";


$route['income-sharing']      = "admin/Incoming";
// $route['outgoing']      = "admin/Outgoing";

$route['merchants']                 = "admin/Merchants";
$route['merchants/(:num)']          = "admin/Merchants/index/$1";
$route['merchants/new']             = "admin/Merchants/new";
$route['merchants/update/(:any)']   = "admin/Merchants/update/$1";

// AGENTS
$route['agents']                 = "admin/Agents";
$route['agents/(:num)']          = "admin/Agents/index/$1";
$route['agents/new']             = "admin/Agents/new";
$route['agents/update/(:any)']   = "admin/Agents/update/$1";

$route['agent-shares-cash-in-otc']  = "admin/Agent_shares_cash_in_otc";
$route['agent-shares-cash-out-otc'] = "admin/Agent_shares_cash_out_otc";

// DEALERS
$route['dealers']                 = "admin/Dealers";
$route['dealers/(:num)']          = "admin/Dealers/index/$1";
$route['dealers/new']             = "admin/Dealers/new";
$route['dealers/update/(:any)']   = "admin/Dealers/update/$1";

$route['top-up']                    = "admin/Top_up";
$route['top-up/(:num)']             = "admin/Top_up/index/$1";
$route['top-up/update/(:any)']      = "admin/Top_up/update/$1";

$route["crawler/dragonpay"]                 = "public/Crawler/dragonpay";
$route["cron/dragonpay"]                    = "public/Cron/dragonpay";
$route["cron/dragonpay/(:any)"]             = "public/Cron/dragonpay/$1";
$route["cron/dragonpay/(:any)/(:any)"]      = "public/Cron/dragonpay/$1/$2";

$route["login"]         = "public/Login";
$route["logout"]        = "public/Logout";

$route["settlement-report"]         = "admin/Settlement_report";
$route["settlement-report/(:num)"]  = "admin/Settlement_report/index/$1";

// $route["dragonpay"] = "public/Dragonpay";

$route['404_override'] = 'public/Error_404';
$route['translate_uri_dashes'] = FALSE;

























