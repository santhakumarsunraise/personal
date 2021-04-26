<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Franchise extends CI_Controller {

    function __construct() {
        parent::__construct();
		if($this->session->userdata('utype') != "Frnc"){redirect(base_url('franchise'));}
		$this->load->model('Frnc_Model');
	}

	public function index(){ $this->dashboard(); }
	public function dashboard(){
			$data['title'] = 'Dashboard';
			$this->load->view('franchise/template/header', $data);
			$this->load->view('franchise/template/nav');
			$this->load->view('franchise/template/topbar');
 			$this->load->view('franchise/template/footer');
	}


	public function addseller(){
		$data['title'] = 'Seller Form';
		$data['subscriptions'] = $this->Frnc_Model->get_subscriptions();
		$this->load->view('franchise/template/header', $data);
		$this->load->view('franchise/template/nav');
		$this->load->view('franchise/template/topbar');
		$this->load->view('franchise/pages/sellers/addseller');
		 $this->load->view('franchise/template/footer');
}


public function add_seller(){

	$unique = $this->input->post('seller_phone',TRUE);
	if(!empty($_FILES['store_image'])){
		$s_image=$this->Frnc_Model->fileupload('store_image', 'sellers', $unique);
	}else{$s_image='0';}
	if(!empty($_FILES['pan_image'])){
		$p_image=$this->Frnc_Model->fileupload('pan_image', 'sellers', $unique);
	}else{$p_image='0';}
	if(!empty($_FILES['seller_payment_img'])){
		$pay_image=$this->Frnc_Model->fileupload('seller_payment_img', 'sellers', $unique);
	}else{$pay_image='0';}


	$frnc_id=$this->session->userdata('uid');
	$data = array(
		'seller_name'=>$this->input->post('seller_name',TRUE),
		'seller_store_name'=>$this->input->post('seller_store_name',TRUE),
		'seller_phone_code'=>$this->input->post('seller_phone_code',TRUE),
		'seller_phone'=>$this->input->post('seller_phone',TRUE),
		'seller_email'=>$this->input->post('seller_email',TRUE),
		'seller_country'=>$this->input->post('seller_country',TRUE),
		'seller_state'=>$this->input->post('seller_state',TRUE),
		'seller_city'=>$this->input->post('seller_city',TRUE),
		'seller_pincode'=>$this->input->post('seller_pincode',TRUE),
		'seller_store_address'=>$this->input->post('seller_store_address',TRUE),
		'seller_lat'=>$this->input->post('seller_lat',TRUE),
		'seller_long'=>$this->input->post('seller_long',TRUE),
		'seller_pan_number'=>$this->input->post('seller_pan_number',TRUE),
		'seller_licence_number'=>$this->input->post('seller_lic_number',TRUE),
		'seller_minimum_order'=>$this->input->post('seller_minimum_order',TRUE),
		'seller_store_time_open'=>$this->input->post('seller_store_open',TRUE),
		'seller_store_time_close'=>$this->input->post('seller_store_close',TRUE),
		'seller_device_id'=>$this->input->post('seller_device',TRUE),
		'seller_verified'=>$this->input->post('seller_verified',TRUE),
		'seller_frnc'=>$frnc_id,
		'seller_store_image'=>$s_image,
		'seller_pan_image'=>$p_image,
		'seller_payment_img'=>$pay_image,
		'seller_payment_id'=>$this->input->post('seller_payment_id',TRUE),
		'seller_astatus'=>$this->input->post('seller_astatus',TRUE),
		'seller_status'=>"pending",
	);

	$added = $this->Frnc_Model->addNew($data, 'sellers');
	if($added) {

		$plan = $this->input->post('seller_plan',TRUE);
		$plandtl = $this->Frnc_Model->get_row('subscriptions', array('subsc_id' => $plan));
		$subsc_amount = $plandtl['subsc_amount'];
		$subsc_validity = $plandtl['subsc_validity'];
		$vDate = date('Y-m-d', strtotime("+$subsc_validity days"));  
		$data2 = array(
			'pay_susbc' => $plan,
			'pay_payer' => $added,
			'pay_ref' => 'fr'.$frnc_id,
			'pay_date' => date("Y-m-d"),
			'pay_time' => date("H:i:s"),
			'pay_validity' => $vDate,
			'pay_amount' => $subsc_amount,
			'pay_for' => 'seller',
			'pay_status' => '1'
		);
		$added2 = $this->Frnc_Model->addNew($data2, 'payments');
		$this->load->model('Sms_model');
		$message='Your '.$this->input->post('seller_store_name',TRUE).' is Added/Updated at ELLOCART Seller';
		$sent=$this->Sms_model->sendSMS($this->input->post('seller_phone',TRUE), $message);


		$this->session->set_flashdata('message', 'Added Successfully!');
		redirect(base_url('franchise/mysellers/'));
}

}



// *************** sellers 
public function newsellers(){
	$data['type'] = "newsellers";

	if(!$this->uri->segment(3)){

	   $data['sellers'] = $this->Frnc_Model->get_newsellers();
	   $data['title'] = 'sellers';
	   $this->load->view('franchise/template/header', $data);
	   $this->load->view('franchise/template/nav');
	   $this->load->view('franchise/template/topbar');
	   $this->load->view('franchise/pages/sellers/newsellers');
	   $this->load->view('franchise/template/footer');
	}
    elseif($this->uri->segment(3)==='edit_seller'){
	   if(!$this->uri->segment(4)){ redirect(base_url('franchise/newsellers')); }
	   else{

		   $seller_id = $this->uri->segment(4);
		   $data['seller'] = $this->Frnc_Model->get_newsellers($seller_id);
		   $data['s_products'] = $this->Frnc_Model->get_seller_products($seller_id);
		   $data['title'] = 'seller';
		   $this->load->view('franchise/template/header', $data);
		   $this->load->view('franchise/template/nav');
		   $this->load->view('franchise/template/topbar');
		   $this->load->view('franchise/pages/sellers/editseller');
		   $this->load->view('franchise/template/footer');
	   }
   }
   else{show_404();}
   }
// ***************  END sellers



public function update_seller(){

	$type = $this->uri->segment(3);
	$status = $this->input->post('seller_status',TRUE);
 	$frnc_id=$this->session->userdata('uid');
		   $data = array(
			   'seller_name'=>$this->input->post('seller_name',TRUE),
			   'seller_store_name'=>$this->input->post('seller_store_name',TRUE),
			   'seller_phone_code'=>$this->input->post('seller_phone_code',TRUE),
			   'seller_phone'=>$this->input->post('seller_phone',TRUE),
			   'seller_email'=>$this->input->post('seller_email',TRUE),
			   'seller_country'=>$this->input->post('seller_country',TRUE),
			   'seller_state'=>$this->input->post('seller_state',TRUE),
			   'seller_city'=>$this->input->post('seller_city',TRUE),
			   'seller_pincode'=>$this->input->post('seller_pincode',TRUE),
			   'seller_store_address'=>$this->input->post('seller_store_address',TRUE),
			   'seller_lat'=>$this->input->post('seller_lat',TRUE),
			   'seller_long'=>$this->input->post('seller_long',TRUE),
			   'seller_pan_number'=>$this->input->post('seller_pan_number',TRUE),
			   'seller_minimum_order'=>$this->input->post('seller_minimum_order',TRUE),
			   'seller_verified'=>$this->input->post('seller_verified',TRUE),
			   'seller_frnc'=>$frnc_id,
			   'seller_status'=>$status
		   );

$updated = $this->Frnc_Model->update($data, 'sellers', array('seller_id' => $this->input->post('seller_id')));
$this->session->set_flashdata('message', 'Updated Successfully!');


if($type === "newsellers"){
	$this->load->model('Sms_model');
	$message='Your '.$this->input->post('seller_store_name',TRUE).' is Added/Updated at ELLOCART Seller';
	$sent=$this->Sms_model->sendSMS($this->input->post('seller_phone',TRUE), $message);
	redirect(base_url('franchise/newsellers'));
}else{	redirect(base_url('franchise/mysellers'));

}

}


// *************** sellers 
public function mysellers(){
	$data['type'] = "mysellers";
	if(!$this->uri->segment(3)){
	   $data['sellers'] = $this->Frnc_Model->get_mysellers();
	   $data['title'] = 'sellers';
	   $this->load->view('franchise/template/header', $data);
	   $this->load->view('franchise/template/nav');
	   $this->load->view('franchise/template/topbar');
	   $this->load->view('franchise/pages/sellers/mysellers');
	   $this->load->view('franchise/template/footer');
	}
    elseif($this->uri->segment(3)==='edit_seller'){
	   if(!$this->uri->segment(4)){ redirect(base_url('admin/sellers')); }
	   else{
		   $seller_id = $this->uri->segment(4);
		   $data['seller'] = $this->Frnc_Model->get_newsellers($seller_id);
		   $data['s_products'] = $this->Frnc_Model->get_seller_products($seller_id);
		   $data['title'] = 'seller';
		   $this->load->view('franchise/template/header', $data);
		   $this->load->view('franchise/template/nav');
		   $this->load->view('franchise/template/topbar');
		   $this->load->view('franchise/pages/sellers/editseller');
		   $this->load->view('franchise/template/footer');
	   }
   }
   elseif($this->uri->segment(3)==='update_seller'){
	$status = $this->input->post('seller_status',TRUE);
	if($status === "active"){ $frnc_id=$this->session->userdata('uid'); }else{ $frnc_id = '0'; }


		   $data = array(
			   'seller_name'=>$this->input->post('seller_name',TRUE),
			   'seller_store_name'=>$this->input->post('seller_store_name',TRUE),
			   'seller_phone_code'=>$this->input->post('seller_phone_code',TRUE),
			   'seller_phone'=>$this->input->post('seller_phone',TRUE),
			   'seller_email'=>$this->input->post('seller_email',TRUE),
			   'seller_country'=>$this->input->post('seller_country',TRUE),
			   'seller_state'=>$this->input->post('seller_state',TRUE),
			   'seller_city'=>$this->input->post('seller_city',TRUE),
			   'seller_pincode'=>$this->input->post('seller_pincode',TRUE),
			   'seller_store_address'=>$this->input->post('seller_store_address',TRUE),
			   'seller_lat'=>$this->input->post('seller_lat',TRUE),
			   'seller_long'=>$this->input->post('seller_long',TRUE),
			   'seller_pan_number'=>$this->input->post('seller_pan_number',TRUE),
			   'seller_minimum_order'=>$this->input->post('seller_minimum_order',TRUE),
			   'seller_verified'=>$this->input->post('seller_verified',TRUE),
			   'seller_frnc'=>$frnc_id,
			   'seller_status'=>$status
		   );

	   $updated = $this->Frnc_Model->update($data, 'sellers', array('seller_id' => $this->input->post('seller_id')));
	   if($updated) {
		$this->session->set_flashdata('message', 'Updated Successfully!');
		redirect(base_url('franchise/newsellers/'));
		}
	 
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }
// ***************  END sellers



public function newboys(){
	$data['type'] = "newboys";
	if(!$this->uri->segment(3)){
	   $data['boys'] = $this->Frnc_Model->get_newboys();
	   $data['title'] = 'boys';
	   $this->load->view('franchise/template/header', $data);
	   $this->load->view('franchise/template/nav');
	   $this->load->view('franchise/template/topbar');
	   $this->load->view('franchise/pages/boys/newboys');
	   $this->load->view('franchise/template/footer');
	}
    elseif($this->uri->segment(3)==='edit_boy'){
	   if(!$this->uri->segment(4)){ redirect(base_url('franchise/newboys')); }
	   else{
		   $seller_id = $this->uri->segment(4);
		   $data['boy'] = $this->Frnc_Model->get_newboys($seller_id);
		   $data['title'] = 'Boy';
		   $this->load->view('franchise/template/header', $data);
		   $this->load->view('franchise/template/nav');
		   $this->load->view('franchise/template/topbar');
		   $this->load->view('franchise/pages/boys/editboy');
		   $this->load->view('franchise/template/footer');
	   }
   }
   else{show_404();}
}



public function update_boy(){

	$type = $this->uri->segment(3);
 	$frnc_id=$this->session->userdata('uid');
		   $data = array(
			   'boy_name'=>$this->input->post('boy_name',TRUE),
			   'boy_phone_code'=>$this->input->post('boy_phone_code',TRUE),
			   'boy_phone'=>$this->input->post('boy_phone',TRUE),
			   'boy_country'=>$this->input->post('boy_country',TRUE),
			   'boy_state'=>$this->input->post('boy_state',TRUE),
			   'boy_city'=>$this->input->post('boy_city',TRUE),
			   'boy_pincode'=>$this->input->post('boy_pincode',TRUE),
			   'boy_address'=>$this->input->post('boy_address',TRUE),
			   'boy_bank_name'=>$this->input->post('boy_bank_name',TRUE),
			   'boy_bank_branch'=>$this->input->post('boy_bank_branch',TRUE),
			   'boy_bank_acc'=>$this->input->post('boy_bank_acc',TRUE),
			   'boy_bank_ifsc'=>$this->input->post('boy_bank_ifsc',TRUE),
			   'boy_bank_phone'=>$this->input->post('boy_bank_phone',TRUE),
			   'boy_status'=>$this->input->post('boy_status',TRUE),
			   'boy_frnc'=>$frnc_id,
			   'boy_wallet'=>$this->input->post('boy_wallet',TRUE),

		   );

$updated = $this->Frnc_Model->update($data, 'boys', array('boy_id' => $this->input->post('boy_id')));
$this->session->set_flashdata('message', 'Updated Successfully!');

if($type === "newboys"){
	redirect(base_url('franchise/newboys'));
}else{	redirect(base_url('franchise/myboys'));

}

}



public function myboys(){
	$data['type'] = "myboys";
	if(!$this->uri->segment(3)){
	   $data['boys'] = $this->Frnc_Model->get_myboys();
	   $data['title'] = 'My Boys';
	   $this->load->view('franchise/template/header', $data);
	   $this->load->view('franchise/template/nav');
	   $this->load->view('franchise/template/topbar');
	   $this->load->view('franchise/pages/boys/myboys');
	   $this->load->view('franchise/template/footer');
	}
    elseif($this->uri->segment(3)==='edit_boy'){
		if(!$this->uri->segment(4)){ redirect(base_url('franchise/newboys')); }
		else{
			$boy_id = $this->uri->segment(4);
			$data['boy'] = $this->Frnc_Model->get_myboys($boy_id);
			$data['title'] = 'Boy';
			$this->load->view('franchise/template/header', $data);
			$this->load->view('franchise/template/nav');
			$this->load->view('franchise/template/topbar');
			$this->load->view('franchise/pages/boys/editboy');
			$this->load->view('franchise/template/footer');
		}
	}
	else{show_404();}
}






public function orders(){
	$type = $this->uri->segment(3);
	$data['orders'] = $this->Frnc_Model->get_orders($type);
	$data['title'] = 'Orders';
	$this->load->view('franchise/template/header', $data);
	$this->load->view('franchise/template/nav');
	$this->load->view('franchise/template/topbar');
	$this->load->view('franchise/pages/orders/orders');
	$this->load->view('franchise/template/footer');
}
public function orderdetail(){
	if($this->uri->segment(3)){
	  $odrid = $this->uri->segment(3);
	  $data['order'] = $this->Frnc_Model->get_order($odrid);
	  $data['cart'] = $this->Frnc_Model->get_order_cart($odrid);
	  $data['title'] = 'Order';
	  $this->load->view('franchise/template/header', $data);
	  $this->load->view('franchise/template/nav');
	  $this->load->view('franchise/template/topbar');
	  $this->load->view('franchise/pages/orders/orderdetail');
	  $this->load->view('franchise/template/footer');
	  }
	  else{redirect(base_url('seller/orders'));}
}

public function orderUpdate(){
	$od_id = $this->input->post('order_id');
	$data['order_status'] = $this->input->post('orderTrackStatus');
	$update = $this->Frnc_Model->update($data, 'orders', array('order_id ' => $od_id));
	redirect(base_url("franchise/orderdetail/$od_id"));
}











// *************** sellers 
public function mysupport(){
	$data['type'] = "mysupport";
	if(!$this->uri->segment(3)){
	   $data['supports'] = $this->Frnc_Model->get_mysupport();
	   $data['title'] = 'Support';
	   $this->load->view('franchise/template/header', $data);
	   $this->load->view('franchise/template/nav');
	   $this->load->view('franchise/template/topbar');
	   $this->load->view('franchise/pages/support/mysupport');
	   $this->load->view('franchise/template/footer');
	}
	elseif($this->uri->segment(3)==='add_support'){
		$id = $this->session->userdata('uid');
		$data = array(
			'fs_name' => $this->input->post('fs_name'),
			'fs_phone' => $this->input->post('fs_phone'),
			'fs_email' => $this->input->post('fs_email'),
			'fs_status' => $this->input->post('fs_status'),
			'fs_password' => $this->input->post('fs_password'),
			'fs_frnc' => $id
		);
		$insert = $this->Frnc_Model->addNew($data, 'franchise_support');
		echo json_encode(array("status" => TRUE));
	}

	elseif($this->uri->segment(3)==='edit_support'){
		if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
		else{
			$fs_id = $this->uri->segment(4);
			$data = $this->Frnc_Model->get_mysupport($fs_id);
			echo json_encode($data);
		}
	}

	elseif($this->uri->segment(3)==='update_support'){
		$id = $this->session->userdata('uid');
			$data = array(
				'fs_name' => $this->input->post('fs_name'),
				'fs_phone' => $this->input->post('fs_phone'),
				'fs_email' => $this->input->post('fs_email'),
				'fs_password' => $this->input->post('fs_password'),
				'fs_status' => $this->input->post('fs_status'),
			);
	$this->Frnc_Model->update($data, 'franchise_support', array('fs_id' => $this->input->post('fs_id'), 'fs_frnc' => $id));
			echo json_encode(array("status" => TRUE));
		}


   else{show_404();}
   }
// ***************  END sellers





public function logout(){ $this->session->sess_destroy(); redirect(base_url('franchise')); }



}