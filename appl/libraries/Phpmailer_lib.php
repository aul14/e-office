<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PHPMailer_Lib
{
    public function __construct()
    {
        log_message('Debug', 'PHPMailer class is loaded.');
    }

    public function load()
    {
        require_once(APPPATH . "third_party/PHPMailer/PHPMailerAutoload.php");
        $objMail = new PHPMailer;
        return $objMail;
    }
}
