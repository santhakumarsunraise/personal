<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;
class Admin_model extends CI_Model
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

      public function checkSellerPhn($phn){
        $query = $this->db->get_where('sellers', array('seller_phone'=>$phn));
        $result = $query->num_rows();
        if($result > 0){ return false; }
        else{ return true; }
      }
      
      public function checkB2BPhn($phn){
        $query = $this->db->get_where('b2b', array('b2b_phone'=>$phn));
        $result = $query->num_rows();
        if($result > 0){ return false; }
        else{ return true; }
      }


      function get_row($table, $where)
      {
        $query = $this->db->get_where($table, $where);
        return $query->row_array();
      }

    public function count_rows($table, $where, $var){
        $query = $this->db->get_where($table, array($where => $var));
        return $query->num_rows();
    }

    public function count($table, $where){
        return $this->db->get_where($table, $where)->num_rows();
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



    public function get_states($stateID=FALSE)
    {
        if($stateID===FALSE){
            $query = $this->db->get('states');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('states', array('stateID' => $stateID));
        return $query->row_array();
        }
    }
    public function get_cities($stateID, $cityID=FALSE)
    {
        if($cityID===FALSE){
            $query = $this->db->get_where('cities', array('cityState' => $stateID));
            return $query->result_array();
        }else{
        $query = $this->db->get_where('cities', array('cityID' => $cityID));
        return $query->row_array();
        }
    }
    public function get_areas($cityID, $areaID=FALSE)
    {
        if($areaID===FALSE){
            $query = $this->db->get_where('areas', array('areaCity' => $cityID));
            return $query->result_array();
        }else{
        $query = $this->db->get_where('areas', array('areaID' => $areaID));
        return $query->row_array();
        }
    }
	
    public function get_categorys($category_id = FALSE)
    {
        if($category_id === FALSE){
            $query = $this->db->get('categories');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('categories', array('category_id' => $category_id));
        return $query->row_array();
        }   
    }

    public function get_b2bcategorys($b2category_id = FALSE)
    {
        if($b2category_id === FALSE){
            $query = $this->db->get('b2categories');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('b2categories', array('b2category_id' => $b2category_id));
        return $query->row_array();
        }   
    }


    public function get_servcategorys($servcat_id = FALSE)
    {
        if($servcat_id === FALSE){
            $query = $this->db->get('servcategories');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('servcategories', array('servcat_id' => $servcat_id));
        return $query->row_array();
        }   
    }

    public function get_subcategorys($catID, $subID = FALSE)
    {
        if($subID === FALSE){
            $query = $this->db->get_where('subcategories', array('subcategory_main' => $catID));
            return $query->result_array();
        }else{
        $query = $this->db->get_where('subcategories', array('subcategory_main' => $catID, 'subcategory_id' => $subID));
        return $query->row_array();
        } 
    }
    public function get_b2bsubcategorys($catID, $subID = FALSE)
    {
        if($subID === FALSE){
            $query = $this->db->get_where('b2subcategories', array('b2subcategory_main' => $catID));
            return $query->result_array();
        }else{
        $query = $this->db->get_where('b2subcategories', array('b2subcategory_main' => $catID, 'b2subcategory_id' => $subID));
        return $query->row_array();
        } 
    }
    public function get_servsubcategorys($catID, $subID = FALSE)
    {
        if($subID === FALSE){
            $query = $this->db->get_where('servsubcategories', array('servsubcategory_main' => $catID));
            return $query->result_array();
        }else{
        $query = $this->db->get_where('servsubcategories', array('servsubcategory_main' => $catID, 'servsubcategory_id' => $subID));
        return $query->row_array();
        } 
    }

    
    public function get_measures($measure_id = FALSE)
    {
        if($measure_id === FALSE){
            $query = $this->db->get('measures');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('measures', array('measure_id' => $measure_id));
        return $query->row_array();
        }   
    }

    public function get_brands($brandID = FALSE)
    {
        if($brandID === FALSE){
            $query = $this->db->get('brands');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('brands', array('brandID' => $brandID));
        return $query->row_array();
        }   
    }


    public function get_b2b($b2bID = FALSE)
    {
        if($b2bID === FALSE){
            $query = $this->db->get('b2b');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('b2b', array('b2b_id' => $b2bID));
        return $query->row_array();
        }
    }


    public function get_banners($banner_id = FALSE){
        if($banner_id === FALSE){

            $query = $this->db->get('banners');
            return $query->result_array();

        }else{

        $query = $this->db->get_where('banners', array('banner_id' => $banner_id));
        return $query->row_array();
      }
    }



    public function get_tutors($spl_id = FALSE){
        if($spl_id === FALSE){
            $query = $this->db->get('screens');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('screens', array('spl_id' => $spl_id));
        return $query->row_array();
      }
    }


    public function get_coupons($coup_id = FALSE){
        if($coup_id === FALSE){
            $query = $this->db->get('coupons');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('coupons', array('coup_id' => $coup_id));
        return $query->row_array();
      }
    }




    public function get_franchises($frn_id  = FALSE){
        if($frn_id  === FALSE){
            $query = $this->db->get('franchises');
            return $query->result_array();
        }else{
        $query = $this->db->get_where('franchises', array('frn_id ' => $frn_id ));
        return $query->row_array();
      }
    }

    public function get_active_franchises(){
            $this->db->where('frn_status', '1');
            $query = $this->db->get('franchises');
            return $query->result_array();
     
    }






    public function get_sellers($seller_id = FALSE){
        if($seller_id === FALSE){
            $query=$this->db->get('sellers');
            return $query->result_array();
        }else{
            $query = $this->db->get_where('sellers', array('seller_id' => $seller_id));
            return $query->row_array();
        }   
    }

    public function get_b2sellers($seller_id = FALSE){
        if($seller_id === FALSE){
            $query=$this->db->get('b2b');
            return $query->result_array();
        }else{
            $query = $this->db->get_where('b2b', array('b2b_id' => $seller_id));
            return $query->row_array();
        }   
    }
    
    


    public function get_frsellers($seller_id = FALSE){
        if($seller_id === FALSE){
            $this->db->join('franchises', 'franchises.frn_id = sellers.seller_frnc');
            $this->db->where('seller_astatus', '1');
            $query=$this->db->get('sellers');
            return $query->result_array();
        }else{
            $query = $this->db->get_where('sellers', array('seller_id' => $seller_id));
            return $query->row_array();
        }
    }



    
    public function get_boys($boy_id = FALSE){
        if($boy_id === FALSE){
            $this->db->order_by("boy_id", "desc");
            $query=$this->db->get('boys');
            return $query->result_array();
        }else{
            $query = $this->db->get_where('boys', array('boy_id' => $boy_id));
            return $query->row_array();
        }
    }


    public function get_plans($subsc_id = FALSE){
        if($subsc_id === FALSE){
            $this->db->order_by("subsc_id", "desc");
            $query=$this->db->get('subscriptions');
            return $query->result_array();
        }else{
            $query = $this->db->get_where('subscriptions', array('subsc_id' => $subsc_id));
            return $query->row_array();
        }
    }

    public function get_seller_products($seller_id){
        $this->db->join('categories', 'categories.category_id = products.product_category');
        $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
        $this->db->where('product_store', $seller_id);
        $this->db->where('product_b2', '0');
        $query = $this->db->get('products');
        return  $query->result_array();   
    }


    function get_seller_subCategories($id){
        $this->db->select("subcategory_id, subcategory_name, subcategory_image, product_substatus");
        $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
        $this->db->group_by('product_subcategory');
        $this->db->where('product_store', $id);
        $this->db->where('product_a_status', '1'); 
        $query = $this->db->get('products');
        return  $query->result_array();
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

    public function get_b2b_products($b2b_id){
        $this->db->join('b2categories', 'b2categories.b2category_id = products.product_category');
        $this->db->join('b2subcategories', 'b2subcategories.b2subcategory_id = products.product_subcategory');
        $this->db->where('product_store', $b2b_id);
        $this->db->where('product_b2', '1');
        $query = $this->db->get('products');
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
    public function get_cats(){
        $this->db->where('category_status', '1');
        $query = $this->db->get('categories');
        return  $query->result_array();   
    }public function get_subcats(){
        $this->db->where('subcategory_status', '1');
        $query = $this->db->get('subcategories');
        return  $query->result_array();   
    }


    public function get_b2cats(){
        $this->db->where('b2category_status', '1');
        $query = $this->db->get('b2categories');
        return  $query->result_array();   
    }public function get_b2subcats(){
        $this->db->where('b2subcategory_status', '1');
        $query = $this->db->get('b2subcategories');
        return  $query->result_array();   
    }

    public function get_ameasures(){
        $this->db->where('measure_status', '1');
        $query = $this->db->get('measures');
        return  $query->result_array();   
    }



    public function get_orders($trk){
        $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
        $this->db->where('order_status', $trk);
        $query = $this->db->get('orders');
        $result2 = $query->result_array();
        return $result2;
    }
    public function get_orders_canc($trk, $typ2){
        $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
        $this->db->where('order_status', $trk);
        $this->db->where('order_canc', $typ2);
        $query = $this->db->get('orders');
        $result2 = $query->result_array();
        return $result2;
    }
    public function get_order($oid){
        $this->db->join('users', 'users.user_id = orders.order_user_id');
        $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
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


    public function countorderpayments(){
        $this->db->trans_start();
        $this->db->select_sum('order_final');
        $this->db->from('orders');
        $dt['all'] = $this->db->get()->row()->order_final;
        $this->db->trans_start();

        $this->db->trans_start();
        $this->db->select_sum('order_final');
        $this->db->from('orders');
        $this->db->where('order_pay_type', 'online');
        $dt['onl'] = $this->db->get()->row()->order_final;
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->select_sum('order_final');
        $this->db->from('orders');
        $this->db->where('order_pay_type', 'cod');
        $dt['cod'] = $this->db->get()->row()->order_final;
        $this->db->trans_complete();
        return $dt;
 

    }


    public function countservviews(){
        $this->db->trans_start();
        $this->db->select_sum('post_views');
        $this->db->from('posts');
        $this->db->where('post_status >', '0');
        $dt = $this->db->get()->row()->post_views;
        $this->db->trans_complete();
        return $dt;
    }


     public function countorderpaymentsdtl($stat){
        $this->db->trans_start();
        $this->db->select_sum('order_final');
        $this->db->from('orders');
        $this->db->where('order_status', $stat);
        $dt['all'] = $this->db->get()->row()->order_final;
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->select_sum('order_final');
        $this->db->from('orders');
        $this->db->where('order_status', $stat);
        $this->db->where('order_pay_type', 'online');
        $dt['onl'] = $this->db->get()->row()->order_final;
        $this->db->trans_complete();

        $this->db->trans_start();
        $this->db->select_sum('order_final');
        $this->db->from('orders');
        $this->db->where('order_status', $stat);
        $this->db->where('order_pay_type', 'cod');
        $dt['cod'] = $this->db->get()->row()->order_final;
        $this->db->trans_complete();

        return $dt;
     }




}

?>