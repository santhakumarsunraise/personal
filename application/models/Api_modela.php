<?php

require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;

class Api_modela extends CI_Model
{
    public function __construct()
    { parent::__construct(); $this->load->database(); }

    function checkPhoneExists($phone, $table, $column){
      $phonewho=$column.'_phone';
      $verifywho=$column.'_verified';
      $query = $this->db->get_where($table, array($phonewho => $phone, $verifywho => 1));
      if($query->num_rows()>0){ return TRUE; }
      else{ return FALSE; }
    }
    function add_new($data, $table)
    {
      $this->db->insert($table, $data);
      return $this->db->insert_id();
    }
    function update($data, $table, $where){
      $this->db->update($table, $data, $where);
      return $this->db->affected_rows();
    }
    function get_row($table, $where)
    {
      $query = $this->db->get_where($table, $where);
      return $query->row_array();
    }
    function get_field($field, $table, $where){
      $query = $this->db->get_where($table, $where);
      $result = $query->row_array()[$field];
      return $result;
    }
    function count_rows($table, $where, $var){
      $query = $this->db->get_where($table, array($where => $var));
      return $query->num_rows();
    }


    function get_splash($for){
      $this->db->where('spl_for', $for);
      $this->db->where('spl_status', '1');
      $this->db->order_by('spl_priority', 'asc'); 
      $query = $this->db->get('screens');
      return  $query->result_array();
    }


function checkCountry($lat, $long){
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent:curl'));
curl_setopt($ch, CURLOPT_URL, "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$long&zoom=18&addressdetails=1");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$cout = curl_exec($ch);
curl_close ($ch);
$cdat = json_decode($cout, true);
if(isset($cdat['address']['country_code'])) {  
$ccode = $cdat['address']['country_code'];
      $this->db->where('cnty_code', $ccode); 
      $this->db->where('cnty_status', '1');
      $query = $this->db->get('countries');
      return  $query->row_array();
}else {
    return [];
}

}




public function fileupload($img, $type, $unique){
  if(!is_dir("./uploads/$type/$unique")){mkdir("./uploads/".$type.'/'.$unique,0777);}
    $config['upload_path'] = "./uploads/$type/$unique";
    $config['allowed_types'] = 'gif|jpg|png|jpeg';
    $config['encrypt_name'] = TRUE;
    $config['max_size'] = '50000';
    $config['file_name'] = $_FILES[$img]['name'];
    $this->load->library('upload',$config);
    $this->upload->initialize($config);
    if($this->upload->do_upload($img)){
      $uploadData = $this->upload->data();
      $filename = $uploadData['file_name'];
      $file_path = "uploads/$type/$unique/$filename";
      $url = $this->awsupload($file_path, $filename);
      if($url != '0'){ return $url; }
      else{ return base_url().$file_path; }
    }else{return '0';}
}
function awsupload($file_path, $filename){
  $credentials = new Aws\Credentials\Credentials('AKIAZBRHXLJJ243I5R37', 'vLaeh3Znpyk99SxJYQKqYsuHbX6Qd1CWCHisqvNY');
  $s3 = new Aws\S3\S3Client([
      'version'     => 'latest',
      'region'      => 'ap-south-1',
      'credentials' => $credentials,
      'debug'   => false
  ]);
  $upload = $s3->upload('ellocart', $file_path, fopen($file_path, 'rb'), 'public-read');
  if($upload->get('@metadata')['statusCode'] === 200){ $url = htmlspecialchars($upload->get('ObjectURL')); }
  else{ $url = '0'; }
  return $url;
}



    function fileuploadbase64($img, $type, $unique){
      if(!is_dir("./uploads/$type/$unique")){mkdir("./uploads/".$type.'/'.$unique,0777);}
        $img = str_replace('data:image/png;base64,', '', $img);
      $img = str_replace(' ', '+', $img);
      $dataimg = base64_decode($img);
      $name = md5(uniqid(rand(), true));
      $filename= $name . '.' . 'png';
      $file = "./uploads/$type/$unique/$filename";
      $upload = file_put_contents($file, $dataimg);
      if($upload){
      return $file_path = "uploads/$type/$unique/$filename";
      }else{return '0';}
     }

