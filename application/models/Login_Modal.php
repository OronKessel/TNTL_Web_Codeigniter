<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_Modal extends CI_Model{

	public function __construct() {
		parent::__construct();
		$this->load->database(); 
	}

	public function checkLogin($credential) {

		$sql = $this->db->get_where('tbl_user', $credential);
		$row = $sql->row();
		return $row;
	}

	public function getUserInfo($email) {
	    $sql = "SELECT * FROM tbl_user WHERE email='$email'";
	    $result = $this->db->query($sql)->row();
	    return $result;
    }

}