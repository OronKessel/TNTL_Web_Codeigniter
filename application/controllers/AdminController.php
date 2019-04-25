<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'BaseController.php';

class AdminController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function isCheckLicense() {
        $licenses = $this->sqllibs->rawSelectSql($this->db, "select * from license");
        if (count($licenses) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
      Parameter Example
      $data = array('post_id'=>'12345','post_title'=>'A Blog post');
      $target = 'single tocken id or topic name';
      or
      $target = array('token1','token2','...'); // up to 1000 in one request
     */

    public function sendMessage($type, $title, $message, $target) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = 'AAAAnnjJENU:APA91bEz-kksbhA_LljFhbq_FGBQ0LVCUCF8mRfYm_RfWZBDysxYewsoRBrDY4AXXV9WlN5GQ9icFwgvLgRrDMi-yIr0gLBX_AiTWvb5oXHoBuyZZLGFthNoabOCOAOZZsFSmgHDFkYJ';
        $fields = array();
        $data = array();
        $data['type'] = $type;
        $data['title'] = $title;
        $data['message'] = $message;
        $fields['data'] = $data;
        if (is_array($target)) {
            $fields['registration_ids'] = $target;
        } else {
            $fields['to'] = $target;
        }
        //header with content_type api key
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    public function actionSendPush() {
        $postVars = $this->utils->inflatePost(array('pushUserType', 'pushApplicationId', 'pushType'));
        $token = null;
        if ($postVars['pushUserType'] == '1') { //One User
            $memberId = $_POST['pushMemberID'];
            $memberInfo = $this->sqllibs->getOneRow($this->db, 'members', array('member_id' => $memberId));
            $token = $memberInfo->token;
        } else {
            $members = $this->sqllibs->selectAllRows($this->db, 'members');
            $ids = array();
            foreach ($members as $member) {
                $ids[] = $member->token;
            }
            $token = $ids;
        }
        $type = '';
        $title = '';
        $message = 'test';
        $imageFile = 'none';
        if ($postVars['pushType'] == '1') {//Command
            $type = 'Command';
            $title = $_POST['pushCommand'];
            if ($title == '5') {
                if (isset($_FILES['uploadLogo']) && !empty($_FILES['uploadLogo']['name'])) {
                    $imageFile = $this->utils->uploadImage($_FILES['uploadLogo'], 0, 200, 150);
                }
                $message = array('text' => $_POST['pushMessage'], 'image' => $imageFile, 'title' => $_POST['pushTitle']);
            }
        } else {//Text
            $type = 'Text';
            $title = $_POST['pushTitle'];
            $message = $_POST['pushMessage'];
        }
        $this->sendMessage($type, $title, $message, $token);
        $this->session->set_flashdata('message', "Push sent success");
        $this->utils->redirectPage('index.php/AdminController/pushPage');
    }

    public function actionUpdateAds() {
        $postVars = $this->utils->inflatePost(array('companyName'));
        $companyInfo = $this->sqllibs->getOneRow($this->db, "application_ads", array('id' => 1));
        $imageFile = $companyInfo->box_left_banner;
        if (isset($_FILES['uploadAd1']) && !empty($_FILES['uploadAd1']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['uploadAd1'], 0, 100, 100);
        }

        $imageFile1 = $companyInfo->box_right_banner;
        if (isset($_FILES['uploadAd2']) && !empty($_FILES['uploadAd2']['name'])) {
            $imageFile1 = $this->utils->uploadImage($_FILES['uploadAd2'], 1, 100, 100);
        }

        $imageFile2 = $companyInfo->box_top_banner;
        if (isset($_FILES['uploadAd3']) && !empty($_FILES['uploadAd3']['name'])) {
            $imageFile2 = $this->utils->uploadImage($_FILES['uploadAd3'], 2, 100, 100);
        }

        $imageFile3 = $companyInfo->mobile_bottom_banner;
        if (isset($_FILES['uploadAd4']) && !empty($_FILES['uploadAd4']['name'])) {
            $imageFile3 = $this->utils->uploadImage($_FILES['uploadAd4'], 3, 100, 100);
        }

        $info = $this->sqllibs->updateRow($this->db, 'application_ads', array(
            "box_left_banner" => $imageFile,
            "box_right_banner" => $imageFile1,
            "box_top_banner" => $imageFile2,
            "mobile_bottom_banner" => $imageFile3,
                ), array('id' => 1));
        $this->utils->redirectPage('index.php/AdminController/adsPage');
    }

    public function actionUpdateCompany() {
        $postVars = $this->utils->inflatePost(array('companyName'));
        $companyInfo = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $imageFile = $companyInfo->logo_upload;
        if (isset($_FILES['uploadLogo']) && !empty($_FILES['uploadLogo']['name'])) {
            $imageFile = $this->utils->uploadImage($_FILES['uploadLogo'], 0, 100, 100);
        }

        $info = $this->sqllibs->updateRow($this->db, 'company', array(
            "name" => $postVars['companyName'],
            "logo_upload" => $imageFile
                ), array('id' => 1));
        $this->utils->redirectPage('index.php/AdminController/companyPage');
    }

    public function actionUpdateAppInfo() {
        $postVars = $this->utils->inflatePost(array('boxVersion', 'mobileVersion', 'loadTime', 'inactiveTime', 'firebaseID'));
        $this->sqllibs->updateRow($this->db, 'application', array(
            "box_version" => $postVars['boxVersion'],
            "mobile_version" => $postVars['mobileVersion'],
            "player_load_timeout" => $postVars['loadTime'],
            "player_inactivity_timeout" => $postVars['inactiveTime'],
            "firebase_id" => $postVars['firebaseID']
                ), array('id' => 1));
        $this->utils->redirectPage('index.php/AdminController/applicationPage');
    }

    public function pushPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Push", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));

        $this->load->view('view_main', $data);
    }

