<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
		$this->load->library('authlib');
		//if($this->session->userdata('utype') != "Admin"){redirect(base_url('admin'));}
		$this->load->model('Admin_Model');
	}
	public function index(){ $this->dashboard(); }
	

	public function dashboard(){
			$data['title'] = 'Dashboard';

			$data['users'] = $this->Admin_Model->count('users', array('user_status'=> '1'));
			$data['usersand'] = $this->Admin_Model->count('users', array('user_status'=> '1', 'user_device'=> "ANDROID"));
			$data['usersios'] = $this->Admin_Model->count('users', array('user_status'=> '1', 'user_device'=> "IOS"));
			$data['sellersbc'] = $this->Admin_Model->count('sellers', array());
			$data['sellersbcon'] = $this->Admin_Model->count('sellers', array('seller_status'=> "active", 'seller_astatus'=> '1', 'seller_verified'=> '1'));
			$data['sellersbb'] = $this->Admin_Model->count('b2b', array());
			$data['sellersbbon'] = $this->Admin_Model->count('b2b', array('b2b_status'=> '1', 'b2b_astatus'=> '1', 'b2b_verified'=> '1'));
			$data['services'] = $this->Admin_Model->count('posts', array());
			$data['serviceson'] = $this->Admin_Model->count('posts', array('post_status'=> '1'));

			$data['agents'] = $this->Admin_Model->count('users', array('user_agent'=> '1'));
		
			$data['delivers'] = $this->Admin_Model->count('boys', array('boy_status'=> '1'));
			$data['deliverson'] = $this->Admin_Model->count('boys', array('boy_status'=> '1', 'boy_online'=> '1'));

			$data['plans'] = $this->Admin_Model->count('subscriptions', array());
			$data['planson'] = $this->Admin_Model->count('subscriptions', array('subsc_status'=> '1'));


			$data['banners'] = $this->Admin_Model->count('banners', array('banner_status'=> '1'));
			$data['bannersbc'] = $this->Admin_Model->count('banners', array('banner_status'=> '1', 'banner_type'=> "user"));
			$data['bannersb2'] = $this->Admin_Model->count('banners', array('banner_status'=> '1', 'banner_type'=> "b2b"));
			$data['bannersu'] = $data['bannersbc']+$data['bannersb2'];

			$data['bannerss'] = $this->Admin_Model->count('banners', array('banner_status'=> '1', 'banner_type'=> "seller"));
			$data['bannersd'] = $this->Admin_Model->count('banners', array('banner_status'=> '1', 'banner_type'=> "services"));


			$data['screens'] = $this->Admin_Model->count('screens', array('spl_status'=> '1'));
			$data['screensur'] = $this->Admin_Model->count('screens', array('spl_status'=> '1', 'spl_for'=> '1'));
			$data['screenssr'] = $this->Admin_Model->count('screens', array('spl_status'=> '1', 'spl_for'=> '2'));
			$data['screensdv'] = $this->Admin_Model->count('screens', array('spl_status'=> '1', 'spl_for'=> '3'));


			$data['coupons'] = $this->Admin_Model->count('coupons', array());
			$data['couponson'] = $this->Admin_Model->count('coupons', array('coup_status'=> '1'));


			$data['b2ccat'] = $this->Admin_Model->count('categories', array('category_status'=> '1'));
			$data['b2bcat'] = $this->Admin_Model->count('b2categories', array('b2category_status'=> '1'));
			$data['servcat'] = $this->Admin_Model->count('servcategories', array('servcat_status'=> '1'));


			$data['b2csubcat'] = $this->Admin_Model->count('subcategories', array('subcategory_status'=> '1'));
			$data['b2bsubcat'] = $this->Admin_Model->count('b2subcategories', array('b2subcategory_status'=> '1'));
			$data['servsubcat'] = $this->Admin_Model->count('servsubcategories', array('servsubcategory_status'=> '1'));


			$data['orders'] = $this->Admin_Model->count('orders', array('order_status >'=> '0'));
			$data['ordersnew'] = $this->Admin_Model->count('orders', array('order_status'=> '1'));
			$data['orderspend'] = $this->Admin_Model->count('orders', array('order_status'=> '2'));
			$data['orderscomp'] = $this->Admin_Model->count('orders', array('order_status'=> '3'));
			$data['orderscanc'] = $this->Admin_Model->count('orders', array('order_status'=> '4'));
			$data['payments'] = $this->Admin_Model->countorderpayments();
			$data['paymentsrcvd'] = $this->Admin_Model->countorderpaymentsdtl('1');
			$data['paymentspdng'] = $this->Admin_Model->countorderpaymentsdtl('2');
			$data['paymentscncl'] = $this->Admin_Model->countorderpaymentsdtl('4');
			$data['paymentscomp'] = $this->Admin_Model->countorderpaymentsdtl('3');

			$data['b2borders'] = $this->Admin_Model->count('b2orders', array('b2order_status >'=> '0'));
			$data['servviews'] = $this->Admin_Model->countservviews();
 

			$data['products'] = $this->Admin_Model->count('products', array());
			$data['productson'] = $this->Admin_Model->count('products', array('product_status'=> '1', 'product_a_status'=> '1', 'product_substatus'=> '1'));


			$data['deliverson'] = $this->Admin_Model->count('boys', array('boy_online'=> '1'));
			$data['deliversoff'] = $this->Admin_Model->count('boys', array('boy_online'=> '0'));


			$data['orders'] = $this->Admin_Model->count('orders', array('order_status >'=> '0'));
			$data['ordersnew'] = $this->Admin_Model->count('orders', array('order_status'=> '1'));
			$data['orderspend'] = $this->Admin_Model->count('orders', array('order_status'=> '2'));
			$data['orderscomp'] = $this->Admin_Model->count('orders', array('order_status'=> '3'));
			$data['orderscanc'] = $this->Admin_Model->count('orders', array('order_status'=> '4'));


			$this->load->view('admin/template/header', $data);
			$this->load->view('admin/template/nav');
			$this->load->view('admin/template/topbar');
			$this->load->view('admin/pages/dashboard');
			$this->load->view('admin/template/footer');
	}


// ***************  Categories

	public function categorys(){
	 if(!$this->uri->segment(3)){
		$data['categorys'] = $this->Admin_Model->get_categorys();
		$data['title'] = 'Categories';
		$this->load->view('admin/template/header', $data);
		$this->load->view('admin/template/nav');
		$this->load->view('admin/template/topbar');
		$this->load->view('admin/pages/categories/categories');
		$this->load->view('admin/template/footer');
	 }
	 elseif($this->uri->segment(3)==='add_category'){


		  if(!empty($_FILES['category_image']['name'])){
			$cat_img=$this->Admin_Model->fileupload('category_image', 'categories', 'ello');
		  }else{$cat_img='0';}
		  if(!empty($_FILES['category_image2']['name'])){
			$cat_img2=$this->Admin_Model->fileupload('category_image2', 'categories', 'ello');
		  }else{$cat_img2='0';}

		$data = array(
			'category_name' => strtoupper($this->input->post('category_name')),
			'category_status' => $this->input->post('category_status'),
			'category_image' => $cat_img,
			'category_image2' => $cat_img2
		);
		$insert = $this->Admin_Model->addNew($data, 'categories');
        echo json_encode(array("status" => TRUE));
	}
	elseif($this->uri->segment(3)==='edit_category'){
		if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
		else{
			$category_id=$this->uri->segment(4);
			$data = $this->Admin_Model->get_categorys($category_id);
			echo json_encode($data);
		}
	}
	elseif($this->uri->segment(3)==='update_category'){



		if(!empty($_FILES['category_image']['name'])){
			$data['category_image']=$this->Admin_Model->fileupload('category_image', 'categories', 'ello');
		  }
		  if(!empty($_FILES['category_image2']['name'])){
			$data['category_image2']=$this->Admin_Model->fileupload('category_image2', 'categories', 'ello');
		  }


		$data['category_name'] = strtoupper($this->input->post('category_name'));
		$data['category_status'] = $this->input->post('category_status');
		$this->Admin_Model->update($data, 'categories', array('category_id' => $this->input->post('category_id')));
        echo json_encode(array("status" => TRUE));
	}
	else{show_404();}
	}

