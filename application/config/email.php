<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

//Set default e-mail settings
$config = array(
    'protocol' => 'smtp',
    'smtp_host' => 'smtp.antispamcloud.com', 
    'smtp_port' => 587,
    'smtp_user' => 'smtp@beutech.nl',
    'smtp_pass' => 'Pv83C31!#',
    'smtp_crypto' => 'tls', 
    'mailtype' => 'html', 
    'smtp_timeout' => '4', 
    'charset' => 'UTF-8',
    'wordwrap' => TRUE
);
?>