<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apid extends CI_Controller {

public function __construct() { parent::__construct(); $this->load->model('ApidM'); }

function index(){ $rdata['status'] = 'error'; $rdata['message'] = 'Not Authorized'; echo json_encode($rdata); exit; }
function error(){ $rdata['status'] = 'error'; $rdata['message'] = 'Something went wrong'; echo json_encode($rdata); exit; }
function notfound(){ $rdata['status'] = 'error'; $rdata['message'] = 'No data Found'; echo json_encode($rdata); exit; }
function userExists(){ $rdata['status'] = 'error'; $rdata['message'] = 'User Already Exist'; echo json_encode($rdata); exit; }

function get_splash(){
  $imgs = $this->ApidM->get_splash('3');
  if(!empty($imgs)){
  foreach ($imgs as $row):
    $res_dataa[] = [
      'spl_id' => $row['spl_id'],
      'spl_name' => $row['spl_name'],
      'spl_image' => $row['spl_image']
    ];
  endforeach;
    $res_data['status'] = 'ok';
		$res_data['splash'] = $res_dataa;
	}else{
    $res_data['status'] = 'error';
    $res_data['splash'] = [];
  }
    echo json_encode($res_data);
}

function get_country(){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $lat = trim($_POST['lat']);
        $long = trim($_POST['long']);
        $cCheck = $this->ApidM->checkCountry($lat, $long);
        if(!empty($cCheck)){
           $rdata['status'] = 'ok';
           $rdata['cnty_code']=$cCheck['cnty_code'];
           $rdata['cnty_name']=$cCheck['cnty_name'];
           $rdata['cnty_phone']=$cCheck['cnty_phone'];
           $rdata['cnty_currency']=$cCheck['cnty_currency'];
           $rdata['cnty_entity']=$cCheck['cnty_entity'];
           $rdata['cnty_flag'] = $cCheck['cnty_flag'];
           $rdata['message']='country exist';
         }
        else{
          $rdata['status'] = 'error';
          $rdata['message']='no country';
        }
         echo json_encode($rdata); exit;
    }else{$this->error();}
 }
 

 function sendOTP($id, $phone, $type){
  if($phone === "9505610288"){$otp_digit = 1234;}else{ $otp_digit = mt_rand(1000, 9999);}

	$data = array('boy_otp' => $otp_digit);
	$updated=$this->ApidM->update($data, $type, array('boy_id' => $id, 'boy_phone' => $phone));
	if(1===1){
		$this->load->model('Sms_model');
		$message="Your OTP at ELLOCART is ".$otp_digit;
		$sent=$this->Sms_model->sendSMS($phone, $message, "1207161425250670430");
		if($sent){return "sent";} else{return "failed";}
	}else{$this->error();}
}



function resend_otp(){
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$phone = trim($_POST['boy_phone']);
	$phone_code = trim($_POST['boy_phone_code']);
    $otp_digit=$this->ApidM->get_field('boy_otp', 'boys', array('boy_phone_code' => $phone_code, 'boy_phone' => $phone));
    if($otp_digit > 999){
	$this->load->model('Sms_model');
	$message="Your OTP at ELLOCART is ".$otp_digit;
	$sent=$this->Sms_model->sendSMS($phone, $message, "1207161425250670430");
	if($sent){
		$rdata['status'] = 'ok';
		$rdata['boy_phone_code'] = $phone_code;
		$rdata['boy_phone'] = $phone;
		$rdata['sms'] = 'sent';
		echo json_encode($rdata); exit;
	} else{
		$rdata['status'] = 'error';
		$rdata['boy_phone_code'] = $phone_code;
		$rdata['boy_phone'] = $phone;
		$rdata['sms'] = 'failed';
		echo json_encode($rdata); exit;
    } 
}else{$this->error();}
}else{$this->error();}
}



