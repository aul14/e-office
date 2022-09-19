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

class Eksternal_model extends LX_Model {
	
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
		
	}
	
	/**
	 * @param unknown $surat_eksternal_id
	 */
	function get_surat_eksternal($surat_eksternal_id = NULL) {
		if($surat_eksternal_id == NULL) {
			$sql = "SELECT * FROM surat_eksternal WHERE status >= 0";
			return $this->db->query($sql);
		} else {
			$sql = "SELECT se.*, u.user_name, osa.status unit_archive_status FROM surat_eksternal se
					LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat_eksternal' AND osa.ref_id = se.surat_eksternal_id " . ((!has_permission(8)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
 					JOIN system_security.users u ON(u.user_id = se.created_id)  
					WHERE se.status >= 0 AND surat_eksternal_id = '$surat_eksternal_id'";
			return $this->db->query($sql);
		}
	}
	
	/**
	 * @param unknown $surat_eksternal_id
	 */
	function get_surat_eksternal_flow($surat_eksternal_id) {
		$sql = "SELECT pn.*, u.user_name FROM process_notes pn
				JOIN system_security.users u ON(u.user_id = pn.user_id)  
				WHERE \"table\" = 'surat_eksternal' AND ref_id = '$surat_eksternal_id' ORDER BY created_time";
		return $this->db->query($sql);
	}
	
	/**
	 * @param unknown $surat_eksternal_id
	 */
	function get_surat_eksternal_ttd($surat_eksternal_id) {
		return $this->db->get_where('surat_eksternal_ttd', array('surat_eksternal_id' => $surat_eksternal_id));
	}
	
	function get_current_no() {
		$return = array('error' => '', 'message' => '');
		$result = $this->db->get_where('konsep_surat', array('table' => 'surat_eksternal', 'status' => 1, 'ref_id' => $_POST['ref_id']));
		$konsep_surat = $result->row_array();
		
		$return['surat_no'] = $this->lx->number_generator($this->_system_config('variables', 'no_surat_eksternal'));
		$return['surat_tgl'] = date('d-m-Y');
		$return['message'] = 'No. ' . $return['surat_no'] . ' Tanggal ' . $return['surat_tgl'];
		
		$this->db->update('surat_eksternal', array('surat_no' => $return['surat_no'], 'surat_tgl' => human_to_db($return['surat_tgl'])), array('surat_eksternal_id' => $_POST['ref_id']));
		
		$konsep_surat['konsep_text'] = str_replace('{surat_no}', $return['surat_no'], $konsep_surat['konsep_text']);
		$konsep_surat['konsep_text'] = str_replace('{surat_tgl}', $return['surat_tgl'], $konsep_surat['konsep_text']);
		$this->db->update('konsep_surat', array('konsep_text' => $konsep_surat['konsep_text']), array('table' => 'surat_eksternal', 'status' => 1, 'ref_id' => $_POST['ref_id']));
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 */
	function insert_surat_masuk() {
// 		var_dump($_POST);
// 		exit;
		if($this->_validate_post_data($this->data_object->surat_eksternal, 'add') != FALSE) {
			$result = $this->db->get_where('system_security.function_ref', array('module_function' => 'external/incoming'));
			$function_data = $result->row();
			
			$surat_eksternal_id = generate_unique_id();
			
			$data = array_intersect_key($_POST, $this->data_object->surat_eksternal);
			$data['surat_eksternal_id'] = $surat_eksternal_id; 
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
			$data['agenda_id'] = $this->lx->number_generator($function_data->format_agenda);
			$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			
			$this->db->insert('surat_eksternal', $data);
			
			// set workspace
			$list = user_with_permission(8);
			$note = 'Surat Masuk Eksternal No. ' . $data['surat_no'] . ' dari ' . $data['surat_ext_nama'] . ' &nbsp; | &nbsp; ' . $data['surat_ext_title'] . ' &nbsp; | &nbsp; ' .  $data['surat_ext_instansi'];
			foreach ($list->result() as $row) {
				$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $data['surat_eksternal_id'], 'agenda' => ('M-' . $data['agenda_id']), 'note' => $note, 'detail_link' => ('surat/external/incoming_view/' . $data['surat_eksternal_id']), 'notify_user_id' => $row->user_id, 'status' => 0, 'read' => 0));
			}
			
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf';
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
							$file_attached['table'] = 'surat_eksternal';
							$file_attached['ref_id'] = $surat_eksternal_id;
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
			$data['flow_type'] = 1;  //forward
			$data['table'] = 'surat_eksternal';
			$data['ref_id'] = $surat_eksternal_id;
			$data['user_id'] = get_user_id();
			$data['flow_seq'] = 0;
			$this->db->insert('process_notes', $data);
			
			set_success_message('Data surat masuk berhasil disimpan.');
			redirect('surat/external/incoming/' . $surat_eksternal_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 *
	 */
	function update_surat_masuk() {
//  	var_dump($_POST);exit;
		if($this->_validate_post_data($this->data_object->surat_eksternal, 'edit') != FALSE) {
			$surat_eksternal_id = $_POST['surat_eksternal_id'];
			unset($_POST['surat_eksternal_id']);
				
			$data = array_intersect_key($_POST, $this->data_object->surat_eksternal);
			$data['modified_id'] = get_user_id();
			$data['modified_time'] = date('Y-m-d H:i:s');
			
			$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			
			$this->db->update('surat_eksternal', $data, array('surat_eksternal_id' => $surat_eksternal_id));

			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf';
				$this->load->library('upload', $config_file);
				
				$i = 0;
				foreach($_POST['attachment'] as $k => $v) {
					switch ($v['state']) {
						case 'insert' :
							if(!empty($_FILES['attachment_file_' . $k]['name'])) {
								if (!$this->upload->do_upload('attachment_file_' . $k)) {
									set_error_message($this->upload->display_errors());
								} else {
									$file = $this->upload->data();
									$file_attached = array();
									$file_attached['organization_id'] = get_user_data('organization_id');
									$file_attached['table'] = 'surat_eksternal';
									$file_attached['ref_id'] = $surat_eksternal_id;
									$file_attached['title'] = $v['title'];
									$file_attached['file_name'] = $_FILES['attachment_file_' . $k]['name'];
									$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
									$file_attached['sort'] = $i++;
									$this->db->insert('file_attachment', $file_attached);
								}
							}
							
						break;
						case 'delete' :
							unlink(str_replace('/lx_media/', 'assets/media/', $v['file']));
							$this->db->delete('file_attachment', array('file_attachment_id' => $v['id']));
							
						break;
						default:
							$file_attached = array();
							if(!empty($_FILES['attachment_file_' . $k]['name'])) {
								if (!$this->upload->do_upload('attachment_file_' . $k)) {
									set_error_message($this->upload->display_errors());
								} else {
									$file = $this->upload->data();
									$file_attached['file_name'] = $_FILES['attachment_file_' . $k]['name'];
									$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
								}
							}
							$file_attached['sort'] = $i++;
							$file_attached['title'] = $v['title'];
							$this->db->update('file_attachment', $file_attached, array('file_attachment_id' => $v['id']));
							
						break;
					}

					log_message('debug', 'update attachment : ' . $this->db->last_query());
				}
				
			}
			set_success_message('Data surat masuk berhasil perbaharui.');
			redirect('surat/external/incoming/' . $surat_eksternal_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 *
	 */
	function insert_surat_keluar() {
//		var_dump($_POST);
// 		var_dump($_FILES);
//		exit;
		$obj = $this->data_object->surat_eksternal;
		$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		$_POST['surat_no'] = '{surat_no}';
		$_POST['surat_tgl'] = date('Y-m-d');

		if($this->_validate_post_data($obj, 'add') != FALSE) {
			$result = $this->db->get_where('system_security.function_ref', array('module_function' => 'external/outgoing'));
			$function_data = $result->row();
	
			$surat_eksternal_id = generate_unique_id();
	
			$data = array_intersect_key($_POST, $this->data_object->surat_eksternal);
			$data['surat_no'] = '{surat_no}';
			$data['surat_eksternal_id'] = $surat_eksternal_id;
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
 			$data['jenis_agenda'] = 'K';
 			
 			if(isset($_POST['tembusan'])) {
 				$tembusan = array();
 				foreach($_POST['tembusan'] as $k => $v) {
 					$tembusan[] = $v;
 				}
 				$data['tembusan'] = json_encode($tembusan);
 			}

 			$approval = array();
 			$approval[get_user_data('unit_level')] = array();
 			$approval[get_user_data('unit_level')]['name'] = trim(get_user_data('unit_code')) . ' - ' . get_user_data('unit_name');
 			$approval[get_user_data('unit_level')]['status'] = FALSE;
 			
 			$level = get_user_data('unit_level');
 			$parent = get_user_data('unit_parent_id');
 			while ($level != 'L1') {
 				$result = $this->db->get_where('system_security.organization_structure', array('organization_structure_id' => $parent));
 				if($result->num_rows() > 0) {
 					$unit = $result->row();
 					$approval[$unit->level] = array();
 					$approval[$unit->level]['name'] = trim($unit->unit_code) . ' - ' . $unit->unit_name;
 					$approval[$unit->level]['status'] = FALSE;
 					
 					$level = $unit->level;
 					$parent = $unit->parent_id;
 			
 				} else {
 					$level = 'L1';
 						
 				}
 			}
 			$data['approval'] = json_encode($approval);
 			
			$this->db->insert('surat', $data);

			foreach($_POST['surat_eksternal_ttd'] as $k => $v) {
				if($v['surat_int_unit'] != '') {
					$v['surat_eksternal_id'] = $surat_eksternal_id;
	 				$this->db->insert('surat_eksternal_ttd', $v);
				}
			}
			
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf';
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
							$file_attached['table'] = 'surat_eksternal';
							$file_attached['ref_id'] = $surat_eksternal_id;
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
			$data['table'] = 'surat_eksternal';
			$data['ref_id'] = $surat_eksternal_id;
			$data['user_id'] = get_user_id();
			$data['flow_seq'] = 0;
			$this->db->insert('process_notes', $data);
			
			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $surat_eksternal_id, 'agenda' => ('K-'), 'note' => $note, 'detail_link' => ('surat/external/outgoing_view/' . $surat_eksternal_id), 'notify_user_id' => get_user_id(), 'status' => 0, 'read' => 0));
	
			set_success_message('Data surat masuk berhasil disimpan.');
			redirect('surat/external/outgoing/' . $surat_eksternal_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 *
	 */
	function update_surat_keluar() {
		//  		var_dump($_POST);
		//  		var_dump($_FILES);
		//  		exit;
		$obj = $this->data_object->surat_eksternal;
		$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		if($this->_validate_post_data($obj, 'edit') != FALSE) {
				
			$surat_eksternal_id = $_POST['surat_eksternal_id'];
			unset($_POST['surat_eksternal_id']);
	
			$data = array_intersect_key($_POST, $this->data_object->surat_eksternal);
			$data['modified_id'] = get_user_id();
			$data['modified_time'] = date('Y-m-d H:i:s');
				
// 			if(isset($_POST['surat_tgl'])) {
// 				$data['surat_tgl'] = human_to_db($data['surat_tgl']);
// 			}
			if(isset($_POST['surat_tgl_masuk'])) {
				$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			}

			if(isset($_POST['tembusan'])) {
				$tembusan = array();
				foreach($_POST['tembusan'] as $k => $v) {
					$tembusan[] = $v;
				}
				$data['tembusan'] = json_encode($tembusan);
			}
			$this->db->update('surat_eksternal', $data, array('surat_eksternal_id' => $surat_eksternal_id));
			
			$i = 1;
			foreach($_POST['surat_eksternal_ttd'] as $k => $v) {
				
				$v['sort'] = $i++;
				switch ($v['state']) {
					case 'insert' :
						unset($v['state']);
						if($v['surat_int_unit'] != '') {
							$v['surat_eksternal_id'] = $surat_eksternal_id;
							unset($v['surat_eksternal_ttd_id']);
							$this->db->insert('surat_eksternal_ttd', $v);
						}
						break;
					case 'delete' :
						unset($v['state']);
						$this->db->delete('surat_eksternal_ttd', array('surat_eksternal_ttd_id' => $v['surat_eksternal_ttd_id']));
						break;
					default:
						unset($v['state']);
						$surat_eksternal_ttd_id = $v['surat_eksternal_ttd_id'];
						unset($v['surat_eksternal_ttd_id']);
 						$this->db->update('surat_eksternal_ttd', $v, array('surat_eksternal_ttd_id' => $surat_eksternal_ttd_id));
// 						echo $this->db->last_query();
					break;
				}
				
			}
			
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf';
				$this->load->library('upload', $config_file);
	
				$i = 0;
				foreach($_POST['attachment'] as $k => $v) {
					switch ($v['state']) {
						case 'insert' :
							if(!empty($_FILES['attachment_file_' . $k]['name'])) {
								if (!$this->upload->do_upload('attachment_file_' . $k)) {
									set_error_message($this->upload->display_errors());
								} else {
									$file = $this->upload->data();
									$file_attached = array();
									$file_attached['organization_id'] = get_user_data('organization_id');
									$file_attached['table'] = 'surat_eksternal';
									$file_attached['ref_id'] = $surat_eksternal_id;
									$file_attached['title'] = $v['title'];
									$file_attached['file_name'] = $_FILES['attachment_file_' . $k]['name'];
									$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
									$file_attached['sort'] = $i++;
									$this->db->insert('file_attachment', $file_attached);
								}
							}
								
							break;
						case 'delete' :
							unlink(str_replace('/lx_media/', 'assets/media/', $v['file']));
							$this->db->delete('file_attachment', array('file_attachment_id' => $v['id']));
								
							break;
						default:
							$file_attached = array();
							if(!empty($_FILES['attachment_file_' . $k]['name'])) {
								if (!$this->upload->do_upload('attachment_file_' . $k)) {
									set_error_message($this->upload->display_errors());
								} else {
									$file = $this->upload->data();
									$file_attached['file_name'] = $_FILES['attachment_file_' . $k]['name'];
									$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
								}
							}
							$file_attached['sort'] = $i++;
							$file_attached['title'] = $v['title'];
							$this->db->update('file_attachment', $file_attached, array('file_attachment_id' => $v['id']));
								
							break;
					}
					log_message('debug', 'update attachment : ' . $this->db->last_query());
				}
	
			}
			
			set_success_message('Data surat masuk berhasil perbaharui.');
			redirect('surat/external/outgoing/' . $surat_eksternal_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 * 
	 */
	function add_konsep() {

		$sql = "SELECT MAX(ks.version) last_version FROM konsep_surat ks
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		if($result->num_rows() == 0) {
			$this_version = 1;
		} else {
			$version = $result->row();
			$this_version = $version->last_version + 1;
		}
		
		$this->db->update('konsep_surat', array('status' => 0), array('table' => $_POST['table'], 'ref_id' => $_POST['ref_id']));
		
		$konsep = array();
		$konsep['organization_id'] = get_user_data('organization_id');
		$konsep['table'] = $_POST['table'];
		$konsep['ref_id'] = $_POST['ref_id'];
		$konsep['format_surat_id'] = $_POST['format_surat_id'];
		$konsep['title'] = $_POST['format_surat_text'];
		$konsep['version'] = $this_version;
		
		$result = $this->db->get_where('system_security.format_surat', array('format_surat_id' => $_POST['format_surat_id']));
		$format = $result->row();
		
		$result = $this->db->get_where('surat_eksternal', array('surat_eksternal_id' => $_POST['ref_id']));
		$param = $result->row_array();

		$list = $this->get_surat_eksternal_ttd($_POST['ref_id']);
		$ttd = $list->result();
			
		foreach($ttd as $row) {
			if(!isset($count[$row->type_ttd])) {
				$count[$row->type_ttd] = 1;
			}
			$param[$row->type_ttd . '_jabatan_' . $count[$row->type_ttd]] = $row->surat_int_jabatan;
			$param[$row->type_ttd . '_unit_' . $count[$row->type_ttd]] = humanize(str_replace('DIREKTORAT ', ' ', $row->surat_int_unit));
			$param[$row->type_ttd . '_nama_' . $count[$row->type_ttd]] = $row->surat_int_nama;
			$param[$row->type_ttd . '_nip_' . $count[$row->type_ttd]] = $row->surat_int_nip;
		
			$count[$row->type_ttd]++;
		}
		
		$konsep['konsep_text'] = sprintformat($format->format_text, $param);
		
		$this->db->insert('konsep_surat', $konsep);
			
		$sql = "SELECT ks.*, fs.format_title FROM konsep_surat ks
				JOIN system_security.format_surat fs ON(fs.format_surat_id = ks.format_surat_id)
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'
				ORDER BY version DESC";
			
		$list = $this->db->query($sql);
		$opt = '';
		$selected = ' selected="selected" ';
		foreach ($list->result() as $row) {
			$opt .= '<option value="' . $row->konsep_surat_id . '" ' . $selected . ' >' . $row->format_title . ' - Versi ' . $row->version . '</option>';
			$selected = '';
		}
		
		$return = array('error' => '', 'message' => 'Konsep berhasil disimpan.', 'execute' => "", 'new_option' => $opt, 'konsep_text' => $konsep['konsep_text']);
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 *
	 */
	function save_konsep() {
//  		var_dump($_POST); exit;
		$konsep = array();
		$konsep['organization_id'] = get_user_data('organization_id');
		$konsep['table'] = $_POST['table'];
		$konsep['ref_id'] = $_POST['ref_id'];
		$konsep['format_surat_id'] = $_POST['format_surat_id'];
		$konsep['title'] = $_POST['format_surat_text'];
		$konsep['konsep_text'] = $_POST['konsep_text'];
		
		if($_POST['konsep_surat_id'] == '0') {
 			$this->db->insert('konsep_surat', $konsep);
			
			$sql = "SELECT ks.*, fs.format_title FROM konsep_surat ks 
					JOIN system_security.format_surat fs ON(fs.format_surat_id = ks.format_surat_id) 
					WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'
					ORDER BY version DESC";
			
			$list = $this->db->query($sql);
			$opt = '';
			$selected = ' selected="selected" ';
			foreach ($list->result() as $row) {
				$opt .= '<option value="' . $row->konsep_surat_id . '" ' . $selected . ' >' . $row->format_title . ' - Versi ' . $row->version . '</option>';
				$selected = '';
			}
			
			$return = array('error' => '', 'message' => 'Konsep berhasil disimpan.', 'execute' => "", 'new_option' => $opt);
			
		} else {
			$konsep_surat_id = $_POST['konsep_surat_id'];
			
 			$this->db->update('konsep_surat', $konsep, array('konsep_surat_id' => $konsep_surat_id));
			$return = array('error' => '', 'message' => 'Konsep berhasil diupdate.', 'execute' => "");
		}
	
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 *
	 */
	function save_konsep_as() {

		$sql = "SELECT MAX(ks.version) last_version FROM konsep_surat ks
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		if($result->num_rows() == 0) {
			$this_version = 1;
		} else {
			$version = $result->row();
			$this_version = $version->last_version + 1;
		}

		$this->db->update('konsep_surat', array('status' => 0), array('table' => $_POST['table'], 'ref_id' => $_POST['ref_id']));
		
		$konsep = array();
		$konsep['organization_id'] = get_user_data('organization_id');
		$konsep['table'] = $_POST['table'];
		$konsep['ref_id'] = $_POST['ref_id'];
		$konsep['format_surat_id'] = $_POST['format_surat_id'];
		$konsep['title'] = $_POST['format_surat_text'];
		$konsep['konsep_text'] = $_POST['konsep_text'];
		$konsep['version'] = $this_version;

		$this->db->insert('konsep_surat', $konsep);
			
		$sql = "SELECT ks.*, fs.format_title FROM konsep_surat ks
				JOIN system_security.format_surat fs ON(fs.format_surat_id = ks.format_surat_id)
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'
				ORDER BY version DESC";
			
		$list = $this->db->query($sql);
		$konsep_surat_id = $list->row()->konsep_surat_id;
		$opt = '';
		$selected = ' selected="selected" ';
		foreach ($list->result() as $row) {
			$opt .= '<option value="' . $row->konsep_surat_id . '" ' . $selected . ' >' . $row->format_title . ' - Versi ' . $row->version . '</option>';
			$selected = '';
		}
			
		$return = array('error' => '', 'message' => 'Konsep berhasil disimpan.', 'execute' => "", 'new_option' => $opt, 'konsep_surat_id' => $konsep_surat_id);
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 *
	 */
	function remove_konsep() {
		$konsep_surat_id = $_POST['konsep_surat_id'];
		
		$this->db->delete('konsep_surat', array('konsep_surat_id' => $konsep_surat_id));
		
		$this->db->update('konsep_surat', array('status' => 0), array('table' => $_POST['table'], 'ref_id' => $_POST['ref_id']));
		$sql = "SELECT MAX(ks.version) last_version FROM konsep_surat ks
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		if($result->num_rows() > 0) {
			$version = $result->row();
			$this_version = $version->last_version;
			$this->db->update('konsep_surat', array('status' => 1), array('table' => $_POST['table'], 'ref_id' => $_POST['ref_id'], 'version' => $this_version));
		}
		
		$sql = "SELECT ks.*, fs.format_title FROM konsep_surat ks
				JOIN system_security.format_surat fs ON(fs.format_surat_id = ks.format_surat_id)
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'
				ORDER BY version DESC";
			
		$list = $this->db->query($sql);
		$opt = '';
		$selected = ' selected="selected" ';
		foreach ($list->result() as $row) {
			$opt .= '<option value="' . $row->konsep_surat_id . '" ' . $selected . ' >' . $row->format_title . ' - Versi ' . $row->version . '</option>';
			$selected = '';
		}
			
		$return = array('error' => '', 'message' => 'Konsep berhasil dihapus.', 'execute' => "", 'new_option' => $opt);
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	function comment_approval() {
//		var_dump($_POST);
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		if($result->num_rows() > 0) {
			$surat = $result->row();
		
			$return = array('error' => '', 'message' => 'Komentar berhasil ditambahkan.', 'execute' => "");
			$approval = json_decode($surat->approval, TRUE);
			
			$comments = isset($approval[$_POST['level']]['comments']) ? $approval[$_POST['level']]['comments'] : array();
			$return['count'] = count($comments);
			
			$comment = array();
			$comment['time'] = date('d-m-Y H:i:s');
			$comment['user_id'] = get_user_id();
			$comment['user_name'] = get_user_data('user_name');
			$comment['note'] = $_POST['note'];
			
			$comments[count($comments) + 1] = $comment;
			$approval[$_POST['level']]['comments'] = $comments;
	
			if(isset($_POST['status']) && $_POST['status'] == 1) {
				$approval[$_POST['level']]['status'] = TRUE;
				$return['reload'] = 1;
			}
			
			$this->db->update('surat', array('approval' => json_encode($approval)), array('surat_id' => $_POST['ref_id']));
		} else {
			$return = array('error' => 1, 'message' => 'data tidak dikenali.');
				
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 */
	function return_data() {
		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");
		
		$sql = "UPDATE surat_eksternal 
				   SET status = (CASE WHEN status = 0 THEN 0 ELSE status - 1 END) 
				 WHERE surat_eksternal_id = '" . $_POST['ref_id'] . "'";
		$this->db->query($sql);

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = -1;//return
		$data['table'] = 'surat_eksternal';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		$sql = "SELECT se.*, fp.title process_title, fp.role_handle, fp.permission_handle FROM surat_eksternal se
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = se.status AND fp.status = 1)
				WHERE se.surat_eksternal_id = '" . $_POST['ref_id'] .  "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();
		
		$jenis_agenda = ($surat->jenis_agenda == 'M') ? 'Masuk' : 'Keluar';
		
		$subject = "Notifikasi Proses surat $jenis_agenda Eksternal - " . $surat->jenis_agenda . '-' . $surat->agenda_id;
		$body = 'Surat ' . $jenis_agenda . ' Eksternal ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' dikembalikan ke proses ' . $surat->process_title;
		
// 		$list_tujuan = user_in_unit($surat->surat_int_unit_id);
		$list_tujuan = user_in_unit(get_user_data('unit_id'));
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => $row_tujuan->user_id));
// 			echo $this->db->last_query();
			// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}
		
// 		$note = 'Surat Masuk Eksternal No. ' . $surat->surat_no . ' dari ' . $surat->surat_ext_title . ', '  . $surat->surat_ext_instansi;
// 		$list = user_with_permission(8);
// 		foreach ($list->result() as $row) {
// 			$this->db->update('notify', 
// 								array('note' => $note), 
// 								array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => $row->user_id)
// 					);
// // 			$this->db->update('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id), 'note' => $note, 'detail_link' => ('surat/external/incoming/' . $surat->surat_eksternal_id), 'notify_user_id' => $row->user_id, 'status' => $surat->status, 'read' => 0));
// 			echo $this->db->last_query();
// 			$this->_send_mail_notification($row->email, $subject, $body, array());
// 		}
// 		exit;
		set_success_message('Berkas berhasil dikembalikan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 */
	function proses_data() {
		
 		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");

		$sql = "UPDATE surat_eksternal
				   SET status = (CASE WHEN status = " . $_POST['last_flow'] . " THEN " . $_POST['last_flow'] . " ELSE status + 1 END)
				 WHERE surat_eksternal_id = '" . $_POST['ref_id'] . "'";
		$this->db->query($sql);
		
		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['table'] = 'surat_eksternal';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
		
		$sql = "SELECT se.*, fp.title process_title, fp.role_handle, fp.permission_handle FROM surat_eksternal se
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = se.status AND fp.status = 1)
				WHERE se.surat_eksternal_id = '" . $_POST['ref_id'] . "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();
		
		if($surat->jenis_agenda == 'M') {
			$jenis_agenda = 'Masuk';
// 			$return['execute'] = "location.assign('" . site_url('surat/external/incoming_view/' . $_POST['ref_id']) . "')";
		} else {
			$jenis_agenda = 'Keluar';
// 			$return['execute'] = "location.assign('" . site_url('surat/external/outgoing_view/' . $_POST['ref_id']) . "')";
		}
		
		$subject = "Notifikasi Proses surat $jenis_agenda Eksternal - " . $surat->jenis_agenda . '-' . $surat->agenda_id;
		$body = 'Surat ' . $jenis_agenda . ' Eksternal ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' telah memasuki proses ' . $surat->process_title;
		
// 		$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id']));
// 		$list = user_with_permission(8);
// 		foreach ($list->result() as $row) {
// 			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id), 'note' => ('Surat telah memasuki proses ' . $surat->process_title), 'detail_link' => ('surat/external/incoming_view/' . $data['ref_id']), 'notify_user_id' => $row->user_id, 'status' => $surat->status, 'read' => 0));
			
// 			$this->_send_mail_notification($row->email, $subject, $body, array());
// 		}
		
		set_success_message('Proses berkas berhasil dilanjutkan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * @param unknown $jenis_agenda
	 * @param unknown $dir
	 * @param unknown $status
	 */
	function get_surat_masuk_dir($jenis_agenda, $dir, $status) {
		$sql = "SELECT * FROM surat_eksternal WHERE status = $status AND jenis_agenda = '$jenis_agenda' AND surat_int_dir = '$dir' ";
		return $this->db->query($sql);
		
	}
	
	/**
	 * @param unknown $pengantar_surat_eksternal_id
	 */
	function get_surat_masuk_pengantar($pengantar_surat_eksternal_id) {
		$sql = "SELECT * FROM surat_eksternal WHERE surat_pengantar_id = '$pengantar_surat_eksternal_id' ";
		return $this->db->query($sql);
		
	}
	
	/**
	 * @param unknown $surat_pengantar_id
	 */
	function get_surat_pengantar($surat_pengantar_id = NULL) {
		if($surat_pengantar_id == NULL) {
			$sql = "SELECT * FROM pengantar_surat_eksternal WHERE status >= 0";
			return $this->db->query($sql);
			
		} else {
			$sql = "SELECT * FROM pengantar_surat_eksternal WHERE status >= 0 AND pengantar_surat_eksternal_id = '$surat_pengantar_id'";
			return $this->db->query($sql);
			
		}
	}
	
	/**
	 * @param unknown $surat_eksternal_id
	 */
	function get_surat_pengantar_aktif($surat_eksternal_id) {
		$sql = "SELECT * FROM pengantar_surat_eksternal pe 
				JOIN pengantar_surat_eksternal_list pel ON(pel.pengantar_surat_eksternal_id = pe.pengantar_surat_eksternal_id) 
				WHERE pe.status >= 0 AND pel.status_terima >= 0 AND surat_eksternal_id = '$surat_eksternal_id'";
		return $this->db->query($sql);
	}
	
	/**
	 * 
	 */
	function insert_surat_pengantar() {
//		var_dump($_POST);

		if(isset($_POST['detail_pengantar'])) { 
			if($this->_validate_post_data($this->data_object->surat_pengantar, 'add') != FALSE) {
	
				$data = array_intersect_key($_POST, $this->data_object->surat_pengantar);
				$data['pengantar_surat_eksternal_id'] = generate_unique_id();
				$data['created_id'] = get_user_id();
				$data['organization_id'] = get_user_data('organization_id');
				
				if(isset($_POST['surat_tembusan'])) {
					$tembusan = array();
					foreach($_POST['surat_tembusan'] as $k => $v) {
						$tembusan[] = $v;
					}
					$data['tembusan'] = json_encode($tembusan);
				}
	
				$this->db->insert('pengantar_surat_eksternal', $data);
				
				foreach($_POST['detail_pengantar'] as $k => $v) {
					$this->db->update('surat_eksternal', array('pengantar' => 1, 'surat_pengantar_id' => $data['pengantar_surat_eksternal_id']), array('surat_eksternal_id' => $v));
					
					$detail = array();
					$detail['pengantar_surat_eksternal_id'] = $data['pengantar_surat_eksternal_id'];
					$detail['surat_eksternal_id'] = $v;
					
					$this->db->insert('pengantar_surat_eksternal_list', $detail);
							
					// set workspace
					$list = user_with_permission(8);
					foreach ($list->result() as $row) {
						$this->db->update('notify', array('note' => ('Surat Pengantar Masuk Eksternal untuk ' . $data['tujuan_unit'] . ' baru dibuat')), array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $v, 'notify_user_id' => $row->user_id));
					}
				}
				
				set_success_message('Data surat pengantar berhasil disimpan.');
				redirect('surat/external/pengantar/' . $data['pengantar_surat_eksternal_id']);
				exit;
			} else {
				
				set_error_message(validation_errors());
			}
		} else {
			set_error_message('Tidak ada surat yang dipilih.');
		}
	}
	
	/**
	 * 
	 */
	function update_surat_pengantar() {

		if(isset($_POST['detail_pengantar'])) {
			if($this->_validate_post_data($this->data_object->surat_pengantar, 'edit') != FALSE) {

				$data = array_intersect_key($_POST, $this->data_object->surat_pengantar);
				$data['modified_id'] = get_user_id();
				$data['modified_time'] = date('Y-m-d H:i:s');
				
				if(isset($_POST['surat_tembusan'])) {
					$tembusan = array();
					foreach($_POST['surat_tembusan'] as $k => $v) {
						$tembusan[] = $v;
					}
					$data['tembusan'] = json_encode($tembusan);
				}
				
				unset($data['pengantar_surat_eksternal_id']);
				$this->db->update('pengantar_surat_eksternal', $data, array('pengantar_surat_eksternal_id' => $_POST['pengantar_surat_eksternal_id']));
				
				$this->db->update('surat_eksternal', array('surat_pengantar_id' => '-'), array('surat_pengantar_id' => $_POST['pengantar_surat_eksternal_id']));
				$this->db->delete('pengantar_surat_eksternal_list', array('surat_pengantar_id' => $_POST['pengantar_surat_eksternal_id']));
				foreach($_POST['detail_pengantar'] as $k => $v) {
					$this->db->update('surat_eksternal', array('surat_pengantar_id' => $_POST['pengantar_surat_eksternal_id']), array('surat_eksternal_id' => $v));

					$detail = array();
					$detail['surat_pengantar_id'] = $_POST['pengantar_surat_eksternal_id'];
					$detail['surat_eksternal_id'] = $v;
						
					$this->db->insert('pengantar_surat_eksternal_list', $detail);
						
				}

				set_success_message('Data surat pengantar berhasil perbaharui.');
				redirect('surat/external/pengantar/' . $_POST['pengantar_surat_eksternal_id']);
				exit;
			} else {
				
				set_error_message(validation_errors());
			}
		} else {
			set_error_message('Tidak ada surat yang dipilih.');
		}
	}
	
	/**
	 * 
	 */
	function kirim_surat_pengantar() {
		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('global/dashboard') . "')");
		
		$this->db->update('pengantar_surat_eksternal', array('status' => 1, 'pengiriman_time' => date('Y-m-d H:i:s')), array('pengantar_surat_eksternal_id' => $_POST['ref_id']));
		
		$sql = "SELECT * FROM pengantar_surat_eksternal WHERE status >= 0 AND pengantar_surat_eksternal_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		$pengantar = $result->row();
		
		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'external/incoming' ";
		$result = $this->db->query($sql);
		$function_ref = $result->row();

		$subject = 'Notifikasi pengiriman surat';
		$body = 'Surat Masuk Baru dalam daftar kerja anda.<br>' .
// 				'<strong>Catatan : </strong><br>' . $pengantar->catatan_pengirim .
				'<strong>Petugas Pengirim : </strong><br>' . $pengantar->petugas_pengirim . '<br>' .
				'<strong>Surat : </strong><br>';

		$list_tujuan = user_in_unit($pengantar->tujuan_unit_id);
		
		$sql = "SELECT * FROM surat_eksternal WHERE surat_pengantar_id = '" . $_POST['ref_id'] . "' ";
		$list = $this->db->query($sql);
		foreach ($list->result() as $row) {
			
 			$sql = "UPDATE surat_eksternal
 					   SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 1 END),
 						   kirim_time = '" . date('Y-m-d H:i:s') . "'
					 WHERE surat_eksternal_id = '" . $row->surat_eksternal_id . "'";
			$this->db->query($sql);
			
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat_eksternal';
			$data['ref_id'] = $row->surat_eksternal_id;
			$data['flow_seq'] = $row->status + 1;
			$data['note'] = $pengantar->catatan_pengirim;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);
			
			$body .= $row->jenis_agenda . '-' . $row->agenda_id . ' : Surat Masuk Eksternal dari ' . $row->surat_ext_title . '.<br>';
			
			// set workspace untuk TU
// 			$list = user_with_permission(8);
// 			foreach ($list->result() as $row_noty) {
			//	$this->db->update('notify', array('note' => ('Surat Pengantar Masuk Eksternal untuk ' . $pengantar->tujuan_unit . ' telah dikirim')), array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_eksternal_id, 'notify_user_id' => $row_noty->user_id));
// 			}

			// set workspace untuk Penerima (link surat pengantar)
			$note = 'Surat Masuk Eksternal No. ' . $row->surat_no . ' dari ' . $row->surat_ext_nama . ' &nbsp; | &nbsp; ' . $row->surat_ext_title . ' &nbsp; | &nbsp; ' .  $row->surat_ext_instansi;
			foreach ($list_tujuan->result() as $row_tujuan) {
				$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_eksternal_id, 'agenda' => ($row->jenis_agenda . '-' . $row->agenda_id), 'note' => $note, 'detail_link' => ('surat/external/incoming_view/' . $row->surat_eksternal_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
			}
			
		}

		foreach ($list_tujuan->result() as $row) {
			// $this->_send_mail_notification($row->email, $subject, $body, array());
			
		}
			
		set_success_message('Surat berhasil dikirim.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * @param unknown $function_ref_id
	 * @param unknown $surat_eksternal_id
	 */
	function terima_surat($function_ref_id, $surat_eksternal_id) {
// 		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('surat/external/incoming/' . $_POST['ref_id']) . "')");
		
		$result = $this->db->get_where('surat_eksternal', array('surat_eksternal_id' => $surat_eksternal_id));
		if($result->num_rows() > 0) {
			$surat_eksternal = $result->row();
			if($surat_eksternal->terima_time) {
				
				set_error_message('Surat Sudah diterima.');
				return;
			} else {
				
				$sql = "UPDATE surat_eksternal
						SET status = status + 1, terima_time = NOW()
						WHERE surat_eksternal_id = '" . $surat_eksternal_id . "'";
				$this->db->query($sql);
				
				$data = array();
				$data['organization_id'] = get_user_data('organization_id');
				$data['flow_type'] = 1; //forward
				$data['table'] = 'surat_eksternal';
				$data['ref_id'] = $surat_eksternal_id;
				$data['flow_seq'] = $surat_eksternal->status + 1;
				$data['user_id'] = get_user_id();
				$this->db->insert('process_notes', $data);

				$jenis = ($function_ref_id == 1) ? 'Masuk' : 'Keluar';
				
				$subject = 'Notifikasi Penerimaan Surat ' . $jenis . ' Eksternal';
				$body = "Surat $jenis Eksternal telah diterima oleh " . get_user_data('user_name') . ".<br>";
				
				// set workspace untuk TU
				$list = user_with_permission(8);
				foreach ($list->result() as $row) {
					$this->db->update('notify', array('note' => ('Surat ' . $jenis . ' Eksternal untuk ' . $surat_eksternal->surat_int_unit . ' telah diterima')), array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'notify_user_id' => $row->user_id));
					// $this->_send_mail_notification($row->email, $subject, $body, array());
				}
				
				$list_tujuan = user_in_unit($surat_eksternal->surat_int_unit_id);
				// set workspace untuk Penerima (link surat pengantar)
				foreach ($list_tujuan->result() as $row_tujuan) {
					$this->db->update('notify', array('note' => ('Surat ' . $jenis . ' Eksternal untuk ' . $surat_eksternal->surat_int_unit . ' telah diterima')), array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'notify_user_id' => $row_tujuan->user_id));
					// $this->_send_mail_notification($row->email, $subject, $body, array());
					
				}
					
			}
			
		} else {

			set_error_message('Data tidak dikenali.');
			return;
			
		}
		
		set_success_message('Surat telah diterima.');
// 		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * @param unknown $surat_eksternal_id
	 */
	function tolak_surat($surat_eksternal_id) {
		
	}
	
	/**
	 * 
	 */
	function terima_surat_pengantar() {
//		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('global/dashboard') . "')");

		$this->db->update('pengantar_surat_eksternal', array('status' => 2, 'penerima_time' => date('Y-m-d H:i:s')), array('pengantar_surat_eksternal_id' => $_POST['pengantar_surat_eksternal_id']));
		
		$result = $this->db->get_where('pengantar_surat_eksternal', array('pengantar_surat_eksternal_id' => $_POST['pengantar_surat_eksternal_id']));
		$pengantar = $result->row();
		
		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'external/incoming' ";
		$result = $this->db->query($sql);
		$function_ref = $result->row();

		$subject = 'Notifikasi penerimaan surat';
		$body = 'Surat Pengantar telah diterima.<br>' .
				'<strong>Catatan : </strong><br>' . $pengantar->catatan_penerima .
				'<strong>Petugas Penerima : </strong><br>' . $pengantar->petugas_penerima;
		
		$sql = "SELECT * FROM surat_eksternal WHERE surat_pengantar_id = '" . $_POST['pengantar_surat_eksternal_id'] . "' ";
		$list = $this->db->query($sql);
		foreach ($list->result() as $row) {
				
			$sql = "UPDATE surat_eksternal
 					   SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 1 END)
					 WHERE surat_eksternal_id = '" . $row->surat_eksternal_id . "'";
			$this->db->query($sql);
				
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat_eksternal';
			$data['ref_id'] = $row->surat_eksternal_id;
			$data['flow_seq'] = $row->status;
// 			$data['note'] = $pengantar->catatan_penerima;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);
			
			// set workspace untuk TU
			$list = user_with_permission(8);
			foreach ($list->result() as $row_noty) {
// 				$this->db->update('notify', array('note' => ('Surat Masuk Eksternal untuk ' . $data['tujuan_unit'] . ' telah diterima')), array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $v, 'notify_user_id' => $row_noty->user_id));
				// $this->_send_mail_notification($row_noty->email, $subject, $body, array());
			}
		}

// 		$sql = "SELECT u.* FROM system_security.users u
// 				JOIN system_security.users_structure us ON(us.user_id = u.user_id AND us.status = 1 AND u.active = 1)
// 				WHERE us.organization_structure_id = " . $pengantar->tujuan_unit_id;
// 		$list = $this->db->query($sql);
// 		foreach ($list->result() as $row) {
				
// 			// set workspace untuk Penerima (link surat pengantar)
// 			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $pengantar->pengantar_surat_eksternal_id, 'note' => ('Surat Pengantar Masuk Eksternal untuk ' . $data['tujuan_unit'] . ' sudah diterima'), 'detail_link' => ('surat/external/pengantar/' . $pengantar->pengantar_surat_eksternal_id), 'notify_user_id' => $row->user_id, 'status' => 0, 'read' => 0));
// 		}
		
		set_success_message('Surat diterima.');
		redirect('surat/external/pengantar/' . $_POST['pengantar_surat_eksternal_id']);
		exit;
	//	$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * @param unknown $function_ref_id
	 * @param unknown $surat_eksternal_id
	 * @param unknown $status
	 */
	function baca_surat($function_ref_id, $surat_eksternal_id, $status) {

		$this->db->update('surat_eksternal', array('baca_time' => date('Y-m-d H:i:s')), array('surat_eksternal_id' => $surat_eksternal_id));
		
		$data = array();
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['flow_seq'] = $status;
		$data['table'] = 'surat_eksternal';
		$data['ref_id'] = $surat_eksternal_id;
		$data['note'] = 'Surat telah dibaca';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
		
	}
	
	/**
	 * @param unknown $function_ref_id
	 * @param unknown $surat_eksternal_id
	 */
	function draf($function_ref_id, $surat_eksternal_id) {

		$result = $this->db->get_where('surat_eksternal', array('surat_eksternal_id' => $surat_eksternal_id));
		if($result->num_rows() > 0) {
			$surat_eksternal = $result->row();
		
			$sql = "UPDATE surat_eksternal
					SET status = status + 1, terima_time = NOW()
					WHERE surat_eksternal_id = '" . $surat_eksternal_id . "'";
			$this->db->query($sql);
	
			$data = array();
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat_eksternal';
			$data['ref_id'] = $surat_eksternal_id;
			$data['flow_seq'] = $surat_eksternal->status + 1;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);
	
			$jenis = ($function_ref_id == 1) ? 'Masuk' : 'Keluar';
	
			$subject = 'Notifikasi Draf Surat ' . $jenis . ' Eksternal';
			$body = "Draf Surat $jenis Eksternal telah dibuat oleh " . get_user_data('user_name') . ", Mohon Verifikasinya.<br>";
	
			// set workspace 
			$this->db->delete('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id));
			
			$list_tujuan = user_in_unit(get_user_data('unit_id'));
			foreach ($list_tujuan->result() as $row) {
				$this->db->insert('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'agenda' => ($surat_eksternal->jenis_agenda . '-' . $surat_eksternal->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat_eksternal_id), 'notify_user_id' => $row->user_id, 'read' => 0));
				// $this->_send_mail_notification($row->email, $subject, $body, array());
			}
			
			if(in_array(get_user_data('unit_level'), array('L3', 'TU'))) {

				$list_tujuan = user_in_unit(get_user_data('unit_parent_id'));
				foreach ($list_tujuan->result() as $row) {
					$this->db->insert('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'agenda' => ($surat_eksternal->jenis_agenda . '-' . $surat_eksternal->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat_eksternal_id), 'notify_user_id' => $row->user_id, 'read' => 0));
					// $this->_send_mail_notification($row->email, $subject, $body, array());
				}
					
			}
	
		} else {
		
			set_error_message('Data tidak dikenali.');
			return;
				
		}
		
		set_success_message('Draf telah di kirim.');
	}

	/**
	 * @param unknown $function_ref_id
	 * @param unknown $surat_eksternal_id
	 */
	function verifikasi_dir($function_ref_id, $surat_eksternal_id) {
	
		$result = $this->db->get_where('surat_eksternal', array('surat_eksternal_id' => $surat_eksternal_id));
		if($result->num_rows() > 0) {
			$surat_eksternal = $result->row();
	
			$sql = "UPDATE surat_eksternal
					SET status = status + 1, terima_time = NOW()
					WHERE surat_eksternal_id = '" . $surat_eksternal_id . "'";
			$this->db->query($sql);
	
			$data = array();
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat_eksternal';
			$data['ref_id'] = $surat_eksternal_id;
			$data['flow_seq'] = $surat_eksternal->status + 1;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);
	
			$jenis = ($function_ref_id == 1) ? 'Masuk' : 'Keluar';
	
			$subject = 'Notifikasi Draf Surat ' . $jenis . ' Eksternal';
			$body = "Draf Surat $jenis Eksternal telah dibuat oleh " . get_user_data('user_name') . ", Mohon Verifikasinya.<br>";
	
			$sql = "SELECT dir.organization_structure_id, dir.unit_name
				  FROM system_security.organization_structure os
			 	  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
					WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND os.organization_structure_id = " . get_user_data('unit_id');
			$result = $this->db->query($sql);
			$dir = $result->row();
			
			// set workspace
			$list_tujuan = user_in_unit($dir->organization_structure_id);
			foreach ($list_tujuan->result() as $row) {
				$this->db->insert('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'agenda' => ($surat_eksternal->jenis_agenda . '-' . $surat_eksternal->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat_eksternal_id), 'notify_user_id' => $row->user_id, 'read' => 0));
				// $this->_send_mail_notification($row->email, $subject, $body, array());
			}
			
		} else {
	
			set_error_message('Data tidak dikenali.');
			return;
	
		}
	
		set_success_message('Draf telah di kirim.');
	}

	/**
	 * @param unknown $function_ref_id
	 * @param unknown $surat_eksternal_id
	 */
	function verifikasi_admin($function_ref_id, $surat_eksternal_id) {
	
		$result = $this->db->get_where('surat_eksternal', array('surat_eksternal_id' => $surat_eksternal_id));
		if($result->num_rows() > 0) {
			$surat_eksternal = $result->row();
	
			$sql = "UPDATE surat_eksternal
					SET status = status + 1, terima_time = NOW()
					WHERE surat_eksternal_id = '" . $surat_eksternal_id . "'";
			$this->db->query($sql);
	
			$data = array();
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat_eksternal';
			$data['ref_id'] = $surat_eksternal_id;
			$data['flow_seq'] = $surat_eksternal->status + 1;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);
	
			$jenis = ($function_ref_id == 1) ? 'Masuk' : 'Keluar';
	
			$subject = 'Notifikasi Draf Surat ' . $jenis . ' Eksternal';
			$body = "Draf Surat $jenis Eksternal telah dibuat oleh " . get_user_data('user_name') . ", Mohon Verifikasinya.<br>";
	
			$sql = "SELECT dir.organization_structure_id, dir.unit_name
				  FROM system_security.organization_structure os
			 	  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
					WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND os.organization_structure_id = " . get_user_data('unit_id');
			$result = $this->db->query($sql);
			$dir = $result->row();
				
			$approval = json_decode($surat_eksternal->approval, TRUE);
			if(!isset($approval['TU'])) {
				$approval['TU'] = array();
				$approval['TU']['name'] = ' TU ';
				$approval['TU']['status'] = FALSE;
				$this->db->update('surat_eksternal', array('approval' => json_encode($approval)), array('surat_eksternal_id' => $surat_eksternal_id));
			}
			
			// set workspace
			$list_tujuan = user_with_permission(8);
			foreach ($list_tujuan->result() as $row) {
				$this->db->delete('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'notify_user_id' => $row->user_id));
				$this->db->insert('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'agenda' => ($surat_eksternal->jenis_agenda . '-' . $surat_eksternal->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat_eksternal_id), 'notify_user_id' => $row->user_id, 'read' => 0));
				// $this->_send_mail_notification($row->email, $subject, $body, array());
			}
				
		} else {
	
			set_error_message('Data tidak dikenali.');
			return;
	
		}
	
		set_success_message('Draf telah di kirim.');
	}
	
	/**
	 * 
	 */
	function save_surat_keluar_send() {
	
		$surat_eksternal_id = $_POST['surat_eksternal_id'];
		unset($_POST['surat_eksternal_id']);
		
		$data = array_intersect_key($_POST, $this->data_object->surat_eksternal);
		$data['kirim_time'] = human_to_db($data['kirim_time']);
		$data['modified_id'] = get_user_id();
		$data['modified_time'] = date('Y-m-d H:i:s');
		
		$data['catatan_pengiriman'] = json_encode($_POST['catatan_pengiriman']);
			
		$this->db->update('surat_eksternal', $data, array('surat_eksternal_id' => $surat_eksternal_id));
			
		set_success_message('Data Pengiriman Surat berhasil disimpan.');
		redirect('surat/external/kirim_surat_keluar/' . $surat_eksternal_id);
		exit;
	
	}
	
	/**
	 * @param unknown $function_ref_id
	 * @param unknown $surat_eksternal_id
	 */
	function simpan_arsip($function_ref_id, $surat_eksternal_id) {

		$result = $this->db->get_where('surat_eksternal', array('surat_eksternal_id' => $surat_eksternal_id));
		if($result->num_rows() > 0) {
			$surat_eksternal = $result->row();
			
			$jenis_agenda = ($surat_eksternal->jenis_agenda == 'M') ? 'Masuk' : 'Keluar';
				
			$subject = "Notifikasi Proses surat $jenis_agenda Eksternal - " . $surat_eksternal->jenis_agenda . '-' . $surat_eksternal->agenda_id;
			$body = 'Surat ' . $jenis_agenda . ' Eksternal ' . $surat_eksternal->jenis_agenda . '-' . $surat_eksternal->agenda_id . ' telah disimpan sebagai Arsip ';
				
			if(!has_permission(8)) {
			
				$data = array();
				$data['organization_structure_id'] = get_user_data('unit_id');
				$data['created_id'] = get_user_id();
				$data['ref_type'] = 'surat_eksternal';
				$data['ref_id'] = $surat_eksternal_id;
				$data['status'] = 99; //arsip
				$this->db->insert('org_struc_archive', $data);

				$list_tujuan = user_in_unit(get_user_data('unit_id'));
				foreach ($list_tujuan->result() as $row) {
					$this->db->delete('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'notify_user_id' => $row->user_id));
					
					// $this->_send_mail_notification($row->email, $subject, $body, array());
				}
				
			} else {
						
				if($surat_eksternal->status == 99) {
	
					set_error_message('Surat Sudah terdaftar sebagai arsip.');
					return;
					
				} else {
					
					$this->db->update('surat_eksternal', array('status' => 99, 'arsip_time' => date('Y-m-d H:i:s')), array('surat_eksternal_id' => $surat_eksternal_id));
					
					$data = array();
					$data['organization_id'] = get_user_data('organization_id');
					$data['flow_type'] = 1; //forward
					$data['flow_seq'] = 99; //arsip
					$data['table'] = 'surat_eksternal';
					$data['ref_id'] = $surat_eksternal_id;
					$data['user_id'] = get_user_id();
					$this->db->insert('process_notes', $data);
			
					$list = user_with_permission(8);
					foreach ($list->result() as $row) {
						
						$this->db->delete('notify', array('function_ref_id' => $function_ref_id, 'ref_id' => $surat_eksternal_id, 'notify_user_id' => $row->user_id));
						// $this->_send_mail_notification($row->email, $subject, $body, array());
					}
					
				}
			}
			
			redirect('global/dashboard');
			exit;
			
		} else {

			set_error_message('Data tidak dikenali.');
			return;
				
		}
		
		set_success_message('Surat disimpan sebagai arsip.');
			
	}
	
}

/**
 * End of file
 */