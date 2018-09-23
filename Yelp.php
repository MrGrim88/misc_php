<?php
/*
	Copyright 2018 Richard McQuiston
	Free for personal, non-commercial use.
	
	Donations Gladly Accepted via PayPalto bigrpromotions@gmail.com
*/
class Yelp {
    private $token = '';
    public function getOAuth2Token() {
        $authUrl = "https://api.yelp.com/oauth2/token";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array(
           'client_id' => '',
           'client_secret' =>'',
           'redirect_uri' => '',
           'scope' => '',
           'grant_type' => '',),
            CURLOPT_URL => $authUrl,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true
        ));
        $http_data = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($http_data);
        $this->token = $response->access_token;
        return $http_data;
    }
    public function getReviews($id){
        $key = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.yelp.com/v3/businesses/". $id ."/reviews");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $key ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        return json_decode($contents);
    }
    public function getReviewsArray($id,$fName){
        $data = [];
        $value = $this->getReviews($id);
        if (sizeof($value->reviews) > 0) {
            for ($x = 0; $x < sizeof($value->reviews); $x++) {
                $data[] = Miscellaneous::reviewEntry($fName,$value->reviews[$x]->user->name,$value->reviews[$x]->text,$value->reviews[$x]->rating,$value->reviews[$x]->url,'Yelp',$value->reviews[$x]->time_created ?? '',$value->reviews[$x]->user->image_url ?? '');
            }
        }
        return $data;
    }
    public function cacheReviews($id,$fName,$fID) {
        $data = [];
        $value = $this->getReviews($id);
        $i = 0;
        if (sizeof($value->reviews) > 0) {
            for ($x = 0; $x < sizeof($value->reviews); $x++) {
              $compare = CacheReviews::find()->where(['facility_id'=>$fID,'reply' =>$value->reviews[$x]->url])->all();
                if(sizeof($compare) == 0){
                $c = new CacheReviews();
                $c->facility_id = $fID;
                $c->company = $fName;
                $c->reviewer = htmlentities($value->reviews[$x]->user->name, ENT_COMPAT, "UTF-8");
                $c->comment = htmlentities($value->reviews[$x]->text, ENT_COMPAT, "UTF-8");
                $c->rating = (int)$value->reviews[$x]->rating;
                $c->reply = $value->reviews[$x]->url ?? '';
                $c->site = 'Yelp';
                $c->updateTime = date('Y-m-d', strtotime($value->reviews[$x]->time_created)) ?? '';
                $c->photo = $value->reviews[$x]->user->image_url ?? '';
                $c->myReply = '';
                $c->save();
                $data[] = $c->getErrors();
                $i++;
            }
          }
        }
        return $i;
    }
    public function getInformation($id){
        if ($this->token == '') { $this->getOAuth2Token(); }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.yelp.com/v3/businesses/". $id ."");
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->token ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        return json_decode($contents);
    }
    public function searchByPhone($phone){
        if ($this->token == '') { $this->getOAuth2Token(); }
        $results = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.yelp.com/v3/businesses/search/phone?phone=+1" . $phone);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->token ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = json_decode(curl_exec($ch));
        $businesses = $contents->businesses;
        if (sizeof($businesses) > 0) {
            foreach ($businesses as $bus) {
                $results[] = [$bus->id, $bus->name . ' - ' . $bus->location->city . ',' . $bus->location->state];
            }
        }
        return json_encode($results,true);
    }
    public function searchByZip($zip,$radius = 10) {
        if ($this->token == '') { $this->getOAuth2Token(); }
        if ($radius > 25) { $radius = 25; }
        $results = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.yelp.com/v3/businesses/search?location=" . $zip . '&radius=' . $radius . '&category=localservices');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer ' . $this->token ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = json_decode(curl_exec($ch));
        $businesses = $contents->businesses;
        if (sizeof($businesses) > 0) {
            foreach ($businesses as $bus) {
                $results[] = [$bus->id, $bus->name . ' - ' . $bus->location->city . ',' . $bus->location->state];
            }
        }
        return json_encode($results,true);
    }
}