// ***************  END Categories





// *************** B2B  Categories

public function b2bcategorys(){
	if(!$this->uri->segment(3)){
	   $data['categorys'] = $this->Admin_Model->get_b2bcategorys();
	   $data['title'] = 'B2B Categories';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/categories/categoriesb2');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_category'){


		   if(!empty($_FILES['category_image']['name'])){
			$cat_img=$this->Admin_Model->fileupload('category_image', 'b2bcategories', 'ello');
		  }else{$cat_img='0';}
		  if(!empty($_FILES['category_image2']['name'])){
			$cat_img2=$this->Admin_Model->fileupload('category_image2', 'b2bcategories', 'ello');
		  }else{$cat_img2='0';}


	   $data = array(
		   'b2category_name' => strtoupper($this->input->post('category_name')),
		   'b2category_status' => $this->input->post('category_status'),
		   'b2category_image' => $cat_img,
		   'b2category_image2' => $cat_img2

	   );
	   $insert = $this->Admin_Model->addNew($data, 'b2categories');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_category'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $category_id=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_b2bcategorys($category_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_category'){

		if(!empty($_FILES['category_image']['name'])){
			$data['b2category_image']=$this->Admin_Model->fileupload('category_image', 'b2bcategories', 'ello');
		  }
		  if(!empty($_FILES['category_image2']['name'])){
			$data['b2category_image2']=$this->Admin_Model->fileupload('category_image2', 'b2bcategories', 'ello');
		  }

	   $data['b2category_name'] = strtoupper($this->input->post('category_name'));
	   $data['b2category_status'] = $this->input->post('category_status');

	   $this->Admin_Model->update($data, 'b2categories', array('b2category_id' => $this->input->post('category_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }

// ***************  END B2bCategories




// ***************Servc  Categories

public function servcategorys(){
	if(!$this->uri->segment(3)){
	   $data['categorys'] = $this->Admin_Model->get_servcategorys();
	   $data['title'] = 'Services Categories';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/categories/categoriesserv');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_category'){


		 if(!empty($_FILES['category_image']['name'])){
			$cat_img=$this->Admin_Model->fileupload('category_image', 'servcategories', 'ello');
		  }else{$cat_img='0';}

	   $data = array(
		   'servcat_name' => strtoupper($this->input->post('category_name')),
		   'servcat_status' => $this->input->post('category_status'),
		   'servcat_image' => $cat_img,

	   );
	   $insert = $this->Admin_Model->addNew($data, 'servcategories');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_category'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $category_id=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_servcategorys($category_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_category'){

	   if(!empty($_FILES['category_image']['name'])){
		$data['servcat_image']=$this->Admin_Model->fileupload('category_image', 'servcategories', 'ello');
	  }


	   $data['servcat_name'] = strtoupper($this->input->post('category_name'));
	   $data['servcat_status'] = $this->input->post('category_status');

	   $this->Admin_Model->update($data, 'servcategories', array('servcat_id' => $this->input->post('category_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }

// ***************  END B2bCategories





// ***************  Sub-Categories

public function subcategorys(){
	$catID = $this->uri->segment(4);
	$catName = $this->Admin_Model->get_field('category_name', 'categories', array('category_id' => $catID));
   if($this->uri->segment(3)==='all'){
	  $data['subcategorys'] = $this->Admin_Model->get_subcategorys($catID);
	  $data['title'] = 'Sub-Categories - '.$catName;
	  $data['catID'] = $catID;
	  $this->load->view('admin/template/header', $data);
	  $this->load->view('admin/template/nav');
	  $this->load->view('admin/template/topbar');
	  $this->load->view('admin/pages/categories/subcategories');
	  $this->load->view('admin/template/footer');
   }
   elseif($this->uri->segment(3)==='add_subcategory'){

		if(!empty($_FILES['category_image2']['name'])){
			$subcategoryImage=$this->Admin_Model->fileupload('subcategoryImage', 'subcategories', 'ello');
		  }else{$subcategoryImage='0';}


	  $data = array(
		  'subcategory_name' => strtoupper($this->input->post('subcategoryName')),
		  'subcategory_main' => $catID,
		  'subcategory_status' => $this->input->post('subcategoryStatus'),
		  'subcategory_image' => $subcategoryImage
	  );
	  $insert = $this->Admin_Model->addNew($data, 'subcategories');
	  echo json_encode(array("status" => TRUE));
  }
  elseif($this->uri->segment(3)==='edit_subcategory'){
   if(!$this->uri->segment(5)){echo json_encode(array("status" => FALSE));}
	   else{
		   $subCatID=$this->uri->segment(5);
		   $data = $this->Admin_Model->get_subcategorys($catID, $subCatID);
		   echo json_encode($data);
	   }
  }
  elseif($this->uri->segment(3)==='update_subcategory'){


		if(!empty($_FILES['subcategoryImage']['name'])){
			$data['subcategory_image']=$this->Admin_Model->fileupload('subcategoryImage', 'subcategories', 'ello');
		  }


		  $data['subcategory_name'] = strtoupper($this->input->post('subcategoryName'));
		  $data['subcategory_status'] = strtoupper($this->input->post('subcategoryStatus'));

	  $this->Admin_Model->update($data, 'subcategories', array('subcategory_id' => $this->input->post('subcategoryID')));
	  echo json_encode(array("status" => TRUE));
  }
  else{show_404();}
  }





  
// ***************  Sub-Categories

public function b2bsubcategorys(){
	$catID = $this->uri->segment(4);
	$catName = $this->Admin_Model->get_field('b2category_name', 'b2categories', array('b2category_id' => $catID));
   if($this->uri->segment(3)==='all'){
	  $data['subcategorys'] = $this->Admin_Model->get_b2bsubcategorys($catID);
	  $data['title'] = 'B2B Sub-Categories - '.$catName;
	  $data['catID'] = $catID;
	  $this->load->view('admin/template/header', $data);
	  $this->load->view('admin/template/nav');
	  $this->load->view('admin/template/topbar');
	  $this->load->view('admin/pages/categories/subcategoriesb2');
	  $this->load->view('admin/template/footer');
   }
   elseif($this->uri->segment(3)==='add_subcategory'){
	   
	  $data = array(
		  'b2subcategory_name' => strtoupper($this->input->post('subcategoryName')),
		  'b2subcategory_main' => $catID,
		  'b2subcategory_status' => $this->input->post('subcategoryStatus'),
		  'b2subcategory_image' => '0'
	  );
	  $insert = $this->Admin_Model->addNew($data, 'b2subcategories');
	  echo json_encode(array("status" => TRUE));
  }
  elseif($this->uri->segment(3)==='edit_subcategory'){
   if(!$this->uri->segment(5)){echo json_encode(array("status" => FALSE));}
	   else{
		   $subCatID=$this->uri->segment(5);
		   $data = $this->Admin_Model->get_b2bsubcategorys($catID, $subCatID);
		   echo json_encode($data);
	   }
  }
  elseif($this->uri->segment(3)==='update_subcategory'){
	

		  $data['b2subcategory_name'] = strtoupper($this->input->post('subcategoryName'));
		  $data['b2subcategory_status'] = strtoupper($this->input->post('subcategoryStatus'));

	  $this->Admin_Model->update($data, 'b2subcategories', array('b2subcategory_id ' => $this->input->post('subcategoryID')));
	  echo json_encode(array("status" => TRUE));
  }
  else{show_404();}
  }





  
// ***************  Sub-Categories

public function servsubcategorys(){
	$catID = $this->uri->segment(4);
	$catName = $this->Admin_Model->get_field('servcat_name', 'servcategories', array('servcat_id' => $catID));
   if($this->uri->segment(3)==='all'){
	  $data['subcategorys'] = $this->Admin_Model->get_servsubcategorys($catID);
	  $data['title'] = 'Service Sub-Categories - '.$catName;
	  $data['catID'] = $catID;
	  $this->load->view('admin/template/header', $data);
	  $this->load->view('admin/template/nav');
	  $this->load->view('admin/template/topbar');
	  $this->load->view('admin/pages/categories/subcategoriesserv');
	  $this->load->view('admin/template/footer');
   }
   elseif($this->uri->segment(3)==='add_subcategory'){



	  if(!empty($_FILES['subcategoryimage']['name'])){
		$subcategoryImage=$this->Admin_Model->fileupload('subcategoryimage', 'servsubcategories', 'ello');
	  }else{$subcategoryImage='0';}


	  $data = array(
		  'servsubcategory_name' => strtoupper($this->input->post('subcategoryName')),
		  'servsubcategory_main' => $catID,
		  'servsubcategory_status' => $this->input->post('subcategoryStatus'),
		  'servsubcategory_image' => $subcategoryImage
	  );
	  $insert = $this->Admin_Model->addNew($data, 'servsubcategories');
	  echo json_encode(array("status" => TRUE));
  }
  elseif($this->uri->segment(3)==='edit_subcategory'){
   if(!$this->uri->segment(5)){echo json_encode(array("status" => FALSE));}
	   else{
		   $subCatID=$this->uri->segment(5);
		   $data = $this->Admin_Model->get_servsubcategorys($catID, $subCatID);
		   echo json_encode($data);
	   }
  }
  elseif($this->uri->segment(3)==='update_subcategory'){
	

	  if(!empty($_FILES['subcategoryimage']['name'])){
		$data['servsubcategory_image']=$this->Admin_Model->fileupload('subcategoryimage', 'servsubcategories', 'ello');
	  }

		  $data['servsubcategory_name'] = strtoupper($this->input->post('subcategoryName'));
		  $data['servsubcategory_status'] = strtoupper($this->input->post('subcategoryStatus'));

	  $this->Admin_Model->update($data, 'servsubcategories', array('servsubcategory_id ' => $this->input->post('subcategoryID')));
	  echo json_encode(array("status" => TRUE));
  }
  else{show_404();}
  }





// ***************  Measures

public function measures(){

	if(!$this->uri->segment(3)){
	   $data['measures'] = $this->Admin_Model->get_measures();
	   $data['title'] = 'Measures';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/categories/measures');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_measure'){
	   $data = array(
		   'measure_name' => strtoupper($this->input->post('measure_name')),
		   'measure_status' => $this->input->post('measure_status'),
	   );
	   $insert = $this->Admin_Model->addNew($data, 'measures');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_measure'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $measure_id=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_measures($measure_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_measure'){
		   $data = array(
			   'measure_name' => strtoupper($this->input->post('measure_name')),
			   'measure_status' => $this->input->post('measure_status')
		   );
	   $this->Admin_Model->update($data, 'measures', array('measure_id' => $this->input->post('measure_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }




// ***************  END Measures




// ***************  Brands

public function brands(){

	if(!$this->uri->segment(3)){
	   $data['brands'] = $this->Admin_Model->get_brands();
	   $data['title'] = 'Brands';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/brands/brands');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_brand'){
	   $this->brand_validate();
	   $data = array(
		   'brandName' => strtoupper($this->input->post('brandName')),
		   'brandPriority' => $this->input->post('brandPriority'),
		   'brandStatus' => $this->input->post('brandStatus'),
		   'brandImage' => '0'
	   );
	   $insert = $this->Admin_Model->addNew($data, 'brands');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_brand'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $brandID=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_brands($brandID);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_brand'){
		   $data = array(
			   'brandName' => strtoupper($this->input->post('brandName')),
			   'brandPriority' => $this->input->post('brandPriority'),
			   'brandStatus' => $this->input->post('brandStatus')
		   );
	   $this->Admin_Model->update($data, 'brands', array('brandID' => $this->input->post('brandID')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }


   private function brand_validate()
{
   $data = array();
   $data['error_string'] = array();
   $data['inputerror'] = array();
   $data['status'] = TRUE;
   if($this->input->post('brandName') == '')
   {
	   $data['inputerror'][] = 'brandName';
	   $data['error_string'][] = '*Name is required';
	   $data['status'] = FALSE;
   }
   if($this->input->post('brandStatus') == '')
   {
	   $data['inputerror'][] = 'brandStatus';
	   $data['error_string'][] = 'Please select status';
	   $data['status'] = FALSE;
   }
   if($data['status'] === FALSE)
   {
	   echo json_encode($data);
	   exit();
   }
}


// ***************  END Brands
// *************** sellers 
public function sellers(){
	if(!$this->uri->segment(3)){
	   $data['sellers'] = $this->Admin_Model->get_sellers();
	   $data['title'] = 'sellers';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/sellers/sellers');
	   $this->load->view('admin/template/footer');
	}

	elseif($this->uri->segment(3)==='view_seller'){
		if(!$this->uri->segment(4)){ redirect(base_url('admin/sellers')); }
		else{
			$seller_id = $this->uri->segment(4);
			$data['seller'] = $this->Admin_Model->get_sellers($seller_id);
			$data['s_products'] = $this->Admin_Model->get_seller_products($seller_id);
			$data['s_subcats'] = $this->Admin_Model->get_seller_subCategories($seller_id);
			$data['title'] = 'seller';
			$this->load->view('admin/template/header', $data);
			$this->load->view('admin/template/nav');
			$this->load->view('admin/template/topbar');
			$this->load->view('admin/pages/sellers/viewseller');
			$this->load->view('admin/template/footer');
		}
	}

    elseif($this->uri->segment(3)==='edit_seller'){
	   if(!$this->uri->segment(4)){ redirect(base_url('admin/sellers')); }
	   else{
		   $seller_id = $this->uri->segment(4);
		   $data['seller'] = $this->Admin_Model->get_sellers($seller_id);
		   $data['s_products'] = $this->Admin_Model->get_seller_products($seller_id);
		   $data['title'] = 'seller';
		   $this->load->view('admin/template/header', $data);
		   $this->load->view('admin/template/nav');
		   $this->load->view('admin/template/topbar');
		   $this->load->view('admin/pages/sellers/editseller');
		   $this->load->view('admin/template/footer');
	   }
   }
   elseif($this->uri->segment(3)==='disable_product'){
	if(!$this->uri->segment(4)){ redirect(base_url('admin/sellers')); }
	if(!$this->uri->segment(5)){ redirect(base_url('admin/sellers')); }
	else{
		$seller_id = $this->uri->segment(5);
		$product_id = $this->uri->segment(4);
		$paStatus=$this->Admin_Model->get_field('product_a_status', 'products', array('product_id' => $product_id, 'product_store' => $seller_id));

		if($paStatus === '1'){ $data = array('product_a_status' => '0');}
		else{ $data = array('product_a_status' => '1'); }
		
		$updated = $this->Admin_Model->update($data, 'products', array('product_id' => $product_id));
		if($updated) {
		 $this->session->set_flashdata('message', 'Updated Successfully!');
		 redirect(base_url('admin/sellers/edit_seller/'.$seller_id.''));
		 }
		 else{
			$this->session->set_flashdata('message', 'Something Wrong!');
			redirect(base_url('admin/sellers/edit_seller/'.$seller_id.''));
			}
	}
}
   elseif($this->uri->segment(3)==='update_seller'){


	$sphn = $this->input->post('seller_phone',TRUE);


	if(!empty($_FILES['seller_imagenew']['name'])){
		$ssimg=$this->Admin_Model->fileupload('seller_imagenew', 'sellers', $sphn);
	}else{
		$ssimg=$this->input->post('seller_imageold',TRUE);
	}


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
			   'seller_licence_number'=>$this->input->post('seller_licence_number',TRUE),
			   'seller_commission'=>$this->input->post('seller_commission',TRUE),
			   'seller_minimum_order'=>$this->input->post('seller_minimum_order',TRUE),
			   'seller_store_time_open'=>$this->input->post('seller_store_open',TRUE),
			   'seller_store_time_close'=>$this->input->post('seller_store_close',TRUE),
			   'seller_verified'=>$this->input->post('seller_verified',TRUE),
			   'seller_status'=>$this->input->post('seller_status',TRUE),
			   'seller_astatus'=>$this->input->post('seller_astatus',TRUE),
			   'seller_time'=>$this->input->post('seller_time',TRUE),
			   'seller_store_image'=>$ssimg
		   );


	   $updated = $this->Admin_Model->update($data, 'sellers', array('seller_id' => $this->input->post('seller_id')));
	   if($updated) {
		$this->session->set_flashdata('message', 'Updated Successfully!');
		redirect(base_url('admin/sellers/edit_seller/'.$this->input->post('seller_id').''));
		}
		redirect(base_url('admin/sellers/edit_seller/'.$this->input->post('seller_id').''));

   }

   elseif($this->uri->segment(3)==='online'){
	$id = $this->uri->segment(4);
	$stat = $this->uri->segment(5);
	$updated = $this->Admin_Model->update(array('seller_ostatus' => $stat), 'sellers', array('seller_id' => $id));
	redirect(base_url('admin/sellers'));
}
elseif($this->uri->segment(3)==='subonline'){
	$id = $this->uri->segment(4);
	$subid = $this->uri->segment(5);
	$stat = $this->uri->segment(6);
	$updated = $this->Admin_Model->update(array('product_substatus' => $stat), 'products', array('product_store' => $id, 'product_subcategory' => $subid));
	redirect(base_url('admin/sellers/view_seller/'.$id.''));
}



   else{show_404();}
   }
// ***************  END sellers




public function frsellers(){
	if(!$this->uri->segment(3)){
		$data['sellers'] = $this->Admin_Model->get_frsellers();
		$data['title'] = 'Franchise Sellers';
		$this->load->view('admin/template/header', $data);
		$this->load->view('admin/template/nav');
		$this->load->view('admin/template/topbar');
		$this->load->view('admin/pages/sellers/frsellers');
		$this->load->view('admin/template/footer');
	 }
}


/*
| -------------------------------------------------------------------
|  Banners
| -------------------------------------------------------------------
*/
public function banners(){

	if(!$this->uri->segment(3)){
	   $data['banners'] = $this->Admin_Model->get_banners();
	   $data['title'] = 'Banners';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/banners/banners');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_banner'){

		 if(!empty($_FILES['banner_image']['name'])){
			$banner_image=$this->Admin_Model->fileupload('banner_image', 'banners', 'ello');
		  }else{$banner_image='0';}


	   $data = array(
		   'banner_name' => strtoupper($this->input->post('banner_name')),
		   'banner_status' => $this->input->post('banner_status'),
		   'banner_type' => $this->input->post('banner_type'),
		   'banner_image' => $banner_image
	   );
	   $insert = $this->Admin_Model->addNew($data, 'banners');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_banner'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $banner_id=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_banners($banner_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_banner'){

	 if(!empty($_FILES['banner_image']['name'])){
		$data['banner_image']=$this->Admin_Model->fileupload('banner_image', 'banners', 'ello');
	  }

	   $data['banner_name'] = strtoupper($this->input->post('banner_name'));
	   $data['banner_status'] = strtoupper($this->input->post('banner_status'));
	   $data['banner_type'] = $this->input->post('banner_type');
	   $this->Admin_Model->update($data, 'banners', array('banner_id' => $this->input->post('banner_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }
// ***************  END Banners




/*
| -------------------------------------------------------------------
|  Tutors
| -------------------------------------------------------------------
*/
public function tutors(){

	if(!$this->uri->segment(3)){
	   $data['banners'] = $this->Admin_Model->get_tutors();
	   $data['title'] = 'Totorials';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/tutors/tutors');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_tutor'){

		 if(!empty($_FILES['spl_image']['name'])){
			$spl_image=$this->Admin_Model->fileupload('spl_image', 'screens', 'ello');
		  }else{$spl_image='0';}


	   $data = array(
		   'spl_name' => strtoupper($this->input->post('spl_name')),
		   'spl_status' => $this->input->post('spl_status'),
		   'spl_priority' => $this->input->post('spl_priority'),
		   'spl_for' => $this->input->post('spl_for'),
		   'spl_image' => $spl_image
	   );
	   $insert = $this->Admin_Model->addNew($data, 'screens');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_tutor'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $spl_id=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_tutors($spl_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_tutor'){

	 if(!empty($_FILES['spl_image']['name'])){
		$data['spl_image']=$this->Admin_Model->fileupload('spl_image', 'screens', 'ello');
	  }

	   $data['spl_name'] = strtoupper($this->input->post('spl_name'));
	   $data['spl_status'] = $this->input->post('spl_status');
	   $data['spl_priority'] = $this->input->post('spl_priority');
	   $data['spl_for'] = $this->input->post('spl_for');
	   $this->Admin_Model->update($data, 'screens', array('spl_id' => $this->input->post('spl_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }
// ***************  END Tutors




/*
| -------------------------------------------------------------------
|  Pre Products
| -------------------------------------------------------------------
*/
public function products(){

	if(!$this->uri->segment(3)){
	   $data['products'] = $this->Admin_Model->get_productspre();
	   $data['title'] = 'Pre Products';

	   $data['cats'] = $this->Admin_Model->get_cats();
	   $data['subcats'] = $this->Admin_Model->get_subcats();
	   $data['meas'] = $this->Admin_Model->get_measures();

	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/productspre/products');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_product'){

		if(!empty($_FILES['pd_img1']['name'])){
			$pd_img1=$this->Admin_Model->fileupload('pd_img1', 'products', 'ad');
		}else{$pd_img1='0';}
		if(!empty($_FILES['pd_img2']['name'])){
			$pd_img2=$this->Admin_Model->fileupload('pd_img2', 'products', 'ad');
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
	   $insert = $this->Admin_Model->addNew($data, 'productspre');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_product'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $pd_id =$this->uri->segment(4);
		   $data = $this->Admin_Model->get_productpre($pd_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_product'){


	if(!empty($_FILES['pd_img1']['name'])){
		$data['pd_img1']=$this->Admin_Model->fileupload('pd_img1', 'products', 'ad');
	}
	if(!empty($_FILES['pd_img2']['name'])){
		$data['pd_img2']=$this->Admin_Model->fileupload('pd_img2', 'products', 'ad');
	}
	   $data['pd_name'] = $this->input->post('pd_name');
	   $data['pd_category'] = $this->input->post('pd_category');
	   $data['pd_subcategory'] = $this->input->post('pd_subcategory');
	   $data['pd_measure'] = $this->input->post('pd_measure');
	   $data['pd_description'] = $this->input->post('pd_description');
	   $data['pd_status'] = $this->input->post('pd_status');
	   $data['pd_description'] = $this->input->post('pd_description');

	   $this->Admin_Model->update($data, 'productspre', array('pd_id' => $this->input->post('pd_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }
// ***************  Pre Products






/*
| -------------------------------------------------------------------
|  Plans
| -------------------------------------------------------------------
*/
public function plans(){

	if(!$this->uri->segment(3)){
	   $data['plans'] = $this->Admin_Model->get_plans();
	   $data['title'] = 'Plans';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/plans/plans');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_plan'){



		
	  
	   $data = array(
		   'subsc_name' => strtoupper($this->input->post('subsc_name')),
		   'subsc_amount' => $this->input->post('subsc_amount'),
		   'subsc_tax' => $this->input->post('subsc_tax'),
		   'subsc_percent' => $this->input->post('subsc_percent'),
		   'subsc_validity' => $this->input->post('subsc_validity'),
		   'subsc_description' => $this->input->post('subsc_description'),
		   'subsc_for' => $this->input->post('subsc_for'),
		   'subsc_status' => $this->input->post('subsc_status'),
	   );
	   $insert = $this->Admin_Model->addNew($data, 'subscriptions');
	   echo json_encode(array("status" => TRUE));
   }
   elseif($this->uri->segment(3)==='edit_plan'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $subsc_id=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_plans($subsc_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_plan'){
   
	   $data['subsc_name'] = $this->input->post('subsc_name');
	   $data['subsc_amount'] = $this->input->post('subsc_amount');
	   $data['subsc_tax'] = $this->input->post('subsc_tax');
	   $data['subsc_percent'] = $this->input->post('subsc_percent');
	   $data['subsc_validity'] = $this->input->post('subsc_validity');
	   $data['subsc_description'] = $this->input->post('subsc_description');
	   $data['subsc_for'] = $this->input->post('subsc_for');
	   $data['subsc_status'] = $this->input->post('subsc_status');
	   $this->Admin_Model->update($data, 'subscriptions', array('subsc_id' => $this->input->post('subsc_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }
// ***************  END Plans







/*
| -------------------------------------------------------------------
|  Franchise
| -------------------------------------------------------------------
*/
public function franchise(){

	if(!$this->uri->segment(3)){
	   $data['franchises'] = $this->Admin_Model->get_franchises();
	   $data['title'] = 'Franchise';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/franchise/franchise');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_franchise'){
	
	$unique = $this->input->post('frnc_phone');

	if(!empty($_FILES['frnc_adr_image']['name'])){
		$a_image=$this->Admin_Model->fileupload('frnc_adr_image', 'franchises', $unique);
	}else{$a_image='0';}
	if(!empty($_FILES['frnc_pan_image']['name'])){
		$p_image=$this->Admin_Model->fileupload('frnc_pan_image', 'franchises', $unique);
	}else{$p_image='0';}

	   $data = array(
		   'frn_name' => strtoupper($this->input->post('frnc_name')),
		   'frn_phone_code' => $this->input->post('frnc_phone_code'),
		   'frn_phone' => $this->input->post('frnc_phone'),
		   'frn_email' => $this->input->post('frnc_email'),
		   'frn_password' => md5($this->input->post('frnc_password')),
		   'frn_address' => $this->input->post('frnc_address'),
		   'frn_state' => $this->input->post('frnc_state'),
		   'frn_city' => $this->input->post('frnc_city'),
		   'frn_pincode' => $this->input->post('frnc_pincode'),
		   'frn_lat' => $this->input->post('frnc_lat'),
		   'frn_long' => $this->input->post('frnc_long'),
		   'frn_aadhaar' => $this->input->post('frnc_aadhaar'),
		   'frn_pan' => $this->input->post('frnc_pan'),
		   'frn_aadhaar_img' => $a_image,
		   'frn_pan_img' => $p_image,
		   'frn_pincodes' => $this->input->post('frnc_pincodes'),
		   'frn_wallet' => '0',
		   'frn_reg_date' => date("Y-m-d"),
		   'frn_status' => $this->input->post('frnc_status'),
	   );
	   $insert = $this->Admin_Model->addNew($data, 'franchises');
	   echo json_encode(array("status" => TRUE));
   }

   elseif($this->uri->segment(3)==='edit_franchise'){
	   if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
	   else{
		   $fr_id=$this->uri->segment(4);
		   $data = $this->Admin_Model->get_franchises($fr_id);
		   echo json_encode($data);
	   }
   }
   elseif($this->uri->segment(3)==='update_franchise'){

if($this->input->post('frnc_password') != ""){
	$data['frn_password'] = md5($this->input->post('frnc_password'));
}
	   $data['frn_name'] = strtoupper($this->input->post('frnc_name'));
	   $data['frn_phone_code'] = $this->input->post('frnc_phone_code');
	   $data['frn_phone'] = $this->input->post('frnc_phone');
	   $data['frn_email'] = $this->input->post('frnc_email');
	   $data['frn_address'] = $this->input->post('frnc_address');
	   $data['frn_state'] = $this->input->post('frnc_state');
	   $data['frn_city'] = $this->input->post('frnc_city');
	   $data['frn_pincode'] = $this->input->post('frnc_pincode');
	   $data['frn_lat'] = $this->input->post('frnc_lat');
	   $data['frn_long'] = $this->input->post('frnc_long');
	   $data['frn_aadhaar'] = $this->input->post('frnc_aadhaar');
	   $data['frn_pan'] = $this->input->post('frnc_pan');
	   $data['frn_pincodes'] = $this->input->post('frnc_pincodes');
	   $data['frn_status'] = $this->input->post('frnc_status');
	   $this->Admin_Model->update($data, 'franchises', array('frn_id' => $this->input->post('frnc_id')));
	   echo json_encode(array("status" => TRUE));
   }
   else{show_404();}
   }
// ***************  END Franchise






public function addseller(){
	$data['title'] = 'Seller Form';
	$data['subscriptions'] = $this->Admin_Model->get_subscriptions();
	$data['francs'] = $this->Admin_Model->get_active_franchises();
	$this->load->view('admin/template/header', $data);
	$this->load->view('admin/template/nav');
	$this->load->view('admin/template/topbar');
	$this->load->view('admin/pages/sellers/addseller');
	 $this->load->view('admin/template/footer');
}




public function add_seller(){

	$check = $this->Admin_Model->checkSellerPhn($this->input->post('seller_phone',TRUE));

	if($check){
	$unique = $this->input->post('seller_phone',TRUE);
	if(!empty($_FILES['store_image']['name'])){
		$s_image=$this->Admin_Model->fileupload('store_image', 'sellers', $unique);
	}else{$s_image='0';}
	if(!empty($_FILES['pan_image']['name'])){
		$p_image=$this->Admin_Model->fileupload('pan_image', 'sellers', $unique);
	}else{$p_image='0';}
	if(!empty($_FILES['seller_payment_img']['name'])){
		$pay_image=$this->Admin_Model->fileupload('seller_payment_img', 'sellers', $unique);
	}else{$pay_image='0';}
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
		'seller_frnc'=>$this->input->post('seller_frnc',TRUE),
		'seller_commission'=>$this->input->post('seller_commission',TRUE),
		'seller_store_image'=>$s_image,
		'seller_pan_image'=>$p_image,
		'seller_payment_img'=>$pay_image,
		'seller_payment_id'=>$this->input->post('seller_payment_id',TRUE),
		'seller_astatus'=>$this->input->post('seller_astatus',TRUE),
		'seller_time'=>$this->input->post('seller_time',TRUE),
		'seller_status'=>$this->input->post('seller_status',TRUE),
	);

	$added = $this->Admin_Model->addNew($data, 'sellers');
	if($added) {

		$plan = $this->input->post('seller_plan',TRUE);
		$plandtl = $this->Admin_Model->get_row('subscriptions', array('subsc_id' => $plan));
		$subsc_amount = $plandtl['subsc_amount'];
		$subsc_validity = $plandtl['subsc_validity'];
		$vDate = date('Y-m-d', strtotime("+$subsc_validity days"));  
		$data2 = array(
			'pay_susbc' => $plan,
			'pay_payer' => $added,
			'pay_ref' => 'fr'.$this->input->post('seller_frnc',TRUE),
			'pay_date' => date("Y-m-d"),
			'pay_time' => date("H:i:s"),
			'pay_validity' => $vDate,
			'pay_amount' => $subsc_amount,
			'pay_for' => 'seller',
			'pay_type' => '1',
			'pay_status' => '1'
		);
		$added2 = $this->Admin_Model->addNew($data2, 'payments');
		$this->load->model('Sms_model');
		$message='Your '.$this->input->post('seller_store_name',TRUE).' is Added/Updated at ELLOCART Seller';
		$sent=$this->Sms_model->sendSMS($this->input->post('seller_phone',TRUE), $message);
		$this->session->set_flashdata('message', 'Added Successfully!');
		redirect(base_url('admin/sellers/'));
}

}else{
	$this->session->set_flashdata('message', 'Mobile Number already exists!');
	redirect(base_url('admin/addseller'));
}

}




public function product(){
	$seller_id = $this->uri->segment(3);
   $product_id = $this->uri->segment(4);
   $data['product'] = $this->Admin_Model->get_product($product_id);
   $data['cats'] = $this->Admin_Model->get_cats();
   $data['subcats'] = $this->Admin_Model->get_subcats();
   $data['meas'] = $this->Admin_Model->get_measures();
   $hasvar = $data['product']['product_var'];
   $who = $data['product']['product_b2'];
   if($who === '1'){
   $data['seller'] = $this->Admin_Model->get_b2sellers($seller_id);
	}else{
		$data['seller'] = $this->Admin_Model->get_sellers($seller_id);
	}

if($hasvar == '1'){
$data['vars'] = $this->Admin_Model->get_pvars($product_id);
}else{
$data['vars'] = [];
}
   $data['title'] = "Product";
   $this->load->view('admin/template/header', $data);
   $this->load->view('admin/template/nav');
   $this->load->view('admin/template/topbar');
   $this->load->view('admin/pages/products/product');
   $this->load->view('admin/template/footer');
}

public function addproduct(){
	if(!$this->uri->segment(4)){
	   $seller_id = $this->uri->segment(3);
	   $data['seller'] = $this->Admin_Model->get_sellers($seller_id);
	   $data['cats'] = $this->Admin_Model->get_cats();
	   $data['subcats'] = $this->Admin_Model->get_subcats();
	   $data['meas'] = $this->Admin_Model->get_measures();
	   $data['title'] = $data['seller']['seller_store_name'];
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/products/addproduct');
	   $this->load->view('admin/template/footer');
	}
   else{show_404();}
}



public function addp(){




	$seller = $this->input->post('product_store');
	$data['product_store'] = $seller;
	$data['product_name'] = $this->input->post('product_name');
	$data['product_category'] = $this->input->post('product_category');
	$data['product_subcategory'] = $this->input->post('product_subcategory');
	$data['product_measure'] = $this->input->post('product_measure');
	$data['product_description'] = $this->input->post('product_description');



	$stat = $this->Admin_Model->get_field('product_substatus', 'products', array('product_subcategory' => $_POST['product_subcategory'], 'product_store' => $_POST['id']));
	if($stat === '' || $stat === null){ $substat = '0'; }else{ $substat = $stat; }
	$data['product_substatus'] = $substat;


	$data['product_a_status'] = '1';
	$hasvar = $this->input->post('product_var');
	if($hasvar == "0"){
		if(!empty($_FILES['product_img1']['name'])){
			$data['product_img1']=$this->Admin_Model->fileupload('product_img1', 'products', $data['product_store']);
		}else{$data['product_img1']='0';}
		// if(!empty($_FILES['product_img2']['name'])){
		// 	$data['product_img2']=$this->Admin_Model->fileupload('product_img2', 'products', $data['product_store']);
		// }else{$data['product_img2']='0';}
		// if(!empty($_FILES['product_img3']['name'])){
		// 	$data['product_img3']=$this->Admin_Model->fileupload('product_img3', 'products', $data['product_store']);
		// }else{$data['product_img3']='0';}
		// if(!empty($_FILES['product_img4']['name'])){
		// 	$data['product_img4']=$this->Admin_Model->fileupload('product_img4', 'products', $data['product_store']);
		// }else{$data['product_img4']='0';}
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

	$added = $this->Admin_Model->addNew($data, 'products');
	if($added) {
		$this->session->set_flashdata('message', 'Added Successfully!');
		redirect(base_url('admin/product/'.$seller.'/'.$added));
	}

   }



   
   public function updatep(){

	$seller = $this->input->post('product_store');
	$product_id = $this->input->post('product_id');
	$data['product_name'] = $this->input->post('product_name');
	$data['product_description'] = $this->input->post('product_description');
	$data['product_status'] = $this->input->post('product_status');
	$data['product_category'] = $this->input->post('product_category1');
	$data['product_subcategory'] = $this->input->post('product_subcategory1');
	$data['product_measure'] = $this->input->post('product_measure1');

	$hasvar = $this->input->post('product_var');
	if($hasvar == "0"){
		if($_FILES['product_img1']['size'] != 0){
			$data['product_img1']=$this->Admin_Model->fileupload('product_img1', 'products', $data['product_store']);
		}
		if($_FILES['product_img2']['size'] != 0){
			$data['product_img2']=$this->Admin_Model->fileupload('product_img2', 'products', $data['product_store']);
		}
		if($_FILES['product_img3']['size'] != 0){
			$data['product_img3']=$this->Admin_Model->fileupload('product_img3', 'products', $data['product_store']);
		}
		if($_FILES['product_img4']['size'] != 0){
			$data['product_img4']=$this->Admin_Model->fileupload('product_img4', 'products', $data['product_store']);
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
		$update = $this->Admin_Model->update($data, 'products', array('product_id' => $product_id));
		$this->session->set_flashdata('message', 'Updated Successfully!');
		redirect(base_url('admin/product/'.$seller.'/'.$product_id));
   }

   public function edit_var(){
	if(!$this->uri->segment(3)){echo json_encode(array("status" => FALSE));}
	else{
		$var_id=$this->uri->segment(3);
		$data = $this->Admin_Model->get_var($var_id);
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
		$data['vproduct_img1']=$this->Admin_Model->fileupload('vproduct_img1', 'products', $seller);
		}else{$data['vproduct_img1']='0';}
		if($_FILES['vproduct_img2']['size'] != 0){
			$data['vproduct_img2']=$this->Admin_Model->fileupload('vproduct_img2', 'products', $seller);
		}else{$data['vproduct_img2']='0';}
		if($_FILES['vproduct_img3']['size'] != 0){
			$data['vproduct_img3']=$this->Admin_Model->fileupload('vproduct_img3', 'products', $seller);
		}else{$data['vproduct_img3']='0';}
		if($_FILES['vproduct_img4']['size'] != 0){
			$data['vproduct_img4']=$this->Admin_Model->fileupload('vproduct_img4', 'products', $seller);
		}else{$data['vproduct_img4']='0';}

	$added = $this->Admin_Model->addNew($data, 'productsvar');
	echo json_encode(array("status" => TRUE));
   }


   
   public function update_var(){
	$seller = $this->input->post('seller_id');
	$product = $this->input->post('product_id');
	$varid = $this->input->post('var_id');

	$data['vproduct_p1'] = $this->input->post('vproduct_p1');
	$data['vproduct_status'] = $this->input->post('vproduct_status');
	if($_FILES['vproduct_img1']['size'] != 0){
		$data['vproduct_img1']=$this->Admin_Model->fileupload('vproduct_img1', 'products', $seller);
		}
		if($_FILES['vproduct_img2']['size'] != 0){
			$data['vproduct_img2']=$this->Admin_Model->fileupload('vproduct_img2', 'products', $seller);
		}
		if($_FILES['vproduct_img3']['size'] != 0){
			$data['vproduct_img3']=$this->Admin_Model->fileupload('vproduct_img3', 'products', $seller);
		}
		if($_FILES['vproduct_img4']['size'] != 0){
			$data['vproduct_img4']=$this->Admin_Model->fileupload('vproduct_img4', 'products', $seller);
		}
		$update = $this->Admin_Model->update($data, 'productsvar', array('vproduct_id' => $varid));
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
	$added = $this->Admin_Model->addNew($data, 'productssub');
	echo json_encode(array("status" => TRUE));
   }

   public function edit_sub(){
	if(!$this->uri->segment(3)){echo json_encode(array("status" => FALSE));}
	else{
		$sub_id=$this->uri->segment(3);
		$data = $this->Admin_Model->get_sub($sub_id);
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
	$update = $this->Admin_Model->update($data, 'productssub', array('sproduct_id' => $sub));
	echo json_encode(array("status" => TRUE));

   }


// *************** sellers 
public function b2b(){
	if(!$this->uri->segment(3)){
	   $data['sellers'] = $this->Admin_Model->get_b2b();
	   $data['title'] = 'B2B';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/b2b/b2b');
	   $this->load->view('admin/template/footer');
	}
   else{show_404();}
   }
// ***************  END sellers



public function addb2b(){
	$data['title'] = 'B2B Form';
	$data['subscriptions'] = $this->Admin_Model->get_subscriptions();
	$this->load->view('admin/template/header', $data);
	$this->load->view('admin/template/nav');
	$this->load->view('admin/template/topbar');
	$this->load->view('admin/pages/b2b/addb2b');
	$this->load->view('admin/template/footer');
}




public function add_b2b(){

	$check = $this->Admin_Model->checkB2BPhn($this->input->post('b2b_phone',TRUE));

	if($check){

	$unique = $this->input->post('b2b_phone',TRUE);
	if(!empty($_FILES['b2b_store_image']['name'])){
		$s_image=$this->Admin_Model->fileupload('b2b_store_image', 'b2b', $unique);
	}else{$s_image='0';}
	if(!empty($_FILES['b2b_pan_image']['name'])){
		$p_image=$this->Admin_Model->fileupload('b2b_pan_image', 'b2b', $unique);
	}else{$p_image='0';}
	if(!empty($_FILES['b2b_licence_image']['name'])){
		$pay_image=$this->Admin_Model->fileupload('b2b_licence_image', 'b2b', $unique);
	}else{$pay_image='0';}
	$data = array(
		'b2b_name'=>$this->input->post('b2b_name',TRUE),
		'b2b_store_name'=>$this->input->post('b2b_store_name',TRUE),
		'b2b_phone_code'=>$this->input->post('b2b_phone_code',TRUE),
		'b2b_phone'=>$this->input->post('b2b_phone',TRUE),
		'b2b_email'=>$this->input->post('b2b_email',TRUE),
		'b2b_country'=>$this->input->post('b2b_country',TRUE),
		'b2b_state'=>$this->input->post('b2b_state',TRUE),
		'b2b_city'=>$this->input->post('b2b_city',TRUE),
		'b2b_pincode'=>$this->input->post('b2b_pincode',TRUE),
		'b2b_address'=>$this->input->post('b2b_address',TRUE),
		'b2b_lat'=>$this->input->post('b2b_lat',TRUE),
		'b2b_long'=>$this->input->post('b2b_long',TRUE),
		'b2b_pan_number'=>$this->input->post('b2b_pan_number',TRUE),
		'b2b_licence_number'=>$this->input->post('b2b_licence_number',TRUE),
		'b2b_device_id'=>$this->input->post('b2b_device_id',TRUE),
		'b2b_verified'=>$this->input->post('b2b_verified',TRUE),
		'b2b_status'=>$this->input->post('b2b_status',TRUE),
		'b2b_astatus'=>$this->input->post('b2b_astatus',TRUE),
		'b2b_store_image'=>$s_image,
		'b2b_pan_image'=>$p_image,
		'b2b_licence_image'=>$pay_image,
	);
	$added = $this->Admin_Model->addNew($data, 'b2b');
		$this->session->set_flashdata('message', 'Added Successfully!');
		redirect(base_url('admin/b2b'));


}else{
	$this->session->set_flashdata('message', 'Mobile Number already exists!');
	redirect(base_url('admin/addb2b'));
}
}


public function edit_b2b(){
	if(!$this->uri->segment(3)){ redirect(base_url('admin/b2b')); }
	else{
		$b2b_id = $this->uri->segment(3);
		$data['seller'] = $this->Admin_Model->get_b2b($b2b_id);
		$data['s_products'] = $this->Admin_Model->get_b2b_products($b2b_id);
		$data['title'] = 'B2B';
		$this->load->view('admin/template/header', $data);
		$this->load->view('admin/template/nav');
		$this->load->view('admin/template/topbar');
		$this->load->view('admin/pages/b2b/editb2b');
		$this->load->view('admin/template/footer');
	}
}


public function update_b2b(){
	$id = $this->input->post('b2b_id',TRUE);
	$unique = $this->input->post('b2b_phone',TRUE);
	if(!empty($_FILES['b2b_store_image']['name'])){
		$s_image=$this->Admin_Model->fileupload('b2b_store_image', 'b2b', $unique);
		$data['b2b_store_image']=$s_image;
	}
	if(!empty($_FILES['b2b_pan_image']['name'])){
		$p_image=$this->Admin_Model->fileupload('b2b_pan_image', 'b2b', $unique);
		$data['b2b_pan_image']=$p_image;
	}
	if(!empty($_FILES['b2b_licence_image']['name'])){
		$b2b_licence_image=$this->Admin_Model->fileupload('b2b_licence_image', 'b2b', $unique);
		$data['b2b_licence_image']=$b2b_licence_image;
	}
	$data['b2b_name']=$this->input->post('b2b_name',TRUE);
	$data['b2b_store_name']=$this->input->post('b2b_store_name',TRUE);
	$data['b2b_phone_code']=$this->input->post('b2b_phone_code',TRUE);
	$data['b2b_phone']=$this->input->post('b2b_phone',TRUE);
	$data['b2b_email']=$this->input->post('b2b_email',TRUE);
	$data['b2b_country']=$this->input->post('b2b_country',TRUE);
	$data['b2b_state']=$this->input->post('b2b_state',TRUE);
	$data['b2b_city']=$this->input->post('b2b_city',TRUE);
	$data['b2b_pincode']=$this->input->post('b2b_pincode',TRUE);
	$data['b2b_address']=$this->input->post('b2b_address',TRUE);
	$data['b2b_lat']=$this->input->post('b2b_lat',TRUE);
	$data['b2b_long']=$this->input->post('b2b_long',TRUE);
	$data['b2b_pan_number']=$this->input->post('b2b_pan_number',TRUE);
	$data['b2b_licence_number']=$this->input->post('b2b_licence_number',TRUE);
	$data['b2b_device_id']=$this->input->post('b2b_device_id',TRUE);
	$data['b2b_verified']=$this->input->post('b2b_verified',TRUE);
	$data['b2b_status']=$this->input->post('b2b_status',TRUE);
	$data['b2b_astatus']=$this->input->post('b2b_astatus',TRUE);
	$update = $this->Admin_Model->update($data, 'b2b', array('b2b_id ' => $id));

	$this->session->set_flashdata('message', 'Added Successfully!');
	redirect(base_url("admin/edit_b2b/$id"));

}

public function addproductb2b(){
	if(!$this->uri->segment(4)){
	   $seller_id = $this->uri->segment(3);
	   $data['seller'] = $this->Admin_Model->get_b2b($seller_id);
	   $data['cats'] = $this->Admin_Model->get_b2cats();
	   $data['subcats'] = $this->Admin_Model->get_b2subcats();
	   $data['meas'] = $this->Admin_Model->get_measures();
	   $data['title'] = $data['seller']['b2b_store_name'];
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/products/addproductb2b');
	   $this->load->view('admin/template/footer');
	}
   else{show_404();}
}



public function addpb2b(){
	$seller = $this->input->post('product_store');
	$data['product_store'] = $seller;
	$data['product_b2'] = '1';
	$data['product_name'] = $this->input->post('product_name');
	$data['product_category'] = $this->input->post('product_category');
	$data['product_subcategory'] = $this->input->post('product_subcategory');
	$data['product_measure'] = $this->input->post('product_measure');
	$data['product_description'] = $this->input->post('product_description');
	$data['product_a_status'] = '1';
	$hasvar = $this->input->post('product_var');
	if($hasvar == "0"){
		if(!empty($_FILES['product_img1']['name'])){
			$data['product_img1']=$this->Admin_Model->fileupload('product_img1', 'products', $data['product_store']);
		}else{$data['product_img1']='0';}
		if(!empty($_FILES['product_img2']['name'])){
			$data['product_img2']=$this->Admin_Model->fileupload('product_img2', 'products', $data['product_store']);
		}else{$data['product_img2']='0';}
		if(!empty($_FILES['product_img3']['name'])){
			$data['product_img3']=$this->Admin_Model->fileupload('product_img3', 'products', $data['product_store']);
		}else{$data['product_img3']='0';}
		if(!empty($_FILES['product_img4']['name'])){
			$data['product_img4']=$this->Admin_Model->fileupload('product_img4', 'products', $data['product_store']);
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
	$added = $this->Admin_Model->addNew($data, 'products');
	if($added) {
		$this->session->set_flashdata('message', 'Added Successfully!');
		redirect(base_url('admin/product/'.$seller.'/'.$added));
	}
   }

// *************** orders
public function orders(){
	$type = $this->uri->segment(3);
	if(!$this->uri->segment(4)){ $data['orders'] = $this->Admin_Model->get_orders($type); }
	if($this->uri->segment(4)){ 
		$type2 = $this->uri->segment(4);
		$data['orders'] = $this->Admin_Model->get_orders_canc($type, $type2); 
	}

	
	$data['title'] = 'Orders';
	$this->load->view('admin/template/header', $data);
	$this->load->view('admin/template/nav');
	$this->load->view('admin/template/topbar');
	$this->load->view('admin/pages/orders/orders');
	$this->load->view('admin/template/footer');
}


public function orderdetail(){
	if($this->uri->segment(3)){
	  $odrid = $this->uri->segment(3);
	  $data['order'] = $this->Admin_Model->get_order($odrid);
	  $data['cart'] = $this->Admin_Model->get_order_cart($odrid);
	  $data['title'] = 'Order';
	  $this->load->view('admin/template/header', $data);
	  $this->load->view('admin/template/nav');
	  $this->load->view('admin/template/topbar');
	  $this->load->view('admin/pages/orders/orderdetail');
	  $this->load->view('admin/template/footer');
	  }
	  else{redirect(base_url('seller/orders'));}
}

public function orderUpdate(){
	$order_id = $this->input->post('order_id');
	$data['order_status'] = $this->input->post('orderTrackStatus');
	$update = $this->Admin_Model->update($data, 'orders', array('order_id ' => $order_id));
	$this->load->model('Notify_model');
	if($data['order_status'] == '2'){ $this->Notify_model->user($order_id, "confirm"); }
	else if($data['order_status'] == '3'){ $this->Notify_model->user($order_id, "delivered"); }
	else if($data['order_status'] == '4'){ $this->Notify_model->user($order_id, "cancelled"); }
	redirect(base_url("admin/orderdetail/$order_id"));
}


// ***************  END orders

public function settings(){
	if(!$this->uri->segment(3)){
	$data['sett'] = $this->Admin_Model->get_row('admin', array('admin_id ' => '1'));
	$data['title'] = 'Settings';
	$this->load->view('admin/template/header', $data);
	$this->load->view('admin/template/nav');
	$this->load->view('admin/template/topbar');
	$this->load->view('admin/pages/settings/settings');
	$this->load->view('admin/template/footer');
}
	elseif($this->uri->segment(3)==='updatecred'){
		$data['admin_phone'] = $this->input->post('admin_phone');
		if($this->input->post('admin_password') != "" && $this->input->post('admin_passwordconfirm') != "" && $this->input->post('admin_password') == $this->input->post('admin_passwordconfirm')){
		  $data['admin_password'] = md5($this->input->post('admin_password'));
		 }else{
			$this->session->set_flashdata('message', 'Check all Details!');
			redirect(base_url('admin/settings'));

		 }
	$this->Admin_Model->update($data, 'admin', array('admin_id' => '1'));
	$this->session->set_flashdata('message', 'Successfully Updated!');
	redirect(base_url('admin/settings'));
}
}








public function boys(){
	$data['type'] = "All Delivery Boys";
	if(!$this->uri->segment(3)){
	   $data['boys'] = $this->Admin_Model->get_boys();
	   $data['title'] = 'Boys';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/boys/boys');
	   $this->load->view('admin/template/footer');
	}
    elseif($this->uri->segment(3)==='edit_boy'){
	   if(!$this->uri->segment(4)){ redirect(base_url('admin/boys')); }
	   else{
		   $seller_id = $this->uri->segment(4);
		   $data['boy'] = $this->Admin_Model->get_boys($seller_id);
		   $data['title'] = 'Boy';
		   $this->load->view('admin/template/header', $data);
		   $this->load->view('admin/template/nav');
		   $this->load->view('admin/template/topbar');
		   $this->load->view('admin/pages/boys/editboy');
		   $this->load->view('admin/template/footer');
	   }
   }


   else{show_404();}
}




public function update_boy(){

	$type = $this->uri->segment(3);
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
			   'boy_astatus'=>$this->input->post('boy_astatus',TRUE),
			   'boy_wallet'=>$this->input->post('boy_wallet',TRUE),

		   );

$updated = $this->Admin_Model->update($data, 'boys', array('boy_id' => $this->input->post('boy_id')));
$this->session->set_flashdata('message', 'Updated Successfully!');

if($type === "boys"){
	redirect(base_url('admin/boys'));
}else{	redirect(base_url('admin/boys'));

}

}






/*
===============================
COUPONS
===============================
*/
public function coupons(){

	if(!$this->uri->segment(3)){
	    $data['coupons'] = $this->Admin_Model->get_coupons();
	    $data['sellers'] = $this->Admin_Model->get_sellers();


	   $data['title'] = 'Coupons';
	   $this->load->view('admin/template/header', $data);
	   $this->load->view('admin/template/nav');
	   $this->load->view('admin/template/topbar');
	   $this->load->view('admin/pages/coupons/coupons');
	   $this->load->view('admin/template/footer');
	}
	elseif($this->uri->segment(3)==='add_coupon'){
	

	if(!empty($_FILES['coup_image']['name'])){
		$coup_image=$this->Admin_Model->fileupload('coup_image', 'coupons', 'cp');
	}else{$coup_image='0';}
	if(!empty($_FILES['coup_banner']['name'])){
		$coup_banner=$this->Admin_Model->fileupload('coup_banner', 'coupons', 'cp');
	}else{$coup_banner='0';}

		$data = array(
			'coup_name' => strtoupper($this->input->post('coup_name')),
			'coup_desc' => $this->input->post('coup_desc'),
			'coup_amount' => $this->input->post('coup_amount'),
			'coup_lat' => $this->input->post('coup_lat'),
			'coup_long' => $this->input->post('coup_long'),
			'coup_min' => $this->input->post('coup_min'),
			'coup_count' => $this->input->post('coup_count'),
			'coup_per' => $this->input->post('coup_per'),
			'coup_store' => $this->input->post('coup_store'),
			'coup_start' => $this->input->post('coup_start'),
			'coup_end' => $this->input->post('coup_end'),
			'coup_status' => $this->input->post('coup_status'),
			'coup_image' => $coup_image,
			'coup_banner' => $coup_banner
		);
		$insert = $this->Admin_Model->addNew($data, 'coupons');
		echo json_encode(array("status" => TRUE));
	}
	elseif($this->uri->segment(3)==='edit_coupon'){
		if(!$this->uri->segment(4)){echo json_encode(array("status" => FALSE));}
		else{
			$coup_id=$this->uri->segment(4);
			$data = $this->Admin_Model->get_coupons($coup_id);
			echo json_encode($data);
		}
	}

	elseif($this->uri->segment(3)==='update_coupon'){

		if(!empty($_FILES['coup_image']['name'])){
			$data['coup_image'] =$this->Admin_Model->fileupload('coup_image', 'coupons', 'cp');
		}else{}
		if(!empty($_FILES['coup_banner']['name'])){
			$data['coup_banner']=$this->Admin_Model->fileupload('coup_banner', 'coupons', 'cp');
		}else{}


			$data['coup_name'] = strtoupper($this->input->post('coup_name'));
			$data['coup_desc'] = $this->input->post('coup_desc');
			$data['coup_amount'] = $this->input->post('coup_amount');
			$data['coup_lat'] = $this->input->post('coup_lat');
			$data['coup_long'] = $this->input->post('coup_long');
			$data['coup_min'] = $this->input->post('coup_min');
			$data['coup_count'] = $this->input->post('coup_count');
			$data['coup_per'] = $this->input->post('coup_per');
			$data['coup_store'] = $this->input->post('coup_store');
			$data['coup_start'] = $this->input->post('coup_start');
			$data['coup_end'] = $this->input->post('coup_end');
			$data['coup_status'] = $this->input->post('coup_status');
			$this->Admin_Model->update($data, 'coupons', array('coup_id' => $this->input->post('coup_id')));
			echo json_encode(array("status" => TRUE));
		}

}





public function logout(){ $this->session->sess_destroy(); redirect(base_url('admin')); }

}