function verify_otp(){
    $type = trim($_POST['type']); 
    $phone = trim($_POST['boy_phone']); 
    $phone_code = trim($_POST['boy_phone_code']);
    $otp = trim($_POST['otp']);
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $type === "reg"){
        $boy_id = trim($_POST['boy_id']);
		$otp_digit=$this->ApidM->get_field('boy_otp', 'boys', array('boy_id' => $boy_id, 'boy_phone_code' => $phone_code, 'boy_phone' => $phone));
		if($otp===$otp_digit){
			$data = array('boy_status' => '1');
			$updated=$this->ApidM->update($data, 'boys', array('boy_id' => $boy_id, 'boy_phone_code' => $phone_code, 'boy_phone' => $phone));
			if($updated===1){
				$status=$this->ApidM->get_field('boy_astatus', 'boys', array('boy_id' => $boy_id, 'boy_phone_code' => $phone_code, 'boy_phone' => $phone));
        $name=$this->ApidM->get_field('boy_name', 'boys', array('boy_id' => $boy_id, 'boy_phone_code' => $phone_code, 'boy_phone' => $phone));
        $res_data['status'] = 'ok';
				$res_data['type'] = $type;
				$res_data['boy_id'] = $boy_id;
				$res_data['phone_code'] = $phone_code;
				$res_data['phone'] = $phone;
        $res_data['boy_name']=$name;
				$res_data['verified'] = '1';
				$res_data['message']="Verification Completed Successfully";
        $res_data['approval'] = $status;

        $sCheck = $this->ApidM->checkBoySubsc($boy_id);
        if($sCheck === 1){ $rdata['deposit_status'] = 'ok'; $rdata['deposit_amount']="500"; }
        else{ $rdata['deposit_status'] = 'error'; $rdata['deposit_amount']="500"; }         

				echo json_encode($res_data); exit;
			}else{$this->error();}
		}else{
      $name=$this->ApidM->get_field('boy_name', 'boys', array('boy_id' => $boy_id, 'boy_phone_code' => $phone_code, 'boy_phone' => $phone));
			$res_data['status'] = 'error';
			$res_data['type'] = $type;
			$res_data['boy_id'] = $boy_id;
			$res_data['boy_phone_code'] = $phone_code;
			$res_data['boy_phone'] = $phone;
      $res_data['boy_name']=$name;
			$res_data['verified'] = '0';
			$res_data['message']="Wrong OTP";

      $sCheck = $this->ApidM->checkBoySubsc($boy_id);
      if($sCheck === 1){ $rdata['deposit_status'] = 'ok'; $rdata['deposit_amount']="500"; }
      else{ $rdata['deposit_status'] = 'error'; $rdata['deposit_amount']="500"; }         

			echo json_encode($res_data); exit;
		}
  }

      else if($_SERVER['REQUEST_METHOD'] == 'POST' && $type === "login"){
        $uCheck = $this->ApidM->checkBoyLogin($phone, $phone_code);
        if($uCheck){
          if($otp === $uCheck['boy_otp']){
            $rdata['status'] = 'ok'; $rdata['boy']="1";
            $rdata['boy_phone_code']=$uCheck['boy_phone_code'];
            $rdata['boy_phone']=$uCheck['boy_phone'];
            $rdata['boy_id']=$uCheck['boy_id'];
            $rdata['boy_name']=$uCheck['boy_name'];
            $rdata['message']='login success';
            $sCheck = $this->ApidM->checkBoySubsc($uCheck['boy_id']);
            if($sCheck === 1){ $rdata['deposit_status'] = 'ok'; $rdata['deposit_amount']="500"; }
            else{ $rdata['deposit_status'] = 'error'; $rdata['deposit_amount']="500"; }          


          }else{
            $rdata['status'] = 'error'; $rdata['boy']="1";
            $rdata['boy_phone_code']=$uCheck['boy_phone_code'];
            $rdata['boy_phone']=$uCheck['boy_phone'];
            $rdata['boy_id']='';
            $rdata['user_name']='';
            $rdata['message']='wrong otp';
          } echo json_encode($rdata); exit;
         }

        else{
          $rdata['status'] = 'error'; $rdata['boy']="0"; $rdata['boy_phone_code']=$phone_code; $rdata['boy_phone']=$phone; $rdata['message']='No user or Not approved';
          echo json_encode($rdata); exit;
        }
	  }else{$this->error();}
	}

 
  function checkcoupon(){
    if(strtolower(trim($_POST['boy_couponcode'])) === "ellod8"){
      $rdata['status'] = 'ok';
      $rdata['couponvalid'] = 'ok';
      $rdata['message'] = 'Registration fee of Rs.500 is free for 1 Year';
    }else{
      $rdata['status'] = 'error';
      $rdata['couponvalid'] = 'error';
      $rdata['message'] = 'no Coupon';
    }

    echo json_encode($rdata);

  }

 function register(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "boy"){
        $phone = trim($_POST['boy_phone']);
        $phone_code = trim($_POST['boy_phone_code']);
		$checkPhone =  $this->ApidM->checkBoyExists($phone, $phone_code);
		if($checkPhone){ $this->userExists(); exit; }
		else {
	if(!empty($_FILES['boy_image'])){ $boy_image=$this->ApidM->fileupload('boy_image', 'boys', $phone);}else{$boy_image='0';}
    if(!empty($_FILES['boy_proof1'])){ $boy_proof1=$this->ApidM->fileupload('boy_proof1', 'boys', $phone);}else{$boy_proof1='0';}
	if(!empty($_FILES['boy_proof2'])){ $boy_proof2=$this->ApidM->fileupload('boy_proof2', 'boys', $phone);}else{$boy_proof2='0';}

			$data = array(
				      'boy_name' => trim($_POST['boy_name']),
				      'boy_phone' => trim($_POST['boy_phone']),
                'boy_phone_code' => trim($_POST['boy_phone_code']),
                'boy_image' => $boy_image,
			        	'boy_proof1' => $boy_proof1,
                'boy_proof2' => $boy_proof2,
                'boy_country' => trim($_POST['boy_country']),
                'boy_state' => trim($_POST['boy_state']),
                'boy_city' => trim($_POST['boy_city']),
                'boy_address' => trim($_POST['boy_address']),
                'boy_device' => trim($_POST['boy_device']),
                'boy_token' => trim($_POST['boy_token']),
                'boy_pincode' => trim($_POST['boy_pincode']),
                'boy_wallet' => '0',
                'boy_lat' => trim($_POST['boy_lat']),
                'boy_long' => trim($_POST['boy_long']),
                'boy_astatus' => '0',
                'boy_status' => '0',
                'boy_bank_name' => trim($_POST['boy_bank_name']),
                'boy_bank_branch' => trim($_POST['boy_bank_branch']),
                'boy_bank_acc' => trim($_POST['boy_bank_acc']),
                'boy_bank_ifsc' => trim($_POST['boy_bank_ifsc']),
                'boy_bank_phone' => trim($_POST['boy_bank_phone'])
			);

			$added=$this->ApidM->add_new($data, 'boys');

      if(strtolower(trim($_POST['boy_couponcode'])) === "ellod8"){$this->makeDepositCoup($added);}

			if ($added>0) {
				$msgsent = $this->sendOTP($added, $phone, 'boys');
				$res_data['status'] = 'ok';
				$res_data['type'] = 'boy';
				$res_data['boy_id'] = $added;
				$res_data['boy_phone_code'] = trim($_POST['boy_phone_code']);
				$res_data['boy_phone'] = trim($_POST['boy_phone']);
				$res_data['verified'] = '0';
				$res_data['sms'] = $msgsent;
				$res_data['approval'] = 'pending';
				$res_data['message']="Registration Completed Successfully";
				if($msgsent==='sent'){ $res_data['message2']="Proceed for Verification";}
				else{$res_data['message2']="Sms Sending Failed";}

				echo json_encode($res_data);

				exit;

			}else{$this->error();}
	 	}
	}
}



