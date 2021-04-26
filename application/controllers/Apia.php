<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Apia extends CI_Controller {

 public function __construct() {

  parent::__construct();
  $this->load->model('Api_modela');
  $this->load->helper('string');
  date_default_timezone_set('Asia/Kolkata');
 }

 function index(){
	$res_data['status'] = 'error';
	$res_data['message'] = 'Not Authorized';
	echo json_encode($res_data); exit;
 }

 

 function error(){

	$res_data['status'] = 'error';

	$res_data['message'] = 'Something went wrong';

	echo json_encode($res_data); exit;

 }

 function notfound(){

	$res_data['status'] = 'error';

	$res_data['message'] = 'No data Found';

	echo json_encode($res_data); exit;

 }

 function userExists(){

	$res_data['status'] = 'error';

	$res_data['message']="User Already Exist";

	echo json_encode($res_data); exit;

 }


  







function verify_pin(){

	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "admin"){

		$otp = trim($_POST['otp']);
		if($otp==="1234"){
				$res_data['status'] = 'ok';
				$res_data['message']="Verified";
				echo json_encode($res_data); exit;
		}else{
			$res_data['status'] = 'error';	
			$res_data['message']="Wrong Pin";
			echo json_encode($res_data); exit;
		}

	  }

		else{$this->error();}

	}











function get_categories(){

	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "all"){

		$catg =  $this->Api_model->getAllCategories();

		if(!empty($catg)){

		foreach ($catg as $row):
			$res_dataa[] = [
			'category_id' => $row['category_id'],
			'category_name' => $row['category_name'],
			'category_image' => $row['category_image']
			];
		endforeach;

		$res_data['status'] = 'ok';

		$res_data['categories'] = $res_dataa;

		}else{
			$res_data['status'] = 'error';
			$res_data['categories'] = [];
		}

		$res_data['type'] = 'categories';

		echo json_encode($res_data);

	}

	else if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "my"){

		$id =  trim($_POST["id"]);
		$catg =  $this->Api_model->getMyCategories($id);
		if(!empty($catg)){

		foreach ($catg as $row):
			$res_dataa[] = [
			'category_id' => $row['category_id'],
			'category_name' => $row['category_name'],
			'category_image' => $row['category_image']
			];

		endforeach;

		$res_data['categories'] = $res_dataa;
		$res_data['status'] = 'ok';
	}else{		$res_data['status'] = 'error';
		$res_data['categories'] = [];}
		$res_data['type'] = 'categories';
		echo json_encode($res_data);
	}
	else{$this->error();}
}



function get_subcategories() {

	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "all"){
		$catg_id = trim($_POST["category_id"]);
		$subcatg =  $this->Api_model->getAllSubCategories($catg_id);
		if(!empty($subcatg)){
		foreach ($subcatg as $row):
			$res_dataa[] = [
			'subcategory_id' => $row['subcategory_id'],
			'subcategory_name' => $row['subcategory_name'],
			'subcategory_image' => $row['subcategory_image']

			];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['subcategories'] = $res_dataa;
		}else{
		$res_data['status'] = 'error';
		$res_data['subcategories'] = [];
		}
	
		$res_data['type'] = 'subcategories';
		echo json_encode($res_data);
		exit;
	}

	elseif($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "my"){
		$catg_id = trim($_POST["category_id"]);
		$id = trim($_POST["id"]);
		$subcatg =  $this->Api_model->getMySubCategories($id, $catg_id);
		if(!empty($subcatg)){
		foreach ($subcatg as $row):
			$res_dataa[] = [
			'subcategory_id' => $row['subcategory_id'],
			'subcategory_name' => $row['subcategory_name'],
			'subcategory_image' => $row['subcategory_image']

			];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['subcategories'] = $res_dataa;
	}else{
		$res_data['status'] = 'error';
		$res_data['subcategories'] = [];
	}
		$res_data['type'] = 'my subcategories';
		echo json_encode($res_data);
	}

	else{$this->error();}

}





function get_measures(){

	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "all"){
		$meas =  $this->Api_model->getAllMeasures();
		foreach ($meas as $row):
			$res_dataa[] = [
			'measure_id' => $row['measure_id'],
			'measure_name' => $row['measure_name'],
			];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['type'] = 'measures';
		$res_data['measures'] = $res_dataa;
		echo json_encode($res_data);
	}

	else{$this->error();}
}

// ******************* get main Products

function percent($num, $num2){
    $diff = $num2 - $num;
    $more_less = $diff > 0 ? "+" : "-";
    $diff = abs($diff);
    $percent = ($diff/$num)*100;
    $rnd =  round($percent, 0);
    return $rnd;
}




function get_products(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "my"  && trim($_POST["id"]) != ''){
		$id = trim($_POST["id"]);
		$cat_id = trim($_POST["category_id"]);
		$subcat_id = trim($_POST["subcategory_id"]);
		if($this->input->post('type2') === "b2b"){$b2b = 1;}else{$b2b = 0;}
		$prod =  $this->Api_model->getMyProducts($id, $cat_id, $subcat_id, $b2b);
		if(!empty($prod)){
		foreach ($prod as $row):
			if($row['product_var'] === '1'){
				$varprod =  $this->Api_model->getMyVarProductDetail($id, $row['product_id']);
				if(!empty($varprod)){
					foreach ($varprod as $row2):
						$res_datavar[] = [
							'vproduct_id' => $row2['vproduct_id'], 
							'vproduct_p1' => $row2['vproduct_p1'],
							'vproduct_type' => $row2['vproduct_type'], 
							'vproduct_status' => $row2['vproduct_status'], 
						];
					endforeach;
					$varys = $res_datavar;
					$img1= $varprod[0]['vproduct_img1'];
				}else{ $img1=''; $varys = []; }
			}else{ 
			    
			    		if($row['product_img1'] != '0'){ $img1 = $row['product_img1'];}else{$img1='';}
			if($row['product_img2'] != '0'){ $img2 = $row['product_img2'];}else{$img2='';}
			if($row['product_img3'] != '0'){ $img3 = $row['product_img3'];}else{$img3='';}
			if($row['product_img4'] != '0'){ $img4 = $row['product_img4'];}else{$img4='';}
			    $varys = []; }

			$res_dataa[] = [
			'product_id' => $row['product_id'],
			'product_name' => $row['product_name'],
			'product_category' => $row['product_category'],
			'product_category_name' => $row['category_name'],
			'product_subcategory' => $row['product_subcategory'],
			'product_subcategory_name' => $row['subcategory_name'],
			'product_measure' => $row['product_measure'],
			'product_var' => $row['product_var'],
			'product_type' => $row['product_type'],
			'product_mrp' => $row['product_mrp'],
			'product_sale' => $row['product_sale'],
			'product_img1' => $img1,
			'product_img2' => "",
			'product_img3' => "",
			'product_img4' => "",
			'product_stock' => $row['product_stock'],
			'product_description' => $row['product_description'],
			'product_a_status' => $row['product_a_status'],
			'product_status' => $row['product_status'],
			'product_vars' => $varys,
			];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['products'] = $res_dataa;
	}else{
		$res_data['status'] = 'error';
		$res_data['products'] = [];
	}
		$res_data['type'] = 'products';
		$res_data['subcategory_id'] = $subcat_id;
		echo json_encode($res_data);
	}
	else{$this->error();}
}



