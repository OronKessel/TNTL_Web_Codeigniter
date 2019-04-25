<?php

defined('BASEPATH') or exit('No direct script access allowed');
include('parse/parsecsv.lib.php');

class ParseTV
{
    public $csvHeaderMapDB = array(
        'Series Name' => 'name',
        'Series ID' => 'sid',
        'Series Title Card' => 'cardimage',
        'Network' => 'network',
        'Network Logo' => 'network_logo',
        'Genre' => 'genre',
        'Star #1' => 'star1',
        'Star #2' => 'star2',
        'Star #3' => 'star3',
        'Also Starring' => 'starother',
        'Production Company' => 'company',
        'Created By' => 'createdby',
        'Executive Producer(s)' => 'exproducer',
        'Distributor' => 'distributor',
        'Series Synopsis' => 'series_synopsis',
        'Series Premier Date' => 'series_premier_date',
        'Theme Song Composer' => 'theme_song_composer',
        'Series Theme Song' => 'series_theme_song',
        'Link to Series Trailer' => 'link_to_series_trailer',
        'Broadcast Season' => 'broadcast_season',
        'Total  Episodes' => 'total_episodes',
        'Series Rankings' => 'series_rankings',
        'Series Average # Viewers (Millions)' => 'series_avg_viewers',
        'Rotten Tomatoes TomatoMeter Average %' => 'rotten_avg',
        'Rotten Tomatoes Average Audience Score %' => 'rotten_avg_score',
        'Metacritic Meta Score' => 'meta_score',
        'Metacritic User Score' => 'meta_user_score',
        'Twitter Followers' => 'tw_followers',
        'Facebook Followers' => 'fb_followers',
        'New/Returning/Cancelled' => 'status',
        'Series Season' => 'series_season',
        'Episode #' => 'episode',
        'Episode ID#\\' => 'episode_id',
        'Episode Title' => 'episode_title',
        'Broadcast Day' => 'broadcast_day',
        'Broadcast Time' => 'broadcast_time',
        'Running Time (minutes)' => 'runtime',
        'Episode Directed by' => 'episode_director',
        'Episode Written by' => 'episode_writer',
        'Featured Character' => 'featured_character',
        'Episode Original Air Date' => 'episode_org_date',
        'Production Code' => 'pr_code',
        'Ratings Share U.S. (18-49)' => 'rating_share',
        'Broadcast Viewers U.S.' => 'broadcast_viewers',
        'DVR Viewers U.S.(18-49)' => 'dvr_viewers',
        'DVR Viewers U.S.' => 'dvr_viewers1',
        'Total Viewers U.S. (18-49)' => 'total_viewers',
        'Total Viewers U.S.' => 'total_viewrs1',
        'Episode Synopsis' => 'episode_synopsis',
        'Episode One Sheet' => 'episode_one_sheet',
        'Link to Episode/Trailer' => 'link_episode',
        'Price/ 30 Sec Ad (Upfront Market)' => 'price_upfront',
        'Price/ 30 Sec Ad (Spot Market)' => 'price_spot',
        'Top Advertisers' => 'top_advertiser'
    );
    public $tvmazeMapDB = array(
        'id' => 'sid',
        'url' => 'link_external',
        'name' => 'name',
        'type' => 'type',
        'language' => 'language',
        'genres' => 'genres',
        'status' => 'status',
        'runtime' => 'runtime',
        'premiered' => 'premiered',
        'officialSite' => 'officialsite',
        'schedule_time' => 'schedule_time',
        'schedule_days' => 'schedule_days',
        'rating_average' => 'rating_avg',
        'weight' => 'weight',
        'network_id' => 'network_id',
        'network_name' => 'network_name',
        'network_country_name' => 'network_country_name',
        'network_country_code' => 'network_country_code',
        'network_country_timezone' => 'network_country_timezone',
        'webChannel_id' => 'webchannel_id',
        'webChannel_name' => 'webchannel_name',
        'webChannel_country_name' => 'webchannel_country_name',
        'webChannel_country_code' => 'webchannel_country_code',
        'webChannel_country_timezone' => 'webchannel_country_timezone',
        'externals_tvrage' => 'externals_tvrage',
        'externals_thetvdb' => 'externals_thetvdb',
        'externals_imdb' => 'externals_imdb',
        'image_medium' => 'image_medium',
        'image_original' => 'image_original',
        'summary' => 'summary',
        'updated' => 'updated',
        '_links_self_href' => 'link_self',
        '_links_previousepisode_href' => 'link_prev',
        '_links_nextepisode_href' => 'link_next',
    );
    public $parseKeyword = array(
        'id', 'url', 'name', 'type', 'language',
        'genres' => array(),
        'status', 'runtime', 'premiered', 'officialSite',
        'schedule' => array(
            'time',
            'days' => array()
        ),
        'rating' => array(
            'average'
        ),
        'weight',
        'network' => array(
            'id', 'name',
            'country' => array(
                'name', 'code', 'timezone'
            )
        ),
        'webChannel' => array(
            'id', 'name',
            'country' => array(
                'name', 'code', 'timezone'
            )
        ),
        'externals' => array(
            'tvrage', 'thetvdb', 'imdb'
        ),
        'image' => array(
            'medium', 'original'
        ),
        'summary', 'updated',
        '_links' => array(
            'self' => array(
                'href'
            ),
            'previousepisode' => array(
                'href'
            ),
            'nextepisode' => array(
                'href'
            )
        )
    );
    public $parseKeywordTvmazePeople = array(
        'id', 'url', 'name',
        'image' => array(
            'medium', 'original'
        ),
        '_links' => array(
            'self' => array(
                'href'
            )
        )
    );
    public $tvmazePeopleMapDB = array(
        'id' => 'id',
        'url' => 'url',
        'name' => 'name',
        'image_medium' => 'image_medium',
        'image_original' => 'image_original',
        '_links_self_href' => 'link',
    );