     function loginSeller($phone_code, $phone)
     {
      $this->db->where('seller_phone_code', $phone_code);
      $this->db->where('seller_phone', $phone); 
      $this->db->where('seller_verified', '1');
      $this->db->where('seller_astatus', '1');
      $this->db->where('seller_status', 'active'); 
      $query = $this->db->get('sellers');
      return $query;
     }
     function loginB2B($phone_code, $phone)
     {
      $this->db->where('b2b_phone_code', $phone_code);
      $this->db->where('b2b_phone', $phone); 
      $this->db->where('b2b_verified', '1');
      $this->db->where('b2b_astatus', '1');
      $this->db->where('b2b_status', 'active'); 
      $query = $this->db->get('b2b');
      return $query;
     }

         
     function getSellerSubs(){
      $this->db->where('subsc_for', 'seller'); 
      $this->db->where('subsc_status', '1'); 
      $query = $this->db->get('subscriptions');
      return  $query->result_array();
    }

    function checkSellerSub($id){
      date_default_timezone_set('Asia/Kolkata');
      $dt = date('Y-m-d'); 
      $this->db->where('pay_payer', $id);
      $this->db->where('pay_status', '1');
      $this->db->where('pay_validity >=', $dt);
      $this->db->limit(1); 
      $query = $this->db->get('payments')->num_rows();
      if($query > 0){ return true; }else{ return false; }
    }


    function getMySubsSeller($id){
      $this->db->join('subscriptions', 'subscriptions.subsc_id = payments.pay_susbc');
      $this->db->where('pay_payer', $id); 
      $this->db->where('pay_status', '1');
      $this->db->order_by('pay_id', 'desc'); 
      $query = $this->db->get('payments');
      return  $query->result_array();
    }
    
