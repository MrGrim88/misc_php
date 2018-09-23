<?php
/*

	Copyright 2018 Richard McQuiston
	Free for personal, non-commercial use.
	
	Donations Gladly Accepted via PayPalto bigrpromotions@gmail.com
*/

class FlowRoute {
    private static $key = '';
    private static $secret = '';
    private static $token = '';
    private static $baseURL  = 'https://api.flowroute.com/';
    private static $version = 'v2/';

    public static function set($var,$val) {
        self::$$var = ($val != '') ? $val : self::$$var;
    }
    public static function get($var) {
        return self::$$var;
    }
    public static function getRequest($url,$params) {
        $fullUrl = self::$baseURL . self::$version . $url;
        $params['token'] = self::$key;
        $params['secret'] = self::$secret;
        $params = http_build_query($params);
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $fullUrl . '?' . $params,
            CURLOPT_USERAGENT => ' curl/7.43.0',
            CURLOPT_USERPWD => self::$key . ':' . self::$secret
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            Yii::error('FlowRoute->getRequest() = ' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            return ['errors'=> 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl),'url' => $fullUrl];
        } else {
            return json_decode($resp,true);
        }
    }
    public static function postRequest($url,$params) {
        $fullUrl = self::$baseURL . self::$version . $url;
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $fullUrl,
            CURLOPT_USERAGENT => ' curl/7.43.0',
            CURLOPT_POST => 1,
            CURLOPT_USERPWD => self::$key . ':' . self::$secret,
            CURLOPT_POSTFIELDS => json_encode($params)
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            Yii::error('FlowRoute->postRequest() = ' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            return ['errors'=> 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl),'url' => $fullUrl,'params' => json_encode($params,true)];
        } else {
            return json_decode($resp,true);
        }
    }
    public static function sendMessage($to,$from,$msg) {
        Yii::error('Message Sent to ' . $to);
        return self::postRequest('messages',[
            'to'=>$to,'from' =>$from, 'body'=>$msg,
        ]);
    }
    public static function lookupMessage($id) {
        return self::getRequest('messages/' . $id,[]);
    }
    public static function lookupMessages($start,$end,$limit = 100,$offset=0) {
        return self::getRequest('messages', [
            'start_date' => $start,
            'end_date' => $end,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
