<?php
class My404 extends CI_Controller{
   public function index()
   {
       $this->output->set_status_header('404');
       $this->load->view('front/headers/header');
       $this->load->view('front/headers/navbar');
       $this->load->view('errors/html/error_404');
       $this->load->view('front/footers/footer_scripts');
    }
}