function get_product_detail(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "my"  && trim($_POST["id"]) != ''){
		$id = trim($_POST["id"]);
		$product_id = trim($_POST["product_id"]);
		$prod =  $this->Api_model->getMyProductDetail($id, $product_id);
	if(!empty($prod)){
	if($prod['product_img1'] != '0'){ $img1 = $prod['product_img1']; }else{$img1='';}
	if($prod['product_img2'] != '0'){ $img2 = $prod['product_img2']; }else{$img2='';}
	if($prod['product_img3'] != '0'){ $img3 = $prod['product_img3']; }else{$img3='';}
	if($prod['product_img4'] != '0'){ $img4 = $prod['product_img4']; }else{$img4='';}
	$images = array();
	if($prod['product_img1'] != '0'){ $images[] = $prod['product_img1']; }
	if($prod['product_img2'] != '0'){ $images[] = $prod['product_img2']; }
	if($prod['product_img3'] != '0'){ $images[] = $prod['product_img3']; }
	if($prod['product_img4'] != '0'){ $images[] = $prod['product_img4']; }
		$res_dataa[] = [
			'product_id' => $prod['product_id'],
			'product_name' => $prod['product_name'],
			'product_category' => $prod['product_category'],
			'product_category_name' => $prod['category_name'],
			'product_subcategory' => $prod['product_subcategory'],
			'product_subcategory_name' => $prod['subcategory_name'],
			'product_measure' => $prod['product_measure'],
			'product_measure_name' => $prod['measure_name'],
			'product_var' => $prod['product_var'],
			'product_type' => $prod['product_type'],
			'product_mrp' => $prod['product_mrp'],
			'product_sale' => $prod['product_sale'],
			'product_stock' => $prod['product_stock'],
			'product_img1' => $img1,
			'product_img2' => $img2,
			'product_img3' => $img3,
			'product_img4' => $img4,
			'product_description' => $prod['product_description'],
			'product_a_status' => $prod['product_a_status'],
			'product_status' => $prod['product_status'],
			'product_review' => 3
		];
	
		if($prod['product_var'] === '1'){
			$varprod =  $this->Api_model->getMyVarProductDetail($id, $product_id);
			if(!empty($varprod)){
				$im = 0;
				foreach ($varprod as $row2):

					if($row2['vproduct_img1'] != '0'){ $img1 = $row2['vproduct_img1']; }else{$img1='';}
					if($row2['vproduct_img2'] != '0'){ $img2 = $row2['vproduct_img2']; }else{$img2='';}
					if($row2['vproduct_img3'] != '0'){ $img3 = $row2['vproduct_img3']; }else{$img3='';}
					if($row2['vproduct_img4'] != '0'){ $img4 = $row2['vproduct_img4']; }else{$img4='';}
					$res_datavar[] = [
						'vproduct_id' => $row2['vproduct_id'], 
						'vproduct_p1' => $row2['vproduct_p1'],
						'vproduct_type' => $row2['vproduct_type'], 
						'vproduct_img1' => $img1,
						'vproduct_img2' => $img2,
						'vproduct_img3' => $img3,
						'vproduct_img4' => $img4,
						'vproduct_status' => $row2['vproduct_status'], 
					];
if($im === 0){
	if($img1 != ''){ $images[] = $img1; }
	if($img2 != ''){ $images[] = $img2; }
	if($img3 != ''){ $images[] = $img3; }
	if($img4 != ''){ $images[] = $img4; }
}

					$im++;
				endforeach;
				$res_data['status_var'] = 'ok';
				$res_data['product_var_data'] = $res_datavar;
		}else{ $res_data['status_var'] = 'error'; $res_data['product_var_data'] = []; }
	 }

		$res_data['status'] = 'ok';
		$res_data['product_data'] = $res_dataa;
		$res_data['product_images'] = $images;
	}else{
		$res_data['status'] = 'error';
		 $res_data['product_data'] = []; 
	}
		$res_data['type'] = 'product';
		$res_data['product_id'] = $product_id;
		echo json_encode($res_data);

	}
	else{$this->error();}
}


function get_products_sub(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "my"  && trim($_POST["id"]) != ''){
		$id = trim($_POST["id"]);
		$product_id = trim($_POST["product_id"]);
		$vproduct_id = trim($_POST["vproduct_id"]);
		$var =  $this->Api_model->getMyProductDetailvar($id, $product_id, $vproduct_id);
		$prod =  $this->Api_model->getMyProductDetailSub($id, $product_id, $vproduct_id);
		if($var['vproduct_img1'] != '0'){ $img1 = $var['vproduct_img1']; }else{$img1='';}
		if($var['vproduct_img2'] != '0'){ $img2 = $var['vproduct_img2']; }else{$img2='';}
		if($var['vproduct_img3'] != '0'){ $img3 = $var['vproduct_img3']; }else{$img3='';}
		if($var['vproduct_img4'] != '0'){ $img4 = $var['vproduct_img4']; }else{$img4='';}
		$images = array();
		if($var['vproduct_img1'] != '0'){ $images[] = $var['vproduct_img1']; }
		if($var['vproduct_img2'] != '0'){ $images[] = $var['vproduct_img2']; }
		if($var['vproduct_img3'] != '0'){ $images[] = $var['vproduct_img3']; }
		if($var['vproduct_img4'] != '0'){ $images[] = $var['vproduct_img4']; }
		$res_datavar[] = [
			'vproduct_id' => $var['vproduct_id'], 
			'vproduct_p1' => $var['vproduct_p1'],
			'vproduct_type' => $var['vproduct_type'], 
			'vproduct_img1' => $img1,
			'vproduct_img2' => $img2,
			'vproduct_img3' => $img3,
			'vproduct_img4' => $img4,
			'vproduct_status' => $var['vproduct_status'], 
		];
		$res_data['product_var'] = $res_datavar;
		$res_data['product_var_images'] = $images;
		if(!empty($prod)){
		foreach ($prod as $row):
			$res_dataa[] = [
			'sproduct_id' => $row['sproduct_id'],
			'sproduct_stock' => $row['sproduct_stock'],
			'sproduct_mrp' => $row['sproduct_mrp'],
			'sproduct_sale' => $row['sproduct_sale'],
			'sproduct_p2' => $row['sproduct_p2'],
			'sproduct_p3' => $row['sproduct_p3'],
			'sproduct_p4' => $row['sproduct_p4'],
			'sproduct_p5' => $row['sproduct_p5'],
			'sproduct_status' => $row['sproduct_status'],
			'sproduct_review' => 3
			];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['products_sub'] = $res_dataa;
	}else{
		$res_data['status'] = 'error';

		$res_data['products_sub'] = [];
	}
		$res_data['product_id'] = $product_id;
		$res_data['vproduct_id'] = $vproduct_id;
		$res_data['type'] = 'products sub';
		$res_data['message'] = 'my sub product';
		echo json_encode($res_data);
	}else{ $this->error(); }
}



