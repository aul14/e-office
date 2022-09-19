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

class Keputusan extends LX_Controller {
	
	var $func_keputusan = 4;
	
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
		
		$this->load->model(array('auth/user_model', 'global/admin_model', 'eksternal_model', 'surat_model', 'disposisi_model', 'keputusan_model'));
		
//		$function_ref = $this->admin_model->get_function_ref('external/incoming');
//		$this->func_eks_masuk = $function_ref->function_ref_id;
		
//		$function_ref = $this->admin_model->get_function_ref('external/outgoing');
//		$this->func_eks_keluar = $function_ref->function_ref_id;
	}
	
	/**
	 * Function to handle request from javascript ajax
	 * do nothing
	 */
	function index() {
		
	}

	/**
	 * 
	 */
	function incoming_ins($organization_structure_id) {
		if(!has_permission(7)) {
			set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
			redirect('global/dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
				'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
// 				assets_url() . '/plugins/datepicker/datepicker3.css',
// 				assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
		);
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js'
// 				assets_url() . '/plugins/datepicker/bootstrap-datepicker.js'
		);
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_eksternal_masuk';
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Masuk Eksternal';
		$this->output_data['function_ref_id'] = $this->func_eks_masuk;
		
		$result = $this->admin_model->get_ref_internal($organization_structure_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/incoming/');
			exit;
		}
		$struc = $result->row();
		$_POST['surat_int_unit'] = $struc->value;
		$_POST['surat_int_kode'] = $struc->unit_code;
		$_POST['surat_int_unit_id'] = $struc->id;
		$_POST['surat_int_jabatan'] = $struc->jabatan;
		$_POST['surat_int_nama'] = $struc->nama_pejabat;
		$_POST['surat_int_dir'] = $struc->instansi;
		
		$this->load->view('external_incoming_form_add', $this->output_data);

		$this->load->view('global/footer');
	}
	
	
	/**
	 * @param unknown $surat_id
	 */
	function input_keputusan($surat_id = NULL) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
													assets_url() . '/plugins/select2/select2.min.css',
													assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css'
// 													assets_url() . '/plugins/datepicker/datepicker3.css',
// 													assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
												assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
												assets_url() . '/plugins/select2/select2.full.min.js'
//  											assets_url() . '/plugins/datepicker/bootstrap-datepicker.js'
		);
		
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_keputusan';
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['title'] = 'Keputusan';
		$this->output_data['function_ref_id'] = $this->func_keputusan;
		
		if($surat_id == NULL) {
			if(!has_permission(7)) {
				set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
				redirect('global/dashboard');
				exit;
			}
			
			$this->load->view('keputusan_form_add', $this->output_data);
			
		}

		$this->load->view('global/footer');
	}
	

	/**
	 * @param unknown $surat_id
	 */
	function keputusan_view($surat_id) {
		
		$this->output_data['title'] = 'Surat Keputusan';
		$this->output_data['function_ref_id'] = $this->func_keputusan;

		$result = $this->surat_model->get_surat($surat_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
		}
		
		$surat = $result->row();
		$this->output_data['surat'] = $surat;
				
		if(!$surat->baca_time && $surat->surat_to_ref_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
			$this->surat_model->baca_surat($this->func_keputusan, $surat_id, $surat->status);
		}
			
		$list = $this->admin_model->get_process($this->func_keputusan);
		$flow = $list->result();
		$this->output_data['flow'] = $flow;

		$list = $this->surat_model->get_surat_flow($surat_id);
		$flow_notes = $list->result();
		$this->output_data['flow_notes'] = $flow_notes;
			
// 		if($surat->status != 99) {
// 			$result = $this->admin_model->get_process($this->$func_keputusan, $surat->status);
// 			$process = $result->row();
// 		} else {
// 			$process = new stdClass();
// 			$process->flow_seq = 99;
// 		}
// 		$this->output_data['process'] = $process;
			
		$list = $this->admin_model->get_file_attachment('surat', $surat_id);
		$attachment = $list->result();
		$this->output_data['attachment'] = $attachment;
			
		$this->load->view('keputusan_form_view', $this->output_data);
	
	}
	
	function ubah_keputusan($surat_id = NULL) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
													assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
													assets_url() . '/plugins/select2/select2.min.css'
