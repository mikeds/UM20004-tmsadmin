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

// $route["income-groups/(:num)"]          = "admin/Income_groups/index/$1";
// $route["income-groups/new"]             = "admin/Income_groups/new";
// $route["income-groups/update/(:any)"]   = "admin/Income_groups/update/$1";

// $route["income-groups/merchant-list"]           = "admin/Income_groups/merchant_list";
// $route["income-groups/merchant-list/(:any)"]    = "admin/Income_groups/merchant_list/$1";

// $route["income-shares"]                 = "admin/Income_shares";
// $route["income-shares/(:num)"]          = "admin/Income_shares/index/$1";
// $route["income-shares/update/(:any)"]   = "admin/Income_shares/update/$1";

// $route["income-scheme-types"]               = "admin/Income_scheme_types";
// $route["income-scheme-types/(:num)"]        = "admin/Income_scheme_types/index/$1";
// $route["income-scheme-types/new"]           = "admin/Income_scheme_types/new";
// $route["income-scheme-types/update/(:num)"] = "admin/Income_scheme_types/update/$1";

// $route["income-schemes"]               = "admin/Income_schemes";
// $route["income-schemes/(:num)"]        = "admin/Income_schemes/index/$1";
// $route["income-schemes/update/(:num)"] = "admin/Income_schemes/update/$1";
// $route["income-schemes/edit/(:num)"]   = "admin/Income_schemes/edit/$1";
// $route["income-schemes/delete/(:num)"] = "admin/Income_schemes/delete/$1";

$route["income-schemes/get-merchants-in-scheme/(:num)/(:num)"]   = "admin/Income_schemes/get_merchants_in_scheme/$1/$2";

// $route['ledger']                                = "admin/Ledger";
// $route['ledger/(:num)']                         = "admin/Ledger/index/$1";

$route['ledger-merchant']                       = "admin/Ledger_merchant";
$route['ledger-merchant/(:num)']                = "admin/Ledger_merchant/index/$1";

// $route['ledger-merchant/search/(:any)']         = "admin/Ledger_merchant/search/$1";
// $route['ledger-merchant/search/(:any)/(:num)']  = "admin/Ledger_merchant/search/$1/$2";

$route['ledger-client']                         = "admin/Ledger_client";
$route['ledger-client/(:num)']                  = "admin/Ledger_client/index/$1";

// $route['ledger-client/search/(:any)']           = "admin/Ledger_client/search/$1";
// $route['ledger-client/search/(:any)/(:num)']    = "admin/Ledger_client/search/$1/$2";

$route['income-sharing']      = "admin/Incoming";
// $route['outgoing']      = "admin/Outgoing";

$route['merchants']                 = "admin/Merchants";
$route['merchants/(:num)']          = "admin/Merchants/index/$1";
$route['merchants/new']             = "admin/Merchants/new";
$route['merchants/update/(:any)']   = "admin/Merchants/update/$1";

$route['agents']                 = "admin/Agents";
$route['agents/(:num)']          = "admin/Agents/index/$1";
$route['agents/new']             = "admin/Agents/new";
$route['agents/update/(:any)']   = "admin/Agents/update/$1";

$route['agent-shares-cash-in-otc']  = "admin/Agent_shares_cash_in_otc";
$route['agent-shares-cash-out-otc'] = "admin/Agent_shares_cash_out_otc";

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

























