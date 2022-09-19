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
 * @filesource Dashboard_model.php
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

class Laporan_model extends LX_Model {

	var $func_eks_masuk = 1;
	var $func_eks_keluar = 2;
	var $func_internal = 3;
	var $func_keputusan = 4;
	var $func_tugas = 13;
	
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
		
	}	
	
	/**
	 * @param unknown $jenis_agenda
	 */
	function get_my_surat($jenis_agenda = NULL) {
		$param = " s.jenis_agenda = '$jenis_agenda' AND s.status <> 99 AND s.status <> 404 ";
		if(!has_permission(7)) {
			$param = " s.jenis_agenda = '$jenis_agenda' AND osa.status IS NULL AND s.status <> 404";
		}
		
		$sql = "SELECT DISTINCT s.surat_id
				  FROM surat s
 				  JOIN notify n ON(n.ref_id = s.surat_id AND s.jenis_agenda = '$jenis_agenda' " . ((!has_permission(7)) ? (" AND notify_user_id = '" . get_user_id() . "'") : '') . ")
			 LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(23)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
			 LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
				  JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
						WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.function_ref_id = s.function_ref_id AND mr.module_ref_name = 'Surat') AND fp.flow_seq = s.status)
			WHERE $param ";
		
		return $this->db->query($sql);
	}
	
	function get_my_contract($jenis_agenda = NULL) {
		$sql = "SELECT s.surat_id
				  FROM surat s
 				  JOIN notify n ON(n.ref_id = s.surat_id AND s.jenis_agenda = '$jenis_agenda' AND notify_user_id = '" . get_user_id() . "')
			 LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(23)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
			 LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
				  JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
																	WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'kontrak' AND mr.module_ref_name = 'Surat') AND fp.flow_seq = s.status)
				 WHERE s.jenis_agenda = '$jenis_agenda' AND osa.status IS NULL AND s.status != 99 ";
		
		return $this->db->query($sql);
	}
	
	/**
	 * 
	 */
	function get_my_disposisi() {
		$sql = "SELECT d.disposisi_id id
				  FROM disposisi d
				  JOIN surat s ON(d.ref_type = 'surat' AND d.ref_id = s.surat_id)
				  JOIN notify n ON(n.ref_id = d.disposisi_id AND n.agenda = 'disposisi' AND notify_user_id = '" . get_user_id() . "')
				 WHERE d.status <> 99";
		
		return $this->db->query($sql);
	}
	
	/**
	 * 
	 */
	function get_structure_tree() {
		$sql = "SELECT os.organization_structure_id id,
			   os.parent_id parent,
			   os.unit_name title,
			   os.description,
			   u.user_id,
			   u.external_id nip,
			   u.user_name,
			   u.email,
			   u.profile_picture photo,
			   u.sex,
			   'structureTemplate' AS \"templateName\"
		  FROM system_security.organization_structure os
	 LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id AND us.structure_head = 1 AND us.status = 1)
	 LEFT JOIN system_security.users u ON(u.user_id = us.user_id AND u.active = 1)
		 WHERE os.status = 1
	  ORDER BY os.organization_structure_id, os.unit_name";
		
		$list = $this->db->query($sql);
		
		$return = array();
		foreach($list->result() as $row) {
			$row->id = intval($row->id);
			if($row->photo == '' || !file_exists( str_replace('/lx_media', './assets/media/', $row->photo))) {
				$row->photo = ($row->sex) ? '/lx_media/photo/' . $row->sex .  '.jpg' : '/lx_media/photo/m.jpg';
			}
			
			$return[] = $row;
		}
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * @param unknown $type
	 */
	function get_my_list($type) {
	
		switch ($type) {
			case 'surat_masuk_eksternal' :
 				$param = " s.jenis_agenda = 'SME' AND s.status <> 404";
 				if(!has_permission(7)) {
 					$param = " s.jenis_agenda = 'SME' AND osa.status IS NULL AND s.status <> 404";
 				}
				
 				$sql = "SELECT DISTINCT s.surat_id id, s.agenda_id, s.jenis_agenda, 
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, s.surat_no no_surat,
 							   to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal_surat,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
 							    '<br> <strong>Status : </strong>' || COALESCE(fp.title, 'Arsip TU')
 							   ) deskripsi,
 							   COALESCE(fp.title, 'Arsip') status_surat,
 							   n.detail_link link,
 							   ('surat/external/incoming_view/' || s.surat_id) link1,
 								s.status, s.surat_from_ref_data, (d.status) status_disposisi, (d.distribusi) distribusi_disposisi, d.to_user_id, s.created_time
 						  FROM surat s
 						  JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id =  " . $this->func_eks_masuk . ((!has_permission(7)) ? (" AND notify_user_id = '" . get_user_id() . "'") : '') . ")
 						  LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(23)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
 						  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = s.function_ref_id 
 						  	AND fp.flow_seq = s.status)
 						  LEFT JOIN (SELECT DISTINCT ON (ref_id) ref_id, disposisi_id, to_user_id, status, distribusi FROM disposisi) d ON(s.surat_id = d.ref_id)
 						 WHERE $param ORDER BY s.created_time DESC";

				$list = $this->db->query($sql);
				break;
			case 'surat_keluar_eksternal' :
				$param = " s.jenis_agenda = 'SKE' ";
 				if(!has_permission(7)) {
 					$param = " s.jenis_agenda = 'SKE' AND osa.status IS NULL ";
 				}
				
 				$sql = "SELECT DISTINCT s.surat_id id, s.agenda_id, s.jenis_agenda, 
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip ||'/_/RSUD-BLJ') ELSE s.surat_no END) no_surat,
 							   to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal_surat, 
 							   ('No. <strong>' || (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip ||'/_/RSUD-BLJ') ELSE s.surat_no END) || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
 							    
 							    '<br> <strong>Status : </strong>' || COALESCE(fp.title, 'Arsip TU')
 							   ) deskripsi,
 							   COALESCE(fp.title, 'Arsip') status_surat,
 							   n.detail_link link,
 							   ('surat/external/outgoing_view/' || s.surat_id) link1,
 								s.status, s.surat_to_ref_data, s.created_time, to_char(s.surat_awal, 'DD-MM-YYYY') surat_awal
 						  FROM surat s
 						  JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = " . $this->func_eks_keluar . ((!has_permission(7)) ? (" AND notify_user_id = '" . get_user_id() . "'") : '') . ")
 						  LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(23)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
 						  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
							JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
							WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'external/outgoing' AND mr.module_ref_name = 'Surat') AND fp.flow_seq = s.status)
 						 WHERE $param ORDER BY s.created_time DESC";
 					 
 				$list = $this->db->query($sql);
				break;
			case 'surat_internal' :
				$param = " s.jenis_agenda = 'SI' ";
				if(!has_permission(7)) {
					$param = " s.jenis_agenda = 'SI' AND osa.status IS NULL ";
				}
				
				$sql = "SELECT DISTINCT s.surat_id id, s.agenda_id, s.jenis_agenda,
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip || '/' || s.surat_from_ref || '/________/' || date_part('year', CURRENT_DATE)) ELSE s.surat_no END) no_surat,
 							   to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal_surat,
 							   ('No. <strong>' || (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip || '/' || s.surat_from_ref || '/________/' || date_part('year', CURRENT_DATE)) ELSE s.surat_no END) || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
						
 							    '<br> <strong>Status : </strong>' || COALESCE(fp.title, 'Arsip TU')
 							   ) deskripsi,
 							   COALESCE(fp.title, 'Arsip') status_surat,
 							   n.detail_link link,
 							   ('surat/internal/sheet_view/' || s.surat_id) link1,
 								s.status, (d.status) status_disposisi, s.created_time, to_char(s.surat_awal, 'DD-MM-YYYY') surat_awal
 						  FROM surat s
 						  JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = " . $this->func_internal . ((!has_permission(7)) ? (" AND notify_user_id = '" . get_user_id() . "'") : '') . ")
 						  LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(23)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
 						  LEFT JOIN disposisi d ON(s.surat_id = d.ref_id)
 						  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
							JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
							WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'internal' AND mr.module_ref_name = 'Surat') AND fp.flow_seq = s.status)
							WHERE $param ORDER BY s.created_time DESC";
				
				$list = $this->db->query($sql);
				break;				
			case 'disposisi' :
 				$sql = "SELECT d.disposisi_id id, s.agenda_id, s.jenis_agenda, 
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, s.surat_no no_surat,
 							   to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal_surat,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal
 							   ) deskripsi,
 							   '-' status_surat,
 							   --n.detail_link link,
 							   ('surat/disposisi/sheet/' || d.disposisi_id) link1, s.status, s.surat_from_ref_data, s.surat_to_ref_data, (d.status) status_disposisi, (d.distribusi) distribusi_disposisi, d.to_user_id
 						  FROM (SELECT DISTINCT ON (ref_id) ref_id, ref_type, disposisi_id, to_user_id, status, distribusi FROM disposisi) d
 						  JOIN surat s ON(d.ref_type = 'surat' AND d.ref_id = s.surat_id)  
 						  " . ((!has_permission(7)) ? (" JOIN notify n ON(n.ref_id = d.disposisi_id AND n.agenda = 'disposisi' AND notify_user_id = '" . get_user_id() ."')" ) : '') . "
 						 WHERE d.status <> 99";
 				
 				$list = $this->db->query($sql);
				break;				
			case 'kontrak_maintenance' :
				$param = " s.jenis_agenda = 'CM' ";
				$sql = "SELECT s.surat_id id, s.agenda_id, s.jenis_agenda, 
					(s.jenis_agenda || '-' || s.agenda_id) no_agenda, 
				   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || s.surat_unit_lampiran ||
					'</strong><br> <strong>Mitra : </strong>' || sv.val ||
					'<strong><br>Tanggal Berlaku: </strong> ' || to_char(s.surat_awal, 'DD-MM-YYYY') || ' s/d ' || to_char(s.surat_akhir, 'DD-MM-YYYY') ||
					'<br> <strong>Perihal : </strong>' || s.surat_perihal ||
					'<br> <strong>Status : </strong>' || COALESCE(fp.title, 'Proses Batal')
				   ) deskripsi,
				   s.surat_akhir as surat_akhir,
				   n.detail_link link
					FROM surat s
					 JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = s.function_ref_id AND notify_user_id = '" . get_user_id() . "')
					 LEFT JOIN system_security.system_variables sv on (s.status_berkas = sv.key)
					 LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id)
					 LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = 5 AND fp.flow_seq = s.status)
					 WHERE $param ORDER BY s.surat_akhir ASC";
				
				$list = $this->db->query($sql);
				break;
			case 'tugas' :
				$param = " s.jenis_agenda = 'ST' AND s.status <> 404 ";
				if(!has_permission(7)) {
					$param = " s.jenis_agenda = 'ST' AND osa.status IS NULL AND s.status <> 404 ";
				}
				
				$sql = "SELECT DISTINCT s.surat_id id, s.agenda_id, s.jenis_agenda,
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda, (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip ||'/_/SURAT PERINTAH') ELSE s.surat_no END) no_surat,
 							   to_char(s.surat_tgl, 'DD-MM-YYYY') tgl_surat, to_char(s.surat_tgl_masuk, 'DD-MM-YYYY') tgl_terima_surat, s.surat_perihal perihal_surat,
 							   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
 							    '</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
						
 							    '<br> <strong>Status : </strong>' || COALESCE(fp.title, 'Arsip')
 							   ) deskripsi,
 							   COALESCE(fp.title, 'Arsip') status_surat,
 							   n.detail_link link,
 							   ('surat/tugas/tugas_view/' || s.surat_id) link1,
 								s.status, s.created_time, to_char(s.surat_awal, 'DD-MM-YYYY') surat_awal, s.distribusi distribusi_tujuan
 						  FROM surat s
 						  JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = " . $this->func_tugas. ((!has_permission(7)) ? (" AND notify_user_id = '" . get_user_id() . "'") : '') . ")
 						  LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(23)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
 						  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
							JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
							WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'tugas/tugas' AND mr.module_ref_name = 'Surat') AND fp.flow_seq = s.status)
							WHERE $param ORDER BY s.created_time DESC";
				
				$list = $this->db->query($sql);
				break;
		}

		return $list;		
	}
	
	/**
	 * @param unknown $key
	 */
	function get_keyword_result($key, $type = NULL) {
// 		var_dump($type);
		$key = strtolower($key);
		$sql = array();
			
		$param_masuk = " s.jenis_agenda = 'SME' ";
		if(!has_permission(7)) {
			$param_masuk = " s.jenis_agenda = 'SME' AND osa.status IS NULL AND s.surat_to_ref_id = " . get_user_data('unit_id');
		}
	
		$param_keluar = " s.jenis_agenda = 'K' AND s.status <> 99 ";
		if(!has_permission(7)) {
			$param_keluar = " s.jenis_agenda = 'K' AND osa.status IS NULL ";
		}
				
 		$sql['surat_eksternal_masuk'] = "SELECT s.surat_id id, s.agenda_id, 
													   (s.jenis_agenda || '-' || s.agenda_id) no_agenda,
													   ('No. <strong>' || s.surat_no || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
														'</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
														'<br> <strong>Status : </strong>' || COALESCE(fp.title, 'Arsip TU')
													   ) deskripsi,
													   n.detail_link link,
														s.status
											  FROM surat s
											  JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = s.function_ref_id AND notify_user_id = '" . get_user_id() . "')
											  LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(8)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
											  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = 1 AND fp.flow_seq = s.status)
											WHERE $param_masuk";
							
		$sql['surat_eksternal_keluar'] = "SELECT s.surat_id id, s.agenda_id, 
 							   (s.jenis_agenda || '-' || s.agenda_id) no_agenda,
											   ('No. <strong>' || (CASE WHEN s.surat_no = '{surat_no}' THEN (s.kode_klasifikasi_arsip || '/________/' || date_part('year', CURRENT_DATE)) ELSE s.surat_no END) || '</strong> Tgl. <strong>' || to_char(s.surat_tgl, 'DD-MM-YYYY') ||
														'</strong><br> <strong>Perihal : </strong>' || s.surat_perihal ||
														'<br> <strong>Status : </strong>' || COALESCE(fp.title, 'Arsip TU')
													   ) deskripsi,
											   n.detail_link link,
												s.status
										  FROM surat s
										  JOIN notify n ON(n.ref_id = s.surat_id AND n.function_ref_id = " . $this->func_eks_keluar . " AND notify_user_id = '" . get_user_id() . "')
										  LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(8)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '' ) . ")
										  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
																					JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
																					WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'external/outgoing' AND mr.module_ref_name = 'Surat') AND fp.flow_seq = s.status)
										WHERE $param_keluar";
		$sql['disposisi'] = "SELECT d.disposisi_id id ,
		 							   (se.jenis_agenda || '-' || se.agenda_id) no_agenda,
		 							   ('No. <strong>' || se.surat_no || '</strong> Tgl. <strong>' || to_char(se.surat_tgl, 'DD-MM-YYYY') ||
		 							    '</strong><br> <strong>Perihal : </strong>' || se.surat_perihal ||
		 							    '<br> <strong>Dari : </strong>' || se.surat_ext_nama || ', ' || se.surat_ext_title || ', ' || se.surat_ext_instansi ||
		 							    '<br> <strong>Kepada : </strong>' || se.surat_int_nama || ', ' || se.surat_int_jabatan || ' ' || se.surat_int_unit ||
		 							    '<br> <strong>Status : </strong>' || fp.title
		 							   ) deskripsi,
		 							   ('surat/external/incoming_view/' || se.surat_eksternal_id) link
	 						  FROM disposisi d
	 						  JOIN surat_eksternal se ON(d.ref_type = 'surat_eksternal' AND d.ref_id = se.surat_eksternal_id)
	 						  LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = (SELECT fr.function_ref_id FROM system_security.function_ref fr
																			JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
																			WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'disposisi' AND mr.module_ref_name = 'Surat') AND fp.flow_seq = se.status)
		 					WHERE d.surat_from_unit_id = " . get_user_data('unit_id') . " OR d.surat_to_unit_id = " . get_user_data('unit_id');
