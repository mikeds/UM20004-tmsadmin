<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Downloads extends Admin_Controller {
	public function after_init() {}


	public function merchants($id) {
		if(isset($_GET['link'])) {
			$link = $_GET['link'];
			$file = "{$this->_upload_path}/" . ENVIRONMENT . "/uploads/merchants/{$id}/{$link}";

		if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			}
		} //- the missing closing brace

		echo "File not found!";
	}
}
