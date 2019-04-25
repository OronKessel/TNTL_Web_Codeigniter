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
        $this->load->view('front', $data);
    }

    public function login() {
        $data = $this->getViewParameters("Login", "Front");
        $data = $this->setMessages($data);
        $this->load->view('front', $data);
    }

    public function register() {
        $data = $this->getViewParameters("Register", "Front");
        $data = $this->setMessages($data);
        $this->load->view('front', $data);
    }

    public function ajaxFeed() {
        $postVars = $this->utils->inflatePost(array('memberId', 'start', 'count'));
        $feeds = $this->sqllibs->rawSelectSql($this->db, 'select * from videos order by created desc limit ' . $postVars['start'] . ', ' . ($postVars['start'] + $postVars['count']));
        $items = array();
        foreach ($feeds as $feed) {
            $memberInfo = $this->sqllibs->getOneRow($this->db, "members", array('member_id' => $feed->member_id));
            $feed->memberInfo = $memberInfo;
            $items[] = $feed;
        }
        $result = array();
        $result['result'] = 200;
        $result['feeds'] = $items;
        echo json_encode($result);
    }

    public function actionRegister() {
        $postVars = $this->utils->inflatePost(array('email', 'fullname', 'username', 'password'));
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

    public function showsPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Shows", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $servers = $this->sqllibs->selectAllRows($this->db, 'servers');
        $contents = $this->sqllibs->rawSelectSql($this->db, 'select A.*, B.category_name as mcategory, D.subcategory_name as sname from vod_shows as A left join vod_categories as B on A.category = B.category_id left join vod_subcategories as D on A.subcategory = D.subcategory_id');
        $categorys = $this->sqllibs->selectAllRows($this->db, 'vod_categories');
        $data['contents'] = $contents;
        $data['servers'] = $servers;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $data['categorys'] = $categorys;
        $this->load->view('view_main', $data);
    }

    public function ajaxSubCategory($id) {
        $categorys = $this->sqllibs->selectAllRows($this->db, 'vod_subcategories', array('category_id' => $id));
        $data['categorys'] = $categorys;
        echo json_encode($data);
    }

    public function memberPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Member", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $members = $this->sqllibs->rawSelectSql($this->db, 'select A.*, B.package_id as package_id from members as A left join members_packages as B on A.member_id = B.member_id');
        $packages = $this->sqllibs->selectAllRows($this->db, 'packages');
        $data['members'] = $members;
        $data['packages'] = $packages;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function devicePage($memberId) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $memberInfo = $this->sqllibs->getOneRow($this->db, 'members', array('member_id' => $memberId));
        if ($memberInfo == null) {
            $this->utils->redirectPage(ADMIN_PAGE_MEMBER);
            return;
        }
        $devices = $this->sqllibs->rawSelectSql($this->db, "select * from members_devices where member_id='" . $memberId . "'");
        $data = $this->getViewParameters("Device", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $data['devices'] = $devices;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $data['memberId'] = $memberId;
        $this->load->view('view_main', $data);
    }

    public function packagePage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Package", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $packages = $this->sqllibs->selectAllRows($this->db, 'packages');
        $channels = $this->sqllibs->selectAllRows($this->db, 'channels');
        $categorys = $this->sqllibs->selectAllRows($this->db, 'vod_categories');


        foreach ($categorys as $category) {
            $subCategory = $this->sqllibs->selectAllRows($this->db, 'vod_subcategories', array('category_id' => $category->category_id));
            $category->subInfos = $subCategory;
        }
        foreach ($packages as $package) {
            $pChannels = $this->sqllibs->selectAllRows($this->db, 'packages_channels', array('package_id' => $package->package_id));
            $pVods = $this->sqllibs->selectAllRows($this->db, 'packages_vod', array('package_id' => $package->package_id));
            $channelNames = "";
            foreach ($pChannels as $cid) {
                $chInfo = $this->sqllibs->getOneRow($this->db, 'channels', array('channel_id' => $cid->channel_id));
                $package->chInfos[] = $chInfo;
            }
            $package->vodInfos = $pVods;
//            foreach ($pVods as $vod) {
//                $vodInfo = $this->sqllibs->getOneRow($this->db, 'vod_subcategories', array('subcategory_id' => $vod->vod_subcategory_id));
//                $package->vodInfos[] = $vodInfo;
//            }
        }

        $data['channels'] = $channels;
        $data['packages'] = $packages;
        $data['categorys'] = $categorys;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function actionAddEmployee() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('employeeUser', 'employeePassword', 'employeeMember', 'employeeChannel', 'employeeVod'));
        if ($this->sqllibs->isExist($this->db, 'employees', array("username" => $postVars['employeeUser']))) {
            $this->session->set_flashdata('errorMessage', $this->lang->line('employee_message_error'));
            $this->utils->redirectPage(ADMIN_PAGE_EMPLOYEE);
            return;
        }
        $this->sqllibs->insertRow($this->db, 'employees', array(
            "username" => $postVars['employeeUser'],
            "password" => md5($postVars['employeePassword']),
            "members_management" => $postVars['employeeMember'],
            "channels_management" => $postVars['employeeChannel'],
            "vod_management" => $postVars['employeeVod'],
            "is_admin" => 0
        ));
        $this->session->set_flashdata('message', $this->lang->line('employee_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_EMPLOYEE);
    }

    public function actionAddDevice() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('memberId', 'deviceSn'));
        $this->sqllibs->insertRow($this->db, 'members_devices', array(
            "device_name" => $postVars['deviceSn'],
            "member_id" => $postVars['memberId']
        ));
        $this->session->set_flashdata('message', $this->lang->line('device_success_add'));
        $this->utils->redirectPage(ADMIN_PAGE_DEVICE . "/" . $postVars['memberId']);
    }

    public function actionAddServer() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('serverAddress', 'serverType'));
        $this->sqllibs->insertRow($this->db, 'servers', array(
            "address" => $postVars['serverAddress'],
            "type" => $postVars['serverType']
        ));
        $this->session->set_flashdata('message', $this->lang->line('server_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_SERVER);
    }

    public function actionAddChannel() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('channelName', 'channelNumber', 'channelActive', 'channelLanguage', 'channelLiveIp', 'channelImage', 'channelDvrIp', 'channelInputAddress', 'channelAdaptive', 'channelLives', 'channelDvrs', 'channelId'));
        $imageFile = '';
        if (isset($_FILES['uploadChannelImage']) && !empty($_FILES['uploadChannelImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['uploadChannelImage'], 0, 200, 200);
        }

        $channelId = $this->sqllibs->insertRow($this->db, 'channels', array(
            "active" => $postVars['channelActive'],
            "name" => $postVars['channelName'],
            "channel_number" => $postVars['channelNumber'],
            "language" => $postVars['channelLanguage'],
            "adaptive" => $postVars['channelAdaptive'],
            "input_address" => $postVars['channelInputAddress'],
            "thumb_image" => $postVars['channelImage'],
            "image" => $imageFile,
        ));
        foreach ($postVars['channelLives'] as $serverId) {
            $this->sqllibs->insertRow($this->db, 'channels_server', array(
                "channel_id" => $channelId,
                "server_id" => $serverId,
            ));
        }
        foreach ($postVars['channelDvrs'] as $serverId) {
            $this->sqllibs->insertRow($this->db, 'channels_server', array(
                "channel_id" => $channelId,
                "server_id" => $serverId,
            ));
        }
        $this->session->set_flashdata('message', $this->lang->line('channel_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_CHANNEL);
    }

    public function actionUploadEpg() {
        $postVars = $this->utils->inflatePost(array('channelId'));
        if (isset($_FILES['channelEPG'])) {
            $jsonFile = $this->utils->uploadFile($_FILES['channelEPG']);
        }
        $this->sqllibs->insertRow($this->db, 'epg', array(
            'channel_id' => $postVars['channelId']
        ));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_EPG);
    }

    public function actionAddChannelSort() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('groupName', 'groupChannel'));
        $groupId = $this->sqllibs->insertRow($this->db, 'groups', array(
            "name" => $postVars['groupName']
        ));
        foreach ($postVars['groupChannel'] as $channelId) {
            $this->sqllibs->insertRow($this->db, 'groups_channels', array(
                "group_id" => $groupId,
                "channel_id" => $channelId,
            ));
        }
        $this->session->set_flashdata('message', $this->lang->line('sort_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_SORT_GROUP);
    }

    public function actionAddTranscoder() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('tChannel', 'tActive', 'tServerId', 'tInputAddress', 'tOutAddress', 'tKbps', 'tResolution'));
        $groupId = $this->sqllibs->insertRow($this->db, 'transcoder', array(
            "channel_name" => $postVars['tChannel'],
            "active" => $postVars['tActive'],
            "server_id" => $postVars['tServerId'],
            "input_address" => $postVars['tInputAddress'],
            "output_address" => $postVars['tOutAddress'],
            "kbps" => $postVars['tKbps'],
            "resolution" => $postVars['tResolution']
        ));
        $this->session->set_flashdata('message', $this->lang->line('transcoder_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_TRANSCODER);
    }

    public function actionAddCategory() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('categoryName', 'categoryImage', 'categoryDescription'));
        $imageFile = '';
        if (isset($_FILES['uploadCategoryImage']) && !empty($_FILES['uploadCategoryImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['uploadCategoryImage'], 0, 200, 200);
        }
        $groupId = $this->sqllibs->insertRow($this->db, 'vod_categories', array(
            "category_name" => $postVars['categoryName'],
            "image" => $postVars['categoryImage'],
            "description" => $postVars['categoryDescription'],
            "image" => $imageFile,
        ));
        $this->session->set_flashdata('message', $this->lang->line('category_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_CATEGORY);
    }

    public function actionAddSubCategory() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('subName', 'subCategory'));
        $groupId = $this->sqllibs->insertRow($this->db, 'vod_subcategories', array(
            "category_id" => $postVars['subCategory'],
            "subcategory_name" => $postVars['subName']
        ));
        $this->session->set_flashdata('message', $this->lang->line('subcategory_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_CATEGORY);
    }

    public function actionAddContent() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('contentName', 'contentServer', 'contentType', 'contentShowId', 'contentPromoted', 'contentDescription', 'contentCategory', 'contentSubCategory', 'contentPrice', 'contentAge'));


        $imageFile = '';
        if (isset($_FILES['uploadContentImage']) && !empty($_FILES['uploadContentImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['uploadContentImage'], 0, 200, 200);
        }

        $videoFile = '';
        if (isset($_FILES['uploadVideo']) && !empty($_FILES['uploadVideo']['name'])) {
            $videoFile = $this->utils->uploadFile($_FILES['uploadVideo']);
        }



        $groupId = $this->sqllibs->insertRow($this->db, 'vod_content', array(
            "name" => $postVars['contentName'],
            "server" => $postVars['contentServer'],
            "description" => $postVars['contentDescription'],
            "category" => $postVars['contentCategory'],
            "subcategory" => $postVars['contentSubCategory'],
            "price" => $postVars['contentPrice'],
            "age" => $postVars['contentAge'],
            "image" => $imageFile,
            "type" => $postVars['contentType'],
            "show_id" => $postVars['contentShowId'],
            "link" => $videoFile,
            "promoted" => $postVars['contentPromoted']
        ));
        $this->session->set_flashdata('message', $this->lang->line('content_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_CONTENT);
    }

    public function actionAddShow() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('contentName', 'contentDescription', 'contentCategory', 'contentSubCategory', 'contentPromoted', 'contentAge', 'contentImage'));

        $imageFile = '';
        if (isset($_FILES['uploadShowImage']) && !empty($_FILES['uploadShowImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['uploadShowImage'], 0, 200, 200);
        }

        $groupId = $this->sqllibs->insertRow($this->db, 'vod_shows', array(
            "show_name" => $postVars['contentName'],
            "description" => $postVars['contentDescription'],
            "category" => $postVars['contentCategory'],
            "subcategory" => $postVars['contentSubCategory'],
            "age" => $postVars['contentAge'],
            "image" => $imageFile,
            "promoted" => $postVars['contentPromoted']
        ));
        $this->session->set_flashdata('message', $this->lang->line('shows_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_SHOWS);
    }

    public function actionAddMember() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('memberBill', 'memberName', 'memberEmail', 'memberPhone', 'memberPassword', 'memberAddress', 'memberApprove', 'memberPackage', 'memberBalance'));
        $memberId = $this->sqllibs->insertRow($this->db, 'members', array(
            "billing_id" => $postVars['memberBill'],
            "name" => $postVars['memberName'],
            "email" => $postVars['memberEmail'],
            "phone" => $postVars['memberPhone'],
            "password" => $postVars['memberPassword'],
            "address" => $postVars['memberAddress'],
            "approved" => $postVars['memberApprove'],
            "balance" => $postVars['memberBalance']
        ));

        $memberId = $this->sqllibs->insertRow($this->db, 'members_packages', array(
            "member_id" => $memberId,
            "package_id" => $postVars['memberPackage']
        ));
        $this->session->set_flashdata('message', $this->lang->line('member_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_MEMBER);
    }

    public function actionAddPackage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('pName', 'channelAll', 'groupChannel', 'category', 'subcategory', 'res_subcategory', 'res_subcategory'));
        $packageId = $this->sqllibs->insertRow($this->db, 'packages', array(
            "package_name" => $postVars['pName'],
            "all_channels" => $postVars['channelAll']
        ));
        foreach ($postVars['groupChannel'] as $channelId) {
            $this->sqllibs->insertRow($this->db, 'packages_channels', array(
                "package_id" => $packageId,
                "channel_id" => $channelId
            ));
        }
        foreach ($postVars['subcategory'] as $subcategory) {
            $this->sqllibs->insertRow($this->db, 'packages_vod', array(
                "package_id" => $packageId,
                "vod_subcategory_id" => $subcategory,
                "is_free" => 1,
                "is_restricted" => 0
            ));
        }
        foreach ($postVars['res_subcategory'] as $ss) {
            $this->sqllibs->insertRow($this->db, 'packages_vod', array(
                "package_id" => $packageId,
                "vod_subcategory_id" => $ss,
                "is_restricted" => 1,
                "is_free" => 0
            ));
        }
        $this->session->set_flashdata('message', $this->lang->line('package_message_success'));
        $this->utils->redirectPage(ADMIN_PAGE_MEMBER_PACKAGE);
    }

    public function actionEditEmployee() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('employeeUser', 'empId', 'employeeMember', 'employeeChannel', 'employeeVod'));
        $this->sqllibs->updateRow($this->db, 'employees', array(
            "username" => $postVars['employeeUser'],
            "password" => md5($postVars['employeePassword']),
            "members_management" => $postVars['employeeMember'],
            "channels_management" => $postVars['employeeChannel'],
            "vod_management" => $postVars['employeeVod'],
            "is_admin" => 0
                ), array('id' => $postVars['empId']));
        $this->session->set_flashdata('message', $this->lang->line('employee_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_EMPLOYEE);
    }

    public function actionEditServer() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('serverAddress', 'serverId', 'serverType'));
        $this->sqllibs->updateRow($this->db, 'servers', array(
            "address" => $postVars['serverAddress'],
            "type" => $postVars['serverType'],
                ), array('id' => $postVars['serverId']));
        $this->session->set_flashdata('message', $this->lang->line('server_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_SERVER);
    }

    public function actionEditChannel() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('editChannelId', 'channelName', 'channelNumber', 'channelActive', 'channelLanguage', 'channelImage', 'channelInputAddress', 'channelAdaptive', 'channelLives', 'channelDvrs', 'channelId')); #'editChannelId', 'channelName', 'channelNumber', 'channelActive', 'channelLanguage', 'channelLiveIp', 'channelImage', 'channelDvrIp', 'channelInputAddress', 'channelAdaptive'));

        $chInfo = $this->sqllibs->getOneRow($this->db, 'channels', array('channel_id' => $postVars['editChannelId']));
        $imageFile = $chInfo->image;
        if (isset($_FILES['editChannelImage']) && !empty($_FILES['editChannelImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['editChannelImage'], 0, 200, 200);
            if ($imageFile == null) {
                $imageFile = $chInfo->image;
            }
        }

        $this->sqllibs->updateRow($this->db, 'channels', array(
            "active" => $postVars['channelActive'],
            "name" => $postVars['channelName'],
            "channel_number" => $postVars['channelNumber'],
            "language" => $postVars['channelLanguage'],
            "adaptive" => $postVars['channelAdaptive'],
            "input_address" => $postVars['channelInputAddress'],
            "thumb_image" => $postVars['channelImage'],
            "image" => $imageFile,
                ), array('channel_id' => $postVars['editChannelId']));
        $this->sqllibs->deleteRow($this->db, 'channels_server', array(
            'channel_id' => $postVars['editChannelId']
        ));
        foreach ($postVars['channelLives'] as $serverId) {
            $this->sqllibs->insertRow($this->db, 'channels_server', array(
                "channel_id" => $postVars['editChannelId'],
                "server_id" => $serverId,
            ));
        }
        foreach ($postVars['channelDvrs'] as $serverId) {
            $this->sqllibs->insertRow($this->db, 'channels_server', array(
                "channel_id" => $postVars['editChannelId'],
                "server_id" => $serverId,
            ));
        }
        $this->session->set_flashdata('message', $this->lang->line('channel_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_CHANNEL);
    }

    public function actionEditChannelSort() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('groupId', 'groupChannel', 'groupName'));
        $this->sqllibs->updateRow($this->db, 'groups', array(
            "name" => $postVars['groupName'],
                ), array('id' => $postVars['groupId']));
        $this->sqllibs->deleteRow($this->db, 'groups_channels', array(
            "group_id" => $postVars['groupId']
        ));
        foreach ($postVars['groupChannel'] as $channelId) {
            $this->sqllibs->insertRow($this->db, 'groups_channels', array(
                "group_id" => $postVars['groupId'],
                "channel_id" => $channelId,
            ));
        }
        $this->session->set_flashdata('message', $this->lang->line('sort_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_SORT_GROUP);
    }

    public function actionEditTranscoder() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('tChannel', 'tActive', 'tServerId', 'tInputAddress', 'tOutputAddress', 'tKbps', 'tResolution', 'editTransId'));
        $this->sqllibs->updateRow($this->db, 'transcoder', array(
            "channel_name" => $postVars['tChannel'],
            "active" => $postVars['tActive'],
            "server_id" => $postVars['tServerId'],
            "input_address" => $postVars['tInputAddress'],
            "output_address" => $postVars['tOutputAddress'],
            "kbps" => $postVars['tKbps'],
            "resolution" => $postVars['tResolution']
                ), array('id' => $postVars['editTransId']));
        $this->session->set_flashdata('message', $this->lang->line('transcoder_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_TRANSCODER);
    }

    public function actionEditCategory() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('categoryName', 'categoryImage', 'categoryDescription', 'categoryId'));
        $caInfo = $this->sqllibs->getOneRow($this->db, 'vod_categories', array('category_id' => $postVars['categoryId']));
        $imageFile = $caInfo->image;
        if (isset($_FILES['editCategoryImage']) && !empty($_FILES['editCategoryImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['editCategoryImage'], 0, 200, 200);
            if ($imageFile == null) {
                $imageFile = $caInfo->image;
            }
        }
        $this->sqllibs->updateRow($this->db, 'vod_categories', array(
            "category_name" => $postVars['categoryName'],
            "image" => $postVars['categoryImage'],
            "description" => $postVars['categoryDescription'],
            "image" => $imageFile,
                ), array('category_id' => $postVars['categoryId']));
        $this->session->set_flashdata('message', $this->lang->line('category_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_CATEGORY);
    }

    public function actionEditSubCategory() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('subName', 'subCategory', 'subId'));
        $this->sqllibs->updateRow($this->db, 'vod_subcategories', array(
            "category_id" => $postVars['subCategory'],
            "subcategory_name" => $postVars['subName']
                ), array('subcategory_id' => $postVars['subId']));
        $this->session->set_flashdata('message', $this->lang->line('subcategory_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_CATEGORY);
    }

    public function actionEditContent() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('contentName', 'contentServer', 'contentType', 'contentShowId', 'contentPromoted', 'contentDescription', 'contentCategory', 'contentSubCategory', 'contentPrice', 'contentAge', 'contentImage', 'editContentId'));

        $coInfo = $this->sqllibs->getOneRow($this->db, 'vod_content', array('id' => $postVars['editContentId']));
        $imageFile = $coInfo->image;
        if (isset($_FILES['editContentImage']) && !empty($_FILES['editContentImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['editContentImage'], 0, 200, 200);
            if ($imageFile == null) {
                $imageFile = $caInfo->image;
            }
        }

        $videoFile = '';
        if (isset($_FILES['uploadVideoEdit']) && !empty($_FILES['uploadVideoEdit']['name'])) {
            $videoFile = $this->utils->uploadFile($_FILES['uploadVideoEdit']);
            if ($videoFile == null) {
                $videoFile = $coInfo->link;
            }
        }



        $this->sqllibs->updateRow($this->db, 'vod_content', array(
            "name" => $postVars['contentName'],
            "server" => $postVars['contentServer'],
            "description" => $postVars['contentDescription'],
            "category" => $postVars['contentCategory'],
            "subcategory" => $postVars['contentSubCategory'],
            "price" => $postVars['contentPrice'],
            "age" => $postVars['contentAge'],
            "image" => $imageFile,
            "type" => $postVars['contentType'],
            "show_id" => $postVars['contentShowId'],
            "link" => $videoFile,
            "promoted" => $postVars['contentPromoted']
                ), array('id' => $postVars['editContentId']));
        $this->session->set_flashdata('message', $this->lang->line('content_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_CONTENT);
    }

    public function actionEditShow() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('contentName', 'contentDescription', 'contentCategory', 'contentSubCategory', 'contentPromoted', 'contentAge', 'editShowsId'));

        $shInfo = $this->sqllibs->getOneRow($this->db, 'vod_shows', array('show_id' => $postVars['editShowsId']));
        $imageFile = $shInfo->image;
        if (isset($_FILES['editShowImage']) && !empty($_FILES['editShowImage']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['editShowImage'], 0, 200, 200);
            if ($imageFile == null) {
                $imageFile = $caInfo->image;
            }
        }
        $this->sqllibs->updateRow($this->db, 'vod_shows', array(
            "show_name" => $postVars['contentName'],
            "description" => $postVars['contentDescription'],
            "category" => $postVars['contentCategory'],
            "subcategory" => $postVars['contentSubCategory'],
            "age" => $postVars['contentAge'],
            "image" => $imageFile,
            "promoted" => $postVars['contentPromoted']
                ), array('show_id' => $postVars['editShowsId']));
        $this->session->set_flashdata('message', $this->lang->line('shows_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_VOD_SHOWS);
    }

    public function actionEditPackage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('editPackageId', 'pName', 'channelAll', 'groupChannel', 'category', 'subcategory', 'res_subcategory', 'res_subcategory'));
        $packageId = $postVars['editPackageId'];
        $this->sqllibs->updateRow($this->db, 'packages', array(
            "package_name" => $postVars['pName'],
            "all_channels" => $postVars['channelAll'],
                ), array('package_id' => $postVars['editPackageId']));

        $this->sqllibs->deleteRow($this->db, 'packages_channels', array(
            "package_id" => $postVars['editPackageId']
        ));
        $this->sqllibs->deleteRow($this->db, 'packages_vod', array(
            "package_id" => $postVars['editPackageId']
        ));
        foreach ($postVars['groupChannel'] as $channelId) {
            $this->sqllibs->insertRow($this->db, 'packages_channels', array(
                "package_id" => $packageId,
                "channel_id" => $channelId
            ));
        }
        foreach ($postVars['subcategory'] as $subcategory) {
            $this->sqllibs->insertRow($this->db, 'packages_vod', array(
                "package_id" => $packageId,
                "vod_subcategory_id" => $subcategory,
                "is_free" => 1,
                "is_restricted" => 0
            ));
        }
        foreach ($postVars['res_subcategory'] as $ss) {
            $this->sqllibs->insertRow($this->db, 'packages_vod', array(
                "package_id" => $packageId,
                "vod_subcategory_id" => $ss,
                "is_restricted" => 1,
                "is_free" => 0
            ));
        }
        $this->session->set_flashdata('message', $this->lang->line('package_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_MEMBER_PACKAGE);
    }

    public function actionEditMember() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('memberBill', 'memberName', 'memberEmail', 'memberPhone', 'memberPassword', 'memberAddress', 'memberApprove', 'memberPackage', 'memberId', 'memberBalance'));
        $this->sqllibs->updateRow($this->db, 'members', array(
            "billing_id" => $postVars['memberBill'],
            "name" => $postVars['memberName'],
            "email" => $postVars['memberEmail'],
            "phone" => $postVars['memberPhone'],
            "password" => $postVars['memberPassword'],
            "address" => $postVars['memberAddress'],
            "approved" => $postVars['memberApprove'],
            "balance" => $postVars['memberBalance']
                ), array('member_id' => $postVars['memberId']));

        $this->sqllibs->deleteRow($this->db, 'members_packages', array(
            "member_id" => $postVars['memberId']
        ));
        $memberId = $this->sqllibs->insertRow($this->db, 'members_packages', array(
            "member_id" => $postVars['memberId'],
            "package_id" => $postVars['memberPackage']
        ));

        $this->session->set_flashdata('message', $this->lang->line('member_message_edit_success'));
        $this->utils->redirectPage(ADMIN_PAGE_MEMBER);
    }

    public function actionDeleteDevice($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $deviceInfo = $this->sqllibs->getOneRow($this->db, 'members_devices', array('id' => $id));
        $memberId = $deviceInfo->member_id;
        $this->sqllibs->deleteRow($this->db, 'members_devices', array('id' => $id));
        redirect(base_url() . ADMIN_PAGE_DEVICE . "/" . $memberId);
    }

    public function actionDeleteEmployee($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'employees', array(
            "id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_EMPLOYEE);
    }

    public function actionDeleteServer($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'servers', array(
            "id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_CHANNEL_SERVER);
    }

    public function actionDeleteChannel($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'channels', array(
            "channel_id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_CHANNEL_CHANNEL);
    }

    public function actionDeleteChannelSort($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'groups', array(
            "id" => $id
        ));
        $this->sqllibs->deleteRow($this->db, 'groups_channels', array(
            "group_id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_CHANNEL_SORT_GROUP);
    }

    public function actionDeleteTranscoder($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'transcoder', array(
            "id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_CHANNEL_TRANSCODER);
    }

    public function actionDeleteCategory($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'vod_categories', array(
            "category_id" => $id
        ));
        $this->sqllibs->deleteRow($this->db, 'vod_subcategories', array(
            "category_id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_VOD_CATEGORY);
    }

    public function actionDeleteSubCategory($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'vod_subcategories', array(
            "subcategory_id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_VOD_CATEGORY);
    }

    public function actionDeleteContent($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'vod_content', array(
            "id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_VOD_CONTENT);
    }

    public function actionDeleteShow($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'vod_shows', array(
            "show_id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_VOD_SHOWS);
    }

    public function actionDeletePackage($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'packages', array(
            "package_id" => $id
        ));
        $this->sqllibs->deleteRow($this->db, 'packages_channels', array(
            "package_id" => $id
        ));
        $this->sqllibs->deleteRow($this->db, 'packages_vod', array(
            "package_id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_MEMBER_PACKAGE);
    }

    public function actionDeleteMember($id) {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $this->sqllibs->deleteRow($this->db, 'members', array(
            "member_id" => $id
        ));
        $this->sqllibs->deleteRow($this->db, 'members_packages', array(
            "member_id" => $id
        ));
        redirect(base_url() . ADMIN_PAGE_MEMBER);
    }

    public function actionLogout() {
        $this->session->set_userdata(array("adminLogin" => ""));
        $this->utils->redirectPage(ADMIN_PAGE_HOME);
    }

}
