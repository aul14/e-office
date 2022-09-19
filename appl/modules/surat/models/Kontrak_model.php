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

class Kontrak_model extends LX_Model {
	
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
	// function get_surat_eksternal($surat_eksternal_id = NULL) {
		// if($surat_eksternal_id == NULL) {
			// $sql = "SELECT * FROM surat_eksternal WHERE status >= 0";
			// return $this->db->query($sql);
			
		// } else {
			// $sql = "SELECT se.*, u.user_name, osa.status unit_archive_status FROM surat_eksternal se
					// LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat_eksternal' AND osa.ref_id = se.surat_eksternal_id " . ((!has_permission(8)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
 					// JOIN system_security.users u ON(u.user_id = se.created_id)  
					// WHERE se.status >= 0 AND surat_eksternal_id = '$surat_eksternal_id'";
			// return $this->db->query($sql);
			
		// }
	// }
	
	/**
	 * @param unknown $surat_eksternal_id
	 */
	// function get_surat_eksternal_flow($surat_eksternal_id) {
		// $sql = "SELECT pn.*, u.user_name FROM process_notes pn
				// JOIN system_security.users u ON(u.user_id = pn.user_id)  
				// WHERE \"table\" = 'surat_eksternal' AND ref_id = '$surat_eksternal_id' ORDER BY created_time";
		// return $this->db->query($sql);
	// }
	
	/**
	 * @param unknown $surat_eksternal_id
	 */
	// function get_surat_eksternal_ttd($surat_eksternal_id) {
		// return $this->db->get_where('surat_eksternal_ttd', array('surat_eksternal_id' => $surat_eksternal_id));
	// }
	
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

	function get_referensi_full($type, $key = NULL) {
		if($key == NULL) {
			$return = array();
			$this->db->order_by("entry_id");
			$rows = $this->db->get_where('_ref_instansi_eksternal', array('nama_pejabat' => $type));
			
			foreach($rows->result() as $row) {
				$return[$row->entry_id] = $row->instansi;
			}
			return $return;
		} else {
			$rows = $this->db->get_where('_ref_instansi_eksternal', array('nama_pejabat' => $type, 'status'=>1, 'entry_id' => $key));
			if($rows->num_rows() > 0) {
				$row = $rows->row();
				return $row->instansi;
			}else {
				return '';
			}
		}
	}
	
	/**
	 * 
	 */
	function insert_kontrak() {
// 		var_dump($_POST); exit;
		if($this->_validate_post_data($this->data_object->surat_keputusan, 'add') != FALSE) {
			
			$result = $this->db->get_where('system_security.function_ref', array('module_function' => 'kontrak/input_kontrak'));
			$function_data = $result->row();
					
			$kontrak_id = generate_unique_id();
			
			$data = array_intersect_key($_POST, $this->data_object->surat_keputusan);
			$data['surat_id'] = $kontrak_id; 
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
			$data['agenda_id'] = $this->lx->number_generator($function_data->format_agenda);
			$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			//$data['surat_awal'] = human_to_db($data['surat_tgl_masuk']);
			//$data['surat_akhir'] = human_to_db($data['surat_tgl_masuk']);
			
			$data['surat_awal'] = $data['surat_tgl'];
			$data['surat_akhir'] = $data['surat_tgl_masuk'];
			
			$agenda = $_POST['jenis_agenda'] . ' - ';
			if($_POST['create_agenda'] == 1) {
				$data['agenda_id'] = $this->lx->number_generator($function_data->format_agenda);
				$agenda .= $data['agenda_id'];
			}
		
			$this->db->insert('surat', $data);
			
			$list = user_with_permission(23);
			$subject = "Notifikasi Proses surat - " .  $_POST[jenis_agenda]. '-' . $data['agenda_id'];
			$note = 'Surat Keputusan No. ' . $_POST['surat_no']. ' tentang ' . $_POST['surat_perihal']. ' Berhasil Diinputkan ';
			foreach ($list->result() as $row) {
				//$this->_send_mail_notification($row->email, $subject, $note, array());
			}
			// set workspace
			$note = 'Contract Maintenance No. ' . $data['surat_no'] . ' tentang ' . $data['surat_perihal']. ' telah dibuat ';
			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $data['surat_id'], 'agenda' => ('CM -' . $data['agenda_id']), 'note' => $note, 'detail_link' => ('surat/kontrak/kontrak_view/'. $data['surat_id']), 'notify_user_id' => get_user_id(), 'status' => 0, 'read' => 0));
			
			
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
							$file_attached['ref_id'] = $kontrak_id;
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
			$data['ref_id'] = $kontrak_id;
			$data['user_id'] = get_user_id();
			$data['flow_seq'] = 0;
			$data['note'] = 'Surat ' . $_POST['function_ref_name'] . ' dibuat oleh ' . get_user_data('user_name');
			$this->db->insert('process_notes', $data);
			