    public function adsPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $adsInfo = $this->sqllibs->getOneRow($this->db, "application_ads", array('id' => 1));
        $data = $this->getViewParameters("Ads", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $data['info'] = $adsInfo;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function companyPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $companyInfo = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $data = $this->getViewParameters("Company", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $data['info'] = $companyInfo;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function applicationPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $appInfo = $this->sqllibs->getOneRow($this->db, "application", array('id' => 1));
        $data = $this->getViewParameters("Application", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $data['info'] = $appInfo;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function actionAddLicense() {
        $postVars = $this->utils->inflatePost(array('license'));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://192.168.1.117:88/license/index.php/AdminController/verifyLicense");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "license=" . $postVars['license']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $result = json_decode($server_output, true);
        if ($result['result'] == 200) {
            $id = $this->sqllibs->insertRow($this->db, 'license', array(
                "license_serial" => $postVars['license']
            ));
            $this->session->set_flashdata('message', "License Activated");
        }
        $this->utils->redirectPage(ADMIN_PAGE_EMPLOYEE);
        curl_close($ch);
    }

    public function index() {
        if ($this->isLogin() == 1) {
            $userInfo = $this->session->userInfo;
            if ($userInfo->is_admin == 1) {
                $this->utils->redirectPage(ADMIN_PAGE_EMPLOYEE);
                return;
            } else {
                if ($userInfo->channels_management == 1)
                    $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_CHANNEL);
                else if ($userInfo->vod_management == 1)
                    $this->utils->redirectPage(ADMIN_PAGE_VOD_CATEGORY);
                else if ($userInfo->members_management == 1)
                    $this->utils->redirectPage(ADMIN_PAGE_MEMBER);
                return;
            }
            return;
        } else {
            $data = $this->setMessages(array());
            $this->load->view('login', $data);
        }
    }

    public function registerPage() {
        if ($this->isLogin() == 1) {
            $this->utils->redirectPage(ADMIN_PAGE_DASHBOARD);
        } elseif ($this->isLogin() == 2) {
            $this->utils->redirectPage(ADMIN_PAGE_PRO_PROFILE);
        } else {
            $data = $this->setMessages(array());
            $this->load->view('register', $data);
        }
    }

    public function welcomePage() {
        $data = array();
        $this->load->view('view_success_signup', $data);
    }

    public function actionRegister() {
        $postVars = $this->utils->inflatePost(array('user', 'email', 'phone', 'pw'));
        if ($this->sqllibs->isExist($this->db, 'tbl_prof', array("email" => $postVars['email']))) {
            $result['result'] = 400;
            $result['status'] = 'false';
            $result['message'] = "Email already registered";
            echo json_encode($result);
            return;
        }
        $random = $this->generateRandomString();
        $id = $this->sqllibs->insertRow($this->db, 'tbl_prof', array(
            "name" => $postVars['user'],
            "email" => $postVars['email'],
            "password" => $postVars['pw'],
            "phone" => $postVars['phone'],
            "activate_code" => $random,
            "email_verify" => 0,
        ));
        $result = array();
        $result['status'] = 'true';
        $result['result'] = 200;
//Send email
        $msg = "Please <a href='" . base_url() . "index.php/PrfController/verifyEmail/" . $id . "/" . $random . "'>click here </a>to verify email.";
//        mail($postVars['email'], 'Verify Email', $msg);
        $this->sendEmail("Verify Email", $postVars['email'], "Verify Email", $msg);
        echo json_encode($result);
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function sendEmail($tt, $to, $title, $msg) {
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = true;
        $this->email->initialize($config);

        $this->email->set_mailtype("html");


        $this->email->from('info@consult.com', $tt);
        $this->email->to($to);

        $this->email->subject($title);
        $this->email->message($msg);

        $this->email->send();
    }

    public function testEmail() {
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = true;
        $this->email->initialize($config);



        $this->email->from('info@consult.com', 'Your Name');
        $this->email->to('pgyhw718@hotmail.com');

        $this->email->subject('Email Test');
        $this->email->message('Testing the email class.');

        $this->email->send();
    }

    public function dashboardPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Dashboard", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $data['countAccount'] = count($this->sqllibs->selectAllRows($this->db, 'members'));
        $data['countChannels'] = count($this->sqllibs->selectAllRows($this->db, 'channels'));
        $data['countVods'] = count($this->sqllibs->selectAllRows($this->db, 'vod_content'));
        $data['countDevices'] = count($this->sqllibs->selectAllRows($this->db, 'members_devices'));
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function employeePage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Employee", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $employees = $this->sqllibs->selectAllRows($this->db, 'employees', array('is_admin' => 0));
        $data['employees'] = $employees;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function employeeContactPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Employee_Contact", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $employees = $this->sqllibs->selectAllRows($this->db, 'employees', array('is_admin' => 0));
        $data['employees'] = $employees;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function serverPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Server", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $servers = $this->sqllibs->selectAllRows($this->db, 'servers');
        $data['servers'] = $servers;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function channelPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Channel", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $lives = $this->sqllibs->selectAllRows($this->db, 'servers', array('type' => 'live'));
        $dvrs = $this->sqllibs->selectAllRows($this->db, 'servers', array('type' => 'dvr'));
        $channels = $this->sqllibs->selectAllRows($this->db, 'channels');

        foreach ($channels as $channel) {
            $serverids = $this->sqllibs->selectAllRows($this->db, 'channels_server', array('channel_id' => $channel->channel_id));
            $liveAddresses = "";
            $dvrAddresses = "";

            foreach ($serverids as $serverid) {
                $liveInfo = $this->sqllibs->getOneRow($this->db, 'servers', array('id' => $serverid->server_id, 'type' => 'live'));
                if ($liveInfo == null)
                    continue;
                $liveAddresses = $liveAddresses . $liveInfo->address . " ";
                $channel->liveInfos[] = $liveInfo;
            }
            $channel->liveAddresses = $liveAddresses;

            foreach ($serverids as $serverid) {
                $dvrInfo = $this->sqllibs->getOneRow($this->db, 'servers', array('id' => $serverid->server_id, 'type' => 'dvr'));
                if ($dvrInfo == null)
                    continue;
                $dvrAddresses = $dvrAddresses . $dvrInfo->address . " ";
                $channel->dvrInfos[] = $dvrInfo;
            }
            $channel->dvrAddresses = $dvrAddresses;
        }

        $data['channels'] = $channels;
        $data['lives'] = $lives;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $data['dvrs'] = $dvrs;
        $this->load->view('view_main', $data);
    }

    public function channelsortPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Channel_Sort", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $groups = $this->sqllibs->selectAllRows($this->db, 'groups');
        $channels = $this->sqllibs->selectAllRows($this->db, 'channels');
        foreach ($groups as $group) {
            $cids = $this->sqllibs->selectAllRows($this->db, 'groups_channels', array('group_id' => $group->id));
            $channelNames = "";
            foreach ($cids as $cid) {
                $chInfo = $this->sqllibs->getOneRow($this->db, 'channels', array('channel_id' => $cid->channel_id));
                $channelNames = $channelNames . $chInfo->name . " ";
                $group->chInfos[] = $chInfo;
            }
            $group->channelNames = $channelNames;
        }
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $data['groups'] = $groups;
        $data['channels'] = $channels;
        $this->load->view('view_main', $data);
    }

    public function epgPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data['license'] = $this->isCheckLicense();
        $channels = $this->sqllibs->selectAllRows($this->db, 'channels');
        $data = $this->getViewParameters("EPG", "Admin");
        $data = $this->setMessages($data);
        $data['channels'] = $channels;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function transcoderPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Transcoder", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $transcodes = $this->sqllibs->rawSelectSql($this->db, 'select A.*,B.address as server_addr from transcoder as A left join servers as B on A.server_id=B.id');
        $servers = $this->sqllibs->selectAllRows($this->db, 'servers', array('type' => 'transcoder'));
        $data['transcoders'] = $transcodes;
        $data['servers'] = $servers;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function categoryPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Category", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $subCategories = $this->sqllibs->rawSelectSql($this->db, 'select A.*,B.category_name as mcategory from vod_subcategories as A left join vod_categories as B on A.category_id=B.category_id');
        $categorys = $this->sqllibs->selectAllRows($this->db, 'vod_categories');
        $data['categorys'] = $categorys;
        $data['subCategories'] = $subCategories;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
    }

