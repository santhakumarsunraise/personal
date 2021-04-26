<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Apiserv extends CI_Controller {

 public function __construct() {

  parent::__construct();
  $this->load->model('ApiserM');
  $this->load->helper('string');
  date_default_timezone_set('Asia/Kolkata');
 }
 function error(){
	$res['status'] = 'error';
	$res['message'] = 'Something went wrong';
	echo json_encode($res); exit;
 }
 function userExists(){
	$res['status'] = 'error';
	$res['message']="User Already Exist";
	echo json_encode($res); exit;
 }

 function index(){
$typ = $this->input->post('api');
if($typ == "tutorials"){ $this->tutorials(); }
elseif($typ == "home"){  $this->home(); }
elseif($typ == "subcat"){  $this->subcat(); }
elseif($typ == "checkservice"){  $this->checkservice(); }
elseif($typ == "subscriptions"){  $this->subscriptions(); }
elseif($typ == "add_subscriptions"){  $this->add_subscriptions(); }
elseif($typ == "get_all_cat"){  $this->get_all_cat(); }
elseif($typ == "get_all_subcat"){  $this->get_all_subcat(); }
elseif($typ == "add_post_check"){  $this->add_post_check(); }
elseif($typ == "add_post"){  $this->add_post(); }
elseif($typ == "my_posts"){  $this->my_posts(); }
elseif($typ == "my_post_detail"){  $this->my_post_detail(); }
elseif($typ == "my_post_edit"){  $this->my_post_edit(); }
elseif($typ == "get_posts"){  $this->get_posts(); }
elseif($typ == "get_post_detail"){  $this->get_post_detail(); }
elseif($typ == "login"){  $this->login(); }
elseif($typ == "verify"){  $this->verify(); }
elseif($typ == "resend"){  $this->resend(); }
elseif($typ == "getcategories"){  $this->categories(); }
elseif($typ == "getsubcategories"){  $this->subcategories(); }
elseif($typ == "addservice"){  $this->addservice(); }
else{$this->error();}
}
 
  
function get_all_cat(){
	$catgs = $this->ApiserM->get_allcategorys();
	if(!empty($catgs)){
		foreach ($catgs as $catg):
	 	 $catgdata[] = [
			'servcat_id' => $catg['servcat_id'],
			'servcat_name' => $catg['servcat_name'],
			'servcat_image' => $catg['servcat_image']
	  	 ];
		endforeach;
		   $res['status'] = "ok"; $res['categories'] = $catgdata;
	}else{ $res['status'] = "error"; $res['categories'] = []; }	
	echo json_encode($res);
}

function get_all_subcat(){
	$cat_id = $this->input->post('servcat_id');
	$subcat =  $this->ApiserM->get_allsubcategorys($cat_id);
	if(!empty($subcat)){
	$res['status'] = 'ok';
	foreach ($subcat as $row):
		$resd[] = [
		'servsubcategory_id' => $row['servsubcategory_id'],
		'servsubcategory_name' => $row['servsubcategory_name'],
		'servsubcategory_image' => $row['servsubcategory_image']
		];
	endforeach;
	$res['subcategories'] = $resd;
	}
	else{
	$res['status'] = 'error';
	$res['subcategories'] = [];
	}
	$res['type'] = 'subcategories';
	$res['servcat_id'] = $cat_id;
	echo json_encode($res);
	exit;	
}




  function home(){
	$user_lat = $this->input->post('user_lat');
	$user_long = $this->input->post('user_long');
	$bnrs = $this->ApiserM->get_banners();
	$catgs = $this->ApiserM->get_homecategorys($user_lat, $user_long);
	if(!empty($bnrs)){
		foreach ($bnrs as $bnr):
	 	 $bnrdata[] = [
			'banner_id' => $bnr['banner_id'],
			'banner_name' => $bnr['banner_name'],
			'banner_image' => $bnr['banner_image']
	  	 ];
		endforeach;
		   $res['banner_status'] = "ok"; $res['banners'] = $bnrdata;
	}else{ $res['banner_status'] = "error"; $res['banners'] = []; }

	if(!empty($catgs)){
		foreach ($catgs as $catg):
	 	 $catgdata[] = [
			'servcat_id' => $catg['servcat_id'],
			'servcat_name' => $catg['servcat_name'],
			'servcat_image' => $catg['servcat_image']
	  	 ];
		endforeach;
		   $res['servcat_status'] = "ok"; $res['categories'] = $catgdata;
	}else{ $res['servcat_status'] = "error"; $res['categories'] = []; }	
	$res['status'] = 'ok';
	echo json_encode($res);
  }
  

function subcat(){
		$cat_id = $this->input->post('servcat_id');
		$subcat =  $this->ApiserM->get_subcategorys($cat_id);
		if(!empty($subcat)){
		$res['status'] = 'ok';
		foreach ($subcat as $row):
			$resd[] = [
			'servsubcategory_id' => $row['servsubcategory_id'],
			'servsubcategory_name' => $row['servsubcategory_name'],
			'servsubcategory_image' => $row['servsubcategory_image']
			];
		endforeach;
		$res['subcategories'] = $resd;
		}
		else{
		$res['status'] = 'error';
		$res['subcategories'] = [];
		}
		$res['type'] = 'subcategories';
		$res['servcat_id'] = $cat_id;
		echo json_encode($res);
		exit;	
}



function checkservice(){
     $user_id = $this->input->post('user_id');
     $check =  $this->ApiserM->check_service($user_id);
    if($check){
        $res['status'] = 'ok';
    }else{
        $res['status'] = 'error';
    }
    echo json_encode($res);
	exit;
}


function subscriptions(){
    $res['status'] = 'ok';
    $res['subscriptions'][] = [
        'subsc_id' => 1,
        'subsc_name' => "Hello",
        'subsc_amount' => 1000,
        'subsc_tax' => 0,
		'subsc_total' => 1000,
        'subsc_percent' => "18%",
        'subsc_validity' => "1 Year",
        'subsc_description' => "Test",
        ];
        echo json_encode($res);
        exit;    
}


function add_subscriptions(){
	date_default_timezone_set('Asia/Kolkata');
	$id = trim($_POST['user_id']);
	$subsc_id = trim($_POST['subsc_id']);
	$pay_ref = trim($_POST['pay_ref']);
	$subsc_amount = trim($_POST['subsc_amount']);
	$subsc_validity = '365';
	$vDate = date('Y-m-d', strtotime("+$subsc_validity days"));  
	$data = array(
		'pay_susbc' => $subsc_id,
		'pay_payer' => $id,
		'pay_ref' => $pay_ref,
		'pay_date' => date("Y-m-d"),
		'pay_time' => date("H:i:s"),
		'pay_validity' => $vDate,
		'pay_amount' => $subsc_amount,
		'pay_for' => 'service',
        'pay_type' => '1',
		'pay_status' => '1'
	);

	$added = $this->ApiserM->add_new($data, 'paymentsserv');
	if($added > 0){ 
        $res_data['status'] = 'ok'; 	$res_data['message'] = 'payment added';
	}
	else{ $res_data['status'] = 'error'; 	$res_data['message'] = 'something wrong';
	}
	$res_data['type'] = 'services';
	echo json_encode($res_data);
    exit;    

}

function add_post_check(){
	$id = trim($_POST['user_id']);
	$cat = trim($_POST['servcat_id']);
	$chk = $this->ApiserM->checkPostPre($id, $cat);
	if($chk){
		$res_data['status'] = 'ok';
	}else{
		$res_data['status'] = 'error';
	}
	echo json_encode($res_data);
    exit;    
}

function add_post(){
	$id = trim($_POST['user_id']);
	if(!empty($_FILES['post_img1'])){
		$post_img1=$this->ApiserM->fileupload('post_img1', 'posts', $id);
	}else{$post_img1='0';}
	$data = array(
		'post_user' => $id,
		'post_cat' =>  $this->input->post('post_cat'),
		'post_scats' => $this->input->post('post_scats'),
		'post_title' => $this->input->post('post_title'),
		'post_address' => $this->input->post('post_address'),
		'post_lat' => $this->input->post('post_lat'),
		'post_long' => $this->input->post('post_long'),
		'post_description' => $this->input->post('post_description'),
		'post_expr' => $this->input->post('post_expr'),
		'post_img1' => $post_img1,
		'post_status' => $this->input->post('post_status'),
		'post_date' => date("Y-m-d"),
		'post_time' => date("H:i:s"),
	);
	$added = $this->ApiserM->add_new($data, 'posts');
	if($added > 0){ 
        $res_data['status'] = 'ok'; $res_data['post_id'] = $added; }
	else{ $res_data['status'] = 'error'; $res_data['post_id'] = ''; }
	$res_data['type'] = 'add services posts';
	$res_data['message'] = 'added';
	echo json_encode($res_data);
    exit;    
}



function my_posts(){
	$id = trim($_POST['user_id']);
	$lst = $this->ApiserM->my_posts($id);
		if(!empty($lst)){
		foreach ($lst as $row):
			if($row['post_img1'] === '0'){ $pimg = ""; }else{ $pimg = $row['post_img1']; }
			$resd[] = [
			'post_id' => $row['post_id'],
			'post_title' => $row['post_title'],
			'post_views' => (int)$row['post_views'],
			'post_img1' => $pimg,
			'servcat_name' => $row['servcat_name'],
			'servcat_image' => $row['servcat_image'],
			];
		endforeach;
		$res['status'] = 'ok';
		$res['posts'] = $resd;
	}
	else{
	$res['status'] = 'error';
	$res['posts'] = [];
	}
	echo json_encode($res);
	exit;	
}


function my_post_detail(){
	$id = trim($_POST['user_id']);
	$post_id = trim($_POST['post_id']);
	$pst = $this->ApiserM->my_post($id, $post_id);
	if(!empty($pst)){
		$subbys = explode(",", $pst['post_scats']);
		
		$subs = [];
		foreach ($subbys as $row):
			$subdt = $this->ApiserM->get_sub($row);
			$subs[] = [
				'servsubcategory_id' => $subdt['servsubcategory_id'],
				'servsubcategory_name' => $subdt['servsubcategory_name'],
				'servsubcategory_image' => $subdt['servsubcategory_image'],
				];
		endforeach;
		if($pst['post_img1'] != '0'){ $pimg = $pst['post_img1']; }else{ $pimg = ""; }
		$res['post_id'] = $pst['post_id'];
		$res['post_cat'] = $pst['post_cat'];
		$res['servcat_name'] = $pst['servcat_name'];
		$res['servcat_image'] = $pst['servcat_image'];
		$res['post_scats'] = $pst['post_scats'];
		$res['servsubcategories'] = $subs;
		$res['post_title'] = $pst['post_title'];
		$res['post_views'] = (int)$pst['post_views'];
		$res['post_address'] = $pst['post_address'];
		$res['post_description'] = $pst['post_description'];
		$res['post_expr'] = $pst['post_expr'];
		$res['post_lat'] = $pst['post_lat'];
		$res['post_long'] = $pst['post_long'];
		$res['post_img1'] = $pimg;
		$res['post_date'] = $pst['post_date'];
		$res['post_time'] = $pst['post_time'];
		$res['post_status'] = $pst['post_status'];
		$res['status'] = 'ok';

	}else{
	$res['status'] = 'error';
	$res['post'] = [];
	}
	echo json_encode($res);
	exit;
}





function my_post_edit(){

	$id = trim($_POST['user_id']);
	$pid = trim($_POST['post_id']);

	if(!empty($_FILES['post_img1'])){
		$data['post_img1'] = $this->ApiserM->fileupload('post_img1', 'posts', $id);
	}
	$data['post_user'] = $id;
	$data['post_cat'] = $this->input->post('post_cat');
	$data['post_scats'] = $this->input->post('post_scats');
	$data['post_title'] = $this->input->post('post_title');
	$data['post_address'] = $this->input->post('post_address');
	$data['post_description'] = $this->input->post('post_description');
	$data['post_expr'] = $this->input->post('post_expr');
	$data['post_lat'] = $this->input->post('post_lat');
	$data['post_long'] = $this->input->post('post_long');
	$data['post_status'] = $this->input->post('post_status');
	$data['post_expr'] = $this->input->post('post_expr');
	$data['post_modified'] = date("Y-m-d");
	$updt = $this->ApiserM->update($data, 'posts', array('post_id' => $pid, 'post_user' => $id));
	if($updt == 1){ 
        $res_data['status'] = 'ok'; $res_data['message'] = 'updated';}
	else{ $res_data['status'] = 'error'; $res_data['message'] = 'Something wrong or Same update!';}
	$res_data['post_id'] = $pid;
	$res_data['type'] = 'update services posts';
	echo json_encode($res_data);
    exit;    
}


function get_posts(){
	$scat_id = $this->input->post('servsubcategory_id');

	$user_lat = $this->input->post('user_lat');
	$user_long = $this->input->post('user_long');
	$psts =  $this->ApiserM->get_posts($scat_id, $user_lat, $user_long);
	if(!empty($psts)){
		foreach ($psts as $row):
			if($row['post_img1'] === '0'){ $pimg = ""; }else{ $pimg = $row['post_img1']; }
			$resd[] = [
			'post_id' => $row['post_id'],
			'post_title' => $row['post_title'],
			'post_img1' => $pimg,
			'post_views' => (int)$row['post_views'],
			'post_rating' => 1,
			];
		endforeach;
		$res['status'] = 'ok';
		$res['posts'] = $resd;
	}
	else{
	$res['status'] = 'error';
	$res['posts'] = [];
	}
	echo json_encode($res);
	exit;	
}



function get_post_detail(){
	$post_id = trim($_POST['post_id']);
	$pst = $this->ApiserM->get_post($post_id);
	if(!empty($pst)){
		$subbys = explode(",", $pst['post_scats']);
		$subs = [];
		foreach ($subbys as $row):
			$subdt = $this->ApiserM->get_sub($row);
			$subs[] = [
				'servsubcategory_id' => $subdt['servsubcategory_id'],
				'servsubcategory_name' => $subdt['servsubcategory_name'],
				'servsubcategory_image' => $subdt['servsubcategory_image'],
				];
		endforeach;
		$otr = $this->ApiserM->get_otherpostcats($pst['post_user']);
		if(!empty($otr)){
		foreach ($otr as $row2):
			$othrs[] = [
				'servcat_id' => $row2['servcat_id'],
				'servcat_name' => $row2['servcat_name'],
				'servcat_image' => $row2['servcat_image']
				];
		endforeach;
		}else{ $othrs = []; }
		if($pst['post_img1'] != '0'){ $pimg = $pst['post_img1']; }else{ $pimg = ""; }
		$res['post_id'] = $pst['post_id'];
		$res['post_cat'] = $pst['post_cat'];
		$res['servcat_name'] = $pst['servcat_name'];
		$res['servcat_image'] = $pst['servcat_image'];
		$res['post_scats'] = $pst['post_scats'];
		$res['servsubcategories'] = $subs;
		$res['post_title'] = $pst['post_title'];
		$res['post_address'] = $pst['post_address'];
		$res['post_description'] = $pst['post_description'];
		$res['post_expr'] = $pst['post_expr'];
		$res['post_img1'] = $pimg;
		$res['post_date'] = $pst['post_date'];
		$res['post_time'] = $pst['post_time'];
		$res['post_status'] = $pst['post_status'];
		$res['post_lat'] = $pst['post_lat'];
		$res['post_long'] = $pst['post_long'];
		$res['post_views'] = (int)$pst['post_views'];
		$res['user_name'] = $pst['user_name'];
		$res['user_image'] = $pst['user_image'];
		$res['user_phone'] = $pst['user_phone_code'].$pst['user_phone'];
		$res['other_services'] = $othrs;
		$res['status'] = 'ok';

		$this->db->set('post_views', 'post_views+1', FALSE);
		$this->db->where('post_id', $post_id);
		$this->db->update('posts');

	}else{
	$res['status'] = 'error';
	$res['post'] = [];
	}
	echo json_encode($res);
	exit;
}






}