function get_product_sub_detail(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "my"  && trim($_POST["id"]) != ''){
		$id = trim($_POST["id"]);
		$product_id = trim($_POST["product_id"]);
		$sproduct_id = trim($_POST["sproduct_id"]);
		$row =  $this->Api_model->getMyProductOnlySub($id, $product_id, $sproduct_id);
	
		if(!empty($row)){

			$res_dataa[] = [
			'sproduct_id' => $row['sproduct_id'],
			'sproduct_stock' => $row['sproduct_stock'],
			'sproduct_mrp' => $row['sproduct_mrp'],
			'sproduct_sale' => $row['sproduct_sale'],
			'sproduct_p2' => $row['sproduct_p2'],
			'sproduct_p3' => $row['sproduct_p3'],
			'sproduct_p4' => $row['sproduct_p4'],
			'sproduct_p5' => $row['sproduct_p5'],
			'sproduct_status' => $row['sproduct_status'],
			'sproduct_review' => 3
			];
		$res_data['status'] = 'ok';
		$res_data['product_sub'] = $res_dataa;
	}else{
		$res_data['status'] = 'error';

		$res_data['product_sub'] = [];
	}
		$res_data['product_id'] = $product_id;
		$res_data['sproduct_id'] = $sproduct_id;
		$res_data['type'] = 'product sub';
		$res_data['message'] = 'my sub product';
		echo json_encode($res_data);
	}else{ $this->error(); }
}



function upload_product(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "upload"){
	$unique = "ELOC".random_string('alnum',6).random_string('alnum',6);
	if(!empty($_FILES['product_img1'])){
		$product_img1=$this->Api_model->fileupload('product_img1', 'products', $unique);
	}else{$product_img1='0';}
	if(!empty($_FILES['product_img2'])){
		$product_img2=$this->Api_model->fileupload('product_img2', 'products', $unique);
	}else{$product_img2='0';}
	if(!empty($_FILES['product_img3'])){
		$product_img3=$this->Api_model->fileupload('product_img3', 'products', $unique);
	}else{$product_img3='0';}
	if(!empty($_FILES['product_img4'])){
		$product_img4=$this->Api_model->fileupload('product_img4', 'products', $unique);
	}else{$product_img4='0';}


	if($this->input->post('product_b2')){ $b2 = 1; }else{ $b2 = 0; }


	$data = array(
		'product_store' => trim($_POST['id']),
		'product_name' => trim($_POST['product_name']),
		'product_category' => trim($_POST['product_category']),
		'product_subcategory' => trim($_POST['product_subcategory']),
		'product_measure' => trim($_POST['product_measure']),
		'product_var' => trim($_POST['product_var']),
		'product_type' => trim($_POST['product_type']),
		'product_stock' => trim($_POST['product_stock']),
		'product_mrp' => trim($_POST['product_mrp']),
		'product_sale' => trim($_POST['product_sale']),
		'product_img1' => $product_img1,
		'product_img2' => $product_img2,
		'product_img3' => $product_img3,
		'product_img4' => $product_img4,
		'product_description' => trim($_POST['product_description']),
		'product_modified' => date('Y-m-d H:i:s'),
		'product_a_status' => '1',
		'product_b2' => $b2,
		'product_status' => trim($_POST['product_status'])
	);
	$added=$this->Api_model->add_new($data, 'products');
	if($added){

		if($this->input->post('type2') === "b2b"){
			$b2 = array('product_b2' => '1');
			$upd = $this->Api_model->update($b2, 'products', array('product_id' => $added));
		}

		$res_data['status'] = 'ok';
		$res_data['type'] = 'product_upload';
		$res_data['product_id'] = $added;
		$res_data['message'] = 'product uploaded';
		echo json_encode($res_data);
	}else{
		$this->error();
	}
	}
	else{$this->error();}
}

function upload_product_var(){
	$id = trim($_POST['id']);
	$product_id = trim($_POST['product_id']);
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "upload_var"){
	if(!empty($_FILES['product_img1'])){
		$product_img1=$this->Api_model->fileupload('product_img1', 'products', $id);
	}else{$product_img1='0';}
	if(!empty($_FILES['product_img2'])){
		$product_img2=$this->Api_model->fileupload('product_img2', 'products', $id);
	}else{$product_img2='0';}
	if(!empty($_FILES['product_img3'])){
		$product_img3=$this->Api_model->fileupload('product_img3', 'products', $id);
	}else{$product_img3='0';}
	if(!empty($_FILES['product_img4'])){
		$product_img4=$this->Api_model->fileupload('product_img4', 'products', $id);
	}else{$product_img4='0';}
	$vdat['vproduct_main'] = $product_id;
	$vdat['vproduct_store'] = $id;
	$vdat['vproduct_type'] = trim($_POST['product_type']);
	$vdat['vproduct_p1'] = trim($_POST['vproduct_p1']);
	$vdat['vproduct_img1'] = $product_img1;
	$vdat['vproduct_img2'] = $product_img2;
	$vdat['vproduct_img3'] = $product_img3;
	$vdat['vproduct_img4'] = $product_img4;
	$vdat['vproduct_status'] = trim($_POST['vproduct_status']);
	$varadd = $this->Api_model->add_new($vdat, 'productsvar');
	if($varadd > 0){
		$res_data['status'] = 'ok';
		$res_data['id'] = $id;
		$res_data['product_id'] = $product_id;
		$res_data['vproduct_id'] = $varadd;
		$res_data['type'] = 'upload_var';
		$res_data['message'] = 'product var uploaded';
		echo json_encode($res_data);
	}else{ $this->error(); }
	}else{$this->error();}
}

