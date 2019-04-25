<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tvderbyapi_model extends CI_Model{

    public function __construct() {
        parent::__construct();
    }

    public function getgenredata($genre) {
      $sql = "SELECT * FROM tbl_data WHERE genres LIKE '%$genre%'";
      $result  = $this->db->query($sql)->result();
      return $result;
    }
}
