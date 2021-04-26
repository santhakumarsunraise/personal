<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth_Model extends CI_Model {
    function adminLogin($phone, $password){
        $this->db->select('admin_id, admin_name, admin_phone');
        $this->db->where('admin_phone', $phone);
        $this->db->where('admin_password', $password);
        $this->db->where('admin_status', '1');
        $this->db->limit(1);
        return $this->db->get('admin');
    }
    function frncLogin($email, $password){
        $this->db->select('frn_id, frn_name, frn_phone');
        $this->db->where('frn_email', $email);
        $this->db->where('frn_password', $password);
        $this->db->where('frn_status', '1');
        $this->db->limit(1);
        return $this->db->get('franchises');
    }
    function spLogin($email, $password){
        $this->db->where('fs_email', $email);
        $this->db->where('fs_password', $password);
        $this->db->where('fs_status', '1');
        $this->db->limit(1);
        return $this->db->get('franchise_support');
    }

    function slrLogin($mobile){
        $this->db->where('seller_phone', $mobile);
        $this->db->where('seller_verified', '1');
        $this->db->where('seller_status', 'active');
        $reslt = $this->db->get('sellers')->num_rows();
        if($reslt >= 1){
            return true;
        }else{
            return false;
        }
    }
    function update($data, $table, $where){
        $this->db->update($table, $data, $where);
        return $this->db->affected_rows();
      }
      function get_row($table, $where){
        $query = $this->db->get_where($table, $where);
        $result = $query->row_array();
        return $result;
      }

   
}

?>