function upload_product_sub(){
	$id = trim($_POST['id']);
	$product_id = trim($_POST['product_id']);
	$vproduct_id = trim($_POST['vproduct_id']);
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "upload_sub"){
			$sdat['sproduct_main'] = $product_id;
			$sdat['sproduct_var'] = $vproduct_id;
			$sdat['sproduct_store'] = $id;
			$sdat['sproduct_stock'] = trim($_POST['sproduct_stock']);
			$sdat['sproduct_mrp'] = trim($_POST['sproduct_mrp']);
			$sdat['sproduct_sale'] = trim($_POST['sproduct_sale']);
			$sdat['sproduct_p2'] = trim($_POST['sproduct_p2']);
			$sdat['sproduct_p3'] = trim($_POST['sproduct_p3']);
			$sdat['sproduct_p4'] = trim($_POST['sproduct_p4']);
			$sdat['sproduct_p5'] = trim($_POST['sproduct_p5']);
			$sdat['sproduct_status'] = trim($_POST['sproduct_status']);
		$subadd =$this->db->insert('productssub', $sdat);
		if($subadd){
		$res_data['status'] = 'ok';
		$res_data['product_id'] = $product_id;
		$res_data['type'] = 'upload_sub';
		$res_data['message'] = 'product sub uploaded';
		echo json_encode($res_data);
	}else{ $this->error(); }
	}else{$this->error();}
}






function update_product(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "update"){
	$id = trim($_POST['id']);
	$product_id = trim($_POST['product_id']);
	$product_type = trim($_POST['product_type']);
	if(trim(isset($_POST['product_img1'])) != '0' && !empty($_FILES['product_img1'])){
		$data['product_img1'] = $this->Api_model->fileupload('product_img1', 'products', $id);
	}elseif($_POST['product_img1'] != '0' && empty($_FILES['product_img1'])){$data['product_img1'] = '0';}
	if(trim(isset($_POST['product_img2'])) != '0' && !empty($_FILES['product_img2'])){
		$data['product_img2'] = $this->Api_model->fileupload('product_img2', 'products', $id);
	}elseif($_POST['product_img2'] != '0' && empty($_FILES['product_img2'])){$data['product_img2'] = '0';}
	if(trim(isset($_POST['product_img3'])) != '0' && !empty($_FILES['product_img3'])){
		$data['product_img3'] = $this->Api_model->fileupload('product_img3', 'products', $id);
	}elseif($_POST['product_img3'] != '0' && empty($_FILES['product_img3'])){$data['product_img3'] = '0';}
	if(trim(isset($_POST['product_img4'])) != '0' && !empty($_FILES['product_img4'])){
		$data['product_img4'] = $this->Api_model->fileupload('product_img4', 'products', $id);
	}elseif($_POST['product_img4'] != '0' && empty($_FILES['product_img4'])){$data['product_img4'] = '0';}
	$data['product_name'] = trim($_POST['product_name']);
	$data['product_category'] = trim($_POST['product_category']);
	$data['product_subcategory'] = trim($_POST['product_subcategory']);
	$data['product_measure'] = trim($_POST['product_measure']);
	$data['product_var'] = trim($_POST['product_var']);
	$data['product_type'] = trim($_POST['product_type']);
	$data['product_stock'] = trim($_POST['product_stock']);
	$data['product_mrp'] = trim($_POST['product_mrp']);
	$data['product_sale'] = trim($_POST['product_sale']);
	$data['product_description'] = trim($_POST['product_description']);
	$data['product_status'] = trim($_POST['product_status']);
	$updated = $this->Api_model->update($data, 'products', array('product_store' => $id, 'product_id' => $product_id));
	if($updated){
		$res_data['status'] = 'ok';
		$res_data['type'] = 'product_update';
		$res_data['message'] = 'product updated';
		$res_data['product_id'] = $product_id;
		echo json_encode($res_data);
	}else{
	$this->error();
	}
  }
  else{$this->error();}
}


function update_product_var(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "update"){
	$id = trim($_POST['id']);
	$product_id = trim($_POST['product_id']);
	$vproduct_id = trim($_POST['vproduct_id']);
	if(trim(isset($_POST['vproduct_img1'])) != '0' && !empty($_FILES['vproduct_img1'])){
		$data['vproduct_img1'] = $this->Api_model->fileupload('vproduct_img1', 'products', $id);
	}elseif($_POST['vproduct_img1'] != '0' && empty($_FILES['vproduct_img1'])){$data['vproduct_img1'] = '0';}
	if(trim(isset($_POST['vproduct_img2'])) != '0' && !empty($_FILES['vproduct_img2'])){
		$data['vproduct_img2'] = $this->Api_model->fileupload('vproduct_img2', 'products', $id);
	}elseif($_POST['vproduct_img2'] != '0' && empty($_FILES['vproduct_img2'])){$data['vproduct_img2'] = '0';}
	if(trim(isset($_POST['vproduct_img3'])) != '0' && !empty($_FILES['vproduct_img3'])){
		$data['vproduct_img3'] = $this->Api_model->fileupload('vproduct_img3', 'products', $id);
	}elseif($_POST['vproduct_img3'] != '0' && empty($_FILES['vproduct_img3'])){$data['vproduct_img3'] = '0';}
	if(trim(isset($_POST['vproduct_img4'])) != '0' && !empty($_FILES['vproduct_img4'])){
		$data['vproduct_img4'] = $this->Api_model->fileupload('vproduct_img4', 'products', $id);
	}elseif($_POST['vproduct_img4'] != '0' && empty($_FILES['vproduct_img4'])){$data['vproduct_img4'] = '0';}
	$data['vproduct_type'] = trim($_POST['product_type']);
	$data['vproduct_p1'] = trim($_POST['vproduct_p1']);
	$data['vproduct_status'] = trim($_POST['vproduct_status']);
	$updated = $this->Api_model->update($data, 'productsvar', array('vproduct_store' => $id, 'vproduct_main' => $product_id, 'vproduct_id' => $vproduct_id));
	if($updated){
		$res_data['status'] = 'ok';
		$res_data['type'] = 'product_var_update';
		$res_data['message'] = 'product var updated';
		$res_data['product_id'] = $product_id;
		$res_data['vproduct_id'] = $vproduct_id;
		echo json_encode($res_data);
	}else{
	$this->error();
	}
  }
  else{$this->error();}
}



