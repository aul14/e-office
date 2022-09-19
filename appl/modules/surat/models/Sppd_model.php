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
 * @filesource Eksternal_model.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Sep 26, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Sppd_model extends LX_Model {
	
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
		
	}
	
	function get_referensi($type, $key = NULL) {
		if($key == NULL) {
			$return = array();
			$this->db->order_by("entry_id");
			$rows = $this->db->get_where('_ref_instansi_eksternal', array('nama_pejabat' => $type, 'status'=>1));
			
			foreach($rows->result() as $row) {
				$return[$row->entry_id] = $row->instansi;
			}
			return $return;
			
		} else {
			$rows = $this->db->get_where('_ref_instansi_eksternal', array('nama_pejabat' => $type, 'status'=>1, 'entry_id' => $key));
			if($rows->num_rows() > 0) {
				$row = $rows->row();
				return $row->instansi;
			} else {
				return '';
			}
		}
	}
	
	function insert_sppd() {
// 		var_dump($_POST); exit;
		$obj = $this->data_object->surat;
		if(!isset($_POST['surat_tgl_masuk'])) {
			$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		} else {
			$_POST['surat_tgl_masuk'] = human_to_db($_POST['surat_tgl_masuk']);
		}
		if(!isset($_POST['surat_no'])) {		
			$_POST['surat_no'] = '{surat_no}';
//			$_POST['surat_no'] = $_POST['kode_klasifikasi_arsip'] . '/' . $_POST['official_code'] . '/________/' . date('Y');
			
		}
		if(!isset($_POST['surat_tgl'])) {
			$_POST['surat_tgl'] = date('Y-m-d');
		} else {
			$_POST['surat_tgl'] = human_to_db($_POST['surat_tgl']);
		}
		
		if($this->_validate_post_data($obj, 'add') != FALSE) {

			$result = $this->db->get_where('system_security.function_ref', array('function_ref_id' => $_POST['function_ref_id']));
			$function_data = $result->row();
			
			$surat_id = generate_unique_id();
			$data = array_intersect_key($_POST, $this->data_object->surat);
			$data['surat_id'] = $surat_id;
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
//			$data['status'] = 99;
//			$data['jenis_agenda'] = 'I';
			$agenda = $_POST['jenis_agenda'] . ' - ';
			if ($_POST['create_agenda'] == 1) {
				$data['agenda_id'] = $this->lx->number_generator($function_data->format_agenda);
				$agenda .= $data['agenda_id'];
			}

			$data['surat_from_ref_data'] = json_encode($data['surat_from_ref_data']);
			
			$data['surat_to_ref_data'] = json_encode($data['surat_to_ref_data']);
			
			if(isset($_POST['tembusan'])) {
				$tembusan = array();
				foreach($_POST['tembusan'] as $k => $v) {
					$tembusan[] = $v;
				}
				$data['tembusan'] = json_encode($tembusan);
			}
			
			if(isset($_POST['approval'])) {
				
				if(isset($_POST['signed'])) {
					$max_key = $_POST['approval']['direksi'][$_POST['signed']]['index'];
					
					foreach($_POST['approval']['direksi'] as $ak => $av) {
						if($av['index'] > $max_key) {
							unset($_POST['approval']['direksi'][$ak]);
						}
					}
				}
				
				$data['approval'] = json_encode($_POST['approval']);
			}
			
			if(isset($_POST['signed'])) {
				$signed = array();
				$signed['unit_id'] = $_POST['signed'];
				$signed['jabatan'] = $_POST['approval']['direksi'][$_POST['signed']]['jabatan'];
				$signed['unit_name'] = $_POST['approval']['direksi'][$_POST['signed']]['unit_name'];
				$signed['nama_pejabat'] = $_POST['approval']['direksi'][$_POST['signed']]['nama_pejabat'];
				$signed['nip'] = $_POST['approval']['direksi'][$_POST['signed']]['nip'];
				
				$data['signed'] = json_encode($signed);
			}
			
		
			$this->db->insert('surat', $data);

			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
				$this->load->library('upload', $config_file);
				
				$i = 0;
				foreach($_POST['attachment'] as $k => $v) {
					if($v['title'] != '' && !empty($_FILES['attachment_file_' . $k]['name'])) {
						if (!$this->upload->do_upload('attachment_file_' . $k)) {
							set_error_message($this->upload->display_errors());
						} else {
							$file = $this->upload->data();
							$file_attached = array();
							$file_attached['organization_id'] = get_user_data('organization_id');
							$file_attached['table'] = 'surat';
							$file_attached['ref_id'] = $surat_id;
							$file_attached['title'] = $v['title'];
							$file_attached['file_name'] = $_FILES['attachment_file_' . $k]['name'];
							$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
							$file_attached['sort'] = $i++;
							$this->db->insert('file_attachment', $file_attached);
							
						}
					}
				}
			}
			
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat';
			$data['ref_id'] = $surat_id;
			$data['user_id'] = get_user_id();
			$data['flow_seq'] = 0;
			$data['note'] = 'Surat ' . $_POST['function_ref_name'] . ' dibuat oleh ' . get_user_data('user_name');
			$this->db->insert('process_notes', $data);
			
			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $surat_id, 'agenda' => $agenda, 'note' => $data['note'], 'detail_link' => ($_POST['return'] . '_view/' . $surat_id), 'notify_user_id' => get_user_id(), 'status' => 0, 'read' => 0));
	
			set_success_message('Data surat ' . humanize($_POST['function_ref_name']) . ' berhasil disimpan.');
			redirect($_POST['return'] . '/' . $surat_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}
	
	
	
	
}