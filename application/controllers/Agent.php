<?php 

	class Agent extends CI_Controller{

	function __construct() {
		parent::__construct();
        $this->load->model('AgentM');
    }
	public function index(){
        $this->home();
    }
    

	public function home()
	{
        $this->load->helper('string');
        $data['rand'] =  rand(10000, 99999);
        $data['bCaptcha'] = base64_encode($data['rand']);
        $this->load->view('agent/front/home', $data);
	}


	public function terms()
	{
          $this->load->view('agent/front/terms');
	}

	public function privacy()
	{
          $this->load->view('agent/front/privacy');
	}

public function login(){
    $this->load->view('agent/front/includes/header');
    $this->form_validation->set_rules('ag_email', 'Email', 'required');
    $this->form_validation->set_rules('ag_password', 'Password', 'required');
    $this->form_validation->set_message('required', 'Enter {field}..');
    $this->form_validation->set_message('min_length', '%s characters should be above %s');
    $this->form_validation->set_message('max_length', '%s characters should be below %s');
    if($this->form_validation->run() === FALSE){
        $this->load->view('agent/front/login');
    }else{
        $email = $this->input->post('ag_email');
        $password = md5($this->input->post('ag_password'));
        $ag_data = $this->AgentM->login($email, $password);
        if ($ag_data){
			$this->session->set_userdata('agentdata', $ag_data);			
            $this->session->set_userdata('agentLogin', true);
            $this->session->set_flashdata('login_success', 'Login Successful');
            redirect(base_url('agent/dashboard'));
        }else{
            $this->session->set_flashdata('login_failed', 'Login Failed.');
            redirect(base_url('agent/login'));
        }
    }

    $this->load->view('agent/front/includes/footer');
}




function agent_otp_send(){
	$mobile=$this->input->post('ag_mobile',TRUE);
	$res=$this->AgentM->agLogin($mobile);
	if($res){
		$this->load->model('Sms_model');
		$otp_digit = mt_rand(1000, 9999);
		$data = array('ag_otp' => $otp_digit);
		$updated=$this->AgentM->update($data, 'agents', array('ag_astatus' => '1', 'ag_country_code' => "+91", 'ag_phone' => $mobile));
		$message="Your OTP at ELLOCART is ".$otp_digit;
		$sent=$this->Sms_model->sendSMS($mobile, $message, "1207161425250670430");
		if($sent){
			echo json_encode(array("status" => TRUE, "phone" => "Sent to $mobile"));
		} else{
			echo json_encode(array("status" => FALSE, "phone" => ""));
		}

	}else{
		echo json_encode(array("status" => FALSE, "phone" => ""));
	}
}
function agent_otp_verify(){
	$mobile=$this->input->post('ag_mobile',TRUE);
	$otp=$this->input->post('ag_otp',TRUE);
	$data = array('ag_astatus' => '1', 'ag_country_code' => "+91", 'ag_phone' => $mobile);
	$slrres=$this->AgentM->get_row('agents', $data);
	if($otp == $slrres['ag_otp']){
		$login_data = array(
			'aid' => $slrres['ag_id'],
			'ag_phone' => $slrres['ag_phone'],
			'utype' => 'Agent',
			'loggedin' => true,
		);
		$this->session->set_userdata($login_data);
		echo json_encode(array("status" => TRUE));
	}else{
		echo json_encode(array("status" => FALSE));
	}
}


public function logout(){ $this->session->sess_destroy(); redirect(base_url('agent/login')); }




public function callback_checkMobile($post_roll) 
{
    $this->db->where('ag_phone', $roll);
    $query = $this->db->get('agents');
    $count_row = $query->num_rows();
    if ($count_row > 0) 
    {
        return FALSE;
    } 
    else 
    {
        return TRUE;
    }
}



    public function register(){
        $this->load->view('agent/front/includes/header');
        $this->form_validation->set_rules('ag_name', 'Name', 'required');
        $this->form_validation->set_rules('ag_email', 'Email', 'required');
        $this->form_validation->set_rules('ag_phone', 'Mobile', 'required');

        if($this->form_validation->run() === FALSE){
            $this->load->view('agent/front/register');
        }else{
        $ag_phone = $this->input->post('ag_phone');
        if(!empty($_FILES['ag_adhaar'])){
            $adri=$this->AgentM->fileupload('ag_adhaar', 'agents', $ag_phone);
        }else{$adri='0';}
        if(!empty($_FILES['ag_pan'])){
            $pai=$this->AgentM->fileupload('ag_pan', 'agents', $ag_phone);
        }else{$pai='0';}
        if(!empty($_FILES['ag_profileimg'])){
            $prof=$this->AgentM->fileupload('ag_profileimg', 'agents', $ag_phone);
        }else{$prof='0';}
        $agname = $this->input->post('ag_name');
        $agphn = $this->input->post('ag_phone');

        $agData = array(
            'ag_name' => $agname,
            'ag_gender' => $this->input->post('ag_gender'),
            'ag_dob' => $this->input->post('ag_dob'),
            'ag_address' => $this->input->post('ag_address'),
            'ag_email' => $this->input->post('ag_email'),
            'ag_country_code' => "+91",
            'ag_phone' => $agphn,
            'ag_pan' => $pai,
            'ag_adhaar' => $adri,
            'ag_profile' => $prof,
            'ag_acc_no' => $this->input->post('ag_acc_no'),
            'ag_ifsc_code' => $this->input->post('ag_ifsc_code'),
            'ag_bank_name' => $this->input->post('ag_bank_name'),
            'ag_branch_name' => $this->input->post('ag_branch_name'),
            'ag_reg_date' => date('Y-m-d H:i:s'),
            'ag_astatus' => '1',
            'ag_status' => '0',
            'ag_password' => md5("Ellocart@99"),
        );
        $added_id = $this->AgentM->addNew($agData, 'agents');
        if(!empty($added_id)){
            $this->load->model('Sms_model');
            $message="Thank you $agname for joining Ellocart family as an ElloAgent. Your Agent ID is - ELOAG$added_id. Please go through the Terms and conditions here https://www.elloagent.com/";
            $this->Sms_model->sendSMS($agphn, $message);
            $sent=$this->session->set_flashdata('msg', 'Successfully Registered!');
                redirect(base_url('agent/login')); 
            }else{
                $this->session->set_flashdata('msg', 'Something wrong.');
                redirect(base_url('agent/register')); 
            }
        }     
        
    $this->load->view('agent/front/includes/footer'); 
    }





   function editprofile(){
            if(!$this->session->userdata('franchiseLogin') == TRUE){redirect(base_url('franchise/login'));}
            $this->form_validation->set_rules('fr_firstname', 'First Name', 'required');
            $this->form_validation->set_rules('fr_lastname', 'Last Name', 'required');
            $this->form_validation->set_rules('fr_password', 'Password', 'required');
            $this->form_validation->set_message('required', 'Enter {field}..');
            $this->form_validation->set_message('min_length', '%s characters should be above %s');
            $this->form_validation->set_message('max_length', '%s characters should be below %s');
            $board['franchise'] =  $this->session->userdata('franchisedata');
            if($this->form_validation->run() === FALSE){
            $this->load->view('index/header');
            $this->load->view('franchise/dashboardnav');
            $this->load->view('franchise/profileEdit', $board);
            $this->load->view('franchise/footer');
            }else{

                $fr_id = $board['franchise']->fr_id;
                $franchiseUpdateData = array(
                    'fr_firstname' => $this->input->post('fr_firstname'),
                    'fr_lastname' => $this->input->post('fr_lastname'),
                    'fr_careof' => $this->input->post('fr_careof'),
                    'fr_address' => $this->input->post('fr_address'),
                    'fr_acc_no' => $this->input->post('fr_acc_no'),
                    'fr_ifsc_code' => $this->input->post('fr_ifsc_code'),
                    'fr_bank_name' => $this->input->post('fr_bank_name'),
                    'fr_branch_name' => $this->input->post('fr_branch_name'),
                    'fr_password' => base64_encode($this->input->post('fr_password')),
                );
                $success = $this->Franchise_Model->updateFranchise($fr_id, $franchiseUpdateData);

                if ($success) {
                    $franchise_data = $this->Franchise_Model->getFranchise($fr_id);
                    if ($franchise_data) {
                        $this->session->set_userdata('franchisedata', $franchise_data);			
                        $this->session->set_userdata('franchiseLogin', true);
                        $this->session->set_flashdata('update_status', 'Update Successful');
                        redirect(base_url('index.php?/franchise/editprofile'));
                    }else{
                        $this->session->set_flashdata('update_status', 'Something Wrong');
                        redirect(base_url('index.php?/franchise/login'));
                    }

                    }else {
                        redirect(base_url('index.php?/franchise/login'));
                    }
                }
            }
        





            function dashboard(){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
               
               $data['sellers']=$this->sales(true);
                $data['renews']=$this->renewal(true);
                $data['commission']=$this->commission(true);
                $data['updates']=$this->updates(true);

               
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/dashboard');
                $this->load->view('agent/admin/includes/footer');
            }
    


            public function sales($cc = false){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $id = $this->session->userdata('aid');
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" => $id));
              
                $sells=$this->AgentM->getmysellers($id);
                $res = array();

                $count = 0;
                foreach ($sells as $row):
                $res[] = [
                    'seller_store_name' => $row['seller_store_name'],
                    'seller_phone_code' =>  $row['seller_phone_code'],
                    'seller_store_address' =>  $row['seller_store_address'],
                    'seller_store_image' =>  $row['seller_store_image'],
                    'seller_phone' =>  $row['seller_phone'],
                    'seller_city' =>  $row['seller_city'],
                    'seller_pincode' =>  $row['seller_pincode'],
                ];
                $count++;
                endforeach;

                if($cc){ return $count; exit; };

                $data['sellers'] = $res;
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/sales');
                $this->load->view('agent/admin/includes/footer');
            }


            public function renewal($cc = false){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $id = $this->session->userdata('aid');
                $sells=$this->AgentM->getmysellers($id);
                $res = array();
                $date = date('Y-m-d');
                 $minday = date( "Y-m-d", strtotime("$date -5 day"));
                  $nowdate = strtotime($minday);
                  $count = 0;
                foreach ($sells as $row):
                    $val = $this->AgentM->checkValidity($row['seller_id']);
                    if(!empty($val)){
                         $val['pay_validity'];
                 //       $exdate = date("Y-m-d", $val['pay_validity']);
                        $stexdate = strtotime($val['pay_validity']);
                        if($stexdate >= $nowdate){ 
                            $valid = $val['pay_validity'];
                            $notvalid = false; 
                        }else{ 
                            $valid = $val['pay_validity'];
                            $notvalid = true; 
                        }

                    }else{
                        $valid = "No Validity";
                        $notvalid = true; 
                    }
                    
            if($notvalid){
                $res[] = [
                    'seller_store_name' => $row['seller_store_name'],
                    'seller_phone_code' =>  $row['seller_phone_code'],
                    'seller_store_address' =>  $row['seller_store_address'],
                    'seller_store_image' =>  $row['seller_store_image'],
                    'seller_phone' =>  $row['seller_phone'],
                    'seller_city' =>  $row['seller_city'],
                    'seller_pincode' =>  $row['seller_pincode'],
                    'validity' => $valid
                ];
                $count++;

            }
                endforeach;

                if($cc){ return $count; exit; };

                $data['sellers'] = $res;
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/renewal');
                $this->load->view('agent/admin/includes/footer');
            }
            
            public function profile(){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('admin/profile');
                $this->load->view('admin/includes/footer');
            }
        

        
            public function reports(){

                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));

                $data['upds']=$this->AgentM->getupdates();

                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('admin/includes/sidebar');
                $this->load->view('admin/reports');
                $this->load->view('admin/includes/footer');
            }
        
            public function updates($cc = false){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $data['upds']=$this->AgentM->getupdates();
                if($cc){ 
                    $count = count($data['upds']);
                    return $count;exit;
                }
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/updates');
                $this->load->view('agent/admin/includes/footer');
            }
        
            public function commission($cc = false){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $id = $this->session->userdata('aid');
                $data['comms']=$this->AgentM->getmycomps($id);
                $total = 0;
                foreach ($data['comms'] as $row):
                    $total = $total + $row['agp_amount'];
                endforeach;
                $data['total']=$total;
                if($cc){ return $total; exit; };
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/commission');
                $this->load->view('agent/admin/includes/footer');
            }
        
            public function materials(){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $data['mats']=$this->AgentM->getmaterials();
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/materials');
                $this->load->view('agent/admin/includes/footer');
            }
        
            public function support(){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/support');
                $this->load->view('agent/admin/includes/footer');
            }

            public function supportadd(){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $id = $this->session->userdata('aid');
                $agData = array(
                    'ags_main' => $id,
                    'ags_message' => $this->input->post('ag_message'),
                    'ags_date' => date("Y-m-d"),
                    'ags_status' => '1'
                );
                $added_id = $this->AgentM->addNew($agData, 'agentsupport');
                if(!empty($added_id)){
                    $this->session->set_flashdata('msg', 'Successfully Sent!');
                        redirect(base_url('agent/support')); 
                    }else{
                        $this->session->set_flashdata('msg', 'Something wrong.');
                        redirect(base_url('agent/support')); 
                    }
            }          
        
            public function contest(){
                if(!$this->session->userdata('loggedin') == TRUE){redirect(base_url('agent/login'));}
                $data['agent']=$this->AgentM->get_row('agents', array("ag_id" =>  $this->session->userdata('aid')));
                $this->load->view('agent/admin/includes/header', $data);
                $this->load->view('agent/admin/includes/sidebar');
                $this->load->view('agent/admin/contest');
                $this->load->view('agent/admin/includes/footer');
            }




}

	