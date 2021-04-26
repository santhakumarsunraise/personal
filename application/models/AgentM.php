<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AgentM extends CI_Model
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

   
    public function login($email, $pass){
            $this->db->where('ag_email', $email);
            $this->db->where('ag_password', $pass);
            $query = $this->db->get('agents');
            return $query->row_array();
    }



    function agLogin($mobile){
        $this->db->where('ag_phone', $mobile);
        $this->db->where('ag_astatus', '1');
        $reslt = $this->db->get('agents')->num_rows();
        if($reslt >= 1){
            return true;
        }else{
            return false;
        }
    }



   
public function getmyssellers($id){
    $this->db->join('measures', 'measures.measure_id = products.product_measure');
    $this->db->join('categories', 'categories.category_id = products.product_category');
    $this->db->join('subcategories', 'subcategories.subcategory_id = products.product_subcategory');
    $this->db->where('product_id', $product_id);
    $query = $this->db->get('products');
    return  $query->row_array();   
}

public function checkValidity($sid){
    $this->db->where('pay_payer', $sid);
    $this->db->order_by("pay_id", "desc");
    $this->db->limit(1);
    $query = $this->db->get('payments')->row_array();
    return $query;   
}
public function getmycomps($id){
    $this->db->where('agp_main', $id);
    $this->db->where('agp_status', '1');
    $query = $this->db->get('agentpayments')->result_array();
    return $query;   
}
public function getupdates(){
    $this->db->where('agu_status', '1');
    $query = $this->db->get('ageupdates')->result_array();
    return $query;   
}

public function getmaterials(){
    $this->db->where('agm_status', '1');
    $query = $this->db->get('agentmaterials')->result_array();
    return $query;   
}




public function getmysellers($id){
    $this->db->where('seller_referral', $id);
    $query = $this->db->get('sellers');
    return  $query->result_array();   
}


}

?>