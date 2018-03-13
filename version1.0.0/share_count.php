<?php

class Dlv_share
{
    function getPlus1($url)
    {
        $html = file_get_contents("https://plusone.google.com/_/+1/fastbutton?url=" . urlencode($url));
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $counter = $doc->getElementById('aggregateCount');

        return $counter->nodeValue;
    }

    function getTweets($url)
    {
        $json = file_get_contents("http://urls.api.twitter.com/1/urls/count.json?url=" . $url);
        $ajsn = json_decode($json, true);
        $cont = $ajsn['count'];

        return $cont;
    }

    function getPins($url)
    {
        $json = file_get_contents("http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=" . $url);
        $json = substr($json, 13, -1);
        $ajsn = json_decode($json, true);
        $cont = $ajsn['count'];

        return $cont;
    }

    function getFacebooks($url)
    {
        $xml = file_get_contents(
            "http://api.facebook.com/restserver.php?method=links.getStats&urls=" . urlencode($url)
        );
        $xml = simplexml_load_string($xml);
        $shares = $xml->link_stat->share_count;
        $likes = $xml->link_stat->like_count;
        $comments = $xml->link_stat->comment_count;

        return $likes + $shares + $comments;
    }
}

class Dlv_ShareCount
{
    private $url, $timeout;

    function __construct($url, $timeout = 50)
    {
        $this->url = rawurlencode($url);
        $this->timeout = $timeout;
    }

    function get_tweets()
    {
        $json_string = $this->file_get_contents_curl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->url);
        $json = json_decode($json_string, true);

        return isset($json['count']) ? intval($json['count']) : 0;
    }

    function get_linkedin()
    {
        $json_string = $this->file_get_contents_curl(
            "http://www.linkedin.com/countserv/count/share?url=$this->url&format=json"
        );
        $json = json_decode($json_string, true);

        return isset($json['count']) ? intval($json['count']) : 0;
    }

    function get_fb()
    {
        $json_string = $this->file_get_contents_curl(
            'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $this->url
        );
        $json = json_decode($json_string, true);

        return isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0;
    }

    function get_plusones()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode(
                $this->url
            ) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]'
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        $curl_results = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($curl_results, true);

        return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval(
            $json[0]['result']['metadata']['globalCounts']['count']
        ) : 0;
    }

    function get_stumble()
    {
        $json_string = $this->file_get_contents_curl(
            'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $this->url
        );
        $json = json_decode($json_string, true);

        return isset($json['result']['views']) ? intval($json['result']['views']) : 0;
    }

    function get_delicious()
    {
        $json_string = $this->file_get_contents_curl(
            'http://feeds.delicious.com/v2/json/urlinfo/data?url=' . $this->url
        );
        $json = json_decode($json_string, true);

        return isset($json[0]['total_posts']) ? intval($json[0]['total_posts']) : 0;
    }

    function get_pinterest()
    {
        $return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?url=' . $this->url);
        $json_string = preg_replace('/^receiveCount\((.*)\)$/', "\1", $return_data);
        $json = json_decode($json_string, true);

        return isset($json['count']) ? intval($json['count']) : 0;
    }

    private function file_get_contents_curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $cont = curl_exec($ch);
        if (curl_error($ch)) {
            die(curl_error($ch));
        }

        return $cont;
    }
}
//
//$obj = new Dlv_ShareCount("http://actthemes.com/krish/hello-world/");  //Use your website or URL
//echo 'Tweet:';
//echo $obj->get_tweets(); //to get tweets
//echo '<br/>FB:';
//echo $obj->get_fb(); //to get facebook total count (likes+shares+comments)
//echo '<br/>linked:';
//echo $obj->get_linkedin(); //to get linkedin shares
//echo '<br/>Plus:';
//echo $obj->get_plusones(); //to get google plusones
//echo '<br/>Delicious:';
//echo $obj->get_delicious(); //to get delicious bookmarks  count
//echo '<br/>stumble:';
//echo $obj->get_stumble(); //to get Stumbleupon views
//echo '<br/>Pin:';
//echo $obj->get_pinterest(); //to get pinterest pins