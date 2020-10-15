<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CMS_Controller class
 * Base controller ?
 *
 * @author Marknel Pineda
 */
class Cli_Controller extends Global_Controller {
	protected
		$_today = "";

	protected
		$_base_controller = "public",
		$_base_session = "session",
		$_data = array(); // shared data with child controller

	/**
	 * Constructor
	 */
	public function __construct() {
		// Initialize all configs, helpers, libraries from parent
		parent::__construct();
		date_default_timezone_set("Asia/Manila");
		$this->_today = date("Y-m-d H:i:s");

		// $this->validate_access();
		$this->after_init();
	}

	private function validate_access() {
		if (PHP_SAPI != 'cli') {
			// access via browser
			$this->output->set_status_header(401);
            die();
        }
	}

	public function get_environment_url($environment) {
		if ($environment == "dev") {
			return DEV_URL;
		} else if ($environment == "test") {
			return TEST_URL;
		} else if ($environment == "stag") {
			return STAG_URL;
		} else if ($environment == "prod") {
			return PROD_URL;
		} else {
			return LOCAL_URL;
		}
	}
}
