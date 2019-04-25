<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Base extends CI_Controller {

    public $database;

    public function __construct() {
        parent::__construct();
        $this->connectDB();

        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('utils');
        $this->load->library('sqllibs');
        $this->load->library('user_agent');
        $this->load->library('scheduleLib');
        $this->lang->load('admin_lang');
    }

    public function isLogin() {
        if ($this->session->userInfo == "") {
            return 0;
        } elseif ($this->session->userInfo == "1") {
            return 1;
        } else {
            return 2;
        }
    }

    public function setMessages($data) {
        $data['error'] = $this->session->flashdata('errorMessage');
        $data['message'] = $this->session->flashdata('message');
        $this->session->set_flashdata('errorMessage', "");
        $this->session->set_flashdata('message', "");
        return $data;
    }

    public function connectDB() {
        $this->database = $this->load->database();
    }

    public function getViewParameters($pageName = '', $role = 'Customer', $title = 'Bruped') {
        $data['title'] = $title;
        $data['pageName'] = $pageName;
        $data['role'] = $role;
        return $data;
    }

}
