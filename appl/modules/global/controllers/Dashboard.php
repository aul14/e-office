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
 * @filesource Dashboard.php
 * @copyright Copyright 2011-2015, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Aug 14, 2015
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Dashboard extends LX_Controller {
	
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
		
		$this->load->model(array('dashboard_model', 'admin_model', 'auth/user_model'));
		$this->output_head['search_type'] = 'global';
	}
	
	/**
	 * Enter description here ...
	 */
	function index() {
		
		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(//assets_url() . '/plugins/primitives/jquerylayout/layout-default-latest.css',
													assets_url() . '/plugins/primitives/css/primitives.latest.css?3600'
		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/primitives/js/json3.min.js',
												assets_url() . '/plugins/primitives/jquerylayout/jquery.layout-latest.min.js',
												assets_url() . '/plugins/primitives/js/primitives.min.js?3600'
		);
		
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
		
//		$list = $this->dashboard_model->get_my_notification();
//		$this->output_data['notification_list'] = $list;
		
		$this->output_data['title'] = 'Dashboard ';
		$this->load->view('dashboard', $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	/**
	 * 
	 */
	function search_keywords() {
		
		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
													assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Search Result ';
		$this->output_data['search_keyword'] = $_POST['search_keyword'];
		$list = $this->dashboard_model->get_keyword_result($_POST['search_keyword'], $_POST['search_type']);
		$this->output_data['list'] = $list;
		
		$this->load->view('search_keywords_result', $this->output_data);
		
		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $type
	 */
	function workspace($type) {

		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
													assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = $type;
		
		$this->load->view('global/header', $this->output_head);

		$list = $this->dashboard_model->get_my_list($type);
		$this->output_data['list'] = $list;
		$this->output_data['type'] = $type;
		
		switch ($type) {
			case 'surat_masuk_eksternal' :
				$this->output_data['title'] = 'Surat Masuk Eksternal';
			break;
			case 'surat_keluar_eksternal' :
				$this->output_data['title'] = 'Surat Keluar Eksternal';
			break;
			case 'surat_internal' :
				$this->output_data['title'] = 'Surat Nota Dinas';
			break;
			case 'disposisi' :
				$this->output_data['title'] = 'Disposisi';
			break;
			case 'kontrak_maintenance' :
				$this->output_data['title'] = 'Contract Maintenance';
			break;
			case 'tugas' :
				$this->output_data['title'] = 'Surat Perintah';
			break;
			// case 'sppd' :
				// $this->output_data['title'] = 'Surat Sppd';
			// break;
		}
		
		$this->load->view('my_workspace', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $type
	 */
	function my_task_list($type) {

		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = $type;
		
		$this->load->view('global/header', $this->output_head);

		$list = $this->dashboard_model->get_my_list($type);
		$this->output_data['list'] = $list;
		
		switch ($type) {
			case 'surat_eksternal_masuk' :
				$this->output_data['title'] = 'Surat Masuk Eksternal';
			break;
			case 'surat_eksternal_keluar' :
				$this->output_data['title'] = 'Surat Keluar Eksternal';
			break;
			case 'surat_internal' :
				$this->output_data['title'] = 'Surat Nota Dinas';
			break;
			case 'disposisi' :
				$this->output_data['title'] = 'Disposisi';
			break;
			case 'kontrak_maintenance' :
				$this->output_data['title'] = 'Contract Maintenance';
			break;
			case 'tugas' :
				$this->output_data['title'] = 'Surat Perintah';
			break;
			// case 'sppd' :
				// $this->output_data['title'] = 'Surat Sppd';
			// break;
		}
		
		$this->load->view('my_list', $this->output_data);
		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $type
	 */
	function surat_log($type) {
		
		//$type = 'surat_masuk_eksternal';
		
		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
													assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = $type;
		
		$this->load->view('global/header', $this->output_head);	

		$list = $this->dashboard_model->get_log_surat();
		$this->output_data['list'] = $list;
		$this->output_data['title'] = 'Surat Masuk Eksternal';
		
		$this->load->view('sme_log', $this->output_data);
		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $type
	 */
	function notification() {
		$notify_id = $_POST['notify_id'];

		$result = $this->dashboard_model->edit_notification($notify_id);

		if($result) {
			return '1';
		}else {
			return '0';
		}

	}
	
	/**
	 * Function to handle request from javascript ajax
	 * do nothing 
	 */
	function ajax_handler() {
		return;
	}
	
	function my_task_board() {
		$return = array('new_task' => 1, 'items' => array());
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
}

/**
 * End of file
 */