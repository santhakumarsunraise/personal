<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class SMS_model extends CI_Model{

function sendSMS($phone, $message){

    $sender = 'ELOCRT';
    $route = '4';
    $country = '91';
    $message = $message;
    $mobile = $phone;
    $authKey = '330739AkE8Snh05603786e7P1';
    $smsData = '{
      "sender": "'.$sender.'",
      "route": "'.$route.'",
      "country": "'.$country.'",
      "sms": [
          { "message": "'.$message.'",
            "to": [ "'.$mobile.'" ]
          }
        ]
    }';        
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.msg91.com/api/v2/sendsms",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $smsData,
      CURLOPT_SSL_VERIFYHOST => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_HTTPHEADER => array(
        "authkey: $authKey",
        "content-type: application/json"
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    print_r($response);

    if ($err) {
       return FALSE;
    } else {
        return TRUE;
    }

    }

  
    public function senddSMS($SmsMobile, $SmsMessage){
      $message = rawurlencode($SmsMessage);
      $mob = '91'.$SmsMobile;
      $url = 'http://text.justsms.co.in/vendorsms/pushsms.aspx?apiKey=cda49c67-ebc0-421a-a4d6-edc851702147&clientid=8a02c3ad-3b28-4e96-a313-7451a747d7df&msisdn='.$mob.'&sid=ELLORA&msg='.$message.'&fl=0&gwid=2';
      $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl); 
        curl_close($curl);
        $res = json_decode($result, true);
        if($res['ErrorCode']==="000"){
            return true;
        }else{
        return false;
        }
    }

}

	