<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Support extends CI_Controller {

    function __construct() {
        parent::__construct();
		if($this->session->userdata('utype') != "FSprt"){redirect(base_url('support'));}
		$this->load->model('Frnc_Model');
	}

	public function index(){ $this->dashboard(); }
	public function dashboard(){
			$data['title'] = 'Dashboard';
			$this->load->view('support/template/header', $data);
			$this->load->view('support/template/nav');
			$this->load->view('support/template/topbar');
 			$this->load->view('support/template/footer');
	}




// *************** sellers 
public function sellers(){
	$data['type'] = "mysellers";
	if(!$this->uri->segment(3)){
	   $data['sellers'] = $this->Frnc_Model->get_myspsellers();
	   $data['title'] = 'sellers';
	   $this->load->view('support/template/header', $data);
	   $this->load->view('support/template/nav');
	   $this->load->view('support/template/topbar');
	   $this->load->view('support/pages/sellers/mysellers');
	   $this->load->view('support/template/footer');
	}
   else{show_404();}
   }
// ***************  END sellers


public function seller(){
	$data['type'] = "mysellers";
	if(!$this->uri->segment(4)){
	   $seller_id = $this->uri->segment(3);
	   $data['seller'] = $this->Frnc_Model->get_myspsellers($seller_id);
	   $data['s_products'] = $this->Frnc_Model->get_seller_products($seller_id);
	   $data['title'] = 'sellers';
	   $this->load->view('support/template/header', $data);
	   $this->load->view('support/template/nav');
	   $this->load->view('support/template/topbar');
	   $this->load->view('support/pages/sellers/myseller');
	   $this->load->view('support/template/footer');
	}
   else{show_404();}
   }




   public function addproduct(){
	if(!$this->uri->segment(4)){
	   $seller_id = $this->uri->segment(3);
	   $data['seller'] = $this->Frnc_Model->get_myspsellers($seller_id);
	   $data['cats'] = $this->Frnc_Model->get_cats();
	   $data['subcats'] = $this->Frnc_Model->get_subcats();
	   $data['meas'] = $this->Frnc_Model->get_measures();
	   $data['title'] = $data['seller']['seller_store_name'];
	   $this->load->view('support/template/header', $data);
	   $this->load->view('support/template/nav');
	   $this->load->view('support/template/topbar');
	   $this->load->view('support/pages/products/addproduct');
	   $this->load->view('support/template/footer');
	}
   else{show_404();}
   }

   public function product(){
		$seller_id = $this->uri->segment(3);
		$data['seller'] = $this->Frnc_Model->get_myspsellers($seller_id);
	   $product_id = $this->uri->segment(4);
	   $data['product'] = $this->Frnc_Model->get_product($product_id);
	   $hasvar = $data['product']['product_var'];

if($hasvar == '1'){
	$data['vars'] = $this->Frnc_Model->get_pvars($product_id);
}else{
	$data['vars'] = [];
}


	   $data['title'] = "Product";
	   $this->load->view('support/template/header', $data);
	   $this->load->view('support/template/nav');
	   $this->load->view('support/template/topbar');
	   $this->load->view('support/pages/products/product');
	   $this->load->view('support/template/footer');
	}
   


   public function addp(){
	$seller = $this->input->post('product_store');
	$data['product_store'] = $seller;
	$data['product_name'] = $this->input->post('product_name');
	$data['product_category'] = $this->input->post('product_category');
	$data['product_subcategory'] = $this->input->post('product_subcategory');
	$data['product_measure'] = $this->input->post('product_measure');
	$data['product_description'] = $this->input->post('product_description');
	$data['product_a_status'] = '1';



	$stat = $this->Frnc_Model->get_field('product_substatus', 'products', array('product_subcategory' => $_POST['product_subcategory'], 'product_store' => $seller));
	if($stat === '' || $stat === null){ $substat = '0'; }else{ $substat = $stat; }
	$data['product_substatus'] = $substat;


	$hasvar = $this->input->post('product_var');
	if($hasvar == "0"){
		if(!empty($_FILES['product_img1'])){
			$data['product_img1']=$this->Frnc_Model->fileupload('product_img1', 'products', $data['product_store']);
		}else{$data['product_img1']='0';}
		if(!empty($_FILES['product_img2'])){
			$data['product_img2']=$this->Frnc_Model->fileupload('product_img2', 'products', $data['product_store']);
		}else{$data['product_img2']='0';}
		if(!empty($_FILES['product_img3'])){
			$data['product_img3']=$this->Frnc_Model->fileupload('product_img3', 'products', $data['product_store']);
		}else{$data['product_img3']='0';}
		if(!empty($_FILES['product_img4'])){
			$data['product_img4']=$this->Frnc_Model->fileupload('product_img4', 'products', $data['product_store']);
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

	$added = $this->Frnc_Model->addNew($data, 'products');

	if($added) {
		$this->session->set_flashdata('message', 'Added Successfully!');
		redirect(base_url('support/product/'.$seller.'/'.$added));
	}

   }




   
   public function updatep(){
	$seller = $this->input->post('product_store');
	$product_id = $this->input->post('product_id');
	$data['product_name'] = $this->input->post('product_name');
	$data['product_description'] = $this->input->post('product_description');
	$data['product_status'] = $this->input->post('product_status');
	$hasvar = $this->input->post('product_var');
	if($hasvar == "0"){
		if($_FILES['product_img1']['size'] != 0){
			$data['product_img1']=$this->Frnc_Model->fileupload('product_img1', 'products', $data['product_store']);
		}
		if($_FILES['product_img2']['size'] != 0){
			$data['product_img2']=$this->Frnc_Model->fileupload('product_img2', 'products', $data['product_store']);
		}
		if($_FILES['product_img3']['size'] != 0){
			$data['product_img3']=$this->Frnc_Model->fileupload('product_img3', 'products', $data['product_store']);
		}
		if($_FILES['product_img4']['size'] != 0){
			$data['product_img4']=$this->Frnc_Model->fileupload('product_img4', 'products', $data['product_store']);
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

		$update = $this->Frnc_Model->update($data, 'products', array('product_id' => $product_id));

		$this->session->set_flashdata('message', 'Updated Successfully!');
		redirect(base_url('support/product/'.$seller.'/'.$product_id));

   }


   public function edit_var(){
	if(!$this->uri->segment(3)){echo json_encode(array("status" => FALSE));}
	else{
		$var_id=$this->uri->segment(3);
		$data = $this->Frnc_Model->get_var($var_id);
		echo json_encode($data);
	}
   }



   public function add_var(){
	$seller = $this->input->post('seller_id');
	$product = $this->input->post('product_id');
	$data['vproduct_store'] = $seller;
	$data['vproduct_main'] = $product;
	$data['vproduct_p1'] = $this->input->post('vproduct_p1');
	$data['vproduct_status'] = $this->input->post('vproduct_status');
	if($_FILES['vproduct_img1']['size'] != 0){
		$data['vproduct_img1']=$this->Frnc_Model->fileupload('vproduct_img1', 'products', $seller);
		}else{$data['vproduct_img1']='0';}
		if($_FILES['vproduct_img2']['size'] != 0){
			$data['vproduct_img2']=$this->Frnc_Model->fileupload('vproduct_img2', 'products', $seller);
		}else{$data['vproduct_img2']='0';}
		if($_FILES['vproduct_img3']['size'] != 0){
			$data['vproduct_img3']=$this->Frnc_Model->fileupload('vproduct_img3', 'products', $seller);
		}else{$data['vproduct_img3']='0';}
		if($_FILES['vproduct_img4']['size'] != 0){
			$data['vproduct_img4']=$this->Frnc_Model->fileupload('vproduct_img4', 'products', $seller);
		}else{$data['vproduct_img4']='0';}

	$added = $this->Frnc_Model->addNew($data, 'productsvar');
	echo json_encode(array("status" => TRUE));
   }


   
   public function update_var(){
	$seller = $this->input->post('seller_id');
	$product = $this->input->post('product_id');
	$varid = $this->input->post('var_id');

	$data['vproduct_p1'] = $this->input->post('vproduct_p1');
	$data['vproduct_status'] = $this->input->post('vproduct_status');
	if($_FILES['vproduct_img1']['size'] != 0){
		$data['vproduct_img1']=$this->Frnc_Model->fileupload('vproduct_img1', 'products', $seller);
		}
		if($_FILES['vproduct_img2']['size'] != 0){
			$data['vproduct_img2']=$this->Frnc_Model->fileupload('vproduct_img2', 'products', $seller);
		}
		if($_FILES['vproduct_img3']['size'] != 0){
			$data['vproduct_img3']=$this->Frnc_Model->fileupload('vproduct_img3', 'products', $seller);
		}
		if($_FILES['vproduct_img4']['size'] != 0){
			$data['vproduct_img4']=$this->Frnc_Model->fileupload('vproduct_img4', 'products', $seller);
		}
		$update = $this->Frnc_Model->update($data, 'productsvar', array('vproduct_id' => $varid));
	echo json_encode(array("status" => TRUE));
   }





   public function add_sub(){
	$seller = $this->input->post('seller_id');
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
	$added = $this->Frnc_Model->addNew($data, 'productssub');
	echo json_encode(array("status" => TRUE));
   }

   public function edit_sub(){
	if(!$this->uri->segment(3)){echo json_encode(array("status" => FALSE));}
	else{
		$sub_id=$this->uri->segment(3);
		$data = $this->Frnc_Model->get_sub($sub_id);
		echo json_encode($data);
	}
   }

   public function update_sub(){
	$seller = $this->input->post('seller_id');
	$product = $this->input->post('product_id');
	$var = $this->input->post('svar_id');
	$sub = $this->input->post('suby_id');

	$data['sproduct_stock'] = $this->input->post('sproduct_stock');
	$data['sproduct_mrp'] = $this->input->post('sproduct_mrp');
	$data['sproduct_sale'] = $this->input->post('sproduct_sale');
	$data['sproduct_p2'] = $this->input->post('sproduct_p2');
	$data['sproduct_status'] = $this->input->post('sproduct_status');
	$update = $this->Frnc_Model->update($data, 'productssub', array('sproduct_id' => $sub));
	echo json_encode(array("status" => TRUE));

   }







/*
| -------------------------------------------------------------------
|  Pre Products
| -------------------------------------------------------------------
*/
public function products(){

	if(!$this->uri->segment(3)){
	   $data['products'] = $this->Frnc_Model->get_productspre();
	   $data['title'] = 'Pre Products';

	   $data['cats'] = $this->Frnc_Model->get_cats();
	   $data['subcats'] = $this->Frnc_Model->get_subcats();
	   $data['meas'] = $this->Frnc_Model->get_measures();

	   $this->load->view('support/template/header', $data);
	   $this->load->view('support/template/nav');
	   $this->load->view('support/template/topbar');
	   $this->load->view('support/pages/productspre/products');
	   $this->load->view('support/template/footer');
	}
	elseif($this->uri->segment(3)==='add_product'){

		if(!empty($_FILES['pd_img1'])){
			$pd_img1=$this->Frnc_Model->fileupload('pd_img1', 'products', 'ad');
		}else{$pd_img1='0';}
		if(!empty($_FILES['pd_img2'])){
			$pd_img2=$this->Frnc_Model->fileupload('pd_img2', 'products', 'ad');
		}else{$pd_img2='0';}
	   $data = array(
		   'pd_name' => $this->input->post('pd_name'),
		   'pd_category' => $this->input->post('pd_category'),
		   'pd_subcategory' => $this->input->post('pd_subcategory'),
		   'pd_measure' => $this->input->post('pd_measure'),
		   'pd_description' => $this->input->post('pd_description'),
		   'pd_status' => $this->input->post('pd_status'),
		   'pd_img1' => $pd_img1,
		   'pd_img2' => $pd_img2
	   );
	   $insert = $this->Frnc_Model->addNew($data, 'productspre');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_product'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $pd_id =$this->uri->segment(4);
		   $data = $this->Frnc_Model->get_productpre($pd_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_product'){


	if(!empty($_FILES['pd_img1'])){
		$data['pd_img1']=$this->Frnc_Model->fileupload('pd_img1', 'products', 'ad');
	}
	if(!empty($_FILES['pd_img2'])){
		$data['pd_img2']=$this->Frnc_Model->fileupload('pd_img2', 'products', 'ad');
	}
	   $data['pd_name'] = $this->input->post('pd_name');
	   $data['pd_category'] = $this->input->post('pd_category');
	   $data['pd_subcategory'] = $this->input->post('pd_subcategory');
	   $data['pd_measure'] = $this->input->post('pd_measure');
	   $data['pd_description'] = $this->input->post('pd_description');
	   $data['pd_status'] = $this->input->post('pd_status');
	   $data['pd_description'] = $this->input->post('pd_description');

	   $this->Frnc_Model->update($data, 'productspre', array('pd_id' => $this->input->post('pd_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }
// ***************  Pre Products








}