function login(){
    if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "login"){
        $phone = trim($_POST['boy_phone']);
        $phone_code = trim($_POST['boy_phone_code']);
        $uCheck = $this->ApidM->checkBoyExists($phone, $phone_code);
        if($uCheck){
          if($uCheck['boy_astatus'] === '1'){
            $rdata['status'] = 'ok';
            $rdata['boy']="1";
            $rdata['boy_phone_code'] = $uCheck['boy_phone_code'];
            $rdata['boy_phone']=$uCheck['boy_phone'];
            $rdata['message']='boy exist';
            $otp = $this->sendOTP($uCheck['boy_id'], $uCheck['boy_phone'], 'boys');
            if($otp){$rdata['sms']='sent';}else{$rdata['sms']='failed';}
            echo json_encode($rdata); exit;
          }else{
            $rdata['status'] = 'error'; $rdata['boy']="0"; $rdata['boy_phone_code']=$phone_code; $rdata['boy_phone']=$phone; $rdata['message']='processing at admin';
            echo json_encode($rdata); exit;

          }


          }
         else{
           $rdata['status'] = 'error'; $rdata['boy']="0"; $rdata['boy_phone_code']=$phone_code; $rdata['boy_phone']=$phone; $rdata['message']='no user';
           echo json_encode($rdata); exit;
         }
    }else{$this->error();}
 }

 function update_device(){
	$type = trim($_POST['type']);
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $type === "boy"){
	$boy_id = trim($_POST['boy_id']);
	$boy_device = trim($_POST['boy_device']);
    $boy_token = trim($_POST['boy_token']);
    $boy_lat = trim($_POST['boy_lat']);
	$boy_long = trim($_POST['boy_long']);
	$data = array('boy_device' => $boy_device, 'boy_token' => $boy_token, 'boy_lat' => $boy_lat, 'boy_long' => $boy_long);
  $updated=$this->ApidM->update($data, 'boys', array('boy_id' => $boy_id));	
  $stat = $this->ApidM->get_field('boy_online', 'boys', array('boy_id' => $boy_id));
  $sCheck = $this->ApidM->checkBoySubsc($boy_id);
  if($sCheck === 1){ $res_data['deposit_status'] = 'ok'; $res_data['deposit_amount']="500"; }
  else{ $res_data['deposit_status'] = 'error'; $res_data['deposit_amount']="500"; }
		$res_data['status'] = 'ok';
		$res_data['type'] = 'boy';
		$res_data['boy_id'] = $boy_id;
		$res_data['online'] = $stat;
    echo json_encode($res_data);
   }
   else{$this->error();}
}

