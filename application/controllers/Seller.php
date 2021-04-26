<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Seller extends CI_Controller {
    function __construct() {
        parent::__construct();
		if($this->session->userdata('utype') != "Sellr"){redirect(base_url('seller'));}
		$this->load->model('Seller_Model');
	}

	public function index(){ $this->dashboard(); }
	public function dashboard(){
    $id = $this->session->userdata('uid');

	$data['seller_id'] = $id;

			$data['seller_ostatus'] = $this->Seller_Model->get_field('seller_ostatus', 'sellers', array('seller_id'=> $id));
			$data['orders'] = $this->Seller_Model->count('orders', array('order_seller_id >'=> '0', 'order_status >'=> '0'));
			$data['ordersnew'] = $this->Seller_Model->count('orders', array('order_status'=> '1'));
			$data['orderspend'] = $this->Seller_Model->count('orders', array('order_status'=> '2'));
			$data['orderscomp'] = $this->Seller_Model->count('orders', array('order_status'=> '3'));
			$data['orderscanc'] = $this->Seller_Model->count('orders', array('order_status'=> '4'));

			$data['payments'] = $this->Seller_Model->countorderpayments();
			$data['paymentsrcvd'] = $this->Seller_Model->countorderpaymentsdtl('1');
			$data['paymentspdng'] = $this->Seller_Model->countorderpaymentsdtl('2');
			$data['paymentscncl'] = $this->Seller_Model->countorderpaymentsdtl('4');
			$data['paymentscomp'] = $this->Seller_Model->countorderpaymentsdtl('3');

			$data['payments'] = $this->Seller_Model->countorderpayments();
			$data['payments'] = $this->Seller_Model->countorderpayments();
			$data['payments'] = $this->Seller_Model->countorderpayments();




			$data['title'] = 'Dashboard';
			$this->load->view('seller/template/header', $data);
			$this->load->view('seller/template/nav');
			$this->load->view('seller/template/topbar');
			$this->load->view('seller/pages/dashboard');
 			$this->load->view('seller/template/footer');
	}




	public function online(){
		$id = $this->session->userdata('uid');
		$stat = $this->uri->segment(3);
		$updated = $this->Seller_Model->update(array('seller_ostatus' => $stat), 'sellers', array('seller_id' => $id));
		redirect(base_url('seller/dashboard'));
	}



// *************** orders
public function orders(){
	$type = $this->uri->segment(3);
	$data['orders'] = $this->Seller_Model->get_orders($type);
	$data['title'] = 'Orders';
	$this->load->view('seller/template/header', $data);
	$this->load->view('seller/template/nav');
	$this->load->view('seller/template/topbar');
	$this->load->view('seller/pages/orders/orders');
	$this->load->view('seller/template/footer');
}
public function orderdetail(){
	if($this->uri->segment(3)){
	  $odrid = $this->uri->segment(3);
	  $data['order'] = $this->Seller_Model->get_order($odrid);
	  $data['cart'] = $this->Seller_Model->get_order_cart($odrid);
	  $data['title'] = 'Order';
	  $this->load->view('seller/template/header', $data);
	  $this->load->view('seller/template/nav');
	  $this->load->view('seller/template/topbar');
	  $this->load->view('seller/pages/orders/orderdetail');
	  $this->load->view('seller/template/footer');
	  }
	  else{redirect(base_url('seller/orders/1'));}
}


public function order_Update(){
	$od_id = $this->input->post('orderID');
	$data['order_status'] = $this->input->post('orderTrackStatus');
	$update = $this->Seller_Model->update($data, 'orders', array('order_id ' => $od_id));
	redirect(base_url("seller/orderdetail/$od_id"));
}


// *************** Products 
public function products(){
	  $data['s_products'] = $this->Seller_Model->get_seller_products();
	  $data['title'] = 'Mt Items';
	  $this->load->view('seller/template/header', $data);
	  $this->load->view('seller/template/nav');
	  $this->load->view('seller/template/topbar');
	  $this->load->view('seller/pages/products/products');
	  $this->load->view('seller/template/footer');
  }
  // ***************  END sellers


public function addproduct(){
	if(!$this->uri->segment(3)){
	   $data['cats'] = $this->Seller_Model->get_cats();
	   $data['subcats'] = $this->Seller_Model->get_subcats();
	   $data['meas'] = $this->Seller_Model->get_measures();
	   $data['title'] = "New Item Upload";
	   $this->load->view('seller/template/header', $data);
	   $this->load->view('seller/template/nav');
	   $this->load->view('seller/template/topbar');
	   $this->load->view('seller/pages/products/addproduct');
	   $this->load->view('seller/template/footer');
	}
   else{show_404();}
   }




   public function product(){
	$seller_id = $this->session->userdata('uid');
	//$data['seller'] = $this->Seller_Model->get_myspsellers($seller_id);
   $product_id = $this->uri->segment(3);
   $data['product'] = $this->Seller_Model->get_product($product_id);
   $hasvar = $data['product']['product_var'];
if($hasvar == '1'){
$data['vars'] = $this->Seller_Model->get_pvars($product_id);
}else{
$data['vars'] = [];
}
   $data['title'] = "Product";
   $this->load->view('seller/template/header', $data);
   $this->load->view('seller/template/nav');
   $this->load->view('seller/template/topbar');
   $this->load->view('seller/pages/products/product');
   $this->load->view('seller/template/footer');
}



   public function addp(){
	$seller = $this->session->userdata('uid');
	$data['product_store'] = $seller;
	$data['product_name'] = $this->input->post('product_name');
	$data['product_category'] = $this->input->post('product_category');
	$data['product_subcategory'] = $this->input->post('product_subcategory');
	$data['product_measure'] = $this->input->post('product_measure');
	$data['product_description'] = $this->input->post('product_description');
	$data['product_a_status'] = '1';
	$hasvar = $this->input->post('product_var');
	if($hasvar == "0"){
		if(!empty($_FILES['product_img1'])){
			$data['product_img1']=$this->Seller_Model->fileupload('product_img1', 'products', $data['product_store']);
		}else{$data['product_img1']='0';}
		if(!empty($_FILES['product_img2'])){
			$data['product_img2']=$this->Seller_Model->fileupload('product_img2', 'products', $data['product_store']);
		}else{$data['product_img2']='0';}
		if(!empty($_FILES['product_img3'])){
			$data['product_img3']=$this->Seller_Model->fileupload('product_img3', 'products', $data['product_store']);
		}else{$data['product_img3']='0';}
		if(!empty($_FILES['product_img4'])){
			$data['product_img4']=$this->Seller_Model->fileupload('product_img4', 'products', $data['product_store']);
		}else{$data['product_img4']='0';}

		$data['product_var'] = '0';
		$data['product_mrp'] = $this->input->post('product_mrp');
		$data['product_sale'] = $this->input->post('product_sale');
		$data['product_stock'] = $this->input->post('product_stock');
		$data['product_status'] = $this->input->post('product_status');
	}else{
		$data['product_img1']='0';$data['product_img2']='0';$data['product_img3']='0';$data['product_img4']='0';
		$data['product_var'] = '1';
		$data['product_mrp'] = '0';
		$data['product_sale'] = '0';
		$data['product_stock'] = '0';
		$data['product_status'] = '1';
	}

	$added = $this->Seller_Model->addNew($data, 'products');

	if($added) {
		$this->session->set_flashdata('message', 'Added Successfully!');
		redirect(base_url('seller/product/'.$added));
	}
   }





   
   public function updatep(){
	$seller = $this->session->userdata('uid');
	$product_id = $this->input->post('product_id');
	$data['product_name'] = $this->input->post('product_name');
	$data['product_description'] = $this->input->post('product_description');
	$data['product_status'] = $this->input->post('product_status');
	$hasvar = $this->input->post('product_var');
	if($hasvar == "0"){
		if($_FILES['product_img1']['size'] != 0){
			$data['product_img1']=$this->Seller_Model->fileupload('product_img1', 'products', $data['product_store']);
		}
		if($_FILES['product_img2']['size'] != 0){
			$data['product_img2']=$this->Seller_Model->fileupload('product_img2', 'products', $data['product_store']);
		}
		if($_FILES['product_img3']['size'] != 0){
			$data['product_img3']=$this->Seller_Model->fileupload('product_img3', 'products', $data['product_store']);
		}
		if($_FILES['product_img4']['size'] != 0){
			$data['product_img4']=$this->Seller_Model->fileupload('product_img4', 'products', $data['product_store']);
		}
		$data['product_var'] = '0';
		$data['product_mrp'] = $this->input->post('product_mrp');
		$data['product_sale'] = $this->input->post('product_sale');
		$data['product_stock'] = $this->input->post('product_stock');
	}else{
		$data['product_img1']='0';$data['product_img2']='0';$data['product_img3']='0';$data['product_img4']='0';
		$data['product_var'] = '1';
		$data['product_mrp'] = '0';
		$data['product_sale'] = '0';
		$data['product_stock'] = '0';
		$data['product_status'] = '1';
	}

		$update = $this->Seller_Model->update($data, 'products', array('product_id' => $product_id));

		$this->session->set_flashdata('message', 'Updated Successfully!');
		redirect(base_url('seller/product/'.$product_id));

   }











   public function edit_var(){
	if(!$this->uri->segment(3)){echo json_encode(array("status" => FALSE));}
	else{
		$var_id=$this->uri->segment(3);
		$data = $this->Seller_Model->get_var($var_id);
		echo json_encode($data);
	}
   }



   public function add_var(){
	$seller = $this->session->userdata('uid');
	$product = $this->input->post('product_id');
	$data['vproduct_store'] = $seller;
	$data['vproduct_main'] = $product;
	$data['vproduct_p1'] = $this->input->post('vproduct_p1');
	$data['vproduct_status'] = $this->input->post('vproduct_status');
	if($_FILES['vproduct_img1']['size'] != 0){
		$data['vproduct_img1']=$this->Seller_Model->fileupload('vproduct_img1', 'products', $seller);
		}else{$data['vproduct_img1']='0';}
		if($_FILES['vproduct_img2']['size'] != 0){
			$data['vproduct_img2']=$this->Seller_Model->fileupload('vproduct_img2', 'products', $seller);
		}else{$data['vproduct_img2']='0';}
		if($_FILES['vproduct_img3']['size'] != 0){
			$data['vproduct_img3']=$this->Seller_Model->fileupload('vproduct_img3', 'products', $seller);
		}else{$data['vproduct_img3']='0';}
		if($_FILES['vproduct_img4']['size'] != 0){
			$data['vproduct_img4']=$this->Seller_Model->fileupload('vproduct_img4', 'products', $seller);
		}else{$data['vproduct_img4']='0';}

	$added = $this->Seller_Model->addNew($data, 'productsvar');
	echo json_encode(array("status" => TRUE));
   }


   
   public function update_var(){
	$seller = $this->input->post('seller_id');
	$product = $this->input->post('product_id');
	$varid = $this->input->post('var_id');

	$data['vproduct_p1'] = $this->input->post('vproduct_p1');
	$data['vproduct_status'] = $this->input->post('vproduct_status');
	if($_FILES['vproduct_img1']['size'] != 0){
		$data['vproduct_img1']=$this->Seller_Model->fileupload('vproduct_img1', 'products', $seller);
		}
		if($_FILES['vproduct_img2']['size'] != 0){
			$data['vproduct_img2']=$this->Seller_Model->fileupload('vproduct_img2', 'products', $seller);
		}
		if($_FILES['vproduct_img3']['size'] != 0){
			$data['vproduct_img3']=$this->Seller_Model->fileupload('vproduct_img3', 'products', $seller);
		}
		if($_FILES['vproduct_img4']['size'] != 0){
			$data['vproduct_img4']=$this->Seller_Model->fileupload('vproduct_img4', 'products', $seller);
		}
		$update = $this->Seller_Model->update($data, 'productsvar', array('vproduct_id' => $varid));
	echo json_encode(array("status" => TRUE));
   }





   public function add_sub(){
	$seller = $this->session->userdata('uid');
	$product = $this->input->post('product_id');
	$var = $this->input->post('svar_id');
	$data['sproduct_store'] = $seller;
	$data['sproduct_main'] = $product;
	$data['sproduct_var'] = $var;
	$data['sproduct_stock'] = $this->input->post('sproduct_stock');
	$data['sproduct_mrp'] = $this->input->post('sproduct_mrp');
	$data['sproduct_sale'] = $this->input->post('sproduct_sale');
	$data['sproduct_p2'] = $this->input->post('sproduct_p2');
	$data['sproduct_status'] = $this->input->post('sproduct_status');
	$added = $this->Seller_Model->addNew($data, 'productssub');
	echo json_encode(array("status" => TRUE));
   }

   public function edit_sub(){
	if(!$this->uri->segment(3)){echo json_encode(array("status" => FALSE));}
	else{
		$sub_id=$this->uri->segment(3);
		$data = $this->Seller_Model->get_sub($sub_id);
		echo json_encode($data);
	}
   }

   public function update_sub(){
	$seller = $this->session->userdata('uid');
	$product = $this->input->post('product_id');
	$var = $this->input->post('svar_id');
	$sub = $this->input->post('suby_id');

	$data['sproduct_stock'] = $this->input->post('sproduct_stock');
	$data['sproduct_mrp'] = $this->input->post('sproduct_mrp');
	$data['sproduct_sale'] = $this->input->post('sproduct_sale');
	$data['sproduct_p2'] = $this->input->post('sproduct_p2');
	$data['sproduct_status'] = $this->input->post('sproduct_status');
	$update = $this->Seller_Model->update($data, 'productssub', array('sproduct_id' => $sub));
	echo json_encode(array("status" => TRUE));

   }




   public function logout(){ $this->session->sess_destroy(); redirect(base_url('seller')); }


}