    public function contentPage() {
        if ($this->isLogin() == 0) {
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $data = $this->getViewParameters("Content", "Admin");
        $data = $this->setMessages($data);
        $data['license'] = $this->isCheckLicense();
        $servers = $this->sqllibs->selectAllRows($this->db, 'servers');
        $shows = $this->sqllibs->selectAllRows($this->db, 'vod_shows');
        $contents = $this->sqllibs->rawSelectSql($this->db, 'select A.*,B.category_name as mcategory,C.address as server_addr,D.subcategory_name as sname, E.show_name AS showname from vod_content as A left join vod_categories as B on A.category=B.category_id left join servers as C on A.server=C.id left join vod_subcategories as D on A.subcategory=D.subcategory_id left join vod_shows as E on A.show_id=E.show_id');
        $categorys = $this->sqllibs->selectAllRows($this->db, 'vod_categories');
        $data['contents'] = $contents;
        $data['servers'] = $servers;
        $data['shows'] = $shows;
        $data['categorys'] = $categorys;
        $data['company'] = $this->sqllibs->getOneRow($this->db, "company", array('id' => 1));
        $this->load->view('view_main', $data);
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
        $contents = $this->sqllibs->rawSelectSql($this->db, 'select A.*,B.category_name as mcategory,D.subcategory_name as sname from vod_shows as A left join vod_categories as B on A.category=B.category_id left join vod_subcategories as D on A.subcategory=D.subcategory_id');
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
        $members = $this->sqllibs->rawSelectSql($this->db, 'select A.*,B.package_id as package_id from members as A left join members_packages as B on A.member_id=B.member_id');
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

    public function actionLogin() {
        if ($this->utils->isEmptyPost(array('user', 'pw'))) {
            $this->session->set_flashdata('errorMessage', "Please fill input.");
            $this->utils->redirectPage(ADMIN_PAGE_HOME);
            return;
        }
        $postVars = $this->utils->inflatePost(array('user', 'pw'));
        $pw = md5($postVars['pw']);
        if ($this->sqllibs->isExist($this->db, 'employees', array("username" => $postVars['user'], "password" => $pw))) {
            $userInfo = $this->sqllibs->getOneRow($this->db, 'employees', array("username" => $postVars['user'], "password" => $pw));
            if ($userInfo->is_admin == 1) { // Admin
                $this->session->set_userdata(array("level" => "1"));
            } else {
                $this->session->set_userdata(array("level" => "0"));
            }
            $this->session->set_userdata(array("adminLogin" => "1"));
            $this->session->set_userdata(array("adminName" => $userInfo->username));
            $this->session->set_userdata(array("userInfo" => $userInfo));
            if ($userInfo->is_admin == 1) {
                $this->utils->redirectPage(ADMIN_PAGE_EMPLOYEE);
                return;
            } else {
                if ($userInfo->channels_management == 1)
                    $this->utils->redirectPage(ADMIN_PAGE_CHANNEL_CHANNEL);
                else if ($userInfo->vod_management == 1)
                    $this->utils->redirectPage(ADMIN_PAGE_VOD_CATEGORY);
                else if ($userInfo->members_management == 1)
                    $this->utils->redirectPage(ADMIN_PAGE_MEMBER);
                return;
            }
            return;
        }
        $this->session->set_flashdata('errorMessage', "Login Fail");
        $this->utils->redirectPage(ADMIN_PAGE_HOME);
    }

    public function actionLogout() {
        $this->session->set_userdata(array("adminLogin" => ""));
        $this->utils->redirectPage(ADMIN_PAGE_HOME);
    }

}
