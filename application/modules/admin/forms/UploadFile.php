<?php
class Admin_Forms_UploadFile {
	public $_upload;
	function __construct($save_path = "/icon/") {
		$this->_upload = new Zend_File_Transfer;
		// $this->_upload->setDestination(PUBLIC_PATH.$save_path);
	}
}