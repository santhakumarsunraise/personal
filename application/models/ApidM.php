<?php
 require 'aws/aws-autoloader.php';
 use Aws\S3\S3Client;
 
class ApidM extends CI_Model
{
    public function __construct() { parent::__construct(); $this->load->database(); }
    function checkBoyExists($phone, $phone_code){
      $query = $this->db->get_where('boys', array('boy_phone' => $phone, 'boy_phone_code' => $phone_code));
      if( $query->num_rows() > 0 ){ return $query->row_array(); } else{ return FALSE; }
    }
    function checkBoyLogin($phone, $phone_code){
      $query = $this->db->get_where('boys', array('boy_phone' => $phone, 'boy_status' => '1', 'boy_phone_code' => $phone_code));
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

function getNew($lat, $long, $kms){
  $this->db->select("*, ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 AS `distance`");
  $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
  $this->db->join('addresses', 'addresses.addr_id = orders.order_address');
  $this->db->where("ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 < $kms");
  $this->db->where('order_assign', '1');
  $this->db->order_by('order_id', 'desc'); 
  $query = $this->db->get('orders');
  return  $query->result_array();
  }
  
  
  function getMy($lat, $long, $kms, $id){
  $this->db->select("*, ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 AS `distance`");
  $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
  $this->db->join('addresses', 'addresses.addr_id = orders.order_address');
  $this->db->where('order_assign >', 1);
  $this->db->where('order_assign <', 4);
$this->db->where('order_boy', $id);
  $this->db->order_by('order_date', 'desc'); 
  $this->db->order_by('order_time', 'desc'); 
  $query = $this->db->get('orders');
  return  $query->result_array();
  }
  
    
  function getOver($id, $from, $to){
  $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
  $this->db->join('addresses', 'addresses.addr_id = orders.order_address');
  $this->db->where('order_assign', '4');
  $this->db->where('order_boy', $id);

if($from != ''){
  $this->db->where('order_delivered >=', $from);
  $this->db->where('order_delivered <=', $to);
}

  $query = $this->db->get('orders');
  return  $query->result_array();
  }


  function getDetail($order_id, $lat, $long){
    $this->db->select("*, ACOS( SIN( RADIANS( `seller_lat` ) ) * SIN( RADIANS( $lat ) ) + COS( RADIANS( `seller_lat` ) )* COS( RADIANS( $lat )) * COS( RADIANS( `seller_long` ) - RADIANS( $long )) ) * 6380 AS `distance`");
    $this->db->join('sellers', 'sellers.seller_id = orders.order_seller_id');
    $this->db->join('addresses', 'addresses.addr_id = orders.order_address');
    $this->db->where('order_id', $order_id);
    $query = $this->db->get('orders');
    return  $query->row_array();
    }


    function checkBoySubsc($boy_id){
      $this->db->where('boyt_boy', $boy_id);
      $this->db->where('boyt_type', '3');
      $this->db->where('boyt_status', '1');
      $this->db->where('boyt_valid >=', date("Y-m-d"));

      $query = $this->db->get('boys_transac');
      return  $query->num_rows();
    }
      
    function getTransac($boy_id){
      $this->db->where('boyt_boy', $boy_id);
      $query = $this->db->get('boys_transac');
      return  $query->result_array();
      }
    
    
    
   function getOrdersI($order_id){
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

    
                
    }else{
                
            
          if($row['product_img1'] != '0'){ $image = $row['product_img1']; }else{ $image = '0'; }

          $name = $row['product_name'];

          
            }
            
            
            


          $item[] = [
            'ordered_id' => $row['ordered_id'],
            'product_id' => $row['product_id'],
            'sproduct_id' => $row['ordered_sproduct'],
            'product_name' => $name,
            'product_measure' => $row['measure_name'],
            'product_img1' => $image,
            'ordered_amount' => $row['ordered_amount'],
            'ordered_qty' => $row['ordered_qty'],
          ];
        endforeach;
        $rdata['item'] = $item;
      } else{ $rdata['item'] = [];}
        return  $rdata;
    }


  
    

   
}