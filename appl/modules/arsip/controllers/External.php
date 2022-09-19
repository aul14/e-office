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

class External extends LX_Controller {
	
// 	var $func_eks_masuk = 1;
// 	var $func_eks_keluar = 2;
	
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
		
		$this->load->model(array('auth/user_model', 'global/admin_model', 'arsip_model'));
		
// 		$function_ref = $this->admin_model->get_function_ref('external/incoming');
// 		$this->func_eks_masuk = $function_ref->function_ref_id;
		
// 		$function_ref = $this->admin_model->get_function_ref('external/outgoing');
// 		$this->func_eks_keluar = $function_ref->function_ref_id;
	}
	
	/**
	 * Function to handle request from javascript ajax
	 * do nothing
	 */
	function index() {
		$this->list_arsip('surat_masuk_eksternal');
	}

	/**
	 * 
	 */
	function incoming() {
		$list = $this->arsip_model->get_my_list('surat_masuk_eksternal');
		$this->output_data['list'] = $list;

		$this->output_data['title'] = 'Surat Masuk Eksternal';
		$this->output_data['type'] = 'surat_masuk_eksternal';

		$this->output_head['search_type'] = 'surat_masuk_eksternal';
		$this->list_arsip('surat_masuk_eksternal');
	}
	
	/**
	 * 
	 */
	function outgoing() {
		$list = $this->arsip_model->get_my_list('surat_eksternal_keluar');
		$this->output_data['list'] = $list;

		$this->output_data['title'] = 'Surat Keluar Eksternal';
		$this->output_data['type'] = 'surat_eksternal_keluar';

		$this->output_head['search_type'] = 'surat_eksternal_keluar';
		$this->list_arsip('surat_eksternal_keluar');
	}

	/**
	 * @param unknown $type
	 */
	function list_arsip($type) {

		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
													assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);
		
		$this->load->view('my_archive', $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	/**
	 * 
	 */
	function set_arsip($surat_id) {
		$result = $this->arsip_model->get_ref_data($type, $ref_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$ref = $result->row();
		
		$this->output_data['ref'] = $ref;
		$this->output_data['function_ref_id'] = $this->func_disposisi;
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
				'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
				assets_url() . '/plugins/select2/select2.min.css'
		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
				assets_url() . '/plugins/select2/select2.full.min.js'
		);
		
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = '';	//'Surat Masuk Eksternal';
		$this->output_data['type'] = $type;
		$this->output_data['ref_id'] = $ref_id;
		$this->output_data['parent_id'] = $parent_id;
		
		$this->load->view('arsip_form_add', $this->output_data);

		$this->load->view('global/footer');
		
	}
	
}

/**
 * End of file
 */