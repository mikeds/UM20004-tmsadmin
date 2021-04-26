<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base64_image_viewer extends Admin_Controller {
	public function after_init() {}

	public function profile_picture($account_number) {
		$this->load->model("admin/Merchant_pre_registration_model", "m_pre_registration");
		$this->load->model("admin/Client_pre_registration_model", "c_pre_registration");

		$row_merchant = $this->m_pre_registration->get_datum(
			'',
			array(
				'account_number'	=> $account_number
			)
		)->row();

		$row_client = $this->c_pre_registration->get_datum(
			'',
			array(
				'account_number'	=> $account_number
			)
		)->row();

		$row = ($row_merchant != "" ? $row_merchant : $row_client);

		if ($row == "") {
			echo "Image not found!";
			return;
		}

		header('Content-Type: image/jpeg');
		echo base64_decode($row->account_avatar_base64);
	}

	public function id_front($account_number) {
		$this->load->model("admin/Merchant_pre_registration_model", "m_pre_registration");
		$this->load->model("admin/Client_pre_registration_model", "c_pre_registration");

		$row_merchant = $this->m_pre_registration->get_datum(
			'',
			array(
				'account_number'	=> $account_number
			)
		)->row();

		$row_client = $this->c_pre_registration->get_datum(
			'',
			array(
				'account_number'	=> $account_number
			)
		)->row();

		$row = ($row_merchant != "" ? $row_merchant : $row_client);

		if ($row == "") {
			echo "Image not found!";
			return;
		}

		header('Content-Type: image/jpeg');
		echo base64_decode($row->account_id_front_base64);
	}

	public function id_back($account_number) {
		$this->load->model("admin/Merchant_pre_registration_model", "m_pre_registration");
		$this->load->model("admin/Client_pre_registration_model", "c_pre_registration");

		$row_merchant = $this->m_pre_registration->get_datum(
			'',
			array(
				'account_number'	=> $account_number
			)
		)->row();

		$row_client = $this->c_pre_registration->get_datum(
			'',
			array(
				'account_number'	=> $account_number
			)
		)->row();

		$row = ($row_merchant != "" ? $row_merchant : $row_client);

		if ($row == "") {
			echo "Image not found!";
			return;
		}

		header('Content-Type: image/jpeg');
		echo base64_decode($row->account_id_back_base64);
	}
}
