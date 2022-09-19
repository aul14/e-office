<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * PHP 5
 *
 * Application System Environment (X-ASE)
 * laxono :  Rapid Development Framework (http://www.laxono.us)
 * Copyright 2011-2013.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource xconf.php
 * @copyright Copyright 2011-2013, laxono.us.
 * @author budi.lx
 * @package 
 * @subpackage	
 * @since Jan 01, 2013
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */
$config['site_logo'] = '';

$config['site_title'] = 'Welcome';

$config['site_name'] = 'Base CMS';

$config['company_name'] = 'Base CMS';

$config['company_address'] = '';

$config['company_phone'] = '';

$config['company_fax'] = '';

$config['lx_footer_text'] = '&copy; 2015 laxono.us';

$config['lx_home_url'] = 'home';

$config['lx_admin_url'] = 'system/dashboard';

/*
|--------------------------------------------------------------------------
| 
|--------------------------------------------------------------------------
|
| users
| lx_user_activation = 'auto', 'email', 'admin'
|
*/

$config['lx_enable_user_register'] = TRUE;
$config['lx_user_activation'] = 'auto';

/*
|--------------------------------------------------------------------------
| 
|--------------------------------------------------------------------------
|
| default
*/

$config['cache_time'] = 0;

$config['lx_def_password'] = 'password';
$config['lx_upload_path'] = 'assets/media/';

$config['lx_finder_basePath'] = '../../assets/js/ckfinder';
$config['lx_upload_path'] = 'assets/media/';

/*
|--------------------------------------------------------------------------
| 
|--------------------------------------------------------------------------
|
| Languages
*/

$config['lx_def_language'] = 'en';
$config['lx_languages']['en'] = array('label' => 'English', 'active' => TRUE);
$config['lx_languages']['id'] = array('label' => 'Indonesia', 'active' => TRUE);

/*
|--------------------------------------------------------------------------
| 
|--------------------------------------------------------------------------
|
| Fields Option
*/

$config['lx_fields']['input'] = array('label' => 'Input', 
										'fields' => array()
								);

$config['lx_fields']['dropdown'] = array('label' => 'Dropdown', 
										'fields' => array()
								);

$config['lx_fields']['textarea'] = array('label' => 'Textarea', 
										'fields' => array()
								);

$config['lx_fields']['editor'] = array('label' => 'Editor', 
										'fields' => array()
								);

$config['lx_fields']['date'] = array('label' => 'Date', 
										'fields' => array()
								);

$config['lx_fields']['datetime'] = array('label' => 'Date Time', 
										'fields' => array()
								);

$config['lx_fields']['file'] = array('label' => 'File', 
										'fields' => array()
								);
								
/**
 * End of file
 */