<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'Base.php';

class Video extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function index($videoId) {
        $data = $this->getViewParameters("Video", "Front");
        $data = $this->setMessages($data);
        $videoInfo = $this->sqllibs->getOneRow($this->db, 'videos', array('id' => $videoId));
        $videoInfo->view_count++;
        $this->sqllibs->updateRow($this->db, 'videos', array(
            "view_count" => $videoInfo->view_count,
                ), array('id' => $videoInfo->id));
        
        $videoInfo = $this->getDetailInfo($videoInfo);
        $isFollow = false;
        if ($this->session->userInfo !='' && $this->sqllibs->isExist($this->db, 'followers', array('member_id' => $this->session->userInfo->member_id,'to_follower_id' => $videoInfo->member_id))) {
            $isFollow = true;
        }
        $otherVideos = $this->sqllibs->rawSelectSql($this->db, "select * from videos where member_id='".$videoInfo->member_id."' and id !='".$videoId."'");        
        $videos = array();
        foreach($otherVideos as $vInfo)
        {
            $videos[] = $vInfo = $this->getDetailInfo($vInfo);
        }
        $data['videoInfo'] = $videoInfo;
        $data['videos'] = $videos;
        $data['isFollow'] = $isFollow;
        $this->load->view('main', $data);
    }

    public function ajaxGetComments()
    {
        $postVars = $this->utils->inflatePost(array('videoId', 'page','order'));
        $order = "desc";
        if ($postVars['order'] == 1)
            $order = "asc";
        $comments = $this->sqllibs->rawSelectSql($this->db, "select * from comments where video_id='".$postVars['videoId']."' order by created ".$order." limit ".($postVars['page'] * 50).",50");
        $exComments = array();
        foreach($comments as $comment)
        {
            $memberInfo = $this->sqllibs->getOneRow($this->db, 'members', array('member_id' => $comment->member_id));
            $comment->memberInfo = $memberInfo;
            $date = new DateTime();
            $comment->elapse = $this->utils->makeTimeString($date->getTimestamp(),$comment->created);
            $exComments[] = $comment;
        }
        $result = array();
        $result['result'] = 200;
        $result['comments'] = $exComments;
        $result['page'] = $postVars['page'];
        echo json_encode($result);
    }
    public function actionComment()
    {
        $postVars = $this->utils->inflatePost(array('memberId', 'postId', 'content'));
        $date = new DateTime();
        $this->sqllibs->insertRow($this->db, 'comments', array(
            "video_id" => $postVars['postId'],
            "member_id" => $postVars['memberId'],
            "content" => $postVars['content'],
            "created" => $date->getTimestamp()
        ));
        redirect($this->agent->referrer());
    }
}