// 													assets_url() . '/plugins/datepicker/datepicker3.css',
// 													assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
												assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
												assets_url() . '/plugins/select2/select2.full.min.js'
//  											assets_url() . '/plugins/datepicker/bootstrap-datepicker.js'
		);
		
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_eksternal_masuk';
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['title'] = 'Surat Keputusan';
		$this->output_data['function_ref_id'] = $this->func_keputusan;
		
		if($surat_id == NULL) {
			if(!has_permission(7)) {
				set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
				redirect('global/dashboard');
				exit;
			}
			
			$this->load->view('keputusan_form_add', $this->output_data);
			
		} else {
			$result = $this->surat_model->get_surat($surat_id);
			if($result->num_rows() == 0) {
				set_error_message('Data tidak dikenali.');
				redirect('global/dashboard');
				exit;
			}
			
			$surat = $result->row();
			$this->output_data['surat'] = $surat;
			
			if(!$surat->baca_time && $surat->surat_to_ref_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
				$this->surat_model->baca_surat($this->func_kontrak_main, $surat_id, $surat->status);
			}
			
// 			$list = $this->admin_model->get_process($this->func_kontrak_main);
// 			$flow = $list->result();
// 			$this->output_data['flow'] = $flow;
				
// 			$list = $this->surat_model->get_surat_flow($surat_id);
// 			$flow_notes = $list->result();
// 			$this->output_data['flow_notes'] = $flow_notes;
				
// 			if($surat->status != 99) {
// 				$result = $this->admin_model->get_process($this->func_kontrak_main, $surat->status);
// 				$process = $result->row();
// 			} else {
// 				$process = new stdClass();
// 				$process->flow_seq = 99;
// 			}
// 			$this->output_data['process'] = $process;
			
			$list = $this->admin_model->get_file_attachment('surat', $surat_id);
			$attachment = $list->result();
			$this->output_data['attachment'] = $attachment;
			
			if(!has_permission(1)) {
				if($surat->status == 99) {
					$view = 'keputusan_form_edit';
				} else {
					if(editable_data($this->func_keputusan, get_role(), $surat->surat_id)) {
						$view = 'keputusan_form_edit';
					} else {
						$view = 'keputusan_form_edit';
					}
				}
			} else {
				$view = 'keputusan_form_edit';
			}
			
			$this->load->view($view, $this->output_data);

		}
	
		$this->load->view('global/footer');
	}
	
	
	function batalkan_keputusan($surat_id) {
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
													assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
													assets_url() . '/plugins/select2/select2.min.css'
// 													assets_url() . '/plugins/datepicker/datepicker3.css',
// 													assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
												assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
												assets_url() . '/plugins/select2/select2.full.min.js'
//  											assets_url() . '/plugins/datepicker/bootstrap-datepicker.js'
		);
		
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_keputusan';
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['title'] = 'Surat Keputusan';
		$this->output_data['function_ref_id'] = $this->func_keputusan;
		
		
			if(!has_permission(7)) {
				set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
				redirect('global/dashboard');
				exit;
			}
			
		$result = $this->surat_model->get_surat($surat_id);
			if($result->num_rows() == 0) {
				set_error_message('Data tidak dikenali.');
				redirect('surat/external/incoming/');
				exit;
			}
			$surat = $result->row();
			$this->output_data['surat'] = $surat;
			
			if(!$surat->baca_time && $surat->surat_to_ref_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
				$this->surat_model->baca_surat($this->func_keputusan, $surat_id, $surat->status);
			}
			
// 			$list = $this->admin_model->get_process($this->func_keputusan);
// 			$flow = $list->result();
// 			$this->output_data['flow'] = $flow;
				
// 			$list = $this->surat_model->get_surat_flow($surat_id);
// 			$flow_notes = $list->result();
// 			$this->output_data['flow_notes'] = $flow_notes;
				
