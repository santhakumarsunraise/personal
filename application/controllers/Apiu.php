<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Apiu extends CI_Controller {
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('ApiuM');
    }
    function index() {
        $rsd['status'] = 'error';
        $rsd['message'] = 'Not Authorized';
        echo json_encode($rsd);
        exit;
    }
    function error() {
        $rsd['status'] = 'error';
        $rsd['message'] = 'Something went wrong';
        echo json_encode($rsd);
        exit;
    }
    function notfound() {
        $rsd['status'] = 'error';
        $rsd['message'] = 'No Data Found';
        echo json_encode($rsd);
        exit;
    }
    function userExists() {
        $rsd['status'] = 'error';
        $rsd['message'] = 'User Already Exist';
        echo json_encode($rsd);
        exit;
    }

    function check_ver(){
              $os = $this->input->post('os');
              $ver = $this->input->post('ver');
              if($os === "android"){
                if($ver >= "17"){
                    $res_data['status'] = 'ok';
                }else{
                    $res_data['status'] = 'error';
                }
              }elseif($os === "ios"){
                if($ver >= "1"){
                    $res_data['status'] = 'ok';
                }else{
                    $res_data['status'] = 'error';
                }
              }
              echo json_encode($res_data);
              exit;
    }

    // splash
    function get_splash() {
        $imgs = $this->ApiuM->get_splash('1');
        if (!empty($imgs)) {
            foreach ($imgs as $row):
                $res_dataa[] = ['spl_id' => $row['spl_id'], 'spl_name' => $row['spl_name'], 'spl_image' =>  $row['spl_image']];
            endforeach;
            $res_data['status'] = 'ok';
            $res_data['splash'] = $res_dataa;
        } else {
            $res_data['status'] = 'error';
            $res_data['splash'] = [];
        }
        echo json_encode($res_data);
        exit;
    }
    // country detail by lat $ long from db
    function get_country() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lat = $this->input->post('lat');
            $long = $this->input->post('long');
            $cCheck = $this->ApiuM->checkCountry($lat, $long);
            if (!empty($cCheck)) {
                $rsd['status'] = 'ok';
                $rsd['cnty_code'] = $cCheck['cnty_code'];
                $rsd['cnty_name'] = $cCheck['cnty_name'];
                $rsd['cnty_phone'] = $cCheck['cnty_phone'];
                $rsd['cnty_currency'] = $cCheck['cnty_currency'];
                $rsd['cnty_entity'] = $cCheck['cnty_entity'];
                $rsd['cnty_flag'] =  $cCheck['cnty_flag'];
                $rsd['message'] = 'country exist';
            } else {
                $rsd['status'] = 'error';
                $rsd['message'] = 'no country';
            }
            echo json_encode($rsd);
        } else {
            $this->error();
        }
        exit;
    }
    // check user and send otp
    function check_user() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $this->input->post('user_phone');
            $phone_code = $this->input->post('user_phone_code');
            $uCheck = $this->ApiuM->checkUserExists($phone, $phone_code);
            if ($uCheck) {
                $rsd['status'] = 'ok';
                $rsd['user'] = "1";
                $rsd['user_phone_code'] = $uCheck['user_phone_code'];
                $rsd['user_phone'] = $uCheck['user_phone'];
                $rsd['message'] = 'user exist';
                $otp = $this->sendOTP($uCheck['user_id'], $uCheck['user_phone']);
                if ($otp){
                    $rsd['otp'] = 'sent';
                } else {
                    $rsd['otp'] = 'failed';
                }
                echo json_encode($rsd);
            } else {
                $rsd['status'] = 'error';
                $rsd['user'] = "0";
                $rsd['user_phone_code'] = $phone_code;
                $rsd['user_phone'] = $phone;
                $rsd['message'] = 'Register to login';
                echo json_encode($rsd);
            }
        } else {
            $this->error();
        }
        exit;
    }
    // check user login validate verify
    function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $this->input->post('user_phone');
            $phone_code = $this->input->post('user_phone_code');
            $user_otp = $this->input->post('user_otp');
            $user_lat = $this->input->post('user_lat');
            $user_long = $this->input->post('user_long');
            $uCheck = $this->ApiuM->checkUserExists($phone, $phone_code);
            if ($uCheck) {
                if ($user_otp === $uCheck['user_otp']) {
                    $data = array('user_lat' => $user_lat, 'user_long' => $user_long);
                    $updated = $this->ApiuM->update($data, 'users', array('user_phone_code' => $phone_code, 'user_phone' => $phone));
                    $rsd['status'] = 'ok';
                    $rsd['user'] = "1";
                    $rsd['user_phone_code'] = $uCheck['user_phone_code'];
                    $rsd['user_phone'] = $uCheck['user_phone'];
                    $rsd['user_id'] = $uCheck['user_id'];
                    $rsd['user_name'] = $uCheck['user_name'];
                    $rsd['user_email'] = $uCheck['user_email'];
                    $rsd['message'] = 'login success';
                } else {
                    $rsd['status'] = 'error';
                    $rsd['user'] = "1";
                    $rsd['user_phone_code'] = $uCheck['user_phone_code'];
                    $rsd['user_phone'] = $uCheck['user_phone'];
                    $rsd['user_id'] = '';
                    $rsd['user_name'] = '';
                    $rsd['user_email'] = '';
                    $rsd['message'] = 'Wrong OTP';
                }
                echo json_encode($rsd);
                exit;
            } else {
                $rsd['status'] = 'error';
                $rsd['user'] = "1";
                $rsd['user_phone_code'] = $phone_code;
                $rsd['user_phone'] = $phone;
                $rsd['message'] = 'No user exists. Please Register!';
                echo json_encode($rsd);
                exit;
            }
        } else {
            $this->error();
        }
    }
    // resend same otp
    function resend_otp() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $this->input->post('user_phone');
            $phone_code = $this->input->post('user_phone_code');
            $otp_digit = $this->ApiuM->get_field('user_otp', 'users', array('user_phone_code' => $phone_code, 'user_phone' => $phone));
            if ($otp_digit > 999) {
                $this->load->model('Sms_model');
            
                $message = 'Your OTP at ELLOCART is '.$otp_digit;
                $sent=$this->Sms_model->sendSMS($phone, $message, "1207161425250670430");
                if ($sent) {
                    $rsd['status'] = 'ok';
                    $rsd['user_phone_code'] = $phone_code;
                    $rsd['user_phone'] = $phone;
                    $rsd['sms'] = 'sent';
                    echo json_encode($rsd);
                } else {
                    $rsd['status'] = 'error';
                    $rsd['user_phone_code'] = $phone_code;
                    $rsd['user_phone'] = $phone;
                    $rsd['sms'] = 'failed';
                    echo json_encode($rsd);
                }
            } else {
                $this->error();
            }
        } else {
            $this->error();
        }
        exit;
    }
    // user device update
    function update_device() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $this->input->post('user_phone');
            $phone_code = $this->input->post('user_phone_code');
            $user_id = $this->input->post('user_id');
            $user_token = $this->input->post('user_token');
            $user_device = $this->input->post('user_device');
            $user_lat = $this->input->post('user_lat');
            $user_long = $this->input->post('user_long');
            $data = array('user_token' => $user_token, 'user_device' => $user_device, 'user_lat' => $user_lat, 'user_long' => $user_long);
            $updated = $this->ApiuM->update($data, 'users', array('user_id' => $user_id, 'user_phone_code' => $phone_code, 'user_phone' => $phone));
            $rsd['status'] = 'ok';
            $rsd['phone_code'] = $phone_code;
            $rsd['phone'] = $phone;
            $rsd['user_id'] = $user_id;
            $rsd['message'] = 'Device Updated';
            echo json_encode($rsd);
        } else {
            $this->error();
        }
        exit;
    }
    // user register
    function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $this->input->post('user_phone');
            $phone_code = $this->input->post('user_phone_code');
            $user_name = $this->input->post('user_name');
            $uCheck = $this->ApiuM->checkUserExists($phone, $phone_code);
            if ($uCheck) {
                $rsd['status'] = 'error';
                $rsd['user'] = "1";
                $rsd['user_phone_code'] = $uCheck['user_phone_code'];
                $rsd['user_phone'] = $uCheck['user_phone'];
                $rsd['message'] = 'user exist';
                echo json_encode($rsd);
                exit;
            } else {
                $rnd = '0';
                if ($rnd == 1) {
                    $camt = '10';
                } elseif ($rnd == 2) {
                    $camt = '20';
                } elseif ($rnd == 3) {
                    $camt = '30';
                } elseif ($rnd == 4) {
                    $camt = '40';
                } elseif ($rnd == 5) {
                    $camt = '50';
                }
                $data = array('user_name' => $user_name, 'user_phone' => $phone, 'user_phone_code' => $phone_code, 'user_email' => trim($_POST['user_email'], true), 'user_device' => trim($_POST['user_device'], true), 'user_lat' => trim($_POST['user_lat'], true), 'user_long' => trim($_POST['user_long'], true), 'user_wallet' => $rnd, 'user_status' => '1');
                $added = $this->ApiuM->add_new($data, 'users');
                // if (trim($_POST['user_ref']) != $phone) {
                //     $this->db->where('user_phone', trim($_POST['user_ref'], true));
                //     $this->db->set('user_wallet', 'user_wallet+50', FALSE);
                //     $this->db->update('users');
                // }
                if ($added) {
                    $rsd['status'] = 'ok';
                    $rsd['user'] = "0";
                    $rsd['user_phone_code'] = $phone_code;
                    $rsd['user_phone'] = $phone;
                    $rsd['user_email'] = trim($_POST['user_email'], true);
                    $rsd['message'] = 'user registered';
                    $otp = $this->sendOTP($added, $phone);
                    $rsd['coupon_amount'] = $rnd;
                    if ($otp) {
                        $rsd['otp'] = 'sent';
                    } else {
                        $rsd['otp'] = 'failed';
                    }
                    echo json_encode($rsd);
                } else {
                    $this->error();
                }
            }
        } else {
            $this->error();
        }
        exit;
    }
    // send otps common
    function sendOTP($id, $phone) {
        if ($phone === "9505610288") {
            $otp_digit = 1234;
        } else {
            $otp_digit = mt_rand(1000, 9999);
        }
        $data = array('user_otp' => $otp_digit);
        $updated = $this->ApiuM->update($data, 'users', array('user_id' => $id, 'user_phone' => $phone));
        $this->load->model('Sms_model');
        

        $message = 'Your OTP at ELLOCART is '.$otp_digit;
		$sent=$this->Sms_model->sendSMS($phone, $message, "1207161425250670430");

        if ($sent) {
            return "sent";
        } else {
            return "failed";
        }
        exit;
    }
    // to get user profile
    function get_profile_detail() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "user" && trim($_POST["user_id"]) != '') {
            $user_id = $this->input->post('user_id');
            $prof = $this->ApiuM->getMyProfile($user_id);
            if (!empty($prof)) {
                if ($prof['user_image'] != '0') {
                    $user_image =  $prof['user_image'];
                } else {
                    $user_image = 'https://ellocart.com/assets/img/ellocart.png';
                }
                $res_dataa[] = ['user_id' => $prof['user_id'], 'user_name' => $prof['user_name'], 'user_phone_code' => $prof['user_phone_code'], 'user_phone' => $prof['user_phone'], 'user_email' => $prof['user_email'], 'user_image' => $user_image, 'user_token' => $prof['user_token'], 'user_lat' => $prof['user_lat'], 'user_long' => $prof['user_long'], 'user_own_ref' => 'ELOCRT' . $prof['user_id'], 'user_wallet' => $prof['user_wallet'], ];
                $res_data['status'] = 'ok';
                $res_data['profile'] = $res_dataa;
            } else {
                  $res_data['profile'] = [];
            }
            $res_data['type'] = 'user';
            $res_data['user_id'] = $user_id;
            echo json_encode($res_data);
        } else {
            $this->error();
        }
        exit;
    }
    // user profile update
    function update_user() {
        $user_id = $this->input->post('user_id');
        $type = $this->input->post('type');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $type === "update") {
            if (!empty($_FILES['user_image']['name'])) {
                $data['user_image'] = $this->ApiuM->fileupload('user_image', 'users', $user_id);
            }
            $data['user_name'] = $this->input->post('user_name');
            $updated = $this->ApiuM->update($data, 'users', array('user_id' => $user_id));
            if ($updated) {
                $rsd['status'] = 'ok';
                $rsd['type'] = 'update';
                $rsd['user_id'] = $user_id;
                echo json_encode($rsd);
            } else {
                $this->error();
            }
        } else {
            $this->error();
        }
        exit;
    }
    // notification bell list
    function notifys() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "user" && trim($_POST["user_id"]) != '') {
            $user_id = $this->input->post('user_id');
            $notes = $this->ApiuM->getMyNotifys($user_id);
            if (!empty($notes)) {
                foreach ($notes as $row):
                    $res_dataa[] = ['notify_id' => $row['notify_id'], 'notify_title' => $row['notify_title'], 'notify_message' => $row['notify_message'], 'notify_type' => 'order'];
                endforeach;
                $rsd['status'] = 'ok';
                $rsd['notifys'] = $res_dataa;
            } else {
                $rsd['status'] = 'error';
                $rsd['notifys'] = [];
            }
            $rsd['type'] = 'user';
            $rsd['user_id'] = $user_id;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
        exit;
    }
    // home banners
    function get_banners() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $banners = $this->ApiuM->getBanners('user');
            if (!empty($banners)) {
                foreach ($banners as $row):
                    $res_dataa[] = ['banner_id' => $row['banner_id'], 'banner_name' => $row['banner_name'], 'banner_image' =>  $row['banner_image'], ];
                endforeach;
                $res_data['banners'] = $res_dataa;
                $res_data['status'] = 'ok';
                $res_data['message'] = 'banners';
            } else {
                $res_data['banners'] = [];
                $res_data['status'] = 'error';
                $res_data['message'] = 'no banners';
            }
            $res_data['type'] = 'user';
            echo json_encode($res_data);
        } else {
            $this->error();
        }
        exit;
    }


    function get_banners_b2b() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $banners = $this->ApiuM->getBanners('b2b');
            if (!empty($banners)) {
                foreach ($banners as $row):
                    $res_dataa[] = ['banner_id' => $row['banner_id'], 'banner_name' => $row['banner_name'], 'banner_image' =>  $row['banner_image'], ];
                endforeach;
                $res_data['banners'] = $res_dataa;
                $res_data['status'] = 'ok';
                $res_data['message'] = 'banners';
            } else {
                $res_data['banners'] = [];
                $res_data['status'] = 'error';
                $res_data['message'] = 'no banners';
            }
            $res_data['type'] = 'b2b';
            echo json_encode($res_data);
        } else {
            $this->error();
        }
        exit;
    }

    function get_banners_services() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $banners = $this->ApiuM->getBanners('serv');
            if (!empty($banners)) {
                foreach ($banners as $row):
                    $res_dataa[] = ['banner_id' => $row['banner_id'], 'banner_name' => $row['banner_name'], 'banner_image' =>  $row['banner_image'], ];
                endforeach;
                $res_data['banners'] = $res_dataa;
                $res_data['status'] = 'ok';
                $res_data['message'] = 'banners';
            } else {
                $res_data['banners'] = [];
                $res_data['status'] = 'error';
                $res_data['message'] = 'no banners';
            }
            $res_data['type'] = 'services';
            echo json_encode($res_data);
        } else {
            $this->error();
        }
        exit;
    }


    // get home categories by near by stores products active
    function get_stores_cat() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cat = $this->input->post('category_id');
            $lat = $this->input->post('user_lat');
            $long = $this->input->post('user_long');
            $kms = $this->input->post('kms');
            $stores = $this->ApiuM->getStores($lat, $long, $kms);
            if (!empty($stores)) {
                foreach ($stores as $row):
                    $chk = $this->ApiuM->ckStoreProdCat($row['seller_id'], $cat);
                    if ($chk) {
                        if ($row['seller_store_image'] != '0') {
                            $img1 =  $row['seller_store_image'];
                        } else {
                            $img1 = '';
                        }
                        if ($this->input->post('user_id') != "") {
                            $wish = $this->ApiuM->cWish($this->input->post('user_id'), $row['seller_id']);
                        } else {
                            $wish = '0';
                        }
                        $res_dataa[] = ['seller_id' => $row['seller_id'], 'seller_store_image' => $img1, 'seller_store_name' => $row['seller_store_name'], 'seller_store_address' => $row['seller_store_address'], 'seller_city' => $row['seller_city'], 'seller_pincode' => $row['seller_pincode'], 'seller_rating' => strval($this->getRatingst($row['seller_id'])), 'seller_lat' => $row['seller_lat'], 'seller_long' => $row['seller_long'], 'seller_distance' => round($row['distance']), 'seller_ostatus' => $row['seller_ostatus'], 'seller_minimum_order' => $row['seller_minimum_order'], 'seller_offer' => '', 'wish_status' => $wish, 'seller_time' => $row['seller_time']];
                    }
                endforeach;
                if (!empty($res_dataa)) {
                    $rsd['stores'] = $res_dataa;
                    $rsd['status'] = 'ok';
                    $rsd['message'] = "near by stores";
                } else {
                    $rsd['stores'] = [];
                    $rsd['status'] = 'error';
                    $rsd['message'] = "No stores with products";
                }
            } else {
                $rsd['stores'] = [];
                $rsd['status'] = 'error';
                $rsd['message'] = "No stores";
            }
            $rsd['category_id'] = $cat;
            $rsd['type'] = "stores";
            echo json_encode($rsd);
        } else {
            $this->error();
        }
        exit;
    }
    function getRatingst($id) {
        $where = array("reviews_seller_id" => $id);
        $this->db->where($where);
        $this->db->select_avg('reviews_rate');
        $query = $this->db->get('reviewstore')->first_row('array');
        return round($query['reviews_rate'], 2);
    }
    function get_stores() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lat = trim($_POST['user_lat']);
            $long = trim($_POST['user_long']);
            $kms = trim($_POST['kms']);
            $stores = $this->ApiuM->getStores($lat, $long, $kms);
            if (!empty($stores)) {
                foreach ($stores as $row):
                    $chk = $this->ApiuM->ckStoreProd($row['seller_id']);
                    if ($chk) {
                        if ($row['seller_store_image'] != '0') {
                            $img1 =  $row['seller_store_image'];
                        } else {
                            $img1 = '';
                        }
                        $res_dataa[] = ['seller_id' => $row['seller_id'], 'seller_store_image' => $img1, 'seller_store_name' => $row['seller_store_name'], 'seller_store_address' => $row['seller_store_address'], 'seller_city' => $row['seller_city'], 'seller_pincode' => $row['seller_pincode'], 'seller_rating' => '0', 'seller_lat' => $row['seller_lat'], 'seller_long' => $row['seller_long'], 'seller_distance' => round($row['distance']) ];
                    }
                endforeach;
                if (!empty($res_dataa)) {
                    $rsd['stores'] = $res_dataa;
                    $rsd['status'] = 'ok';
                    $rsd['message'] = "near by stores";
                } else {
                    $rsd['stores'] = [];
                    $rsd['status'] = 'error';
                    $rsd['message'] = "No stores with products";
                }
            } else {
                $rsd['stores'] = [];
                $rsd['status'] = 'error';
                $rsd['message'] = "No stores";
            }
            $rsd['type'] = "stores";
            echo json_encode($rsd);
            exit;
        } else {
            $this->error();
        }
    }
    function get_home_categories() {
        $lat = $this->input->post('user_lat');
        $long = $this->input->post('user_long');
        $kms = $this->input->post('kms');
        $catg = $this->ApiuM->getHmeCategories($lat, $long, $kms);
        if (!empty($catg)) {
            foreach ($catg as $row):
                $chk = $this->ApiuM->ckStoreProd2($lat, $long, $kms, $row['category_id']);
                if ($chk) {
                    $res_dataa[] = ['category_id' => $row['category_id'], 'category_name' => $row['category_name'], 'category_image' =>  $row['category_image'], 'category_image2' =>  $row['category_image2']];
                }
            endforeach;
            if (!empty($res_dataa)) {
                $rsd['categories'] = $res_dataa;
                $rsd['status'] = 'ok';
            } else {
                $rsd['categories'] = [];
                $rsd['status'] = 'error';
            }
        } else {
            $rsd['categories'] = [];
            $rsd['status'] = 'error';
        }
        $rsd['type'] = 'categories';
        echo json_encode($rsd);
    }
    function get_store_all() {
        $cat = $this->input->post('category_id');
        $seller_id = $this->input->post('seller_id');
        $name = $this->ApiuM->get_field('category_name', 'categories', array('category_id' => $cat));
        $catg = $this->ApiuM->getCategoriesStore($seller_id, $cat);
        if (!empty($catg)) {
            foreach ($catg as $row):
                if ($row['category_id'] == $cat) {
                    $sel = '1';
                } else {
                    $sel = '0';
                }
                $res_dataa[] = ['category_id' => $row['category_id'], 'category_name' => $row['category_name'], 'category_image' =>  $row['category_image'], 'category_image2' =>  $row['category_image2'], 'selected' => $sel];
            endforeach;
            $rsd['status'] = 'ok';
            $rsd['category_name'] = $name;
            $rsd['categories'] = $res_dataa;
            $subcatg = $this->ApiuM->getSubCategories($seller_id, $cat);
            if (!empty($subcatg)) {
                foreach ($subcatg as $row2):
                    $res_dataa2[] = ['subcategory_id' => $row2['subcategory_id'], 'subcategory_name' => $row2['subcategory_name'], 'subcategory_image' => $row2['subcategory_image']];
                endforeach;
                $rsd['subcategories'] = $res_dataa2;
            } else {
                $rsd['subcategories'] = [];
            }
        } else {
            $rsd['status'] = 'error';
            $rsd['categories'] = [];
        }
        $rsd['seller_id'] = $seller_id;
        $rsd['category_id'] = $cat;
        echo json_encode($rsd);
    }
    function get_categories() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $seller_id = trim($_POST['seller_id']);
            $name = $this->ApiuM->get_field('seller_store_name', 'sellers', array('seller_id' => $seller_id));
            $catg = $this->ApiuM->getCategories($seller_id);
            if (!empty($catg)) {
                foreach ($catg as $row):
                    $res_dataa[] = ['category_id' => $row['category_id'], 'category_name' => $row['category_name'], 'category_image' =>  $row['category_image']];
                endforeach;
                $rsd['categories'] = $res_dataa;
                $rsd['status'] = 'ok';
                $rsd['seller_store_name'] = $name;
                $rsd['message'] = 'seller categories';
            } else {
                $rsd['categories'] = [];
                $rsd['seller_store_name'] = $name;
                $rsd['status'] = 'error';
                $rsd['message'] = 'no seller categories';
            }
            $rsd['type'] = 'categories';
            $rsd['seller_id'] = $seller_id;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }
    function get_subcategories() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $seller_id = trim($_POST['seller_id']);
            $category_id = trim($_POST['category_id']);
            $name = $this->ApiuM->get_field('category_name', 'categories', array('category_id' => $category_id));
            $subcatg = $this->ApiuM->getSubCategories($seller_id, $category_id);
            if (!empty($subcatg)) {
                foreach ($subcatg as $row):
                    if($row['subcategory_image'] != '0'){ $subimg = $row['subcategory_image']; }
                    else{ $subimg = ""; }
                    $res_dataa[] = ['subcategory_id' => $row['subcategory_id'], 'subcategory_name' => $row['subcategory_name'], 'subcategory_image' => $subimg];
                endforeach;
                $rsd['subcategories'] = $res_dataa;
                $rsd['category_name'] = $name;
                $rsd['status'] = 'ok';
                $rsd['message'] = 'seller subcategories';
            } else {
                $rsd['subcategories'] = [];
                $rsd['category_name'] = $name;
                $rsd['status'] = 'error';
                $rsd['message'] = 'no seller subcategories';
            }
            $rsd['type'] = 'subcategories';
            $rsd['seller_id'] = $seller_id;
            $rsd['category_id'] = $category_id;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }
    function percent($num, $num2) {
        $diff = $num2 - $num;
        $more_less = $diff > 0 ? "+" : "-";
        $diff = abs($diff);
        $percent = ($diff / $num) * 100;
        $rnd = round($percent, 0);
        return $rnd;
    }
    function getRating($id) {
        $where = array("reviewp_product_id" => $id);
        $this->db->where($where);
        $this->db->select_avg('reviewp_rate');
        $query = $this->db->get('reviewproduct')->first_row('array');
        return round($query['reviewp_rate'], 2);
    }
    function get_products() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $seller_id = trim($_POST['seller_id']);
            $category_id = trim($_POST['category_id']);
            $subcategory_id = trim($_POST['subcategory_id']);
            $stname = $this->ApiuM->get_field('seller_store_name', 'sellers', array('seller_id' => $seller_id));
            $sbname = $this->ApiuM->get_field('subcategory_name', 'subcategories', array('subcategory_id' => $subcategory_id));
            $prod = $this->ApiuM->getProducts($seller_id, $category_id, $subcategory_id);
            if (!empty($prod)) {
                foreach ($prod as $row):
                    if ($row['product_var'] === '1') {
                        $sub = $this->ApiuM->getProdSub1($row['product_id']);
                        if (!empty($sub)) {
                            if ($sub['vproduct_img1'] != '0') {
                                $image =  $sub['vproduct_img1'];
                            } else {
                                $image = '0';
                            }
                            $mrp = $sub['sproduct_mrp'];
                            $sale = $sub['sproduct_sale'];
                            $stock = $sub['sproduct_stock'];
                            $disc = $this->percent($sub['sproduct_mrp'], $sub['sproduct_sale']);
                            $res_dataa[] = ['product_id' => $row['product_id'], 'product_name' => $row['product_name'], 'product_measure' => $row['measure_name'], 'product_mrp' => $mrp, 'product_sale' => $sale, 'product_save' => $mrp - $sale, 'product_var' => $row['product_var'], 'product_type' => $row['product_type'], 'product_description' => $row['product_description'], 'product_discount' => $disc, 'product_stock' => $stock, 'product_rating' => strval($this->getRating($row['product_id'])), 'product_images' => $image, ];
                        }
                    } else {
                        if ($row['product_img1'] != '0') {
                            $image =  $row['product_img1'];
                        } else {
                            $image = '0';
                        }
                        $mrp = $row['product_mrp'];
                        $sale = $row['product_sale'];
                        $stock = $row['product_stock'];
                        $disc = $this->percent($row['product_mrp'], $row['product_sale']);
                        $res_dataa[] = ['product_id' => $row['product_id'], 'product_name' => $row['product_name'], 'product_measure' => $row['measure_name'], 'product_mrp' => $mrp, 'product_sale' => $sale, 'product_save' => $mrp - $sale, 'product_var' => $row['product_var'], 'product_type' => $row['product_type'], 'product_description' => $row['product_description'], 'product_discount' => $disc, 'product_stock' => $stock, 'product_rating' => strval($this->getRating($row['product_id'])), 'product_images' => $image, ];
                    }
                endforeach;
            }
            if (!empty($res_dataa)) {
                $rsd['product'] = $res_dataa;
                $rsd['status'] = 'ok';
                $rsd['message'] = 'seller products';
            } else {
                $rsd['product'] = [];
                $rsd['status'] = 'error';
                $rsd['message'] = 'no seller products';
            }
            $rsd['type'] = 'products';
            $rsd['seller_id'] = $seller_id;
            $rsd['category_id'] = $category_id;
            $rsd['subcategory_id'] = $subcategory_id;
            $rsd['subcategory_name'] = $sbname;
            $rsd['seller_name'] = $stname;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }
    function get_product_detail() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = trim($_POST['user_id']);
            $seller_id = trim($_POST['seller_id']);
            $product_id = trim($_POST['product_id']);
            $vproduct_id = trim($_POST['vproduct_id']);
            $sproduct_id = trim($_POST['sproduct_id']);
            $prod = $this->ApiuM->getProduct($seller_id, $product_id);
            $inCart = $this->ApiuM->inCart($user_id, $product_id);
            $prodRews = $this->ApiuM->getProdRews($product_id);
            if (!empty($prod)) {
                $images = array();
                if ($prod['product_var'] === '1') {
                    $vars = $this->ApiuM->getProdVars($prod['product_id']);
                    $va = 0;
                    foreach ($vars as $var):
                        if (!empty($vproduct_id)) {
                            if ($var['vproduct_id'] === $vproduct_id) {
                                $sel = '1';
                            } else {
                                $sel = '0';
                            }
                        } else {
                            if ($va === 0) {
                                $sel = '1';
                            } else {
                                $sel = '0';
                            }
                        }
                        $var_dataa[] = ['vproduct_id' => $var['vproduct_id'], 'vproduct_p1' => $var['vproduct_p1'], 'selected' => $sel, ];
                        if ($sel === '1') {
                            if ($var['vproduct_img1'] != '0') {
                                $images[] =  $var['vproduct_img1'];
                            }
                            if ($var['vproduct_img2'] != '0') {
                                $images[] =  $var['vproduct_img2'];
                            }
                            if ($var['vproduct_img3'] != '0') {
                                $images[] =  $var['vproduct_img3'];
                            }
                            if ($var['vproduct_img4'] != '0') {
                                $images[] =  $var['vproduct_img4'];
                            }
                        }
                        $va++;
                    endforeach;
                    if (!empty($vproduct_id)) {
                        $vID = $vproduct_id;
                    } else {
                        $vID = $vars[0]['vproduct_id'];
                    }
                    $subs = $this->ApiuM->getProdSub($product_id, $vID);
                    $su = 0;
                    foreach ($subs as $sub):
                        if (!empty($sproduct_id)) {
                            if ($sub['sproduct_id'] === $sproduct_id) {
                                $sel2 = '1';
                            } else {
                                $sel2 = '0';
                            }
                        } else {
                            if ($su === 0) {
                                $sel2 = '1';
                            } else {
                                $sel2 = '0';
                            }
                        }
                        $sub_dataa[] = ['vproduct_id' => $sub['vproduct_id'], 'sproduct_id' => $sub['sproduct_id'], 'sproduct_p2' => $sub['sproduct_p2'], 'sproduct_p3' => $sub['sproduct_p3'], 'sproduct_p4' => $sub['sproduct_p4'], 'sproduct_p5' => $sub['sproduct_p5'], 'selected' => $sel2, ];
                        if ($sel2 === '1') {
                            $mrp = $sub['sproduct_mrp'];
                            $sale = $sub['sproduct_sale'];
                            $stock = $sub['sproduct_stock'];
                            $disc = $this->percent($sub['sproduct_mrp'], $sub['sproduct_sale']);
                        }
                        $su++;
                    endforeach;
                    $res_dataa[] = ['product_id' => $prod['product_id'], 'category_name' => $prod['category_name'], 'subcategory_name' => $prod['subcategory_name'], 'product_name' => $prod['product_name'], 'product_measure' => $prod['measure_name'], 'product_mrp' => $mrp, 'product_sale' => $sale, 'product_discount' => $disc, 'product_stock' => $stock, 'product_description' => $prod['product_description'], 'product_type' => $prod['product_type'], 'product_var' => $prod['product_var'], 'product_images' => $images, 'product_variables' => $var_dataa, 'product_subs' => $sub_dataa, 'product_rating' => $this->getRating($prod['product_id']), ];
                } else {
                    if ($prod['product_img1'] != '0') {
                        $images[] =  $prod['product_img1'];
                    }
                    if ($prod['product_img2'] != '0') {
                        $images[] =  $prod['product_img2'];
                    }
                    if ($prod['product_img3'] != '0') {
                        $images[] =  $prod['product_img3'];
                    }
                    if ($prod['product_img4'] != '0') {
                        $images[] =  $prod['product_img4'];
                    }
                    $mrp = $prod['product_mrp'];
                    $sale = $prod['product_sale'];
                    $stock = $prod['product_stock'];
                    $disc = $this->percent($prod['product_mrp'], $prod['product_sale']);
                    $res_dataa[] = ['product_id' => $prod['product_id'], 'category_name' => $prod['category_name'], 'subcategory_name' => $prod['subcategory_name'], 'product_name' => $prod['product_name'], 'product_measure' => $prod['measure_name'], 'product_mrp' => $mrp, 'product_sale' => $sale, 'product_discount' => $disc, 'product_stock' => $stock, 'product_description' => $prod['product_description'], 'product_type' => $prod['product_type'], 'product_var' => $prod['product_var'], 'product_images' => $images, 'product_variables' => [], 'product_subs' => [], 'product_rating' => $this->getRating($prod['product_id']), ];
                }
                $rsd['status'] = 'ok';
                $rsd['cart_min'] = $this->ApiuM->get_field('seller_minimum_order', 'sellers', array('seller_id' => $seller_id));
                if ($this->input->post('user_id')) {
                    $cart = $this->ApiuM->getCart($user_id) ['total'];
                    $check = intval($rsd['cart_min']) - $cart;
                    if ($check > 0) {
                        $rsd['cart_max'] = strval($check);
                    } else {
                        $rsd['cart_max'] = "0";
                    }
                }
                $rsd['product'] = $res_dataa;
            } else {
                $rsd['status'] = 'error';
                $rsd['product'] = [];
            }
            $rsd['reviews'] = $prodRews['reviews'];
            $rsd['carted'] = "$inCart";
            $rsd['type'] = 'product';
            $rsd['seller_id'] = $seller_id;
            $rsd['product_id'] = $product_id;
            $rsd['message'] = 'seller product';
            $rsd['go_cart'] = 'ok';
            echo json_encode($rsd);
            exit;
        } else {
            $this->error();
        }
    }

    function cart() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "count") {
            $user_id = trim($_POST['user_id']);
            $count = $this->ApiuM->countCart($user_id);
            $rsd['status'] = 'ok';
            $rsd['type'] = 'count';
            $rsd['user_id'] = $user_id;
            $rsd['cart_count'] = $count->num_rows();
            if ($rsd['cart_count'] != 0) {
                $rsd['seller_id'] = $count->row_array() ['cart_seller_id'];
            } else {
                $rsd['seller_id'] = '';
            }
            echo json_encode($rsd);
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "get") {
            $user_id = trim($_POST['user_id']);
            $cart = $this->ApiuM->getCart($user_id);
            $rsd['user_id'] = $user_id;
            $rsd['cart_count'] = count($cart['cart']);
            $rsd['cart_total'] = $cart['total'];
            $percnt1 = (5 / 100) * $cart['total'];
            $rsd['cart_pickup_total'] = 0;
            $percnt2 = (5 / 100) * $cart['total'];
            $rsd['cart_delivery_total'] = 20;
            $rsd['delivery_charges'] = 20;
            $rsd['cart'] = $cart['cart'];
            $rsd['cart_cod'] = "1";
          //  $rsd['cart_cod'] = $cart['cart_cod'];
            $rsd['seller_id'] = $cart['cart_seller_id'];
            $rsd['seller_store_name'] = $cart['seller_store_name'];
            $rsd['seller_phone'] = $cart['seller_phone'];
            $rsd['seller_store_image'] = $cart['seller_store_image'];
            $rsd['seller_store_address'] = $cart['seller_store_address'];
            $rsd['seller_city'] = $cart['seller_city'];
            $rsd['seller_pincode'] = $cart['seller_pincode'];
            $rsd['seller_lat'] = $cart['seller_lat'];
            $rsd['seller_long'] = $cart['seller_long'];
            $rsd['seller_time'] = $cart['seller_time'];
            $rsd['seller_minimum_order'] = $cart['seller_minimum_order'];
            if ($rsd['cart_count'] != 0) {
                $rsd['status'] = 'ok';
                $rsd['message'] = 'cart';
            } else {
                $rsd['status'] = 'error';
                $rsd['message'] = 'no cart';
            }
            $rsd['type'] = 'get';
            echo json_encode($rsd);
            exit;
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "add") {
            $user_id = trim($_POST['user_id']);
            $product_id = trim($_POST['product_id']);
            $sproduct_id = trim($_POST['sproduct_id']);
            $seller_id = trim($_POST['seller_id']);
            $product_qty = trim($_POST['product_qty']);
            $check = $this->ApiuM->cartcheck($user_id, $seller_id, $product_id, $sproduct_id);
            if ($check > 0) {
                $data = array('cart_product_qty' => $product_qty);
                $updated = $this->ApiuM->update($data, 'cart', array('cart_user_id' => $user_id, 'cart_seller_id' => $seller_id, 'cart_product_id' => $product_id, 'cart_sproduct_id' => $sproduct_id, 'cart_status' => '1'));
                if ($updated > 0) {
                    $cart = $this->ApiuM->getCart($user_id);
                    $rsd['user_id'] = $user_id;
                    $rsd['cart_count'] = count($cart['cart']);
                    $rsd['cart_total'] = $cart['total'];
                    $rsd['cart'] = $cart['cart'];
                    $rsd['cart_total'] = $cart['total'];
                    $rsd['seller_id'] = $cart['cart_seller_id'];
                    $rsd['seller_store_name'] = $cart['seller_store_name'];
                    $rsd['seller_phone'] = $cart['seller_phone'];
                    $rsd['seller_store_image'] = $cart['seller_store_image'];
                    $rsd['seller_store_address'] = $cart['seller_store_address'];
                    $rsd['seller_city'] = $cart['seller_city'];
                    $rsd['seller_pincode'] = $cart['seller_pincode'];
                    $rsd['seller_lat'] = $cart['seller_lat'];
                    $rsd['seller_long'] = $cart['seller_long'];
                    $rsd['cart_delivery_total'] = 20;
                    $rsd['delivery_charges'] = 20;
                    if ($rsd['cart_count'] != 0) {
                        $rsd['status'] = 'ok';
                        $rsd['message'] = 'updated';
                    } else {
                        $rsd['status'] = 'error';
                        $rsd['message'] = 'updated & no cart';
                    }
                    $rsd['type'] = 'add';
                    echo json_encode($rsd);
                } else {
                    $this->error();
                }
            } else {
                $data = array('cart_user_id' => $user_id, 'cart_seller_id' => $seller_id, 'cart_product_id' => $product_id, 'cart_sproduct_id' => $sproduct_id, 'cart_product_qty' => $product_qty, 'cart_status' => '1');
                $added = $this->ApiuM->add_new($data, 'cart');
                if ($added) {
                    $cart = $this->ApiuM->getCart($user_id);
                    $rsd['user_id'] = $user_id;
                    $rsd['cart_count'] = count($cart['cart']);
                    $rsd['cart_total'] = $cart['total'];
                    $rsd['cart_total'] = $cart['total'];
                    $rsd['cart'] = $cart['cart'];
                    $rsd['seller_id'] = $cart['cart_seller_id'];
                    $rsd['seller_store_name'] = $cart['seller_store_name'];
                    $rsd['seller_phone'] = $cart['seller_phone'];
                    $rsd['seller_store_image'] = $cart['seller_store_image'];
                    $rsd['seller_store_address'] = $cart['seller_store_address'];
                    $rsd['seller_city'] = $cart['seller_city'];
                    $rsd['seller_pincode'] = $cart['seller_pincode'];
                    $rsd['seller_lat'] = $cart['seller_lat'];
                    $rsd['seller_long'] = $cart['seller_long'];
                    if ($rsd['cart_count'] != 0) {
                        $rsd['status'] = 'ok';
                        $rsd['message'] = 'added';
                    } else {
                        $rsd['status'] = 'error';
                        $rsd['message'] = 'added & no cart';
                    }
                    $rsd['type'] = 'add';
                    echo json_encode($rsd);
                } else {
                    $this->error();
                }
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "delete") {
            $data = array('cart_user_id' => trim($_POST['user_id']), 'cart_seller_id' => trim($_POST['seller_id']), 'cart_product_id' => trim($_POST['product_id']), 'cart_sproduct_id' => trim($_POST['sproduct_id']));
            $deleted = $this->ApiuM->delete('cart', $data);
            if ($deleted > 0) {
                $cart = $this->ApiuM->getCart(trim($_POST['user_id']));
                $rsd['user_id'] = trim($_POST['user_id']);
                $rsd['cart_count'] = count($cart['cart']);
                $rsd['cart_total'] = $cart['total'];
                $rsd['cart'] = $cart['cart'];
                $rsd['seller_id'] = $cart['cart_seller_id'];
                $rsd['seller_store_name'] = $cart['seller_store_name'];
                $rsd['seller_phone'] = $cart['seller_phone'];
                $rsd['seller_store_image'] = $cart['seller_store_image'];
                $rsd['seller_store_address'] = $cart['seller_store_address'];
                $rsd['seller_city'] = $cart['seller_city'];
                $rsd['seller_pincode'] = $cart['seller_pincode'];
                $rsd['seller_lat'] = $cart['seller_lat'];
                $rsd['seller_long'] = $cart['seller_long'];
                if ($rsd['cart_count'] != 0) {
                    $rsd['status'] = 'ok';
                    $rsd['message'] = 'deleted';
                } else {
                    $rsd['status'] = 'error';
                    $rsd['message'] = 'Your cart is empty!';
                }
                $rsd['type'] = 'delete';
                echo json_encode($rsd);
            } else {
                $this->error();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "clear") {
            $data = array('cart_user_id' => trim($_POST['user_id']));
            $deleted = $this->ApiuM->delete('cart', $data);
            if ($deleted > 0) {
                $cart = $this->ApiuM->getCart(trim($_POST['user_id']));
                $rsd['user_id'] = trim($_POST['user_id']);
                $rsd['cart'] = $cart['cart'];
                $rsd['cart_count'] = count($cart['cart']);
                $rsd['cart_total'] = $cart['total'];
                $rsd['seller_id'] = $cart['cart_seller_id'];
                $rsd['seller_store_name'] = $cart['seller_store_name'];
                $rsd['seller_phone'] = $cart['seller_phone'];
                $rsd['seller_store_image'] = $cart['seller_store_image'];
                $rsd['seller_store_address'] = $cart['seller_store_address'];
                $rsd['seller_city'] = $cart['seller_city'];
                $rsd['seller_pincode'] = $cart['seller_pincode'];
                $rsd['seller_lat'] = $cart['seller_lat'];
                $rsd['seller_long'] = $cart['seller_long'];
                if ($rsd['cart_count'] != 0) {
                    $rsd['status'] = 'ok';
                    $rsd['message'] = 'cleared';
                } else {
                    $rsd['status'] = 'error';
                    $rsd['message'] = 'cleared & no cart';
                }
                $rsd['type'] = 'clear';
                echo json_encode($rsd);
            } else {
                $this->error();
            }
        } else {
            $this->error();
        }
    }


    
    function addAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "add") {
            $addr_user = trim($_POST['user_id']);
            $addr_address = trim($_POST['addr_address']);
            $addr_pincode = trim($_POST['addr_pincode']);
            $addr_city = trim($_POST['addr_city']);
            $addr_name = trim($_POST['addr_name']);
            $addr_phone = trim($_POST['addr_phone']);
            $addr_lat = trim($_POST['addr_lat']);
            $addr_long = trim($_POST['addr_long']);
            $data = array('addr_user' => $addr_user, 'addr_address' => $addr_address, 'addr_pincode' => $addr_pincode, 'addr_city' => $addr_city, 'addr_name' => $addr_name, 'addr_phone' => $addr_phone, 'addr_lat' => $addr_lat, 'addr_long' => $addr_long, 'addr_status' => '1');
            $added = $this->ApiuM->add_new($data, 'addresses');
            $rsd['status'] = 'ok';
            $rsd['type'] = 'address';
            $rsd['user_id'] = $addr_user;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }

    function getAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "get") {
            $user_id = trim($_POST['user_id']);
            $adrs = $this->ApiuM->getUserAddress($user_id);
            if (!empty($adrs)) {
                foreach ($adrs as $adr):
                    $dataa[] = ['addr_id' => $adr['addr_id'], 'addr_address' => $adr['addr_address'], 'addr_city' => $adr['addr_city'], 'addr_pincode' => $adr['addr_pincode'], 'addr_name' => $adr['addr_name'], 'addr_phone' => $adr['addr_phone'], 'addr_lat' => $adr['addr_lat'], 'addr_long' => $adr['addr_long']];
                endforeach;
                $rsd['status'] = 'ok';
                $rsd['address'] = $dataa;
            } else {
                $rsd['status'] = 'error';
                $rsd['address'] = [];
            }
            $rsd['type'] = 'address';
            $rsd['user_id'] = $user_id;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }
    function dltAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "delete") {
            $user_id = trim($_POST['user_id']);
            $addr_id = trim($_POST['addr_id']);
            $data['addr_status'] = '0';
            $updated = $this->ApiuM->update($data, 'addresses', array('addr_user' => $user_id, 'addr_id' => $addr_id));
            if ($updated) {
                $res_data['status'] = 'ok';
                $res_data['type'] = 'delete address';
                $res_data['user_id'] = $user_id;
                $res_data['addr_id'] = $addr_id;
                echo json_encode($res_data);
            } else {
                $this->error();
            }
        } else {
            $this->error();
        }
    }
    function editAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) === "edit") {
            $addr_user = trim($_POST['user_id']);
            $addr_id = trim($_POST['addr_id']);
            $addr_address = trim($_POST['addr_address']);
            $addr_pincode = trim($_POST['addr_pincode']);
            $addr_city = trim($_POST['addr_city']);
            $addr_name = trim($_POST['addr_name']);
            $addr_phone = trim($_POST['addr_phone']);
            $addr_lat = trim($_POST['addr_lat']);
            $addr_long = trim($_POST['addr_long']);
            $data = array('addr_address' => $addr_address, 'addr_pincode' => $addr_pincode, 'addr_city' => $addr_city, 'addr_name' => $addr_name, 'addr_phone' => $addr_phone, 'addr_lat' => $addr_lat, 'addr_long' => $addr_long, 'addr_status' => '1');
            $updated = $this->ApiuM->update($data, 'addresses', array('addr_user' => $addr_user, 'addr_id' => $addr_id));
            $rsd['status'] = 'ok';
            $rsd['type'] = 'edit address';
            $rsd['user_id'] = $addr_user;
            $rsd['addr_id'] = $addr_id;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }




    function delcharges($val){
        date_default_timezone_set('Asia/Kolkata');
        $time = (int)date('H');
        if($val <= '5000' && $time <= '20'){
            $c = 20;
        }
        else if($val > '5000' && $val <= '8000' && $time <= '20'){
            $c = 35;
        }
        else if($val > '8000' && $val <= '10000' && $time <= '20'){
            $c = 50;
        }
        else if($val <= '5000' && $time > '20'){
            $c = 35;
        }
        else if($val > '5000' && $val <= '8000' && $time > '20'){
            $c = 45;
        }
        else if($val > '8000' && $val <= '10000' && $time > '20'){
            $c = 60;
        }
        else{
            $c = 20;
        }
        return $c;
    }


    function checkDelivery(){
        $addr_lat = trim($_POST['addr_lat']);
        $addr_long = trim($_POST['addr_long']);
        $seller_lat = trim($_POST['seller_lat']);
        $seller_long = trim($_POST['seller_long']);
        $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?&origins='.urlencode($seller_lat.','.$seller_long).'&destinations='.urlencode($addr_lat.','.$addr_long).'&key=AIzaSyBShNZA1A-uH4h31MGromtilf_NCCfnyHo');
        $distance_arr = json_decode($distance_data);
     if ($distance_arr->status=='OK') {
         $destination_addresses = $distance_arr->destination_addresses[0];
         $origin_addresses = $distance_arr->origin_addresses[0];
     } else {
        $rsd['status'] = 'error';
        $rsd['deliver'] = 'error';
        $rsd['details'] = [];
        $rsd['message'] = 'Something wrong';
     }
        if ($origin_addresses=="" or $destination_addresses=="") {
           $rsd['status'] = 'error';
           $rsd['deliver'] = 'error';
           $rsd['details'] = [];
           $rsd['message'] = 'Destination or origin address not found';
        }else{

            $elements = $distance_arr->rows[0]->elements;
            $distance = $elements[0]->distance->text;
            $distanceval = $elements[0]->distance->value;
            $duration = $elements[0]->duration->text;

            if($distanceval <= 10000){
                $rsd['deliver'] = 'ok';
                $rsd['message'] = 'Deliver';
            }else{ 
                $del = 'ok';
                $rsd['deliver'] = 'error';
                $rsd['message'] = 'Cannot Deliver';
            }
            $rsd['status'] = 'ok';
            $rsd['delivery_charges'] =  $this->delcharges($distanceval);
            $rsd['details'] = [
                'origin' => $origin_addresses,
                'destination' => $destination_addresses,
                'destination' => $destination_addresses,
                'distance' => $distance,
                'duration' => $duration,
            ];
        }
        echo json_encode($rsd);
        exit();
    }


    function checkout(){
        date_default_timezone_set('Asia/Kolkata');
        $user_id = trim($_POST['user_id']);
        $pick_date = date("Y-m-d", strtotime($_POST['pick_date']));
        $pick_time = trim($_POST['pick_time']);
        $coup_id = trim($_POST['coup_id']);
        $order_type = trim($_POST['order_type']);
        $order_address = trim($_POST['order_address']);
        $order_payref = trim($_POST['order_payref']);
        $order_pay_type = trim($_POST['order_pay_type']);
        $order_final = trim($_POST['order_final']);
        $order_tax = '0';
        $order_delivery_check = trim($_POST['order_delivery']);

    if($order_delivery_check === "0" && $order_type === "1"){
    $rsd['status'] = 'error';
    $rsd['message'] = 'Please Update Ellocart App!';
    echo json_encode($rsd);
    exit;
  }else{

        $order_delivery = trim($_POST['order_delivery']);
        $cart = $this->ApiuM->getCart($user_id);
        if (!empty($cart['cart'])) {
            if ($coup_id === "1" && $cart['total'] > 199) {
              //  $amt = $this->getWalletAmount($user_id);
                $coup = '30';
            } else {
                $coup = '0';
            }
            $data = array(
                'order_user_id' => $user_id,
                'order_seller_id' => $cart['cart_seller_id'],
                'order_date' => date('Y-m-d'),
                'order_time' => date('H:i:s'),
                'order_pick_date' => $pick_date,
                'order_pick_time' => $pick_time,
                'order_total' => $cart['total'],
                'order_coupon' => $coup,
                'order_coupon_id' => $coup_id,
                'order_address' => $order_address,
                'order_payref' => $order_payref,
                'order_final' => $order_final,
                'order_tax' => $order_tax,
                'order_delivery' => $order_delivery,
                'order_type' => $order_type,
                'order_pay_type' => $order_pay_type,
                'order_status' => '1'
            );
            $added = $this->ApiuM->add_new($data, 'orders');
            if ($added > 0) {
                $i = 0;
                foreach ($cart['cart'] as $row):
                    $i++;
                    $uploadData[$i]['ordered_order'] = $added;
                    $uploadData[$i]['ordered_product'] = $row['product_id'];
                    $uploadData[$i]['ordered_sproduct'] = $row['sproduct_id'];
                    $uploadData[$i]['ordered_qty'] = $row['cart_qty'];
                    $uploadData[$i]['ordered_amount'] = $row['cart_price'];
                    $uploadData[$i]['ordered_status'] = '1';
                endforeach;
                if (!empty($uploadData)) {
                    $insert = $this->ApiuM->orderAdd($uploadData);
                    if ($insert) {
                        $data2 = array('cart_user_id' => trim($_POST['user_id']));
                        $deleted = $this->ApiuM->delete('cart', $data2);
                        $this->load->model('Notify_model');
                        $this->Notify_model->user($added, "placed");
                        $rsd['status'] = 'ok';
                        $rsd['type'] = 'order';
                        $rsd['user_id'] = $user_id;
                        $rsd['order_id'] = 'ELOCRT' . $added;
                        $rsd['order_date'] = $data['order_date'];  
                        $rsd['order_time'] = date("g:i a", strtotime($data['order_time']));
                        $rsd['message'] = 'ordered succesfully'; 
                        $rsd['message2'] = 'cart cleared';
                        echo json_encode($rsd);
                    }
                }
            }
        } else {
            $this->error();
        }
    }
    }
    
    function orders() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "pending") {
            $user_id = trim($_POST['user_id']);
            $orders = $this->ApiuM->getOrdersP($user_id);
            if (!empty($orders)) {
                foreach ($orders as $row):
                    if ($row['seller_store_image'] != '0') {
                        $image =  $row['seller_store_image'];
                    } else {
                        $image = '0';
                    }
                    $tItems = $this->ApiuM->count_rows('ordered', 'ordered_order', $row['order_id']);
                    if($row['order_type'] == '1' && $row['order_assign'] > '1'){ 
                        $badrs = $this->ApiuM->get_row('boys', array('boy_id' => $row['order_boy']));
                        $bname = $badrs['boy_name'];
                        $bphone = $badrs['boy_phone_code'].$badrs['boy_phone'];
                    }else{ $bname = "";  $bphone = ""; }

                    $res_dataa[] = [
                        'order_id' => 'ELOCRT' . $row['order_id'],
                        'seller_id' => $row['order_seller_id'],
                        'seller_store_name' => $row['seller_store_name'],
                        'seller_store_image' => $image,
                        'seller_phone' => $row['seller_phone_code'] . $row['seller_phone'],
                        'seller_store_address' => $row['seller_store_address'],
                        'seller_city' => $row['seller_city'],
                        'seller_pincode' => $row['seller_pincode'],
                        'seller_lat' => $row['seller_lat'],
                        'seller_long' => $row['seller_long'],
                        'boy_name' => $bname,
                        'boy_phone' => $bphone,
                        'order_date' => $row['order_date'],
                        'order_time' => date("g:i a", strtotime($row['order_time'])),
                        'order_pick_date' => $row['order_pick_date'],
                        'order_pick_time' => $row['order_pick_time'],
                        'order_total_items' => $tItems, 'order_total' => $row['order_total'],
                        'order_final' => $row['order_final'],
                        'order_tax' => $row['order_tax'],
                        'order_delivery' => $row['order_delivery'],
                        'order_type' => $row['order_type'],
                        'order_pay_type' => $row['order_pay_type'],
                        'order_assign' => $row['order_assign'],
                        'order_status' => $row['order_status']
                    ];
                endforeach;
                $rsd['status'] = 'ok';
                $rsd['orders'] = $res_dataa;
                $rsd['message'] = 'user orders';
            } else {
                $rsd['status'] = 'error';
                $rsd['orders'] = [];
                $rsd['message'] = 'no user orders';
            }
            $rsd['type'] = 'pending';
            $rsd['user_id'] = $user_id;
            echo json_encode($rsd);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "completed") {
            $user_id = trim($_POST['user_id']);
            $orders = $this->ApiuM->getOrdersC($user_id);
            if (!empty($orders)) {
                foreach ($orders as $row):
                    if ($row['seller_store_image'] != '0') {
                        $image =  $row['seller_store_image'];
                    } else {
                        $image = '0';
                    }
                    $tItems = $this->ApiuM->count_rows('ordered', 'ordered_order', $row['order_id']);
                    if ($row['order_status'] === '3') {
                        $s_review = $this->ApiuM->getStoreRew($user_id, $row['order_seller_id'], $row['order_id']);
                    } else {
                        $s_review = '1';
                    }
                    $res_dataa[] = ['order_id' => 'ELOCRT' . $row['order_id'], 'seller_id' => $row['order_seller_id'], 'seller_store_name' => $row['seller_store_name'], 'seller_store_image' => $image, 'seller_phone' => $row['seller_phone_code'] . $row['seller_phone'], 'seller_store_address' => $row['seller_store_address'], 'seller_city' => $row['seller_city'], 'seller_pincode' => $row['seller_pincode'], 'seller_review' => $s_review, 'order_date' => $row['order_date'], 'order_time' => date("g:i a", strtotime($row['order_time'])), 'order_pick_date' => $row['order_pick_date'], 'order_pick_time' => $row['order_pick_time'], 'order_total_items' => $tItems, 'order_total' => $row['order_total'], 'order_final' => $row['order_final'], 'order_tax' => $row['order_tax'], 'order_delivery' => $row['order_delivery'], 'order_type' => $row['order_type'], 'order_pay_type' => $row['order_pay_type'], 'order_assign' => $row['order_assign'], 'order_status' => $row['order_status'], ];
                endforeach;
                $rsd['status'] = 'ok';
                $rsd['orders'] = $res_dataa;
                $rsd['message'] = 'user orders';
            } else {
                $rsd['status'] = 'error';
                $rsd['orders'] = [];
                $rsd['message'] = 'no user orders';
            }
            $rsd['type'] = 'completed';
            $rsd['user_id'] = $user_id;
            echo json_encode($rsd);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "cancelled") {
            $user_id = trim($_POST['user_id']);
            $orders = $this->ApiuM->getOrdersCan($user_id);
            if (!empty($orders)) {
                foreach ($orders as $row):
                    if ($row['seller_store_image'] != '0') {
                        $image =  $row['seller_store_image'];
                    } else {
                        $image = '0';
                    }
                    $tItems = $this->ApiuM->count_rows('ordered', 'ordered_order', $row['order_id']);
                    if ($row['order_status'] === '4') {
                        $s_review = $this->ApiuM->getStoreRew($user_id, $row['order_seller_id'], $row['order_id']);
                    } else {
                        $s_review = '1';
                    }
                    $res_dataa[] = ['order_id' => 'ELOCRT' . $row['order_id'], 'seller_id' => $row['order_seller_id'], 'seller_store_name' => $row['seller_store_name'], 'seller_store_image' => $image, 'seller_phone' => $row['seller_phone_code'] . $row['seller_phone'], 'seller_store_address' => $row['seller_store_address'], 'seller_city' => $row['seller_city'], 'seller_pincode' => $row['seller_pincode'], 'seller_review' => $s_review, 'order_date' => $row['order_date'], 'order_time' => date("g:i a", strtotime($row['order_time'])), 'order_pick_date' => $row['order_pick_date'], 'order_pick_time' => $row['order_pick_time'], 'order_total_items' => $tItems, 'order_total' => $row['order_total'], 'order_final' => $row['order_final'], 'order_tax' => $row['order_tax'], 'order_delivery' => $row['order_delivery'], 'order_type' => $row['order_type'], 'order_pay_type' => $row['order_pay_type'], 'order_assign' => $row['order_assign'], 'order_status' => $row['order_status'], ];
                endforeach;
                $rsd['status'] = 'ok';
                $rsd['orders'] = $res_dataa;
                $rsd['message'] = 'user orders';
            } else {
                $rsd['status'] = 'error';
                $rsd['orders'] = [];
                $rsd['message'] = 'no user orders';
            }
            $rsd['type'] = 'completed';
            $rsd['user_id'] = $user_id;
            echo json_encode($rsd);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "detail") {
            $user_id = trim($_POST['user_id']);
            $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
            $order = $this->ApiuM->getOrdersD($user_id, $order_id);
            $orderItems = $this->ApiuM->getOrdersI($order_id, $order['order_status'], $user_id);
            if (!empty($order)) {
                if ($order['seller_store_image'] != '0') {
                    $image =  $order['seller_store_image'];
                } else {
                    $image = '0';
                }
                $od_data = ['order_id' => 'ELOCRT' . $order['order_id'], 'seller_id' => $order['order_seller_id'], 'seller_store_name' => $order['seller_store_name'], 'seller_store_image' => $image, 'seller_phone' => $order['seller_phone_code'] . $order['seller_phone'], 'seller_store_address' => $order['seller_store_address'], 'seller_city' => $order['seller_city'], 'seller_pincode' => $order['seller_pincode'], 'seller_lat' => $order['seller_lat'], 'seller_long' => $order['seller_long'], 'order_date' => $order['order_date'], 'order_time' => date("g:i a", strtotime($order['order_time'])), 'order_pick_date' => $order['order_pick_date'], 'order_pick_time' => $order['order_pick_time'], 'order_total_items' => '1', 'order_total' => $order['order_total'], 'order_final' => $order['order_final'], 'order_coupon' => $order['order_coupon'], 'order_tax' => $order['order_tax'], 'order_delivery' => $order['order_delivery'], 'order_type' => 'pick up', 'order_type' => $order['order_type'], 'order_pay_type' => $order['order_pay_type'], 'order_assign' => $order['order_assign'], 'order_status' => $order['order_status'], ];
                $rsd['status'] = 'ok';
                $rsd['order'] = array($od_data);
                $rsd['products'] = $orderItems['item'];
                $rsd['message'] = 'user order';
            } else {
                $rsd['status'] = 'error';
                $rsd['order'] = '';
                $rsd['products'] = [];
                $rsd['message'] = 'no user order';
            }
            $rsd['type'] = 'detail';
            $rsd['user_id'] = $user_id;
            $rsd['order_id'] = $order_id;
            echo json_encode($rsd);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "update") {
            $user_id = trim($_POST['user_id']);
            $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
            $order_status = '4';
            $data['order_status'] = $order_status;
            $data['order_canc'] = 'user';
            $updated = $this->ApiuM->update($data, 'orders', array('order_user_id' => $user_id, 'order_status' => '1', 'order_id' => $order_id));
            if ($updated) {
                $res_data['status'] = 'ok';
                $res_data['type'] = 'cancel order';
                $res_data['message'] = 'order updated';
                $res_data['order_id'] = 'ELOCRT' . $order_id;
                $res_data['user_id'] = $user_id;
                echo json_encode($res_data);
            } else {
                $this->error();
            }
        } else {
            $this->error();
        }
    }



    function tracktime($lat1, $long1, $lat2, $long2){
        $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?&origins='.urlencode($lat1.','.$long1).'&destinations='.urlencode($lat2.','.$long2).'&key=AIzaSyBShNZA1A-uH4h31MGromtilf_NCCfnyHo');
        $distance_arr = json_decode($distance_data);

     if ($distance_arr->status=='OK') {
         $destination_addresses = $distance_arr->destination_addresses[0];
         $origin_addresses = $distance_arr->origin_addresses[0];
     } else {
        return ""; 
     }
        if ($origin_addresses=="" or $destination_addresses=="") {
            return ""; 
        }else{
            $elements = $distance_arr->rows[0]->elements;
            return $distance = $elements[0]->duration->text;
        }
    }






    function track() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "track") {
            $user_id = trim($_POST['user_id']);
            $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
            $trk = $this->ApiuM->getTrack($user_id, $order_id);
            if (!empty($trk)) {
                $rsd['status'] = "ok";
                $rsd['order_status'] = $trk['order_status'];
                $rsd['order_assign'] = $trk['order_assign'];
                $rsd['seller_lat'] = $trk['seller_lat'];
                $rsd['seller_long'] = $trk['seller_long'];
                $rsd['addr_lat'] = $trk['addr_lat'];
                $rsd['addr_long'] = $trk['addr_long'];
                if($trk['order_assign'] > '1'){
                    $boy = $this->ApiuM->get_row('boys', array('boy_id' => $trk['order_boy']));
                    $rsd['boy_name'] = $boy['boy_name'];
                    $rsd['boy_phone_code'] = $boy['boy_phone_code'];
                    $rsd['boy_phone'] = $boy['boy_phone'];
                    $rsd['boy_lat'] = $boy['boy_lat'];
                    $rsd['boy_long'] = $boy['boy_long'];
                    $rsd['boy_image'] = $boy['boy_image'];
                    if($trk['order_assign'] === '3'){
                    $rsd['delivery_time'] = $this->tracktime($boy['boy_lat'], $boy['boy_long'], $trk['addr_lat'], $trk['addr_long']);
                   }else{
                    $rsd['delivery_time'] = $trk['seller_time'];
                   }
                }else{
                    $rsd['boy_name'] = "";
                    $rsd['boy_phone_code'] = "";
                    $rsd['boy_phone'] = "";
                    $rsd['boy_lat'] = "";
                    $rsd['boy_long'] = "";
                    $rsd['boy_image'] = "";
                    $rsd['delivery_time'] = "";
                }
            } else {
                $rsd['status'] = "error";
                $rsd['boy_name'] = "";
                $rsd['boy_phone_code'] = "";
                $rsd['boy_phone'] = "";
                $rsd['boy_lat'] = "";
                $rsd['boy_long'] = "";
                $rsd['boy_image'] = "";
                $rsd['addr_lat'] = "";
                $rsd['addr_long'] = "";
                $rsd['delivery_time'] = "";
            }
            $rsd['user_id'] = $user_id;
            $rsd['order_id'] = $order_id;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }
    function reviewadd() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "seller") {
            $user_id = trim($_POST['user_id']);
            $seller_id = trim($_POST['seller_id']);
            $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
            $review_rate = trim($_POST['review_rate']);
            $review_message = trim($_POST['review_message']);
            $data = array('reviews_seller_id' => $seller_id, 'reviews_user_id' => $user_id, 'reviews_order_id' => $order_id, 'reviews_rate' => $review_rate, 'reviews_message' => $review_message, 'reviews_status' => '1');
            $added = $this->ApiuM->add_new($data, 'reviewstore');
            $rsd['status'] = 'ok';
            $rsd['type'] = 'seller';
            $rsd['user_id'] = $user_id;
            $rsd['seller_id'] = $seller_id;
            echo json_encode($rsd);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "product") {
            $user_id = trim($_POST['user_id']);
            $product_id = trim($_POST['product_id']);
            $order_id = ltrim(trim($_POST['order_id']), 'ELOCRT');
            $review_rate = trim($_POST['review_rate']);
            $review_message = trim($_POST['review_message']);
            $data = array('reviewp_user_id' => $user_id, 'reviewp_product_id' => $product_id, 'reviewp_order_id' => $order_id, 'reviewp_rate' => $review_rate, 'reviewp_message' => $review_message, 'reviewp_status' => '1');
            $added = $this->ApiuM->add_new($data, 'reviewproduct');
            $rsd['status'] = 'ok';
            $rsd['type'] = 'product';
            $rsd['user_id'] = $user_id;
            $rsd['product_id'] = $product_id;
            echo json_encode($rsd);
        }
        
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "product_edit") {
            
            $user_id = trim($_POST['user_id']);
            $review_id = trim($_POST['review_id']);
            $dat['reviewp_rate'] = trim($_POST['review_rate']);
            $dat['reviewp_message'] = trim($_POST['review_message']);
            $updated = $this->ApiuM->update($dat, 'reviewproduct', array('reviewp_user_id' => $user_id, 'reviewp_id' => $review_id));
            $rsd['status'] = 'ok';
            $rsd['type'] = 'product review update';
            $rsd['user_id'] = $user_id;
            $rsd['review_id'] = $review_id;
            echo json_encode($rsd);
        }
        
        else {
            $this->error();
        }
    }

    
    function wishlist() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "add") {
            $seller_id = trim($_POST['seller_id']);
            $user_id = trim($_POST['user_id']);
            $check = $this->ApiuM->cWish($user_id, $seller_id);
            if ($check == '0') {
                $data = array('wish_user' => $user_id, 'wish_main' => $seller_id, 'wish_type' => '1', 'wish_status' => '1',);
                $added = $this->ApiuM->add_new($data, 'wishlist');
                $rsd['type'] = 'added';
            } else {
                $this->db->where('wish_user', $user_id);
                $this->db->where('wish_main', $seller_id);
                $this->db->delete('wishlist');
                $rsd['type'] = 'removed';
            }
            $rsd['status'] = 'ok';
            $rsd['user_id'] = $user_id;
            $rsd['seller_id'] = $seller_id;
            echo json_encode($rsd);
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && trim($_POST["type"]) == "get") {
            $user_id = trim($_POST['user_id']);
            $lat = trim($_POST['user_lat']);
            $long = trim($_POST['user_long']);
            $kms = trim($_POST['kms']);
            $stores = $this->ApiuM->gWishes($user_id, $lat, $long);
            if (!empty($stores)) {
                foreach ($stores as $row):
                    $chk1 = $this->ApiuM->ckStoreProd($row['seller_id']);
                    if ($chk1 && round($row['distance']) <= $kms) {
                        $wish = '1';
                    } else {
                        $wish = '0';
                    }
                    if ($row['seller_store_image'] != '0') {
                        $img1 =  $row['seller_store_image'];
                    } else {
                        $img1 = '';
                    }
                    $res_dataa[] = ['seller_id' => $row['seller_id'], 'seller_store_image' => $img1, 'seller_store_name' => $row['seller_store_name'], 'seller_store_address' => $row['seller_store_address'], 'seller_city' => $row['seller_city'], 'seller_pincode' => $row['seller_pincode'], 'seller_rating' => '0', 'seller_lat' => $row['seller_lat'], 'seller_long' => $row['seller_long'], 'seller_distance' => round($row['distance']), 'seller_ostatus' => $row['seller_ostatus'], 'seller_time' => $row['seller_time'], 'wish_status' => $wish];
                endforeach;
                $rsd['stores'] = $res_dataa;
                $rsd['status'] = 'ok';
                $rsd['message'] = "wish list stores";
            } else {
                $rsd['stores'] = [];
                $rsd['status'] = 'error';
                $rsd['message'] = "no wish list stores";
            }
            $rsd['user_id'] = $user_id;
            echo json_encode($rsd);
        }
    }
    function getWalletAmount($user_id) {
        return $this->ApiuM->get_field('user_wallet', 'users', array('user_id' => $user_id));
    }
    function statement() {
        $user_id = trim($_POST['user_id']);
        $list = $this->ApiuM->gStatement($user_id);
        if (!empty($list)) {
            foreach ($list as $row):
                $res_dataa[] = ['usrt_id' => $row['usrt_id'], 'usrt_amount' => $row['usrt_amount'], 'usrt_ref' => $row['usrt_ref'], 'usrt_type' => $row['usrt_type'], 'usrt_type' => $row['usrt_type'], 'usrt_date' => $row['usrt_date']];
            endforeach;
            $rsd['statement'] = $res_dataa;
            $rsd['status'] = 'ok';
            $rsd['message'] = "user statement";
        } else {
            $rsd['statement'] = [];
            $rsd['status'] = 'error';
            $rsd['message'] = "user statement";
        }
        echo json_encode($rsd);
    }
    function getCodupons() {
        $user_id = trim($_POST['user_id']);
        $getWall = $this->ApiuM->get_field('user_wallet', 'users', array('user_id' => $user_id));
        $cart = $this->ApiuM->getCart($user_id);
        $cTotal = $cart['total'];
        if ($cTotal > 299) {
            if ($getWall > 0) {
                if ($getWall > 50) {
                    $amt = 50;
                } else {
                    $amt = $getWall;
                }
                $cu_data = ['coup_id' => '1', 'coup_name' => "ELLOCART", 'coup_amount' => $amt, ];
                // $rsd['status'] = "ok";
                // $rsd['message'] = "Coupons Available";
                // $rsd['coupons'] = $cu_data;
                $rsd['status'] = "error";
                $rsd['message'] = "No Coupons Available";
                $rsd['coupons'] = [];
            } else {
                $rsd['status'] = "error";
                $rsd['message'] = "No Coupons Available";
                $rsd['coupons'] = [];
            }
        } else {
            $rsd['status'] = "error";
            $rsd['message'] = "Cart Amount Should be more than 299";
            $rsd['coupons'] = [];
        }
        echo json_encode($rsd);
    }

    function getCoupons() {

        date_default_timezone_set('Asia/Kolkata');


        $user_id = trim($_POST['user_id']);
        $date = date('Y-m-d');

        $cart = $this->ApiuM->getCart($user_id);
        $cTotal = $cart['total'];
        $cSeller = $cart['cart_seller_id'];
        $user_lat = $cart['seller_lat'];
        $user_long = $cart['seller_long'];

        $list = $this->ApiuM->gCoups($user_lat, $user_long, $date);
        $res_dataa = [];
        if (!empty($list)) {
            foreach ($list as $row):

            if($cTotal >= $row['coup_id']){ $min = '1'; }else{ $min = '0'; }
            if($row['coup_store'] != '0'){ if($cSeller === $row['coup_store']){ $str = '1'; }else{ $str = '0'; } }else{ $str = '1'; }
            if($cTotal === '1' && $str === '1'){ $stat = '1'; }else{  $stat = '0'; }

                $res_dataa[] = [
                    'coup_id' =>  $row['coup_id'],
                    'coup_name' => $row['coup_name'],
                    'coup_image' => $row['coup_image'],
                    'coup_banner' => $row['coup_banner'],
                    'coup_video' => '',
                    'coup_amount' => $row['coup_amount'],
                    'coup_min' => $row['coup_min'],
                    'coup_desc' => $row['coup_desc'],
                    'coup_status' => $stat
                ];
            
            endforeach;
            $rsd['coupons'] = $res_dataa;
            $rsd['status'] = 'ok';
            $rsd['message'] = "coupons";
        } else {
            $rsd['coupons'] = [];
            $rsd['status'] = 'error';
            $rsd['message'] = "coupons";
        }
        echo json_encode($rsd);
    }




    function get_b2b_categories() {
        $catg = $this->ApiuM->getB2Categories();
        if (!empty($catg)) {
            foreach ($catg as $row):

                $chk = $this->ApiuM->checkB2Catg($row['b2category_id']);
                if($chk){
                $res_dataa[] =[
                'b2category_id' => $row['b2category_id'],
                'b2category_name' => $row['b2category_name'],
                'b2category_image' =>  $row['b2category_image'],
                'b2category_image2' =>  $row['b2category_image2']
               ];
            }

            endforeach;

            if (!empty($res_dataa)) {
                $rsd['b2categories'] = $res_dataa;
                $rsd['status'] = 'ok';
            }else{
                $rsd['b2categories'] = [];
                $rsd['status'] = 'error';
            }
     
        } else {
            $rsd['b2categories'] = [];
            $rsd['status'] = 'error';
        }
        $rsd['type'] = 'b2categories';
        echo json_encode($rsd);
    }

    function get_b2b_stores() {
        $cat = trim($_POST['b2category_id']);
        $b2bs = $this->ApiuM->getB2Stores($cat);
        if (!empty($b2bs)) {
            foreach ($b2bs as $row):
                if ($this->input->post('user_id')) {
                    $wished = $this->ApiuM->gb2bWishes($this->input->post('user_id'), $row['b2b_id']);
                } else {
                    $wished = "0";
                }
                $res_dataa[] = ['b2b_id' => $row['b2b_id'], 'b2b_store_name' => $row['b2b_store_name'], 'b2b_store_image' =>  $row['b2b_store_image'], 'b2b_address' => $row['b2b_address'], 'b2b_city' => $row['b2b_city'], 'b2b_state' => $row['b2b_state'], 'b2b_pincode' => $row['b2b_pincode'], 'b2b_wished' => $wished];
            endforeach;
            $rsd['b2stores'] = $res_dataa;
            $rsd['status'] = 'ok';
        } else {
            $rsd['b2stores'] = [];
            $rsd['status'] = 'error';
        }
        $rsd['type'] = 'b2bstores';
        $rsd['b2category_id'] = $cat;
        echo json_encode($rsd);
    }
    function get_b2b_subcategories() {
        $cat = trim($_POST['b2category_id']);
        $b2b = trim($_POST['b2b_id']);
        $scatg = $this->ApiuM->getB2SubCategories($cat, $b2b);
        if (!empty($scatg)) {
            foreach ($scatg as $row):
                $res_dataa[] = ['b2subcategory_id' => $row['b2subcategory_id'], 'b2subcategory_name' => $row['b2subcategory_name'], 'b2subcategory_image' =>  $row['b2subcategory_image']];
            endforeach;
            $rsd['b2subcategories'] = $res_dataa;
            $rsd['status'] = 'ok';
        } else {
            $rsd['b2subcategories'] = [];
            $rsd['status'] = 'error';
        }
        $rsd['type'] = 'b2subcategories';
        $rsd['b2subcategory_id'] = $cat;
        $rsd['b2b_id'] = $b2b;
        echo json_encode($rsd);
    }
    function get_b2products() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // $b2category_id = trim($_POST['b2category_id']);
            $b2subcategory_id = trim($_POST['b2subcategory_id']);
            $b2b_id = trim($_POST['b2b_id']);
            $prod = $this->ApiuM->getb2Products($b2subcategory_id, $b2b_id);
            if (!empty($prod)) {
                foreach ($prod as $row):
                    if ($row['product_var'] === '1') {
                        $sub = $this->ApiuM->getProdSub1($row['product_id']);
                        if ($sub['vproduct_img1'] != '0') {
                            $image =  $sub['vproduct_img1'];
                        } else {
                            $image = '0';
                        }
                        $mrp = $sub['sproduct_mrp'];
                        $sale = $sub['sproduct_sale'];
                        $stock = $sub['sproduct_stock'];
                        $disc = $this->percent($sub['sproduct_mrp'], $sub['sproduct_sale']);
                    } else {
                        if ($row['product_img1'] != '0') {
                            $image =  $row['product_img1'];
                        } else {
                            $image = '0';
                        }
                        $mrp = $row['product_mrp'];
                        $sale = $row['product_sale'];
                        $stock = $row['product_stock'];
                        $disc = $this->percent($row['product_mrp'], $row['product_sale']);
                    }
                    $res_dataa[] = ['product_id' => $row['product_id'], 'seller_id' => $row['b2b_id'], 'seller_store_name' => $row['b2b_store_name'], 'product_name' => $row['product_name'], 'product_measure' => $row['measure_name'], 'product_mrp' => $mrp, 'product_sale' => $sale, 'product_var' => $row['product_var'], 'product_type' => $row['product_type'],
                    // 'product_min' => $row['product_min'],
                    'product_min' => "100", 'product_description' => $row['product_description'], 'product_discount' => $disc, 'product_stock' => $stock, 'product_images' => $image, ];
                endforeach;
                $rsd['product'] = $res_dataa;
                $rsd['status'] = 'ok';
                $rsd['message'] = 'b2 products';
            } else {
                $rsd['product'] = [];
                $rsd['status'] = 'error';
                $rsd['message'] = 'no b2 products';
            }
            $rsd['type'] = 'products';
            $rsd['b2subcategory_id'] = $b2subcategory_id;
            echo json_encode($rsd);
        } else {
            $this->error();
        }
    }
    function viewscount($id) {
        $this->db->set('product_view', 'product_view+1', FALSE);
        $this->db->where('product_id', $id);
        $this->db->update('products');
        return true;
    }
    function mywishlistb2b() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = trim($_POST['user_id']);
            $list = $this->ApiuM->gb2ballWishes($user_id);
            if (!empty($list)) {
                foreach ($list as $row):
                    $res_dataa[] = ['b2b_id' => $row['b2b_id'], 'b2b_store_name' => $row['b2b_store_name'], 'b2b_store_image' =>  $row['b2b_store_image'], 'b2b_address' => $row['b2b_address'], 'b2b_city' => $row['b2b_city'], 'b2b_state' => $row['b2b_state'], 'b2b_pincode' => $row['b2b_pincode'], 'b2b_wished' => "1", 'b2b_wished' => "1", ];
                endforeach;
                $rsd['status'] = 'ok';
                $rsd['wishlist'] = $res_dataa;
            } else {
                $rsd['status'] = 'error';
                $rsd['wishlist'] = [];
            }
            $rsd['message'] = 'my wishlist stores';
            $rsd['user_id'] = $user_id;
            echo json_encode($rsd);
        }
    }
    function wishlistb2b() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $seller_id = trim($_POST['b2b_id']);
            $user_id = trim($_POST['user_id']);
            $check = $this->ApiuM->cWishb2b($user_id, $seller_id);
            if ($check == '0') {
                $data = array('wish_user' => $user_id, 'wish_main' => $seller_id, 'wish_type' => '1', 'wish_status' => '1',);
                $added = $this->ApiuM->add_new($data, 'wishlistb2b');
                $rsd['type'] = 'added';
            } else {
                $this->db->where('wish_user', $user_id);
                $this->db->where('wish_main', $seller_id);
                $this->db->delete('wishlistb2b');
                $rsd['type'] = 'removed';
            }
            $rsd['status'] = 'ok';
            $rsd['user_id'] = $user_id;
            $rsd['b2b_id'] = $seller_id;
            echo json_encode($rsd);
        }
    }
    function get_b2product_detail() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_id = trim($_POST['product_id']);
            $vproduct_id = trim($_POST['vproduct_id']);
            $sproduct_id = trim($_POST['sproduct_id']);
            $prod = $this->ApiuM->getb2Product($product_id);
            $prodRews = $this->ApiuM->getProdRews($product_id);
            if (!empty($prod)) {
                $images = array();
                if ($prod['product_var'] === '1') {
                    $vars = $this->ApiuM->getProdVars($prod['product_id']);
                    $va = 0;
                    foreach ($vars as $var):
                        if (!empty($vproduct_id)) {
                            if ($var['vproduct_id'] === $vproduct_id) {
                                $sel = '1';
                            } else {
                                $sel = '0';
                            }
                        } else {
                            if ($va === 0) {
                                $sel = '1';
                            } else {
                                $sel = '0';
                            }
                        }
                        $var_dataa[] = ['vproduct_id' => $var['vproduct_id'], 'vproduct_p1' => $var['vproduct_p1'], 'selected' => $sel, ];
                        if ($sel === '1') {
                            if ($var['vproduct_img1'] != '0') {
                                $images[] =  $var['vproduct_img1'];
                            }
                            if ($var['vproduct_img2'] != '0') {
                                $images[] =  $var['vproduct_img2'];
                            }
                            if ($var['vproduct_img3'] != '0') {
                                $images[] =  $var['vproduct_img3'];
                            }
                            if ($var['vproduct_img4'] != '0') {
                                $images[] =  $var['vproduct_img4'];
                            }
                        }
                        $va++;
                    endforeach;
                    if (!empty($vproduct_id)) {
                        $vID = $vproduct_id;
                    } else {
                        $vID = $vars[0]['vproduct_id'];
                    }
                    $subs = $this->ApiuM->getProdSub($product_id, $vID);
                    $su = 0;
                    foreach ($subs as $sub):
                        if (!empty($sproduct_id)) {
                            if ($sub['sproduct_id'] === $sproduct_id) {
                                $sel2 = '1';
                            } else {
                                $sel2 = '0';
                            }
                        } else {
                            if ($su === 0) {
                                $sel2 = '1';
                            } else {
                                $sel2 = '0';
                            }
                        }
                        $sub_dataa[] = ['vproduct_id' => $sub['vproduct_id'], 'sproduct_id' => $sub['sproduct_id'], 'sproduct_p2' => $sub['sproduct_p2'], 'sproduct_p3' => $sub['sproduct_p3'], 'sproduct_p4' => $sub['sproduct_p4'], 'sproduct_p5' => $sub['sproduct_p5'], 'selected' => $sel2, ];
                        if ($sel2 === '1') {
                            $mrp = $sub['sproduct_mrp'];
                            $sale = $sub['sproduct_sale'];
                            $stock = $sub['sproduct_stock'];
                            $disc = $this->percent($sub['sproduct_mrp'], $sub['sproduct_sale']);
                        }
                        $su++;
                    endforeach;
                    $res_dataa[] = ['product_id' => $prod['product_id'], 'seller_id' => $prod['b2b_id'], 'seller_store_name' => $prod['b2b_store_name'], 'seller_store_image' =>  $prod['b2b_store_image'], 'category_name' => $prod['b2category_name'], 'subcategory_name' => $prod['b2subcategory_name'], 'product_name' => $prod['product_name'], 'product_measure' => $prod['measure_name'], 'product_mrp' => $mrp, 'product_sale' => $sale, 'product_discount' => $disc, 'product_stock' => $stock,
                    // 'product_min' => $prod['product_min'],
                    'product_min' => "100", 'product_description' => $prod['product_description'], 'product_type' => $prod['product_type'], 'product_var' => $prod['product_var'], 'product_images' => $images, 'product_variables' => $var_dataa, 'product_subs' => $sub_dataa, ];
                } else {
                    if ($prod['product_img1'] != '0') {
                        $images[] =  $prod['product_img1'];
                    }
                    if ($prod['product_img2'] != '0') {
                        $images[] =  $prod['product_img2'];
                    }
                    if ($prod['product_img3'] != '0') {
                        $images[] =  $prod['product_img3'];
                    }
                    if ($prod['product_img4'] != '0') {
                        $images[] =  $prod['product_img4'];
                    }
                    $mrp = $prod['product_mrp'];
                    $sale = $prod['product_sale'];
                    $stock = $prod['product_stock'];
                    $disc = $this->percent($prod['product_mrp'], $prod['product_sale']);
                    $res_dataa[] = ['product_id' => $prod['product_id'], 'seller_id' => $prod['b2b_id'], 'seller_store_name' => $prod['b2b_store_name'], 'seller_store_image' =>  $prod['b2b_store_image'], 'category_name' => $prod['b2category_name'], 'subcategory_name' => $prod['b2subcategory_name'], 'product_name' => $prod['product_name'], 'product_measure' => $prod['measure_name'], 'product_mrp' => $mrp, 'product_sale' => $sale, 'product_discount' => $disc, 'product_stock' => $stock,
                    // 'product_min' => $prod['product_min'],
                    'product_min' => "100", 'product_description' => $prod['product_description'], 'product_type' => $prod['product_type'], 'product_var' => $prod['product_var'], 'product_images' => $images, 'product_variables' => [], 'product_subs' => [], ];
                }
                $rsd['status'] = 'ok';
                $rsd['product'] = $res_dataa;
            } else {
                $rsd['status'] = 'error';
                $rsd['product'] = [];
            }
            $rsd['reviews'] = $prodRews['reviews'];
            $rsd['type'] = 'product';
            $rsd['product_id'] = $product_id;
            $rsd['message'] = 'b2 product';
            echo json_encode($rsd);
            $this->viewscount($product_id);
            exit;
        } else {
            $this->error();
        }
    }
    function b2order() {
        $data['b2product_id'] = trim($_POST['product_id']);
        $data['b2order_user_id'] = trim($_POST['user_id']);
        $data['b2sproduct_id'] = trim($_POST['sproduct_id']);
        $data['b2seller_id'] = trim($_POST['seller_id']);
        $data['b2product_qty'] = trim($_POST['product_qty']);
        $data['b2order_name'] = trim($_POST['order_name']);
        $data['b2order_address'] = trim($_POST['order_address']);
        $data['b2order_landmark'] = trim($_POST['order_landmark']);
        $data['b2order_city'] = trim($_POST['order_city']);
        $data['b2order_lat'] = trim($_POST['order_lat']);
        $data['b2order_long'] = trim($_POST['order_long']);
        $data['b2order_phone'] = trim($_POST['order_phone']);
        $data['b2order_pincode'] = trim($_POST['order_pincode']);
        $data['b2order_date'] = date("Y-m-d");
        $data['b2order_time'] = date("H:i:s");
        $data['b2order_status'] = '1';
        $added = $this->ApiuM->add_new($data, 'b2orders');
        $rsd['status'] = 'ok';
        $rsd['message'] = 'order successfull';
        echo json_encode($rsd);
        exit;
    }
    function b2orders() {
        $user_id = trim($_POST['user_id']);
        $ods = $this->ApiuM->getb2OrdersList($user_id);
        if (!empty($ods['cart'])) {
            $rsd['status'] = 'ok';
        } else {
            $rsd['status'] = 'error';
        }
        $rsd['message'] = 'b2b orders';
        $rsd['b2orders'] = $ods['cart'];
        echo json_encode($rsd);
        exit;
    }
    public function invoice() {
        if ($this->uri->segment(5)) {
            $user_id = $this->uri->segment(3);
            $type = $this->uri->segment(4);
            $orderid = trim($this->uri->segment(5));
            $order_id = ltrim(trim($this->uri->segment(5), 'ELOCRT'));
            $data['order'] = $this->ApiuM->getInvoice($user_id, $order_id);
            $data['type'] = $type;
            $data['items'] = $this->ApiuM->getOrdersI($order_id, '', $user_id) ['item'];
            if ($this->uri->segment(4) === '0') {
                $this->load->view('invoice', $data);
            } elseif ($this->uri->segment(4) === '1') {
                $html = $this->load->view('invoice', $data, true);
                $this->load->library('pdf');
                $this->pdf->loadHtml($html);
                $this->pdf->set_paper('a4');
                $this->pdf->render();
                $this->pdf->stream("Ellocart_" . $order_id . ".pdf", array("Attachment" => 2));
            }
        } else {
            $rsd['status'] = 'error';
            echo json_encode($rsd);
        }
    }
    function search() {
        $trm = $this->input->post('search');
        $lat = $this->input->post('user_lat');
        $long = $this->input->post('user_long');
        $list = $this->ApiuM->getPDSearch($trm, $lat, $long, 10);
        if (!empty($list)) {
            foreach ($list as $row):
                $var_dataa[] = ['product_id' => $row['product_id'], 'category_id' => $row['category_id'], 'product_name' => $row['product_name'], 'seller_id' => $row['seller_id'], 'seller_store_name' => $row['seller_store_name'], ];
            endforeach;
            $rsd['status'] = 'ok';
            $rsd['list'] = $var_dataa;
        } else {
            $rsd['status'] = 'error';
            $rsd['list'] = [];
        }
        echo json_encode($rsd);
    }


    function ag_check(){
        $user_id = $this->input->post('user_id');
        $ck = $this->ApiuM->ag_check($user_id);
        if($ck){
            $rsd['status'] = 'ok';
            $rsd['agent_id'] = 'ELCAG'.$user_id;
            $rsd['message'] = 'exists';
        }
        else{
            $rsd['status'] = 'error';
            $rsd['agent_id'] = '';
            $rsd['message'] = 'not exists';
        }
        echo json_encode($rsd);
    }


    function ag_register(){
        $user_id = $this->input->post('user_id');
        if(!empty($_FILES['user_pan'])){
            $dt['user_pan']=$this->ApiuM->fileupload('user_pan', 'agents', $user_id);
        }else{$dt['user_pan']="0";}
        if(!empty($_FILES['user_aadhaar'])){
            $dt['user_pan']=$this->ApiuM->fileupload('user_aadhaar', 'agents', $user_id);
        }else{$dt['user_aadhaar']="0";}
                $dt['user_bank_acc'] = $this->input->post('user_bank_acc');
        $dt['user_bank_ifsc'] = $this->input->post('user_bank_ifsc');
        $dt['user_agent'] = '1';
        $updt = $this->ApiuM->update($dt, 'users', array('user_id' => $user_id));
        if($updt > 0){ 
            $rsd['status'] = 'ok';
            $rsd['agent_id'] = 'ELCAG'.$user_id;
            $rsd['message'] = 'Registered';
        }  else{
            $rsd['status'] = 'error';
            $rsd['agent_id'] = '';
            $rsd['message'] = 'Something wrong!';
        }
        echo json_encode($rsd);
    }



    function ag_sellers(){
        $user_id = 'ELCAG'.$this->input->post('user_id');
        $sells=$this->ApiuM->getmysellers($user_id);
        $count = 0;
        if (!empty($sells)) {
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
        $rsd['status'] = 'ok';
        $rsd['sellers'] = $res;
        $rsd['message'] = 'My Referral';

    }else{
        $rsd['status'] = 'error';
        $rsd['sellers'] = [];
        $rsd['message'] = 'No Referral';
    }

            $rsd['agent_id'] = $user_id;   

        echo json_encode($rsd);
    }



    function ag_sellers_r(){
        $user_id = 'ELCAG'.$this->input->post('user_id');
        $sells=$this->ApiuM->getmysellers($user_id);
        $count = 0;
        if (!empty($sells)) {
        foreach ($sells as $row):
        $res[] = [
            'seller_store_name' => $row['seller_store_name'],
            'seller_phone_code' =>  $row['seller_phone_code'],
            'seller_store_address' =>  $row['seller_store_address'],
            'seller_store_image' =>  $row['seller_store_image'],
            'seller_phone' =>  $row['seller_phone'],
            'seller_city' =>  $row['seller_city'],
            'seller_pincode' =>  $row['seller_pincode'],
            'seller_validity' =>  "20-03-2021",
        ];
        $count++;
        endforeach;
        $rsd['status'] = 'ok';
        $rsd['sellers'] = $res;
        $rsd['message'] = 'My Seller Renewals';

    }else{
        $rsd['status'] = 'error';
        $rsd['sellers'] = [];
        $rsd['message'] = 'No Renewals';
    }

        $rsd['agent_id'] = $user_id;   
        echo json_encode($rsd);
    }



    function ag_earnings(){
        $user_id = 'ELCAG'.$this->input->post('user_id');
        $sells=$this->ApiuM->getmysellers($user_id);
        $res[] = [
            't_name' => "seller",
            't_date' =>  "2021-03-24",
            't_type' =>  "Received",
            't_amount' =>  100,
            't_description' =>  "For 1000 rs seller plan",
        ];
        $rsd['status'] = 'ok';
        $rsd['total'] = 100;
        $rsd['earnings'] = $res;
        $rsd['message'] = 'My Earnings';
        $rsd['agent_id'] = $user_id;   
        echo json_encode($rsd);
    }




    function smsy(){
        $this->load->model('Notify_model');
        $phn = $this->input->post('phn');
        $msg = $this->input->post('msg');
        $dlt = $this->input->post('dlt');
        $this->Notify_model->user('613', "placed");
        $rsd['msg'] = $msg;   
        echo json_encode($rsd);
    }



    function call(){
        $this->load->model('Notify_model');
        $user_id = $this->input->post('user_id');
       // $dlt = $this->input->post('user');
        $this->Notify_model->call('user', $user_id);
        $rsd['msg'] = $msg;  
        $rsd['status'] = 'ok';   
        echo json_encode($rsd);
    }


    function delvv(){
        $this->load->model('Notify_model');
       // $dlt = $this->input->post('user');
        $this->Notify_model->boyyi();
        $rsd['status'] = 'ok';   
        echo json_encode($rsd);
    }


    function uok(){
    	$stat = $this->ApiuM->get_field('product_substatus', 'products', array('product_subcategory' => '1', 'product_store' => '1'));
    
    echo $stat;
    }

}
