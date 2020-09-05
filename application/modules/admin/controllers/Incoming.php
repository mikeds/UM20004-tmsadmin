<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incoming extends Admin_Controller {

	public function after_init() {
		$this->set_scripts_and_styles();
	}

	public function index() {
		$this->_data['title']  = "Incoming";
		$this->set_template("incoming/index", $this->_data);
	}
}
