<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;

class Seller_Model extends CI_Model
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

    public function count($table, $where){
        return $this->db->get_where($table, $where)->num_rows();
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



public function get_neworders(){
    $id = $this->session->userdata('uid');
    $this->db->where('order_seller_id', $id);
    $query = $this->db->get('orders');
    $result2 = $query->result_array();
    return $result2;
}

public function get_seller_products(){
    $id = $this->session->userdata('uid');
    $this->db->join('measures', 'measures.measure_id = products.product_measure');
    $this->db->join('categories', 'categories.category_id = products.product_category');
    $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
    $this->db->where('product_store', $id);
    $query = $this->db->get('products');
    return  $query->result_array();   
}



public function get_orders($trk){
    $id = $this->session->userdata('uid');
    $this->db->where('order_seller_id', $id);
    $this->db->where('order_status', $trk);
    $query = $this->db->get('orders');
    $result2 = $query->result_array();
    return $result2;
}
public function get_order($oid){
    $id = $this->session->userdata('uid');
    $this->db->join('users', 'users.user_id = orders.order_user_id');
    $this->db->where('order_seller_id', $id);
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

    $id = $this->session->userdata('uid');


    $this->db->trans_start();
    $this->db->select_sum('order_final');
    $this->db->from('orders');
    $this->db->where('order_seller_id', $id);
    $dt['all'] = $this->db->get()->row()->order_final;
    $this->db->trans_start();

    $this->db->trans_start();
    $this->db->select_sum('order_final');
    $this->db->from('orders');
    $this->db->where('order_seller_id', $id);
    $this->db->where('order_pay_type', 'online');
    $dt['onl'] = $this->db->get()->row()->order_final;
    $this->db->trans_complete();

    $this->db->trans_start();
    $this->db->select_sum('order_final');
    $this->db->from('orders');
    $this->db->where('order_seller_id', $id);
    $this->db->where('order_pay_type', 'cod');
    $dt['cod'] = $this->db->get()->row()->order_final;
    $this->db->trans_complete();
    return $dt;


}


 public function countorderpaymentsdtl($stat){
    $id = $this->session->userdata('uid');

    $this->db->trans_start();
    $this->db->select_sum('order_final');
    $this->db->from('orders');
    $this->db->where('order_seller_id', $id);
    $this->db->where('order_status', $stat);
    $dt['all'] = $this->db->get()->row()->order_final;
    $this->db->trans_complete();

    $this->db->trans_start();
    $this->db->select_sum('order_final');
    $this->db->from('orders');
    $this->db->where('order_status', $stat);
    $this->db->where('order_seller_id', $id);
    $this->db->where('order_pay_type', 'online');
    $dt['onl'] = $this->db->get()->row()->order_final;
    $this->db->trans_complete();

    $this->db->trans_start();
    $this->db->select_sum('order_final');
    $this->db->from('orders');
    $this->db->where('order_status', $stat);
    $this->db->where('order_pay_type', 'cod');
    $this->db->where('order_seller_id', $id);
    $dt['cod'] = $this->db->get()->row()->order_final;
    $this->db->trans_complete();

    return $dt;
 }






}

?>