<?php

header('Content-Type: application/json');
defined('BASEPATH') or exit('No direct script access allowed');
require 'BaseController.php';

class WebserviceController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function serviceLogin() {
        $postVars = $this->utils->inflatePost(array('user', 'pw', 'sn', 'token'));
        $result = array();
        $pw = $postVars['pw']; //md5($postVars['pw']);
        $isDeviceExist = $this->sqllibs->isExist($this->db, 'members_devices', array("device_name" => $postVars['sn']));
        if ($isDeviceExist) {
            $deviceLog = $this->sqllibs->getOneRow($this->db, 'members_devices', array("device_name" => $postVars['sn']));
            $userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("member_id" => $deviceLog->member_id, "password_md5" => $pw));
            $info = $this->sqllibs->updateRow($this->db, 'members', array(
                "token" => $postVars['token']
                    ), array('member_id' => $userInfo->member_id));
            $result['userInfo'] = $userInfo;
            $result['result'] = 200;
            echo json_encode($result, JSON_NUMERIC_CHECK);
            return;
        }
        $result['result'] = 400;
        $result['message'] = "Device does not exist";
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function serviceLoginMobile() {
        $postVars = $this->utils->inflatePost(array('user', 'pw', 'token'));
        $result = array();
        $pw = $postVars['pw']; //md5($postVars['pw']);
        $userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("email" => $postVars['user'], "password_md5" => $pw));
        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' order by list_order asc");
        $info = $this->sqllibs->updateRow($this->db, 'members', array(
            "token" => $postVars['token']
                ), array('member_id' => $userInfo->member_id));
        foreach ($channels as $channel) {
            $channel_name = strtolower(str_replace(" ", "_", $channel->name));
            $channel->dvrDir = $channel_name;
            $channel->transcode_levels = 0;
        }
        $result['userInfo'] = $userInfo;
        $result['channels'] = $channels;
        $result['result'] = 200;
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function serviceHome() {
        $postVars = $this->utils->inflatePost(array('memberId'));
        $result = array();
        $vods = $this->sqllibs->rawSelectSql($this->db, "select * from vod_content where processed='yes' and type='movie' limit 0,9");
        $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_shows limit 0,9");
        $allChannels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' order by if(channel_number = '' or channel_number is null,1,0),length(channel_number),channel_number");
        $memberFav = $this->sqllibs->rawSelectSql($this->db, "select * from members_favorite_channels where member_id='" . $postVars['memberId'] . "'");
        $channels = array();
        $containChannels = "";
        foreach ($memberFav as $fav) {
            $containChannels = $containChannels . $fav->channel_id . ",";
            $channels[] = $this->sqllibs->getOneRow($this->db, 'channels', array("channel_id" => $fav->channel_id));
        }
        $containChannels = substr($containChannels, 0, -1);
        $remainCount = 10 - count($channels);
        $otherChannels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id limit 0," . $remainCount);
        if ($containChannels != '')
            $otherChannels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id not in (" . $containChannels . ") order by list_order limit 0," . $remainCount);
        foreach ($otherChannels as $ch) {
            $channels[] = $ch;
        }
        foreach ($channels as $channel) {
            $channel_name = strtolower(str_replace(" ", "_", $channel->name));
            $channel->dvrDir = $channel_name;
            $channel->transcode_levels = 0;
        }
        foreach ($allChannels as $channel) {
            $channel_name = strtolower(str_replace(" ", "_", $channel->name));
            $channel->dvrDir = $channel_name;
            $channel->transcode_levels = 0;
        }
        $result['result'] = 200;
        $result['vods'] = $vods;
        $result['shows'] = $shows;
        $result['channels'] = $channels;
        $result['allChannels'] = $allChannels;
        echo json_encode($result);
    }

    public function serviceLoadAllChannel() {
        $postVars = $this->utils->inflatePost(array('memberId'));
        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' order by list_order asc");
        foreach ($channels as $channel) {
            $channel_name = strtolower(str_replace(" ", "_", $channel->name));
            $channel->dvrDir = $channel_name;
            $channel->transcode_levels = 0;
        }
        $result['channels'] = $channels;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceLoadChannel() {
        $postVars = $this->utils->inflatePost(array('memberId', 'isFav', 'keyword', 'sort'));
        $result = array();
        $channels = array();
        $groups = array();
        if ($postVars['isFav'] == "false") {
            if ($postVars['sort'] == '0') { //AZ
                $result['type'] = "Channel";
                if ($postVars['keyword'] == '') {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' order by name asc");
                } else {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' order by name asc");
                }
            } else if ($postVars['sort'] == '1') {  //ZA
                $result['type'] = "Channel";
                if ($postVars['keyword'] == '') {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' order by name desc");
                } else {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' order by name desc");
                }
            } else if ($postVars['sort'] == '2') {  //Number Ascending
                $result['type'] = "Channel";
                if ($postVars['keyword'] == '') {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' order by if(channel_number = '' or channel_number is null,1,0),length(channel_number),channel_number");
                } else {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' order by if(channel_number = '' or channel_number is null,1,0),length(channel_number),channel_number");
                }
            } else if ($postVars['sort'] == '3') {  //Number Descending
                $result['type'] = "Channel";
                if ($postVars['keyword'] == '') {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' order by if(channel_number = '' or channel_number is null,1,0),length(channel_number) desc,channel_number desc");
                } else {
                    $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' order by if(channel_number = '' or channel_number is null,1,0),length(channel_number) desc,channel_number desc");
                }
            } else if ($postVars['sort'] == '4') {  //Group
                $result['type'] = "Group";
                $groups = $this->sqllibs->rawSelectSql($this->db, "select * from groups order by name asc");
                foreach ($groups as $group) {
                    $gChannels = $this->sqllibs->selectAllRows($this->db, 'groups_channels', array('group_id' => $group->id));
                    $chInfos = array();
                    foreach ($gChannels as $ch) {
                        $chs = array();
                        if ($postVars['keyword'] == '') {
                            $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id='" . $ch->channel_id . "'");
                        } else {
                            $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' abd name like '%" . $postVars['keyword'] . "%' and channel_id='" . $ch->channel_id . "'");
                        }
                        foreach ($chs as $channel) {
                            $channel_name = strtolower(str_replace(" ", "_", $channel->name));
                            $channel->dvrDir = $channel_name;
                            $channel->transcode_levels = 0;
                        }
                        if (count($chs) > 0)
                            $chInfos[] = $chs[0];
                    }
                    $group->channels = $chInfos;
                }
            }
            else if ($postVars['sort'] == '5') {  //Group
                $result['type'] = "Group";
                $groups = $this->sqllibs->rawSelectSql($this->db, "select * from groups order by name desc");
                foreach ($groups as $group) {
                    $gChannels = $this->sqllibs->selectAllRows($this->db, 'groups_channels', array('group_id' => $group->id));
                    $chInfos = array();
                    foreach ($gChannels as $ch) {
                        $chs = array();
                        if ($postVars['keyword'] == '') {
                            $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' abd channel_id='" . $ch->channel_id . "'");
                        } else {
                            $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' and channel_id='" . $ch->channel_id . "'");
                        }
                        foreach ($chs as $channel) {
                            $channel_name = strtolower(str_replace(" ", "_", $channel->name));
                            $channel->dvrDir = $channel_name;
                            $channel->transcode_levels = 0;
                        }
                        if (count($chs) > 0)
                            $chInfos[] = $chs[0];
                    }
                    $group->channels = $chInfos;
                }
            }
        }
        else {
            $memberChannels = $this->sqllibs->selectAllRows($this->db, 'members_favorite_channels', array('member_id' => $postVars['memberId']));
            if (count($memberChannels) > 0) {
                $filterFav = "";
                foreach ($memberChannels as $favId) {
                    $filterFav = "'" . $favId->channel_id . "',";
                }
                $filterFav = substr($filterFav, 0, -1);
                if ($postVars['sort'] == '0') { //AZ
                    $result['type'] = "Channel";
                    if ($postVars['keyword'] == '') {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id in (" . $filterFav . ") order by name asc");
                    } else {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' and channel_id in (" . $filterFav . ") order by name asc");
                    }
                } else if ($postVars['sort'] == '1') {  //ZA
                    $result['type'] = "Channel";
                    if ($postVars['keyword'] == '') {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id in (" . $filterFav . ") order by name desc");
                    } else {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' and channel_id in (" . $filterFav . ") order by name desc");
                    }
                } else if ($postVars['sort'] == '2') {  //ZA
                    $result['type'] = "Channel";
                    if ($postVars['keyword'] == '') {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id in (" . $filterFav . ") order by channel_number asc");
                    } else {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' and channel_id in (" . $filterFav . ") order by channel_number asc");
                    }
                } else if ($postVars['sort'] == '3') {  //ZA
                    $result['type'] = "Channel";
                    if ($postVars['keyword'] == '') {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id in (" . $filterFav . ") order by channel_number desc");
                    } else {
                        $channels = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' and channel_id in (" . $filterFav . ") order by channel_number desc");
                    }
                } else if ($postVars['sort'] == '4') {  //Group
                    $result['type'] = "Group";
                    $groups = $this->sqllibs->rawSelectSql($this->db, "select * from groups order by name asc");
                    foreach ($groups as $group) {
                        $gChannels = $this->sqllibs->selectAllRows($this->db, 'groups_channels', array('group_id' => $group->id));
                        $chInfos = array();
                        foreach ($gChannels as $ch) {
                            $chs = array();
                            if ($postVars['keyword'] == '') {
                                $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id='" . $ch->channel_id . "' and channel_id in (" . $filterFav . ")");
                            } else {
                                $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' and channel_id='" . $ch->channel_id . "' and channel_id in (" . $filterFav . ")");
                            }
                            if (count($chs) > 0)
                                $chInfos[] = $chs[0];
                        }
                        $group->channels = $chInfos;
                    }
                }
                else if ($postVars['sort'] == '5') {  //Group
                    $result['type'] = "Group";
                    $groups = $this->sqllibs->rawSelectSql($this->db, "select * from groups order by name desc");
                    foreach ($groups as $group) {
                        $gChannels = $this->sqllibs->selectAllRows($this->db, 'groups_channels', array('group_id' => $group->id));
                        $chInfos = array();
                        foreach ($gChannels as $ch) {
                            $chs = array();
                            if ($postVars['keyword'] == '') {
                                $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and channel_id='" . $ch->channel_id . "' and channel_id in (" . $filterFav . ")");
                            } else {
                                $chs = $this->sqllibs->rawSelectSql($this->db, "select * from channels where active='yes' and name like '%" . $postVars['keyword'] . "%' and channel_id='" . $ch->channel_id . "' and channel_id in (" . $filterFav . ")");
                            }
                            if (count($chs) > 0)
                                $chInfos[] = $chs[0];
                        }
                        $group->channels = $chInfos;
                    }
                }
            }
        }
        foreach ($channels as $channel) {
            $channel_name = strtolower(str_replace(" ", "_", $channel->name));
            $channel->dvrDir = $channel_name;
            $channel->transcode_levels = 0;
        }
        $result['channels'] = $channels;
        $result['groups'] = $groups;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetAllChannel() {
        $channels = $this->sqllibs->selectAllRows($this->db, 'channels');
        $result = array();
        $result['channels'] = $channels;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function authenticateChannel() {
        ///$postVars = $this->utils->inflatePost(array('channel_id', 'device_id', 'token'));
        $postVars = $this->utils->inflatePost(array('channelId'));
        $result = array();
        //$pw = md5($postVars['pw']);
        //$userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("email" => $postVars['user'],"password" => $pw));
        //$channels = $this->sqllibs->rawSelectSql($this->db,"select * from channels order by name asc");
        //$result['userInfo'] = $userInfo;
        //$result['channels'] = $channels;
        //$result['result'] = 200;

        $channelInfo = $this->sqllibs->getOneRow($this->db, 'channels', array("channel_id" => $postVars['channelId']));
        $channelInfo->transcode_levels = 0;
        $tLevels = '';
        if (substr($channelInfo->input_address, 0, strlen("trans-")) === "trans-") {
            $test = explode("-", explode(":rtmp://", $channelInfo->input_address)[0])[1];
            if (intval($test) > 0) {
                $tLevels = $test;
                $input_address = explode("trans-$tLevels:", $channelInfo->input_address)[1];
                $channelInfo->transcode_levels = $test;
                //echo("Levels IN: ".$tLevels." and address: ".$input_address);
            }
        }


        $lives = $this->sqllibs->selectAllRows($this->db, 'servers', array('type' => 'live'));
        $liveServersChannel = $this->sqllibs->rawSelectSql($this->db, "select server_id from channels_server where channel_id=" . $postVars['channelId']);

        if (substr($channelInfo->input_address, 0, strlen("http://")) === "http://" && empty(array_intersect($lives, $liveServersChannel))) {
            $channelInfo->stream_url = $channelInfo->input_address;
        } else {
            $finalIp = "null";
            $lBalance = @file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/live/index.php/channel_' . $postVars['channelId']);

            //$lBalance = @file_get_contents('http://69.79.26.3/live/index.php/channel_'.$postVars['channelId']);
            if ($lBalance) {
                $json = json_decode($lBalance, true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    $finalIp = $json[0]['address'];
                    if (intval($tLevels) < 1) {
                        $channelInfo->stream_url = 'http://' . $finalIp . '/live/channel_' . $postVars['channelId'] . '/index.m3u8';
                    } else {
                        $channelInfo->stream_url = 'http://' . $finalIp . '/live' . $tLevels . '/channel_' . $postVars['channelId'] . '.m3u8';
                    }
                } else {
                    $channelInfo->stream_url = 'null';
                }
            } else {
                $channelInfo->stream_url = 'null';
            }
        }




        $channelInfo->dvrDir = "null";
        $dBalance = @file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/dvr/index.php/channel_' . $postVars['channelId']);
        //$lBalance = @file_get_contents('http://69.79.26.3/live/index.php/channel_'.$postVars['channelId']);
        if ($dBalance) {
            $json = json_decode($dBalance, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $dFinalIp = $json[0]['address'];
                $channel_name = strtolower(str_replace(" ", "_", $channelInfo->name));
                $channelInfo->dvrDir = "http://" . $dFinalIp . "/hls/" . $channel_name;
            }
        }



        $result['channel'] = $channelInfo;
        $result['result'] = 200;
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function authenticateVod() {
        ///$postVars = $this->utils->inflatePost(array('channel_id', 'device_id', 'token'));
        $postVars = $this->utils->inflatePost(array('vodId'));
        $result = array();
        //$pw = md5($postVars['pw']);
        //$userInfo = $this->sqllibs->getOneRow($this->db, 'members', array("email" => $postVars['user'],"password" => $pw));
        //$channels = $this->sqllibs->rawSelectSql($this->db,"select * from channels order by name asc");
        //$result['userInfo'] = $userInfo;
        //$result['channels'] = $channels;
        //$result['result'] = 200;

        $vodInfo = $this->sqllibs->getOneRow($this->db, 'vod_content', array("id" => $postVars['vodId']));
        $serverInfo = $this->sqllibs->getOneRow($this->db, 'servers', array("id" => $vodInfo->server));
        /* $tLevels = '';
          if (substr($channelInfo->input_address, 0, strlen("trans-")) === "trans-") {
          $test = explode("-",explode(":rtmp://",$channelInfo->input_address)[0])[1];
          if (intval($test) > 0) {
          $tLevels = $test;
          $input_address = explode("trans-$tLevels:",$input_address)[1];
          //echo("Levels IN: ".$tLevels." and address: ".$input_address);
          }
          }

          $finalIp = "null";
          //$lBalance = @file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/live/index.php/channel_'.$postVars['channelId']);
          $lBalance = @file_get_contents('http://69.79.26.3/live/index.php/channel_'.$postVars['channelId']);
          if ($lBalance) {
          $json=json_decode($lBalance, true);
          if (json_last_error() == JSON_ERROR_NONE) {
          $finalIp = $json[0]['address'];
          }
          }

          if (intval($tLevels) <= 1) {
          $channelInfo->stream_url = 'http://'.$finalIp.'/live/channel_'.$postVars['channelId'].'/index.m3u8';
          }
          else {
          $channelInfo->stream_url = 'http://'.$finalIp.'/live'.$tLevels.'/channel_'.$postVars['channelId'].'.m3u8';
          } */
        $stream_url = "";
        //$filenameArray = explode("/", $vodInfo->link);
        //$filenameWithExtension = end($filenameArray);
        $filenameWithExtension = $vodInfo->link;
        $filenameWithoutExtension = explode(".mp4", $filenameWithExtension)[0];
        if ($vodInfo->transcode_levels === null || intval($vodInfo->transcode_levels) < 1) {
            $stream_url = "http://" . $serverInfo->address . "/hls/" . $filenameWithExtension . "/master.m3u8";
        } else if (intval($vodInfo->transcode_levels) == 1) {
            $stream_url = "http://" . $serverInfo->address . "/hls/" . $filenameWithoutExtension . "_hi.mp4/master.m3u8";
        } else if (intval($vodInfo->transcode_levels) == 2) {
            $stream_url = "http://" . $serverInfo->address . "/hls/" . $filenameWithoutExtension . "_,low,hi,.mp4.urlset/master.m3u8";
        } else { // == 3
            $stream_url = "http://" . $serverInfo->address . "/hls/" . $filenameWithoutExtension . "_,low,med,hi,.mp4.urlset/master.m3u8";
        }

        $vodInfo->stream_url = $stream_url;
        $result['vod'] = $vodInfo;
        $result['server'] = $serverInfo;
        $result['result'] = 200;
        echo json_encode($result, JSON_NUMERIC_CHECK);
    }

    public function serviceGetEpgInfo() {
        $postVars = $this->utils->inflatePost(array('channelId', 'timeNow'));
        $timeNow = "0";
        if (strlen($postVars['timeNow']) > 0) {
            $timeNow = $postVars['timeNow'];
        }
        $epgs = $this->sqllibs->rawSelectSql($this->db, "select * from epg where channel_id='" . $postVars['channelId'] . "' and show_time+duration>='" . $timeNow . "' order by epg_display_time limit 0,10");
        //$epgs = $this->sqllibs->rawSelectSql($this->db,"select * from epg where channel_id='".$postVars['channelId']."' and show_time+duration>=UNIX_TIMESTAMP() order by epg_display_time limit 0,10");
        $time = $this->sqllibs->rawSelectSql($this->db, "select UNIX_TIMESTAMP()");
        $result = array();
        $result['epgs'] = $epgs;
        $result['time'] = $time;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetFutureEpgInfo() {
        $postVars = $this->utils->inflatePost(array('channelId'));
        $epgs = $this->sqllibs->rawSelectSql($this->db, "select * from epg where channel_id='" . $postVars['channelId'] . "' and show_time>UNIX_TIMESTAMP() order by epg_display_time desc limit 0,35");
        $result = array();
        $result['epgs'] = $epgs;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetPastEpgInfo() {
        $postVars = $this->utils->inflatePost(array('channelId'));
        $epgs = $this->sqllibs->rawSelectSql($this->db, "select * from epg where channel_id='" . $postVars['channelId'] . "' and show_time+duration<UNIX_TIMESTAMP() order by epg_display_time desc limit 0,10");
        $result = array();
        $result['epgs'] = $epgs;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetDailyEpgInfo() {
        $postVars = $this->utils->inflatePost(array('channelId', 'date'));
        $fromDate = strval($postVars['date']);
        $toDate = strval(intval($fromDate) + 3600 * 24);
        $epgs = $this->sqllibs->rawSelectSql($this->db, "select * from epg where channel_id='" . $postVars['channelId'] . "' and show_time>='" . $fromDate . "' and show_time<'" . $toDate . "' order by epg_display_time desc");
        $result = array();
        $result['epgs'] = $epgs;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetPastDailyEpgInfo() { //For box, show 10 day history
        $postVars = $this->utils->inflatePost(array('channelId', 'date'));
        $fromDateStart = strval($postVars['date']);
        $allEpg = array();
        for ($i = 0; $i <= 9; $i++) {
            $fromDate = $fromDateStart - (3600 * 24 * $i);
            $toDate = strval(intval($fromDate) + 3600 * 24);
            $epgs = $this->sqllibs->rawSelectSql($this->db, "select * from epg where channel_id='" . $postVars['channelId'] . "' and show_time>='" . $fromDate . "' and show_time<'" . $toDate . "' order by epg_display_time desc");
            $allEpg[$i] = $epgs;
        }

        $result = array();
        $result['allEpg'] = $allEpg;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceMovieWithKeyword() {
        $postVars = $this->utils->inflatePost(array('keyword'));
        $vods = $this->sqllibs->rawSelectSql($this->db, "select * from vod_content where name like '%" . $postVars['keyword'] . "%' and type='movie' and processed='yes'");
        $result = array();
        $result['vods'] = $vods;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetShowInfo() {
        $postVars = $this->utils->inflatePost(array('showId'));
        $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_content where processed='yes' and show_id='" . $postVars['showId'] . "'");
        $result = array();
        $result['vods'] = $shows;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetMovieCategory() {
        $postVars = $this->utils->inflatePost(array('categoryId', 'keyword'));
        $keyword = $postVars['keyword'];
        if ($keyword === '') {
            $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_content where processed='yes' and type='movie' and category='" . $postVars['categoryId'] . "'");
        } else {
            $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_content where processed='yes' and type='movie' and category='" . $postVars['categoryId'] . "' and name like '%" . $keyword . "%'");
        }
        $result = array();
        $result['vods'] = $shows;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceGetShowCategory() {
        $postVars = $this->utils->inflatePost(array('categoryId', 'keyword'));
        $keyword = $postVars['keyword'];
        if ($keyword === '') {
            $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_shows where category='" . $postVars['categoryId'] . "'");
        } else {
            $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_shows where category='" . $postVars['categoryId'] . "' and show_name like '%" . $keyword . "%'");
        }
        $result = array();
        $result['vods'] = $shows;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceLoadVod() {
        $postVars = $this->utils->inflatePost(array('memberId', 'type', 'keyword', 'category'));
        $vods = array();
        $shows = array();
        $categorys = $this->sqllibs->selectAllRows($this->db, 'vod_categories');
        $result = array();
        foreach ($categorys as $category) {
            $movies = array();
            $shows = array();
            if ($postVars['type'] == "0") { // Movie
                $result['type'] = 'Movie';
                if ($postVars['keyword'] == '') {
                    $movies = $this->sqllibs->rawSelectSql($this->db, "select * from vod_content where type='movie' and category='" . $category->category_id . "' and processed='yes'");
                } else {
                    $movies = $this->sqllibs->rawSelectSql($this->db, "select * from vod_content where name like '%" . $postVars['keyword'] . "%' and type='movie' and category='" . $category->category_id . "' and processed='yes'");
                }
                $category->movies = $movies;
            } else {
                $result['type'] = 'Show';
                if ($postVars['keyword'] == '') {
                    $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_shows where category='" . $category->category_id . "'");
                } else {
                    $shows = $this->sqllibs->rawSelectSql($this->db, "select * from vod_shows where show_name like '%" . $postVars['keyword'] . "%' and category='" . $category->category_id . "'");
                }
                $category->shows = $shows;
            }
        }
        $result['vods'] = $categorys;
        $result['result'] = 200;
        echo json_encode($result);
    }

    public function serviceUpdateTranscodedVodLink() {
        $postVars = $this->utils->inflatePost(array('original_link', 'new_link'));
        $this->sqllibs->rawSql($this->db, "update vod_content set processed='yes', link='" . $postVars['new_link'] . "' where link LIKE '" . $postVars['original_link'] . "%'");
        $result['result'] = 200;
        echo json_encode($result);
    }

}