function update_product_sub(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "update"){
	$id = trim($_POST['id']);
	$product_id = trim($_POST['product_id']);
	$sproduct_id = trim($_POST['sproduct_id']);
	$data['sproduct_stock'] = trim($_POST['sproduct_stock']);
	$data['sproduct_mrp'] = trim($_POST['sproduct_mrp']);
	$data['sproduct_sale'] = trim($_POST['sproduct_sale']);
	$data['sproduct_p2'] = trim($_POST['sproduct_p2']);
	$data['sproduct_p3'] = trim($_POST['sproduct_p3']);
	$data['sproduct_p4'] = trim($_POST['sproduct_p4']);
	$data['sproduct_p5'] = trim($_POST['sproduct_p5']);
	$data['sproduct_status'] = trim($_POST['sproduct_status']);
	$updated = $this->Api_model->update($data, 'productssub', array('sproduct_store' => $id, 'sproduct_id' => $sproduct_id));
	if($updated){
		$res_data['status'] = 'ok';
		$res_data['type'] = 'product_sub_update';
		$res_data['message'] = 'product sub updated';
		$res_data['product_id'] = $product_id;
		$res_data['sproduct_id'] = $sproduct_id;
		echo json_encode($res_data);
	}else{
	$this->error();
	}
  }
  else{$this->error();}
}




function get_profile_detail(){
	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "seller"  && trim($_POST["id"]) != ''){
		$id = trim($_POST["id"]);
		$phone = trim($_POST['phone']);
		$prof =  $this->Api_model->getMyProfile($id, $phone);
	if(!empty($prof)){
	if($prof['seller_store_image'] != '0'){ $seller_store_image = $prof['seller_store_image'];}else{$seller_store_image='';}
	if($prof['seller_pan_image'] != '0'){ $seller_pan_image = $prof['seller_pan_image'];}else{$seller_pan_image='';}
		$res_dataa[] = [
			'id' => $prof['seller_id'],
			'name' => $prof['seller_name'],
			'store_name' => $prof['seller_store_name'],
			'email' => $prof['seller_email'],
			'phone_code' => $prof['seller_phone_code'],
			'phone' => $prof['seller_phone'],
			'store_address' => $prof['seller_store_address'],
			'country' => $prof['seller_country'],
			'state' => $prof['seller_state'],
			'city' => $prof['seller_city'],
			'pincode' => $prof['seller_pincode'],
			'store_image' => $seller_store_image,
			'pan_number' => $prof['seller_pan_number'],
			'pan_image' => $seller_pan_image,
			'licence_number' => $prof['seller_licence_number'],
			'minimum_order' => $prof['seller_minimum_order'],
			'store_time_open' => $prof['seller_store_time_open'],
			'store_time_close' => $prof['seller_store_time_close'],
			'lat' => $prof['seller_lat'],
			'long' => $prof['seller_long'],
			'status' => $prof['seller_status']
		];
	$res_data['profile'] = $res_dataa;

	}else{
		$res_data['profile'] = [];}
		$res_data['status'] = 'ok';
		$res_data['type'] = 'product';
		$res_data['id'] = $id;
		echo json_encode($res_data);
	}
	else{$this->error();}
}









function get_banners(){

	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "seller"){

		$type = trim($_POST["type"]);

		$banners =  $this->Api_model->getBanners($type);

		if(!empty($banners)){

			foreach ($banners as $row):

			$res_dataa[] = [

			'banner_id' => $row['banner_id'],

			'banner_name' => $row['banner_name'],

			'banner_image' => $row['banner_image'],

			];

			endforeach;

			$res_data['banners'] = $res_dataa;

		}else{ $res_data['banners'] = []; }



		$res_data['status'] = 'ok';

		$res_data['type'] = 'seller';

		$res_data['message'] = 'banners';

		echo json_encode($res_data);

	}

	else{$this->error();}

}




