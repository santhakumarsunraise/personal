<?php

require 'aws/aws-autoloader.php';
use Aws\S3\S3Client;

class ApiserM extends CI_Model
{
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

    function get_banners(){
      $this->db->where('banner_status', '1');
      $this->db->where('banner_type', 'services');
      $query = $this->db->get('banners')->result_array();
      return  $query;
    }
    function get_homecategorys($lat, $long, $kms=10){
      $this->db->join('servcategories', 'servcategories.servcat_id = posts.post_cat');
      $this->db->where("ACOS( SIN( RADIANS( `post_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `post_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `post_long` ) - RADIANS( $long )) ) * 6380 < $kms");
      $this->db->where('servcat_status', '1');
      $this->db->where('post_status', '1');
      $this->db->group_by("servcat_id");
      $query = $this->db->get('posts')->result_array();
      return  $query;
    }
    function get_subcategorys($id){
      $this->db->where('servsubcategory_main', $id); 
      $this->db->where('servsubcategory_status', '1'); 
      $query = $this->db->get('servsubcategories')->result_array();
      return  $query;
    }
    function get_allcategorys(){
      $this->db->where('servcat_status', '1'); 
      $query = $this->db->get('servcategories')->result_array();
      return  $query;
    }
    function get_allsubcategorys($id){
      $this->db->where('servsubcategory_main', $id); 
      $this->db->where('servsubcategory_status', '1'); 
      $query = $this->db->get('servsubcategories')->result_array();
      return  $query;
    }

    function get_profiles($id){
      $this->db->where("FIND_IN_SET(".$id.",pro_subcats) >", 0);
      $this->db->where('pro_status', '1');
      $query = $this->db->get('profiles')->result_array();
      return  $query;
    }
    function check_service($user){
      date_default_timezone_set('Asia/Kolkata');
      $dt = date('Y-m-d'); 
      $this->db->where('pay_payer', $user);
      $this->db->where('pay_status', '1');
      $this->db->where('pay_validity >=', $dt);
      $this->db->limit(1); 
      $query = $this->db->get('paymentsserv')->num_rows();
      if($query > 0){ return true; }else{ return false; }
    }


    function my_posts($id){
      $this->db->join('servcategories', 'servcategories.servcat_id = posts.post_cat');
      $this->db->where('post_user', $id);
      $this->db->order_by('post_id', 'desc'); 
      $query = $this->db->get('posts');
      return  $query->result_array();
    }
    function my_post($id, $pstid){
      $this->db->join('servcategories', 'servcategories.servcat_id = posts.post_cat');
      $this->db->where('post_user', $id);
      $this->db->where('post_id', $pstid);
      $query = $this->db->get('posts');
      return  $query->row_array();
    }
    function get_sub($id){
      $this->db->where('servsubcategory_id', $id); 
      $query = $this->db->get('servsubcategories')->row_array();
      return  $query;
    }


    function get_posts($id, $lat, $long, $kms = '10'){
      $this->db->where("ACOS( SIN( RADIANS( `post_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `post_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `post_long` ) - RADIANS( $long )) ) * 6380 < $kms");
      $this->db->where("FIND_IN_SET(".$id.",post_scats) >", 0);
      $this->db->where('post_status', '1');
      $query = $this->db->get('posts')->result_array();
      return  $query;
    }


    function checkPostPre($id, $cat){
      $this->db->where('post_user', $id);
      $this->db->where('post_cat', $cat);
      $query = $this->db->get('posts')->num_rows();
      if($query == 0){ return true; }else{ return false; }
    }
    

    function get_post($pstid){
      $this->db->join('users', 'users.user_id = posts.post_user');
      $this->db->join('servcategories', 'servcategories.servcat_id = posts.post_cat');
      $this->db->where('post_id', $pstid);
      $query = $this->db->get('posts');
      return  $query->row_array();
    }



    function get_otherpostcats($pstuser){
      $this->db->select("servcat_id, servcat_name, servcat_image");
      $this->db->join('servcategories', 'servcategories.servcat_id = posts.post_cat');
      $this->db->where('post_user', $pstuser);
      $this->db->group_by('servcat_id'); 
      $query = $this->db->get('posts');
      return  $query->result_array();
    }




}