<?php	if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * PHP 5
 *
 * Application System Environment (X-ASE)
 * laxono :  Rapid Development Framework (http://www.laxono.us)
 * Copyright 2011-2015.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource email.php
 * @copyright Copyright 2011-2015, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Aug 29, 2015
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

$config['protocol'] = 'smtp';
$config['mailpath'] = '/usr/sbin/sendmail';
//$config['mailpath'] = 'C:/xampp/sendmail/sendmail.exe';
$config['charset'] = 'iso-8859-1';
//$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;

$config['smtp_host'] = 'smtp.gmail.com';
$config['smtp_port'] = '465';
$config['smtp_user'] = 'rsudbalaraja.eoffice@gmail.com';
$config['smtp_pass'] = 'Eoffice698!';
$config['smtp_timeout']= '10';
//$config['smtp_crypto']= 'tls';
$config['smtp_auth']= TRUE; 
$config['priority'] = 3;
$config['validate'] = TRUE;
//$config['charset'] = 'utf-8';

$config['mailtype'] = "html";
$config['newline'] = "\r\n";
$config['crlf'] = "\r\n";

/*$config = Array(
      'protocol' => 'smtp',
      'smtp_host' => 'smtp.googlemail.com',
      'smtp_port' => 465,
      'smtp_user' => 'eoffice.ham@gmail.com',
      'smtp_pass' => 'buanavaria102938',
      'smtp_timeout' => '10',
      'charset' => 'iso-8859-1',
      'smtp_crypto' => 'tls',
    );*/

/**
 * End of file email.php 
 * Location: ./.../.../.../email.php 
 */