function orders(){

	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "received"){

		$orders = $this->Api_modela->getOrdersR();
		if(!empty($orders)){
		  foreach ($orders as $row):
			$tItems = $this->Api_modela->count_rows('ordered', 'ordered_order', $row['order_id']);


if($row['seller_store_image'] != "0"){ $strimg = $row['seller_store_image']; }else{ $strimg = "https://ellocart.com/assets/img/ellocart.png"; }

			$res_dataa[] = [
			'order_id' => 'ELOCRT'.$row['order_id'],
			'user_id' => $row['user_id'],
			'user_name' => $row['user_name'],
			'user_phone' => $row['user_phone_code'].$row['user_phone'],
			'user_image' => $row['user_image'],
			'seller_id' => $row['seller_id'],
			'seller_store_name' => $row['seller_store_name'],
			'seller_store_image' => $strimg,
			'seller_city' => $row['seller_city'],
			'order_date' => $row['order_date'],
			'order_time' => $row['order_time'],
			'order_pick_date' => $row['order_pick_date'],
			'order_pick_time' => $row['order_pick_time'],
			'order_total_items' => $tItems,
			'order_tax' => $row['order_tax'],
			'order_delivery' => $row['order_delivery'],
			'order_total' => $row['order_total'],
			'order_coupon' => $row['order_coupon'],
			'order_final' => $row['order_final'],
			'order_type' => $row['order_type'],
			'order_pay_type' => $row['order_pay_type'],
			'order_payref' => $row['order_payref'],
			'order_boy' => $row['order_boy'],
			'order_assign' => $row['order_assign'],
			'order_status' => $row['order_status'],
			];
		  endforeach;

		  $rdata['status'] = 'ok';
		  $rdata['orders'] = $res_dataa;
		  $rdata['message'] = 'seller orders';

		}else{

		  $rdata['status'] = 'error';

		  $rdata['orders'] = [];

		  $rdata['message'] = 'no seller orders';

		}

		  $rdata['type'] = 'received';

		  echo json_encode($rdata);

	  }



	else if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "pending"){


	$orders = $this->Api_modela->getOrdersP();

	if(!empty($orders)){

	  foreach ($orders as $row):

		$tItems = $this->Api_modela->count_rows('ordered', 'ordered_order', $row['order_id']);
		$res_dataa[] = [
		'order_id' => 'ELOCRT'.$row['order_id'],

		'user_id' => $row['user_id'],

		'user_name' => $row['user_name'],

		'user_phone' => $row['user_phone_code'].$row['user_phone'],

		'user_image' => $row['user_image'],

		'seller_id' => $row['seller_id'],
		'seller_store_name' => $row['seller_store_name'],
		'seller_store_image' => $row['seller_store_image'],
		'seller_city' => $row['seller_city'],


		'order_date' => $row['order_date'],
		'order_time' => $row['order_time'],

		'order_pick_date' => $row['order_pick_date'],

		'order_pick_time' => $row['order_pick_time'],

		'order_total_items' => $tItems,

		'order_total' => $row['order_total'],
		
					'order_tax' => $row['order_tax'],
			'order_delivery' => $row['order_delivery'],
			'order_coupon' => $row['order_coupon'],
			'order_final' => $row['order_final'],
		
			'order_type' => $row['order_type'],

			'order_pay_type' => $row['order_pay_type'],
			'order_payref' => $row['order_payref'],
			'order_boy' => $row['order_boy'],
			'order_assign' => $row['order_assign'],


		'order_status' => $row['order_status'],

		];

	  endforeach;

	  $rdata['status'] = 'ok';

	  $rdata['orders'] = $res_dataa;

	  $rdata['message'] = 'seller orders';

	}else{

	  $rdata['status'] = 'error';

	  $rdata['orders'] = [];

	  $rdata['message'] = 'no seller orders';

	}

	  $rdata['type'] = 'pending';


	  echo json_encode($rdata);

  }
  
  
  
  	else if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "assigned"){


	$orders = $this->Api_modela->getOrdersA();

	if(!empty($orders)){

	  foreach ($orders as $row):

		$tItems = $this->Api_modela->count_rows('ordered', 'ordered_order', $row['order_id']);

		$res_dataa[] = [

		'order_id' => 'ELOCRT'.$row['order_id'],

		'user_id' => $row['user_id'],

		'user_name' => $row['user_name'],

		'user_phone' => $row['user_phone_code'].$row['user_phone'],

		'user_image' => $row['user_image'],

		'seller_id' => $row['seller_id'],
			'seller_store_name' => $row['seller_store_name'],
			'seller_store_image' => $row['seller_store_image'],
			'seller_city' => $row['seller_city'],

		'order_date' => $row['order_date'],
		'order_time' => $row['order_time'],

		'order_pick_date' => $row['order_pick_date'],

		'order_pick_time' => $row['order_pick_time'],

		'order_total_items' => $tItems,

		'order_total' => $row['order_total'],
		'order_coupon' => $row['order_coupon'],

					'order_tax' => $row['order_tax'],
			'order_delivery' => $row['order_delivery'],
			'order_final' => $row['order_final'],
		
			'order_type' => $row['order_type'],

			'order_pay_type' => $row['order_pay_type'],
			'order_payref' => $row['order_payref'],
			'order_boy' => $row['order_boy'],
			'order_assign' => $row['order_assign'],


		'order_status' => $row['order_status'],

		];

	  endforeach;

	  $rdata['status'] = 'ok';

	  $rdata['orders'] = $res_dataa;

	  $rdata['message'] = 'seller orders';

	}else{

	  $rdata['status'] = 'error';

	  $rdata['orders'] = [];

	  $rdata['message'] = 'no seller orders';

	}

	  $rdata['type'] = 'pending';


	  echo json_encode($rdata);

  }
  

  

  elseif($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "completed"){


	$orders = $this->Api_modela->getOrdersC();

	if(!empty($orders)){

	  foreach ($orders as $row):

		$tItems = $this->Api_modela->count_rows('ordered', 'ordered_order', $row['order_id']);

		$res_dataa[] = [

			'order_id' => 'ELOCRT'.$row['order_id'],

			'user_id' => $row['user_id'],

			'user_name' => $row['user_name'],

			'user_phone' => $row['user_phone_code'].$row['user_phone'],

			'user_image' => $row['user_image'],

			'seller_id' => $row['seller_id'],
			'seller_store_name' => $row['seller_store_name'],
			'seller_store_image' => $row['seller_store_image'],
			'seller_city' => $row['seller_city'],

			'order_date' => $row['order_date'],
			'order_time' => $row['order_time'],

			'order_pick_date' => $row['order_pick_date'],

			'order_pick_time' => $row['order_pick_time'],

			'order_total_items' => $tItems,

			'order_total' => $row['order_total'],
			
						'order_tax' => $row['order_tax'],
			'order_delivery' => $row['order_delivery'],
			'order_coupon' => $row['order_coupon'],
			'order_final' => $row['order_final'],
			

				'order_type' => $row['order_type'],
			'order_pay_type' => $row['order_pay_type'],
			'order_payref' => $row['order_payref'],
			'order_boy' => $row['order_boy'],
			'order_assign' => $row['order_assign'],

			
			'order_status' => $row['order_status'],
		];

	  endforeach;

	  $rdata['status'] = 'ok';

	  $rdata['orders'] = $res_dataa;

	  $rdata['message'] = 'seller orders';

	}else{

	  $rdata['status'] = 'error';

	  $rdata['orders'] = [];

	  $rdata['message'] = 'no seller orders';

	}

	  $rdata['type'] = 'completed';


	  echo json_encode($rdata);

  }





  elseif($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "cancelled"){


	$orders = $this->Api_modela->getOrdersCn($seller_id);

	if(!empty($orders)){

	  foreach ($orders as $row):

		$tItems = $this->Api_model->count_rows('ordered', 'ordered_order', $row['order_id']);

		$res_dataa[] = [

			'order_id' => 'ELOCRT'.$row['order_id'],

			'user_id' => $row['user_id'],

			'user_name' => $row['user_name'],

			'user_phone' => $row['user_phone_code'].$row['user_phone'],

			'user_image' => $row['user_image'],
			'seller_id' => $row['seller_id'],
			'seller_store_name' => $row['seller_store_name'],
			'seller_store_image' => $row['seller_store_image'],
			'seller_city' => $row['seller_city'],

			'order_date' => $row['order_date'],
			'order_time' => $row['order_time'],

			'order_pick_date' => $row['order_pick_date'],

			'order_pick_time' => $row['order_pick_time'],

			'order_total_items' => $tItems,

			'order_total' => $row['order_total'],
			'order_coupon' => $row['order_coupon'],

			'order_tax' => $row['order_tax'],
			'order_delivery' => $row['order_delivery'],
			'order_final' => $row['order_final'],

			'order_type' => $row['order_type'],
			'order_pay_type' => $row['order_pay_type'],
			'order_payref' => $row['order_payref'],
			'order_boy' => $row['order_boy'],
			'order_assign' => $row['order_assign'],


			'order_status' => $row['order_status'],

		];

	  endforeach;

	  $rdata['status'] = 'ok';

	  $rdata['orders'] = $res_dataa;

	  $rdata['message'] = 'seller orders';

	}else{

	  $rdata['status'] = 'error';

	  $rdata['orders'] = [];

	  $rdata['message'] = 'no seller orders';

	}

	  $rdata['type'] = 'cancelled';


	  echo json_encode($rdata);

  }



  

  elseif($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "detail"){

	$order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');

	$order = $this->Api_modela->getOrdersD($order_id);

	$orderItems = $this->Api_modela->getOrdersI($order_id, $order['order_status']);

	if(!empty($order)){

		$tItems = $this->Api_modela->count_rows('ordered', 'ordered_order', $order['order_id']);



	  $od_data = [

		'order_id' => 'ELOCRT'.$order['order_id'],

		'user_id' => $order['user_id'],

		'user_name' => $order['user_name'],

		'user_phone' => $order['user_phone_code'].$order['user_phone'],
		'user_email' => $order['user_email'],
		'user_device' => $order['user_device'],


		'user_image' => $order['user_image'],
		'seller_id' => $order['seller_id'],
		'seller_store_name' => $order['seller_store_name'],
		'seller_store_image' => $order['seller_store_image'],
		'seller_store_address' => $order['seller_store_address'],
		'seller_state' => $order['seller_state'],
		'seller_pincode' => $order['seller_pincode'],
		'seller_city' => $order['seller_city'],
		'seller_phone_code' => $order['seller_phone_code'],
		'seller_phone' => $order['seller_phone'],


		'order_date' => $order['order_date'],
		'order_time' => $order['order_time'],

		'order_pick_date' => $order['order_pick_date'],

		'order_pick_time' => $order['order_pick_time'],

		'order_total_items' => $tItems,

		'order_total' => $order['order_total'],
		
			'order_tax' => $order['order_tax'],
			'order_delivery' => $order['order_delivery'],
			'order_coupon' => $order['order_coupon'],
			'order_final' => $order['order_final'],
		

			'order_type' => $order['order_type'],
			'order_pay_type' => $order['order_pay_type'],
			'order_payref' => $order['order_payref'],
			'order_boy' => $order['order_boy'],
			'order_assign' => $order['order_assign'],


		'order_status' => $order['order_status'],

		];

		$rdata['status'] = 'ok';
		$rdata['order'] = $od_data;
		$rdata['products'] = $orderItems['item'];

		if($order['order_type'] === "1"){
			$dvlAddr = $this->Api_modela->getuserAddress($order['order_user_id'], $order['order_address']);
			$rdata['address'] =   array('addr_id' => $dvlAddr['addr_id'], 'addr_address' => $dvlAddr['addr_address'], 'addr_city' => $dvlAddr['addr_city'], 'addr_pincode' => $dvlAddr['addr_pincode'], 'addr_name' => $dvlAddr['addr_name'], 'addr_phone' => $dvlAddr['addr_phone'], 'addr_lat' => $dvlAddr['addr_lat'], 'addr_long' => $dvlAddr['addr_long']);
		}else{
			$rdata['address'] = [];
		}


		$rdata['message'] = 'seller order';

	  }else{

	  $rdata['status'] = 'error';
	  $rdata['order'] = '';
	  $rdata['products'] = [];
	  $rdata['message'] = 'no seller order';
	}

	  $rdata['type'] = 'detail';

	  $rdata['order_id'] = $order_id;

	  echo json_encode($rdata);

  }





  elseif($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "update"){

	$order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');

	$order_status = trim($_POST['order_status']);

	if($order_status === '3'){


		if(1 === 1){
			$data['order_status'] = $order_status;

		}else{

			$res_data['status'] = 'error';

			$res_data['type'] = 'update status';

			$res_data['message'] = 'order not matched';

			$res_data['order_id'] = 'ELOCRT'.$order_id;
			echo json_encode($res_data);

			exit;

		}

	}else{$data['order_status'] = $order_status;}

	$updated = $this->Api_modela->update($data, 'orders', array('order_id' => $order_id));

	if($updated){

		$this->load->model('Notify_model');
		if($order_status == '2'){ $this->Notify_model->user($order_id, "confirm"); }
		else if($order_status == '3'){ $this->Notify_model->user($order_id, "delivered"); }
		else if($order_status == '4'){ $this->Notify_model->user($order_id, "cancelled"); }


		$res_data['status'] = 'ok';

		$res_data['type'] = 'update status';

		$res_data['message'] = 'order updated';
		$res_data['order_id'] = 'ELOCRT'.$order_id;
		echo json_encode($res_data);

	}else{

		$this->error();

	}

  }

  else{$this->error();}
  }



  function get_reviews(){

  if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "product"){

		$seller_id = trim($_POST['seller_id']);

		$product_id = trim($_POST['product_id']);

		$rews =  $this->Api_model->getReviewsP($seller_id, $product_id);

		if(!empty($rews)){

			foreach ($rews as $row):

			$res_dataa[] = [

			'review_id' => $row['reviewp_id'],

			'review_rate' => $row['reviewp_rate'],

			'user_name' => $row['user_name'],

			'user_image' => $row['user_image'],

			'review_message' => $row['reviewp_message'],

			];

			endforeach;

			$res_data['status'] = 'ok';

			$res_data['reviews'] = $res_dataa;

		}else{

			$res_data['status'] = 'error';

			$res_data['reviews'] = [];

		}

		$res_data['type'] = 'product';

		$res_data['message'] = 'reviews list';

		echo json_encode($res_data);exit;

	}

	elseif($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "seller"){

		$seller_id = trim($_POST['seller_id']);

		$rews =  $this->Api_model->getReviewsS($seller_id);

		if(!empty($rews)){

			foreach ($rews as $row):

			if($row['user_image'] != '0'){ $uImage = $row['user_image']; }

			else{ $uImage = '0'; }	

			$res_dataa[] = [

			'review_id' => $row['reviews_id'],

			'review_rate' => $row['reviews_rate'],

			'user_name' => $row['user_name'],

			'user_image' => $uImage,

			'review_message' => $row['reviews_message'],

			];

			endforeach;

			$res_data['status'] = 'ok';

			$res_data['reviews'] = $res_dataa;

		}else{ 

			$res_data['status'] = 'error';

			 $res_data['reviews'] = [];

		}

		$res_data['type'] = 'seller';

		$res_data['message'] = 'reviews list';

		echo json_encode($res_data);

	}

	else{$this->error();}

}





