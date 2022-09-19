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
 * @filesource admin.php
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

class Admin extends LX_Controller {
	
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
		
		$this->load->model(array('admin_model', 'mail_model', 'auth/user_model'));
		$this->output_head['search_type'] = 'global';
	}
	
	/**
	 * Enter description here ...
	 */
	function index() {
		$this->dashboard();
	}
	
	/**
	 * Enter description here ...
	 */
	function dashboard() {
		$this->load->view('global/header');
		
		$this->load->view('dashboard');
		
		$this->load->view('global/footer');
	}
	
	/**
	 * Enter description here ...
	 */
	function system_variables() {
		if(!has_permission(1)) {
			set_warning_message('You don\'t permission to access this page.');
			redirect('global/admin');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();

		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'System Variables';
		$this->load->view('system_variables', $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	/**
	 * 
	 */
	function internal_autocomplete() {
		$json = $this->admin_model->get_ref_instansi_internal(str_replace('+', ' ', strtolower($_GET['term'])));
		
		$this->output->set_content_type('application/json')->set_output($json);
	}

	/**
	 * 
	 */
	function eksternal_autocomplete() {
		$json = $this->admin_model->get_ref_instansi_eksternal(str_replace('+', ' ', strtolower($_GET['term'])));
		
		$this->output->set_content_type('application/json')->set_output($json);
	}
	
	/**
	 * 
	 */
	function tembusan_autocomplete() {
		$json = $this->admin_model->get_ref_tembusan_instansi_internal(str_replace('+', ' ', strtolower($_GET['term'])));
		
		$this->output->set_content_type('application/json')->set_output($json);
	}

	/**
	 * 
	 */
	function asal_surat_autocomplete() {
		$json = $this->admin_model->get_ref_asal_surat_masuk(str_replace('+', ' ', strtolower($_GET['term'])));
		
		$this->output->set_content_type('application/json')->set_output($json);
	}

	/**
	 * 
	 */
	function tujuan_surat_autocomplete() {
		$json = $this->admin_model->get_ref_tujuan_surat(str_replace('+', ' ', strtolower($_GET['term'])));
		
		$this->output->set_content_type('application/json')->set_output($json);
	}

	/**
	 * @param unknown $entry_id
	 */
	function detail_posisi($organization_structure_id = NULL) {
		if(!has_permission(6)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
	
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_data['title'] = 'Klasifikasi Arsip';
		$this->output_data['mode'] = 'add';
		$this->output_data['organization_structure_id'] = $organization_structure_id;
		if($organization_structure_id != NULL) {
			$result = $this->admin_model->get_organization_structure($organization_structure_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
				exit;
			}
			
			$data = $result->row();
				
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
				
		}
	
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
	
		$this->load->view('global/header', $this->output_head);
	
		$this->load->view('organization_structure_form', $this->output_data);
		$this->load->view('global/footer');
	}
	
	function klasifikasi_arsip() {
		if(!has_permission(12)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Klasifikasi Arsip';
		$this->load->view('klasifikasi_arsip', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $entry_id
	 */
	function klasifikasi_arsip_detail($entry_id = NULL) {
		if(!has_permission(12)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_data['title'] = 'Klasifikasi Arsip';
		$this->output_data['mode'] = 'add';
		$this->output_data['entry_id'] = $entry_id;
		if($entry_id != NULL) {
			$result = $this->admin_model->get_klasifikasi_arsip_detail($entry_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
		 		exit;
			}

			$data = $result->row();
			
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
		}
		
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);

		$this->load->view('klasifikasi_arsip_form', $this->output_data);
		$this->load->view('global/footer');
	}
	
	function org_structure() {
		if(!has_permission(5)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Struktur Organisasi';
		$this->load->view('org_structure', $this->output_data);
		$this->load->view('global/footer');
		
	}
	
	function referensi() {
		if(!has_permission(1)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Referensi';
		$this->load->view('referensi', $this->output_data);
		$this->load->view('global/footer');
	}

	function mitra() {
		if(!has_permission(23)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Mitra';
		$this->load->view('mitra', $this->output_data);
		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $organization_structure_id
	 */
	function referensi_detail($entry_id = NULL) {
		if(!has_permission(1)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_data['title'] = 'Referensi';
		$this->output_data['mode'] = 'add';
		$this->output_data['entry_id'] = $entry_id;
		if($entry_id != NULL) {
			$result = $this->admin_model->get_referensi_detail($entry_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
		 		exit;
			}

			$data = $result->row();
			
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
			
		}
		
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);
		$this->load->view('referensi_form', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $organization_structure_id
	 */
	function mitra_detail($entry_id = NULL) {
		/*if(!has_permission(1)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}*/
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_data['title'] = 'Mitra';
		$this->output_data['mode'] = 'add';
		$this->output_data['entry_id'] = $entry_id;
		if($entry_id != NULL) {
			$result = $this->admin_model->get_mitra_detail($entry_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
		 		exit;
			}

			$data = $result->row();
			
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
			
		}
		
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);

		$this->load->view('mitra_form', $this->output_data);
		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $organization_structure_id
	 */
	function org_structure_detail($organization_structure_id = NULL) {
		if(!has_permission(5)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_data['title'] = 'Struktur Organisasi';
		$this->output_data['mode'] = 'add';
		$this->output_data['organization_structure_id'] = $organization_structure_id;
		if($organization_structure_id != NULL) {
			$result = $this->admin_model->get_org_structure_detail($organization_structure_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
		 		exit;
			}

			$data = $result->row();
			
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
		}
		
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);

		$this->load->view('org_structure_form', $this->output_data);
		$this->load->view('global/footer');
	}
	
	function format_surat() {
		if(!has_permission(5)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Format Template';
		$this->load->view('format_surat', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $format_surat_id
	 */
	function format_surat_detail($format_surat_id = NULL) {
		if(!has_permission(5)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_data['title'] = 'Format Template';
		$this->output_data['mode'] = 'add';
		$this->output_data['format_surat_id'] = $format_surat_id;
		if($format_surat_id != NULL) {
			$result = $this->admin_model->get_format_surat_detail($format_surat_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
		 		exit;
			}
			$data = $result->row();
			
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
			
		}
		
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/ckeditor/ckeditor.js');
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);

		$this->load->view('format_surat_form', $this->output_data);
		$this->load->view('global/footer');
	}

	function tujuan_surat() {
		if(!has_permission(18)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Tujuan Surat';
		$this->load->view('tujuan_surat', $this->output_data);
		$this->load->view('global/footer');
		
	}

	/**
	 * @param unknown $organization_structure_id
	 */
	function tujuan_surat_detail($entry_id = NULL) {
		/*if(!has_permission(1)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}*/
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);

		$this->output_data['title'] = 'Tujuan Surat';
		$this->output_data['mode'] = 'add';
		$this->output_data['tujuan_surat_id'] = $entry_id;
		if($entry_id != NULL) {
			$result = $this->admin_model->get_tujuan_surat_detail($entry_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
		 		exit;
			}
			$data = $result->row();
			
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
			
		}
		
		$this->load->view('tujuan_surat_form', $this->output_data);
		$this->load->view('global/footer');
	}

	function tujuan_surat_eksternal() {
		if(!has_permission(18)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
	
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Tujuan Surat Eksternal';
		$this->load->view('referensi_external', $this->output_data);
		$this->load->view('global/footer');
		
	}

	/**
	 * @param unknown $organization_structure_id
	 */
	function tujuan_surat_eksternal_detail($entry_id = NULL) {
		// if(!has_permission(1)) {
		// 	set_warning_message('Sory you\'re not allowed to access this page.');
		// 	redirect('global/dashboard');
		// 	exit;
		// }
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_data['title'] = 'Tujuan Surat Eksternal';
		$this->output_data['mode'] = 'add';
		$this->output_data['entry_id'] = $entry_id;
		if($entry_id != NULL) {
			$result = $this->admin_model->get_referensi_detail($entry_id);
			if($result->num_rows() == 0) {
				set_warning_message('Data tidak dikenali.');
				redirect('dashboard');
		 		exit;
			}
			$data = $result->row();
			
			$this->output_data['mode'] = 'edit';
			$this->output_data['data'] = $data;
			
		}
		
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);

		$this->load->view('referensi_external_form', $this->output_data);
		$this->load->view('global/footer');
	}
	
/*
	function back_process() {
		
	}
	
	function next_process() {

		$list = $this->admin_model->get_process($_POST['function_ref_id']);
		$flow = $list->result();
		
		$result = $this->admin_model->get_object_data($_POST['ref_type'], $_POST['ref_id']);
		$data = $result->row();
// 		var_dump($data);
		
		$result = $this->admin_model->get_process($_POST['function_ref_id'], $data->status);
		$process = $result->row();
// 		var_dump($process);
		
		
	}
*/

	/**
	 * Enter description here ...
	 */
	function finder() {
		if(!has_permission(5)) {
			echo 'You don\'t permission to access this page.';
		} else {
			$this->load->view('ckfinder');
		}
	}
	
	/**
	 * Function to handle request from javascript ajax
	 * do nothing 
	 */
	function ajax_handler() {
		
	}
	
}

/**
 * End of file
 */