<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;
class Frnc_Model extends CI_Model
{
    public function addNew($data, $table)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }
    public function update($data, $table, $where)
    {
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
    }
    public function get_field($field, $table, $where){
        $query = $this->db->get_where($table, $where);
        $result = $query->row_array()[$field];
        return $result;
      }

      public function get_row($table, $where){
        $query = $this->db->get_where($table, $where);
        $result = $query->row_array();
        return $result;
      }

    public function count_rows($table, $where, $var){
        $query = $this->db->get_where($table, array($where => $var));
        return $query->num_rows();
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

   
    public function get_newsellers($seller_id = FALSE){

         $id = $this->session->userdata('uid');
         $pincodes = $this->get_field('frn_pincodes', 'franchises', array('frn_id ' => $id, 'frn_status' => '1'));
         $str_arr = explode (",", $pincodes);  
        if($seller_id === FALSE){
            $this->db->where_in('seller_pincode', $str_arr);
            $this->db->where('seller_verified', '1');
            $this->db->where('seller_frnc', '0');
            $this->db->where('seller_status', 'pending');
            $query = $this->db->get('sellers');
            return $query->result_array();
        }else{
            $query = $this->db->get_where('sellers', array('seller_id' => $seller_id));
            return $query->row_array();
        }
    }

    public function get_newboys($boy_id = FALSE){
        $id = $this->session->userdata('uid');
        $pincodes = $this->get_field('frn_pincodes', 'franchises', array('frn_id ' => $id, 'frn_status' => '1'));
        $str_arr = explode (",", $pincodes);  
        if($boy_id === FALSE){
           $this->db->where_in('boy_pincode', $str_arr);
           $this->db->where('boy_frnc', '0');
           $query = $this->db->get('boys');
           return $query->result_array();
        }else{
            $query = $this->db->get_where('boys', array('boy_id' => $boy_id));
            return $query->row_array();
        }
   }

   
    public function get_mysellers($seller_id = FALSE){
        $id = $this->session->userdata('uid');
       if($seller_id === FALSE){
           $this->db->where('seller_verified', '1');
           $this->db->where('seller_frnc', $id);
           $query = $this->db->get('sellers');
           return $query->result_array();
       }else{
           $query = $this->db->get_where('sellers', array('seller_id' => $seller_id));
           return $query->row_array();
       }
   }


   public function get_myboys($boy_id = FALSE){
    $id = $this->session->userdata('uid');
   if($boy_id === FALSE){
       $this->db->where('boy_frnc', $id);
       $query = $this->db->get('boys');
       return $query->result_array();
   }else{
       $query = $this->db->get_where('boys', array('boy_id' => $boy_id, 'boy_frnc' => $id));
       return $query->row_array();
   }
}





    public function get_mysupport($support_id = FALSE){
        $id = $this->session->userdata('uid');
       if($support_id === FALSE){
           $this->db->where('fs_frnc', $id);
           $query = $this->db->get('franchise_support');
           return $query->result_array();
       }else{
           $query = $this->db->get_where('franchise_support', array('fs_id' => $support_id, 'fs_frnc' => $id));
           return $query->row_array();
       }
    }




    public function get_myspsellers($seller_id = FALSE){
        $id = $this->session->userdata('uid');
        $fid = $this->session->userdata('fs_frnc');
       if($seller_id === FALSE){
            $this->db->where('seller_frnc', $fid);
           $this->db->where('seller_verified', '1');
           $this->db->where('seller_status', 'active');
           $query = $this->db->get('sellers');
           return $query->result_array();
       }else{
           $query = $this->db->get_where('sellers', array('seller_id' => $seller_id, 'seller_frnc' => $fid, 'seller_status' => 'active'));
           return $query->row_array();
       }
   }

   public function get_seller_products($seller_id){
    $this->db->join('measures', 'measures.measure_id = products.product_measure');
    $this->db->join('categories', 'categories.category_id = products.product_category');
    $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
    $this->db->where('product_store', $seller_id);
    $query = $this->db->get('products');
    return  $query->result_array();   
}

public function get_cats(){
    $this->db->where('category_status', '1');
    $query = $this->db->get('categories');
    return  $query->result_array();   
}
public function get_subcats(){
    $this->db->where('subcategory_status', '1');
    $query = $this->db->get('subcategories');
    return  $query->result_array();   
}
public function get_measures(){
    $this->db->where('measure_status', '1');
    $query = $this->db->get('measures');
    return  $query->result_array();   
}

public function get_product($product_id){
    $this->db->join('measures', 'measures.measure_id = products.product_measure');
    $this->db->join('categories', 'categories.category_id = products.product_category');
    $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
    $this->db->where('product_id', $product_id);
    $query = $this->db->get('products');
    return  $query->row_array();   
}
public function get_pvars($product_id){
    $this->db->where('vproduct_main', $product_id);
    $query = $this->db->get('productsvar');
    return  $query->result_array();   
}
public function get_var($var_id){
    $this->db->where('vproduct_id', $var_id);
    $query = $this->db->get('productsvar');
    return  $query->row_array();   
}
public function get_sub($sub_id){
    $this->db->where('sproduct_id', $sub_id);
    $query = $this->db->get('productssub');
    return  $query->row_array();   
}
public function get_psubs($product_id, $product_var){
    $this->db->where('sproduct_main', $product_id);
    $this->db->where('sproduct_var', $product_var);
    $query = $this->db->get('productssub');
    return  $query->result_array();   
}
public function get_subscriptions(){
    $this->db->where('subsc_status', '1');
    $query = $this->db->get('subscriptions');
    return  $query->result_array();   
}



public function get_orders($trk){
    $id = $this->session->userdata('uid');
    $pincodes = $this->get_field('frn_pincodes', 'franchises', array('frn_id ' => $id, 'frn_status' => '1'));
    $str_arr = explode (",", $pincodes); 
    $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
    $this->db->where_in('seller_pincode', $str_arr);
    $this->db->where('order_status', $trk);
    $query = $this->db->get('orders');
    $result2 = $query->result_array();
    return $result2;
}
public function get_order($oid){
    $id = $this->session->userdata('uid');
    $pincodes = $this->get_field('frn_pincodes', 'franchises', array('frn_id ' => $id, 'frn_status' => '1'));
    $str_arr = explode (",", $pincodes); 
    $this->db->join('users', 'users.user_id = orders.order_user_id');
    $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
    $this->db->where_in('seller_pincode', $str_arr);
    $this->db->where('order_id', $oid);
    $query = $this->db->get('orders');
    $result = $query->row_array();
    return $result;
}
public function get_order_cart($oid){
    $this->db->join('products', 'products.product_id = ordered.ordered_product');
    $this->db->where('ordered_order', $oid);
    $query = $this->db->get('ordered');
    $result = $query->result_array();
    return $result;
}



public function get_productspre(){
    $this->db->join('categories', 'categories.category_id = productspre.pd_category');
    $this->db->join('subcategories', 'subcategories.subcategory_id = productspre.pd_subcategory');
    $query = $this->db->get('productspre');
    return  $query->result_array();   
}

public function get_productpre($pd_id){
    $this->db->join('categories', 'categories.category_id = productspre.pd_category');
    $this->db->join('subcategories', 'subcategories.subcategory_id = productspre.pd_subcategory');
    $this->db->where('pd_id', $pd_id);
    $query = $this->db->get('productspre');
    return  $query->row_array();   
}


}

?>