     function getAllCategories(){
      $this->db->where('category_status', '1'); 
      $query = $this->db->get('categories');
      return  $query->result_array();
    }
    function getAllSubCategories($catg_id){
      $this->db->where('subcategory_main', $catg_id); 
      $this->db->where('subcategory_status', '1'); 
      $query = $this->db->get('subcategories');
      return  $query->result_array();
    }
    function getAllMeasures(){
      $this->db->where('measure_status', '1'); 
      $query = $this->db->get('measures');
      return  $query->result_array();
    }
    function getMyCategories($id){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->group_by('product_category');
      $this->db->where('product_store', $id); 
      $this->db->where('product_a_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    function getMySubCategories($id, $ctid){
      $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
      $this->db->group_by('product_subcategory');
      $this->db->where('product_store', $id);
      $this->db->where('product_category', $ctid);
      $this->db->where('product_a_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }


    function getMyProducts($id, $cat_id, $subcat_id, $b2b){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
      $this->db->where('product_store', $id);
      $this->db->where('product_b2', $b2b); 
      $this->db->where('product_category', $cat_id);
      if($subcat_id != ''){$this->db->where('product_subcategory', $subcat_id);}
      $this->db->where('product_a_status', '1');
      $this->db->order_by('product_id', 'desc');
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    function getMyProductDetail($id, $product_id){
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
      $this->db->where('product_store', $id);
      $this->db->where('product_id', $product_id); 
      $query = $this->db->get('products');
      return  $query->row_array();
    }
    function getMyVarProductDetail($id, $product_id){
      $this->db->where('vproduct_store', $id);
      $this->db->where('vproduct_main', $product_id);
      $query = $this->db->get('productsvar');
      return  $query->result_array();
    }
    function getMyProductDetailvar($id, $product_id, $vproduct_id){
      $this->db->where('vproduct_id', $vproduct_id);
      $this->db->where('vproduct_store', $id);
      $this->db->where('vproduct_main', $product_id);
      $query = $this->db->get('productsvar');
      return  $query->row_array();
    }
    function getMyProductDetailSub($id, $product_id, $vproduct_id){
      $this->db->where('sproduct_store', $id);
      $this->db->where('sproduct_main', $product_id);
      $this->db->where('sproduct_var', $vproduct_id);
      $this->db->order_by('sproduct_id', 'desc');
      $query = $this->db->get('productssub');
      return  $query->result_array();
    }
    
 function getMyProductOnlySub($id, $product_id, $sproduct_id){
      $this->db->where('sproduct_store', $id);
      $this->db->where('sproduct_main', $product_id);
      $this->db->where('sproduct_id', $sproduct_id);
      $query = $this->db->get('productssub');
      return  $query->row_array();
    }



    function getMyProfile($id, $phone){
      $this->db->where('seller_id', $id); 
      $this->db->where('seller_phone', $phone);
      $this->db->where('seller_verified', '1'); 
      $this->db->where('seller_status', 'active'); 
      $query = $this->db->get('sellers');
      return  $query->row_array();
    }

    function getBanners($type){
      $this->db->where('banner_type', $type);
      $this->db->where('banner_status', '1'); 
      $query = $this->db->get('banners');
      return  $query->result_array();
    }

    function getuserAddress($user_id, $addr_id){
      $this->db->where('addr_id', $addr_id); 
      $this->db->where('addr_user', $user_id);
      $query = $this->db->get('addresses');
      return  $query->row_array();
    }



    function getOrdersR(){
      $this->db->join('users', 'users.user_id = orders.order_user_id');
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_status >', '0'); 
      $this->db->where('order_status <', '2');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc'); 
      $this->db->order_by('order_id', 'desc'); 
      $query = $this->db->get('orders');
      return  $query->result_array();
    }
    function getOrdersP(){
      $this->db->join('users', 'users.user_id = orders.order_user_id');
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_status', '2');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc'); 
      $this->db->order_by('order_id', 'desc'); 
      $query = $this->db->get('orders');
      return  $query->result_array();
      
    }
  function getOrdersA(){
      $this->db->join('users', 'users.user_id = orders.order_user_id');
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_assign', '2');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc');  
      $this->db->order_by('order_id', 'desc'); 
      $query = $this->db->get('orders');
      return  $query->result_array();    
    }
    function getOrdersC(){
      $this->db->join('users', 'users.user_id = orders.order_user_id');
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_status', '3');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc'); 
      $this->db->order_by('order_id', 'desc'); 
      $query = $this->db->get('orders');
      return  $query->result_array();
    }
    function getOrdersCn(){
      $this->db->join('users', 'users.user_id = orders.order_user_id');
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_status', '4');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc'); 
      $this->db->order_by('order_id', 'desc'); 

      $query = $this->db->get('orders');
      return  $query->result_array();
    }
    function getOrdersD($order_id){
      $this->db->join('users', 'users.user_id = orders.order_user_id');
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_id', $order_id); 
      $this->db->where('order_status >', '0'); 
      $query = $this->db->get('orders');
      return  $query->row_array();
    }
    function getOrdersI($order_id, $order_status){
      $this->db->join('products', 'products.product_id = ordered.ordered_product');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->where('ordered_order', $order_id); 
      $this->db->where('ordered_status >', '0'); 
      $query = $this->db->get('ordered')->result_array();
      $total = 0;
      if(!empty($query)){
        foreach ($query as $row):
            
            
            if($row['ordered_sproduct'] > '0'){ 
                
    $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
    $this->db->where('sproduct_id', $row['ordered_sproduct']);
    $query2 = $this->db->get('productssub')->row_array();
                
    if($query2['vproduct_img1'] != '0'){ $image = $query2['vproduct_img1']; }else{ $image = '0'; }
    
$name = $row['product_name'].'-'.$query2['vproduct_p1'].'-'.$query2['sproduct_p2'];


         $p_review = '0'; 
    
                
    }else{
                
            
          if($row['product_img1'] != '0'){ $image = $row['product_img1']; }else{ $image = '0'; }
       $p_review = '0'; 
          $name = $row['product_name'];

          
            }
            
            
            


          $item[] = [
            'ordered_id' => $row['ordered_id'],
            'product_id' => $row['product_id'],
            'sproduct_id' => $row['ordered_sproduct'],

            'product_name' => $name,
            'product_measure' => $row['measure_name'],
            'product_img1' => $image,
            'product_review' => $p_review,
            'ordered_amount' => $row['ordered_amount'],
            'ordered_qty' => $row['ordered_qty'],
          ];
        endforeach;
        $rdata['item'] = $item;
      } else{ $rdata['item'] = "";}
        return  $rdata;
    }