    public $parseKeywordTvmazeEpisode = array(
        'id', 'url', 'name', 'season', 'number', 'airdate', 'airtime', 'airstamp', 'runtime',
        'image' => array(
            'medium', 'original'
        ),
        'summary',
        '_links' => array(
            'self' => array(
                'href'
            )
        )
    );
    public $tvmazeEpisodeMapDB = array(
        'id' => 'id',
        'url' => 'url',
        'name' => 'name',
        'season' => 'season',
        'number' => 'number',
        'airdate' => 'airdate',
        'airtime' => 'airtime',
        'airstamp' => 'airstamp',
        'runtime' => 'runtime',
        'image_medium' => 'image_medium',
        'image_original' => 'image_original',
        'summary' => 'summary',
        '_links_self_href' => 'link',
    );
    public $parseKeywordTvmazeCharacter = array(
        'id', 'url', 'name',
        'image' => array(
            'medium', 'original'
        ),
        '_links' => array(
            'self' => array(
                'href'
            )
        )
    );
    public $tvmazeCharacterMapDB = array(
        'id' => 'id',
        'url' => 'url',
        'name' => 'name',
        'image_medium' => 'image_medium',
        'image_original' => 'image_original',
        '_links_self_href' => 'link',
    );
    public $parseKeywordTvmazeCast = array(
        "person" => array(
            'id'
        ),
        "character" => array(
            'id'
        )
    );

    public $tvmazeShowCastMapDB = array(
        'person_id' => 'pid',
        'character_id' => 'cid'
    );

    public $parseKeywordTvmazeCrew = array(
        'type',
        "person" => array(
            'id'
        )
    );

    public $tvmazeShowCrewMapDB = array(
        'type' => 'type',
        'person_id' => 'pid',
    );

    public $tvmazeCastCreditTvmaze = array(
        '_links' => array(
            'show'=> array(
                'href'
            ),
            'character' => array(
                'href'
            )
        ),
        '_embedded' => array(
            'show' => array(
                'id','url','name','type','language','genres' => array(),'status','runtime'
            )
        )
    );

    public $tvmazeCastCreditTvmazeMapDB = array(
        'show' => 'id',
    );

    public function parseJSON($url = 'http://api.tvmaze.com/shows?page=1')
    {
        $curl = curl_init();
        $i = 1;
        $isEnd = false;
        $dataShows = array();
        do {
            $url1 = 'http://api.tvmaze.com/shows?page=' . $i;
            curl_setopt($curl, CURLOPT_URL, $url1);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($curl);
            $arrayData = json_decode($data, true);
            if (!array_key_exists('status', $arrayData)) {
                foreach ($arrayData as $tvData) {
                    $result = array();
                    $this->generateTvmazeArray($tvData, $this->parseKeyword, '', $result);
                    $converData = array();
                    foreach ($this->tvmazeMapDB as $key => $hField) {
                        if (!array_key_exists($key, $result)) {
                            $converData[$hField] = '';
                        } else {
                            $converData[$hField] = $result[$key];
                        }
                    }
                    $dataShows[] = $converData;
                }
                $isEnd = false;
            } else {
                $isEnd = true;
            }
            $i++;
        } while ($isEnd == false && $i < 2);
        $headerSqlArray = array();
        foreach ($this->tvmazeMapDB as $hField) {
            if ($hField == null || $hField == '') {
                continue;
            }
            $headerSqlArray[] = $hField;
        }
        curl_close($curl);
        $resultJson['header'] = $headerSqlArray;
        $resultJson['data'] = $dataShows;
        return $resultJson;
    }

