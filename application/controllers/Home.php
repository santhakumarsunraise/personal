<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
		$this->load->view('front/home/search');
		$this->load->view('front/home/section2');
		$this->load->view('front/home/section3');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}

	public function about()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
		$this->load->view('front/pages/about');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}
	public function aboutus()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
		$this->load->view('front/pages/about');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}

	public function contact()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
		$this->load->view('front/pages/contact');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}

	public function faqs()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
		$this->load->view('front/pages/faqs');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}

	public function privacy()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
		$this->load->view('front/pages/privacy');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}

	public function terms()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
		$this->load->view('front/pages/terms');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}


}
