<?php
/*

	Copyright 2018 Richard McQuiston
	Free for personal, non-commercial use.
	
	Donations Gladly Accepted via PayPal to bigrpromotions@gmail.com
*/
namespace app\models;

use Yii;
use linslin\yii2\curl;

class Plivo {
    private static $url = 'https://api.plivo.com/.';
    private static $version = 'v1/';
    private static $authID = '';
    private static $token = '';
    /*
        GETTERS & SETTERS & MISC FUNCTIONS
    */
    public static function setID($id) {
        self::$authID = ($id != '') ? $id : self::$authID;
    }
    public static function setToken($token) {
        self::$token = ($token != '') ? $token : self::$token;
    }
    public static function setURL($url) {
        self::$url = ($url != '') ? $url : self::$url;
    }
    public static function setVersion($version) {
        self::$version = ($version != '') ? $version : self::$version;
    }
    /*
        CURL REQUESTS
    */
    public static function getRequest($url,$params) {
        $fullUrl = self::$baseURL . self::$version . $url;
        $params['token'] = self::$apiKey;
        $params['secret'] = self::$apiSecret;
        $params = http_build_query($params);
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $fullUrl . '?' . $params,
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)',
            CURLOPT_USERPWD => self::$apiKey . ':' . self::$apiSecret
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            return ['errors'=> 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl),'url' => $fullUrl];
        } else {
            return json_decode($resp,true);
        }
    }
    public static function postRequest($url,$params) {
        //if (self::$token == '') { return self::getToken(); }
        $fullUrl = self::$url . self::$version . $url;
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $fullUrl,
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)',
            CURLOPT_POST => 1,
            //CURLOPT_USERPWD => self::$authID . ':' . self::$token,
            CURLOPT_POSTFIELDS => json_encode($params)
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            return ['errors'=> 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl),'url' => $fullUrl,'params' => json_encode($params,true)];
        } else {
            return json_decode($resp,true);
        }
    }
    /* POST REQUESTS */
    public static function sendMessage($src,$dst,$text) {
        //POST https://api.plivo.com/v1/Account/{auth_id}/Message/
        $params = [
            'src' => $src,
            'dst' => $dst,
            'text' => $text,
        ];
        return self::postRequest('Account/' . self::$authID . '/Message/',$params);
    }
    /* GET REQUESTS */
    public static function getMessages($id) {
        //GET https://api.plivo.com/v1/Account/{auth_id}/Message/
    }
    public static function getMessage($id,$msg) {
        //GET https://api.plivo.com/v1/Account/{auth_id}/Message/{message_uuid}/
    }
}
