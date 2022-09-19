<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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

class External extends LX_Controller
{

	var $func_eks_masuk = 1;
	var $func_eks_keluar = 2;

	/**
	 * Enter description here ...
	 */
	function __construct()
	{
		parent::__construct();
		if (!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}

		if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));
		$dir = explode('/', str_replace('\\', '/', __DIR__));
		$module = end($dir);

		$this->output_head = array('class' => strtolower(__CLASS__), 'module' => strtolower($module));

		$this->load->model(array('auth/user_model', 'global/admin_model', 'surat_model', 'disposisi_model'));

		//		$function_ref = $this->admin_model->get_function_ref('external/incoming');
		//		$this->func_eks_masuk = $function_ref->function_ref_id;

		//		$function_ref = $this->admin_model->get_function_ref('external/outgoing');
		//		$this->func_eks_keluar = $function_ref->function_ref_id;
	}

	/**
	 * Function to handle request from javascript ajax
	 * do nothing
	 */
	function index()
	{
	}

	/**
	 * 
	 */
	function incoming_ins($organization_structure_id)
	{
		if (!has_permission(7)) {
			set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
			redirect('global/dashboard');
			exit;
		}

		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(
			assets_url() . '/plugins/iCheck/all.css',
			'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
			// 				assets_url() . '/plugins/datepicker/datepicker3.css',
			// 				assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
		);
		$this->output_head['js_extras'] = array(
			assets_url() . '/plugins/iCheck/icheck.min.js',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js'
			// 				assets_url() . '/plugins/datepicker/bootstrap-datepicker.js'
		);
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_eksternal_masuk';
		$this->load->view('global/header', $this->output_head);

		$this->output_data['title'] = 'Masuk Eksternal';
		$this->output_data['function_ref_id'] = $this->func_eks_masuk;

		$result = $this->admin_model->get_ref_internal($organization_structure_id);
		if ($result->num_rows() == 0) {
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
	function incoming($surat_id = NULL)
	{
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(
			assets_url() . '/plugins/iCheck/all.css',
			'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
			assets_url() . '/plugins/select2/select2.min.css'
			// 													assets_url() . '/plugins/datepicker/datepicker3.css',
			// 													assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
		);

		$this->output_head['js_extras'] = array(
			assets_url() . '/plugins/iCheck/icheck.min.js',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
			assets_url() . '/plugins/select2/select2.full.min.js'
			//  											assets_url() . '/plugins/datepicker/bootstrap-datepicker.js'
		);

		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_eksternal_masuk';
		$this->load->view('global/header', $this->output_head);

		$this->output_data['title'] = 'Masuk Eksternal';
		$this->output_data['function_ref_id'] = $this->func_eks_masuk;

		if ($surat_id == NULL) {
			if (!has_permission(7)) {
				set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
				redirect('global/dashboard');
				exit;
			}

			$this->load->view('external_incoming_form_add', $this->output_data);
		} else {
			$result = $this->surat_model->get_surat($surat_id);
			if ($result->num_rows() == 0) {
				set_error_message('Data tidak dikenali.');
				redirect('surat/external/incoming/');
				exit;
			}
			$surat = $result->row();
			$this->output_data['surat'] = $surat;

			if (!$surat->baca_time && $surat->surat_to_ref_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
				$this->surat_model->baca_surat($this->func_eks_masuk, $surat_id, $surat->status);
			}

			$list = $this->admin_model->get_process($this->func_eks_masuk);
			$flow = $list->result();
			$this->output_data['flow'] = $flow;

			$list = $this->surat_model->get_surat_flow($surat_id);
			$flow_notes = $list->result();
			$this->output_data['flow_notes'] = $flow_notes;

			if ($surat->status != 99) {
				$result = $this->admin_model->get_process($this->func_eks_masuk, $surat->status);
				$process = $result->row();
			} else {
				$process = new stdClass();
				$process->flow_seq = 99;
			}
			$this->output_data['process'] = $process;

			$list = $this->admin_model->get_file_attachment('surat', $surat_id);
			$attachment = $list->result();
			$this->output_data['attachment'] = $attachment;

			if (!has_permission(1)) {
				if ($surat->status == 99) {
					$view = 'external_incoming_form_view';
				} else {
					if ($surat->status == 1 && has_permission(15)) {
						$view = 'external_incoming_form_view';
					} else {
						if (editable_data($this->func_eks_masuk, get_role(), $surat->surat_id)) {
							$view = 'external_incoming_form_edit';
						} else {
							$view = 'external_incoming_form_view';
						}
					}
				}
			} else {
				$view = 'external_incoming_form_edit';
			}

			$this->load->view($view, $this->output_data);
		}

		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $surat_id
	 */
	function incoming_view($surat_id)
	{
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(
			assets_url() . '/plugins/iCheck/all.css',
			'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css',
			assets_url() . '/plugins/select2/select2.min.css'
			// 													assets_url() . '/plugins/datepicker/datepicker3.css',
			// 													assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
		);

		$this->output_head['js_extras'] = array(
			assets_url() . '/plugins/iCheck/icheck.min.js',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js',
			assets_url() . '/plugins/select2/select2.full.min.js'
			//  											assets_url() . '/plugins/datepicker/bootstrap-datepicker.js'
		);

		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_eksternal_masuk';
		$this->load->view('global/header', $this->output_head);

		$this->output_data['title'] = 'Masuk Eksternal';
		$this->output_data['function_ref_id'] = $this->func_eks_masuk;

		//$this->output_data['title'] = 'Masuk Eksternal';
		//$this->output_data['function_ref_id'] = $this->func_eks_masuk;

		$result = $this->surat_model->get_surat($surat_id);
		if ($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
		}

		$surat = $result->row();
		$this->output_data['surat'] = $surat;

		if (!$surat->baca_time && $surat->surat_to_ref_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
			$this->surat_model->baca_surat($this->func_eks_masuk, $surat_id, $surat->status);
		}

		if ($surat->status == 3) {
			if (!has_permission(7)) {
				$user_id = get_user_id();
				$result = $this->surat_model->get_notify($surat_id, $user_id);
				$notify = $result->row();
				$this->surat_model->baca_notify($this->func_eks_masuk, $surat_id, $surat->status, $surat->surat_to_ref_id, $notify->read, $notify->notify_user_id, $notify->notify_id, $surat->surat_pengantar_id);
			}
		}

		$list = $this->admin_model->get_process($this->func_eks_masuk);
		$flow = $list->result();
		$this->output_data['flow'] = $flow;

		$list = $this->surat_model->get_surat_flow($surat_id);
		$flow_notes = $list->result();
		$this->output_data['flow_notes'] = $flow_notes;

		if ($surat->status != 99) {
			$result = $this->admin_model->get_process($this->func_eks_masuk, $surat->status);
			$process = $result->row();
		} else {
			$process = new stdClass();
			$process->flow_seq = 99;
		}
		$this->output_data['process'] = $process;

		$list = $this->admin_model->get_file_attachment('surat', $surat_id);
		$attachment = $list->result();
		$this->output_data['attachment'] = $attachment;

		$this->load->view('external_incoming_form_view', $this->output_data);

		$this->load->view('global/footer');
	}

	function distribusi($function_ref = NULL, $ref_id = NULL)
	{
	}

	/**
	 * @param unknown $surat_id
	 */
	function terima($surat_id)
	{

		$this->eksternal_model->terima_surat($this->func_eks_masuk, $surat_id);

		$this->incoming($surat_id);
	}

	/**
	 * Enter description here ...
	 */
	function outgoing_view($surat_id)
	{
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/ckeditor/ckeditor.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_keluar_eksternal';
		$this->load->view('global/header', $this->output_head);

		$this->output_data['title'] = 'Keluar Eksternal';
		$this->output_head['search_type'] = 'surat_keluar_eksternal';
		//		$this->output_data['function_ref_id'] = $this->func_eks_keluar;

		//$this->output_data['title'] = 'Keluar Eksternal';
		$this->output_data['function_ref_id'] = $this->func_eks_keluar;

		$result = $this->surat_model->get_surat($surat_id);
		if ($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/outgoing/');
			exit;
		}
		$surat = $result->row();

		$this->output_data['surat'] = $surat;

		//		echo $surat->surat_int_unit_id . ' == ' . get_user_data('unit_id') . ' && ' . get_user_data('structure_head') . '== 1';
		// 		if(!$surat->baca_time && $surat->surat_int_unit_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
		// 			$this->eksternal_model->baca_surat($this->func_eks_keluar, $surat_id, $surat->status);
		// 		}

		$list = $this->admin_model->get_process($this->func_eks_keluar);
		$flow = $list->result();
		$this->output_data['flow'] = $flow;

		$list = $this->surat_model->get_surat_flow($surat_id);
		$flow_notes = $list->result();
		$this->output_data['flow_notes'] = $flow_notes;

		if ($surat->status != 99) {
			$result = $this->admin_model->get_process($this->func_eks_keluar, $surat->status);
			$process = $result->row();
		} else {
			$process = new stdClass();
			$process->check_field_function = 'surat/external/register_arsip';
			$process->check_field_info = 'Simpan Surat sebagai Arsip?';
			$process->flow_seq = 99;
		}
		$this->output_data['process'] = $process;

		$list = $this->admin_model->get_file_attachment('surat', $surat_id);
		$attachment = $list->result();
		$this->output_data['attachment'] = $attachment;

		$list = $this->admin_model->get_file_attachment('copy_surat', $surat_id);
		if ($list->num_rows() > 0) {
			$copy_surat = $list->row();
			//$copy_surat->state= 'edit';
		} else {
			$copy_surat = new stdClass();
			$copy_surat->file_attachment_id = '-';
			$copy_surat->file = '-';
			$copy_surat->file_name = '-';
			$copy_surat->sort = '0';
		}
		$this->output_data['copy_surat'] = $copy_surat;

		$list = $this->admin_model->get_konsep_surat('surat', $surat_id);
		$this->output_data['konsep'] = $list;

		/*
		$list = $this->surat_model->get_surat_ttd($surat_id);
		$ttd = $list->result();
		$this->output_data['ttd'] = $ttd;
*/
		$result = $this->admin_model->get_klasifikasi_arsip(trim($surat->kode_klasifikasi_arsip));
		$klasifikasi = $result->row();
		$this->output_data['klasifikasi'] = $klasifikasi;

		$this->load->view('external_outgoing_form_view', $this->output_data);

		$this->load->view('global/footer');
	}

	/**
	 * Enter description here ...
	 */
	function outgoing($surat_id = NULL)
	{
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array(
			assets_url() . '/plugins/ckeditor/ckeditor.js',
			assets_url() . '/plugins/select2/select2.full.min.js'

		);
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_keluar_eksternal';
		$this->load->view('global/header', $this->output_head);

		$this->output_data['title'] = 'Keluar Eksternal';
		$this->output_head['search_type'] = 'surat_keluar_eksternal';
		//		$this->output_data['function_ref_id'] = $this->func_eks_keluar;

		if ($surat_id == NULL) {
			$this->load->view('external_outgoing_form_add', $this->output_data);
		} else {
			$result = $this->surat_model->get_surat($surat_id);
			if ($result->num_rows() == 0) {
				set_error_message('Data tidak dikenali.');
				redirect('surat/external/outgoing/');
				exit;
			}
			$surat = $result->row();
			$this->output_data['surat'] = $surat;

			//			echo $surat->surat_int_unit_id . ' == ' . get_user_data('unit_id') . ' && ' . get_user_data('structure_head') . '== 1';
			// 			if(!$surat->baca_time && $surat->surat_int_unit_id == get_user_data('unit_id') && get_user_data('structure_head') == 1) {
			// 				$this->eksternal_model->baca_surat($this->func_eks_keluar, $surat_id, $surat->status);
			// 			}

			$list = $this->admin_model->get_process($this->func_eks_keluar);
			$flow = $list->result();
			$this->output_data['flow'] = $flow;

			$list = $this->surat_model->get_surat_flow($surat_id);
			$flow_notes = $list->result();
			$this->output_data['flow_notes'] = $flow_notes;

			if ($surat->status != 99) {
				$result = $this->admin_model->get_process($this->func_eks_keluar, $surat->status);
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

			// 			$list = $this->surat_model->get_surat_eksternal_ttd($surat_id);
			// 			$ttd = $list->result();
			// 			$this->output_data['ttd'] = $ttd;

			$result = $this->admin_model->get_klasifikasi_arsip(trim($surat->kode_klasifikasi_arsip));
			$klasifikasi = $result->row();
			$this->output_data['klasifikasi'] = $klasifikasi;

			if (!has_permission(1)) {
				if ($surat->status == 99) {
					$view = 'external_outgoing_form_view';
				} else {
					//					if(editable_data(2, has_permission($process->permission_handle), $surat->surat_id)) {
					if (has_permission($process->permission_handle) && $process->modify == 1) {
						$view = 'external_outgoing_form_edit';
					} else {
						$view = 'external_outgoing_form_view';
					}
				}
			} else {
				$view = 'external_incoming_form_edit';
			}


			$this->load->view($view, $this->output_data);
		}

		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $surat_eksternal_id
	 */
	function verifikasi_draf($surat_id)
	{
		$this->eksternal_model->draf($this->func_eks_keluar, $surat_id);
		redirect('surat/external/outgoing/' . $surat_id);
		exit;
	}

	/**
	 * @param unknown $surat_id
	 */
	function verifikasi_dir($surat_id)
	{
		$this->eksternal_model->verifikasi_dir($this->func_eks_keluar, $surat_id);
		redirect('surat/external/outgoing/' . $surat_id);
		exit;
	}

	/**
	 * @param unknown $surat_id
	 */
	function verifikasi_admin($surat_id)
	{
		$this->eksternal_model->verifikasi_admin($this->func_eks_keluar, $surat_id);
		redirect('global/dashboard');
		exit;
	}

	function cetak_surat_eksternal($surat_id)
	{
		$result = $this->surat_model->get_surat($surat_id);
		if ($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/outgoing/');
			exit;
		}
		$surat = $result->row();

		$result = $this->admin_model->get_konsep_surat('surat', $surat_id, TRUE);
		$konsep = $result->row();
		$this->load->library('m_pdf');

		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>'
			. $konsep->konsep_text;
		$this->m_pdf->pdf->WriteHTML($html);

		$print_path = 'assets/media/print/surat/surat_' . $surat_id . '.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');
	}

	function cetak_surat_eksternal_esign($surat_id)
	{
		$sql_esign = "SELECT * FROM public.surat_sign WHERE surat_id = '{$surat_id}' ORDER BY id_surat_sign ASC";
		$esign = $this->db->query($sql_esign)->row_array();
		// $this->output_data['esign'] = $esign;

		$filePath = $esign['dest'];
		//Membuat kondisi jika file tidak ada
		if (!file_exists($filePath)) {
			echo "The file $filePath does not exist";
			die();
		}
		//nama file untuk tampilan
		$filename = "SIGNED_{$esign['surat_id']}.pdf";
		header('Content-type:application/pdf');
		header('Content-disposition: inline; filename="' . $filename . '"');
		header('content-Transfer-Encoding:binary');
		header('Accept-Ranges:bytes');
		//membaca dan menampilkan file
		readfile($filePath);
	}

	// function cetak_surat_eksternal($surat_id)
	// {
	// 	$result = $this->surat_model->get_surat($surat_id);
	// 	if ($result->num_rows() == 0) {
	// 		set_error_message('Data tidak dikenali.');
	// 		redirect('surat/external/outgoing/');
	// 		exit;
	// 	}
	// 	$surat = $result->row();

	// 	$result = $this->admin_model->get_konsep_surat('surat', $surat_id, TRUE);
	// 	$konsep = $result->row();

	// 	$this->load->library('m_pdf');

	// 	$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>'

	// 		. $konsep->konsep_text;
	// 	$mpdfConfig = array(
	// 		'tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR,
	// 		'mode' => 'utf-8',
	// 		'format' => 'A4',    // format - A4, for example, default ''
	// 		'margin_left' => 5,        // 15 margin_left
	// 		'margin_right' => 5,        // 15 margin right
	// 		// 'margin_top' => 5,        // 15 margin right
	// 		// 'margin_bottom' => 5,        // 15 margin right
	// 		'orientation' => 'P'      // L - landscape, P - portrait
	// 	);
	// 	$data = [
	// 		'konsep_text'	=> $konsep->konsep_text,
	// 		'mpdf'			=> $mpdfConfig
	// 	];
	// 	$html = $this->load->view('test/print', $data, TRUE);
	// 	// $this->m_pdf->pdf->SetDisplayMode('fullwidth');
	// 	$this->m_pdf->pdf->WriteHTML($html);

	// 	$print_path = 'assets/media/print/surat/surat_' . $surat_id . '.pdf';
	// 	$this->m_pdf->pdf->Output($print_path, 'I');
	// }

	function kirim_surat_keluar($surat_id)
	{
		if (!has_permission(17)) {
			set_warning_message('Anda tidak memiliki izin untuk mengakses halaman ini.');
			redirect('global/dashboard');
			exit;
		}

		$result = $this->surat_model->get_surat($surat_id);
		if ($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/outgoing/');
			exit;
		}


		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(
			assets_url() . '/plugins/iCheck/all.css',
			'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.css'
		);

		$this->output_head['js_extras'] = array(
			assets_url() . '/plugins/iCheck/icheck.min.js',
			assets_url() . '/plugins/jQueryUI/jquery-ui-timepicker-addon.js'
		);



		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_eksternal_keluar';
		$this->load->view('global/header', $this->output_head);

		$this->output_data['title'] = 'Keluar Eksternal';
		$this->output_head['search_type'] = 'surat_eksternal_keluar';
		$this->output_data['function_ref_id'] = 2;

		$surat = $result->row();
		$this->output_data['surat'] = $surat;

		$list = $this->admin_model->get_file_attachment('surat', $surat_id);
		$attachment = $list->result();
		$this->output_data['attachment'] = $attachment;

		$list = $this->admin_model->get_file_attachment('copy_surat', $surat_id);
		if ($list->num_rows() > 0) {
			$copy_surat = $list->row();
			$copy_surat->state = 'edit';
		} else {
			$copy_surat = new stdClass();
			$copy_surat->file_attachment_id = '-';
			$copy_surat->file = '-';
			$copy_surat->file_name = '-';
			$copy_surat->title = 'Copy Surat Keluar Eksternal ' . $surat->surat_no;
			$copy_surat->state = 'insert';
			$copy_surat->sort = '0';
		}
		$this->output_data['copy_surat'] = $copy_surat;

		$result = $this->admin_model->get_konsep_surat('surat', $surat_id, TRUE);
		$konsep = $result->row();
		$this->output_data['konsep'] = $konsep;

		$result = $this->admin_model->get_klasifikasi_arsip(trim($surat->kode_klasifikasi_arsip));
		$klasifikasi = $result->row();
		$this->output_data['klasifikasi'] = $klasifikasi;



		$view = 'external_outgoing_form_send';
		$this->load->view($view, $this->output_data);

		$this->load->view('global/footer');
	}

	/**
	 * 
	 */
	function register_arsip_sme($surat_id)
	{
		$result = $this->surat_model->get_surat($surat_id);
		if ($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/outgoing/');
			exit;
		}
		$surat = $result->row();

		$this->surat_model->init_arsip_sme($surat);

		// 		$this->incoming($surat_id);
	}

	/**
	 * 
	 */
	function register_arsip($surat_id)
	{
		$result = $this->surat_model->get_surat($surat_id);
		if ($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/outgoing/');
			exit;
		}
		$surat = $result->row();

		$this->surat_model->init_arsip_ske($surat);

		// 		$this->incoming($surat_id);
	}
}

/**
 * End of file
 */