    public function parseEpisodeJsonTVMaze($showId = 1)
    {
        $curl = curl_init();
        $url1 = 'http://api.tvmaze.com/shows/' . $showId . "/episodes";
        curl_setopt($curl, CURLOPT_URL, $url1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $arrayData = json_decode($data, true);
        foreach ($arrayData as $episodeData) {
            $result = array();
            $this->generateTvmazeArray($episodeData, $this->parseKeywordTvmazeEpisode, '', $result);
            $converData = array();
            foreach ($this->tvmazeEpisodeMapDB as $key => $hField) {
                $converData[$hField] = $result[$key];
            }
            $dataEpisode[] = $converData;
        }
        curl_close($curl);
        $headerSqlArray = array();
        foreach ($this->tvmazeEpisodeMapDB as $hField) {
            if ($hField == null || $hField == '') {
                continue;
            }
            $headerSqlArray[] = $hField;
        }
        $resultJson['header'] = $headerSqlArray;
        $resultJson['data'] = $dataEpisode;
        $resultJson['showId'] = $showId;
        return $resultJson;
    }

    public function parseCastJsonTVMaze($showId = 1)
    {
        $curl = curl_init();
        $url1 = 'http://api.tvmaze.com/shows/' . $showId . "/cast";
        curl_setopt($curl, CURLOPT_URL, $url1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $arrayData = json_decode($data, true);

        foreach ($arrayData as $castData) {
            $result = array();
            $this->generateTvmazeArray($castData, $this->parseKeywordTvmazeCast, '', $result);
            $converData = array();
            foreach ($this->tvmazeShowCastMapDB as $key => $hField) {
                $converData[$hField] = $result[$key];
            }
            $dataCast[] = $converData;
        }
        curl_close($curl);
        $headerSqlArray = array();
        foreach ($this->tvmazeShowCastMapDB as $hField) {
            if ($hField == null || $hField == '') {
                continue;
            }
            $headerSqlArray[] = $hField;
        }
        $resultJson['header'] = $headerSqlArray;
        $resultJson['data'] = $dataCast;
        $resultJson['showId'] = $showId;
        return $resultJson;
    }

    public function parseCrewJsonTVMaze($showId = 1)
    {
        $curl = curl_init();
        $url1 = 'http://api.tvmaze.com/shows/' . $showId . "/crew";
        curl_setopt($curl, CURLOPT_URL, $url1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $arrayData = json_decode($data, true);

        foreach ($arrayData as $crewData) {
            $result = array();
            $this->generateTvmazeArray($crewData, $this->parseKeywordTvmazeCrew, '', $result);
            $converData = array();
            foreach ($this->tvmazeShowCrewMapDB as $key => $hField) {
                $converData[$hField] = $result[$key];
            }
            $dataCrew[] = $converData;
        }
        curl_close($curl);
        $headerSqlArray = array();
        foreach ($this->tvmazeShowCrewMapDB as $hField) {
            if ($hField == null || $hField == '') {
                continue;
            }
            $headerSqlArray[] = $hField;
        }
        $resultJson['header'] = $headerSqlArray;
        $resultJson['data'] = $dataCrew;
        $resultJson['showId'] = $showId;
        return $resultJson;
    }

    public function parsePeopleSingleJsonTVMaze($pid = 1)
    {
        $curl = curl_init();
        $url1 = 'http://api.tvmaze.com/people/' . $pid;
        curl_setopt($curl, CURLOPT_URL, $url1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $arrayData = json_decode($data, true);
        if (!array_key_exists('status', $arrayData)) {
            $result = array();
            $this->generateTvmazeArray($arrayData, $this->parseKeywordTvmazePeople, '', $result);
            $converData = array();
            foreach ($this->tvmazePeopleMapDB as $key => $hField) {
                $converData[$hField] = $result[$key];
            }
            return $converData;
        }
        return null;
    }
    public function parseCharacterSingleCreditTVMaze($cid = 1)
    {
        $curl = curl_init();
        $url1 = 'http://api.tvmaze.com/people/' . $cid.'/castcredits?embed=show';
        curl_setopt($curl, CURLOPT_URL, $url1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $arrayData = json_decode($data, true);
        if (!array_key_exists('status', $arrayData)) {
            $result = array();
            var_dump($arrayData);
            $this->generateTvmazeArray($arrayData, $this->tvmazeCastCreditTvmaze, '', $result);
            $converData = array();
            var_dump($result);
            foreach ($this->tvmazeCastCreditTvmazeMapDB as $key => $hField) {
                $converData[$hField] = $result[$key];
            }
            $converData['cast'] = $cid;
            return $converData;
        }
        return null;
    }
    public function parseCharacterSingleJsonTVMaze($cid = 1)
    {
        $curl = curl_init();
        $url1 = 'http://api.tvmaze.com/characters/' . $cid;
        curl_setopt($curl, CURLOPT_URL, $url1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $arrayData = json_decode($data, true);
        if (!array_key_exists('status', $arrayData)) {
            $result = array();
            $this->generateTvmazeArray($arrayData, $this->parseKeywordTvmazeCharacter, '', $result);
            $converData = array();
            foreach ($this->tvmazeCharacterMapDB as $key => $hField) {
                $converData[$hField] = $result[$key];
            }
            return $converData;
        }
        return null;
    }

    public function parsePeopleJsonTVMaze($url = 'http://api.tvmaze.com/people/')
    {
        $curl = curl_init();
        $i = 1;
        $isEnd = false;
        $dataPeople = array();
        do {
            $url1 = $url . $i;
            curl_setopt($curl, CURLOPT_URL, $url1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($curl);
            $arrayData = json_decode($data, true);
            if (!array_key_exists('status', $arrayData)) {
                $result = array();
                $this->generateTvmazeArray($arrayData, $this->parseKeywordTvmazePeople, '', $result);
                $converData = array();
                foreach ($this->tvmazePeopleMapDB as $key => $hField) {
                    $converData[$hField] = $result[$key];
                }
                $dataPeople[] = $converData;
                $isEnd = false;
            } else {
                $isEnd = true;
            }
            $i++;
        } while ($isEnd == false && $i < 6);
        curl_close($curl);
        $headerSqlArray = array();
        foreach ($this->tvmazePeopleMapDB as $hField) {
            if ($hField == null || $hField == '') {
                continue;
            }
            $headerSqlArray[] = $hField;
        }
        $resultJson['header'] = $headerSqlArray;
        $resultJson['data'] = $dataPeople;
        return $resultJson;
    }

    public function parseCharacterJsonTVMaze($url = 'http://api.tvmaze.com/characters/')
    {
        $curl = curl_init();
        $i = 1;
        $isEnd = false;
        $dataPeople = array();
        do {
            $url1 = $url . $i;
            curl_setopt($curl, CURLOPT_URL, $url1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($curl);
            $arrayData = json_decode($data, true);
            if (!array_key_exists('status', $arrayData)) {
                $result = array();
                $this->generateTvmazeArray($arrayData, $this->parseKeywordTvmazeCharacter, '', $result);
                $converData = array();
                foreach ($this->tvmazeCharacterMapDB as $key => $hField) {
                    $converData[$hField] = $result[$key];
                }
                $dataPeople[] = $converData;
                $isEnd = false;
            } else {
                $isEnd = true;
            }
            $i++;
        } while ($isEnd == false && $i < 6);
        curl_close($curl);
        $headerSqlArray = array();
        foreach ($this->tvmazeCharacterMapDB as $hField) {
            if ($hField == null || $hField == '') {
                continue;
            }
            $headerSqlArray[] = $hField;
        }
        $resultJson['header'] = $headerSqlArray;
        $resultJson['data'] = $dataPeople;
        return $resultJson;
    }

    public function parseCSV($filePath = 'upload/spd.csv')
    {
        $result = array();
        $csv = new parseCSV($filePath);
        //get header Info
        $headerFields = $csv->data[0];
        $dataCSV = array();
        $headerSqlArray = array();
        foreach ($headerFields as $hField) {
            if ($hField == null || $hField == '') {
                continue;
            }
            $headerSqlArray[] = $this->csvHeaderMapDB[$hField];
        }
        for ($i = 1; $i < count($csv->data); $i++) {
            if ($csv->data[$i] == null || !array_filter($csv->data[$i])) {
                continue;
            }
            $dataCSV[] = $csv->data[$i];
        }

        $result['header'] = $headerSqlArray;
        $result['data'] = $dataCSV;
        return $result;
    }

    public function generateTvmazeArray($data, $keywordGroup, $baseKeyword, &$result)
    {
        if (count($keywordGroup) == 0) {
            $content = '';
            foreach ($data as $dt) {
                $content = $content . "," . $dt;
            }
            $result[$baseKeyword] = $content;
            return;
        }
        foreach ($keywordGroup as $key => $value) {
            if (is_array($value)) {
                if ($data == null || !array_key_exists($key, $data)) {
                    continue;
                }
                $keyword = $key;
                if ($baseKeyword != '') {
                    $keyword = $baseKeyword . "_" . $key;
                }
                $this->generateTvmazeArray($data[$key], $value, $keyword, $result);
            } else {
                $keyword = $value;
                if ($baseKeyword != '') {
                    $keyword = $baseKeyword . "_" . $value;
                }
                $result[$keyword] = $data[$value];
            }
        }
    }
}
