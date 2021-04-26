<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stores extends CI_Controller {

	public function index()
	{
		$this->load->view('front/headers/header');
		$this->load->view('front/headers/navbar');
        $this->load->view('front/stores/stores_header');
        $this->load->view('front/stores/stores_filter');
		$this->load->view('front/stores/stores_list');
		$this->load->view('front/footers/footer');
		$this->load->view('front/footers/footer_scripts');
	}
}
