<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->library('authlib'); 
		$this->load->model('Auth_Model'); 
	}



function admin(){
    $this->form_validation->set_rules('admin_phone', 'Phone', 'required|trim');
	$this->form_validation->set_rules('admin_password', 'Password', 'required|trim');
	if($this->form_validation->run()){
			 $phone=$this->input->post('admin_phone',TRUE);
			 $password=md5($this->input->post('admin_password',TRUE));
			 $loginResult=$this->Auth_Model->adminLogin($phone, $password);
		if($loginResult->num_rows() > 0){
			$adminData=$loginResult->row_array();
			$sessionkey=$this->authlib->genSessionKey($adminData['admin_id'], 'Admin');
				$login_data = array(
					'uid' => $adminData['admin_id'],
					'admin_name' => $adminData['admin_name'],
					'admin_phone' => $adminData['admin_phone'],
					'utype' => 'Admin',
					'sessionkey' => $sessionkey,
					'loggedin' => true,
				);
			$this->session->set_userdata($login_data);
			$this->session->set_flashdata('message', 'Login Successful');
			redirect(base_url('admin/dashboard'));
		}else{
		   $this->session->set_flashdata('message', 'Login Failed');
		   redirect(base_url('admin'));
		}
	}
	else{
		$data['page']="Admin login";
		$this->load->view('admin/template/header', $data);
		$this->load->view('admin/template/login');
		$this->load->view('admin/template/footer');
	}
}




function franchise(){
    $this->form_validation->set_rules('frnc_email', 'Email', 'required|trim');
	$this->form_validation->set_rules('frnc_password', 'Password', 'required|trim');
	if($this->form_validation->run()){
			 $email=$this->input->post('frnc_email',TRUE);
			 $password=md5($this->input->post('frnc_password',TRUE));
			 $loginResult=$this->Auth_Model->frncLogin($email, $password);
		if($loginResult->num_rows() > 0){
			$adminData=$loginResult->row_array();
				$login_data = array(
					'uid' => $adminData['frn_id'],
					'frn_name' => $adminData['frn_name'],
					'frn_phone' => $adminData['frn_phone'],
					'utype' => 'Frnc',
					'loggedin' => true,
				);
			$this->session->set_userdata($login_data);
			$this->session->set_flashdata('message', 'Login Successful');
			redirect(base_url('franchise/dashboard'));
		}else{
		   $this->session->set_flashdata('message', 'Login Failed');
		   redirect(base_url('franchise'));
		}
	}
	else{
		$data['page']="Franchise login";
		$this->load->view('franchise/template/header', $data);
		$this->load->view('franchise/template/login');
		$this->load->view('franchise/template/footer');
	}
}



function support(){
    $this->form_validation->set_rules('sp_email', 'Email', 'required|trim');
	$this->form_validation->set_rules('sp_password', 'Password', 'required|trim');
	if($this->form_validation->run()){
			 $email=$this->input->post('sp_email',TRUE);
			 $password=$this->input->post('sp_password',TRUE);
			 $loginResult=$this->Auth_Model->spLogin($email, $password);
		if($loginResult->num_rows() > 0){
			$adminData=$loginResult->row_array();
				$login_data = array(
					'uid' => $adminData['fs_id'],
					'fs_frnc' => $adminData['fs_frnc'],
					'fs_name' => $adminData['fs_name'],
					'fs_phone' => $adminData['fs_phone'],
					'fs_email' => $adminData['fs_email'],
					'utype' => 'FSprt',
					'loggedin' => true,
				);
			$this->session->set_userdata($login_data);
			$this->session->set_flashdata('message', 'Login Successful');
			redirect(base_url('support/dashboard'));
		}else{
		   $this->session->set_flashdata('message', 'Login Failed');
		   redirect(base_url('support'));
		}
	}
	else{
		$data['page']="Support login";
		$this->load->view('support/template/header', $data);
		$this->load->view('support/template/login');
		$this->load->view('support/template/footer');
	}
}








function seller(){
		if($this->session->userdata('utype') == "Sellr"){redirect(base_url('seller/dashboard'));}
		$data['page']="Seller login";
		$this->load->view('seller/template/header', $data);
		$this->load->view('seller/template/login');
		$this->load->view('seller/template/footer');
}

function seller_otp_send(){
	$mobile=$this->input->post('slr_mobile',TRUE);
	$res=$this->Auth_Model->slrLogin($mobile);
	if($res){
		$this->load->model('Sms_model');
		$otp_digit = mt_rand(1000, 9999);
		$data = array('seller_otp' => $otp_digit);
		$updated=$this->Auth_Model->update($data, 'sellers', array('seller_status' => "active", 'seller_verified' => '1', 'seller_phone' => $mobile));
		$message="Your OTP at ELLOCART is ".$otp_digit;
		$sent=$this->Sms_model->sendSMS($mobile, $message);
		if($sent){
			echo json_encode(array("status" => TRUE, "phone" => "Sent to $mobile"));
		} else{
			echo json_encode(array("status" => FALSE, "phone" => ""));
		}

	}else{
		echo json_encode(array("status" => FALSE, "phone" => ""));
	}
}
function seller_otp_verify(){
	$mobile=$this->input->post('slr_mobile',TRUE);
	$otp=$this->input->post('slr_otp',TRUE);
	$data = array('seller_status' => "active", 'seller_verified' => '1', 'seller_phone' => $mobile);
	$slrres=$this->Auth_Model->get_row('sellers', $data);
	if($otp == $slrres['seller_otp']){
		$login_data = array(
			'uid' => $slrres['seller_id'],
			'slr_name' => $slrres['seller_store_name'],
			'slr_phone' => $slrres['seller_phone'],
			'utype' => 'Sellr',
			'loggedin' => true,
		);
		$this->session->set_userdata($login_data);
		echo json_encode(array("status" => TRUE));
	}else{
		echo json_encode(array("status" => FALSE));
	}
}


public function logout(){ $this->session->sess_destroy(); redirect(base_url()); }

}