function notify(){

	if($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "order"){

	$data = array(

		'notify_from' => trim($_POST['seller_id']),

		'notify_to' => trim($_POST['user_id']),

		'notify_for' => ltrim(trim($_POST['order_id']), 'ELOCRT'),

		'notify_title' => 'Intimation',

		'notify_message' => trim($_POST['notify_message']),

		'notify_type' => '1',

		'notify_status' => '1',

	);

	$added=$this->Api_model->add_new($data, 'notifys');

	if($added){

		$res_data['status'] = 'ok';

		$res_data['type'] = 'order';

		$res_data['message'] = 'notify user for order';

		echo json_encode($res_data);

	}else{

		$this->error();

	}

}



	elseif($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "getorder"){



		$notify_from = trim($_POST['seller_id']);

		$notify_for = ltrim(trim($_POST['order_id']), 'ELOCRT');

		$notes=$this->Api_model->getODNotifys($notify_from, $notify_for);

		if(!empty($notes)){

			foreach ($notes as $row):
			$res_dataa[] = [
			'notify_id' => $row['notify_id'],
			'notify_message' => $row['notify_message'],
			];
			endforeach;
			$res_data['status'] = 'ok';
			$res_data['notifys'] = $res_dataa;
		}else{ $res_data['status'] = 'error'; $res_data['notifys'] = [];

		}

		$res_data['type'] = 'getorder';
		$res_data['message'] = 'order notifys list sent';
		echo json_encode($res_data);
	}
	else{$this->error();}
}