// 		$sql['surat_internal'] = "";
		
		$surat_eksternal_key = array("LOWER(s.agenda_id) LIKE '%$key%'", "LOWER(s.surat_no) LIKE '%$key%'", "LOWER(s.surat_perihal) LIKE '%$key%'","LOWER(s.surat_from_ref_data) LIKE '%$key%'", "LOWER(s.surat_to_ref_data) LIKE '%$key%'", "LOWER(s.jenis_agenda) LIKE '%$key%'", "LOWER(s.status_berkas) LIKE '%$key%'", "LOWER(s.sifat_surat) LIKE '%$key%'", "LOWER(s.jenis_surat) LIKE '%$key%'", "LOWER(s.surat_ringkasan) LIKE '%$key%'");
		
		switch ($type) {
			case 'surat_eksternal_masuk' :
				$sql_surat_eksternal_masuk = $sql[$type] . " AND (" . implode(' OR ', $surat_eksternal_key) . ")";
				$list = $this->db->query($sql_surat_eksternal_masuk);
				break;
			case 'surat_eksternal_keluar' :
				$surat_eksternal_keluar = $sql[$type] . " AND(" . implode(' OR ', $surat_eksternal_key) . ")";
				$list = $this->db->query($surat_eksternal_keluar);
				break;
			case 'disposisi' :
				break;
// 			case 'surat_internal' :				
// 				break;
			default :
				$global_query = $sql_surat_eksternal_masuk = $sql['surat_eksternal_masuk'] . " AND(" . implode(' OR ', $surat_eksternal_key) . ") ";
				$global_query .= " UNION ";
				$global_query .= $sql['surat_eksternal_keluar'] . " AND(" . implode(' OR ', $surat_eksternal_key) . ")";
				
				$list = $this->db->query($global_query);
				break;
		}
		
		return $list;
	}

	function get_surat_list($function_ref_id, $month, $year) {
		if ($function_ref_id == 1) {
			$param = "AND EXTRACT(MONTH FROM s.surat_tgl_masuk) = '" . $month . "' 
					AND EXTRACT(YEAR FROM s.surat_tgl_masuk) = '" . $year . "' 
					AND s.status <> 404 ORDER BY s.surat_tgl_masuk ASC";
		}else {
			$param = "AND EXTRACT(MONTH FROM s.surat_awal) = '" . $month . "' 
					AND EXTRACT(YEAR FROM s.surat_awal) = '" . $year . "' 
					AND s.status <> 404 ORDER BY s.created_time ASC";
		}

		$sql = "SELECT s.*, COALESCE(fp.title, 'Arsip') status_surat FROM surat s
				LEFT JOIN system_security.flow_process fp ON(fp.function_ref_id = s.function_ref_id 
 					AND fp.flow_seq = s.status) 
				WHERE s.function_ref_id = '" . $function_ref_id . "' 
				$param";
			
		$result = $this->db->query($sql);		
		
		return $result->result_array();
	}

}

/**
 * End of file
 */