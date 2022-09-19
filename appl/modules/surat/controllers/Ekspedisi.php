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
 * @filesource Ekspedisi.php
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

class Ekspedisi extends LX_Controller {

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
		
		$this->load->model(array('auth/user_model', 'global/admin_model', 'surat_model', 'ekspedisi_model', 'disposisi_model'));
		$this->output_head['search_type'] = 'global';
	}
	
	/**
	 * Function to handle request from javascript ajax
	 * do nothing
	 */
	function index() {
		$this->list_outstanding();
	}
	
	/**
	 * @param unknown $type
	 */
	function list_outstanding() {

		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
													assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Surat Belum dikirim.';
		
		$list = $this->surat_model->get_unsend();
		$this->output_data['list'] = $list;
		
		$this->load->view('pengantar_list_outstanding', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $function_ref_id
	 * @param unknown $ref_id
	 */
	function sheet($function_ref_id = NULL, $ref_id = NULL) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
				'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
				assets_url() . '/plugins/datepicker/datepicker3.css',
// 				assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
				assets_url() . '/plugins/select2/select2.min.css'
		);
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
// 				assets_url() . '/plugins/datepicker/bootstrap-datepicker.js',
				assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
				assets_url() . '/plugins/select2/select2.full.min.js'
		);
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'surat_eksternal_masuk';
		$this->load->view('global/header', $this->output_head);

		$this->output_data['function_ref_id'] = $function_ref_id;
		switch ($function_ref_id) {
			case 1 :
				if($ref_id == NULL) {
					
				} else {
					$this->output_data['title'] = 'Surat Masuk Eksternal';
					
					$result = $this->surat_model->get_surat($ref_id);
					if($result->num_rows() == 0) {
						set_error_message('Data tidak dikenali.');
						redirect('global/dashboard');
						exit;
					}
					$surat = $result->row();
					$this->output_data['ref'] = $surat;
					
					$list = $this->surat_model->get_surat_masuk_dir($surat->jenis_agenda, $surat->surat_to_ref_id, $surat->status);
					$list_surat = $list->result();
					$this->output_data['list_surat'] = $list_surat;
					$this->output_data['jenis_agenda'] = 'SME';
					
					$view = 'ekspedisi_add';
					
					if($surat->surat_pengantar_id != '-') {
						$result = $this->ekspedisi_model->get_ekspedisi_aktif($ref_id);
// 						echo $this->db->last_query();
						if($result->num_rows() > 0) {
							$surat_pengantar = $result->row();
							if ($surat->status == 2) {
								$list = $this->ekspedisi_model->get_list_surat($surat_pengantar->ekspedisi_id);
							}else {
								$list = $this->ekspedisi_model->get_list_surat_by_status($surat_pengantar->ekspedisi_id, 5);
							}
							$list_surat = $list->result();
							$this->output_data['list_surat'] = $list_surat;
							$this->output_data['ref'] = $surat_pengantar;
							$this->output_data['cc'] = json_decode($surat_pengantar->tembusan);
							if(has_permission(8) || has_permission(9)) {
								if($surat_pengantar->status == 0) {
									$view = 'ekspedisi_edit';
								} else {
									$view = 'ekspedisi_edit';
								}
							} else {
								if($surat_pengantar->status == 2) {
									$view = 'ekspedisi_edit';
								} else {
									$view = 'ekspedisi_view';
								}
							}
						}
					}
					
					$this->output_data['title'] = 'Surat Masuk Eksternal';
					$this->load->view($view, $this->output_data);
				}
			
				break;
			case '2' :
				$this->output_data['title'] = 'Surat Keluar Eksternal ';
								
				break;
			case '3' :
				if($ref_id == NULL) {
					
				} else {
					$this->output_data['title'] = 'Nota Dinas';
					
					$result = $this->surat_model->get_surat($ref_id);
					if($result->num_rows() == 0) {
						set_error_message('Data tidak dikenali.');
						redirect('global/dashboard');
						exit;
					}
					$surat = $result->row();
					$this->output_data['ref'] = $surat;
					
					$list = $this->surat_model->get_surat_masuk_dir($surat->jenis_agenda, $surat->surat_to_ref_id, $surat->status);
					$list_surat = $list->result();
					$this->output_data['list_surat'] = $list_surat;
					$this->output_data['jenis_agenda'] = '';
					
					$view = 'ekspedisi_add';
					
					if($surat->surat_pengantar_id != '-') {
						$result = $this->ekspedisi_model->get_ekspedisi_aktif($ref_id);
						if($result->num_rows() > 0) {
							$surat_pengantar = $result->row();
							$this->output_data['ref'] = $surat_pengantar;
							$this->output_data['cc'] = json_decode($surat_pengantar->tembusan);
							if(has_permission(8) || has_permission(9)) {
								if($surat_pengantar->status == 0) {
									$view = 'ekspedisi_edit';
								} else {
									$view = 'ekspedisi_edit';
								}
							} else {
								if($surat_pengantar->status == 2) {
									$view = 'ekspedisi_edit';
								} else {
									$view = 'ekspedisi_view';
								}
							}
						}
					}
					
					$this->output_data['title'] = 'Surat Internal';
					$this->load->view($view, $this->output_data);
				}
				
				break;
			case '13' :
				if($ref_id == NULL) {
					
				} else {
					$this->output_data['title'] = 'Surat Perintah';
					
					$result = $this->surat_model->get_surat($ref_id);
					if($result->num_rows() == 0) {
						set_error_message('Data tidak dikenali.');
						redirect('global/dashboard');
						exit;
					}
					
					$surat = $result->row();
					$this->output_data['ref'] = $surat;
					
					$list = $this->surat_model->get_surat_internal_list($surat->jenis_agenda, $surat->surat_id, $surat->status);
					$list_surat = $list->result();
					$this->output_data['list_surat'] = $list_surat;
					$this->output_data['jenis_agenda'] = 'ST';
					
					$view = 'ekspedisi_tugas_add';
					
					if($surat->surat_pengantar_id != '-') {
						$result = $this->ekspedisi_model->get_ekspedisi_aktif($ref_id);
// 						echo $this->db->last_query();
						if($result->num_rows() > 0) {
							$surat_pengantar = $result->row();
							$this->output_data['ref'] = $surat_pengantar;
							$this->output_data['cc'] = json_decode($surat_pengantar->tembusan);
							if(has_permission(8) || has_permission(9)) {
								if($surat_pengantar->status == 0) {
									$view = 'ekspedisi_tugas_edit';
								} else {
									$view = 'ekspedisi_tugas_edit';
								}
							} else {
								if($surat_pengantar->status == 2) {
									$view = 'ekspedisi_tugas_edit';
								} else {
									$view = 'ekspedisi_tugas_view';
								}
							}
						}
					}
					
					$this->output_data['title'] = 'Surat Perintah';
					$this->load->view($view, $this->output_data);
					
				}
				break;	
			case 'terima' :
				$this->output_data['title'] = 'Surat Masuk Eksternal';
				
				if($ref_id == NULL) {
						
				} else {
					$result = $this->eksternal_model->get_surat_pengantar($ref_id);
					if($result->num_rows() == 0) {
						set_error_message('Data tidak dikenali.');
						redirect('global/dashboard');
						exit;
					}
					
					$surat_pengantar = $result->row();
					$this->output_data['ref'] = $surat_pengantar;
					if($surat_pengantar->status == 2) {
						set_error_message('Pengantar Sudah diterima.');
						redirect('surat/pengantar/' . $ref_id);
						exit;
					}
					
					$list = $this->eksternal_model->get_surat_masuk_pengantar($surat_pengantar->pengantar_surat_eksternal_id);
					$list_surat = $list->result();
					$this->output_data['list_surat'] = $list_surat;
					$this->output_data['cc'] = json_decode($surat_pengantar->tembusan);
					$this->load->view('terima_surat_pengantar', $this->output_data);
				}
				break;
			default :				
				if($function_ref_id == NULL) {
					
				} else {
					$result = $this->ekspedisi_model->get_ekspedisi($function_ref_id);
					if($result->num_rows() == 0) {
						set_error_message('Data tidak dikenali.');
						redirect('global/dashboard');
						exit;
					}
			
					$ekspedisi = $result->row();
					$this->output_data['ref'] = $ekspedisi;
						
					$list = $this->ekspedisi_model->get_list_surat($ekspedisi->ekspedisi_id);
					$list_surat = $list->result();
					$this->output_data['list_surat'] = $list_surat;
					$surat = $list->row();
					
					if($surat->surat_pengantar_id != '-') {
						$result = $this->ekspedisi_model->get_ekspedisi_aktif($surat->surat_id);
// 						echo $this->db->last_query();
						if($result->num_rows() > 0) {
							$surat_pengantar = $result->row();
							$this->output_data['ref'] = $surat_pengantar;
							$this->output_data['function_ref_id'] = $surat->function_ref_id;
							$this->output_data['jenis_agenda'] = $surat_pengantar->jenis_agenda;
							$this->output_data['cc'] = json_decode($surat_pengantar->tembusan);
							if(has_permission(9)) {
								if($surat_pengantar->status == 0) {
									$view = 'ekspedisi_edit';
								} else {
									$view = 'ekspedisi_edit';
								}
							} else {
								if($surat_pengantar->status == 2) {
									$view = 'ekspedisi_edit';
								} else {
									$view = 'ekspedisi_view';
								}
							}
						}
					}else {
						$this->output_data['function_ref_id'] = $surat->function_ref_id;
						$this->output_data['jenis_agenda'] = $surat->jenis_agenda;
						$this->output_data['cc'] = json_decode($ekspedisi->tembusan);

						if(has_permission(9)) {
							if($ekspedisi->status == 0) {
								$view = 'ekspedisi_edit';
							} else {
								$view = 'ekspedisi_view';
							}
						} else {
	//						if($ekspedisi->status == 1) {
	//							$view = 'terima_ekspedisi';
	//						} else {
								$view = 'ekspedisi_view';
	//						}
						}
					}
//  				echo $ekspedisi->status . ' - ' . $view;
					$this->load->view($view, $this->output_data);
				}
			break;
		}
		
		$this->load->view('global/footer');
	}
	
	/**
	 * @param unknown $ekspedisi_id
	 */
	function cetak($ekspedisi_id, $type = FALSE) {
		$result = $this->ekspedisi_model->get_ekspedisi($ekspedisi_id);
		if($result->num_rows() == 0) {
			echo 'Data tidak dikenali.';
			exit;
		}
		
		$ekspedisi = $result->row();
		$this->output_data['ekspedisi'] = $ekspedisi;
		
		$list = $this->ekspedisi_model->get_list_surat($ekspedisi_id);
		$list_surat = $list->result();
		$this->output_data['list_surat'] = $list_surat;
		$this->output_data['ekspedisi_id'] = $ekspedisi_id;
		
		if ($ekspedisi->jenis_agenda == 'SME') {
			$surat_row = $list->row();
			if ($surat_row->status == 2) {
				$this->output_data['list_surat'] = $list_surat;
				$cetak_disposisi = '';
			}else {
				$list = $this->ekspedisi_model->get_list_surat_by_status($ekspedisi_id, 5);
				$list_surat = $list->result();
				$this->output_data['list_surat'] = $list_surat;
				// $disposisi_list = $this->ekspedisi_model->get_list_disposisi_by_ekspedisi($ekspedisi_id);
				// $list_disposisi = $disposisi_list->row();
				// var_dump($list_disposisi);exit();
				$cetak_disposisi = 'disposisi';
			}
		}

		if($ekspedisi->jenis_agenda == 'SI'){
			foreach($list_surat as $surat) {
				$data['surat_id'][] = $surat->surat_id;
				$data['tujuan'][] = $surat->surat_from_ref;
			}
			
			$tembusan = $this->ekspedisi_model->get_list_distribusi_tembusan($data);
			$list_tembusan = $tembusan->result();
			$surat_row = $list->row();
			$this->output_data['list_tembusan'] = $list_tembusan;
			$this->output_data['list_tujuan'] = $surat_row->distribusi_tujuan;
			$this->output_data['opt_tujuan'] = $data;
		}

		$this->output_data['cc'] = json_decode($ekspedisi->tembusan);
		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>';
		if($ekspedisi->jenis_agenda == 'ST'){
			$html .= $this->load->view('print/ekspedisi_tugas', $this->output_data, TRUE);
		}else if($ekspedisi->jenis_agenda == 'SI'){
			$html .= $this->load->view('print/ekspedisi_internal', $this->output_data, TRUE);
		}else if($ekspedisi->jenis_agenda == 'SME'){
			if($cetak_disposisi == 'disposisi') {
				$html .= $this->load->view('print/ekspedisi_disposisi', $this->output_data, TRUE);
			}else {
				$html .= $this->load->view('print/ekspedisi', $this->output_data, TRUE);
			}
		}else {
			$html .= $this->load->view('print/ekspedisi', $this->output_data, TRUE);
		}

		if($type) {
			if ($type != 'SI'){
				foreach($list_surat as $row) {
					if ($type == 'SME'){
						$list_disposisi = $this->ekspedisi_model->get_list_disposisi($row->surat_id);
						if(isset($list_disposisi->distribusi)) {
							$distribusi = json_decode($list_disposisi->distribusi, TRUE);
							$distribusi = (isset($list_disposisi->to_user_id)) ? $distribusi[$list_disposisi->to_user_id] : '';
							
							$data['distribusi_disposisi'] = $distribusi;
						}
					}	
					
					$data['ref'] = $row;
					$data['ekspedisi_id'] = $ekspedisi_id;
					if($type == 'SME' && $row->status == 2) {
						$html .= '<pagebreak>' . $this->load->view('print/disposisi_' . $type . '_konsep', $data, TRUE);
					}
				}
			}
		}

		$this->load->library('m_pdf');
		$this->m_pdf->pdf->WriteHTML($html);
		$print_path = 'assets/media/print/pengantar/ekspedisi_' . $ekspedisi_id . '.pdf';
		$this->m_pdf->pdf->Output($print_path, 'I');
	}
	
}

/**
 * End of file
 */