			set_success_message('Data masuk berhasil disimpan.');
			redirect('surat/kontrak/kontrak_aktif/');
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}
	
	// function proses_data() {
 		// $return = array('error' => '', 'message' => '', 'execute' => "location.reload()");

		// $sql = "UPDATE surat
				   // SET status = (CASE WHEN status = " . $_POST['last_flow'] . " THEN " . $_POST['last_flow'] . " ELSE status + 1 END)
				 // WHERE surat_id = '" . $_POST['ref_id'] . "'";
		// $this->db->query($sql);
		
		// $data = array_intersect_key($_POST, $this->data_object->process_notes);
		// $data['organization_id'] = get_user_data('organization_id');
		// $data['flow_type'] = 1; //forward
		// $data['table'] = 'surat';
		// $data['user_id'] = get_user_id();
		// $this->db->insert('process_notes', $data);
		
		// $sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.position_handle FROM surat s
				// JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
				// WHERE s.surat_id = '" . $_POST['ref_id'] . "' ";
		// $result = $this->db->query($sql);
		// $surat = $result->row();
		
		// if($_POST['function_handler'] != '-') {
			// $this->$_POST['function_handler']($surat);
		// }
		// $this->output->set_content_type('application/json')->set_output(json_encode($return));
	// }
	
	/**
	 * 
	 */
	// function init_distribusi_addendum($surat) {
		
		// $subject = "Notifikasi Proses " . $surat->jenis_agenda . " - " . $surat->agenda_id;
		// $body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . ' - ' . $surat->agenda_id . ' telah memasuki proses ' . $surat->process_title;
		
		// $list_tujuan = user_with_permission($surat->permission_handle);
		// foreach ($list_tujuan->result() as $row_tujuan) {
			// $this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/kontrak/kontrak_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
			// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		// }
	// }
	
	function insert_addendum1() {
// 		var_dump($_POST); exit;
		if($this->_validate_post_data($this->data_object->surat_keputusan, 'add') != FALSE) {
			
			$result = $this->db->get_where('system_security.function_ref', array('module_function' => 'kontrak/input_kontrak'));
			$function_data = $result->row();
					
			$surat_id = $_POST['surat_id'];
			unset($_POST['surat_id']);
			
			$this->db->update('surat',array('status'=> 1, 'surat_awal' => human_to_db($_POST['surat_tgl']), 'surat_akhir' => human_to_db($_POST['surat_tgl_masuk'])), array('surat_id' => $surat_id));
			
			$kontrak_id = generate_unique_id();
			
			$data = array_intersect_key($_POST, $this->data_object->surat_keputusan);
			$data['addendum_id'] = $kontrak_id; 
			$data['surat_id'] = $surat_id; 
			$data['status'] = 1;
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
			$data['agenda_id'] = $_POST['agenda_id'];
			$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			
			$this->db->insert('addendum', $data);
			
			// set workspace
			
 		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");
	
		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['table'] = 'surat';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
		
		$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.position_handle FROM surat s
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
				WHERE s.surat_id = '" .$surat_id . "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();
				
		set_success_message('Proses berkas berhasil dilanjutkan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
		
			$list = user_with_permission(23);
			$subject = "Notifikasi Proses surat - " .  $_POST[jenis_agenda]. '-' . $_POST[agenda_id];
			$note = 'Addendum 1 Contract Maintenance No. ' . $_POST['surat_no'] . ' dari ' . $_POST['status_berkas'];
			foreach ($list->result() as $row) {
				//$this->_send_mail_notification($row->email, $subject, $note, array());
			}
			
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
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
									$file_attached['table'] = 'surat';
									$file_attached['ref_id'] = $surat_id;
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
			set_success_message('Data surat masuk berhasil disimpan.');
			redirect('surat/kontrak/kontrak_aktif/');
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}
	
	function insert_addendum2() {
// 		var_dump($_POST); exit;
		if($this->_validate_post_data($this->data_object->surat_keputusan, 'add') != FALSE) {
			
			$result = $this->db->get_where('system_security.function_ref', array('module_function' => 'kontrak/input_kontrak'));
			$function_data = $result->row();
					
			$surat_id = $_POST['surat_id'];
			unset($_POST['surat_id']);
			
			$this->db->update('surat',array('status' => 2,  'surat_awal' => human_to_db($_POST['surat_tgl']), 'surat_akhir' => human_to_db($_POST['surat_tgl_masuk'])), array('surat_id' => $surat_id));
			
			$kontrak_id = generate_unique_id();
			
			$data = array_intersect_key($_POST, $this->data_object->surat_keputusan);
			$data['addendum_id'] = $kontrak_id; 
			$data['surat_id'] = $surat_id; 
			$data['status'] = 2;
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
			$data['agenda_id'] = $_POST['agenda_id'];
			$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			
			$this->db->insert('addendum', $data);
			
			// set workspace
	
 		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['table'] = 'surat';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
		
		$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.position_handle FROM surat s
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
				WHERE s.surat_id = '" .$surat_id . "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();
		
		set_success_message('Proses berkas berhasil dilanjutkan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
		
			$list = user_with_permission(23);
			$subject = "Notifikasi Proses surat - " .  $_POST[jenis_agenda]. '-' . $_POST[agenda_id];
			$note = 'Addendum 2 Contract Maintenance No. ' . $_POST['surat_no'] . ' dari ' . $_POST['status_berkas'];
			foreach ($list->result() as $row) {
				//$this->_send_mail_notification($row->email, $subject, $note, array());
			}
			
				if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
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
									$file_attached['table'] = 'surat';
									$file_attached['ref_id'] = $surat_id;
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

			set_success_message('Data surat masuk berhasil disimpan.');
			redirect('surat/kontrak/kontrak_aktif/');
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 *
	 */
	function get_addendum($surat) {
		return $this->db->get_where('addendum', array('surat_id' => $surat, 'status' => 1));
	}
	
	function get_addendum2($surat) {
		return $this->db->get_where('addendum', array('surat_id' => $surat, 'status' => 2));
	}
	
	function get_kontrakhenti($surat) {
		return $this->db->get_where('addendum', array('surat_id' => $surat, 'status' => 99));
	}
	
	// function get_kontrak_selesai_list($mitra= NULL, $jenis= NUll, $Tanggal = null ) {
		// if ($mitra= NULL && $jenis= NUll && $Tanggal = Null){
			// $sql = "SELECT s.*, n.detail_link link FROM surat s
			// JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = s.function_ref_id AND notify_user_id = '" . get_user_id() . "')
			// WHERE s.jenis_agenda = 'CM' AND s.status <> 99
			// ORDER by s.surat_akhir ASC";
			// $list = $this->db->query($sql);
			// return $list->result();	
		// }
		// else {
			// $sql = "SELECT s.*, n.detail_link link FROM surat s
			// JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = s.function_ref_id AND notify_user_id = '" . get_user_id() . "')
			// WHERE s.jenis_agenda = 'CM' AND s.status <> 99 AND s.status_berkas = $mitra AND s.Jenis_surat = $jenis And s.surat_akhir <= $tanggal
			// ORDER by s.surat_akhir ASC";
			// $list = $this->db->query($sql);
			// return $list->result();	
		// }
	// }
	
	function get_kontrak_aktif_list() {
		$sql = "SELECT s.*,
		n.detail_link link
		FROM surat s
		JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = s.function_ref_id AND notify_user_id = '" . get_user_id() . "')
		WHERE s.jenis_agenda = 'CM' AND s.status < 99
		ORDER by s.surat_akhir ASC";
		$list = $this->db->query($sql);
		return $list->result();	
	}

	function get_kontrak_selesai_list() {
		$sql = "SELECT s.*,
		n.detail_link link
		FROM surat s
		JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = s.function_ref_id AND notify_user_id = '" . get_user_id() . "')
		WHERE s.jenis_agenda = 'CM' AND s.status = 99
		ORDER by s.surat_akhir ASC";
		$list = $this->db->query($sql);
		return $list->result();	
	}
	
	function get_kontrak_list() {
		$sql = "SELECT s.* FROM surat s
		WHERE s.jenis_agenda = 'CM' AND s.status <> 99
		ORDER by s.surat_akhir ASC";
		return $list = $this->db->query($sql);
		return $list->result();
	}
	 
	function nomor_exists($key) {
		$this->db->where('surat_no',$this->input->post('surat_no'));
		$query = $this->db->get('surat');
		if ($query->num_rows() > 0){
			return true;
		}
		else{
			return false;
		}
	}
	
	function update_kontrak() {
// 		var_dump($_POST); exit;
		$obj = $this->data_object->surat_keputusan;
		$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		if($this->_validate_post_data($obj, 'edit') != FALSE) {
				
			$surat_id = $_POST['surat_id'];
			unset($_POST['surat_id']);
	
			$data = array_intersect_key($_POST, $obj);
			$data['modified_id'] = get_user_id();
			$data['modified_time'] = date('Y-m-d H:i:s');
			$data['surat_awal'] = human_to_db($data['surat_tgl']);
			$data['surat_akhir'] = human_to_db($data['surat_tgl_masuk']);
			
			if(isset($_POST['surat_tgl'])) {
				$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			}

			if(isset($_POST['surat_tgl_masuk'])) {
				$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			}

			$this->db->update('surat', $data, array('surat_id' => $surat_id));
			
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
				$this->load->library('upload', $config_file);
	
				$i = 0;
				foreach($_POST['attachment'] as $k => $v) {
					switch ($v['state']) {
						case 'insert' :
							if(!empty($_FILES['attachment_file_' . $k]['name'])) {
								if (!$this->upload->do_upload('attachment_file_' . $k)) {
								echo	set_error_message($this->upload->display_errors());
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
			
			set_success_message('Data surat berhasil perbaharui.');
			redirect('surat/kontrak/kontrak_aktif/');
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}
	
	function update_addendum1() {
// 		var_dump($_POST); exit;
		$obj = $this->data_object->surat_keputusan;
		$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		if($this->_validate_post_data($obj, 'edit') != FALSE) {
			
			$surat_id = $_POST['surat_id'];
			unset($_POST['surat_id']);
	
			$this->db->update('surat',array('status'=> 1, 'surat_awal' => human_to_db($_POST['surat_tgl']), 'surat_akhir' => human_to_db($_POST['surat_tgl_masuk'])), array('surat_id' => $surat_id));
	
			$data = array_intersect_key($_POST, $obj);
			$data['modified_id'] = get_user_id();
			$data['modified_time'] = date('Y-m-d H:i:s');
			
			if(isset($_POST['surat_tgl'])) {
				$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			}

			if(isset($_POST['surat_tgl_masuk'])) {
				$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			}

			$this->db->update('addendum', $data, array('surat_id' => $surat_id, 'status' => 1));
		
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
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
									$file_attached['table'] = 'surat';
									$file_attached['ref_id'] = $surat_id;
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
			
			set_success_message('Data surat berhasil perbaharui.');
			redirect('surat/kontrak/kontrak_aktif/');
			exit;
		} else {
			set_error_message(validation_errors());
		}		
	}
	
	function update_addendum2() {
// 		var_dump($_POST); exit;
		$obj = $this->data_object->surat_keputusan;
		$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		if($this->_validate_post_data($obj, 'edit') != FALSE) {
			
			$surat_id = $_POST['surat_id'];
			unset($_POST['surat_id']);
	
			$this->db->update('surat',array('status'=> 2, 'surat_awal' => human_to_db($_POST['surat_tgl']), 'surat_akhir' => human_to_db($_POST['surat_tgl_masuk'])), array('surat_id' => $surat_id));
	
			$data = array_intersect_key($_POST, $obj);
			$data['modified_id'] = get_user_id();
			$data['modified_time'] = date('Y-m-d H:i:s');
			
			if(isset($_POST['surat_tgl'])) {
				$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			}

			if(isset($_POST['surat_tgl_masuk'])) {
				$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			}

			$this->db->update('addendum', $data, array('surat_id' => $surat_id, 'status' => 2));
		
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
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
									$file_attached['table'] = 'surat';
									$file_attached['ref_id'] = $surat_id;
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
			
			set_success_message('Data surat berhasil perbaharui.');
			redirect('surat/kontrak/kontrak_aktif/');
			exit;
		} else {
			set_error_message(validation_errors());
		}
		
	}
	
	function stop_kontrak() {
//  	var_dump($_POST); exit;
		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['surat_id']));
		if($result->num_rows() > 0) {
			$surat = $result->row();
			
			$subject = "Notifikasi Proses surat - " . $surat->jenis_agenda . '-' . $surat->agenda_id;
			$body = 'Surat ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' telah dihentikan ';
				
			if(has_permission(23)) {
				
				if($surat->status != 99) {
					$this->db->update('surat', array('status' => 99, 'catatan_pengiriman' =>$_POST['note'],'arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $_POST['surat_id']));
				}
					
				$data = array();
				$data['organization_id'] = get_user_data('organization_id');
				$data['flow_type'] = 1; //forward
				$data['flow_seq'] = 99; //arsip
				$data['table'] = 'surat';
				$data['user_id'] = get_user_id();
				$this->db->insert('process_notes', $data);

				$list = user_with_permission(23);
				$subject = "Notifikasi Proses surat - " . $surat->jenis_agenda . '-' . $surat->agenda_id;
				$body = 'Surat ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' telah dihentikan ';
				foreach ($list->result() as $row) {
					//$this->_send_mail_notification($row->email, $subject, $body, array());
				}
			}
		} else {
			set_error_message('Data tidak dikenali.');
			return;
				
		}
	
		set_success_message('Kontrak Berhasil Dikembalikan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	function hentikan_kontrak() {
// 		var_dump($_POST); exit;
		if($this->_validate_post_data($this->data_object->surat_keputusan, 'add') != FALSE) {
			
			$result = $this->db->get_where('system_security.function_ref', array('module_function' => 'kontrak/input_kontrak'));
			$function_data = $result->row();
					
			$surat_id = $_POST['surat_id'];
			unset($_POST['surat_id']);
			
			$this->db->update('surat',array('status'=> 99, 'catatan_pengiriman' => $_POST['catatan_pengiriman'], 'surat_akhir' => human_to_db($_POST['surat_tgl_masuk'])), array('surat_id' => $surat_id));
			
			$kontrak_id = generate_unique_id();
			
			$data = array_intersect_key($_POST, $this->data_object->surat_keputusan);
			$data['addendum_id'] = $kontrak_id; 
			$data['surat_id'] = $surat_id; 
			$data['status'] = 99;
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
			$data['agenda_id'] = $_POST['agenda_id'];
			$data['surat_tgl'] = human_to_db($data['surat_tgl']);
			$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
			
			$this->db->insert('addendum', $data);
			
			// set workspace
			
	 		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");
		
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat';
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);
			
			$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.position_handle FROM surat s
					JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
					WHERE s.surat_id = '" .$surat_id . "' ";
			$result = $this->db->query($sql);
			$surat = $result->row();
			
			$list = user_with_permission(23);
			$subject = "Notifikasi Proses surat - " . $_POST['jenis_agenda'] . '-' . $_POST['agenda_id'];
			$body = 'Surat ' . $_POST['jenis_agenda'] . '-' . $_POST['agenda_id'] . ' telah dihentikan ';
			foreach ($list->result() as $row) {
				//$this->_send_mail_notification($row->email, $subject, $body, array());
			}
			
			set_success_message('Kontrak Berhasil Dihentikan.');
			$this->output->set_content_type('application/json')->set_output(json_encode($return));
		
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

			set_success_message('Data surat masuk berhasil disimpan.');
			redirect('surat/kontrak/kontrak_aktif/');
			exit;
		
		} else {
			set_error_message(validation_errors());
		}
	}
	
	function selesaikan_kontrak ($surat_id){
		$result = $this->db->get_where('surat', array('surat_id' => $surat_id));
		if($result->num_rows() > 0) {
			$surat = $result->row();
			if($surat->status != 99) {
				$this->db->update('surat', array('status' => 99, 'catatan_pengiriman' => 'waktu_habis','arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $surat_id));
				
				$kontrak_id = generate_unique_id();
		
				$data['addendum_id'] = $kontrak_id; 
				$data['surat_id'] = $surat_id; 
				$data['status'] = 99;
				$data['function_ref_id'] = 5;
				$data['created_id'] = get_user_id();
				$data['organization_id'] = get_user_data('organization_id');
				$data['jenis_agenda'] = 'CM';
				$data['agenda_id'] = $surat->agenda_id;
				
				$data['sifat_surat'] = 'KP - ' . $surat->agenda_id; //kode
				$data['surat_no'] = 'NOPEM - ' . $surat->agenda_id; //nomor
				$data['surat_perihal'] = $surat->surat_perihal; //hal
				$data['status_berkas'] = $surat->status_berkas; //mitra
				$data['surat_ringkasan'] = 'Masa Aktif Kontrak Sudah Terlewati'; //keterangan
				$data['catatan_pengiriman'] = 'waktu_habis'; //alasan
				$data['jenis_surat'] = 'jenis_henti_waktu'; //jenis_henti
				$data['surat_tgl'] = date('Y-m-d H:i:s');
				$data['surat_tgl_masuk'] = date('Y-m-d H:i:s');
				
				$this->db->insert('addendum', $data);
			}
		}
	}

}

/**
 * End of file
 */