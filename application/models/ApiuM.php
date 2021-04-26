<?php

require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;

class ApiuM extends CI_Model
{

    public function __construct() { parent::__construct(); $this->load->database(); }
    function checkUserExists($phone, $phone_code){
      $query = $this->db->get_where('users', array('user_phone' => $phone, 'user_phone_code' => $phone_code));
      if( $query->num_rows() > 0 ){ return $query->row_array(); } else{ return FALSE; }
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
    function delete($table, $where){
      $this->db->where($where); $this->db->delete($table);
      return $this->db->affected_rows();
    }
    function get_row($table, $where)
    {
      $query = $this->db->get_where($table, $where);
      $result = $query->row_array();
      return $result;
    }
    function get_field($field, $table, $where){
      $query = $this->db->get_where($table, $where);
      $result = $query->row_array()[$field];
      return $result;
    }
    public function count_rows($table, $where, $var){
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
    
    

    function countCart($user_id){
      $query = $this->db->get_where('cart', array('cart_user_id' => $user_id, 'cart_status' => '1'));
      return $query;
    }
    function cartcheck($user_id, $seller_id, $product_id){
    $query = $this->db->get_where('cart', array('cart_user_id' => $user_id, 'cart_seller_id' => $seller_id, 'cart_product_id' => $product_id, 'cart_status' => '1'))->num_rows();
    return $query;
    }
    
    function getCart($user_id){
      $this->db->join('products', 'products.product_id = cart.cart_product_id');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->join('sellers', 'sellers.seller_id = cart.cart_seller_id');
      $this->db->where('cart_user_id', $user_id);
      $this->db->where('cart_status', '1');
      $query = $this->db->get('cart')->result_array();

      $total = 0;
      $cshod = "1";
      if(!empty($query)){
        foreach ($query as $row):
if($row['cart_sproduct_id'] > 0){
  $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
  $this->db->where('sproduct_id', $row['cart_sproduct_id']);
  $this->db->where('sproduct_status', '1');
  $query1 = $this->db->get('productssub')->row_array();
  if($query1['vproduct_img1'] != '0'){ $image = $query1['vproduct_img1']; }else{ $image = ''; }

  $name = $row['product_name'].'-'.$query1['vproduct_p1'].'-'.$query1['sproduct_p2'];
  $sale = $query1['sproduct_sale'];
  $price = $query1['sproduct_sale'] * $row['cart_product_qty'];

}else{
  if($row['product_img1'] != '0'){ $image = $row['product_img1']; }else{ $image = ''; }
  $name = $row['product_name'];
  $sale = $row['product_sale'];
  $price = $row['product_sale'] * $row['cart_product_qty'];

}

          $cart[] = [
            'cart_id' => $row['cart_id'],
            'seller_id' => $row['cart_seller_id'],
            'product_id' => $row['product_id'],
            'sproduct_id' => $row['cart_sproduct_id'],
            'product_name' => $name,
            'product_measure' => $row['measure_name'],
            'product_sale' => $sale,
            'product_img1' => $image,
            'cart_qty' => $row['cart_product_qty'],
            'cart_price' => $price,
          ];
          $total = $total + $price;

          if($row['category_id'] === '4'){ $cshod = "0"; }
        endforeach;
        $rdata['cart'] = $cart;
        $rdata['cart_cod'] = $cshod;
        $rdata['cart_seller_id'] = $row['product_store'];
        $rdata['seller_store_name'] = $row['seller_store_name'];
        $rdata['seller_phone'] = $row['seller_phone_code'].$row['seller_phone'];
        $rdata['seller_store_image'] = $row['seller_store_image'];
        $rdata['seller_store_address'] = $row['seller_store_address'];
        $rdata['seller_city'] = $row['seller_city'];
        $rdata['seller_pincode'] = $row['seller_pincode'];
        $rdata['seller_lat'] = $row['seller_lat'];
        $rdata['seller_long'] = $row['seller_long'];
        $rdata['seller_time'] = $row['seller_time'];
        $rdata['seller_minimum_order'] = $row['seller_minimum_order'];
      }else{
        $rdata['cart'] = [];
        $rdata['cart_cod'] = "0";
        $rdata['cart_seller_id'] = '';
        $rdata['seller_store_name'] = '';
        $rdata['seller_phone'] = '';
        $rdata['seller_store_image'] = '';
        $rdata['seller_store_address'] = '';
        $rdata['seller_city'] = '';
        $rdata['seller_pincode'] = '';
        $rdata['seller_lat'] = '';
        $rdata['seller_long'] = '';
        $rdata['seller_time'] = '';
        $rdata['seller_minimum_order'] = '';
      }
      $rdata['total'] = $total;
      return $rdata;
    }


    public function inCart($user_id, $product_id){
      $this->db->where('cart_user_id', $user_id);
      $this->db->where('cart_product_id', $product_id);
      $this->db->where('cart_status', '1');
      $query = $this->db->get('cart')->num_rows();
      if($query > 0){  return '1'; }else{ return '0'; }
  }


  
  public function cWish($user_id, $seller_id){
    $this->db->where('wish_user', $user_id);
    $this->db->where('wish_main', $seller_id);
    $this->db->where('wish_status', '1');
    $query = $this->db->get('wishlist')->num_rows();
    if($query > 0){  return '1'; }else{ return '0'; }
}


public function cWishb2b($user_id, $seller_id){
  $this->db->where('wish_user', $user_id);
  $this->db->where('wish_main', $seller_id);
  $this->db->where('wish_status', '1');
  $query = $this->db->get('wishlistb2b')->num_rows();
  if($query > 0){  return '1'; }else{ return '0'; }
}

public function gWishes($user_id, $lat, $long){
  $this->db->select("*, ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 AS `distance`");
  $this->db->join('sellers', 'sellers.seller_id = wishlist.wish_main');
  $this->db->where('wish_user', $user_id);
  $this->db->where('wish_status', '1');
  $query = $this->db->get('wishlist')->result_array();
  return $query;
}




public function gb2bWishes($user_id, $b2b){
  $this->db->where('wish_user', $user_id);
  $this->db->where('wish_main', $b2b);
  $this->db->where('wish_status', '1');
  $this->db->limit(1);
  $query = $this->db->get('wishlistb2b')->num_rows();
  if($query > 0){
    return "1";
  }else{
    return "2";
  }
}

public function gb2ballWishes($user_id){
  $this->db->join('b2b', 'b2b.b2b_id = wishlistb2b.wish_main');
  $this->db->where('wish_user', $user_id);
  $this->db->where('wish_status', '1');
  $query = $this->db->get('wishlistb2b')->result_array();
  return $query;
}




    public function orderAdd($data = array()){
      $insert = $this->db->insert_batch('ordered',$data);
      return $insert?true:false;
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

     function getBanners($type){
      $this->db->where('banner_type', $type);
      $this->db->where('banner_status', '1'); 
      $query = $this->db->get('banners');
      return  $query->result_array();
    }


    function getStores($lat, $long, $kms){
      $this->db->select("*, ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 AS `distance`");
      $this->db->where("ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 < $kms");
      $this->db->where('seller_status', 'active'); 
      $this->db->where('seller_verified', '1'); 
      $query = $this->db->get('sellers');
      return  $query->result_array();
    }




    public function getPDSearch($trm, $lat, $long, $kms){
      $this->db->select('product_id, product_name, seller_id, seller_store_name, category_id');
      $this->db->join('sellers', 'sellers.seller_id = products.product_store');
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->where("ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 < $kms");
      $this->db->where('seller_astatus', '1');
      $this->db->where('seller_status', 'active');
      $this->db->where('product_status', '1');
      $this->db->where('product_a_status', '1');
      $this->db->like('product_name', $trm);
      $this->db->limit(10);
      $query = $this->db->get('products')->result_array();
      return $query;
    }
    
    


    function ckStoreProd($id){
      $this->db->where('product_store', $id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_status', '1');
      $this->db->limit(1); 
      $query = $this->db->get('products')->num_rows();
      if($query > 0){ return true; }else{ return false; }
    }



function ckStoreProdCat($id, $cid){
      $this->db->where('product_store', $id); 
      $this->db->where('product_category', $cid);
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_status', '1');
      $this->db->limit(1); 
      $query = $this->db->get('products')->num_rows();
      if($query > 0){ return true; }else{ return false; }
    }



    function getCategories($id){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->group_by('product_category');
      $this->db->where('product_store', $id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }


    function getCategoriesStore($id, $cat){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->group_by('product_category');
      $this->db->where('product_store', $id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_status', '1');
      $query = $this->db->get('products');
      return  $query->result_array();
    }




function getHmeCategories($lat, $long, $kms){
      $this->db->select("category_id, category_name, category_image, category_image2");
      $this->db->where('category_status', '1'); 
      $query = $this->db->get('categories');
      return  $query->result_array();
    }

    function ckStoreProd2($lat, $long, $kms, $cat){
      $this->db->select("seller_id");
      $this->db->join('products', 'products.product_store = sellers.seller_id');
      $this->db->join('productssub', 'productssub.sproduct_main = products.product_id', 'left');
      $this->db->where("ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 < $kms");
      $this->db->where('product_category', $cat); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_substatus', '1'); 
      $this->db->where('product_status', '1');
      $this->db->where('seller_status', 'active');
      $this->db->where('seller_astatus', '1');
      $this->db->limit(1); 
      $query = $this->db->get('sellers');
      $query1 = $query->num_rows();
      if($query1 > 0){ return true; }else{ return false; }
    }
    function getSubCategories($id, $category_id){
      $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
      $this->db->group_by('product_subcategory');
      $this->db->where('product_store', $id); 
      $this->db->where('product_category', $category_id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_substatus', '1'); 
      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    function getProducts($id, $category_id, $subcategory_id){
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->where('product_store', $id); 
      $this->db->where('product_category', $category_id); 
      $this->db->where('product_subcategory', $subcategory_id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_substatus', '1'); 
      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    function getProduct($id, $product_id){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->where('product_store', $id); 
      $this->db->where('product_id', $product_id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->row_array();
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
    function getMyProducts($id, $cat_id){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
      $this->db->where('product_store', $id);
      $this->db->where('product_category', $cat_id); 
      $this->db->where('product_a_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    function getMyProductDetail($id, $product_id){
      $this->db->join('categories', 'categories.category_id = products.product_category');
      $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
      $this->db->where('product_store', $id);
      $this->db->where('product_id', $product_id); 
      $this->db->where('product_a_status', '1'); 
      $query = $this->db->get('products');
      return  $query->row_array();
    }

    function getMyProfile($id){
      $this->db->where('user_id', $id); 
      $this->db->where('user_status', '1'); 
      $query = $this->db->get('users');
      return  $query->row_array();
    }

    function getOrdersP($user_id){
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc'); 
      $this->db->where('order_user_id', $user_id);
      $this->db->where('order_status >', '0'); 
      $this->db->where('order_status <', '3'); 
      $query = $this->db->get('orders');
      return  $query->result_array();
    }
    function getOrdersC($user_id){
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc'); 
      $this->db->where('order_user_id', $user_id);
      $this->db->where('order_status >', '2'); 
      $this->db->where('order_status <', '4'); 
      $query = $this->db->get('orders');
      return  $query->result_array();
    }

    function getOrdersCan($user_id){
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->order_by('order_date', 'desc');
      $this->db->order_by('order_time', 'desc'); 
      $this->db->where('order_user_id', $user_id);
      $this->db->where('order_status', '4'); 
      $query = $this->db->get('orders');
      return  $query->result_array();
    }

    function getOrdersD($user_id, $order_id){
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_id', $order_id);
      $this->db->where('order_user_id', $user_id);
      $this->db->where('order_status >', '0'); 
      $query = $this->db->get('orders');
      return  $query->row_array();
    }
    function getOrdersI($order_id, $order_status, $user_id){
      $this->db->join('products', 'products.product_id = ordered.ordered_product');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->where('ordered_order', $order_id); 
      $this->db->where('ordered_status >', '0'); 
      $query = $this->db->get('ordered')->result_array();
      $total = 0;
      if(!empty($query)){
        foreach ($query as $row):
          if($row['ordered_sproduct'] === '0'){
           if($row['product_img1'] != '0'){ $image = $row['product_img1']; }else{ $image = '0';}
           $prodname = $row['product_name'];

          } 
          if($row['ordered_sproduct'] > '0'){
            $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
            $this->db->where('sproduct_id', $row['ordered_sproduct']); 
            $query2 = $this->db->get('productssub')->row_array();
            if($query2['vproduct_img1'] != '0'){ $image = $query2['vproduct_img1']; }else{ $image = '0';}
            $prodname = $row['product_name'].'-'.$query2['vproduct_p1'].'-'.$query2['sproduct_p2'];
          }

          if($order_status == '3'){ $p_review = $this->getProdRew($user_id, $row['product_id'], $order_id);
          }else{ $p_review = '0'; }

          $item[] = [
            'ordered_id' => $row['ordered_id'],
            'product_id' => $row['product_id'],
            'product_name' => $prodname,
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






    function getTrack($user_id, $order_id){
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->join('addresses', 'addresses.addr_id = orders.order_address');
      $this->db->where('order_id', $order_id);
      $this->db->where('order_user_id', $user_id);
      $query = $this->db->get('orders');
      return  $query->row_array();
    }



    function getStoreRew($user_id, $seller_id, $order_id){
      $this->db->where('reviews_user_id', $user_id);
      $this->db->where('reviews_user_id', $user_id);
      $this->db->where('reviews_order_id', $order_id);
      $this->db->where('reviews_status', '1'); 
      $query = $this->db->get('reviewstore')->num_rows();
      if($query > 0){ return '1'; }else{ return '0'; }
    }

    function getProdRew($user_id, $prod_id, $order_id){
      $this->db->where('reviewp_user_id', $user_id);
      $this->db->where('reviewp_product_id', $prod_id);
      $this->db->where('reviewp_order_id', $order_id);
      $this->db->where('reviewp_status', '1'); 
      $query = $this->db->get('reviewproduct')->num_rows();
      if($query > 0){ return '1'; }else{ return '0'; }
    }

    function getProdRews($prod_id){
      $this->db->join('users', 'users.user_id = reviewproduct.reviewp_user_id');
      $this->db->where('reviewp_product_id', $prod_id);
      $this->db->where('reviewp_status', '1');
      $this->db->order_by('reviewp_id', 'DESC'); 
      $query = $this->db->get('reviewproduct')->result_array();
      if(!empty($query)){
        foreach ($query as $row):          
          $rew[] = [
            'review_id' => $row['reviewp_id'],
            'user_id' => $row['reviewp_user_id'],
            'user_name' => $row['user_name'],
            'user_image' => $row['user_image'],
            'review_rate' => $row['reviewp_rate'],
            'review_message' => $row['reviewp_message'],
          ];
        endforeach;
        $rdata['reviews'] = $rew;
      } else{ $rdata['reviews'] = [];}
        return  $rdata;
    }
    function getMyNotifys($userid){
      $this->db->where('notify_to', $userid);
      $this->db->where('notify_type', '1');
      $this->db->where('notify_status', '1'); 
      $query = $this->db->get('notifys');
      return  $query->result_array();
    }

    function getProdSub1($prod_id){
      $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
      $this->db->where('sproduct_main', $prod_id);
      $this->db->where('sproduct_status', '1'); 
      $this->db->limit(1); 
      $query = $this->db->get('productssub');
      return  $query->row_array();
    }

    function getProdVars($prod_id){
      $this->db->where('vproduct_main', $prod_id);
      $this->db->where('vproduct_status', '1');
      $query = $this->db->get('productsvar');
      return  $query->result_array();
    }


    function getProdSubs($prod_id){
     $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
      $this->db->where('sproduct_main', $prod_id);
      $this->db->where('sproduct_status', '1'); 
      $query = $this->db->get('productssub');
      return  $query->result_array();
    }


    function getProdSub($prod, $vid){
      $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
       $this->db->where('sproduct_main', $prod);
       $this->db->where('sproduct_var', $vid);
       $this->db->where('sproduct_status', '1'); 
       $query = $this->db->get('productssub');
       return  $query->result_array();
     }

    function getUserAddress($user_id){
      $this->db->where('addr_user', $user_id);
      $this->db->where('addr_status', '1'); 
      $this->db->order_by('addr_id', 'desc'); 
      $query = $this->db->get('addresses');
      return  $query->result_array();
    }

    function gStatement($user_id){
      $this->db->where('usrt_main', $user_id);
      $this->db->where('usrt_status', '1'); 
      $query = $this->db->get('user_transac');
      return  $query->result_array();
    }


    function gCoups($user_lat, $user_long, $date){

   $this->db->where("ACOS( SIN( RADIANS( `coup_lat` ) ) * SIN( RADIANS( $user_lat ) ) + COS( RADIANS( `coup_lat` ) )* COS( RADIANS( $user_lat )) * COS( RADIANS( `coup_long` ) - RADIANS( $user_long )) ) * 6380 < 15");
   $this->db->where('coup_status', '1');
   $this->db->where('coup_start <=', $date);
   $this->db->where('coup_count >', '2');
  $this->db->where('coup_end >=', $date);
   $query = $this->db->get('coupons');
      return  $query->result_array();
      
    }



    
    function getB2Categories(){
      $this->db->where('b2category_status', '1'); 
      $query = $this->db->get('b2categories');
      return  $query->result_array();
    }


    function checkB2Catg($cid){
      $this->db->join('b2b', 'b2b.b2b_id = products.product_store');
      $this->db->join('b2categories', 'b2categories.b2category_id = products.product_category');
      $this->db->where('product_category', $cid);
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_b2', '1'); 
      $this->db->where('product_status', '1');
      $this->db->where('b2b_status', '1'); 
      $this->db->where('b2b_astatus', '1'); 
      $this->db->where('b2b_verified', '1');  
      $this->db->limit(1); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    
    function getB2SubCategories($cid, $b2id){
      $this->db->join('b2subcategories', 'b2subcategories.b2subcategory_id = products.product_subcategory');
      $this->db->group_by('b2subcategory_id');
      $this->db->where('product_category', $cid);
      $this->db->where('product_store', $b2id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_b2', '1'); 
      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    function getb2Products($subcategory_id, $b2id){
      $this->db->join('b2b', 'b2b.b2b_id = products.product_store');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->where('product_subcategory', $subcategory_id);
      $this->db->where('product_store', $b2id); 
      $this->db->where('product_b2', '1');
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }
    function getb2Product($product_id){
      $this->db->join('b2b', 'b2b.b2b_id = products.product_store');
      $this->db->join('b2categories', 'b2categories.b2category_id = products.product_category');
      $this->db->join('b2subcategories', 'b2subcategories.b2subcategory_id = products.product_subcategory');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->where('product_id', $product_id);
      $this->db->where('product_b2', '1'); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->row_array();
    }




 function getb2OrdersList($user_id){
      $this->db->join('products', 'products.product_id = b2orders.b2product_id');
      $this->db->join('measures', 'measures.measure_id = products.product_measure');
      $this->db->join('b2b', 'b2b.b2b_id = b2orders.b2seller_id');
      $this->db->where('b2order_user_id', $user_id);
      $this->db->where('b2order_status', '1');
      $query = $this->db->get('b2orders')->result_array();

      $total = 0;
      if(!empty($query)){
        foreach ($query as $row):


if($row['b2sproduct_id'] > 0){
  $this->db->join('productsvar', 'productsvar.vproduct_id = productssub.sproduct_var');
  $this->db->where('sproduct_id', $row['b2sproduct_id']);
  $query1 = $this->db->get('productssub')->row_array();
  if($query1['vproduct_img1'] != '0'){ $image = $query1['vproduct_img1']; }else{ $image = ''; }

  $name = $row['product_name'].'-'.$query1['vproduct_p1'].'-'.$query1['sproduct_p2'];
  $sale = $query1['sproduct_sale'];
  $price = $query1['sproduct_sale'] * $row['b2product_qty'];

}else{
  if($row['product_img1'] != '0'){ $image = $row['product_img1']; }else{ $image = ''; }
  $name = $row['product_name'];
  $sale = $row['product_sale'];
  $price = $row['product_sale'] * $row['b2product_qty'];
}
          $cart[] = [
            'b2order_id' => $row['b2order_id'],
            'seller_id' => $row['b2seller_id'],
            'seller_store_name' => $row['b2b_store_name'],
            'product_id' => $row['product_id'],
            'sproduct_id' => $row['b2sproduct_id'],
            'product_name' => $name,
            'product_measure' => $row['measure_name'],
            'product_img1' => $image,
            'cart_qty' => $row['b2product_qty'],
          ];
          $total = $total + $price;
        endforeach;
        $rdata['cart'] = $cart;
      }else{
        $rdata['cart'] = [];

      }
      return $rdata;


    }



    function getInvoice($user_id, $order_id){
      $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
      $this->db->where('order_user_id', $user_id);
      $this->db->where('order_id', $order_id);
      $this->db->where('order_status >', '2'); 
      $query = $this->db->get('orders');
      return  $query->row_array();
    }


    function getB2Stores($cat_id){
      $this->db->join('b2b', 'b2b.b2b_id = products.product_store');
      $this->db->group_by('b2b_id');
      $this->db->where('product_category', $cat_id); 
      $this->db->where('product_a_status', '1'); 
      $this->db->where('product_b2', '1');
      $this->db->where('product_status', '1'); 
      $this->db->where('b2b_status', '1'); 
      $this->db->where('b2b_astatus', '1'); 
      $this->db->where('b2b_verified', '1'); 

      $this->db->where('product_status', '1'); 
      $query = $this->db->get('products');
      return  $query->result_array();
    }


    function ag_check($user_id){
      $this->db->where('user_id', $user_id); 
      $this->db->where('user_agent', '1'); 
      $this->db->where('user_status', '1'); 
      $query = $this->db->get('users')->num_rows();
      if($query > 0){
        return true;
      }else{
        return false;
      }
    }

   

  function getmysellers($id){
      $this->db->where('seller_referral', $id);
      $query = $this->db->get('sellers');
      return  $query->result_array();   
  }



}

