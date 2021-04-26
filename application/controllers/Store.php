<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends CI_Controller {

	public function index()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
        $this->load->view('front/store/store_header');
        $this->load->view('front/store/store_items');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}
}