// 			if($surat->status != 99) {
// 				$result = $this->admin_model->get_process($this->func_kontrak_main, $surat->status);
// 				$process = $result->row();
// 			} else {
// 				$process = new stdClass();
// 				$process->flow_seq = 99;
// 			}
// 			$this->output_data['process'] = $process;
			
			// $list = $this->admin_model->get_file_attachment('surat', $surat_id);
			// $attachment = $list->result();
			// $this->output_data['attachment'] = $attachment;
			$view = 'batalkan_form';
			
			
			$this->load->view($view, $this->output_data);

		$this->load->view('global/footer');
	}
	
	
	function cetak_kontrak() {
		//$result = $this->disposisi_model->get_disposisi($disposisi_id);
		$result = $this->kontrak_model->get_kontrak_list();
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$kontrak = $result->row();
		$this->output_data['surat'] = $kontrak;

		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>';
//		$html .= $this->load->view('print/disposisi_' . $disposisi->ref_type, $this->output_data, TRUE);
//		foreach($list_surat as $row) {
//			$data['ref'] = $row;
//			$data['ekspedisi_id'] = $ekspedisi_id;
//			$html .= '<pagebreak>' . $this->load->view('print/disposisi_' . $ref->jenis_agenda, $this->output_data, TRUE);
//		}

		$d[] = $this->load->view('print/cetak_kontrak', $this->output_data, TRUE);
		
		$html .= implode('<pagebreak>', $d);
		
		$this->load->library('m_pdf');
		$this->m_pdf->pdf->WriteHTML($html);
//		if($disposisi->response_text) {
//			$this->m_pdf->pdf->WriteHTML('<pagebreak>');
//			$this->m_pdf->pdf->WriteHTML($disposisi->response_text);
//		}
		
		$print_path = 'assets/media/print/surat/data_kontrak.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');
	
	}

	function cetak_kontrak_selesai() {
		//$result = $this->disposisi_model->get_disposisi($disposisi_id);
		$result = $this->kontrak_model->get_kontrak_list();
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}

		$kontrak = $result->row();
		$this->output_data['surat'] = $kontrak;

		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>';
//		$html .= $this->load->view('print/disposisi_' . $disposisi->ref_type, $this->output_data, TRUE);
//		foreach($list_surat as $row) {
//			$data['ref'] = $row;
//			$data['ekspedisi_id'] = $ekspedisi_id;
//			$html .= '<pagebreak>' . $this->load->view('print/disposisi_' . $ref->jenis_agenda, $this->output_data, TRUE);
//		}

		$d[] = $this->load->view('print/cetak_kontrak_selesai', $this->output_data, TRUE);
		
		$html .= implode('<pagebreak>', $d);
		
		$this->load->library('m_pdf');
		$this->m_pdf->pdf->WriteHTML($html);
//		if($disposisi->response_text) {
//			$this->m_pdf->pdf->WriteHTML('<pagebreak>');
//			$this->m_pdf->pdf->WriteHTML($disposisi->response_text);
//		}
		
		$print_path = 'assets/media/print/surat/data_kontrak.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');
	
	}
	function keputusan_list() {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
													assets_url() . '/plugins/select2/select2.min.css');
													
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_keputusan';
		$this->load->model('user_model');
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Keputusan';
		$this->load->view('keputusan_list', $this->output_data);
		$this->load->view('global/footer');
		
	}
	
	function register_arsip($surat_eksternal_id) {

		$this->eksternal_model->simpan_arsip($this->func_keputusan, $surat_eksternal_id);
		
// 		$this->incoming($surat_eksternal_id);
	}
	
	function kontrak_selesai_otomatis()
	{
		log_message('ERROR', 'kontrak_selesai_otomatis : Function called..');
		
		$list = $this->kontrak_model->get_kontrak_aktif_list();
		if(count($list) > 0) {
			foreach($list as $row) {
				$tgl_akhir = new dateTime($row->surat_akhir);
				$tgl_skrng = new DateTime();
				if($tgl_skrng > $tgl_akhir)
				{
					$this->kontrak_model->selesaikan_kontrak($row->surat_id);
				}
			}
		}
	}
	
}

/**
 * End of file
 */