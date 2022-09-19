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
 * @filesource admin_model.php
 * @copyright Copyright 2011-2015, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Aug 23, 2015
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Admin_model extends LX_Model
{

	function __construct()
	{
		parent::__construct();
		if (!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
	}

	/**
	 * Enter description here ...
	 * @return multitype:string 
	 */
	public function get_config()
	{
		$this->db->order_by('type, sort');
		return $this->db->get('system_security.system_variables');
	}

	/**
	 * @param unknown $module_function
	 */
	function get_function_ref($module_function, $module_name = 'Surat')
	{
		$sql = "SELECT fr.* FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = '$module_function' AND mr.module_ref_name = '$module_name' ";
		$result = $this->db->query($sql);
		return $result->row();
	}

	/**
	 * Enter description here ...
	 * @return multitype:string 
	 */
	public function get_system_config($type, $key = NULL)
	{
		return $this->_system_config($type, $key);
	}

	public function get_contract_config($type, $key = NULL)
	{
		return $this->_contract_config($type, $key);
	}

	public function get_system_cm_config($type, $key = NULL)
	{
		return $this->_system_cm_config($type, $key);
	}

	function get_process($function_ref_id, $flow_seq = NULL)
	{
		if ($flow_seq == NULL) {
			$this->db->order_by('flow_seq');
			return $this->db->get_where('system_security.flow_process', array('function_ref_id' => $function_ref_id, 'status' => 1));
		} else {
			return $this->db->get_where('system_security.flow_process', array('function_ref_id' => $function_ref_id, 'status' => 1, 'flow_seq' => $flow_seq));
		}
	}

	/**
	 * 
	 */
	function save_ref()
	{
		$return = array('error' => '', 'message' => 'referensi berhasil disimpan.', 'execute' => '');

		$instansi = array_intersect_key($_POST, $this->data_object->_ref_instansi_eksternal);
		$instansi['organization_id'] = get_user_data('organization_id');
		$instansi['created_id'] = get_user_id();
		$this->db->insert('_ref_instansi_eksternal', $instansi);

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * Enter description here ...
	 * @param int $parent_id
	 * @param int $level
	 * @return ArrayIterator
	 */
	function get_klasifikasi_list($parent_id = 0, $level = 0)
	{
		$results = array();
		$this->db->order_by('sort, kode_klasifikasi');
		$rows = $this->db->get_where('system_security.klasifikasi_arsip', array('parent_id' => $parent_id, 'organization_id' => get_user_data('organization_id')));

		if ($rows->num_rows() > 0) {
			$n = $level + 1;
			foreach ($rows->result_array() as $row) {
				$row['klasifikasi_tree'] = $row['nama_klasifikasi'];
				if ($level > 0) {
					$pad = '';
					for ($i = 1; $i < $level; $i++) {
						$pad .= '<img src="' . assets_url() . '/img/clear.gif" class="nav-tree">';
					}
					$row['klasifikasi_tree'] = $pad . '<img src="' . assets_url() . '/img/cat_marker.gif" class="nav-tree">' . $row['nama_klasifikasi'];
				}

				$results[] = $row;
				$child = $this->get_klasifikasi_list($row['entry_id'], $n);
				if (count($child) > 0) {
					$results = array_merge($results, $child);
				}
			}
		}

		return $results;
	}

	/**
	 * @param unknown $entry_id
	 */
	function get_klasifikasi_arsip_detail($entry_id)
	{
		return $this->db->get_where('system_security.klasifikasi_arsip', array('entry_id' => $entry_id, 'organization_id' => get_user_data('organization_id')));
	}

	/**
	 * Enter description here ...
	 * @param int $parent_id
	 * @param int $level
	 * @return ArrayIterator
	 */
	function get_parent_klasifikasi_arsip_list($parent_id = 0, $level = 1)
	{
		$results = array(0 => 'ROOT');
		$rows = $this->db->get_where('system_security.klasifikasi_arsip', array('parent_id' => $parent_id));

		if ($rows->num_rows() > 0) {
			$n = $level + 6;
			foreach ($rows->result() as $row) {
				$results[$row->entry_id] = str_pad(" &boxv;", $n, "&boxv;") . '> ' . ($row->kode_klasifikasi . ' - ' . $row->nama_klasifikasi);
				$child = $this->get_parent_klasifikasi_arsip_list($row->entry_id, $n);
				if (count($child) > 0) {
					$results += $child;
				}
			}
		}

		return $results;
	}

	/**
	 * Enter description here ...
	 */
	function save_klasifikasi_arsip()
	{

		if ($this->_validate_post_data($this->data_object->klasifikasi_arsip, 'add') != FALSE) {

			switch ($_POST['mode']) {
				case 'add':
					$data = array_intersect_key($_POST, $this->data_object->klasifikasi_arsip);
					$data['organization_id'] = get_user_data('organization_id');
					$this->db->insert('system_security.klasifikasi_arsip', $data);
					$id = $this->db->insert_id();

					set_success_message('Klasifikasi Arsip berhasil ditambahkan.');
					redirect('global/admin/klasifikasi_arsip_detail/' . $id);
					exit;
					break;
				case 'edit':

					$id = $_POST['entry_id'];
					unset($_POST['entry_id']);

					$data = array_intersect_key($_POST, $this->data_object->klasifikasi_arsip);
					$this->db->update('system_security.klasifikasi_arsip', $data, array('entry_id' => $id));

					set_success_message('Klasifikasi Arsip berhasil diperbaharui.');
					redirect('global/admin/klasifikasi_arsip_detail/' . $id);
					exit;
					break;
			}
		} else {
			set_error_message(validation_errors());
		}
	}

	/**
	 * Enter description here ...
	 */
	function delete_klasifikasi_arsip()
	{

		$tree = $this->get_parent_klasifikasi_arsip_list($_POST['entry_id']);
		foreach ($tree as $k => $v) {
			$this->db->delete('system_security.klasifikasi_arsip', array('entry_id' => $k));
		}
		$this->db->delete('system_security.klasifikasi_arsip', array('entry_id' => $_POST['entry_id']));

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('error' => '', 'msg' => 'Klasifikasi Arsip berhasil dihapus.')));
	}

	/**
	 * Enter description here ...
	 * @return ArrayIterator
	 */
	function get_direksi()
	{
		$sql = "SELECT os.organization_structure_id, os.unit_code, os.unit_name, os.level, us.jabatan, u.user_name FROM system_security.organization_structure os 
				  LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id) 
				  LEFT JOIN system_security.users u ON(u.user_id = us.user_id) 
				 WHERE os.status = 1 AND us.jabatan != 'Staff'
				   AND os.organization_id = '" . get_user_data('organization_id') . "'
				   AND os.level IN('L0', 'L1') 
				ORDER BY os.unit_code ";

		return $this->db->query($sql);
	}

	/**
	 * Enter description here ...
	 * @return ArrayIterator
	 */
	function get_non_direksi()
	{
		$sql = "SELECT os.organization_structure_id, os.unit_code, os.unit_name, os.level, us.jabatan, u.user_name FROM system_security.organization_structure os 
				  LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id) 
				  LEFT JOIN system_security.users u ON(u.user_id = us.user_id) 
				 WHERE os.status = 1 AND us.jabatan != 'Staff'
				   AND os.organization_id = '" . get_user_data('organization_id') . "'
				   AND os.level NOT IN('L0', 'L1') 
				ORDER BY os.unit_code ";

		return $this->db->query($sql);
	}

	/**
	 * @param unknown $organization_structure_id
	 */
	function get_org_structure($organization_structure_id = NULL)
	{

		$where = '';
		if (is_array($organization_structure_id)) {
			$where = " AND os.organization_structure_id IN(" . implode(', ', $organization_structure_id) . ") ";
		} else {
			$where = " AND os.organization_structure_id = $organization_structure_id ";
		}

		$sql = "SELECT os.organization_structure_id, os.unit_code, os.unit_name, os.level, u.user_name FROM system_security.organization_structure os 
				  LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id) 
				  LEFT JOIN system_security.users u ON(u.user_id = us.user_id) 
				 WHERE os.status = 1
				   AND os.organization_id = '" . get_user_data('organization_id') . "'
				   $where
				ORDER BY os.unit_code ";

		return $this->db->query($sql);
	}

	/**
	 * Enter description here ...
	 * @param int $parent_id
	 * @param int $level
	 * @return ArrayIterator
	 */
	function get_org_structure_list($parent_id = NULL, $level = 0)
	{
		$results = array();
		$this->db->order_by('unit_code');
		$rows = $this->db->get_where('system_security.organization_structure', array('parent_id' => $parent_id, 'organization_id' => get_user_data('organization_id')));
		// echo $this->db->last_query();
		if ($rows->num_rows() > 0) {
			$n = $level + 1;
			foreach ($rows->result_array() as $row) {
				$row['klasifikasi_tree'] = $row['unit_name'];
				if ($level > 0) {
					$pad = '';
					for ($i = 1; $i < $level; $i++) {
						$pad .= '<img src="' . assets_url() . '/img/clear.gif" class="nav-tree">';
					}
					$row['klasifikasi_tree'] = $pad . '<img src="' . assets_url() . '/img/cat_marker.gif" class="nav-tree">' . $row['unit_name'];
				}
				$results[] = $row;
				$child = $this->get_org_structure_list($row['organization_structure_id'], $n);
				if (count($child) > 0) {
					$results = array_merge($results, $child);
				}
			}
		}

		return $results;
	}

	/**
	 * @param unknown $organization_structure_id
	 */
	function get_org_structure_detail($organization_structure_id)
	{
		return $this->db->get_where('system_security.organization_structure', array('organization_structure_id' => $organization_structure_id, 'organization_id' => get_user_data('organization_id')));
	}

	/**
	 * Enter description here ...
	 * @param int $parent_id
	 * @param int $level
	 * @return ArrayIterator
	 */
	function get_parent_org_structure_list($parent_id = NULL, $level = 1)
	{
		$results = array('' => 'ROOT');
		$rows = $this->db->get_where('system_security.organization_structure', array('parent_id' => $parent_id));

		if ($rows->num_rows() > 0) {
			$n = $level + 6;
			foreach ($rows->result() as $row) {
				$results[$row->organization_structure_id] = str_pad(" &boxv;", $n, "&boxv;") . '> ' . ($row->unit_code . ' - ' . $row->unit_name);
				$child = $this->get_parent_org_structure_list($row->organization_structure_id, $n);
				if (count($child) > 0) {
					$results += $child;
				}
			}
		}

		return $results;
	}

	/**
	 * Enter description here ...
	 */
	function save_org_structure()
	{

		if ($_POST['parent_id'] == '') {
			unset($_POST['parent_id']);
		}

		switch ($_POST['mode']) {
			case 'add':
				if ($this->_validate_post_data($this->data_object->organization_structure, 'add') != FALSE) {

					$data = array_intersect_key($_POST, $this->data_object->organization_structure);
					$data['organization_id'] = get_user_data('organization_id');
					$this->db->insert('system_security.organization_structure', $data);
					$id = $this->db->insert_id();

					set_success_message('Struktur berhasil ditambahkan.');
					redirect('global/admin/org_structure_detail/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
			case 'edit':
				if ($this->_validate_post_data($this->data_object->organization_structure, 'edit') != FALSE) {

					$id = $_POST['organization_structure_id'];
					unset($_POST['organization_structure_id']);

					$data = array_intersect_key($_POST, $this->data_object->organization_structure);
					$this->db->update('system_security.organization_structure', $data, array('organization_structure_id' => $id));

					set_success_message('Struktur berhasil diperbaharui.');
					redirect('global/admin/org_structure_detail/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
		}
	}

	/**
	 * Enter description here ...
	 */
	function delete_org_structure()
	{

		$tree = $this->get_parent_org_structure_list($_POST['organization_structure_id']);
		foreach ($tree as $k => $v) {
			$this->db->delete('system_security.organization_structure', array('organization_structure_id' => $k));
		}
		$this->db->delete('system_security.organization_structure', array('organization_structure_id' => $_POST['organization_structure_id']));

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('error' => '', 'msg' => 'Struktur berhasil dihapus.')));
	}

	/**
	 * Enter description here ...
	 * @return ArrayIterator
	 */
	function get_format_surat_list()
	{
		$sql = "SELECT fs.*, fr.function_ref_name FROM system_security.format_surat fs JOIN system_security.function_ref fr ON(fr.function_ref_id = fs.function_ref_id)";
		$list = $this->db->query($sql);
		return $list->result();
	}

	function get_referensi_list()
	{
		$sql = "SELECT rie.* FROM _ref_instansi_eksternal rie
		WHERE rie.nama_pejabat = 'mitra' or rie.nama_pejabat = 'kode_kontrak' or rie.nama_pejabat = 'jenis_kontrak' or rie.nama_pejabat = 'jenis_keputusan' or rie.nama_pejabat = 'sumber_usulan' or rie.nama_pejabat = 'alasan_keputusan' or rie.nama_pejabat = 'mata_anggaran'
		ORDER by rie.nama_pejabat";
		$list = $this->db->query($sql);
		return $list->result();
	}

	function get_mitra_list()
	{
		$sql = "SELECT rie.* FROM _ref_instansi_eksternal rie
		WHERE rie.nama_pejabat = 'mitra'  
		ORDER by rie.nama_pejabat";
		$list = $this->db->query($sql);
		return $list->result();
	}

	function get_referensi_external_list()
	{
		$sql = "SELECT rie.* FROM _ref_instansi_eksternal rie
				WHERE rie.nama_pejabat NOT IN ('mitra','kode_kontrak','jenis_kontrak','jenis_keputusan','sumber_usulan','alasan_keputusan','mata_anggaran')
				ORDER by rie.nama_pejabat";
		$list = $this->db->query($sql);
		return $list->result();
	}

	/**
	 * @param unknown $format_surat_id
	 */
	function get_format_surat_detail($format_surat_id)
	{
		return $this->db->get_where('system_security.format_surat', array('format_surat_id' => $format_surat_id));
	}

	function get_referensi_detail($entry_id)
	{
		return $this->db->get_where('_ref_instansi_eksternal', array('entry_id' => $entry_id));
	}

	function get_mitra_detail($entry_id)
	{
		return $this->db->get_where('_ref_instansi_eksternal', array('entry_id' => $entry_id));
	}

	/**
	 * Enter description here ...
	 */
	function save_format_surat()
	{

		switch ($_POST['mode']) {
			case 'add':
				if ($this->_validate_post_data($this->data_object->format_surat, 'add') != FALSE) {
					$data = array_intersect_key($_POST, $this->data_object->format_surat);
					$this->db->insert('system_security.format_surat', $data);
					$id = $this->db->insert_id();

					set_success_message('Format berhasil ditambahkan.');
					redirect('global/admin/format_surat_detail/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
			case 'edit':
				if ($this->_validate_post_data($this->data_object->format_surat, 'edit') != FALSE) {
					$id = $_POST['format_surat_id'];
					unset($_POST['format_surat_id']);

					if (!isset($_POST['status'])) {
						$_POST['status'] = 0;
					}

					$data = array_intersect_key($_POST, $this->data_object->format_surat);
					$this->db->update('system_security.format_surat', $data, array('format_surat_id' => $id));

					set_success_message('Format berhasil diperbaharui.');
					redirect('global/admin/format_surat_detail/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
		}
	}

	/**
	 * Enter description here ...
	 */
	function save_referensi()
	{

		switch ($_POST['mode']) {

			case 'add':

				if ($this->_validate_post_data($this->data_object->referensi, 'add') != FALSE) {
					$data = array_intersect_key($_POST, $this->data_object->referensi);
					$data['created_id'] = get_user_id();
					$data['organization_id'] = get_user_data('organization_id');
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['jabatan'] = "-";
					$this->db->insert('_ref_instansi_eksternal', $data);
					$id = $this->db->insert_id();

					set_success_message('Referensi berhasil ditambahkan.');
					redirect('global/admin/referensi/' . $id);
					exit;
				} else {

					set_error_message('Validation Error');
					redirect('global/admin/referensi/' . $id);
				}
				break;
			case 'edit':
				if ($this->_validate_post_data($this->data_object->referensi, 'edit') != FALSE) {
					$id = $_POST['entry_id'];
					unset($_POST['entry_id']);

					if (!isset($_POST['status'])) {
						$_POST['status'] = 0;
					}


					$data = array_intersect_key($_POST, $this->data_object->referensi);
					$this->db->update('_ref_instansi_eksternal', $data, array('entry_id' => $id));

					set_success_message('Referensi berhasil diperbaharui.');
					redirect('global/admin/referensi/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
		}
	}

	/**
	 * Enter description here ...
	 */
	function save_referensi_external()
	{

		switch ($_POST['mode']) {

			case 'add':

				if ($this->_validate_post_data($this->data_object->referensi, 'add') != FALSE) {
					$data = array_intersect_key($_POST, $this->data_object->referensi);
					$data['created_id'] = get_user_id();
					$data['organization_id'] = get_user_data('organization_id');
					$data['created_time'] = date('Y-m-d H:i:s');
					$this->db->insert('_ref_instansi_eksternal', $data);
					$id = $this->db->insert_id();

					set_success_message('Referensi berhasil ditambahkan.');
					redirect('global/admin/tujuan_surat_eksternal/' . $id);
					exit;
				} else {

					set_error_message('Validation Error');
					redirect('global/admin/tujuan_surat_eksternal/' . $id);
				}
				break;
			case 'edit':
				if ($this->_validate_post_data($this->data_object->referensi, 'edit') != FALSE) {
					$id = $_POST['entry_id'];
					unset($_POST['entry_id']);

					if (!isset($_POST['status'])) {
						$_POST['status'] = 0;
					}


					$data = array_intersect_key($_POST, $this->data_object->referensi);
					$this->db->update('_ref_instansi_eksternal', $data, array('entry_id' => $id));

					set_success_message('Referensi berhasil diperbaharui.');
					redirect('global/admin/tujuan_surat_eksternal/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
		}
	}

	/**
	 * Enter description here ...
	 */
	function save_mitra()
	{

		switch ($_POST['mode']) {

			case 'add':
				if ($this->_validate_post_data($this->data_object->referensi, 'add') != FALSE) {
					$data = array_intersect_key($_POST, $this->data_object->referensi);
					$data['created_id'] = get_user_id();
					$data['organization_id'] = get_user_data('organization_id');
					$data['created_time'] = date('Y-m-d H:i:s');
					$data['jabatan'] = "-";
					$this->db->insert('_ref_instansi_eksternal', $data);
					$id = $this->db->insert_id();

					set_success_message('Mitra berhasil ditambahkan.');
					redirect('global/admin/mitra/' . $id);
					exit;
				} else {

					set_error_message('Validation Error');
					redirect('global/admin/mitra/' . $id);
				}
				break;
			case 'edit':
				if ($this->_validate_post_data($this->data_object->referensi, 'edit') != FALSE) {
					$id = $_POST['entry_id'];
					unset($_POST['entry_id']);

					if (!isset($_POST['status'])) {
						$_POST['status'] = 0;
					}


					$data = array_intersect_key($_POST, $this->data_object->referensi);
					$this->db->update('_ref_instansi_eksternal', $data, array('entry_id' => $id));

					set_success_message('Mitra berhasil diperbaharui.');
					redirect('global/admin/mitra/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
		}
	}


	/**
	 * Enter description here ...
	 */
	function delete_format_surat()
	{

		$this->db->delete('system_security.format_surat', array('format_surat_id' => $_POST['format_surat_id']));

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('error' => '', 'msg' => 'Format berhasil dihapus.')));
	}

	/**
	 * Enter description here ...
	 */
	function delete_referensi()
	{

		$this->db->delete('_ref_instansi_eksternal', array('entry_id' => $_POST['entry_id']));

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('error' => '', 'msg' => 'Referensi berhasil dihapus.')));
	}

	function delete_mitra()
	{

		$this->db->delete('_ref_instansi_eksternal', array('entry_id' => $_POST['entry_id']));

		$this->output->set_content_type('application/json')
			->set_output(json_encode(array('error' => '', 'msg' => 'Mitra berhasil dihapus.')));
	}

	/**
	 *
	 */
	function get_ref_surat_masuk($surat_id)
	{
		$sql = "SELECT s.surat_id, s.surat_no, s.surat_tgl, s.surat_perihal, s.surat_from_ref_data  
				FROM surat s
				JOIN surat_ref sr ON (s.surat_id = sr.surat_from_ref_id)
				WHERE sr.ref_id = '$surat_id'";

		return $this->db->query($sql);
	}

	/**
	 *
	 */
	function get_ref_instansi_eksternal($key)
	{
		$sql = "SELECT entry_id AS id, jabatan AS value, nama_pejabat, instansi, address FROM _ref_instansi_eksternal WHERE organization_id = '" . get_user_data('organization_id') . "' AND LOWER(jabatan) LIKE '%$key%' ORDER BY jabatan";
		$list = $this->db->query($sql);

		return json_encode($list->result());
	}

	/**
	 *
	 */
	function get_ref_instansi_internal($key)
	{
		$sql = "SELECT os.organization_structure_id AS id, os.unit_name AS value, us.jabatan, us.pangkat, u.user_name AS nama_pejabat, u.external_id AS nip_pejabat, dir.unit_name AS instansi, os.unit_code
				  FROM system_security.organization_structure os
			 LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1 AND structure_head = 1)
			 LEFT JOIN system_security.users u ON(u.user_id = us.user_id)
				  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
					WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND LOWER(os.unit_name) LIKE '%$key%' ORDER BY os.unit_name";
		$list = $this->db->query($sql);

		return json_encode($list->result());
	}

	/**
	 *
	 */
	function get_ref_tembusan_instansi_internal($key)
	{
		$sql = "SELECT os.organization_structure_id AS id, os.unit_name, us.jabatan, concat(us.jabatan, ' ', os.unit_name) AS value, u.external_id AS nip_pejabat, dir.unit_name AS instansi, os.unit_code
				  FROM system_security.organization_structure os
			 LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1 AND structure_head = 1)
			 LEFT JOIN system_security.users u ON(u.user_id = us.user_id)
				  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
					WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND LOWER(os.unit_name) LIKE '%$key%' ORDER BY os.unit_name";
		$list = $this->db->query($sql);

		return json_encode($list->result());
	}

	/**
	 *
	 */
	function get_ref_asal_surat_masuk($key)
	{
		$sql = "SELECT s.surat_id, concat(json_extract_path_text(to_json(surat_from_ref_data::json), 'instansi'), ', ', s.surat_no, ', ', to_char(s.surat_tgl, 'DD-MM-YYYY'), ', ', s.surat_perihal) AS value, s.surat_from_ref_data, at.title, at.file_name, at.file
				FROM surat s
				JOIN file_attachment at ON (s.surat_id = at.ref_id)
				WHERE s.function_ref_id = 1 AND s.status >= 4 
					AND s.surat_id IN (SELECT DISTINCT ON (ref_id) ref_id FROM disposisi WHERE ref_id = s.surat_id)
					AND LOWER(json_extract_path_text(to_json(surat_from_ref_data::json), 'instansi')) LIKE '%$key%' ORDER BY s.surat_tgl DESC";
		$list = $this->db->query($sql);

		return json_encode($list->result());
	}

	/**
	 *
	 */
	function get_ref_tujuan_surat($key)
	{
		$sql = "SELECT tujuan_surat_id AS id, title AS value, to_user_data FROM system_security.tujuan_surat 
			WHERE LOWER(title) LIKE '%$key%' ORDER BY tujuan_surat_id ASC";

		$list = $this->db->query($sql);

		return json_encode($list->result());
	}

	/**
	 *
	 */
	function get_default_tujuan_sme()
	{
		$key = strtolower('RSUD BALARAJA');
		return $this->get_ref_instansi_internal($key);
	}

	/**
	 * @param number $parent_id
	 * @param number $level
	 * @return string[]
	 */
	function get_subordinates($parent_id = 1, $level = 0, $depth = 1)
	{
		$results = array();
		//		$this->db->order_by('unit_code');

		$sql = "SELECT organization_structure_id, unit_name FROM system_security.organization_structure os
				WHERE parent_id = $parent_id";

		//		$rows = $this->db->get_where('system_security.organization_structure', array('parent_id' => $parent_id, 'status' => 1));
		$rows = $this->db->query($sql);

		if ($rows->num_rows() > 0) {
			$n = $level + 1;
			$depth--;
			foreach ($rows->result_array() as $row) {
				$row['unit_tree'] = $row['unit_name'];
				if ($level > 0) {
					$row['unit_tree'] = str_pad('', ($level * 3), ' - ', STR_PAD_LEFT) . $row['unit_name'];
				}
				$results[] = $row;
				if ($depth != 0) {
					$child = $this->get_subordinates($row['organization_structure_id'], $n, $depth);
					if (count($child) > 0) {
						$results = array_merge($results, $child);
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Enter description here ...
	 * @return multitype:string 
	 */
	function save_config()
	{
		$sql = "TRUNCATE TABLE system_security.system_variables";
		$this->db->query($sql);
		if (isset($_POST['key'])) {
			foreach ($_POST['key'] as $k => $v) {
				if ($_POST['type'][$k] != '') {
					$this->db->insert('system_security.system_variables', array('type' => $_POST['type'][$k], 'key' => $v, 'val' => $_POST['val'][$k], 'sort' => $_POST['sort'][$k], 'active' => $_POST['active'][$k]));
				}
			}
		}

		redirect('admin/system_variables');
		exit;
	}

	/**
	 * 
	 */
	function get_organization_module()
	{
		return $this->db->get_where('system_security.module_ref', array('organization_id' => get_user_data('organization_id'), 'status' => 1));
	}

	/**
	 * @param unknown $module_ref_id
	 */
	function get_organization_function($module_ref_id)
	{
		$this->db->order_by('sort');
		return $this->db->get_where('system_security.function_ref', array('module_ref_id' => $module_ref_id, 'status' => 1));
	}

	/**
	 * @param unknown $organization_structure_id
	 */
	function get_ref_internal($organization_structure_id)
	{
		if ($organization_structure_id) {
			$sql = "SELECT os.organization_structure_id AS id, os.unit_name AS value, us.jabatan, us.pangkat, u.user_name AS nama_pejabat, u.user_id, u.email, os.ske_sign, os.sub_id, os.abv, os.level, os.official_code, u.external_id AS nip_pejabat, dir.unit_name AS instansi, os.unit_code
					  FROM system_security.organization_structure os
				 LEFT JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1 AND us.structure_head = 1)
				 LEFT JOIN system_security.users u ON(u.user_id = us.user_id)
					  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
						WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND os.organization_structure_id = " . $organization_structure_id . " ORDER BY os.unit_name";

			return $this->db->query($sql);
		} else {
			redirect('global/dashboard');
		}
	}

	/**
	 * @param unknown $table
	 * @param unknown $ref_id
	 */
	function get_file_attachment($table, $ref_id, $sort = NULL)
	{
		if ($sort == NULL) {
			$this->db->order_by('sort');
			return $this->db->get_where('file_attachment', array('table' => $table, 'ref_id' => $ref_id));
		} else {
			return $this->db->get_where('file_attachment', array('table' => $table, 'ref_id' => $ref_id, 'sort' => $sort));
		}
	}

	/**
	 * @param unknown $table
	 * @param unknown $ref_id
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

	/**
	 * @param unknown $code
	 */
	function get_klasifikasi_arsip($code)
	{
		$sql = "SELECT kss.entry_id, kss.kode_klasifikasi kode_klasifikasi_sub_sub, kss.nama_klasifikasi nama_klasifikasi_sub_sub, ks.nama_klasifikasi nama_klasifikasi_sub, k.nama_klasifikasi
			 FROM system_security.klasifikasi_arsip kss
			 JOIN system_security.klasifikasi_arsip ks ON(ks.entry_id = kss.parent_id AND ks.status = 1)
			 JOIN system_security.klasifikasi_arsip k ON(k.entry_id = ks.parent_id AND k.status = 1)
			 WHERE kss.status = 1 AND kss.kode_klasifikasi = '" . $code . "'";
		return $this->db->query($sql);
	}

	/**
	 * Enter description here ...
	 * @return multitype:string 
	 */
	public function get_department()
	{
		return $this->db->get('department');
	}

	/**
	 * Enter description here ...
	 * @return multitype:string 
	 */
	function save_department()
	{
		$sql = "TRUNCATE TABLE department";
		$this->db->query($sql);
		if (isset($_POST['department_id'])) {
			foreach ($_POST['department_id'] as $k => $v) {
				if ($_POST['department_id'][$k] != '') {
					$this->db->insert('department', array('department_id' => $v, 'department_name' => $_POST['department_name'][$k], 'active' => $_POST['active'][$k]));
				}
			}
		}

		redirect('admin/department');
		exit;
	}

	/**
	 * @param unknown $group
	 */
	function get_doc_group($group)
	{
		return $this->db->get_where('doc_type', array('doc_group' => $group, 'status' => 1));
	}

	/**
	 * @return NULL[]
	 */
	function get_parent_role()
	{
		$return = array();

		$rows = $this->db->get('system_security.security_role');

		foreach ($rows->result() as $row) {
			//			if(get_role() <= $row->role_id) {
			$return[$row->role_id] = $row->name;
			//			}
		}

		return $return;
	}

	/**
	 * 
	 */
	function get_parent_klasifikasi_arsip($parent_id)
	{
		$this->db->order_by('sort');
		return $this->db->get_where('system_security.klasifikasi_arsip', array('parent_id' => $parent_id, 'status' => 1));
	}

	/**
	 * @return NULL[]
	 */
	function get_parent_function()
	{
		$return = array();

		$rows = $this->db->get_where('system_security.function_ref', array('module_ref_id' => 1, 'status' => 1));

		foreach ($rows->result() as $row) {
			$return[$row->function_ref_id] = $row->function_ref_name;
		}
		return $return;
	}

	/**
	 * @return NULL[]
	 */
	function get_parent_function_internal()
	{
		$return = array();

		$rows = $this->db->get_where('system_security.function_ref', array('module_ref_id' => 1, 'function_ref_id' => 3, 'status' => 1));

		foreach ($rows->result() as $row) {
			$return[$row->function_ref_id] = $row->function_ref_name;
		}

		return $return;
	}

	/**
	 * @param unknown $table
	 * @param unknown $param
	 */
	function get_object_data($table, $param)
	{
		return $this->db->get_where($table, $param);
	}

	/**
	 * @param unknown $function_ref_id
	 * @param unknown $format_surat_id
	 */
	function get_template_surat($function_ref_id, $format_surat_id = NULL)
	{
		$param = ($format_surat_id == NULL) ? array('status' => 1, 'function_ref_id' => $function_ref_id) : array('status' => 1, 'function_ref_id' => $function_ref_id, 'format_surat_id' => $format_surat_id);
		return $this->db->get_where('system_security.format_surat', $param);
	}

	/**
	 * 
	 */
	function get_header_notification()
	{
		$this->db->order_by('created_time', 'DESC');
		return $this->db->get_where('notify', array(
			'status' => 1,
			'read' => 0,
			'notify_user_id' => get_user_id()
		));
	}

	function get_tujuan_surat_list()
	{
		$sql = "SELECT ts.*, fr.function_ref_name FROM system_security.tujuan_surat ts 
			JOIN system_security.function_ref fr ON(fr.function_ref_id = ts.function_ref_id) ORDER BY tujuan_surat_id ASC";
		$list = $this->db->query($sql);
		return $list->result();
	}

	function get_tujuan_surat_detail($entry_id)
	{
		return $this->db->get_where('system_security.tujuan_surat', array('tujuan_surat_id' => $entry_id));
	}

	/**
	 * Enter description here ...
	 */
	function save_tujuan_surat()
	{
		//		var_dump($_POST); exit();
		switch ($_POST['mode']) {
			case 'add':
				if ($this->_validate_post_data($this->data_object->tujuan_surat, 'add') != FALSE) {
					$data = array_intersect_key($_POST, $this->data_object->tujuan_surat);
					$data['title'] = $_POST['title'];
					$data['function_ref_id'] = $_POST['function_ref_id'];
					$data['to_user_data'] = $_POST['to_user_data'];
					$data['status'] = 1;

					$this->db->insert('system_security.tujuan_surat', $data);
					$id = $this->db->insert_id();

					set_success_message('Tujuan surat berhasil ditambahkan.');
					redirect('global/admin/tujuan_surat/' . $id);
					exit;
				} else {
					set_error_message('Validation Error');
					// redirect('global/admin/tujuan_surat/' . $id);
				}
				break;
			case 'edit':
				if ($this->_validate_post_data($this->data_object->tujuan_surat, 'edit') != FALSE) {
					$id = $_POST['tujuan_surat_id'];
					unset($_POST['tujuan_surat_id']);

					// if(!isset($_POST['status'])) {
					// 	$_POST['status'] = 0;
					// }

					$data = array_intersect_key($_POST, $this->data_object->tujuan_surat);
					$data['title'] = $_POST['title'];
					$data['function_ref_id'] = $_POST['function_ref_id'];
					$data['to_user_data'] = $_POST['to_user_data'];

					$this->db->update('system_security.tujuan_surat', $data, array('tujuan_surat_id' => $id));

					set_success_message('Tujuan surat berhasil diperbaharui.');
					redirect('global/admin/tujuan_surat/' . $id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
				break;
		}
	}
}

/**
 * End of file
 */