function get_new_deliverys(){
  $boy_id = trim($_POST['boy_id']);  
	$boy=$this->ApidM->get_row('boys', array('boy_id' => $boy_id, 'boy_astatus' => '1', 'boy_online' => '1', 'boy_status' => '1'));
  if(!empty($boy)){
  $delvs = $this->ApidM->getNew($boy['boy_lat'], $boy['boy_long'], 13);
	if(!empty($delvs)){
		foreach ($delvs as $row):
		$res_dataa[] = [
    'order_id' => 'ELOCRT'.$row['order_id'],
		'order_seller_id' => $row['order_seller_id'],
		'order_date' => $row['order_date'],
		'order_time' => $row['order_time'],
    'order_pick_date' => $row['order_pick_date'],
    'order_pick_time' => $row['order_pick_time'],
	//	'order_total' => $row['order_total'],
  	'order_type' => $row['order_type'],
		'order_assign' => $row['order_assign'],
		'order_pay_type' => $row['order_pay_type'],
  //  'order_address' => $row['order_address'],
  //  'order_status' => $row['order_status'],
		'seller_name' => $row['seller_name'],
		'seller_store_name' => $row['seller_store_name'],
    'seller_phone' => $row['seller_phone_code'].$row['seller_phone'],
    'seller_store_address' => $row['seller_store_address'],
		'seller_city' => $row['seller_city'],
		'seller_store_image' => $row['seller_store_image'],
	//	'seller_store_time_open' => $row['seller_store_time_open'],
	//	'seller_store_time_close' => $row['seller_store_time_close'],
  //  'seller_lat' => $row['seller_lat'],
  //  'seller_long' => $row['seller_long'],
  //  'addr_id' => $row['addr_id'],
    'addr_address' => $row['addr_address'],
    'addr_city' => $row['addr_city'],
    'addr_pincode' => $row['addr_pincode'],
  //  'addr_lat' => $row['addr_lat'],
  //  'addr_long' => $row['addr_long'],
   // 'distance' => round($row['distance'])
		];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['orders'] = $res_dataa;
	}else{ $res_data['status'] = 'error'; $res_data['orders'] = []; }
		$res_data['type'] = 'new order deliverys';
		$res_data['message'] = 'get new orders within 10 kms';
    echo json_encode($res_data);exit;
  }   else{$this->error();}
}




