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
 * @filesource Surat_model.php
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

class Surat_model extends LX_Model
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
		set_time_limit(0);
	}

	/**
	 * @param unknown $surat_id
	 */
	function get_surat($surat_id = NULL)
	{
		if ($surat_id == NULL) {
			$sql = "SELECT * FROM surat WHERE status >= 0";
			return $this->db->query($sql);
		} else {
			$sql = "SELECT s.*, u.user_name, osa.status unit_archive_status FROM surat s
					LEFT JOIN org_struc_archive osa ON(osa.ref_type = 'surat' AND osa.ref_id = s.surat_id " . ((!has_permission(8)) ? (" AND osa.organization_structure_id = " . get_user_data('unit_id')) : '') . ")
						JOIN system_security.users u ON(u.user_id = s.created_id)
						WHERE s.status >= 0 AND s.surat_id = '$surat_id' ";

			return $this->db->query($sql);
		}
	}

	/**
	 * @param unknown $surat_id
	 */
	function get_surat_flow($surat_id)
	{
		$sql = "SELECT pn.*, u.user_name FROM process_notes pn
		JOIN system_security.users u ON(u.user_id = pn.user_id)
		WHERE \"table\" = 'surat' AND ref_id = '$surat_id' ORDER BY created_time";
		return $this->db->query($sql);
	}

	/**
	 * @param unknown $type
	 * @param unknown $surat_id
	 */
	function get_list_ekspedisi($type, $surat_id)
	{
		$sql = "SELECT ex.*, ei.status_terima FROM ekspedisi ex 
				  JOIN ekspedisi_items ei ON(ei.ekspedisi_id = ex.ekspedisi_id)
				 WHERE ei.ref_type = '$type' AND ei.ref_id = '$surat_id' 
				 ORDER BY ex.created_time ";
		return $this->db->query($sql);
	}

	/**
	 * @param number $unit_id
	 * @return array
	 */
	function get_all_parents($unit_id)
	{
		$results = array();

		$sql = "SELECT os.*, us.jabatan, us.pangkat, u.user_id, u.external_id nip, u.user_name, u.email  FROM system_security.organization_structure os
				LEFT JOIN system_security.users_structure us ON(os.organization_structure_id = us.organization_structure_id) 
				LEFT JOIN system_security.users u ON(us.user_id = u.user_id AND u.active = 1)
				WHERE us.structure_head = 1 AND os.organization_structure_id = (SELECT parent_id FROM system_security.organization_structure WHERE organization_structure_id = $unit_id) ORDER BY us.jabatan DESC";

		$rows = $this->db->query($sql);

		if ($rows->num_rows() > 0) {
			foreach ($rows->result_array() as $row) {
				$row['unit_tree'] = $row['unit_name'];
				$results[] = $row;
				$child = $this->get_all_parents($row['organization_structure_id']);
				if (count($child) > 0) {
					$results = array_merge($results, $child);
				}
			}
		}

		return $results;
	}

	/**
	 * @param number $unit_id
	 * @return array
	 */
	function get_all_parents_st($unit_id)
	{

		if ($unit_id > 1 && $unit_id < 10) {
			$unit_id_2 = 11;
		} else if ($unit_id > 9) {
			$unit_id_2 = 3;
		} else {
			$unit_id_2 = $unit_id;
		}

		$results = array();

		$sql = "SELECT os.*, us.jabatan, us.pangkat, u.user_id, u.external_id nip, u.user_name, u.email  FROM system_security.organization_structure os
				LEFT JOIN system_security.users_structure us ON(os.organization_structure_id = us.organization_structure_id) 
				LEFT JOIN system_security.users u ON(us.user_id = u.user_id AND u.active = 1)
				WHERE us.structure_head = 1 AND (os.organization_structure_id = (SELECT parent_id FROM system_security.organization_structure WHERE organization_structure_id = $unit_id) OR os.organization_structure_id = (SELECT parent_id FROM system_security.organization_structure WHERE organization_structure_id = $unit_id_2)) ORDER BY us.jabatan, us.organization_structure_id DESC";

		$rows = $this->db->query($sql);

		if ($rows->num_rows() > 0) {
			foreach ($rows->result_array() as $row) {
				$row['unit_tree'] = $row['unit_name'];
				$results[] = $row;
				$child = $this->get_all_parents($row['organization_structure_id']);
				if (count($child) > 0) {
					$results = array_merge($results, $child);
				}
			}
		}

		return $results;
	}

	function get_notify($surat_id, $notify_user_id)
	{
		$sql = "SELECT n.* FROM notify n
		WHERE n.ref_id = '$surat_id' AND n.notify_user_id = '$notify_user_id' ";

		return $this->db->query($sql);
	}

	/**
	 * Called from POST method
	 * 
	 */
	function insert_surat()
	{
		// var_dump($_POST); exit;
		$obj = $this->data_object->surat;
		if (!isset($_POST['surat_tgl_masuk'])) {
			$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		} else {
			$_POST['surat_tgl_masuk'] = human_to_db($_POST['surat_tgl_masuk']);
		}

		if (!isset($_POST['surat_no'])) {
			$_POST['surat_no'] = '{surat_no}';
			//			$_POST['surat_no'] = $_POST['kode_klasifikasi_arsip'] . '/' . $_POST['official_code'] . '/________/' . date('Y');			
		}

		if ($_POST['function_ref_id'] == 1) {
			if (!isset($_POST['surat_tgl'])) {
				$_POST['surat_tgl'] = date('Y-m-d');
			} else {
				$_POST['surat_tgl'] = human_to_db($_POST['surat_tgl']);
			}
		} else {
			if (!isset($_POST['surat_awal'])) {
				$_POST['surat_awal'] = date('Y-m-d');
				$obj['surat_tgl'] = array('validate' => 'none', 'label' => '', 'rule' => '');
			} else {
				$_POST['surat_awal'] = human_to_db($_POST['surat_awal']);
				$_POST['surat_tgl'] = human_to_db($_POST['surat_tgl']);
			}
		}

		if ($_POST['function_ref_id'] == 3) {
			if ($_POST['opt_tujuan'] == '2') {
				$_POST['surat_from_ref'] = $_POST['opt_tujuan'];
				$_POST['surat_to_ref_id'] = $_POST['surat_to_ref_multi_id'];
				$_POST['surat_to_ref_data'] = $_POST['surat_to_ref_detail'];
			} else {
				$_POST['surat_from_ref'] = $_POST['opt_tujuan'];
				$_POST['surat_to_ref_id'] = $_POST['surat_to_ref_id'];
				$_POST['surat_to_ref_data'] = $_POST['surat_to_ref_data'];
			}
		} else {
			if ($_POST['surat_to_ref_id'] == '') {
				$obj['surat_to_ref_id'] = array('validate' => 'none', 'label' => '', 'rule' => '');
				$_POST['surat_to_ref_id'] = get_user_data('unit_id');
			} else {
				$_POST['surat_to_ref_id'] = $_POST['surat_to_ref_id'];
			}

			$_POST['surat_to_ref_data'] = $_POST['surat_to_ref_data'];
		}

		if ($this->_validate_post_data($obj, 'add') != FALSE) {
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

			if ($data['surat_from_ref'] == '2') {
				$data['surat_to_ref_data'] = $data['surat_to_ref_data'];
			} else {
				$data['surat_to_ref_data'] = json_encode($data['surat_to_ref_data']);
			}

			$data['surat_to_ref_id'] = $data['surat_to_ref_id'];

			if (isset($_POST['tembusan'])) {
				$tembusan = array();
				foreach ($_POST['tembusan'] as $k => $v) {
					$tembusan[] = $v;
				}

				$data['tembusan'] = json_encode($tembusan);
			}

			if (isset($_POST['distribusi_tembusan'])) {
				$distrbusi_tembusan = array();
				foreach ($_POST['distribusi_tembusan'] as $k => $v) {
					$distrbusi_tembusan[] = $v;
				}

				$data['distribusi'] = json_encode($distrbusi_tembusan);
			}

			if (isset($_POST['signed'])) {
				$signed = array();
				$signed['unit_id'] = $_POST['signed'];
				$signed['jabatan'] = $_POST['approval']['direksi'][$_POST['signed']]['jabatan'];
				$signed['pangkat'] = $_POST['approval']['direksi'][$_POST['signed']]['pangkat'];
				$signed['unit_name'] = $_POST['approval']['direksi'][$_POST['signed']]['unit_name'];
				$signed['nama_pejabat'] = $_POST['approval']['direksi'][$_POST['signed']]['nama_pejabat'];
				$signed['nip'] = $_POST['approval']['direksi'][$_POST['signed']]['nip'];
				$signed['user_id'] = $_POST['approval']['direksi'][$_POST['signed']]['user_id'];

				$data['signed'] = json_encode($signed);
			}

			if (isset($_POST['approval'])) {
				if (isset($_POST['signed'])) {
					$max_key = $_POST['approval']['direksi'][$_POST['signed']]['index'];
					$max_key = $max_key - 1;

					foreach ($_POST['approval']['direksi'] as $ak => $av) {
						if ($av['index'] > $max_key) {
							unset($_POST['approval']['direksi'][$ak]);
						}
					}
				}

				$data['approval'] = json_encode($_POST['approval']);
			}

			$this->db->insert('surat', $data);

			if (isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar|doc|docx|xls|xlsx';
				$this->load->library('upload', $config_file);

				$i = 0;
				foreach ($_POST['attachment'] as $k => $v) {
					if ($v['title'] != '' && !empty($_FILES['attachment_file_' . $k]['name'])) {
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
			$data['flow_type'] = 1;  //forward
			$data['table'] = 'surat';
			$data['ref_id'] = $surat_id;
			$data['user_id'] = get_user_id();
			$data['flow_seq'] = 0;
			$data['note'] = $_POST['function_ref_name'] . ' dibuat oleh ' . get_user_data('user_name');
			$this->db->insert('process_notes', $data);

			if ($_POST['function_ref_id'] != 1) {
				if ($_POST['asal_surat'] != '') {
					$this->db->insert('surat_ref', array('surat_from_ref_id' => $_POST['ref_surat_masuk_id'], 'created_id' => get_user_id(), 'created_time' => date('Y-m-d H:i:s'), 'function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $surat_id, 'note' => $_POST['asal_surat'], 'status' => 1));
				}
			}

			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $surat_id, 'agenda' => $agenda, 'note' => $data['note'], 'detail_link' => ($_POST['return'] . '_view/' . $surat_id), 'notify_user_id' => get_user_id(), 'status' => 0, 'read' => 0));

			set_success_message('Data surat ' . humanize($_POST['function_ref_name']) . ' berhasil disimpan.');
			redirect($_POST['return'] . '/' . $surat_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}

	function insert_surat_internal()
	{
		// 		var_dump($_POST); exit;
		$obj = $this->data_object->surat;
		if (!isset($_POST['surat_tgl_masuk'])) {
			$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		} else {
			$_POST['surat_tgl_masuk'] = human_to_db($_POST['surat_tgl_masuk']);
		}
		if (!isset($_POST['surat_no'])) {
			$_POST['surat_no'] = '{surat_no}';
		}
		if (!isset($_POST['surat_tgl'])) {
			$_POST['surat_tgl'] = date('Y-m-d');
		} else {
			$_POST['surat_tgl'] = human_to_db($_POST['surat_tgl']);
		}

		if ($this->_validate_post_data($obj, 'add') != FALSE) {
			$result = $this->db->get_where('system_security.function_ref', array('function_ref_id' => $_POST['function_ref_id']));
			$function_data = $result->row();
			$surat_id = generate_unique_id();
			$data = array_intersect_key($_POST, $this->data_object->surat);
			$data['surat_id'] = $surat_id;
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
			//	$data['status'] = 99;
			//	$data['jenis_agenda'] = 'I';
			$agenda = $_POST['jenis_agenda'] . ' - ';
			if ($_POST['create_agenda'] == 1) {
				$data['agenda_id'] = $this->lx->number_generator($function_data->format_agenda);
				$agenda .= $data['agenda_id'];
			}
			$data['surat_from_ref_data'] = json_encode($data['surat_from_ref_data']);

			$data['surat_to_ref_data'] = json_encode($data['surat_to_ref_data']);

			if (isset($_POST['tembusan'])) {
				$tembusan = array();
				foreach ($_POST['tembusan'] as $k => $v) {
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
				if ($result->num_rows() > 0) {
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

			if (isset($_POST['surat_ttd'])) {
				$signed = array();
				foreach ($_POST['surat_ttd'] as $k => $v) {
					$signed[$v['type_ttd']][] = $v;
				}

				$data['signed'] = json_encode($signed);
			}

			$this->db->insert('surat', $data);

			if (isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 20000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
				$this->load->library('upload', $config_file);

				$i = 0;
				foreach ($_POST['attachment'] as $k => $v) {
					if ($v['title'] != '' && !empty($_FILES['attachment_file_' . $k]['name'])) {
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
			$data['flow_type'] = 1; 	//forward
			$data['table'] = 'surat';
			$data['ref_id'] = $surat_id;
			$data['user_id'] = get_user_id();
			$data['flow_seq'] = 0;
			$data['note'] = 'Surat ' . $_POST['function_ref_name'] . ' dibuat oleh ' . get_user_data('user_name');
			$this->db->insert('process_notes', $data);

			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $surat_id, 'agenda' => $agenda, 'note' => $data['note'], 'detail_link' => ('surat/internal/sheet_view/internal/' . $surat_id), 'notify_user_id' => get_user_id(), 'status' => 0, 'read' => 0));

			set_success_message('Data surat ' . humanize($_POST['function_ref_name']) . ' berhasil disimpan.');
			redirect($_POST['return'] . '/' . $surat_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 * Called from POST method
	 * 
	 */
	function update_surat()
	{
		// var_dump($_POST); exit;
		$obj = $this->data_object->surat;
		$obj['surat_tgl'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		$obj['surat_tgl_masuk'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		$obj['surat_to_ref_id'] = array('validate' => 'none', 'label' => '', 'rule' => '');
		if ($this->_validate_post_data($obj, 'edit') != FALSE) {
			$surat_id = $_POST['surat_id'];
			unset($_POST['surat_id']);

			$result = $this->db->get_where('surat', array('surat_id' => $surat_id));
			if ($result->num_rows() > 0) {
				$surat = $result->row();

				$data = array_intersect_key($_POST, $obj);
				$data['modified_id'] = get_user_id();
				$data['modified_time'] = date('Y-m-d H:i:s');
				$data['surat_from_ref_data'] = json_encode($data['surat_from_ref_data']);
				//$data['surat_to_ref_data'] = json_encode($data['surat_to_ref_data']);

				if ($_POST['function_ref_id'] == 3) {
					if ($_POST['opt_tujuan'] == 2) {
						$data['surat_from_ref'] = $_POST['opt_tujuan'];
						$data['surat_to_ref_id'] = $_POST['surat_to_ref_multi_id'];
						$data['surat_to_ref_data'] = $_POST['surat_to_ref_detail'];
					} else {
						$data['surat_from_ref'] = $_POST['opt_tujuan'];
						$data['surat_to_ref_id'] = $data['surat_to_ref_id'];
						$data['surat_to_ref_data'] = json_encode($data['surat_to_ref_data']);
					}
				} else {
					$data['surat_to_ref_data'] = json_encode($data['surat_to_ref_data']);
				}

				if (isset($_POST['surat_tgl'])) {
					$data['surat_tgl'] = human_to_db($data['surat_tgl']);
				}

				if (isset($_POST['surat_awal'])) {
					$data['surat_awal'] = human_to_db($data['surat_awal']);
				}

				if (isset($_POST['surat_tgl_masuk'])) {
					$data['surat_tgl_masuk'] = human_to_db($data['surat_tgl_masuk']);
				}

				if (isset($_POST['tembusan'])) {
					$tembusan = array();
					foreach ($_POST['tembusan'] as $k => $v) {
						$tembusan[] = $v;
					}

					$data['tembusan'] = json_encode($tembusan);
				}

				if (isset($_POST['signed'])) {
					$signed = array();
					$signed['unit_id'] = $_POST['signed'];
					$signed['jabatan'] = $_POST['approval']['direksi'][$_POST['signed']]['jabatan'];
					$signed['pangkat'] = $_POST['approval']['direksi'][$_POST['signed']]['pangkat'];
					$signed['unit_name'] = $_POST['approval']['direksi'][$_POST['signed']]['unit_name'];
					$signed['nama_pejabat'] = $_POST['approval']['direksi'][$_POST['signed']]['nama_pejabat'];
					$signed['nip'] = $_POST['approval']['direksi'][$_POST['signed']]['nip'];
					$signed['user_id'] = $_POST['approval']['direksi'][$_POST['signed']]['user_id'];

					$data['signed'] = json_encode($signed);
				}

				if (isset($_POST['approval'])) {
					if (isset($_POST['signed'])) {
						$max_key = $_POST['approval']['direksi'][$_POST['signed']]['index'];
						$max_key = $max_key - 1;

						foreach ($_POST['approval']['direksi'] as $ak => $av) {
							if (isset($av['index'])) {
								if (intval($av['index']) > $max_key) {
									unset($_POST['approval']['direksi'][$ak]);
								}
							}
						}
					}

					$data['approval'] = json_encode($_POST['approval']);
				}

				// if(isset($_POST['approval'])) {
				// 	$approval = json_decode($surat->approval, TRUE);

				// 	if(isset($_POST['signed'])) {
				// 		//var_dump($_POST['signed']); exit();
				// 		$max_key = $_POST['approval']['direksi'][$_POST['signed']]['index'];
				// 		$max_key = $max_key - 1;
				// 		//$max_key = $approval['direksi'][$_POST['signed']]['index'];

				// 		foreach($_POST['approval']['direksi'] as $ak => $av) {
				// 			//echo $av['index'];
				// 			if($av['index'] > $max_key) {
				// 				unset($_POST['approval']['direksi'][$ak]);
				// 			}
				// 		}
				// 	}

				// 	if(isset($approval['non_direksi']['diskusi'])) {
				// 		$_POST['approval']['non_direksi']['diskusi'] = $approval['non_direksi']['diskusi'];
				// 	}

				// 	if(isset($approval['non_direksi']['status'])) {
				// 		$_POST['approval']['non_direksi']['status'] = $approval['non_direksi']['status'];
				// 	}

				// 	if(isset($approval['direksi']['diskusi'])) {
				// 		$_POST['approval']['direksi']['diskusi'] = $approval['direksi']['diskusi'];
				// 	}

				// 	if(isset($approval['direksi']['status'])) {
				// 		$_POST['approval']['direksi']['status'] = $approval['direksi']['status'];
				// 	}

				// 	//$data['approval'] = json_encode($_POST['approval']);
				// 	$data['approval'] = json_encode($approval);
				// }

				if ($_POST['function_ref_id'] == 3) {
					if (isset($_POST['distribusi_tembusan'])) {
						$distrbusi_tembusan = array();
						foreach ($_POST['distribusi_tembusan'] as $k => $v) {
							$distrbusi_tembusan[] = $v;
						}

						$data['distribusi'] = json_encode($distrbusi_tembusan);
					}
				} else {
					$distribusi = array();
					if (isset($_POST['distribusi']['direksi'])) {
						foreach ($_POST['distribusi']['direksi'] as $k => $v) {
							$sql = "SELECT os.organization_structure_id, os.unit_code, os.unit_name, os.level, u.user_name FROM system_security.organization_structure os
							LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id)
							LEFT JOIN system_security.users u ON(u.user_id = us.user_id AND u.active = 1)
							WHERE os.status = 1
							AND os.organization_id = '" . get_user_data('organization_id') . "'
							AND os.level NOT IN('L0', 'L1')
							ORDER BY os.unit_code ";
							// 					$sql = "SELECT os.organization_structure_id AS id, os.unit_name AS value, u.user_id, us.jabatan, u.user_name AS nama_pejabat, u.external_id AS nip_pejabat, u.email, dir.unit_name AS instansi, os.unit_code
							// 							  FROM system_security.users u
							// 						 LEFT JOIN system_security.users_structure us ON(u.user_id = us.user_id)
							// 						 LEFT JOIN system_security.organization_structure os ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1)
							// 							  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
							// 							WHERE os.organization_id = '" . get_user_data('organization_id') . "'";
							$result = $this->db->query($sql);
							$to = $result->row();

							// 					$distribusi['direksi'][$v] = array('user_id' => $to->user_id, 'status_proses' => 0, 'status_terima' => 0, 'jabatan' => $to->jabatan, 'unit_name' => $to->value, 'user_id' => $to->user_id, 'nama' => $to->nama_pejabat, 'email' => $to->email);
							$distribusi['direksi'][$v] = array('unit_id' => $v, 'status_proses' => 0, 'status_terima' => 0);
						}
					}

					if (isset($_POST['distribusi']['non_direksi'])) {
						foreach ($_POST['distribusi']['non_direksi'] as $k => $v) {
							$sql = "SELECT os.organization_structure_id, os.unit_code, os.unit_name, os.level, u.user_name FROM system_security.organization_structure os
						 	 LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id)
						  	 LEFT JOIN system_security.users u ON(u.user_id = us.user_id AND u.active = 1)
							 WHERE os.status = 1
							   AND os.organization_id = '" . get_user_data('organization_id') . "'
							   AND os.level NOT IN('L0', 'L1')
							ORDER BY os.unit_code ";

							// 					$sql = "SELECT os.organization_structure_id AS id, os.unit_name AS value, u.user_id, us.jabatan, u.user_name AS nama_pejabat, u.external_id AS nip_pejabat, u.email, dir.unit_name AS instansi, os.unit_code
							// 							  FROM system_security.users u
							// 						 LEFT JOIN system_security.users_structure us ON(u.user_id = us.user_id)
							// 						 LEFT JOIN system_security.organization_structure os ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1)
							// 							  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
							// 							WHERE os.organization_id = '" . get_user_data('organization_id') . "'";
							// 					$result = $this->db->query($sql);
							$to = $result->row();

							// 					$distribusi['non_direksi'][$v] = array('user_id' => $to->user_id, 'status_proses' => 0, 'status_terima' => 0, 'jabatan' => $to->jabatan, 'unit_name' => $to->value, 'user_id' => $to->user_id, 'nama' => $to->nama_pejabat, 'email' => $to->email);
							$distribusi['non_direksi'][$v] = array('unit_id' => $v, 'status_proses' => 0, 'status_terima' => 0);
						}
					}

					$data['distribusi'] = json_encode($distribusi);
				}

				$data['distribusi_tujuan'] = json_encode($_POST['distribusi_tujuan']);

				$this->db->update('surat', $data, array('surat_id' => $surat_id));

				if (isset($_POST['attachment'])) {
					$config_file['upload_path'] = 'assets/media/doc/';
					$config_file['encrypt_name'] = TRUE;
					$config_file['max_size'] = 20000;
					$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar|doc|docx|xls|xlsx';
					$this->load->library('upload', $config_file);

					$i = 0;
					foreach ($_POST['attachment'] as $k => $v) {
						switch ($v['state']) {
							case 'insert':
								if (!empty($_FILES['attachment_file_' . $k]['name'])) {
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
							case 'delete':
								unlink(str_replace('/lx_media/', 'assets/media/', $v['file']));
								$this->db->delete('file_attachment', array('file_attachment_id' => $v['id']));
								break;
							default:
								$file_attached = array();
								if (!empty($_FILES['attachment_file_' . $k]['name'])) {
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
				/*			
			$list = $this->db->get_where('konsep_surat', array('table' => $table, 'ref_id' => $ref_id, 'status' => 1));
			if($list->num_rows() > 0) {
				$result = $this->db->get_where('system_security.format_surat', array('status' => 1, 'function_ref_id' => $_POST['function_ref_id']));
				$format = $result->row();
				
				$_POST['ref_id'] = $surat_id;
				$_POST['table'] = 'surat';
				$_POST['format_surat_text'] = $format->format_title;
//				$_POST['format_surat_id'] = ;
				
				$this->add_konsep();
			}
*/
				set_success_message('Data surat berhasil perbaharui.');
				redirect($_POST['return'] . $surat_id);
				exit;
			} else {
				set_error_message('data Surat tidak dikenali.');
			}
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 * 
	 */
	function return_data()
	{
		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");

		$sql = "UPDATE surat 
					   SET kirim_time = NULL, status = (CASE WHEN status = 0 THEN 0 ELSE status - 1 END) 
					WHERE surat_id = '" . $_POST['ref_id'] . "'";
		$this->db->query($sql);

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = -1;  //return
		$data['table'] = 'surat';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle FROM surat s
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
				WHERE s.surat_id = '" . $_POST['ref_id'] .  "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();

		$jenis_agenda = ($surat->jenis_agenda == 'M') ? 'Masuk' : 'Keluar';

		if ($surat->surat_id == $_POST['ref_id']) {
			$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => get_user_id()));
		}

		$subject = "Notifikasi Proses surat Internal - " . $surat->agenda_id;
		$body = 'Surat Internal - ' . $surat->agenda_id . ' dikembalikan ke proses ' . $surat->process_title;

		$sql1 = "UPDATE ekspedisi SET penerima_time = NULL, petugas_penerima = '' 
			WHERE ekspedisi_id = '" . $surat->surat_pengantar_id . "'";
		$this->db->query($sql1);

		$distribusi = json_decode($surat->distribusi, TRUE);
		foreach ($distribusi as $dis_key => $dis_val) {
			foreach ($dis_val as $k => $v) {
				if ($surat->function_ref_id != 3) {
					$list_distribusi = user_in_unit($v["unit_id"]);
				} else {
					$list_distribusi = '';
				}

				if ($surat->function_ref_id != 3) {
					foreach ($list_distribusi->result() as $row_distribusi) {
						$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => $row_distribusi->user_id));
					}
				}
			}
		}

		$list_tujuan = user_in_unit($surat->surat_to_ref_id);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => $row_tujuan->user_id));

			// 			echo $this->db->last_query();
			//			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id),  'note' => $body, 'detail_link' => ($detail_link  . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));

			$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}

		set_success_message('Berkas berhasil dikembalikan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}



	/**
	 * 
	 */

	function get_konsep_surat($table, $ref_id, $active = FALSE)
	{
		if (!$active) {
			$this->db->order_by('version', 'DESC');
			return $this->db->get_where('konsep_surat', array('table' => $table, 'ref_id' => $ref_id));
		} else {
			return $this->db->get_where('konsep_surat', array('table' => $table, 'ref_id' => $ref_id, 'status' => 1));
		}
	}

	function post_test()
	{
		$surat_id = $_POST['ref_id'];
		$proses_url = 'surat/external/outgoing_view/' . $surat_id;
		$url = "http://13.212.156.178:8888/adapter/pdfsigning/rest/docSigningZ";
		// $url = "http://10.201.6.87:8888/adapter/pdfsigning/rest/docSigningZ";
		$path = "./barcode";
		$path_db = "/data/sharefolder";
		$path_esign = "/sharefolder";
		// $this->db->query("TRUNCATE TABLE public.surat_sign");
		// $test = $this->db->query("SELECT * FROM public.surat_sign")->result_array();
		// echo json_encode($test);
		// die;
		$angka_random = rand(10, 100);
		$date = date('YmdHis');
		// INISIALISASI UPLOAD FILE MENTAH PDF
		$result = $this->get_surat($surat_id);
		if ($result->num_rows() == 0) {
			set_error_message('Data tidak dikenali.');
			redirect('surat/external/outgoing/');
			exit;
		}
		$surat = $result->row();

		$result = $this->get_konsep_surat('surat', $surat_id, TRUE);
		$konsep = $result->row();
		$this->load->library('m_pdf');

		$html = '<style>' . file_get_contents('assets/css/wysiwyg.css') . '</style>'
			. $konsep->konsep_text;
		$this->m_pdf->pdf->WriteHTML($html);

		$print_path =  "{$path}/surat_{$surat_id}_{$angka_random}_{$date}.pdf";
		$this->m_pdf->pdf->Output($print_path, 'F');
		if (file_exists("{$path}/surat_{$surat_id}_{$angka_random}_{$date}.pdf")) {
			if ($_POST['function_ref_id'] == 2) {
				// UPLOAD QR CODE UNTUK PDF
				$this->load->library('ciqrcode'); //pemanggilan library QR CODE

				$config['cacheable'] = true; //boolean, the default is true
				$config['cachedir'] = "{$path}/cache"; //string, the default is application/cache/
				$config['errorlog'] = "{$path}/log"; //string, the default is application/logs/
				$config['imagedir'] =  "{$path}/"; //direktori penyimpanan qr code
				$config['quality'] = true; //boolean, the default is true
				$config['size'] = '1024'; //interger, the default is 1024
				$config['black'] = array(224, 255, 255); // array, default is array(255,255,255)
				$config['white'] = array(70, 130, 180); // array, default is array(0,0,0)
				$config['label'] = $surat_id;
				$this->ciqrcode->initialize($config);
				$image_name =  "qr_surat_{$surat_id}_{$angka_random}_{$date}.png"; //buat name dari qr code sesuai dengan nim
				$params['data'] = $surat_id; //data yang akan di jadikan QR CODE
				$params['level'] = 'H'; //H=High
				$params['size'] = 10;
				$params['savename'] = $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
				$this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
				if (file_exists("{$path}/qr_surat_{$surat_id}_{$angka_random}_{$date}.png")) {
					$return = array('error' => '', 'message' => 'DATA QR CODE TERSEDIA', 'execute' => "");


					// SAVE KE TABLE
					$data_sign = [
						'surat_id'		=> $surat_id,
						'dest'			=>  "{$path_db}/SIGNED_surat_{$surat_id}_{$angka_random}_{$date}.pdf",
						'spesimenpath'  =>  "{$path_db}/qr_surat_{$surat_id}_{$angka_random}_{$date}.png",
						'src'			=>  "{$path_db}/surat_{$surat_id}_{$angka_random}_{$date}.pdf"
						// 'create_date'	=> date('Y-m-d H:i:s')
					];
					$this->db->insert('surat_sign', $data_sign);
					if ($this->db->affected_rows() == 1) {
						//CREATE E-SIGN DIGITAL
						$data = [
							"certificatelevel" => "NOT_CERTIFIED",
							"dest" =>  "{$path_esign}/SIGNED_surat_{$surat_id}_{$angka_random}_{$date}.pdf",
							"docpass" => "",
							"jwToken" => "",
							"location" => "JAKARTA",
							"profileName" => "20220907KeysignDemoSystem46",
							"reason" => "QR12345",
							"refToken" => "",
							"spesimenPath" =>  "{$path_esign}/qr_surat_{$surat_id}_{$angka_random}_{$date}.png",
							"src" =>  "{$path_esign}/surat_{$surat_id}_{$angka_random}_{$date}.pdf",
							"visLLX" => 100,
							"visLLY" => 10,
							"visSignaturePage" => 1,
							"visURX" => 201,
							"visURY" => 108,
						];

						$data = json_encode($data);
						$response = xrequestwithdata($url, $data, 'post');
						$data_result = json_decode($response, true);

						if ($data_result['status'] == True) {
							$return = array('error' => '', 'message' => 'DATA BERHASIL DI CREATE DI DATABASE DAN QR SIGN!', 'execute' => "location.assign('" . site_url($proses_url) . "')");
						} else {
							$return = array('error' => '', 'message' => "{$data_result['errorMessage']}", 'execute' => "location.reload()");
						}
					} else {
						$return = array('error' => '', 'message' => 'DATA GAGAL DI SIMPAN', 'execute' => "location.reload()");
					}
				} else {
					$return = array('error' => '', 'message' => 'DATA QR CODE TIDAK TERSEDIA', 'execute' => "location.reload()");
				}
			} else {
				$return = array('error' => '', 'message' => 'DATA TIDAK SESUAI', 'execute' => "location.reload()");
			}
		} else {
			$return = array('error' => '', 'message' => "DATA FILE PADA PATH <strong>{$path}/surat_{$surat_id}_{$angka_random}_{$date}.pdf</strong> TIDAK ADA", 'execute' => "location.reload()");
		}
		$sql = "UPDATE surat
		SET status = (CASE WHEN status = " . $_POST['last_flow'] . " THEN " . $_POST['last_flow']  . " - 1 ELSE status + 1 END)
		  WHERE surat_id = '" . $_POST['ref_id'] . "'";

		$this->db->query($sql);

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1;  //forward
		$data['table'] = 'surat';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.position_handle FROM surat s
		 JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
		 WHERE s.surat_id = '" . $_POST['ref_id'] . "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();

		if ($_POST['function_handler'] != '-') {
			$this->$_POST['function_handler']($surat);
		}

		set_success_message('Proses berkas berhasil dilanjutkan.');

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	function proses_data()
	{
		if ($_POST['function_ref_id'] == 2) {
			$proses_url = 'surat/external/outgoing_view/' . $_POST['ref_id'];
			$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url($proses_url) . "')");
		} else {
			$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");
		}

		$sql = "UPDATE surat
				   SET status = (CASE WHEN status = " . $_POST['last_flow'] . " THEN " . $_POST['last_flow']  . " - 1 ELSE status + 1 END)
				 WHERE surat_id = '" . $_POST['ref_id'] . "'";

		$this->db->query($sql);

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1;  //forward
		$data['table'] = 'surat';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.position_handle FROM surat s
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
				WHERE s.surat_id = '" . $_POST['ref_id'] . "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();

		if ($_POST['function_handler'] != '-') {
			$this->$_POST['function_handler']($surat);
		}

		set_success_message('Proses berkas berhasil dilanjutkan.');

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	function proses_keluar_data()
	{
		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");

		$sql = "UPDATE surat
				   SET status = (CASE WHEN status = " . $_POST['last_flow'] . " THEN " . $_POST['last_flow'] . " ELSE status + 1 END)
				 WHERE surat_id = '" . $_POST['ref_id'] . "'";
		$this->db->query($sql);

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1;  //forward
		$data['table'] = 'surat';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle FROM surat s
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
				WHERE s.surat_id = '" . $_POST['ref_id'] . "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();

		$jenis_agenda = 'Keluar';
		// 		$return['execute'] = "location.assign('" . site_url('surat/external/outgoing_view/' . $_POST['ref_id']) . "')";

		$subject = "Notifikasi Proses Surat Keluar Eksternal - " . $surat->jenis_agenda . '-' . $surat->agenda_id;
		$body = 'Surat ' . $jenis_agenda . ' Eksternal ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' telah memasuki proses ' . $surat->process_title;

		$list_tujuan = user_with_permission($surat->permission_handle);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => $row_tujuan->user_id));
			$this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));

			$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());

			// 		$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id']));
			// 		$list = user_with_permission(8);
			// 		foreach ($list->result() as $row) {
			// 			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id), 'note' => ('Surat telah memasuki proses ' . $surat->process_title), 'detail_link' => ('surat/external/incoming_view/' . $data['ref_id']), 'notify_user_id' => $row->user_id, 'status' => $surat->status, 'read' => 0));

			// 			$this->_send_mail_notification($row->email, $subject, $body, array());
		}

		set_success_message('Proses berkas berhasil dilanjutkan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * 
	 */
	function init_distribusi_sme($surat)
	{
		$subject = "Notifikasi Proses " . $surat->jenis_agenda . "-" . $surat->agenda_id;

		$body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' telah memasuki proses ' . $surat->process_title;

		// $list_tujuan = user_with_permission($surat->permission_handle);
		$list_tujuan = user_in_unit($surat->surat_from_ref_id);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/incoming_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
			// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}
	}

	/**
	 * 
	 */
	function init_kirim_sme($surat)
	{

		$subject = "Notifikasi Proses " . $surat->jenis_agenda . "-" . $surat->agenda_id;
		//$subject = $_POST['function_ref_name'] . ' ' . $surat->jenis_agenda . " - " . $surat->agenda_id . ' ' $surat->surat_perihal;

		$body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . ' - ' . $surat->agenda_id . ' telah memasuki proses ' . $surat->process_title;

		//$list_tujuan = user_with_permission($surat->permission_handle);
		$list_tujuan = user_with_permission(7);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/incoming_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));

			// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}
	}

	/**
	 *
	 */
	function init_draft_ske($surat)
	{
		$approval = json_decode($surat->approval, TRUE);

		if (isset($approval['non_direksi'])) {

			$subject = "Notifikasi Proses Draft Surat Keluar Eksternal ";
			$body = 'Surat Keluar Eksternal dari ' . get_user_data('unit_name') . ' telah memasuki proses Draft';

			foreach ($approval['non_direksi'] as $row_tujuan) {
				if (isset($row_tujuan['unit_name'])) {
					$this->db->insert('notify', array('function_ref_id' => 2, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan['user_id'], 'read' => 0));

					$this->_send_mail_notification($row_tujuan['email'], $subject, $body, array());
				}
			}
		} else {

			$sql = "UPDATE surat
				   	SET status = (CASE WHEN status = " . $_POST['last_flow'] . " THEN " . $_POST['last_flow'] . " ELSE status + 1 END)
				 	WHERE surat_id = '" . $_POST['ref_id'] . "'";
			$this->db->query($sql);

			$subject = "Notifikasi Proses Verifikasi Surat Keluar Eksternal ";
			$body = 'Surat Keluar Eksternal dari ' . get_user_data('unit_name') . ' telah memasuki proses Verifikasi';

			foreach ($approval['direksi'] as $row_tujuan) {
				if (isset($row_tujuan['unit_name'])) {
					$this->db->insert('notify', array('function_ref_id' => 2, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan['user_id'], 'read' => 0));

					$this->_send_mail_notification($row_tujuan['email'], $subject, $body, array());
				}
			}
		}
	}

	/**
	 *
	 */
	function init_verifikasi_dir_ske($surat)
	{
		$approval 		= json_decode($surat->approval, TRUE);
		$signed		 	= json_decode($surat->signed, TRUE);
		$signed_user 	= $signed['user_id'];

		if (isset($approval['direksi'])) {

			$subject = "Notifikasi Proses Verifikasi Surat Keluar Eksternal ";
			$body = 'Surat Keluar Eksternal dari ' . get_user_data('unit_name') . ' telah memasuki proses Verifikasi';

			foreach ($approval['direksi'] as $row_tujuan) {
				if (isset($row_tujuan['unit_name'])) {
					$approval_user = $row_tujuan['user_id'];

					$this->db->insert('notify', array('function_ref_id' => 2, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan['user_id'], 'read' => 0));

					$this->_send_mail_notification($row_tujuan['email'], $subject, $body, array());
				}
			}

			if (isset($approval_user)) {
				if ($signed_user !== $approval_user) {
					$this->db->insert('notify', array('function_ref_id' => 2, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat->surat_id), 'notify_user_id' => $signed_user, 'read' => 0));
				}
			}
		} else {
			// --
		}
	}

	/**
	 *
	 */
	function init_verifikasi_tu_ske($surat)
	{
		$subject = "Notifikasi Proses Verifikasi Surat Keluar Eksternal ";
		$body = 'Surat Keluar Eksternal dari ' . get_user_data('unit_name') . ' telah memasuki proses Verifikasi';

		$list_tujuan = user_with_permission($surat->permission_handle);
		foreach ($list_tujuan->result() as $row_tujuan) {
			if ($surat->created_id != $row_tujuan->user_id) {
				$this->db->insert('notify', array('function_ref_id' => 2, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/external/outgoing_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));

				$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
			}
		}
	}

	/**
	 *
	 */
	function init_draft_si($surat)
	{
		$approval = json_decode($surat->approval, TRUE);
		if (isset($approval['direksi'])) {

			$subject = "Notifikasi Proses Draft Surat Internal ";
			$body = 'Surat Internal dari ' . get_user_data('unit_name') . ' telah memasuki proses Draft';

			foreach ($approval['direksi'] as $row_tujuan) {
				if (isset($row_tujuan['unit_name'])) {
					$this->db->insert('notify', array('function_ref_id' => 3, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/internal/sheet_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan['user_id'], 'read' => 0));

					$this->_send_mail_notification($row_tujuan['email'], $subject, $body, array());
				}
			}
		}
	}

	/**
	 *
	 */
	function add_konsep()
	{
		$sql = "SELECT MAX(ks.version) last_version FROM konsep_surat ks
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		if ($result->num_rows() == 0) {
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

		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		$param = $result->row_array();
		$param['sifat_surat'] = humanize($param['sifat_surat']);
		$param['surat_awal'] = db_to_human($param['surat_awal']);

		$result = $this->db->get_where('system_security.organization_structure', array('organization_structure_id' => $param['surat_from_ref_id']));
		$unit = $result->row();

		if ($param['function_ref_id'] == 2) {
			$format_no_surat = 'RSUD-BLJ';
		} else if ($param['function_ref_id'] == 3) {
			$format_no_surat = 'NOTA DINAS';
		} else if ($param['function_ref_id'] == 13) {
			$format_no_surat = 'SURAT PERINTAH';
		}

		$param['surat_no'] = trim($param['kode_klasifikasi_arsip']) . '/_/' . $format_no_surat;

		$surat_to_ref_data = json_decode($param['surat_to_ref_data'], TRUE);
		if ($param['function_ref_id'] == 2) {
			$param['surat_to_ref_data|title'] = $surat_to_ref_data['title'];
			$param['surat_to_ref_data|nama'] = $surat_to_ref_data['nama'];
			$param['surat_to_ref_data|instansi'] = $surat_to_ref_data['instansi'];
			$param['surat_to_ref_data|alamat'] = $surat_to_ref_data['alamat'];
		} else {
			$param['surat_to_ref_data|jabatan'] = $surat_to_ref_data['jabatan'];
			$param['surat_to_ref_data|unit'] = $surat_to_ref_data['unit'];
			$param['surat_to_ref_data|nama'] = $surat_to_ref_data['nama'];
			$param['surat_to_ref_data|kode'] = $surat_to_ref_data['kode'];
			$param['surat_to_ref_data|dir'] = $surat_to_ref_data['dir'];
		}

		$p = '';
		$i = 0;
		foreach (json_decode($param['tembusan']) as $tembusan) {
			$i++;
			if ($tembusan != '') {
				$p .= $i . '. ' . $tembusan . '<br>';
			}
		}

		if ($p != '') {
			$param['tembusan1'] = $p;
			$param['tembusan2'] = 'Tembusan : <br>' . $p;
		} else {
			$param['tembusan1'] = '';
			$param['tembusan2'] = '';
		}

		$signed = json_decode($param['signed'], TRUE);
		$param['signed|unit_id'] = $signed['unit_id'];
		$param['signed|unit_name'] = $signed['unit_name'];
		$param['signed|jabatan'] = $signed['jabatan'];
		$param['signed|pangkat'] = $signed['pangkat'];
		$param['signed|nama_pejabat'] = $signed['nama_pejabat'];
		$param['signed|nip'] = $signed['nip'];

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
	function save_konsep()
	{
		$konsep = array();
		$konsep['organization_id'] = get_user_data('organization_id');
		$konsep['table'] = $_POST['table'];
		$konsep['ref_id'] = $_POST['ref_id'];
		$konsep['format_surat_id'] = $_POST['format_surat_id'];
		$konsep['title'] = $_POST['format_surat_text'];
		$konsep['konsep_text'] = $_POST['konsep_text'];

		if ($_POST['konsep_surat_id'] == '0') {
			$this->db->insert('konsep_surat', $konsep);

			/*
			$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
			$surat= $result->row();
			$distribusi = json_decode($surat->distribusi, TRUE);
			
			$distribusi['konsep_surat'] = $_POST['konsep_text'];
			$this->db->update('surat', array('distribusi' => $distribusi), array('surat_id' => $_POST['ref_id']));
			*/

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
	function save_konsep_as()
	{
		$sql = "SELECT MAX(ks.version) last_version FROM konsep_surat ks
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		if ($result->num_rows() == 0) {
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
	function remove_konsep()
	{
		$konsep_surat_id = $_POST['konsep_surat_id'];

		$this->db->delete('konsep_surat', array('konsep_surat_id' => $konsep_surat_id));

		$this->db->update('konsep_surat', array('status' => 0), array('table' => $_POST['table'], 'ref_id' => $_POST['ref_id']));
		$sql = "SELECT MAX(ks.version) last_version FROM konsep_surat ks
				WHERE ks.table = '" . $_POST['table'] . "' AND ks.ref_id = '" . $_POST['ref_id'] . "'";
		$result = $this->db->query($sql);
		if ($result->num_rows() > 0) {
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

		$return = array('error' => '', 'message' => 'Konsep berhasil dihapus.', 'execute' => '', 'new_option' => $opt);

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 *
	 */
	function set_diskusi()
	{
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		if ($result->num_rows() > 0) {
			$surat = $result->row();
			$approval = json_decode($surat->approval, TRUE);
			if ($approval[$_POST['distribusi_id']]['status'] == 0) {

				if (isset($approval[$_POST['distribusi_id']]['diskusi']) && !is_array($approval[$_POST['distribusi_id']]['diskusi'])) {
					$approval[$_POST['distribusi_id']]['diskusi'] = array();
				}

				$return = array('error' => 0, 'message' => 'Komentar berhasil ditambahkan.', 'execute' => "");
				$approval[$_POST['distribusi_id']]['diskusi'][date('d-m-Y H:i:s')] = array('user_id' => $_POST['user_id'], 'name' => $_POST['name'], 'profile_pic' => $_POST['profile_pic'], 'text' => $_POST['text']);

				$approval = json_encode($approval);
				$this->db->update('surat', array('approval' => $approval), array('surat_id' => $_POST['ref_id']));
			} else {
				$return = array('error' => 2, 'message' => 'Approval telah selesai.', 'execute' => "displaySelesai('" . $_POST['distribusi_id'] . "')");
			}
		} else {
			$return = array('error' => 1, 'message' => 'data tidak dikenali.');
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 *
	 */
	function set_approve()
	{
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		$surat_link = '';
		if ($result->num_rows() > 0) {
			$surat = $result->row();
			$approval = json_decode($surat->approval, TRUE);
			if ($approval[$_POST['distribusi_id']]['status'] == 0) {

				$approval[$_POST['distribusi_id']][$_POST['unit_id']]['status'] = $_POST['approval'];
				$a = 0;
				foreach ($approval[$_POST['distribusi_id']] as $uk => $uv) {
					if (!in_array($uk, array('status', 'diskusi'))) {
						$a += $uv["status"];
					}
				}

				if ((count($approval[$_POST['distribusi_id']]) - $a) == $a) {
					$approval[$_POST['distribusi_id']]['status'] = 1;
				} else {
					$approval[$_POST['distribusi_id']]['status'] = 0;
				}

				$approval = json_encode($approval);

				$msg = $_POST['approval'] == 1 ? ($surat->jenis_agenda . ' Sudah anda setujui.') : ($surat->jenis_agenda . ' Batal anda setujui.');
				$this->db->update('surat', array('approval' => $approval), array('surat_id' => $_POST['ref_id']));

				if ($_POST['function_ref_id'] == 2) {
					$surat_link = 'surat/eksternal/outgoing_view/' . $_POST['ref_id'];
				} else if ($_POST['function_ref_id'] == 3) {
					$surat_link = 'surat/internal/sheet_view/' . $_POST['ref_id'];
				} else if ($_POST['function_ref_id'] == 13) {
					$surat_link = 'surat/tugas/tugas/' . $_POST['ref_id'];
				}

				$return = array('error' => 0, 'message' => $msg, 'execute' => $surat_link);
			} else {
				$return = array('error' => 2, 'message' => 'Proses persetujuan telah selesai.', 'execute' => "displaySelesai('" . $_POST['distribusi_id'] . "')");
			}
		} else {
			$return = array('error' => 1, 'message' => 'data tidak dikenali.');
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 *
	 */
	function konsep_distribusi_si($surat)
	{

		$subject = "Notifikasi Proses Nota Dinas " . $surat->jenis_agenda . " - " . $surat->agenda_id;
		$body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . ' - ' . $surat->agenda_id . ' telah memasuki proses ' . $surat->process_title;

		$list_tujuan = user_with_permission($surat->permission_handle);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/internal/sheet_view/internal/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'status' => 0, 'read' => 0));

			$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}
	}

	function init_kirim_si($surat)
	{
		$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
		$distribusi_tembusan = json_decode($surat->distribusi, TRUE);
		$note = $surat->jenis_agenda . ' - Tembusan';

		if ($distribusi_tembusan != '' || isset($distribusi_tembusan)) {
			foreach ($distribusi_tembusan as $tujuan) {
				if ($tujuan['id'] != '') {
					$list_tujuan = user_in_unit($tujuan['id']);
					foreach ($list_tujuan->result() as $row_tujuan) {
						$this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . ' - ' . $surat->agenda_id), 'note' => $note, 'detail_link' => ('surat/internal/sheet_view/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
					}
				}
			}

			$this->db->insert('distribusi_tembusan', array('created_id' => get_user_id(), 'created_time' => date('Y-m-d H:i:s'), 'function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'distribusi' => $surat->distribusi));
		}

		$distribusi[] = array($surat->surat_to_ref_id => $surat_to_ref_data);
		$data = array();
		$data['distribusi'] = json_encode($distribusi);
		if ($surat->surat_from_ref != '2') {
			$surat_to_ref_data['unit_id'] = $surat->surat_to_ref_id;
		}
		$data['distribusi_tujuan'] = json_encode($surat_to_ref_data);

		$this->db->update('surat', $data, array('surat_id' => $surat->surat_id));
	}

	function init_terima_si($surat)
	{
		$this->db->update('surat', array('terima_time' => date('Y-m-d H:i:s')), array('surat_id' => $surat->surat_id));
		$this->db->update('ekspedisi', array('penerima_time' => date('Y-m-d H:i:s'), 'status' => 1, 'petugas_penerima' => get_user_data('user_name')), array('ekspedisi_id' => $surat->surat_pengantar_id));
	}

	function kirim_surat_internal($surat)
	{
		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('global/dashboard') . "')");

		// 		$this->db->update('ekspedisi', array('status' => 0, 'pengiriman_time' => date('Y-m-d H:i:s')), array('ekspedisi_id' => $_POST['ref_id']));

		// 		$sql = "SELECT * FROM ekspedisi WHERE status >= 0 AND ekspedisi_id = '" . $_POST['ref_id'] . "'";
		// 		$result = $this->db->query($sql);
		// 		$ekspedisi = $result->row();

		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.function_ref_id = " . $_POST['function_ref_id'];
		$result = $this->db->query($sql);
		$function_ref = $result->row();

		$sql = "SELECT * FROM surat WHERE surat_pengantar_id = '" . $_POST['ref_id'] . "' ";
		$list = $this->db->query($sql);
		$row_surat = $list->row();

		$subject = $row_surat->jenis_agenda . '-' . $row_surat->agenda_id . ' - Pengiriman Surat - ' . $row_surat->surat_perihal;
		$body = 'Anda menerima nota dinas dalam daftar kerja anda.<br>' .
			// 		'<strong>Catatan : </strong><br>' . $ekspedisi->catatan_pengirim .
			'<br><strong>Petugas Pengirim : </strong><br>' . $ekspedisi->petugas_pengirim . '<br>' .
			'<br><strong>Identitas Surat </strong><br>';

		$list_tujuan = user_in_unit($surat->surat_to_ref_id);

		$n = 1;
		foreach ($list->result() as $row) {
			$surat_from_ref_data = json_decode($row->surat_from_ref_data, TRUE);

			$sql = "UPDATE surat
 					   SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 1 END),
 						   kirim_time = '" . date('Y-m-d H:i:s') . "'
					 WHERE surat_id = '" . $row->surat_id . "'";
			$this->db->query($sql);

			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1;   // forward
			$data['table'] = 'surat';
			$data['ref_id'] = $row->surat_id;
			$data['flow_seq'] = $row->status + 1;
			$data['note'] = $ekspedisi->catatan_pengirim;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);

			$body .= $n . '. ' . $row->jenis_agenda . '-' . $row->agenda_id . ' : ' . $row->surat_perihal . ' dari ' . $row->surat_from_ref . '.<br>';

			$n++;

			switch ($row->jenis_agenda) {
				case 'SI':
					$detail_link = 'surat/internal/sheet_view/';
					break;
				default:  // SME
					$detail_link = 'surat/external/incoming_view/';
					break;
			}

			// $dist = json_decode($row->distribusi);
			// foreach($dist as $row_dist) {
			// $list_tujuan = user_in_unit($row_dist);

			// set workspace untuk Penerima (link surat pengantar)
			$note = $_POST['title'] . ' No. ' . $row->surat_no . ' dari ' . $surat_from_ref_data['nama'] . ' &nbsp; | &nbsp; ' . $surat_from_ref_data['title'] . ' &nbsp; | &nbsp; ' .  $surat_from_ref_data['instansi'];

			foreach ($list_tujuan->result() as $row_tujuan) {
				$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_id, 'agenda' => ($row->jenis_agenda . ' - ' . $row->agenda_id), 'note' => $note, 'detail_link' => ($detail_link . $row->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
			}

			// 			$distribusi = json_decode($row->distribusi, TRUE);
			// 			foreach ($distribusi as $dis_key => $dis_val) {
			// 				foreach ($distribusi as $dis_val => $v) {
			// 					foreach($v as $unit) {
			// 						$list_distribusi = user_in_unit($unit['unit_id']);
			// 						foreach ($list_distribusi->result() as $row_distribusi) {
			// 							$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_id, 'agenda' => ($row->jenis_agenda . ' - ' . $row->agenda_id), 'note' => $note, 'detail_link' => ($detail_link . $row->surat_id), 'notify_user_id' => $row_distribusi->user_id, 'read' => 0));
			// 						}
			// 					}
			// 				}
			// 			}			
		}

		foreach ($list_tujuan->result() as $row) {
			$this->_send_mail_notification($row->email, $subject, $body, array());
		}

		set_success_message('Surat berhasil dikirim.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}


	/**
	 * 
	 *
	 */
	function init_terima_sme($surat)
	{
		$subject = "Notifikasi Proses " . $surat->jenis_agenda . "-" . $surat->agenda_id;

		$body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . ' - ' . $surat->agenda_id . ' telah diterima oleh ' . get_user_data('user_name');


		$result = $this->db->get_where('ekspedisi', array('ekspedisi_id' => $surat->surat_pengantar_id));
		if ($result->num_rows() > 0) {
			$ekspedisi = $result->row();
			$penerima = json_decode($ekspedisi->petugas_penerima, TRUE);

			$penerima[get_user_data('unit_id')] = array();
			$penerima[get_user_data('unit_id')]['petugas'] = $_POST['penerima'];
			$penerima = json_encode($penerima);

			$this->db->update('ekspedisi', array('penerima_time' => date('Y-m-d H:i:s'), 'petugas_penerima' => $penerima, 'status' => 1), array('ekspedisi_id' => $surat->surat_pengantar_id));
		}

		$list_tujuan = user_with_permission(7);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->update('notify', array('note' => $body, 'read' => 0), array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'notify_user_id' => $row_tujuan->user_id));

			$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}
	}

	/**
	 * 
	 *
	 */
	function simpan_klasifikasi($surat)
	{
		if ($this->_validate_post_data($this->data_object->surat_eksternal, 'edit') != FALSE) {
			$this->db->update('surat', array('kode_klasifikasi_arsip' => $_POST['kode_klasifikasi_arsip']), array('surat_id' => $surat->surat_id));
		}
	}


	//buat send data menjadi arsip. bukan save buat arsip
	function init_arsip_sme($surat)
	{
		//		var_dump($_POST);
		//		exit;
		if (isset($_POST['kode_klasifikasi_arsip']) && $_POST['kode_klasifikasi_arsip'] != '') {
			if ($this->_validate_post_data($_POST['kode_klasifikasi_arsip'], 'edit') != FALSE) {
				if ($surat->status != 99) {
					$this->db->update('surat', array('status' => 99, 'arsip_time' => date('Y-m-d H:i:s'), 'kode_klasifikasi_arsip' => $_POST['kode_klasifikasi_arsip']), array('surat_id' => $surat->surat_id));
				}
				$subject = "Notifikasi Proses " . $surat->jenis_agenda . " - " . $surat->agenda_id;
				$body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . ' - ' . $surat->agenda_id . ' telah disimpan sebagai Arsip oleh ' . get_user_data('user_name');

				$list_tujuan = user_with_permission($surat->permission_handle);
				foreach ($list_tujuan->result() as $row_tujuan) {
					$this->db->update('notify', array('note' => $body, 'read' => 0), array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'notify_user_id' => $row_tujuan->user_id));
					// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
				}

				set_success_message('Surat disimpan sebagai arsip.');
				redirect('global/dashboard');
				exit;
			}
		}
	}

	//buat send data menjadi arsip. bukan save buat arsip
	function init_arsip_si($surat)
	{
		$_POST['function_ref_name'] = 'Surat Internal';
		if ($surat->status != 99) {
			$this->db->update('surat', array('status' => 99, 'arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $surat->surat_id));
		}
		$subject = "Notifikasi Proses " . $surat->jenis_agenda . " - " . $surat->agenda_id;
		$body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . ' - ' . $surat->agenda_id . ' telah disimpan sebagai Arsip oleh ' . get_user_data('user_name');

		$list_tujuan = user_with_permission(21);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->update('notify', array('note' => $body, 'read' => 0), array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'notify_user_id' => $row_tujuan->user_id));
			// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}

		set_success_message('Surat disimpan sebagai arsip.');
		redirect('global/dashboard');
		exit;
	}

	function init_arsip_ske($surat)
	{

		$subject = "Notifikasi Proses Surat Keluar Eksternal - " . $surat->jenis_agenda . '-' . $surat->agenda_id;
		$body = 'Surat Keluar Eksternal ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' telah disimpan sebagai Arsip ';

		$this->db->update('surat', array('status' => 99, 'arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $surat->surat_id));

		//$list_tujuan = user_with_permission($surat_eksternal->permission_handle);			
		$list_tujuan = user_with_permission(14);
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->update('notify', array('note' => $body, 'read' => 0), array('function_ref_id' => 2, 'ref_id' => $surat->surat_id, 'notify_user_id' => $row_tujuan->user_id));
			// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}

		set_success_message('Surat disimpan sebagai arsip.');
		redirect('global/dashboard');
		exit;
	}

	// if($surat->status != 99) {
	// $this->db->update('surat', array('status' => 99, 'arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $surat->surat_id));
	// }
	// $subject = "Notifikasi Proses " . $surat->jenis_agenda . " - " . $surat->agenda_id;
	// $body = $_POST['function_ref_name'] . ' Nomor agenda ' . $surat->jenis_agenda . ' - ' . $surat->agenda_id . ' telah disimpan sebagai Arsip oleh ' . get_user_data('user_name');

	// $list_tujuan = user_with_permission($surat->permission_handle);
	// foreach ($list_tujuan->result() as $row_tujuan) {
	// $this->db->update('notify', array('note' => $body, 'read' => 0), array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'notify_user_id' => $row_tujuan->user_id));
	// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
	// }
	// set_success_message('Surat disimpan sebagai arsip.');

	// }

	function get_unsend()
	{
	}

	/**
	 * @param unknown $function_ref_id
	 * @param unknown $surat_id
	 * @param unknown $status
	 */
	function baca_surat($function_ref_id, $surat_id, $status)
	{

		$this->db->update('surat', array('baca_time' => date('Y-m-d H:i:s')), array('surat_id' => $surat_id));

		/*$data = array();
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['flow_seq'] = $status;
		$data['table'] = 'surat';
		$data['ref_id'] = $surat_id;
		$data['note'] = 'Surat telah dibaca';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);*/
	}

	function baca_notify($function_ref_id, $surat_id, $status, $surat_to_ref_id, $read, $notify_user_id, $notify_id, $surat_pengantar_id)
	{

		if ($read == 0 && $notify_user_id ==  get_user_id()) {

			$data = array();
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['flow_seq'] = $status;
			$data['table'] = 'surat';
			$data['ref_id'] = $surat_id;
			$data['note'] = 'Surat telah dibaca';
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);

			$this->db->update('notify', array('read' => 1), array('notify_id' => $notify_id));

			if ($surat_to_ref_id != get_user_data('unit_id')) {

				$result = $this->db->get_where('ekspedisi', array('ekspedisi_id' => $surat_pengantar_id));
				if ($result->num_rows() > 0) {
					$ekspedisi = $result->row();
					$penerima = json_decode($ekspedisi->petugas_penerima, TRUE);

					$penerima[get_user_data('unit_id')] = array();
					$penerima[get_user_data('unit_id')]['petugas'] = (get_user_data('user_name'));
					$penerima = json_encode($penerima);

					$this->db->update('ekspedisi', array('penerima_time' => date('Y-m-d H:i:s'), 'petugas_penerima' => $penerima, 'status' => 1), array('ekspedisi_id' => $surat_pengantar_id));
				}
			}
		}
	}

	/**
	 * @param unknown $jenis_agenda
	 * @param unknown $dir
	 * @param unknown $status
	 */
	function get_surat_masuk_dir($jenis_agenda, $dir, $status)
	{
		$sql = "SELECT * FROM surat WHERE status = $status AND jenis_agenda = '$jenis_agenda' AND surat_to_ref_id = '$dir' ";
		return $this->db->query($sql);
	}

	/**
	 * @param unknown $surat_id
	 */
	function get_surat_internal_list($jenis_agenda, $ref_id, $status)
	{
		$sql = "SELECT * FROM surat WHERE status = $status AND jenis_agenda = '$jenis_agenda' AND surat_id = '$ref_id' ";
		return $this->db->query($sql);
	}

	/**
	 * @param unknown $surat_pengantar_id
	 */
	function get_surat_masuk_pengantar($surat_pengantar_id)
	{
		$sql = "SELECT * FROM surat WHERE surat_pengantar_id = '$surat_pengantar_id' ";
		return $this->db->query($sql);
	}

	function get_surat_eksternal_ttd($surat_id)
	{
		return $this->db->get_where('surat_eksternal_ttd', array('surat_eksternal_id' => $surat_id));
	}

	/**
	 * @param unknown $surat_id
	 */
	function draf($surat_id)
	{

		$result = $this->db->get_where('surat', array('surat_id' => $surat_id));
		if ($result->num_rows() > 0) {
			$surat = $result->row();

			$result = $this->db->get_where('system_security.function_ref', array('function_ref_id' => $surat->function_ref_id));
			$function_ref = $result->row();

			$approval = array();
			$approval[get_user_data('unit_level')] = array();
			$approval[get_user_data('unit_level')]['name'] = trim(get_user_data('unit_code')) . ' - ' . get_user_data('unit_name');
			$approval[get_user_data('unit_level')]['status'] = FALSE;

			$sql = "UPDATE surat
					SET status = status + 1, terima_time = NOW(), approval = '" . json_encode($approval) . "' 
					WHERE surat_id = '" . $surat_id . "'";
			$this->db->query($sql);

			$data = array();
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat';
			$data['ref_id'] = $surat_id;
			$data['flow_seq'] = $surat->status + 1;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);

			$subject = 'Notifikasi Draf Surat Internal';
			$body = "Draf Surat Internal telah dibuat oleh " . get_user_data('user_name') . ", Mohon Verifikasinya.<br>";

			// set workspace 
			$this->db->delete('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat_id));

			$list_tujuan = user_in_unit(get_user_data('unit_id'));
			foreach ($list_tujuan->result() as $row) {
				$this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat_id, 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id), 'note' => $body, 'detail_link' => ('surat/internal/sheet_view/' . $function_ref->function_ref_name . '/' . $surat_id), 'notify_user_id' => $row->user_id, 'read' => 0));
				//$this->_send_mail_notification($row->email, $subject, $body, array());
			}
		} else {
			set_error_message('Data tidak dikenali.');
			return;
		}

		set_success_message('Draf telah di kirim.');
	}

	function comment_approval()
	{
		//		var_dump($_POST);
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		if ($result->num_rows() > 0) {
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

			if (isset($_POST['status']) && $_POST['status'] == 1) {
				$approval[$_POST['level']]['status'] = TRUE;
				$return['reload'] = 1;
			}
			//			var_dump($approval);

			$this->db->update('surat', array('approval' => json_encode($approval)), array('surat_id' => $_POST['ref_id']));
		} else {
			$return = array('error' => 1, 'message' => 'data tidak dikenali.');
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * @param unknown $surat_id
	 */
	function kirim_surat($surat_id)
	{
		//		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('global/dashboard') . "')");

		$result = $this->db->get_where('surat', array('surat_id' => $surat_id));
		$surat = $result->row();
		$from_ref = json_decode($surat->surat_from_ref_data, TRUE);
		$to_ref = json_decode($surat->surat_to_ref_data, TRUE);

		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE fr.function_ref_id = " . $surat->function_ref_id;
		$result = $this->db->query($sql);
		$function_ref = $result->row();

		$subject = 'Notifikasi pengiriman surat';
		$body = 'Surat Masuk Internal Baru dalam daftar kerja anda.<br>' .
			'<strong>Surat : </strong><br>';

		$list_tujuan = user_in_unit($surat->surat_to_ref_id);

		$sql = "UPDATE surat
			   	SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 1 END),
				   kirim_time = '" . date('Y-m-d H:i:s') . "',
				   terima_time = NULL
			 	WHERE surat_id = '" . $surat_id . "'";
		$this->db->query($sql);

		$data = array();
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['table'] = 'surat';
		$data['ref_id'] = $surat_id;
		$data['flow_seq'] = $surat->status + 1;
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		$body .= $surat->jenis_agenda . '-' . $surat->agenda_id . ' : Surat Internal dari ' . $from_ref['unit'] . '.<br>';

		// set workspace untuk Penerima (link surat pengantar)
		$note = 'Surat Internal No. ' . $surat->surat_no . ' dari ' . $from_ref['nama'] . ' &nbsp; | &nbsp; ' . $from_ref['jabatan'] . ' &nbsp; | &nbsp; ' .  $from_ref['dir'];
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id), 'note' => $note, 'detail_link' => ('surat/internal/sheet_view/internal/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));

			$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}

		set_success_message('Surat berhasil dikirim.');
		//		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * @param unknown $surat_id
	 */
	function terima_surat($surat_id)
	{

		$result = $this->db->get_where('surat', array('surat_id' => $surat_id));
		if ($result->num_rows() > 0) {
			$surat = $result->row();
			if ($surat->terima_time) {

				set_error_message('Surat Sudah diterima.');
				return;
			} else {

				$sql = "UPDATE surat
						SET status = status + 1, terima_time = NOW()
						WHERE surat_id = '" . $surat_id . "'";
				$this->db->query($sql);

				$data = array();
				$data['organization_id'] = get_user_data('organization_id');
				$data['flow_type'] = 1; //forward
				$data['table'] = 'surat';
				$data['ref_id'] = $surat_id;
				$data['flow_seq'] = $surat->status + 1;
				$data['user_id'] = get_user_id();
				$this->db->insert('process_notes', $data);
				/*
				$subject = 'Notifikasi Penerimaan Surat Internal';
				$body = "Surat Internal telah diterima oleh " . get_user_data('user_name') . ".<br>";
				
				$list_asal = user_in_unit($surat->surat_to_ref_id);
				// set workspace untuk Pengirim
				foreach ($list_asal->result() as $row) {
					$this->db->update('notify', array('note' => ('Surat Internal untuk ' . $surat->surat_int_unit . ' telah diterima')), array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat_id, 'notify_user_id' => $row->user_id));
					$this->_send_mail_notification($row->email, $subject, $body, array());
				}
				
				
				$list_tujuan = user_in_unit($surat->surat_to_ref_id);
				// set workspace untuk Penerima (link surat pengantar)
				foreach ($list_tujuan->result() as $row_tujuan) {
					$this->db->update('notify', array('note' => ('Surat Internal untuk ' . $surat>surat_int_unit . ' telah diterima')), array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat_id, 'notify_user_id' => $row_tujuan->user_id));
					$this->_send_mail_notification($row->email, $subject, $body, array());
					
				}
*/
			}
		} else {
			set_error_message('Data tidak dikenali.');
			return;
		}

		set_success_message('Surat telah diterima.');
		// 		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 *
	 */
	function get_current_eksternal_no()
	{
		$return = array('error' => '', 'message' => '');
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		if ($result->num_rows() > 0) {
			$surat = $result->row();
			//		$format = '[{"function":"char","value":"' . $surat->kode_klasifikasi_arsip . '/' . $surat->surat_from_ref . '/"},{"function":"annual_seq","value":"surat_keluar|0"},{"function":"char","value":"/"},{"function":"date","value":"Y"}]';
			$format = '[{"function":"char","value":"' . trim($surat->kode_klasifikasi_arsip) . '/"},{"function":"annual_seq","value":"surat_keluar|0"},{"function":"char","value":"/RSUD-BLJ"}]';

			$return['surat_no'] = $this->lx->number_generator($format);
			$return['surat_tgl'] = date('d-m-Y');
			$return['message'] = 'No. ' . $return['surat_no'] . ' Tanggal ' . $return['surat_tgl'];

			$this->db->update('surat', array('surat_no' => $return['surat_no'], 'surat_tgl' => human_to_db($return['surat_tgl'])), array('surat_id' => $_POST['ref_id']));

			$result = $this->db->get_where('konsep_surat', array('table' => 'surat', 'status' => 1, 'ref_id' => $_POST['ref_id']));
			$konsep_surat = $result->row_array();

			$konsep_surat['konsep_text'] = str_replace((trim($surat->kode_klasifikasi_arsip) . '/_/RSUD-BLJ'), $return['surat_no'], $konsep_surat['konsep_text']);
			$konsep_surat['konsep_text'] = str_replace('__-________-____', $return['surat_tgl'], $konsep_surat['konsep_text']);
			$this->db->update('konsep_surat', array('konsep_text' => $konsep_surat['konsep_text']), array('table' => 'surat', 'status' => 1, 'ref_id' => $_POST['ref_id']));
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * 
	 */
	function get_current_internal_no()
	{
		$return = array('error' => '', 'message' => '');
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		if ($result->num_rows() > 0) {
			$surat = $result->row();
			//		$format = '[{"function":"char","value":"' . $surat->kode_klasifikasi_arsip . '/' . $surat->surat_from_ref . '/"},{"function":"annual_seq","value":"surat_internal|0"},{"function":"char","value":"/"},{"function":"date","value":"Y"}]';
			$format = '[{"function":"annual_seq","value":"surat_internal|3"},{"function":"char","value":"/NOTA DINAS"}]';

			//	$return['surat_no'] = $this->lx->number_generator(get_user_data('unit_no_surat_internal'));
			$return['surat_no'] = $this->lx->number_generator($format);
			$return['surat_tgl'] = date('d-m-Y');
			$return['message'] = 'No. ' . $return['surat_no'] . ' Tanggal ' . $return['surat_tgl'];

			$this->db->update('surat', array('surat_no' => $return['surat_no'], 'surat_tgl' => human_to_db($return['surat_tgl'])), array('surat_id' => $_POST['ref_id']));

			$result = $this->db->get_where('konsep_surat', array('table' => 'surat', 'status' => 1, 'ref_id' => $_POST['ref_id']));
			$konsep_surat = $result->row_array();

			$konsep_surat['konsep_text'] = str_replace((trim($surat->kode_klasifikasi_arsip) . '/_/NOTA DINAS'), $return['surat_no'], $konsep_surat['konsep_text']);
			$konsep_surat['konsep_text'] = str_replace('__-________-____', $return['surat_tgl'], $konsep_surat['konsep_text']);
			$this->db->update('konsep_surat', array('konsep_text' => $konsep_surat['konsep_text']), array('table' => 'surat', 'status' => 1, 'ref_id' => $_POST['ref_id']));
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * 
	 */
	function get_current_tugas_no()
	{
		$return = array('error' => '', 'message' => '');
		$result = $this->db->get_where('surat', array('surat_id' => $_POST['ref_id']));
		if ($result->num_rows() > 0) {
			$surat = $result->row();
			//		$format = '[{"function":"char","value":"' . $surat->kode_klasifikasi_arsip . '/' . $surat->surat_from_ref . '/"},{"function":"annual_seq","value":"surat_internal|0"},{"function":"char","value":"/"},{"function":"date","value":"Y"}]';
			$format = '[{"function":"char","value":"' . trim($surat->kode_klasifikasi_arsip) . '/"},{"function":"annual_seq","value":"surat_tugas|0"},{"function":"char","value":"-SURAT PERINTAH"}]';

			//	$return['surat_no'] = $this->lx->number_generator(get_user_data('unit_no_surat_internal'));
			$return['surat_no'] = $this->lx->number_generator($format);
			$return['surat_tgl'] = date('d-m-Y');
			$return['message'] = 'No. ' . $return['surat_no'] . ' Tanggal ' . $return['surat_tgl'];

			$this->db->update('surat', array('surat_no' => $return['surat_no'], 'surat_tgl' => human_to_db($return['surat_tgl'])), array('surat_id' => $_POST['ref_id']));

			$result = $this->db->get_where('konsep_surat', array('table' => 'surat', 'status' => 1, 'ref_id' => $_POST['ref_id']));
			$konsep_surat = $result->row_array();

			$konsep_surat['konsep_text'] = str_replace((trim($surat->kode_klasifikasi_arsip) . '/_/SURAT PERINTAH'), $return['surat_no'], $konsep_surat['konsep_text']);
			$konsep_surat['konsep_text'] = str_replace('__-________-____', $return['surat_tgl'], $konsep_surat['konsep_text']);
			$this->db->update('konsep_surat', array('konsep_text' => $konsep_surat['konsep_text']), array('table' => 'surat', 'status' => 1, 'ref_id' => $_POST['ref_id']));
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * 
	 */
	function update_arsip_surat()
	{

		if (isset($_POST['kode_klasifikasi_arsip']) && $_POST['kode_klasifikasi_arsip'] != '') {
			$result = $this->db->get_where('surat', array('surat_id' => $_POST['surat_id']));
			if ($result->num_rows() > 0) {
				$surat = $result->row();

				$subject = "Notifikasi Proses surat - " . $surat->jenis_agenda . '-' . $surat->agenda_id;
				$body = 'Surat ' . $surat->jenis_agenda . '-' . $surat->agenda_id . ' telah disimpan sebagai Arsip ';

				if (!has_permission(7)) {

					if ($surat->status != 99) {
						$this->db->update('surat', array('kode_klasifikasi_arsip' => $_POST['kode_klasifikasi_arsip'], 'status' => 99, 'arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $_POST['surat_id']));
					}

					$data = array();
					$data['organization_structure_id'] = get_user_data('unit_id');
					$data['created_id'] = get_user_id();
					$data['ref_type'] = 'surat';
					$data['ref_id'] = $_POST['surat_id'];
					$data['status'] = 99; //arsipp
					$this->db->insert('org_struc_archive', $data);

					if (isset($_POST['attachment'])) {
						$config_file['upload_path'] = 'assets/media/doc/';
						$config_file['encrypt_name'] = TRUE;
						$config_file['max_size'] = 20000;
						$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
						$this->load->library('upload', $config_file);

						$i = 0;
						foreach ($_POST['attachment'] as $k => $v) {
							switch ($v['state']) {
								case 'insert':
									if (!empty($_FILES['attachment_file_' . $k]['name'])) {
										if (!$this->upload->do_upload('attachment_file_' . $k)) {
											set_error_message($this->upload->display_errors());
										} else {
											$file = $this->upload->data();
											$file_attached = array();
											$file_attached['organization_id'] = get_user_data('organization_id');
											$file_attached['table'] = 'surat';
											$file_attached['ref_id'] = $_POST['surat_id'];
											$file_attached['title'] = $v['title'];
											$file_attached['file_name'] = $_FILES['attachment_file_' . $k]['name'];
											$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
											$file_attached['sort'] = $i++;
											$this->db->insert('file_attachment', $file_attached);
										}
									}

									break;
								case 'delete':
									unlink(str_replace('/lx_media/', 'assets/media/', $v['file']));
									$this->db->delete('file_attachment', array('file_attachment_id' => $v['id']));

									break;
								default:
									$file_attached = array();
									if (!empty($_FILES['attachment_file_' . $k]['name'])) {
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

					$list_tujuan = user_in_unit(get_user_data('unit_id'));
					foreach ($list_tujuan->result() as $row) {
						$this->db->delete('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $_POST['surat_id'], 'notify_user_id' => $row->user_id));
						//$this->_send_mail_notification($row->email, $subject, $body, array());
					}
				} else {
					$this->db->update('surat', array('status' => 99, 'kode_klasifikasi_arsip' => $_POST['kode_klasifikasi_arsip'], 'arsip_time' => date('Y-m-d H:i:s')), array('surat_id' => $_POST['surat_id']));

					$data = array();
					$data['organization_structure_id'] = get_user_data('unit_id');
					$data['created_id'] = get_user_id();
					$data['ref_type'] = 'surat';
					$data['ref_id'] = $_POST['surat_id'];
					$data['status'] = 99;  //arsip
					$this->db->insert('org_struc_archive', $data);

					$data = array();
					$data['organization_id'] = get_user_data('organization_id');
					$data['flow_type'] = 1;  //forward
					$data['flow_seq'] = 99;  //arsip
					$data['table'] = 'surat';
					$data['ref_id'] = $_POST['surat_id'];
					$data['user_id'] = get_user_id();
					$this->db->insert('process_notes', $data);

					if (isset($_POST['attachment'])) {
						$config_file['upload_path'] = 'assets/media/doc/';
						$config_file['encrypt_name'] = TRUE;
						$config_file['max_size'] = 20000;
						$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
						$this->load->library('upload', $config_file);

						$i = 0;
						foreach ($_POST['attachment'] as $k => $v) {
							switch ($v['state']) {
								case 'insert':
									if (!empty($_FILES['attachment_file_' . $k]['name'])) {
										if (!$this->upload->do_upload('attachment_file_' . $k)) {
											set_error_message($this->upload->display_errors());
										} else {
											$file = $this->upload->data();
											$file_attached = array();
											$file_attached['organization_id'] = get_user_data('organization_id');
											$file_attached['table'] = 'surat';
											$file_attached['ref_id'] = $_POST['surat_id'];
											$file_attached['title'] = $v['title'];
											$file_attached['file_name'] = $_FILES['attachment_file_' . $k]['name'];
											$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
											$file_attached['sort'] = $i++;
											$this->db->insert('file_attachment', $file_attached);
										}
									}

									break;
								case 'delete':
									unlink(str_replace('/lx_media/', 'assets/media/', $v['file']));
									$this->db->delete('file_attachment', array('file_attachment_id' => $v['id']));

									break;
								default:
									$file_attached = array();
									if (!empty($_FILES['attachment_file_' . $k]['name'])) {
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
					}  //end attachment

					$list = user_with_permission(7);
					foreach ($list->result() as $row) {

						$this->db->delete('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $_POST['surat_id'], 'notify_user_id' => $row->user_id));
						//$this->_send_mail_notification($row->email, $subject, $body, array());

					}
				} //end permission

				redirect('global/dashboard');
				exit;
			} else {
				set_error_message('Data tidak dikenali.');
				return;
			}
			set_success_message('Surat disimpan sebagai arsip.');
		} else {
			set_warning_message('Kode arsip belum ada');

			redirect('global/dashboard');
		}
	}

	function kirim_ekspedisi()
	{
		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('global/dashboard') . "')");

		$this->db->update('surat', array('status' => +1, 'kirim_time' => date('Y-m-d H:i:s')), array('surat_id' => $_POST['ref_id']));
		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.function_ref_id = " . $_POST['function_ref_id'];
		$result = $this->db->query($sql);
		$function_ref = $result->row();

		$subject = 'Notifikasi pengiriman surat';
		$body = 'Surat Masuk Baru dalam daftar kerja anda.<br>' .
			// 				'<strong>Catatan : </strong><br>' . $pengantar->catatan_pengirim .
			'<strong>Surat : </strong><br>';
		$sql = "SELECT * FROM surat WHERE surat_id = '" . $_POST['ref_id'] . "' ";
		$list = $this->db->query($sql);
		foreach ($list->result() as $row) {
			$sql = "UPDATE surat
 					   SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 2 END),
 						   kirim_time = '" . date('Y-m-d H:i:s') . "'
					 WHERE surat_id = '" . $row->surat_id . "'";
			$this->db->query($sql);
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'surat';
			$data['function_ref_id'] = $row->function_ref_id;
			$data['ref_id'] = $row->surat_id;
			$data['flow_seq'] = $row->status + 2;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);

			$body .= $row->jenis_agenda . '-' . $row->agenda_id . ' : Surat Masuk Eksternal dari ' . $_POST['surat_ext_title'] . '.<br>';
			switch ($row->jenis_agenda) {
				case 'SI':
					$detail_link = 'surat/internal/sheet_view/';
					break;
				default: // SME
					$detail_link = 'surat/external/incoming_view/';
					break;
			}
			// set workspace untuk TU
			// 			$list = user_with_permission(8);
			// 			foreach ($list->result() as $row_noty) {
			//	$this->db->update('notify', array('note' => ('Surat Pengantar Masuk Eksternal untuk ' . $pengantar->tujuan_unit . ' telah dikirim')), array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_eksternal_id, 'notify_user_id' => $row_noty->user_id));
			// 			}

			// set workspace untuk Penerima (link surat pengantar)

			// $note = 'Surat Internal No. ' . $surat->surat_no . ' dari ' . $from_ref['nama'] . ' &nbsp; | &nbsp; ' . $from_ref['jabatan'] . ' &nbsp; | &nbsp; ' .  $from_ref['dir'];
			// foreach ($list_tujuan->result() as $row_tujuan) {
			// $this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id), 'note' => $note, 'detail_link' => ('surat/internal/sheet_view/internal/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
			// $this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
			// }

			$note = 'Surat Masuk Eksternal No. ' . $row->surat_no . ' dari ' . $_POST['surat_ext_nama'] . ' &nbsp; | &nbsp; ' .  $_POST['surat_ext_title'] . ' &nbsp; | &nbsp; ' .  $_POST['surat_ext_instansi'];
			//			$list_tujuan = user_in_unit(get_user_data('unit_id'));
			$list_tujuan = user_in_unit($row->surat_to_ref_id);
			foreach ($list_tujuan->result() as $row_tujuan) {
				$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => $row_tujuan->user_id));
				// $this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' => $surat->surat_id, 'agenda' => ($surat->jenis_agenda . '-' . $surat->agenda_id), 'note' => $note, 'detail_link' => ('surat/internal/sheet_view/internal/' . $surat->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
				$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'agenda' => ($row->jenis_agenda . '-' . $row->agenda_id),  'note' => $note, 'detail_link' => ($detail_link  . $row->surat_id), 'notify_user_id' => $row_tujuan->user_id, 'read' => 0));
			}
		}
		foreach ($list_tujuan->result() as $row) {
			// $this->_send_mail_notification($row->email, $subject, $body, array());
		}

		set_success_message('Surat berhasil dikirim.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 *
	 */
	function save_surat_keluar_send()
	{
		$surat_id = $_POST['surat_id'];
		unset($_POST['surat_id']);

		$data = array_intersect_key($_POST, $this->data_object->surat);
		$data['kirim_time'] = human_to_db($data['kirim_time']);
		$data['modified_id'] = get_user_id();
		$data['modified_time'] = date('Y-m-d H:i:s');

		$agenda = $_POST['jenis_agenda'] . ' - ';
		if ($_POST['create_agenda'] == 1) {

			$result = $this->db->get_where('system_security.function_ref', array('function_ref_id' => $_POST['function_ref_id']));
			$function_data = $result->row();

			//$data['agenda_id'] = $this->lx->number_generator($function_data->format_agenda);
			$data['agenda_id'] = $_POST['agenda_id'];
			$agenda .= $data['agenda_id'];
		}

		$data['distribusi'] = json_encode($_POST['distribusi']);

		$this->db->update('surat', $data, array('surat_id' => $surat_id));

		if (isset($_POST['soft_copy'])) {
			$config_file['upload_path'] = 'assets/media/doc/';
			$config_file['encrypt_name'] = TRUE;
			$config_file['max_size'] = 20000;
			$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
			$this->load->library('upload', $config_file);

			switch ($_POST['soft_copy']['state']) {
				case 'insert':
					if (!empty($_FILES['soft_copy']['name'])) {
						if (!$this->upload->do_upload('soft_copy')) {
							set_error_message($this->upload->display_errors());
						} else {
							$file = $this->upload->data();
							$file_attached = array();
							$file_attached['organization_id'] = get_user_data('organization_id');
							$file_attached['table'] = 'copy_surat';
							$file_attached['ref_id'] = $surat_id;
							$file_attached['title'] = $_POST['soft_copy']['title'];
							$file_attached['file_name'] = $_FILES['soft_copy']['name'];
							$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
							$file_attached['sort'] = 0;
							$this->db->insert('file_attachment', $file_attached);
						}
					}

					break;
				case 'delete':
					unlink(str_replace('/lx_media/', 'assets/media/', $v['file']));
					$this->db->delete('file_attachment', array('file_attachment_id' => $v['id']));

					break;
				default:
					$file_attached = array();
					if (!empty($_FILES['soft_copy']['name'])) {
						if (!$this->upload->do_upload('soft_copy')) {
							set_error_message($this->upload->display_errors());
						} else {
							$file = $this->upload->data();
							$file_attached['file_name'] = $_FILES['soft_copy']['name'];
							$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
						}
					}
					$file_attached['sort'] = 0;
					$file_attached['title'] = $_POST['soft_copy']['title'];
					$this->db->update('file_attachment', $file_attached, array('file_attachment_id' => $_POST['soft_copy']['id']));

					break;
			}

			log_message('debug', 'update attachment : ' . $this->db->last_query());
		}

		set_success_message('Data Pengiriman Surat berhasil disimpan.');
		redirect('surat/external/outgoing_view/' . $surat_id);
		exit;
	}

	/**
	 *
	 */
	function delete_surat()
	{
		//var_dump($_POST); exit();
		//$this->db->delete('surat', array('surat_id' => $_POST['surat_id']));
		$this->db->insert('surat_log', array('ref_id' => $_POST['surat_id'], 'status' => 404, 'modified_id' => get_user_id(), 'modified_time' => date('Y-m-d H:i:s'), 'note' => $_POST['note']));

		$this->db->update('surat', array('status' => 404, 'modified_id' => get_user_id(), 'modified_time' => date('Y-m-d H:i:s')), array('surat_id' => $_POST['surat_id']));

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('error' => '', 'msg' => 'Surat berhasil dihapus.')));
	}

	/**
	 *
	 */
	function get_tujuan_surat($tujuan_surat_id)
	{
		$sql = "SELECT * FROM system_security.tujuan_surat 
			WHERE tujuan_surat_id = '$tujuan_surat_id' ";

		return $this->db->query($sql);
	}

	/**
	 *
	 */
	function cek_disposisi_sme($surat_id)
	{
		$sql = "SELECT * FROM disposisi 
			WHERE ref_id = '$surat_id' ";

		$query = $this->db->query($sql);

		$num_row = $query->num_rows();

		return $num_row;
	}

	/**
	 *
	 */
	function selesai_data()
	{
		//   	var_dump($_POST); exit();
		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url('global/dashboard') . "')");

		$this->db->update('surat', array('status' => 7, 'modified_id' => get_user_id(), 'modified_time' => date('Y-m-d H:i:s')), array('surat_id' => $_POST['surat_id']));

		$this->output->set_content_type('application/json')->set_output(json_encode($return));

		// $this->output->set_content_type('application/json')
		// ->set_output(json_encode(array('error' => '', 'msg' => 'Surat berhasil dihapus.')));
	}

	function ubah_tgl()
	{
		if ($_POST['surat_tgl'] != '') {
			$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");

			$sql = "UPDATE surat 
						   SET surat_tgl = '" . $_POST['surat_tgl'] . "'  
						 WHERE function_ref_id = " . $_POST['function_ref_id'] . " AND surat_id = '" . $_POST['ref_id'] . "'";
			$this->db->query($sql);

			set_success_message('Tanggal surat berhasil diubah.');

			$this->output->set_content_type('application/json')->set_output(json_encode($return));
		} else {
			$return = array('message' => 'Tanggal surat belum diisi.');

			// set_warning_message('Tanggal surat belum diisi.');
			// return false;
			$this->output->set_content_type('application/json')->set_output(json_encode($return));
		}
	}
}

/**
 * End of file
 */
