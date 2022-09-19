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
 * @filesource Internal.php
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

class Internal extends LX_Controller {
	
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
		
		$this->load->model(array('auth/user_model', 'global/admin_model', 'surat_model', 'disposisi_model'));
	}
	
	/**
	 * Function to handle request from javascript ajax
	 * do nothing
	 */
	function index() {
		$this->sheet();
	}

	/**
	 * Enter description here ...
	 */
	function sheet($surat_id = NULL) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/ckeditor/ckeditor.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_internal_keluar';
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['title'] = 'Nota Dinas';
		$this->output_head['search_type'] = 'surat_internal';
		$function_ref = $this->admin_model->get_function_ref('internal');
		$function_ref_id = $function_ref->function_ref_id;
		$this->output_data['type'] = 'internal';
		$this->output_data['function_ref_id'] = $function_ref_id;

		if($surat_id == NULL) {
			$this->load->view('internal_form_add', $this->output_data);
		} else {
			$result = $this->surat_model->get_surat($surat_id);
			if($result->num_rows() == 0) {
				set_error_message('Data Surat tidak dikenali.');
				redirect('global/dashboard/');
				exit;
			}

			$surat = $result->row();
			$this->output_data['surat'] = $surat;
			
//			echo $surat_internal->surat_int_unit_id . ' == ' . get_user_data('unit_id') . ' && ' . get_user_data('structure_head') . '== 1';
// 			if(!$surat_internal->baca_time && $surat_internal->surat_int_unit_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
// 				$this->internal_model->baca_surat($this->func_eks_keluar, $surat_internal_id, $surat_internal->status);
// 			}
			
			$list = $this->admin_model->get_process($function_ref_id);
			$flow = $list->result();
			$this->output_data['flow'] = $flow;
			
			$list = $this->surat_model->get_surat_flow($surat_id);
			$flow_notes = $list->result();
			$this->output_data['flow_notes'] = $flow_notes;
			
			if($surat->status != 99) {
				$result = $this->admin_model->get_process($function_ref_id, $surat->status);
				$process = $result->row();
			} else {
				$process = new stdClass();
				$process->flow_seq = 99;
			}
			
			$this->output_data['process'] = $process;
			
			$list = $this->admin_model->get_file_attachment('surat', $surat_id);
			$attachment = $list->result();
			$this->output_data['attachment'] = $attachment;
			
			$list = $this->admin_model->get_konsep_surat('surat', $surat_id);
			$this->output_data['konsep'] = $list;
			
			$result = $this->admin_model->get_klasifikasi_arsip(trim($surat->kode_klasifikasi_arsip));
			$klasifikasi = $result->row();
			$this->output_data['klasifikasi'] = $klasifikasi;
			
			if(!has_permission(1)) {
				if($surat->status == 99) {
					$view = 'internal_form_view';
				}else {
					if(has_permission($process->permission_handle) && $process->modify == 1) {
						$view = 'internal_form_edit';
					} else {
						$view = 'internal_form_view';
					}
				}
			}else {
				$view = 'internal_form_edit';
			}
			
			$this->load->view($view, $this->output_data);
		}
		
		$this->load->view('global/footer');
	}
	
	/**
	 * Enter description here ...
	 */
	function sheet_view($surat_id) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/ckeditor/ckeditor.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_internal_keluar';
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['title'] = 'Nota Dinas';
		$this->output_head['search_type'] = 'surat_internal';
		$function_ref = $this->admin_model->get_function_ref('internal');
		$function_ref_id = $function_ref->function_ref_id;
		$this->output_data['type'] = 'internal';
		$this->output_data['function_ref_id'] = $function_ref_id;

		//$this->output_data['title'] = humanize('Internal');

		$result = $this->surat_model->get_surat($surat_id);
		if($result->num_rows() == 0) {
			set_error_message('Data Surat tidak dikenali.');
			redirect('global/dashboard/');
			exit;
		}
		
		$surat = $result->row();
		$this->output_data['surat'] = $surat;
		
		$this->output_data['type'] = 'internal';
		$this->output_data['function_ref_id'] = $surat->function_ref_id;
		
		$list = $this->admin_model->get_process($surat->function_ref_id);
		$flow = $list->result();
		$this->output_data['flow'] = $flow;
		
		$list = $this->surat_model->get_surat_flow($surat_id);
		$flow_notes = $list->result();
		$this->output_data['flow_notes'] = $flow_notes;
		
		if($surat->status != 99) {
			$result = $this->admin_model->get_process($surat->function_ref_id, $surat->status);
			$process = $result->row();
		} else {
			$process = new stdClass();
			$process->flow_seq = 99;
		}

		$this->output_data['process'] = $process;
		
		$list = $this->admin_model->get_file_attachment('surat', $surat_id);
		$attachment = $list->result();
		$this->output_data['attachment'] = $attachment;
		
		$list = $this->admin_model->get_konsep_surat('surat', $surat_id);
		$this->output_data['konsep'] = $list;
		
		$result = $this->admin_model->get_klasifikasi_arsip(trim($surat->kode_klasifikasi_arsip));
		$klasifikasi = $result->row();
		$this->output_data['klasifikasi'] = $klasifikasi;
		
		$this->load->view('internal_form_view', $this->output_data);

		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $surat_id
	 */
	function verifikasi_draf($surat_id) {
		$this->surat_model->draf($surat_id);
		redirect('surat/internal/sheet/internal/' . $surat_id);
		exit;
	}
	
	/**
	 * @param unknown $surat_id
	 */
	function kirim($surat_id) {
		$this->surat_model->kirim_surat($surat_id);
		redirect('surat/internal/sheet/internal/' . $surat_id);
		exit;
	}
	
	/**
	 * @param unknown $surat_id
	 */
	function terima($surat_id) {
		$this->surat_model->terima_surat($surat_id);
		redirect('surat/internal/sheet/internal/' . $surat_id);
		exit;
	}

	/**
	 * @param unknown $surat_id
	 */
	function cetak_surat($surat_id) {
		$result = $this->surat_model->get_surat($surat_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/internal/sheet/');
			exit;
		}

		$surat = $result->row();
		
		$result = $this->admin_model->get_konsep_surat('surat', $surat_id, TRUE);
		$konsep = $result->row();

		$this->load->library('m_pdf');
		
		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>'
				. ($this->admin_model->get_contract_config('variables', 'print_header') ? ('<div style="height:' . $this->admin_model->get_contract_config('variables', 'print_header'). 'in"></div>') : '')
				. $konsep->konsep_text;
		$this->m_pdf->pdf->WriteHTML($html);
		
		$print_path = 'assets/media/print/surat/surat_' . $surat_id . '.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');		
	}
	
	/**
	 * @param unknown $surat_id
	 */
	function register_arsip($surat_id) {
		$result = $this->surat_model->get_surat($surat_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/outgoing/');
			exit;
		}

		$surat = $result->row();

		$this->surat_model->init_arsip_si($surat);
		
// 		$this->incoming($surat_eksternal_id);
	}	
}

/**
 * End of file
 */