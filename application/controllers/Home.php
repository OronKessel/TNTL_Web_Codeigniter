<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'Base.php';

class Home extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data = $this->getViewParameters("Home", "Front");
        $data = $this->setMessages($data);
        $this->load->view('main', $data);
    }

    public function login() {
        $data = $this->getViewParameters("Login", "Front");
        $data = $this->setMessages($data);
        $this->load->view('main', $data);
    }

    public function register() {
        $data = $this->getViewParameters("Register", "Front");
        $data = $this->setMessages($data);
        $this->load->view('main', $data);
    }
    public function ajaxReport()
    {
        $postVars = $this->utils->inflatePost(array('memberId', 'postId'));
        $videoInfo = $this->sqllibs->getOneRow($this->db, 'videos', array('id' => $postVars['postId']));
        $reportCount = $videoInfo->report_count;
        $this->sqllibs->updateRow($this->db, 'videos', array(
            "report_count" => $reportCount + 1), array('id' => $postVars['postId']));
        $result = array();
        $result['result'] = 200;
        echo json_encode($result);
    }
    public function ajaxLike()
    {
        $postVars = $this->utils->inflatePost(array('memberId', 'postId', 'like'));
        $videoInfo = $this->sqllibs->getOneRow($this->db, 'videos', array('id' => $postVars['postId']));
        $likeCount = $videoInfo->lk_count;
        $unlikeCount = $videoInfo->ulk_count;
        if ($postVars['like'] == 1)
        {
            $likeCount++;
            $this->sqllibs->insertRow($this->db, 'video_follow_like', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId'],'value' => $postVars['like']));
            if ($this->sqllibs->isExist($this->db, 'video_follow_unlike', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId']))) {
                $unlikeCount--;
                $this->sqllibs->deleteRow($this->db, 'video_follow_unlike', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId']));
            }
        }
        else{
            $likeCount--;
            $this->sqllibs->deleteRow($this->db, 'video_follow_like', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId']));
            
        }
        $this->sqllibs->updateRow($this->db, 'videos', array(
            "lk_count" => $likeCount,"ulk_count" => $unlikeCount), array('id' => $postVars['postId']));
        $result = array();
        $result['result'] = 200;
        $result['value'] = $postVars['like'];
        $result['count'] = $likeCount;
        $result['count1'] = $unlikeCount;
        echo json_encode($result);
    }
    public function ajaxUnlike()
    {
        $postVars = $this->utils->inflatePost(array('memberId', 'postId', 'like'));
        $videoInfo = $this->sqllibs->getOneRow($this->db, 'videos', array('id' => $postVars['postId']));
        $unlikeCount = $videoInfo->ulk_count;
        $likeCount = $videoInfo->lk_count;
        if ($postVars['like'] == 1)
        {
            $unlikeCount++;
            $this->sqllibs->insertRow($this->db, 'video_follow_unlike', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId'],'value' => $postVars['like']));
            if ($this->sqllibs->isExist($this->db, 'video_follow_like', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId']))) {
                $likeCount--;
            $this->sqllibs->deleteRow($this->db, 'video_follow_like', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId']));
            }
        }
        else{
            $unlikeCount--;
            $this->sqllibs->deleteRow($this->db, 'video_follow_unlike', array('member_id' => $postVars['memberId'],'video_id' => $postVars['postId']));
        }
        $this->sqllibs->updateRow($this->db, 'videos', array(
            "lk_count" => $likeCount,"ulk_count" => $unlikeCount), array('id' => $postVars['postId']));
        $result = array();
        $result['result'] = 200;
        $result['value'] = $postVars['like'];
        $result['count'] = $unlikeCount;
        $result['count1'] = $likeCount;
        echo json_encode($result);
    }
    public function ajaxAddComment()
    {
        $postVars = $this->utils->inflatePost(array('memberId', 'postId', 'content'));
        $date = new DateTime();
        $this->sqllibs->insertRow($this->db, 'comments', array(
            "video_id" => $postVars['postId'],
            "member_id" => $postVars['memberId'],
            "content" => $postVars['content'],
            "created" => $date->getTimestamp()
        ));
        $result = array();
        $result['result'] = 200;
        echo json_encode($result);
    }
    public function ajaxFeed() {
        $postVars = $this->utils->inflatePost(array('memberId', 'start', 'count'));
        $feeds = $this->sqllibs->rawSelectSql($this->db, 'select * from videos order by created desc limit ' . $postVars['start'] . ', ' . ($postVars['start'] + $postVars['count']));
        $items = array();
        $date = new DateTime();
        foreach ($feeds as $feed) {
            $videoDate = new DateTime($feed->created);
            $memberInfo = $this->sqllibs->getOneRow($this->db, "members", array('member_id' => $feed->member_id));
            $unlikeInfo = NULL;
            $likeInfo = NULL;
            if ($postVars['memberId'] != '')
            {
                $unlikeInfo = $this->sqllibs->getOneRow($this->db, "video_follow_unlike", array('member_id' => $feed->member_id,'video_id' => $feed->id));
                $likeInfo = $this->sqllibs->getOneRow($this->db, "video_follow_like", array('member_id' => $feed->member_id,'video_id' => $feed->id));
            }
            $feed->unlike = false;    
            $feed->like = false;
            $feed->elapse = $this->utils->makeTimeString($date->getTimestamp(),$videoDate->getTimestamp());
            if ($unlikeInfo != NULL)
                $feed->unlike = true;
            if ($likeInfo != NULL)
                $feed->like = true;    
            $feed->memberInfo = $memberInfo;
            $items[] = $feed;
        }
        $result = array();
        $result['result'] = 200;
        $result['feeds'] = $items;
        echo json_encode($result);
    }
    public function ajaxGetComment()
    {
        $postVars = $this->utils->inflatePost(array('videoId', 'page'));
        $comments = $this->sqllibs->rawSelectSql($this->db, "select * from comments where video_id='".$postVars['videoId']."' order by created desc limit ".($postVars['page'] * 5).",5");
        $exComments = array();
        for ($i = count($comments) - 1;$i >=0;$i--)
        {
            $comment = $comments[$i];
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
        $result['videoId'] = $postVars['videoId'];
        echo json_encode($result);
    }
    public function actionRegister() {
        $postVars = $this->utils->inflatePost(array('email', 'fullname', 'username', 'password'));
        if ($this->sqllibs->isExist($this->db, 'members', array("username" => $postVars['username']))) {
            $this->session->set_flashdata('errorMessage', "Username already registered");
            redirect($this->agent->referrer());
            return;
        }
        if ($this->sqllibs->isExist($this->db, 'members', array("email" => $postVars['email']))) {
            $this->session->set_flashdata('errorMessage', "Email already registered");
            redirect($this->agent->referrer());
            return;
        }
        if (strlen($postVars['password']) < 6) {
            $this->session->set_flashdata('errorMessage', "Password must be 6 characters");
            redirect($this->agent->referrer());
            return;
        }
        $this->sqllibs->insertRow($this->db, 'members', array(
            "username" => $postVars['username'],
            "full_name" => $postVars['fullname'],
            "email" => $postVars['email'],
            "password" => md5($postVars['password'])
        ));
        $this->session->set_flashdata('message', "Sign Up Successfully!");
        $this->utils->redirectPage('index.php/Home/login');
    }

    public function actionLogin() {
        if ($this->utils->isEmptyPost(array('email', 'password'))) {
            $this->session->set_flashdata('errorMessage', "Please fill input.");
            redirect($this->agent->referrer());
            return;
        }
        $postVars = $this->utils->inflatePost(array('email', 'password'));
        $pw = md5($postVars['password']);
        if ($this->sqllibs->isExist($this->db, 'members', array("email" => $postVars['email'], "password" => $pw))) {
            $userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("email" => $postVars['email'], "password" => $pw));
            $this->session->set_userdata(array("userInfo" => $userInfo));
            $this->utils->redirectPage('index.php');
        }
        $this->session->set_flashdata('errorMessage', "Login Fail");
        redirect($this->agent->referrer());
    }

    public function actionLogout() {
        $this->session->set_userdata(array("userInfo" => ""));
        $this->utils->redirectPage('index.php');
    }

}
