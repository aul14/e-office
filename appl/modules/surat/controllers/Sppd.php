<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @filesource External.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Sep 22, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Sppd extends LX_Controller {

	 var $func_sppd = 17;
	/**
	 * Enter description here ...
	 */
	 
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
		
		if(!defined('__DIR__') ) define('__DIR__', dirname(__FILE__));
		$dir = explode('/', str_replace('\\', '/', __DIR__));
		$module = end($dir); 

		$this->output_head = array('class' => strtolower(__CLASS__), 'module' => strtolower($module));
		
		$this->load->model(array('auth/user_model', 'global/admin_model', 'surat_model', 'tugas_model','disposisi_model', 'sppd_model'));
	
	}
	/**
	 * Function to handle request from javascript ajax
	 * do nothing
	 */
	function index() {
		$this->sppd();
		
	}
	
	function sppd($surat_id = NULL) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
													assets_url() . '/plugins/select2/select2.min.css',
													assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css'

		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
												assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
												assets_url() . '/plugins/select2/select2.full.min.js'

		);
		
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_sppd';
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['title'] = 'Sppd';
		$this->output_head['search_type'] = 'surat_sppd';
		$this->output_data['function_ref_id'] = $this->func_sppd;

		
		if($surat_id == NULL) {
			$this->load->view('sppd_form_add', $this->output_data);	
		}
		
		$this->load->view('global/footer');
	}

	
	
}
	

/**
 * End of file
 */