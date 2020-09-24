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

$route['transaction-fees']                  = "admin/Transaction_fees";
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

$route["income-scheme-types"]               = "admin/Income_scheme_types";
$route["income-scheme-types/(:num)"]        = "admin/Income_scheme_types/index/$1";
$route["income-scheme-types/new"]           = "admin/Income_scheme_types/new";
$route["income-scheme-types/update/(:num)"] = "admin/Income_scheme_types/update/$1";

$route["income-schemes"]               = "admin/Income_schemes";
$route["income-schemes/(:num)"]        = "admin/Income_schemes/index/$1";
$route["income-schemes/update/(:num)"] = "admin/Income_schemes/update/$1";
$route["income-schemes/delete/(:num)"] = "admin/Income_schemes/delete/$1";

$route['ledger']                            = "admin/Ledger";

$route['ledger-merchant']                   = "admin/Ledger_merchant";
$route['ledger-merchant/search/(:any)']     = "admin/Ledger_merchant/search/$1";

$route['ledger-client']                     = "admin/Ledger_client";
$route['ledger-client/search/(:any)']       = "admin/Ledger_client/search/$1";

$route['incoming']      = "admin/Incoming";
$route['outgoing']      = "admin/Outgoing";

$route['merchants']                 = "admin/Merchants";
$route['merchants/new']             = "admin/Merchants/new";
$route['merchants/update/(:any)']   = "admin/Merchants/update/$1";

$route['top-up']                    = "admin/Top_up";
$route['top-up/update/(:any)']      = "admin/Top_up/update/$1";

$route["login"]         = "public/Login";
$route["logout"]        = "public/Logout";

$route["dragonpay"] = "public/Dragonpay";

$route['404_override'] = 'public/Error_404';
$route['translate_uri_dashes'] = FALSE;

























