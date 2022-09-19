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
 * @filesource Arsip_model.php
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

class Arsip_model extends LX_Model {
	
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}		
	}
	
	/**
	 * @param unknown $type
	 */
	function get_my_list($type) {

		switch ($type) {
			case 'surat_masuk_eksternal' :
				$param = " s.jenis_agenda = 'SME' AND s.status = 99 ";
 				if(!has_permission(7)) {
					//ambil data dan user
 					$param = " s.jenis_agenda = 'SME' AND osa.organization_structure_id = " . get_user_data('unit_id');
 				}
				
 				$sql = "SELECT s.surat_id id, s.agenda_id, s.jenis_agenda,
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, s.surat_no no_surat, to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal, 'Arsip' status_surat,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
 							    '<br> <strong>Status : </strong> Arsip'
								) deskripsi,
 							   ('surat/external/incoming_view/' || s.surat_id) link,
 							   ('surat/external/incoming_view/' || s.surat_id) link1,
 								s.status, s.surat_from_ref_data, (d.distribusi) distribusi_disposisi
 						  FROM surat s 
 						   LEFT JOIN (SELECT DISTINCT ON (ref_id) ref_id, disposisi_id, to_user_id, status, distribusi FROM disposisi WHERE parent_id = '-') d ON(s.surat_id = d.ref_id) ";

 				if(!has_permission(7)) {
 					$sql .= "JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id AND osa.status = 99 AND osa.organization_structure_id = " . get_user_data('unit_id') . ") ";
 				}
 				
 				$sql .= "WHERE $param ORDER BY s.created_time DESC";

 				$list = $this->db->query($sql);
				break;
			case 'surat_eksternal_keluar' :
				$param = " s.jenis_agenda = 'SKE' AND s.status = 99 ";
 				
				
 				$sql = "SELECT s.surat_id id , s.agenda_id, s.jenis_agenda,
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip || '/_/RSUD-BLJ') ELSE s.surat_no END) no_surat, to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal, 'Arsip' status_surat,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
 							   
 							    '<br> <strong>Status : </strong> Arsip' 
 							   ) deskripsi,
 							   ('surat/external/outgoing_view/' || s.surat_id) link,
 							   ('surat/external/outgoing_view/' || s.surat_id) link1,
 								s.status, s.surat_to_ref_data, to_char(s.surat_awal, 'DD-MM-YYYY') awal_surat
 						  FROM surat s
 						 ";
				
 						 $sql .= "WHERE $param ORDER BY s.created_time DESC";
 				$list = $this->db->query($sql);
				break;
				
			case 'surat_internal' :
				$param = " s.jenis_agenda = 'SI' AND s.status = 99 ";
 				if(!has_permission(7)) {
 					$param = " s.jenis_agenda = 'SI' AND osa.organization_structure_id = " . get_user_data('unit_id');
 				}
				
 				$sql = "SELECT s.surat_id id, s.agenda_id, s.jenis_agenda,
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, s.surat_no no_surat, to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal, 'Arsip TU' status_surat,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
 							    '<br> <strong>Status : </strong>' || 'Arsip'
 							   ) deskripsi,
 							   ('surat/internal/sheet_view/' || s.surat_id) link,
 							   ('surat/internal/sheet_view/' || s.surat_id) link1,
 								s.status, to_char(s.surat_awal, 'DD-MM-YYYY') awal_surat
 						  FROM surat s ";

 				if(!has_permission(7)) {
 					$sql .= "JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id AND osa.status = 99 AND osa.organization_structure_id = " . get_user_data('unit_id') . ") ";
 				}
 				
 				$sql .= "WHERE $param ORDER BY s.created_time DESC";
				
				$list = $this->db->query($sql);
				
				break;
			case 'disposisi' :
				$param = "  ";
				if(!has_permission(7)) {
					$param = "WHERE d.surat_from_unit_id = " . get_user_data('unit_id') . " OR d.surat_to_unit_id = " . get_user_data('unit_id');
				}
				$sql = "SELECT d.disposisi_id id, s.agenda_id, s.jenis_agenda,
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, s.surat_no no_surat, to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal, '-' status,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal
 							   ) deskripsi,
							   COALESCE(fp.title, 'Arsip') status_surat,
 							   --n.detail_link link,
 							   ('surat/disposisi/sheet/' || d.disposisi_id) link1, s.surat_from_ref_data, s.surat_to_ref_data, (d.status) status_disposisi, (d.distribusi) distribusi_disposisi
 						  FROM disposisi d
 						  JOIN surat s ON(d.ref_type = 'surat' AND d.ref_id = s.surat_id)
 						  " . ((!has_permission(7)) ? (" JOIN notify n ON(n.ref_id = d.disposisi_id AND n.agenda = 'disposisi' AND notify_user_id = '" . get_user_id() ."')" ) : '') . "
						  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = 12 
 						   AND fp.flow_seq = d.status)
 						 WHERE d.status = 99 ORDER BY d.created_time DESC";
				