function get_my_deliverys(){
  $boy_id = trim($_POST['boy_id']);  
	$boy=$this->ApidM->get_row('boys', array('boy_id' => $boy_id, 'boy_astatus' => '1', 'boy_status' => '1'));
  if(!empty($boy)){
  $delvs = $this->ApidM->getMy($boy['boy_lat'], $boy['boy_long'], 100, $boy['boy_id']);
	if(!empty($delvs)){
		foreach ($delvs as $row):
		$res_dataa[] = [
    'order_id' => 'ELOCRT'.$row['order_id'],
		'order_seller_id' => $row['order_seller_id'],
		'order_date' => $row['order_date'],
		'order_time' => $row['order_time'],
    'order_pick_date' => $row['order_pick_date'],
    'order_pick_time' => $row['order_pick_time'],
		'order_total' => $row['order_total'],
		'order_coupon' => $row['order_coupon'],
      'order_final' => $row['order_final'],
      'order_tax' => $row['order_tax'],
      'order_delivery' => $row['order_delivery'],
		'order_type' => $row['order_type'],
		'order_assign' => $row['order_assign'],
			'order_pay_type' => $row['order_pay_type'],
    'order_address' => $row['order_address'],
    'order_status' => $row['order_status'],
		'seller_name' => $row['seller_name'],
		'seller_store_name' => $row['seller_store_name'],
		'seller_phone_code' => $row['seller_phone_code'],
    'seller_phone' => $row['seller_phone'],
    'seller_store_address' => $row['seller_store_address'],
		'seller_city' => $row['seller_city'],
		'seller_store_image' => $row['seller_store_image'],
		'seller_store_time_open' => $row['seller_store_time_open'],
		'seller_store_time_close' => $row['seller_store_time_close'],
    'seller_lat' => $row['seller_lat'],
    'seller_long' => $row['seller_long'],
    'addr_id' => $row['addr_id'],
    'addr_name' => $row['addr_name'],
    'addr_address' => $row['addr_address'],
    'addr_city' => $row['addr_city'],
    'addr_pincode' => $row['addr_pincode'],
    'addr_lat' => $row['addr_lat'],
    'addr_long' => $row['addr_long'],
    'distance' => round($row['distance'])
		];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['orders'] = $res_dataa;
	}else{ $res_data['status'] = 'error'; $res_data['orders'] = []; }
		$res_data['type'] = 'my order deliverys';
		$res_data['message'] = 'get my deliverys';
    echo json_encode($res_data);exit;
  }   else{$this->error();}
}