function get_boys(){
	$seller_id = trim($_POST['seller_id']);
	$store=$this->Api_model->get_row('sellers', array('seller_id' => $seller_id));
	$boys = $this->Api_model->getBoys($store['seller_lat'], $store['seller_long'], 100);
	if(!empty($boys)){
		foreach ($boys as $row):
		$res_dataa[] = [
		'boy_id' => $row['boy_id'],
		'boy_name' => $row['boy_name'],
		'boy_phone_code' => $row['boy_phone_code'],
		'boy_phone' => $row['boy_phone'],
		'boy_lat' => $row['boy_lat'],
		'boy_long' => $row['boy_long'],
		'distance' => round($row['distance'])
		];
		endforeach;
		$res_data['status'] = 'ok';
		$res_data['boys'] = $res_dataa;
	}else{ $res_data['status'] = 'error'; $res_data['boys'] = []; }
		$res_data['type'] = 'boys';
		$res_data['message'] = 'get boys within 10 kms';
		echo json_encode($res_data);
}


function assign_order(){
	$order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
	$data['order_assign'] = '1';
	$updated = $this->Api_modela->update($data, 'orders', array('order_id' => $order_id));
	if($updated===1){
		$res_data['status'] = 'ok';
		$res_data['message'] = 'open to assign';
		echo json_encode($res_data);
	}else{$this->error();}
}



function get_b2b_categories(){
	$catg = $this->Api_model->getB2Categories();
	if(!empty($catg)){
		  foreach ($catg as $row):
			$res_dataa[] = [
			'b2category_id' => $row['b2category_id'],
			'b2category_name' => $row['b2category_name'],
			'b2category_image' => $row['b2category_image']
			];  
		  endforeach;
		  $rdata['b2categories'] = $res_dataa;
		  $rdata['status'] = 'ok';
		}else{
		  $rdata['b2categories'] = [];
		  $rdata['status'] = 'error';
		}
		  $rdata['type'] = 'b2categories';
		  echo json_encode($rdata);
	}


	function get_b2b_subcategories(){
		$cat = trim($_POST['b2category_id']);
		$scatg = $this->Api_model->getB2SubCategories($cat);
		if(!empty($scatg)){
			  foreach ($scatg as $row):
				$res_dataa[] = [
				'b2subcategory_id' => $row['b2subcategory_id'],
				'b2subcategory_name' => $row['b2subcategory_name'],
				'b2subcategory_image' => $row['b2subcategory_image']
				];  
			  endforeach;
			  $rdata['b2subcategories'] = $res_dataa;
			  $rdata['status'] = 'ok';
		
			}else{
			  $rdata['b2subcategories'] = [];
			  $rdata['status'] = 'error';
			}
			  $rdata['type'] = 'b2subcategories';
			  $rdata['b2subcategory_id'] = $cat;
			  echo json_encode($rdata);
		}
	
		function get_b2b_orders(){
			$type = trim($_POST['type']);
			$id = trim($_POST['b2seller_id']);
			$orders = $this->Api_model->getB2Orders($id, $type);
			if(!empty($orders)){
				foreach ($orders as $row):
				$orderItems = $this->Api_model->getOrdersIB2B($row['b2order_id'], $id, $row['b2product_id'], $row['b2sproduct_id']);
				  $res_dataa[] = [
   				'b2order_id' => $row['b2order_id'],
				'user_id' => $row['user_id'],
				'user_name' => $row['user_name'],
				'user_image' => $row['user_image'],
   				'b2order_phone' => $row['b2order_phone'],
   				'b2order_pincode' => $row['b2order_pincode'],
				   'b2order_address' => $row['b2order_address'],
				   'b2order_landmark' => $row['b2order_landmark'],
				   'b2order_city' => $row['b2order_city'],
				  'b2order_date' => $row['b2order_date'],
				  'b2order_date' => $row['b2order_date'],
				  'b2order_time' => $row['b2order_time'],
				  'b2order_time' => $row['b2order_time'],
				  'b2order_status' => $row['b2order_status'],
				  'ordered' => $orderItems['item']
				  ];  
				endforeach;
				$rdata['orders'] = $res_dataa;
				$rdata['status'] = 'ok';
		  
			  }else{
				$rdata['orders'] = [];
				$rdata['status'] = 'error';
			  }
				$rdata['type'] = 'B2B Orders';
				$rdata['b2seller_id'] = $id;
				echo json_encode($rdata);

		}




		function update_b2b_order(){
			$b2order_status = trim($_POST['b2order_status']);
			$id = trim($_POST['b2seller_id']);
			$oid = trim($_POST['b2order_id']);
			$data = array('b2order_status' => $b2order_status);
			$updated=$this->Api_model->update($data, 'b2orders', array('b2seller_id' => $id, 'b2order_id' => $oid));
			$rdata['status'] = 'ok';
			$rdata['type'] = 'B2B Order Update';
			$rdata['b2seller_id'] = $id;
			$rdata['b2order_id'] = $oid;
			echo json_encode($rdata);
		}
















}
