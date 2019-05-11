<?php
header('Content-Type: application/json');
defined('BASEPATH') or exit('No direct script access allowed');
require 'Base.php';

class Api extends Base {

    public function __construct() {
        parent::__construct();
    }

    public function login() {
        $postVars = $this->utils->inflatePost(array('email', 'password'));
        $result = array();
        $pw = md5($postVars['password']); //md5($postVars['pw']);
        if (!$this->sqllibs->isExist($this->db, 'members', array("email" => $postVars['email'])))
        {
            $result['result'] = 400;
            $result['message'] = "Email not registered";
            echo json_encode($result,JSON_NUMERIC_CHECK);
            return;
        }
        if (!$this->sqllibs->isExist($this->db, 'members', array("email" => $postVars['email'],"password" => $pw)))
        {
            $result['result'] = 401;
            $result['message'] = "Password not correct";
            echo json_encode($result,JSON_NUMERIC_CHECK);
            return;
        }
        $userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("email" => $postVars['email'],"password" => $pw));
        unset($userInfo->password);
        $result['result'] = 200;
        $result['message'] = "success";
        $result['userInfo'] = $userInfo;
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function register()
    {
        $postVars = $this->utils->inflatePost(array('email', 'fullname', 'username', 'password'));
        $result = array();
        $pw = md5($postVars['password']);
        if ($this->sqllibs->isExist($this->db, 'members', array("username" => $postVars['username']))) {
            $result['result'] = 400;
            $result['message'] = "Username already registered";
            echo json_encode($result,JSON_NUMERIC_CHECK);
            return;
        }
        if ($this->sqllibs->isExist($this->db, 'members', array("email" => $postVars['email']))) {
            $result['result'] = 401;
            $result['message'] = "Email already registered";
            echo json_encode($result,JSON_NUMERIC_CHECK);
            return;
        }
        $id = $this->sqllibs->insertRow($this->db, 'members', array(
            "username" => $postVars['username'],
            "full_name" => $postVars['fullname'],
            "email" => $postVars['email'],
            "password" => md5($postVars['password'])
        ));
        $userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("member_id" => $id));
        unset($userInfo->password);
        $result['result'] = 200;
        $result['message'] = "success";
        $result['userInfo'] = $userInfo;
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }

    public function profile()
    {
        $postVars = $this->utils->inflatePost(array('member_id'));
        $userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("member_id" => $postVars['member_id']));
        if ($userInfo == NULL)
        {
            $result['result'] = 400;
            $result['message'] = "Profile not exist";
            echo json_encode($result);
            return;
        }
        unset($userInfo->password);
        $result['result'] = 200;
        $result['message'] = "success";
        $result['userInfo'] = $userInfo;
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }
}
