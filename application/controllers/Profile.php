<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'Base.php';

class Profile extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function index($memberName) {
        $data = $this->getViewParameters("Profile", "Front");
        $data = $this->setMessages($data);
        $userInfo = $this->sqllibs->getOneRow($this->db, 'members', array('username' => $memberName));
        $memberId = $userInfo->member_id;
        $videos = $this->sqllibs->selectAllRows($this->db, 'videos', array('member_id' => $memberId));
        $followers = count($this->sqllibs->selectAllRows($this->db, 'followers', array('member_id' => $memberId)));
        $followings = count($this->sqllibs->selectAllRows($this->db, 'followers', array('to_follower_id' => $memberId)));
        $isFollow = false;
        if ($this->session->userInfo !='' && $this->sqllibs->isExist($this->db, 'followers', array('member_id' => $this->session->userInfo->member_id,'to_follower_id' => $memberId))) {
            $isFollow = true;
        }
        $items = array();
        foreach ($videos as $feed) {
            $items[] = $this->getDetailInfo($feed);
        }
        $data['userInfo'] = $userInfo;
        $data['videos'] = $items;
        $data['followers'] = $followers;
        $data['followings'] = $followings;
        $data['isFollow'] = $isFollow;
        $this->load->view('main', $data);
    }

    public function ajaxFollow()
    {
        $postVars = $this->utils->inflatePost(array('memberId', 'followId', 'value'));
        $date = new DateTime();
        if ($postVars['value'] == '1')
        {
            $this->sqllibs->insertRow($this->db, 'followers', array('member_id' => $postVars['memberId'],'to_follower_id' => $postVars['followId'],'time' => $date->getTimestamp()));
        }
        else
        {
            $this->sqllibs->deleteRow($this->db, 'followers', array('member_id' => $postVars['memberId'],'to_follower_id' => $postVars['followId']));
        }
        $followers = count($this->sqllibs->selectAllRows($this->db, 'followers', array('member_id' => $postVars['memberId'])));
        $followings = count($this->sqllibs->selectAllRows($this->db, 'followers', array('to_follower_id' => $postVars['memberId'])));

        $result = array();
        $result['result'] = 200;
        $result['value'] = $postVars['value'];
        $result['followers'] = $followers;
        $result['followings'] = $followings;
        echo json_encode($result);
    }

}
