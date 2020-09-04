<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Income_schemes extends Admin_Controller {

	public function after_init() {
		$this->set_scripts_and_styles();
	}

	public function index() {
		$this->_data['title']  = "Income Schemes";
		$this->set_template("dashboard/index", $this->_data);
	}
}