function get_completed_deliverys(){
  $boy_id = trim($_POST['boy_id']);

  $from = trim($_POST['from']);  
  $to = trim($_POST['to']);  

	$boy=$this->ApidM->get_row('boys', array('boy_id' => $boy_id, 'boy_astatus' => '1', 'boy_status' => '1'));
  if(!empty($boy)){
  $delvs = $this->ApidM->getOver($boy['boy_id'], $from, $to);
$deltotal = 0;

	if(!empty($delvs)){
		foreach ($delvs as $row):

     $dlchg = $row['order_delivery']-5;

		$res_dataa[] = [
    'order_id' => 'ELOCRT'.$row['order_id'],
		'order_seller_id' => $row['order_seller_id'],
//		'order_date' => $row['order_date'],
//		'order_time' => $row['order_time'],
    'order_pick_date' => $row['order_pick_date'],
    'order_pick_time' => $row['order_pick_time'],
		'order_total' => $row['order_total'],
    'order_coupon' => $row['order_coupon'],
    'order_final' => $row['order_final'],
    'order_tax' => $row['order_tax'],
    'order_delivery' => $row['order_delivery'],
		'order_type' => $row['order_type'],
		'order_assign' => $row['order_assign'],
		'order_pay_type' => $row['order_pay_type'],
  //  'order_address' => $row['order_address'],
    'order_status' => $row['order_status'],
		'seller_name' => $row['seller_name'],
		'seller_store_name' => $row['seller_store_name'],
		'seller_phone_code' => $row['seller_phone_code'],
    'seller_phone' => $row['seller_phone'],
    'seller_store_address' => $row['seller_store_address'],
		'seller_city' => $row['seller_city'],
		'seller_store_image' => $row['seller_store_image'],
	//	'seller_store_time_open' => $row['seller_store_time_open'],
//		'seller_store_time_close' => $row['seller_store_time_close'],
//    'seller_lat' => $row['seller_lat'],
 //   'seller_long' => $row['seller_long'],
//    'addr_id' => $row['addr_id'],
//    'addr_address' => $row['addr_address'],
//    'addr_city' => $row['addr_city'],
//    'addr_pincode' => $row['addr_pincode'],
//    'addr_lat' => $row['addr_lat'],
//    'addr_long' => $row['addr_long'],
    'order_earned' => $dlchg
		];

    $deltotal = $deltotal + $dlchg;
		endforeach;

    $res_data['status'] = 'ok';
    $res_data['earned'] = $deltotal;

		$res_data['orders'] = $res_dataa;
	}else{ $res_data['status'] = 'error'; $res_data['earned'] = '0'; $res_data['orders'] = []; }
		$res_data['type'] = 'my order deliverys';
		$res_data['message'] = 'get my deliverys';
    echo json_encode($res_data);exit;
  }   else{$this->error();}
}


function get_delivery_detail(){
  $boy_id = trim($_POST['boy_id']);  
  $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
	$boy=$this->ApidM->get_row('boys', array('boy_id' => $boy_id, 'boy_astatus' => '1', 'boy_status' => '1'));
  $delv = $this->ApidM->getDetail($order_id, $boy['boy_lat'], $boy['boy_long']);

  if(!empty($delv)){
    $res_dataa[] = [
      'order_id' => 'ELOCRT'.$delv['order_id'],
      'order_seller_id' => $delv['order_seller_id'],
    //  'order_date' => $delv['order_date'],
    //  'order_time' => $delv['order_time'],
      'order_pick_date' => $delv['order_pick_date'],
    //  'order_pick_time' => $delv['order_pick_time'],
      'order_total' => $delv['order_total'],
      'order_coupon' => $delv['order_coupon'],
      'order_final' => $delv['order_final'],
      'order_tax' => $delv['order_tax'],
      'order_delivery' => $delv['order_delivery'],
      'order_type' => $delv['order_type'],
      'order_assign' => $delv['order_assign'],
      'order_pay_type' => $delv['order_pay_type'],

      'order_address' => $delv['order_address'],
      'order_status' => $delv['order_status'],
      'seller_name' => $delv['seller_name'],
      'seller_store_name' => $delv['seller_store_name'],
      'seller_phone_code' => $delv['seller_phone_code'],
      'seller_phone' => $delv['seller_phone'],
      'seller_store_address' => $delv['seller_store_address'],
      'seller_city' => $delv['seller_city'],
      'seller_store_image' => $delv['seller_store_image'],
    //  'seller_store_time_open' => $delv['seller_store_time_open'],
    //  'seller_store_time_close' => $delv['seller_store_time_close'],
      'seller_lat' => $delv['seller_lat'],
      'seller_long' => $delv['seller_long'],
      'addr_id' => $delv['addr_id'],
      'addr_address' => $delv['addr_address'],
      'addr_city' => $delv['addr_city'],
      'addr_name' => $delv['addr_name'],
      'addr_phone' => $delv['addr_phone'],
      'addr_pincode' => $delv['addr_pincode'],
      'addr_lat' => $delv['addr_lat'],
      'addr_long' => $delv['addr_long'],
      'distance' => round($delv['distance'])
      ];
      
      
      
      $res_data['status'] = 'ok';
      $res_data['order'] = $res_dataa;
      

      if($delv['order_assign'] > 1){
          $itms = $this->ApidM->getOrdersI($order_id);
                $res_data['items'] = $itms['item'];
      }else{
        $res_data['items'] = [];
      }
      
      
      
      
  }
else{ $res_data['status'] = 'error'; $res_data['order'] = []; }
  $res_data['type'] = 'detail';
  $res_data['message'] = 'get new order detail';
  echo json_encode($res_data);exit;
}  


  
function assign_delivery(){
  $boy_id = trim($_POST['boy_id']);  
  $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
  $data['order_boy'] = $boy_id;
  $data['order_assign'] = '2';
  $updated = $this->ApidM->update($data, 'orders', array('order_id' => $order_id, 'order_assign' => '1'));
  if($updated===1){

    $this->load->model('Notify_model');
    $this->Notify_model->boy($order_id, "assigned"); 

    $res_data['status'] = 'ok';
    $res_data['message'] = 'Delivery Assigned';
    echo json_encode($res_data);
  }else{$this->error();}
}


