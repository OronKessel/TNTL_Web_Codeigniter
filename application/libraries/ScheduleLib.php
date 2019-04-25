<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ScheduleLib {

    public function getToken() {

        $url = 'http://json.schedulesdirect.org/20141201/token';
        $param = array();
        $pwSha1 = hash("sha1", 'ed1234');
        $param['username'] = "tveddyz";
        $param['password'] = $pwSha1;
        $rawParam = json_encode($param);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $rawParam);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result->token;
    }

    public function getStatus() {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/status';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
    }

    public function getAvailableServices()
    {
        $url = 'http://json.schedulesdirect.org/20141201/available';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        print_r($result);
    }

    public function getAvailableCountry()
    {
        $url = 'http://json.schedulesdirect.org/20141201/available/countries';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data,true);
        curl_close($curl);
        return $result;
    }

    public function getTransmitterGBR()
    {
        $url = 'http://json.schedulesdirect.org/20141201/transmitters/GBR';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        print_r($result);
    }

    public function getListHeadendsPostal($country,$postalCode)
    {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/headends?country='.$country.'&postalcode='.$postalCode;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result;
    }
    public function setLineUpAccount($lineup)
    {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/lineups/'.$lineup;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_PUT, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result;
    }
    public function getLineUpList()
    {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/lineups';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result;
    }
    public function deleteLineup($lineup)
    {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/lineups/'.$lineup;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result;
    }
    public function getStationIds($lineup)
    {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/lineups/'.$lineup;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result;
    }
    public function getLineupJson()
    {
        $token = $this->getToken();
        $url = 'http://ipAddressOfHDHR/lineup.json';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        print_r($data);
    }
    public function getScheduleStation($stationArray ,$date)
    {
        if($date == null)
            $date = new DateTime();
        else
        // $date = new DateTime('2017-10-05');
        $date = new DateTime($date);

        $reqParam = array();
        foreach($stationArray as $stInfo)
        {
            $reqItem = array();
            $reqItem['stationID'] = $stInfo['id'];
            $dateArray = array();
            $dateArray[] = $date->format('Y-m-d');
            $reqItem['date'] = $dateArray;
            $reqParam[] = $reqItem;
        }
        $token = $this->getToken();
        $rawParam = json_encode($reqParam);
        $url = 'http://json.schedulesdirect.org/20141201/schedules';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $rawParam);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result;
    }
    public function getMaplineup($lineup)
    {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/map/lineup/'.$lineup;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        print_r($data);
    }
    public function getPrograms($programIds)
    {
        $token = $this->getToken();
        $url = 'http://json.schedulesdirect.org/20141201/programs';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token,
            "Accept-Encoding:'deflate,gzip'"
        ));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($programIds));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        return $result;
    }
    public function getSchedule()
    {
        $token = $this->getToken();
        echo $token;
        $url = 'http://json.schedulesdirect.org/20141201/schedules';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "tvderby");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Token:".$token
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $result = json_decode($data);
        curl_close($curl);
        print_r($result);

    }
    public function test()
    {
        $this->getStationIds();
    }

}

?>
