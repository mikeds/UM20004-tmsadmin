<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outgoing extends Admin_Controller {

	public function after_init() {
		$this->set_scripts_and_styles();
	}

	public function index() {
		$this->_data['title']  = "Outgoing";
		$this->set_template("outgoing/index", $this->_data);
	}
}
