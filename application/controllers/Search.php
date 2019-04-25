<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'Base.php';

class Search extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = $this->getViewParameters("Search", "Front");
        $data = $this->setMessages($data);
        $this->load->view('front', $data);
    }

}