function update_delivery(){
  $boy_id = trim($_POST['boy_id']);  
  $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
  $order_assign = trim($_POST['order_assign']);
  $data['order_assign'] = $order_assign;
  
  if($order_assign === '4'){$data['order_status'] = '3'; $data['order_delivered'] = date('Y-m-d'); $data['order_deliveredt'] = date('H:m:s');}
  $updated = $this->ApidM->update($data, 'orders', array('order_id' => $order_id, 'order_boy' => $boy_id));
  if($updated===1){
		$this->load->model('Notify_model');
 		if($order_assign == '3'){ $this->Notify_model->user($order_id, "pickedup"); }
 		else if($order_assign == '4'){ $this->Notify_model->user($order_id, "delivered"); }

    $res_data['status'] = 'ok';
    $res_data['message'] = 'Status Updated';
    echo json_encode($res_data);
  }else{$this->error();}
}


function getWallet(){
  $boy_id = trim($_POST['boy_id']);  
  $wall = $this->ApidM->get_row('boys', array('boy_id' => $boy_id));
  if(!empty($wall)){
    $res_data['status'] = 'ok';
    $res_data['boy_name'] = $wall['boy_name'];
    $res_data['boy_wallet'] = $wall['boy_wallet'];
    $res_data['boy_ref_wallet'] = $wall['boy_ref_wallet'];
    $res_data['boy_ref_total'] = $wall['boy_wallet']+$wall['boy_ref_wallet'];
    $res_data['message'] = 'Boy Wallet';

if($res_data['boy_ref_total'] >= 500){ $res_data['settlement'] = 'ok'; }
else{ $res_data['settlement'] = 'error'; }



    $trans = $this->ApidM->getTransac($boy_id);


if(!empty($trans)){
	foreach ($trans as $row):
		$res2_dataa[] = [
      'boyt_id' => 'ELLODT'.$row['boyt_id'],
      'boyt_amount' => $row['boyt_amount'],
      'boyt_date' => $row['boyt_date']
      ];
    endforeach;
    $res_data['transaction_status'] = 'ok';
    $res_data['transactions'] = $res2_dataa;
}else{
  $res_data['transaction_status'] = 'error';
  $res_data['transactions'] = [];
}

    echo json_encode($res_data);
  }else{$this->error();}
}


function reqSettlement(){
  $boy_id = trim($_POST['boy_id']);
  $data['boy_request'] = '1';  
  $updated = $this->ApidM->update($data, 'boys', array('boy_id' => $boy_id));
    $res_data['status'] = 'ok';
    $res_data['message'] = 'Settlement Requested';
    echo json_encode($res_data);
}