    function getOrdersIB2B($order_id, $seller_id, $prod, $sprod){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->where('product_b2', '1'); 
      $row = $this->db->get('products')->row_array();
      if(!empty($row)){
            
            
            if($sprod > '0'){
    $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
    $this->db->where('sproduct_id', $sprod);
    $query2 = $this->db->get('productssub')->row_array();
    if($query2['vproduct_img1'] != '0'){ $image = $query2['vproduct_img1']; }else{ $image = '0'; }
    
$name = $row['product_name'].'-'.$query2['vproduct_p1'].'-'.$query2['sproduct_p2'];
                
    }else{
                
            
          if($row['product_img1'] != '0'){ $image = $row['product_img1']; }else{ $image = '0'; }
          $name = $row['product_name'];
            }
            
            
        
          $item[] = [
            'product_id' => $row['product_id'],
            'sproduct_id' => $sprod,
            'product_name' => $name,
            'product_measure' => $row['measure_name'],
            'product_img1' => $image,
          ];

        $rdata['item'] = $item;
      } else{ $rdata['item'] = "";}
        return  $rdata;
    }









  function getReviewsP($seller_id, $product_id){
      $this->db->join('users', 'users.user_id = reviewproduct.reviewp_user_id');
      $this->db->where('reviewp_product_id', $product_id);
      $this->db->where('reviewp_status', '1');
      $this->db->order_by('reviewp_id', 'desc'); 
      $query = $this->db->get('reviewproduct');
      return  $query->result_array();
  }
  function getReviewsS($seller_id){
      $this->db->join('users', 'users.user_id = reviewstore.reviews_user_id');
      $this->db->where('reviews_seller_id', $seller_id);
      $this->db->where('reviews_status', '1');
      $this->db->order_by('reviews_id', 'desc'); 
      $query = $this->db->get('reviewstore');
      return  $query->result_array();
  }
  function getODNotifys($notify_from, $notify_for){
      $this->db->where('notify_from', $notify_from);
      $this->db->where('notify_for', $notify_for);
      $this->db->where('notify_type', '1');
      $this->db->where('notify_status', '1');
      $this->db->order_by('notify_id', 'desc'); 
      $query = $this->db->get('notifys');
      return  $query->result_array();
  }

	function getBoys($lat, $long, $kms){
		$this->db->select("*, ACOS( SIN( RADIANS( `boy_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `boy_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `boy_long` ) - RADIANS( $long )) ) * 6380 AS `distance`");
		$this->db->where("ACOS( SIN( RADIANS( `boy_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `boy_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `boy_long` ) - RADIANS( $long )) ) * 6380 < $kms");
    $this->db->where('boy_astatus', '1');
    $this->db->where('boy_status', '1');
		$query = $this->db->get('boys');
		return  $query->result_array();
	  }



    function getB2Categories(){
      $this->db->where('b2category_status', '1'); 
      $query = $this->db->get('b2categories');
      return  $query->result_array();
    }
    function getB2SubCategories($cid){
      $this->db->where('b2subcategory_main', $cid); 
      $this->db->where('b2subcategory_status', '1'); 
      $query = $this->db->get('b2subcategories');
      return  $query->result_array();
    }
    function getB2Orders($id, $st){
      $this->db->join('users', 'users.user_id = b2orders.b2order_user_id');
      $this->db->where('b2seller_id', $id); 
      $this->db->where('b2order_status', $st); 
      $query = $this->db->get('b2orders');
      return  $query->result_array();
    }



}