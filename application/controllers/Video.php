<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'Base.php';

class Video extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = $this->getViewParameters("Video", "Front");
        $data = $this->setMessages($data);
        $this->load->view('front', $data);
    }

}
