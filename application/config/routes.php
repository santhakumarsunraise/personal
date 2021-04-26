<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'main/home';
$route['home'] = 'main/home';
$route['about'] = 'main/about';
$route['contact'] = 'main/contact';
$route['faqs'] = 'main/faqs';
$route['privacy'] = 'main/privacy';
$route['terms'] = 'main/terms';
$route['careers'] = 'main/careers';
$route['cancellation'] = 'main/cancellation';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;