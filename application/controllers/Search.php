<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'Base.php';

class Search extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function index($keyword) {
        $data = $this->getViewParameters("Search", "Front");
        $data = $this->setMessages($data);
        $members = $this->sqllibs->rawSelectSql($this->db, "select * from members where username like '%".$keyword."%'");
        $videos = $this->sqllibs->rawSelectSql($this->db, "select * from videos where video_title like '%".$keyword."%'");
        $exMembers = array();
        foreach($members as $member)
        {
            $followings = count($this->sqllibs->selectAllRows($this->db, 'followers', array('to_follower_id' => $member->member_id)));
            $member->followings = $followings;
            $exMembers[] = $member;
        }
        $exVideos = array();
        $date = new DateTime();
        foreach ($videos as $video)
        {
            $videoDate = new DateTime($video->created);
            $memberInfo = $this->sqllibs->getOneRow($this->db, 'members', array('member_id' => $video->member_id));
            $followings = count($this->sqllibs->selectAllRows($this->db, 'followers', array('to_follower_id' => $memberInfo->member_id)));
            $memberInfo->followings = $followings;
            $video->elapse = $this->utils->makeTimeString($date->getTimestamp(),$videoDate->getTimestamp());
            $video->memberInfo = $memberInfo;
            $exVideos[] = $video;
        }
        $data['members'] = $exMembers;
        $data['videos'] = $exVideos;
        $this->load->view('main', $data);
    }
    public function ajaxSearchKeyword()
    {
        $postVars = $this->utils->inflatePost(array('keyword'));
        $members = $this->sqllibs->rawSelectSql($this->db, "select * from members where username like '%".$postVars['keyword']."%' limit 0,2");
        $count = 3;
        $exMembers = array();
        if (count($members) == 0)
        {
            $count = 5;
        }
        else
        {
            foreach($members as $member)
            {
                $followings = count($this->sqllibs->selectAllRows($this->db, 'followers', array('to_follower_id' => $member->member_id)));
                $member->followings = $followings;
                $exMembers[] = $member;
            }
        }
        $videos = $this->sqllibs->rawSelectSql($this->db, "select * from videos where video_title like '%".$postVars['keyword']."%' limit 0,".$count);
        $result = array();
        $result['result'] = 200;
        $result['members'] = $exMembers;
        $result['videos'] = $videos;
        echo json_encode($result);
    }
}
