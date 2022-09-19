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
 * @filesource Disposisi.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Sep 23, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Disposisi extends LX_Controller {

	var $func_disposisi = 12;
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
		
		$this->load->model(array('auth/user_model', 'global/admin_model', 'disposisi_model'));

		$this->output_head['search_type'] = 'disposisi';
//		$function_ref = $this->admin_model->get_function_ref('disposisi');
		
//		$this->func_disposisi = $function_ref->function_ref_id;
	}
	
	function proses() {
		// --
	}

	/**
	 *
	 */
	function index() {
		$this->sheet();
	}
	
	/**
	 * 
	 */
	function outstanding() {

		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);
		
		$list = $this->disposisi_model->get_pre_disposisi();
		$this->output_data['list'] = $list;
		
		$this->output_data['title'] = 'Disposisi';
		$this->load->view('pre_disposisi', $this->output_data);
		$this->load->view('global/footer');
	}

	/**
	 * Enter description here ...
	 */
	function sheet($disposisi_id = NULL) {
//		if(!has_permission(13)) {
//			set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
//			redirect('global/dashboard');
//			exit;
//		}
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
				'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
				// 				assets_url() . '/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
				assets_url() . '/plugins/select2/select2.min.css'
		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
				// 				assets_url() . '/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
				assets_url() . '/plugins/select2/select2.full.min.js'
		);
		
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
			
		$this->output_data['title'] = 'Disposisi ';
		if($disposisi_id == NULL) {
			$this->load->view('disposisi_form_add', $this->output_data);
			
		} else {
			
			$result = $this->disposisi_model->get_disposisi($disposisi_id);
			if($result->num_rows() == 0) {
				set_error_message('Data tidak dikenali.');
				redirect('global/dashboard');
				exit;
			}
			$disposisi = $result->row();
			$this->output_data['disposisi'] = $disposisi;
			$this->output_data['function_ref_id'] = $this->func_disposisi;

			$teruskan = '';
			if($disposisi->parent_id != '-' && $disposisi->from_unit_id == get_user_data('unit_id')) {
				$teruskan = 'teruskan_';
				$result = $this->disposisi_model->get_disposisi($disposisi->parent_id);
				//$result = $this->disposisi_model->get_disposisi($disposisi_id);
				$parent = $result->row();
				$this->output_data['parent'] = $parent;

				$list = $this->admin_model->get_file_attachment('disposisi', $parent->disposisi_id);
				$attachment = $list->result();
				$this->output_data['parent_attachment'] = $attachment;
			}
	
			$result = $this->disposisi_model->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
			$ref = $result->row();
			$this->output_data['ref'] = $ref;
	
			$list = $this->admin_model->get_process($this->func_disposisi);
			$flow = $list->result();
			$this->output_data['flow'] = $flow;
			
			$list = $this->disposisi_model->get_disposisi_flow($disposisi_id);
			$flow_notes = $list->result();
			$this->output_data['flow_notes'] = $flow_notes;
			
			$list = $this->admin_model->get_file_attachment('disposisi', $disposisi_id);
			$attachment = $list->result();
			$this->output_data['attachment'] = $attachment;
			
			if($disposisi->status != 99) {
				$result = $this->admin_model->get_process($this->func_disposisi, $disposisi->status);
				$process = $result->row();
			} else {
				$process = new stdClass();
				$process->flow_seq = 99;
			}
			$this->output_data['process'] = $process;
				
			if($disposisi->status > 0) {
				$view = $teruskan . 'disposisi_form_view';
			} else {
				$view = $teruskan . 'disposisi_form_edit';
			}
			$this->load->view($view, $this->output_data);
		}
		
		$this->load->view('global/footer');
	}

	/**
	 * Enter description here ...
	 */
	function sheet_view($disposisi_id) {
//		if(!has_permission(13)) {
//			set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
//			exit;
//		}
	
		$result = $this->disposisi_model->get_disposisi($disposisi_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$disposisi = $result->row();
		$this->output_data['disposisi'] = $disposisi;
		$this->output_data['function_ref_id'] = $this->func_disposisi;
	
		$teruskan = '';
		if(($disposisi->parent_id != '-') && (get_user_id() == $disposisi->from_user_id)) {
			$teruskan = 'teruskan_';
			$result = $this->disposisi_model->get_disposisi($disposisi->parent_id);
			$parent = $result->row();
			$this->output_data['parent'] = $parent;

			$list = $this->admin_model->get_file_attachment('disposisi', $parent->disposisi_id);
			$attachment = $list->result();
			$this->output_data['parent_attachment'] = $attachment;
			
		}

		$result = $this->disposisi_model->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
		$ref = $result->row();
		$this->output_data['ref'] = $ref;
	
		$list = $this->admin_model->get_process($this->func_disposisi);
		$flow = $list->result();
		$this->output_data['flow'] = $flow;
	
		$list = $this->disposisi_model->get_disposisi_flow($disposisi_id);
		$flow_notes = $list->result();
		$this->output_data['flow_notes'] = $flow_notes;

		$list = $this->admin_model->get_file_attachment('disposisi', $disposisi_id);
		$attachment = $list->result();
		$this->output_data['attachment'] = $attachment;
		
		if($disposisi->status != 99) {
			$result = $this->admin_model->get_process($this->func_disposisi, $disposisi->status);
			$process = $result->row();
		} else {
			$process = new stdClass();
			$process->flow_seq = 99;
		}
		$this->output_data['process'] = $process;
		$this->output_data['title'] = 'Disposisi ';
		$this->load->view($teruskan . 'disposisi_form_view', $this->output_data);
	}
	
	/**
	 * @param unknown $type
	 * @param unknown $ref_id
	 */
	function create_from($type, $ref_id, $parent_id = NULL) {
		if(!has_permission(13)) {
			set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
			redirect('global/dashboard');
			exit;
		}
		
		$result = $this->disposisi_model->get_ref_data($type, $ref_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$ref = $result->row();

		$result = $this->disposisi_model->get_disposisi_from_ref($type, $ref_id);
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
			if($parent_id == NULL) {
				redirect('surat/disposisi/sheet/' . $disposisi->disposisi_id);
				exit;
			} else {
				$result = $this->disposisi_model->get_disposisi($parent_id);
				$parent = $result->row();
				$this->output_data['parent'] = $parent;

				$list = $this->admin_model->get_file_attachment('disposisi', $parent->disposisi_id);
				$attachment = $list->result();
				$this->output_data['parent_attachment'] = $attachment;
			}
		}
		
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
		
		$this->output_data['title'] = '';//'Surat Masuk Eksternal';
		$this->output_data['type'] = $type;
		$this->output_data['ref_id'] = $ref_id;
		$this->output_data['parent_id'] = $parent_id;
		
		if($parent_id == NULL) {
			$this->load->view('disposisi_form_add', $this->output_data);
		} else {
			$this->load->view('teruskan_disposisi_form_add', $this->output_data);
		}

		$this->load->view('global/footer');
	}

	function instruksi_part($disposisi_id, $distribusi_id, $line) {
		$result = $this->db->get_where('disposisi', array('disposisi_id' => $disposisi_id));
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
			$this->output_data['disposisi'] = $disposisi;
			$this->output_data['distribusi_id'] = $distribusi_id;
			$this->output_data['line'] = $line;
			$this->load->view('disposisi_instruksi', $this->output_data);
		} 
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function cetak_disposisi($disposisi_id) {
		$result = $this->disposisi_model->get_disposisi($disposisi_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$disposisi = $result->row();
		$this->output_data['disposisi'] = $disposisi;

		$result = $this->disposisi_model->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
		$ref = $result->row();
		$this->output_data['ref'] = $ref;
		
		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>';
//		$html .= $this->load->view('print/disposisi_' . $disposisi->ref_type, $this->output_data, TRUE);
//		foreach($list_surat as $row) {
//			$data['ref'] = $row;
//			$data['ekspedisi_id'] = $ekspedisi_id;
//			$html .= '<pagebreak>' . $this->load->view('print/disposisi_' . $ref->jenis_agenda, $this->output_data, TRUE);
//		}

		$distribusi = json_decode($disposisi->distribusi);
		$d = array();
		foreach($distribusi as $row) {
			$this->output_data['to_check'] = $row->unit_id;
			$d[] = $this->load->view('print/disposisi_' . $ref->jenis_agenda, $this->output_data, TRUE);
		}
		$html .= implode('<pagebreak>', $d);
		
		$this->load->library('m_pdf');
		$this->m_pdf->pdf->WriteHTML($html);
//		if($disposisi->response_text) {
//			$this->m_pdf->pdf->WriteHTML('<pagebreak>');
//			$this->m_pdf->pdf->WriteHTML($disposisi->response_text);
//		}
		
		$print_path = 'assets/media/print/disposisi/disposisi_' . $disposisi_id . '.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');
	
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function kirim($disposisi_id) {
		
		$this->disposisi_model->kirim_disposisi($disposisi_id, $this->func_disposisi);
		
		redirect('surat/disposisi/sheet/' . $disposisi_id);
		exit;
	}

	/**
	 * @param unknown $disposisi_id
	 */
	function terima($disposisi_id) {
	
		$this->disposisi_model->terima_disposisi($disposisi_id, $this->func_disposisi);
	
		redirect('surat/disposisi/sheet/' . $disposisi_id);
		exit;
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function response($disposisi_id) {

		$this->disposisi_model->response_disposisi($disposisi_id);
		
		redirect('surat/disposisi/sheet/' . $disposisi_id);
		exit;
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function complete($disposisi_id) {

		$this->disposisi_model->complete_disposisi($disposisi_id);
		
		redirect('surat/disposisi/sheet/' . $disposisi_id);
		exit;
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function cetak_response($disposisi_id) {
		$result = $this->disposisi_model->get_disposisi($disposisi_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$disposisi = $result->row();
		$this->output_data['disposisi'] = $disposisi;
		
		$this->load->library('m_pdf');

		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>' . $disposisi->response_text; 
		$this->m_pdf->pdf->WriteHTML($html);
		
		$print_path = 'assets/media/print/disposisi/disposisi_' . $disposisi_id . '.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');
		
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function create_pengantar($disposisi_id) {
		$result = $this->disposisi_model->get_disposisi($disposisi_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$disposisi = $result->row();
		$this->output_data['ref'] = $disposisi;
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
				'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
				assets_url() . '/plugins/datepicker/datepicker3.css',
				assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
				assets_url() . '/plugins/select2/select2.min.css'
		);
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
				assets_url() . '/plugins/datepicker/bootstrap-datepicker.js',
				assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
				assets_url() . '/plugins/select2/select2.full.min.js'
		);
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['function_ref_id'] = $this->func_disposisi;
		
		$list = $this->disposisi_model->get_disposisi_to($disposisi->surat_to_unit);
		$list_disposisi = $list->result();
		$this->output_data['list_disposisi'] = $list_disposisi;
		
		$this->output_data['title'] = 'Disposisi ';
		
		$view = 'pengantar_disposisi_add';
		
		$this->load->view($view, $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function pengantar($disposisi_id = NULL) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
				'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
				assets_url() . '/plugins/datepicker/datepicker3.css',
				assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
				assets_url() . '/plugins/select2/select2.min.css'
		);
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
				assets_url() . '/plugins/datepicker/bootstrap-datepicker.js',
				assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
				assets_url() . '/plugins/select2/select2.full.min.js'
		);
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
	
		$this->output_data['function_ref_id'] = $this->func_disposisi;
		

		$this->output_data['title'] = 'Disposisi ';
		$result = $this->disposisi_model->get_disposisi($disposisi_id);
		if($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('global/dashboard');
			exit;
		}
		$disposisi = $result->row();
		$this->output_data['disposisi'] = $disposisi;

		$view = 'surat_pengantar_add';

		$this->load->view($view, $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $pengantar_surat_eksternal_id
	 */
	function cetak_pengantar($pengantar_surat_eksternal_id) {
		$result = $this->eksternal_model->get_surat_pengantar($pengantar_surat_eksternal_id);
		if($result->num_rows() == 0) {
			echo 'Data tidak dikenali.';
			exit;
		}
		$surat_pengantar = $result->row();
		$this->output_data['pengantar'] = $surat_pengantar;
	
		$list = $this->eksternal_model->get_surat_masuk_pengantar($surat_pengantar->pengantar_surat_eksternal_id);
		$list_surat = $list->result();
		$this->output_data['list_surat'] = $list_surat;
			
		$this->output_data['cc'] = json_decode($surat_pengantar->tembusan);
		$html = $this->load->view('print/pengantar', $this->output_data, TRUE);
	
		$this->load->library('m_pdf');
	
		$this->m_pdf->pdf->WriteHTML($html);
	
		$print_path = 'assets/media/print/pengantar/pengantar_' . $pengantar_surat_eksternal_id . '.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');
	
	}

	/**
	 * Enter description here ...
	 */
	function outgoing($disposisi_id = NULL) {
//		if(!has_permission(13)) {
//			set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
//			redirect('global/dashboard');
//			exit;
//		}
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
				'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
				// 				assets_url() . '/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
				assets_url() . '/plugins/select2/select2.min.css'
		);
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
				// 				assets_url() . '/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js',
				assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
				assets_url() . '/plugins/select2/select2.full.min.js'
		);
		
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
			
		$this->output_data['title'] = 'Disposisi ';
		if($disposisi_id == NULL) {
			$this->load->view('disposisi_form_add', $this->output_data);
			
		} else {
			
			$result = $this->disposisi_model->get_disposisi($disposisi_id);
			if($result->num_rows() == 0) {
				set_error_message('Data tidak dikenali.');
				redirect('global/dashboard');
				exit;
			}
			$disposisi = $result->row();
			$this->output_data['disposisi'] = $disposisi;
			$this->output_data['function_ref_id'] = $this->func_disposisi;

			$teruskan = '';
			if($disposisi->parent_id != '-' && $disposisi->from_unit_id == get_user_data('unit_id')) {
				$teruskan = 'teruskan_';
				$result = $this->disposisi_model->get_disposisi($disposisi->parent_id);
				//$result = $this->disposisi_model->get_disposisi($disposisi_id);
				$parent = $result->row();
				$this->output_data['parent'] = $parent;

				$list = $this->admin_model->get_file_attachment('disposisi', $parent->disposisi_id);
				$attachment = $list->result();
				$this->output_data['parent_attachment'] = $attachment;
			}
	
			$result = $this->disposisi_model->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
			$ref = $result->row();
			$this->output_data['ref'] = $ref;
	
			$list = $this->admin_model->get_process($this->func_disposisi);
			$flow = $list->result();
			$this->output_data['flow'] = $flow;
			
			$list = $this->disposisi_model->get_disposisi_flow($disposisi_id);
			$flow_notes = $list->result();
			$this->output_data['flow_notes'] = $flow_notes;
			
			$list = $this->admin_model->get_file_attachment('disposisi', $disposisi_id);
			$attachment = $list->result();
			$this->output_data['attachment'] = $attachment;
			
			if($disposisi->status != 99) {
				$result = $this->admin_model->get_process($this->func_disposisi, $disposisi->status);
				$process = $result->row();
			} else {
				$process = new stdClass();
				$process->flow_seq = 99;
			}
			$this->output_data['process'] = $process;
				
			if($disposisi->status > 0) {
				$view = $teruskan . 'external_disposisi_view';
			} else {
				$view = $teruskan . 'external_disposisi_view';
			}
			$this->load->view($view, $this->output_data);
		}
		
		$this->load->view('global/footer');
	}

	
}

/**
 * End of file
 */