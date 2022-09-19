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
 * @filesource Ekspedisi_model.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Nov 21, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Ekspedisi_model extends LX_Model {

	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
	}
	
	/**
	 * @param unknown $surat_pengantar_id
	 */
	function get_ekspedisi($ekspedisi_id = NULL) {
		if($ekspedisi_id == NULL) {
			$sql = "SELECT * FROM ekspedisi WHERE status >= 0";
			return $this->db->query($sql);
			
		} else {
			$sql = "SELECT * FROM ekspedisi WHERE status >= 0 AND ekspedisi_id = '$ekspedisi_id'";
			return $this->db->query($sql);
		}
	}
	
	/**
	 * @param unknown $surat_id
	 */
	function get_ekspedisi_aktif($surat_id) {
		$sql = "SELECT * FROM ekspedisi pe 
				JOIN ekspedisi_items pel ON(pel.ekspedisi_id = pe.ekspedisi_id) 
				WHERE pe.status >= 0 AND pel.status_terima >= 0 AND pel.ref_id = '$surat_id' 
				ORDER BY pel.entry_id DESC ";
		return $this->db->query($sql);
	}
	
	/**
	 * @param unknown $ekspedisi_id
	 */
	function get_list_surat($ekspedisi_id) {
		$sql = "SELECT * FROM surat s
				JOIN ekspedisi_items ei ON(ei.ref_id = s.surat_id)
				WHERE ei.ekspedisi_id = '$ekspedisi_id' ";
		return $this->db->query($sql);
	}

	/**
	 * @param unknown $ekspedisi_id
	 */
	function get_list_surat_by_status($ekspedisi_id, $status_surat) {
		$sql = "SELECT * FROM surat s
				JOIN ekspedisi_items ei ON(ei.ref_id = s.surat_id)
				WHERE s.status = '$status_surat' AND ei.ekspedisi_id = '$ekspedisi_id' ";
		return $this->db->query($sql);
	}

	/**
	 * @param unknown $ekspedisi_id
	 */
	function get_list_surat_disposisi($ekspedisi_id) {
		$sql = "SELECT d.distribusi, d.ref_id, d.to_user_id FROM disposisi d
				JOIN ekspedisi_items ei ON(ei.ref_id = d.ref_id)
				WHERE d.parent_id = '-' AND ei.ekspedisi_id = '$ekspedisi_id' ORDER BY d.disposisi_id ";
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	/**
	 * 
	 */
	function insert_ekspedisi() {
//		var_dump($_POST);exit(); 
		if(isset($_POST['detail_ekspedisi'])) { 
			if($this->_validate_post_data($this->data_object->ekspedisi, 'add') != FALSE) {
				$data = array_intersect_key($_POST, $this->data_object->ekspedisi);
				$data['ekspedisi_id'] = generate_unique_id();
				$data['created_id'] = get_user_id();
				$data['organization_id'] = get_user_data('organization_id');
				$data['to_ref_data'] = json_encode($data['to_ref_data']);
				
				if(isset($_POST['surat_tembusan'])) {
					$tembusan = array();
					foreach($_POST['surat_tembusan'] as $k => $v) {
						$tembusan[] = $v;
					}
					
					$data['tembusan'] = json_encode($tembusan);
				}
	
				$this->db->insert('ekspedisi', $data);
				
				foreach($_POST['detail_ekspedisi'] as $k => $v) {
					$this->db->update('surat', array('surat_pengantar_id' => $data['ekspedisi_id']), array('surat_id' => $v));
					
					$detail = array();
					$detail['ekspedisi_id'] = $data['ekspedisi_id'];
					$detail['ref_type'] = $_POST['jenis_agenda'];
					$detail['ref_id'] = $v;
					
					$this->db->insert('ekspedisi_items', $detail);
						
					// set workspace
					$list = user_with_permission(8);
					foreach ($list->result() as $row) {
						$this->db->update('notify', array('note' => ('Ekspedisi ' . $_POST['title'] . ' baru dibuat')), array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $v, 'notify_user_id' => $row->user_id));
					}
				}

				$data_notes = array_intersect_key($_POST, $this->data_object->process_notes);
				$data_notes['organization_id'] = get_user_data('organization_id');
				$data_notes['flow_type'] = 1;  // forward
				$data_notes['table'] = 'surat';
				$data_notes['ref_id'] = $_POST['detail_ekspedisi'][0];
				$data_notes['user_id'] = get_user_id();
				$data_notes['flow_seq'] = 2;
				$this->db->insert('process_notes', $data_notes);
				
				set_success_message('Data surat pengantar berhasil disimpan.');
				
				// if ($_POST['function_ref_id'] == 13) {
				// 	redirect('surat/ekspedisi/sheet/' . $_POST['function_ref_id'] . '/' . $data['ekspedisi_id']);
				// }else {
				// 	redirect('surat/ekspedisi/sheet/' . $data['ekspedisi_id']);
				// }

				redirect('surat/ekspedisi/sheet/' . $data['ekspedisi_id']);
				
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
	function update_ekspedisi() {
//		var_dump($_POST); exit();
		if(isset($_POST['detail_ekspedisi'])) {
			if($this->_validate_post_data($this->data_object->ekspedisi, 'edit') != FALSE) {
				$_POST['status'] = 0;
				$_POST['petugas_penerima'] = '';
				$data = array_intersect_key($_POST, $this->data_object->ekspedisi);
				$data['modified_id'] = get_user_id();
				$data['modified_time'] = date('Y-m-d H:i:s');
				
				$data['to_ref_data'] = json_encode($data['to_ref_data']);
				
				if(isset($_POST['surat_tembusan'])) {
					$tembusan = array();
					foreach($_POST['surat_tembusan'] as $k => $v) {
						$tembusan[] = $v;
					}
					$data['tembusan'] = json_encode($tembusan);
				}

				unset($data['ekspedisi_id']);
				$this->db->update('ekspedisi', $data, array('ekspedisi_id' => $_POST['ekspedisi_id']));
				
				$this->db->update('surat', array('surat_pengantar_id' => '-'), array('surat_pengantar_id' => $_POST['ekspedisi_id']));
				$this->db->delete('ekspedisi_items', array('ekspedisi_id' => $_POST['ekspedisi_id']));
				
				foreach($_POST['detail_ekspedisi'] as $k => $v) {
					$this->db->update('surat', array('surat_pengantar_id' => $_POST['ekspedisi_id']), array('surat_id' => $v));

					$detail = array();
					$detail['ekspedisi_id'] = $_POST['ekspedisi_id'];
					$detail['ref_type'] = $_POST['jenis_agenda'];
					$detail['ref_id'] = $v;
					
					$this->db->insert('ekspedisi_items', $detail);
				}

				set_success_message('Data surat pengantar berhasil perbaharui.');
				
				// if ($_POST['function_ref_id'] == 13) {
				// 	$_POST['ekspedisi_id'] = $v;
				// 	redirect('surat/ekspedisi/sheet/' . $_POST['function_ref_id'] . '/' . $_POST['ekspedisi_id']);
				// }else {
				// 	redirect('surat/ekspedisi/sheet/' . $_POST['ekspedisi_id']);
				// }

				redirect('surat/ekspedisi/sheet/' . $_POST['ekspedisi_id']);
				
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
	function kirim_ekspedisi() {
		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('global/dashboard') . "')");
		
		$this->db->update('ekspedisi', array('status' => 0, 'pengiriman_time' => date('Y-m-d H:i:s')), array('ekspedisi_id' => $_POST['ref_id']));
		
		$sql = "SELECT * FROM ekspedisi WHERE status >= 0 AND ekspedisi_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		$ekspedisi = $result->row();
		
		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.function_ref_id = " . $_POST['function_ref_id'];
		$result = $this->db->query($sql);
		$function_ref = $result->row();

		$sql_surat = "SELECT * FROM surat WHERE surat_pengantar_id = '" . $_POST['ref_id'] . "' ";
		$list = $this->db->query($sql_surat);
		$row_surat = $list->row();

		if ($row_surat->jenis_agenda == 'SME'){
			$tgl_surat = $row_surat->surat_tgl_masuk;
		}else {
			$tgl_surat = $row_surat->surat_tgl;
		}

		//$subject = 'Notifikasi pengiriman surat';
		$subject = $row_surat->jenis_agenda . '-' . $row_surat->agenda_id . ' - Pengiriman Surat - ' . $row_surat->surat_perihal;
		$body = 'Anda menerima surat masuk yang diterima pada tanggal ' . db_to_human_local($tgl_surat) . ' dalam daftar kerja anda.<br>' .
//				'Surat Masuk Baru dalam daftar kerja anda.<br>' .
// 				'<strong>Catatan : </strong><br>' . $ekspedisi->catatan_pengirim .
				'<br><strong>Petugas Pengirim : </strong>' . $ekspedisi->petugas_pengirim . '<br>' .
				'<br><strong>Identitas Surat</strong><br>';

		$list_tujuan = user_in_unit($ekspedisi->to_ref_id);

		$n = 1;
		foreach ($list->result() as $row) {			
			$surat_from_ref_data = json_decode($row->surat_from_ref_data, TRUE);

			if ($row->jenis_agenda == 'SME') {
				$instansi = $surat_from_ref_data['instansi'];
			}else {
				if ($row->jenis_agenda != 'ST') {
					$instansi = $surat_from_ref_data['unit'];
				}else {
					$instansi = '';
				}
			}

			if ( $row->status == 3 ) {
				set_success_message('Surat sudah dikirim.');
				$this->output->set_content_type('application/json')->set_output(json_encode($return));
				return false;
			}

			$surat_from_ref_data = json_decode($row->surat_from_ref_data, TRUE);
			
 			$sql = "UPDATE surat
 					   SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 1 END),
 						   kirim_time = '" . date('Y-m-d H:i:s') . "'
					 WHERE surat_id = '" . $row->surat_id . "'";
			$this->db->query($sql);
			
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1;  //forward
			$data['table'] = 'surat';
			$data['ref_id'] = $row->surat_id;
			$data['flow_seq'] = $row->status + 1;
			$data['note'] = $ekspedisi->catatan_pengirim;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);
			
			$body .= $n . '. '. $row->jenis_agenda . '-' . $row->agenda_id . ' : ' . $row->surat_perihal . ', ' . $instansi . ' tanggal surat ' . db_to_human($row->surat_tgl) . '.<br>';
			
			$n++;

			switch($row->jenis_agenda) {
				case 'SI' :
					$detail_link = 'surat/internal/sheet_view/';
				break;
				case 'ST' :
					$detail_link = 'surat/tugas/tugas_view/';
				break;
				default : 	//SME
					$detail_link = 'surat/external/incoming_view/';
				break;				
			}
			
		// $dist = json_decode($row->distribusi);
		// foreach($dist as $row_dist) {
		// $list_tujuan = user_in_unit($row_dist);
			
			// set workspace untuk Penerima (link surat pengantar)
			if($row->surat_from_ref == 'eksternal') {
				$note = $_POST['title'] . ' No. ' . $row->surat_no . ' dari ' . $surat_from_ref_data['nama'] . ' &nbsp; | &nbsp; ' . $surat_from_ref_data['title'] . ' &nbsp; | &nbsp; ' .  $surat_from_ref_data['instansi'];
			} else {
				$note = $_POST['title'] . ' No. ' . $row->surat_no . ' dari ' . $surat_from_ref_data['nama'] . ' &nbsp; | &nbsp; ' . ((isset($surat_from_ref_data['unit'])) ? ($surat_from_ref_data['jabatan'] . ', ' . $surat_from_ref_data['unit'] . ' &nbsp; | &nbsp; ') : '') . $surat_from_ref_data['dir'];
			}
			
			foreach ($list_tujuan->result() as $row_tujuan) {
				$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_id, 'agenda' => ($row->jenis_agenda . ' - ' . $row->agenda_id), 'note' => $note, 'detail_link' => ($detail_link . $row->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
			}

			$distribusi = json_decode($row->distribusi, TRUE);
			foreach ($distribusi as $dis_key => $dis_val) {
				foreach ($dis_val as $k => $v) {
					if ($row->function_ref_id != 3 && $row->function_ref_id != 13) {
						$list_distribusi = user_in_unit($v["unit_id"]);
					}else {
						$list_distribusi = '';
					}

					if ($row->function_ref_id != 3 && $row->function_ref_id != 13) {
						foreach ($list_distribusi->result() as $row_distribusi) {
							$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_id, 'agenda' => ($row->jenis_agenda . ' - ' . $row->agenda_id), 'note' => $note, 'detail_link' => ($detail_link . $row->surat_id), 'notify_user_id' => $row_distribusi->user_id, 'read' => 0));
						}			
					}		
				}
			}
		}

		foreach ($list_tujuan->result() as $row) {
			$this->_send_mail_notification($row->email, $subject, $body, array());			
		}
			
		set_success_message('Surat berhasil dikirim.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	function get_list_distribusi_tembusan($data) {
		$id = implode("','", $data['surat_id']);
		$sql = "SELECT * FROM distribusi_tembusan 
				WHERE ref_id IN ('" . $id . "')";
		
		return $this->db->query($sql);
	}

	function get_list_disposisi($surat_id) {
		$sql = "SELECT * FROM disposisi 
				WHERE parent_id = '-' AND ref_id = '$surat_id' ";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0){
			return $query->row();
		}else {
			return '';
		}		
	}

}