function getProfile(){

  $boy_id = trim($_POST['boy_id']);
  $boy=$this->ApidM->get_row('boys', array('boy_id' => $boy_id, 'boy_status' => '1'));

    $res_data['boy_name'] = $boy['boy_name'];
    $res_data['boy_phone'] = $boy['boy_phone'];
    $res_data['boy_phone_code'] = $boy['boy_phone_code'];
    $res_data['boy_image'] = $boy['boy_image'];
    $res_data['boy_country'] = $boy['boy_country'];
    $res_data['boy_state'] = $boy['boy_state'];
    $res_data['boy_city'] = $boy['boy_city'];
    $res_data['boy_address'] = $boy['boy_address'];
    $res_data['status'] = 'ok';
    $res_data['message'] = 'Profile';
    echo json_encode($res_data);

}

function makeDeposit(){
  $data['boyt_boy'] = trim($_POST['boy_id']);
  $data['boyt_amount'] = trim($_POST['pay_amt']);
  $data['boyt_ref'] = trim($_POST['pay_ref']);
  $data['boyt_type'] = '3';  
  $data['boyt_date'] = date("Y-m-d");   
  $data['boyt_time'] = date("H:m:s");  
  $data['boyt_status'] = '1';  
  $data['boyt_valid'] = date('Y-m-d', strtotime("+360 days"));  
  $add = $this->ApidM->add_new($data, 'boys_transac');
  $res_data['status'] = 'ok';
  $res_data['message'] = 'Deposit Success';
  echo json_encode($res_data);
}

function makeDepositCoup($id){
  $data['boyt_boy'] = $id;
  $data['boyt_amount'] = 0;
  $data['boyt_ref'] = "ellod8";
  $data['boyt_type'] = '3';  
  $data['boyt_date'] = date("Y-m-d");   
  $data['boyt_time'] = date("H:m:s");  
  $data['boyt_status'] = '1';
  $data['boyt_valid'] = "2021-06-12";
  $add = $this->ApidM->add_new($data, 'boys_transac');
  $res_data['status'] = 'ok';
  $res_data['message'] = 'Deposit Success';
  return true;
}

function bankDetail(){
  $boy_id = trim($_POST['boy_id']);
  if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "get"){
    $boy=$this->ApidM->get_row('boys', array('boy_id' => $boy_id, 'boy_status' => '1'));
    $res_data['boy_bank_name'] = $boy['boy_bank_name'];
    $res_data['boy_bank_branch'] = $boy['boy_bank_branch'];
    $res_data['boy_bank_acc'] = $boy['boy_bank_acc'];
    $res_data['boy_bank_ifsc'] = $boy['boy_bank_ifsc'];
    $res_data['boy_bank_phone'] = $boy['boy_bank_phone'];
    $res_data['status'] = 'ok';
    $res_data['message'] = 'Bank Details';
    echo json_encode($res_data);
  }
  else if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "add"){
  $data['boy_bank_name'] = trim($_POST['boy_bank_name']);
  $data['boy_bank_branch'] = trim($_POST['boy_bank_branch']);
  $data['boy_bank_acc'] = trim($_POST['boy_bank_acc']);
  $data['boy_bank_ifsc'] = trim($_POST['boy_bank_ifsc']);
  $data['boy_bank_phone'] = trim($_POST['boy_bank_phone']);
  $updated = $this->ApidM->update($data, 'boys', array('boy_id' => $boy_id));
    $res_data['status'] = 'ok';
    $res_data['message'] = 'Bank Update';
    echo json_encode($res_data);
  }
  else{ $this->error(); }
}



function status(){
  $boy_id = trim($_POST['boy_id']);
  $stat = $this->ApidM->get_field('boy_online', 'boys', array('boy_id' => $boy_id));
  if($stat === '0'){ $data['boy_online'] = '1'; }
  else{ $data['boy_online'] = '0'; }
  $updated = $this->ApidM->update($data, 'boys', array('boy_id' => $boy_id));
  $res_data['status'] = 'ok';
  $res_data['online'] = $data['boy_online'];
  $res_data['message'] = 'Status Update';
  echo json_encode($res_data);
}



function call(){
  $this->load->model('Notify_model');
  $user_id = $this->input->post('user_id');
 // $dlt = $this->input->post('user');
  $this->Notify_model->call('user', $user_id);
  $rsd['msg'] = "Incomming Call From Ellocart!";  
  $rsd['status'] = 'ok';   
  echo json_encode($rsd);
}


}