//  				$sql = "SELECT d.disposisi_id id ,
//  							   (se.jenis_agenda || '-' || se.agenda_id) no_agenda,
//  							   ('No. <strong>' || se.surat_no || '</strong> Tgl. <strong>' || to_char(se.surat_tgl, 'DD-MM-YYYY') ||
//  							    '</strong><br> <strong>Perihal : </strong>' || se.surat_perihal ||
//  							    '<br> <strong>Dari : </strong>' || se.surat_ext_nama || ', ' || se.surat_ext_title || ' ' || se.surat_ext_instansi ||
//  							    '<br> <strong>Kepada : </strong>' || se.surat_int_nama || ', ' || se.surat_int_jabatan || ' ' || se.surat_int_unit ||
//  							    '<br> <strong>Status : </strong>' || fp.title
//  							   ) deskripsi,
//  							   ('surat/disposisi/sheet_view/' || d.disposisi_id) link,
//  								d.status
//  						  FROM disposisi d
//  						  JOIN surat se ON(d.ref_type = 'surat' AND d.ref_id = se.surat_id)
//  						  JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
// 																	JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
// 																	WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'disposisi' AND mr.module_ref_name = 'Surat') AND fp.flow_seq = se.status)
//  						 $param ";
 				$list = $this->db->query($sql);
				break;
			case 'surat_tugas' :
				$param = " s.jenis_agenda = 'ST' AND s.status = 99 ";
				if(!has_permission(7)) {
					//ambil data dan user
					$param = " s.jenis_agenda = 'ST' AND osa.organization_structure_id = " . get_user_data('unit_id');
				}
				
				$sql = "SELECT s.surat_id id, s.agenda_id, s.jenis_agenda,
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip || '/_/SURAT PERINTAH') ELSE s.surat_no END) no_surat, to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal, 'Arsip' status_surat,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong> <br> <strong>Status : </strong> Arsip'
								) deskripsi,
 							   ('surat/tugas/tugas_view/' || s.surat_id) link,
 							   ('surat/tugas/tugas_view/' || s.surat_id) link1,
 								s.status, s.distribusi distribusi_tujuan, to_char(s.surat_awal, 'DD-MM-YYYY') awal_surat
 						  FROM surat s ";
				if(!has_permission(7)) {
					$sql .= "JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id AND osa.status = 99 AND osa.organization_structure_id = " . get_user_data('unit_id') . ") ";
				}
				
				$sql .= "WHERE $param ORDER BY s.created_time DESC";
				
				$list = $this->db->query($sql);
				break;
		}
		
		return $list;
		
	}

	function insert_arsip() {
// 		var_dump($_POST); exit;
		$obj = $this->data_object->surat;
		if(!isset($_POST['surat_tgl_masuk'])) {
			$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		} else {
			$_POST['surat_tgl_masuk'] = human_to_db($_POST['surat_tgl_masuk']);
		}
		if(!isset($_POST['surat_no'])) {
			if ($_POST['function_ref_id'] == 2) {
				$format = '[{"function":"char","value":"' . $_POST['kode_klasifikasi_arsip'] . '/' . $_POST['surat_from_ref'] . '/"},{"function":"annual_seq","value":"surat_keluar|0"},{"function":"char","value":"/"},{"function":"date","value":"Y"}]';
				
				$_POST['surat_no'] = $this->lx->number_generator($format);
			}else {		
				$_POST['surat_no'] = '{surat_no}';
			}
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
			$data['status'] = 99;
//			$data['jenis_agenda'] = 'I';
			$agenda = $_POST['jenis_agenda'] . ' - ';
			if($_POST['create_agenda'] == 1) {
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

			if(isset($_POST['surat_ttd'])) {
				$signed = array();
				foreach($_POST['surat_ttd'] as $k => $v) {
					$signed[$v['type_ttd']][] = $v; 
				}
				$data['signed'] = json_encode($signed);
			}
			$this->db->insert('surat', $data);

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
			
			if(isset($_POST['kode_klasifikasi_arsip']) && $_POST['kode_klasifikasi_arsip'] != '') {
				//if($surat->status != 99) {
					$this->db->update('surat', array('kode_klasifikasi_arsip' => $_POST['kode_klasifikasi_arsip'], 'status' => 99, 'arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $surat_id));
				//}
				
				$data = array();
				$data['organization_structure_id'] = get_user_data('unit_id');
				$data['created_id'] = get_user_id();
				$data['ref_type'] = 'surat';
				$data['ref_id'] = $surat_id;
				$data['status'] = 99; //arsipp
				$this->db->insert('org_struc_archive', $data);
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
			
			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $surat_id, 'agenda' => $agenda, 'note' => $data['note'], 'detail_link' => ($_POST['return'] . '_view/' . $surat_id), 'notify_user_id' => get_user_id(), 'status' => 99, 'read' => 0));
	
			set_success_message('Data arsip ' . humanize($_POST['function_ref_name']) . ' berhasil disimpan.');
			redirect($_POST['return']);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}
	
}

/**
 * End of file
 */