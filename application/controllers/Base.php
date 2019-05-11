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

    public function getDetailInfo($feed)
    {
        $date = new DateTime();
        $videoDate = new DateTime($feed->created);
        $memberInfo = $this->sqllibs->getOneRow($this->db, "members", array('member_id' => $feed->member_id));
        $unlikeInfo = NULL;
        $likeInfo = NULL;
        if ($this->session->userInfo != '')
        {
            $unlikeInfo = $this->sqllibs->getOneRow($this->db, "video_follow_unlike", array('member_id' => $this->session->userInfo->member_id,'video_id' => $feed->id));
            $likeInfo = $this->sqllibs->getOneRow($this->db, "video_follow_like", array('member_id' => $this->session->userInfo->member_id,'video_id' => $feed->id));
            $feed->unlike = true;    
            $feed->like = true;
        }
        $feed->elapse = $this->utils->makeTimeString($date->getTimestamp(),$videoDate->getTimestamp());
        if ($unlikeInfo == NULL)
            $feed->unlike = false;    
        if ($likeInfo == NULL)
            $feed->like = false;    
        $feed->memberInfo = $memberInfo;
        return $feed;
    }

}
