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
 * @filesource Disposisi_model.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Sep 11, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Disposisi_model extends LX_Model {
	
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
			exit;
		}
	}
	
	/**
	 * 
	 */
	function get_pre_disposisi() {
		$sql = "SELECT se.* 
				  FROM surat_eksternal se
				  JOIN system_security.users_structure us ON(us.organization_structure_id = se.surat_int_unit_id AND us.user_id = '" . get_user_id() . "' AND us.status = 1)
			 	 WHERE se.surat_eksternal_id NOT IN(SELECT ref_id FROM disposisi WHERE ref_type = 'surat_eksternal')";
		
		return $this->db->query($sql);
	}
	
	/**
	 * @param unknown $type
	 * @param unknown $ref_id
	 */
	function get_ref_data($type, $ref_id) {
		return $this->db->get_where($type, array($type . '_id' => $ref_id));
	}

	/**
	 * @param unknown $disposisi_id
	 */
	function get_disposisi($disposisi_id = NULL) {
		if($disposisi_id == NULL) {
			return $this->db->get('disposisi');
		} else {
			return $this->db->get_where('disposisi', array('disposisi_id' => $disposisi_id));
		}
	}

	/**
	 * @param unknown $type
	 * @param unknown $ref_id
	 */
	function get_disposisi_from_ref($type, $ref_id) {
		return $this->db->get_where('disposisi', array('ref_type' => $type, 'ref_id' => $ref_id));
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function get_disposisi_to($unit_to) {
		return $this->db->get_where('disposisi', array('surat_from_unit' => get_user_data('unit_name'), 'surat_from_unit' => $unit_to));
	}

	/**
	 * @param unknown $disposisi_id
	 */
	function get_disposisi_flow($disposisi_id) {
		$sql = "SELECT pn.*, u.user_name FROM process_notes pn
			JOIN system_security.users u ON(u.user_id = pn.user_id)
			WHERE \"table\" = 'disposisi' AND ref_id = '$disposisi_id' ORDER BY created_time";
		return $this->db->query($sql);
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function get_disposisi_tree($parent_id = 0, $level = 0) {
		$results = array();
		$rows = $this->db->get_where('disposisi', array('parent_id' => $parent_id, 'organization_id' => get_user_data('organization_id')));
	
		if($rows->num_rows() > 0) {
			$n = $level + 1;
			foreach ($rows->result_array() as $row) {
				$row['disposisi_tree'] = $row['disposisi_id'];
				if($level > 0) {
					$pad = '';
					for($i=1; $i<$level; $i++) {
						$pad .= '<img src="' . assets_url() .'/img/clear.gif" class="nav-tree">';
					}
					$row['disposisi_tree'] = $pad . '<img src="' . assets_url() .'/img/cat_marker.gif" class="nav-tree">' . $row['nama_klasifikasi'];
				}
				$results[] = $row;
				$child = $this->get_klasifikasi_list($row['entry_id'], $n);
				if(count($child) > 0) {
					$results = array_merge($results, $child);
				}
			}				
		}
		
		return $results;
	}

	/**
	 * @param number $parent_id
	 * @param number $level
	 * @return string[]
	 */
	function get_subordinates($parent_id = 1, $level = 0, $depth = 1) {
		$results = array();
//		$this->db->order_by('unit_code');
		if($parent_id == 1) {
			$user_level = 'L1';
		}else {
			$user_level = 'L2';
		}
		
		$sql = "SELECT u.user_id, os.organization_structure_id, (CASE WHEN us.jabatan = 'Staff' THEN (us.jabatan || ' ' || u.user_name) ELSE (us.jabatan || ' ' || os.unit_name) END) sub_name FROM system_security.users u
				JOIN system_security.users_structure us ON(us.user_id = u.user_id)
				JOIN system_security.organization_structure os ON(os.organization_structure_id = us.organization_structure_id AND os.organization_id = '" . get_user_data('organization_id') . "')
				WHERE os.level = '$user_level' AND u.active = 1 AND us.jabatan <> 'Staff'   
				  AND us.user_id <> '" . get_user_id() . "'
				   OR (os.parent_id = $parent_id AND us.jabatan <> 'Staff')";
		
//		$rows = $this->db->get_where('system_security.organization_structure', array('parent_id' => $parent_id, 'status' => 1));	
		$rows = $this->db->query($sql);
	
		if($rows->num_rows() > 0) {
			$n = $level + 1;
			$depth--;
			foreach ($rows->result_array() as $row) {
				$row['unit_tree'] = $row['sub_name'];
				if($level > 0) {
					$row['unit_tree'] = str_pad('', ($level * 3), ' - ', STR_PAD_LEFT) . $row['sub_name'];
				}
				$results[] = $row;
				if($depth != 0) {
					$child = $this->get_subordinates($row['organization_structure_id'], $n, $depth);
					if(count($child) > 0) {
						$results = array_merge($results, $child);
					}
				}
			}
				
		}
		
		return $results;
	}

	/**
	 * 
	 */	
	function get_child_disposisi($disposisi_id, $user_id = NULL) {
		if($user_id == NULL) {
			$params = array('parent_id' => $disposisi_id);
		} else {
			$params = array('parent_id' => $disposisi_id, 'created_id' => $user_id);
		}

		return $this->db->get_where('disposisi', $params);
	}

	/**
	 * 
	 */
	function insert_disposisi() {		
//		echo '<pre>'; var_dump($_POST); echo '</pre>'; exit;
		
		if($this->_validate_post_data($this->data_object->disposisi, 'add') != FALSE) {
			
			$disposisi_id = generate_unique_id();
			$_POST['from_data'] = json_encode($_POST['from_data']);
			$data = array_intersect_key($_POST, $this->data_object->disposisi);
			$data['disposisi_id'] = $disposisi_id;
			$data['created_id'] = get_user_id();
			$data['organization_id'] = get_user_data('organization_id');
			$data['disposisi_tgl'] = human_to_db($data['disposisi_tgl']);
			$data['target_selesai'] = human_to_db($data['target_selesai']);
			
			if(isset($_POST['distribusi'])) {
				$distribusi = array();
				$viewer = array(get_user_id() => array('type' => 'creator', 'nama' => get_user_data('user_name'), 'email' => get_user_data('email'), 'posisi' => (get_user_data('jabatan') . ', ' . get_user_data('unit_name'))));
				foreach($_POST['distribusi']['instruksi'] as $k => $v) {
					
					$sql = "SELECT os.organization_structure_id AS id, os.unit_name AS value, u.user_id, us.jabatan, u.user_name AS nama_pejabat, u.external_id AS nip_pejabat, u.email, dir.unit_name AS instansi, os.unit_code
							  FROM system_security.users u
						 LEFT JOIN system_security.users_structure us ON(u.user_id = us.user_id)
						 LEFT JOIN system_security.organization_structure os ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1)
							  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
							WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND u.user_id = '" . $v['to'] . "'";
					$result = $this->db->query($sql);
					$to = $result->row();
					
					$distribusi_attachment = array();
					if(isset($_POST['distribusi_attachment_' . $k])) {
						$config_file['upload_path'] = 'assets/media/doc/';
						$config_file['encrypt_name'] = TRUE;
						$config_file['max_size'] = 8000;
						$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar|doc|docx|xls|xlsx';
						$this->load->library('upload', $config_file);
						
						$i = 0;
						foreach($_POST['distribusi_attachment_' . $k] as $i => $f) {
							if(!empty($_FILES['distribusi_' . $k . '_attachment_file_' . $i]['name'])) {
								if (!$this->upload->do_upload('distribusi_' . $k . '_attachment_file_' . $i)) {
									//set_error_message($this->upload->display_errors());
									log_message('ERROR', ('distribusi_' . $k . '_attachment_file_' . $i . ' : ' . $this->upload->display_errors()));
								} else {
									$file = $this->upload->data();
									$file_attached = array();
									$file_attached['owner'] = get_user_id();
									$file_attached['file_name'] = $_FILES['distribusi_' . $k . '_attachment_file_' . $i]['name'];
									$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
									$distribusi_attachment[] = $file_attached;									
								}
							}
						}
					}
					
					$distribusi[$to->user_id] = array('instruksi' => $v['note'], 'user_id' => $to->user_id, 'unit_id' => $to->id, 'unit_name' => $to->value, 'kode' => $to->unit_code, 'jabatan' => $to->jabatan, 'name' => $to->nama_pejabat, 'nip' => $to->nip_pejabat, 'email' => $to->email, 'attachment' => $distribusi_attachment, 'diskusi' => array(), 'status' => 0, 'keterangan' => '');
					$viewer[$to->user_id] = array('type' => 'assigned', 'nama' => $to->nama_pejabat, 'email' => $to->email, 'posisi' => ($to->jabatan . ', ' . $to->value));

				}
				
				$data['to_user_id'] = $to->user_id;
				$data['distribusi'] = json_encode($distribusi);
				
				if(isset($_POST['parent_id'])) {
					$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['parent_id']));
					$parent = $result->row();

					$parent_viewer = json_decode($parent->viewer, TRUE);
					foreach ($parent_viewer as $uid => $t) {
						if($t['type'] == 'viewer' || $t['type'] == 'creator') {
							$viewer[$uid] = $t;
						}
					}
				}
				
				$data['viewer'] = json_encode($viewer);

				$this->db->insert('disposisi', $data);

				$i = 0;
				if(isset($_POST['parent_attachment'])) {
					
					foreach($_POST['parent_attachment'] as $k => $v) {
						if(isset($v['title'])) {
							$file_attached['organization_id'] = get_user_data('organization_id');
							$file_attached['table'] = 'disposisi';
							$file_attached['ref_id'] = $disposisi_id;
							$file_attached['title'] = $v['title'];
							$file_attached['file_name'] = $v['file_name'];
							$file_attached['file'] = $v['file'];
							$file_attached['sort'] = $i++;
							$this->db->insert('file_attachment', $file_attached);
						}
					}
				}
				
				if(isset($_POST['attachment'])) {
					$config_file['upload_path'] = 'assets/media/doc/';
					$config_file['encrypt_name'] = TRUE;
					$config_file['max_size'] = 8000;
					$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar|doc|xls|docx|xlsx';
					$this->load->library('upload', $config_file);
					
					foreach($_POST['attachment'] as $k => $v) {
						if($v['title'] != '' && !empty($_FILES['attachment_file_' . $k]['name'])) {
							if (!$this->upload->do_upload('attachment_file_' . $k)) {
								set_error_message($this->upload->display_errors());
							} else {
								$file = $this->upload->data();
								$file_attached = array();
								$file_attached['organization_id'] = get_user_data('organization_id');
								$file_attached['table'] = 'disposisi';
								$file_attached['ref_id'] = $disposisi_id;
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
				$data['flow_type'] = 1;   //forward
				$data['table'] = 'surat';
				$data['ref_id'] = $_POST['ref_id'];
				$data['user_id'] = get_user_id();
				$data['flow_seq'] = 22;
				$data['note'] = 'Disposisi kepada ' . $to->jabatan . ' ' . $to->value;
				$this->db->insert('process_notes', $data);

				$this->db->insert('notify', array('function_ref_id' => '12', 'agenda' => 'disposisi', 'ref_id' => $disposisi_id, 'note' => ('Disposisi dari ' . get_user_data('unit_name')), 'detail_link' => ('surat/disposisi/sheet_view/' . $disposisi_id), 'notify_user_id' => get_user_id(), 'status' => 0, 'read' => 0));
				
				set_success_message('Data Disposisi berhasil disimpan.');
				redirect('surat/disposisi/sheet/' . $disposisi_id);
				exit;
			} else {
				set_error_message('Distibusi Disposisi Kosong');
			}
		} else {
			set_error_message(validation_errors());
		}
	}
	
	/**
	 *
	 */
	function update_disposisi() {
// 		var_dump($_POST); exit();
		if($this->_validate_post_data($this->data_object->disposisi, 'add') != FALSE) {
			$disposisi_id = $_POST['disposisi_id'];
			$_POST['from_data'] = json_encode($_POST['from_data']);
			$data = array_intersect_key($_POST, $this->data_object->disposisi);
			$data['target_selesai'] = human_to_db($data['target_selesai']);
			$data['modified_id'] = get_user_id();
			$data['modified_time'] = date('Y-m-d H:i:s');
			
			if(isset($_POST['distribusi'])) {
				$distribusi = array();
				$viewer = array(get_user_id() => array('type' => 'creator', 'nama' => get_user_data('user_name'), 'email' => get_user_data('email'), 'posisi' => (get_user_data('jabatan') . ', ' . get_user_data('unit_name'))));
				
				foreach($_POST['distribusi']['instruksi'] as $k => $v) {
					$sql = "SELECT os.organization_structure_id AS id, os.unit_name AS value, u.user_id, us.jabatan, u.user_name AS nama_pejabat, u.external_id AS nip_pejabat, u.email, dir.unit_name AS instansi, os.unit_code
							  FROM system_security.users u
						 LEFT JOIN system_security.users_structure us ON(u.user_id = us.user_id)
						 LEFT JOIN system_security.organization_structure os ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1)
							  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
							WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND u.user_id = '" . $v['to'] . "'";
					$result = $this->db->query($sql);
					$to = $result->row();
					
					$distribusi_attachment = array();
					if(isset($_POST['distribusi_attachment_' . $k])) {
						$config_file['upload_path'] = 'assets/media/doc/';
						$config_file['encrypt_name'] = TRUE;
						$config_file['max_size'] = 8000;
						$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
						$this->load->library('upload', $config_file);
						
						$i = 0;
						foreach($_POST['distribusi_attachment_' . $k] as $i => $f) {
							$file_attached = array();
							$file_attached['owner'] = get_user_id();
							$file_attached['file_name'] = $f['file_name'];
							$file_attached['file'] = $f['file'];
							if(!empty($_FILES['distribusi_' . $k . '_attachment_file_' . $i]['name'])) {
								if (!$this->upload->do_upload('distribusi_' . $k . '_attachment_file_' . $i)) {
									// set_error_message($this->upload->display_errors());
									log_message('ERROR', ('distribusi_' . $k . '_attachment_file_' . $i . ' : ' . $this->upload->display_errors()));
								} else {
									$file = $this->upload->data();
									$file_attached['file_name'] = $_FILES['distribusi_' . $k . '_attachment_file_' . $i]['name'];
									$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
								}
							}

							$distribusi_attachment[] = $file_attached;
						}
					}

					$distribusi[$to->user_id] = array('instruksi' => $v['note'], 'user_id' => $to->user_id, 'unit_id' => $to->id, 'unit_name' => $to->value, 'kode' => $to->unit_code, 'jabatan' => $to->jabatan, 'name' => $to->nama_pejabat, 'nip' => $to->nip_pejabat, 'email' => $to->email, 'attachment' => $distribusi_attachment, 'diskusi' => array(), 'status' => 0, 'keterangan' => '');
					$viewer[$to->user_id] = array('type' => 'assigned', 'nama' => $to->nama_pejabat, 'email' => $to->email, 'posisi' => ($to->jabatan . ', ' . $to->value));
				}

				$data['to_user_id'] = $to->user_id;
				$data['distribusi'] = json_encode($distribusi);

				if(isset($_POST['parent_id'])) {
					$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['parent_id']));
					$parent = $result->row();

					$parent_viewer = json_decode($parent->viewer, TRUE);
					foreach ($parent_viewer as $uid => $t) {
						if($t['type'] == 'viewer' || $t['type'] == 'creator') {
							$viewer[$uid] = $t;
						}
					}
				}
				
				$data['viewer'] = json_encode($viewer);
			}

			$this->db->update('disposisi', $data, array('disposisi_id' => $disposisi_id));
			
			if(isset($_POST['attachment'])) {
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 8000;
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
									$file_attached['table'] = 'disposisi';
									$file_attached['ref_id'] = $disposisi_id;
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
			
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1;  //forward
			$data['table'] = 'surat';
			$data['ref_id'] = $_POST['ref_id'];
			$data['user_id'] = get_user_id();
			$data['flow_seq'] = 22;
			$data['note'] = 'Disposisi kepada ' . $to->jabatan . ' ' . $to->value;
			
			$sql_note = "SELECT count(process_note_id) AS note_id FROM process_notes 
						WHERE ref_id = '".$_POST['ref_id']."' AND flow_seq = 22";			
			$result = $this->db->query($sql_note);
			$notes = $result->row();

			if ($notes->note_id > 0) {
				$this->db->update('process_notes', $data, array('flow_seq' => 22, 'ref_id' => $_POST['ref_id']));
			}else {
				$this->db->insert('process_notes', $data);
			}

			set_success_message('Data Disposisi berhasil disimpan.');
			redirect('surat/disposisi/sheet/' . $disposisi_id);
			exit;
		} else {
			set_error_message(validation_errors());
		}		
	}
	
	/**
	 * 
	 */
	function set_diskusi() {
		$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['ref_id']));
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
			$distribusi = json_decode($disposisi->distribusi, TRUE);
			if($disposisi->status == 1 || $distribusi[$_POST['distribusi_id']]['status'] == 0) {
				$return = array('error' => 0, 'message' => 'Komentar berhasil ditambahkan.', 'execute' => "");
				$distribusi[$_POST['distribusi_id']]['diskusi'][date('d-m-Y H:i:s')] = array('user_id' => $_POST['user_id'], 'name' => $_POST['name'], 'profile_pic' => $_POST['profile_pic'], 'text' => $_POST['text']);
				
				$distribusi = json_encode($distribusi);
				$this->db->update('disposisi', array('distribusi' => $distribusi), array('disposisi_id' => $_POST['ref_id']));
			} else {
				$return = array('error' => 2, 'message' => 'Disposisi / Instruksi telah selesai.', 'execute' => "displaySelesai('" . $_POST['row_id']. "')");
			}
		} else {
			$return = array('error' => 1, 'message' => 'data tidak dikenali.');
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * 
	 */
	function reload_diskusi() {
		$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['ref_id']));
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
			$distribusi = json_decode($disposisi->distribusi, TRUE);
			$diskusi = '';
			foreach ($distribusi[$_POST['distribusi_id']]['diskusi'] as $key => $row) {
				if(file_exists( str_replace('/lx_media', './assets/media/', $row['profile_pic']))) {
					$pp = $row['profile_pic'];
				} else {
					$pp = '/lx_media/photo/m.jpg';
				}

				$diskusi .= '<div class="direct-chat-msg ' . (($row['user_id'] == get_user_id()) ? 'right' : '') . '">
				<div class="direct-chat-info clearfix">
					<span class="direct-chat-name pull-left">' . $row['name'] . '</span>
					<span class="direct-chat-timestamp pull-right">' . $key . '</span>
				</div>
				<img class="direct-chat-img" src="' . $pp . '" alt="Message User Image">
				<div class="direct-chat-text">' . $row['text'] . '</div>
			</div>';
			}

			$return = array('error' => 0, 'message' => '', 'diskusi' => $diskusi);
		} else {
			$return = array('error' => 1, 'message' => 'data tidak dikenali.');
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return)); 
	}
	
	/**
	 *
	 */
	function konfirm_partial_instruksi() {
		$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['ref_id']));
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
			$distribusi = json_decode($disposisi->distribusi, TRUE);
			$viewer= json_decode($disposisi->viewer, TRUE);
			if($disposisi->status == 1) {
				$sql = "SELECT os.organization_structure_id AS id, os.unit_name AS value, u.user_id, us.jabatan, u.user_name AS nama_pejabat, u.external_id AS nip_pejabat, u.email, dir.unit_name AS instansi, os.unit_code
							  FROM system_security.users u
						 LEFT JOIN system_security.users_structure us ON(u.user_id = us.user_id)
						 LEFT JOIN system_security.organization_structure os ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1)
							  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
							WHERE os.organization_id = '" . get_user_data('organization_id') . "' AND u.user_id = '" . $_POST['to'] . "'";
				$result = $this->db->query($sql);
				$to = $result->row();
				
				$k = $_POST['row'];
				
				$distribusi_attachment = array();
				if(isset($_POST['distribusi_attachment_' . $k])) {
					$config_file['upload_path'] = 'assets/media/doc/';
					$config_file['encrypt_name'] = TRUE;
					$config_file['max_size'] = 8000;
					$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar|doc|docx|xls|xlsx';
					$this->load->library('upload', $config_file);
					
					$i = 0;
					foreach($_POST['distribusi_attachment_' . $k] as $i => $f) {
						if(!empty($_FILES['distribusi_' . $k . '_attachment_file_' . $i]['name'])) {
							if (!$this->upload->do_upload('distribusi_' . $k . '_attachment_file_' . $i)) {
								//set_error_message($this->upload->display_errors());
								log_message('ERROR', ('distribusi_' . $k . '_attachment_file_' . $i . ' : ' . $this->upload->display_errors()));
							} else {
								$file = $this->upload->data();
								$file_attached = array();
								$file_attached['owner'] = get_user_id();
								$file_attached['file_name'] = $_FILES['distribusi_' . $k . '_attachment_file_' . $i]['name'];
								$file_attached['file'] = '/lx_media/doc/' . $file['file_name'];
								$distribusi_attachment[] = $file_attached;
							}
						}
					}
				}
				
				$distribusi[$to->user_id] = array('instruksi' => $_POST['note'], 'user_id' => $to->user_id, 'unit_id' => $to->id, 'unit_name' => $to->value, 'kode' => $to->unit_code, 'jabatan' => $to->jabatan, 'name' => $to->nama_pejabat, 'nip' => $to->nip_pejabat, 'email' => $to->email, 'attachment' => $distribusi_attachment, 'diskusi' => array(), 'status' => 0);
				$viewer[$to->user_id] = array('type' => 'assigned', 'nama' => $to->nama_pejabat, 'email' => $to->email, 'posisi' => ($to->jabatan . ', ' . $to->value));
				
				$return = array('error' => 0, 'message' => 'Instruksi berhasil ditambahkan.', 'execute' => "");
				$distribusi = json_encode($distribusi);
				$viewer = json_encode($viewer);
				$this->db->update('disposisi', array('distribusi' => $distribusi, 'viewer' => $viewer), array('disposisi_id' => $_POST['ref_id']));
				
				$asal_surat = json_decode($disposisi->from_data, TRUE);
				$subject = 'Notifikasi pengiriman Disposisi';
				$body = 'Anda menerima Disposisi Surat Masuk dari '
						. '<br> Nama : ' . $asal_surat['nama']
						. '<br> Jabatan : ' . $asal_surat['jabatan'] . ', ' . $asal_surat['unit'] . ' ';
						
				$this->_send_mail_notification($to->email, $subject, $body, array());
				
				$this->db->insert('notify', array('function_ref_id' => '12', 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $to->user_id, 'agenda' => 'disposisi', 'note' => ('Disposisi dari ' . $asal_surat['jabatan'] . ', ' . $asal_surat['unit']), 'detail_link' => ('surat/disposisi/sheet_view/' . $disposisi->disposisi_id), 'status' => 0, 'read' => 0));
			} else {
				$return = array('error' => 2, 'message' => 'Disposisi / Instruksi telah selesai.');
			}
		} else {
			$return = array('error' => 1, 'message' => 'data tidak dikenali.');
		}
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 */
	function set_selesai_child($parent_id, $from_user_id = NULL) {
		if($from_user_id != NULL) {
			$result = $this->db->get_where('disposisi', array('parent_id' => $parent_id, 'from_user_id' => $from_user_id));
			if($result->num_rows() > 0) {
				$child = $result->row(); 

				$from_data = json_decode($child->from_data, TRUE);
				$subject = 'Notifikasi Disposisi Selesai';
				$body = 'Disposisi dari ' . $from_data['nama']  . ', ' . $from_data['jabatan'] . ', ' . $from_data['unit'] . ' Telah Selesai.<br>';
				
				$distribusi = json_decode($child->distribusi, TRUE);
				foreach ($distribusi as $key => $value) {
					$distribusi[$key]['status'] = 99;

					$this->db->update('notify', array('note' => $body), array('function_ref_id' => '12', 'ref_id' => $child->disposisi_id, 'notify_user_id' => $key));
					
					$this->_send_mail_notification($value['email'], $subject, $body, array());

				}
				$distribusi = json_encode($distribusi);
				$this->db->update('disposisi', array('distribusi' => $distribusi, 'status' => 99), array('disposisi_id' => $child->disposisi_id));
				$this->set_selesai_child($child->disposisi_id);
			} else {
				return;
			}
		} else {
			$result = $this->db->get_where('disposisi', array('parent_id' => $parent_id));
			if($result->num_rows() > 0) {
				foreach ($result->result() as $child) {

					$from_data = json_decode($child->from_data, TRUE);
					$subject = 'Notifikasi Disposisi Selesai';
					$body = 'Disposisi dari ' . $from_data['nama']  . ', ' . $from_data['jabatan'] . ', ' . $from_data['unit'] . ' Telah Selesai.<br>';
					
					$distribusi = json_decode($child->distribusi, TRUE);
					foreach ($distribusi as $key => $value) {
						$distribusi[$key]['status'] = 99;

						$this->db->update('notify', array('note' => $body), array('function_ref_id' => '12', 'ref_id' => $child->disposisi_id, 'notify_user_id' => $key));
						$this->_send_mail_notification($value['email'], $subject, $body, array());

					}
					$distribusi = json_encode($distribusi);
					$this->db->update('disposisi', array('distribusi' => $distribusi, 'status' => 99), array('disposisi_id' => $child->disposisi_id));
					$this->set_selesai_child($child->disposisi_id);
				}
			} else {
				return;
			}
		}
	}

	/**
	 * 
	 */
	function set_selesai() {
//		var_dump($_POST);exit();
		if($_POST['keterangan'] == '') {
			$return = array('error' => 1, 'message' => 'Kolom keterangan harus diisi');
		}else {
			$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['ref_id']));
			if($result->num_rows() > 0) {
				$disposisi = $result->row();

				$from_data = json_decode($disposisi->from_data, TRUE);
				$subject = 'Notifikasi Disposisi Selesai';
				$body = 'Disposisi dari ' . $from_data['nama']  . ', ' . $from_data['jabatan'] . ', ' . $from_data['unit'] . ' Telah Selesai.<br>';
				
				$distribusi = json_decode($disposisi->distribusi, TRUE);
				$instruksi = $distribusi[$_POST['distribusi_id']];
			
				$return = array('error' => '', 'message' => ('Disposisi ke ' . $distribusi[$_POST['distribusi_id']]['jabatan'] . ' ' . $distribusi[$_POST['distribusi_id']]['unit_name'] . ', <br>' . $distribusi[$_POST['distribusi_id']]['name'] . '<br>Telah selesai'), 'execute' => "");
				
				$distribusi[$_POST['distribusi_id']]['status'] = 99;
				$distribusi[$_POST['distribusi_id']]['keterangan'] = $_POST['keterangan'];
				
				$distribusi = json_encode($distribusi);
				$this->db->update('disposisi', array('distribusi' => $distribusi), array('disposisi_id' => $_POST['ref_id']));
				$this->db->update('notify', array('note' => $body), array('function_ref_id' => '12', 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $_POST['distribusi_id']));
	
				$this->_send_mail_notification($instruksi['email'], $subject, $body, array());
				// $this->set_selesai_child($_POST['ref_id'], $_POST['distribusi_id']);
			} else {
				$return = array('error' => 1, 'message' => 'data tidak dikenali.');
			}
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 */
	function upload_private_attachment() {
		$return = array('error' => 0, 'message' => 'File berhasil ditambahkan.', 'file' => '', 'file_name' => '');
		$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['ref_id']));
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
			$distribusi = json_decode($disposisi->distribusi, TRUE);
			
			if($disposisi->status == 1 || $distribusi[$_POST['distribusi_id']]['status'] == 0) {
				$k = $_POST['key'];
				
				$config_file['upload_path'] = 'assets/media/doc/';
				$config_file['encrypt_name'] = TRUE;
				$config_file['max_size'] = 8000;
				$config_file['allowed_types'] = 'jpg|jpeg|png|pdf|zip|rar';
				$this->load->library('upload', $config_file);
			
				if(!empty($_FILES['distribusi_' . $k . '_attachment_file']['name'])) {
					if (!$this->upload->do_upload('distribusi_' . $k . '_attachment_file')) {
						$return['error'] =  1;
						$return['message'] =  $this->upload->display_errors();
					} else {
						$file = $this->upload->data();
						$return['file'] = '/lx_media/doc/' . $file['file_name'];
						$return['file_name'] =  $_FILES['distribusi_' . $k . '_attachment_file']['name'];
						
						$distribusi[$k]['attachment'][] = array('owner' => get_user_id(), 'file' => '/lx_media/doc/' . $file['file_name'], 'file_name' =>  $_FILES['distribusi_' . $k . '_attachment_file']['name']);
					}

					$data['distribusi'] = json_encode($distribusi);
				}

				$this->db->update('disposisi', $data, array('disposisi_id' => $_POST['ref_id']));
			} else {
				$return = array('error' => 2, 'message' => 'Disposisi / Instruksi telah selesai.');
			}
		} else {
			$return['error'] =  1;
			$return['message'] =  'Data tidak dikenali.';
		}
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 *
	 */
	function remove_private_attachment() {
		$return = array('error' => 0, 'message' => 'File berhasil dihapus.', 'file' => '', 'file_name' => '');
		$result = $this->db->get_where('disposisi', array('disposisi_id' => $_POST['ref_id']));
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
			$distribusi = json_decode($disposisi->distribusi, TRUE);
			foreach($distribusi[$_POST['key']]['attachment'] as $i => $f) {
				if($f['file'] == $_POST['file']) {
					unset($distribusi[$_POST['key']]['attachment'][$i]);
					unlink(str_replace('/lx_media', './assets/media', stripslashes($f['file'])));
				}
			}
			
			$data['distribusi'] = json_encode($distribusi);
		
			$this->db->update('disposisi', $data, array('disposisi_id' => $_POST['ref_id']));
		} else {
			$return['error'] =  1;
			$return['message'] =  'Data tidak dikenali.';
		}
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 */
	function return_data() {
		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");
		
		$sql = "UPDATE disposisi 
					   SET status = (CASE WHEN status = 0 THEN 0 ELSE status - 1 END) 
					 WHERE disposisi_id = '" . $_POST['ref_id'] . "'";
		$this->db->query($sql);

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = -1;	//return
		$data['table'] = 'surat';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		$sql = "SELECT s.*, fp.title process_title, fp.role_handle, fp.permission_handle FROM surat s
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = s.status AND fp.status = 1)
				WHERE s.surat_id = '" . $_POST['ref_id'] .  "' ";
		$result = $this->db->query($sql);
		$surat = $result->row();
		
		$jenis_agenda = ($surat->jenis_agenda == 'M') ? 'Masuk' : 'Keluar';
		
		$subject = "Notifikasi Proses surat Internal - " . $surat->agenda_id;
		$body = 'Surat Internal - ' . $surat->agenda_id . ' dikembalikan ke proses ' . $surat->process_title;
		
		$list_tujuan = user_in_unit(get_user_data('unit_id'));
		foreach ($list_tujuan->result() as $row_tujuan) {
			$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id'], 'notify_user_id' => $row_tujuan->user_id));
// 			echo $this->db->last_query();
			$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
		}
		
		set_success_message('Berkas berhasil dikembalikan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 */
	function proses_data() {
 		$return = array('error' => '', 'message' => '', 'execute' => "location.reload()");

		$sql = "UPDATE disposisi
				   SET status = (CASE WHEN status = " . $_POST['last_flow'] . " THEN " . $_POST['last_flow'] . " ELSE status + 1 END)
				 WHERE disposisi_id = '" . $_POST['ref_id'] . "'";
		$this->db->query($sql);
		
		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; 	//forward
		$data['table'] = 'disposisi';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
		
		$sql = "SELECT d.*, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.position_handle FROM disposisi d
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = d.status AND fp.status = 1)
				WHERE d.disposisi_id = '" . $_POST['ref_id'] . "' ";
		$result = $this->db->query($sql);
		$disposisi = $result->row();
		
		if($_POST['function_handler'] != '-') {
			$this->$_POST['function_handler']($disposisi);
		}
		
//		set_success_message('Proses berkas berhasil dilanjutkan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * @param unknown $disposisi_id
	 */
	function init_kirim_disposisi($disposisi) {

		if($disposisi->parent_id != '-') {
			$this->db->delete('notify', array('agenda' => 'disposisi', 'ref_id' => $disposisi->parent_id, 'notify_user_id' => get_user_id()));
		}

		$this->db->delete('notify', array('agenda' => 'disposisi', 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $disposisi->from_user_id));
		
		$asal_surat = json_decode($disposisi->from_data, TRUE);
		$tujuan_surat = json_decode($disposisi->viewer, TRUE);
		$distribusi = json_decode($disposisi->distribusi, TRUE);
		$distribusi_to = $distribusi[$disposisi->to_user_id];

		$sql_surat = "SELECT * FROM surat WHERE surat_id = '" . $disposisi->ref_id . "' ";
		$list = $this->db->query($sql_surat);
		$row_surat = $list->row();

		$surat_from_ref_data = json_decode($row_surat->surat_from_ref_data, TRUE);

		switch ($disposisi->sifat) {
			case '0':
				$sifat = 'Biasa'; 
				break;
			case '1':
				$sifat = 'Segera'; 
				break;
			case '2':
				$sifat = 'Sangat Segera'; 
				break;
		}

		//$subject = 'Notifikasi pengiriman Disposisi';
		$subject = $row_surat->jenis_agenda . '-' . $row_surat->agenda_id . ' - Pengiriman Disposisi - ' . $row_surat->surat_perihal;
		$lname = array();

		foreach ($tujuan_surat as $key => $value) {
			switch ($value['type']) {
				case 'viewer':
					# code...
					$body = 'Disposisi diteruskan kepada  '
							. '<br> Nama : ' . $value['nama']  
							. '<br> Jabatan : ' . $value['posisi']  
							. '<br> oleh '
							. '<br> Nama : ' . $asal_surat['nama'] 
							. '<br> Jabatan : ' . $asal_surat['jabatan'] . ', ' . $asal_surat['unit'] . ' ';

					$this->_send_mail_notification($value['email'], $subject, $body, array());
					
					$lname[] = $value['nama'];

				break;
				case 'assigned':
					# code...
					$body = 'Anda menerima disposisi pada tanggal ' . db_to_human_local($disposisi->created_time) . ' dari '
							. '<br> Nama : ' . $asal_surat['nama'] 
							. '<br> Jabatan : ' . $asal_surat['jabatan'] . ', ' . $asal_surat['unit'] 
							. '<br> '
							. '<br><strong>Identitas Surat</strong>'
							. '<br>Nomor Surat : ' . $row_surat->surat_no 
							. '<br>Asal Surat : ' . $surat_from_ref_data['instansi']  
							. '<br>Perihal : ' . $row_surat->surat_perihal
							. '<br>Tanggal Surat : ' . db_to_human($row_surat->surat_tgl)
							. '<br>Tanggal Terima : ' . db_to_human($row_surat->surat_tgl_masuk)
							. '<br> '
							. '<br>Instruksi Disposisi : ' . $distribusi_to['instruksi']
							. '<br>Sifat : ' . $sifat
							. '<br>Target Penyelesaian : ' . db_to_human($disposisi->target_selesai)
						;

					$this->_send_mail_notification($value['email'], $subject, $body, array());
					
					$this->db->delete('notify', array('agenda' => 'disposisi', 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $key));
					$this->db->insert('notify', array('function_ref_id' => '12', 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $key, 'agenda' => 'disposisi', 'note' => ('Disposisi dari ' . $asal_surat['jabatan'] . ', ' . $asal_surat['unit']), 'detail_link' => ('surat/disposisi/sheet_view/' . $disposisi->disposisi_id), 'status' => 0, 'read' => 0));
					
					$lname[] = $value['nama'];

				break;
			}			
		}

		if($disposisi->parent_id == '-'){
			$body = 'Direktur sudah melakukan disposisi pada surat '
							. '<br>Nomor Surat : ' . $row_surat->surat_no 
							. '<br>Asal Surat : ' . $surat_from_ref_data['instansi']  
							. '<br>Perihal : ' . $row_surat->surat_perihal
							. '<br>Tanggal Surat : ' . db_to_human($row_surat->surat_tgl)
							. '<br>Tanggal Terima : ' . db_to_human($row_surat->surat_tgl_masuk)
							. '<br> '
							. '<br>Instruksi Disposisi : ' . $distribusi_to['instruksi']
							. '<br>Sifat : ' . $sifat
							. '<br>Target Penyelesaian : ' . db_to_human($disposisi->target_selesai)
							. '<br>'
							. '<br>Silahkan lakukan Cetak Pengantar & Kirim Disposisi '
						;

			$list_tujuan = user_with_permission(7);
			
			foreach ($list_tujuan->result() as $row_tujuan) {
				$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());	
			}			
		}

		//$body = 'Disposisi untuk ' . (implode('<br> - ', $lname))  . '<br> telah dikirim.';
		$body = 'Anda telah mengirim disposisi pada tanggal ' . db_to_human_local($disposisi->created_time) . ' untuk '
							. '<br> Nama : ' . $distribusi_to['name'] 
							. '<br> Jabatan : ' . $distribusi_to['jabatan'] . ', ' . $distribusi_to['unit_name'] 
							. '<br> '
							. '<br><strong>Identitas Surat</strong>'
							. '<br>Nomor Surat : ' . $row_surat->surat_no 
							. '<br>Asal Surat : ' . $surat_from_ref_data['instansi']  
							. '<br>Perihal : ' . $row_surat->surat_perihal
							. '<br>Tanggal Surat : ' . db_to_human($row_surat->surat_tgl)
							. '<br>Tanggal Terima : ' . db_to_human($row_surat->surat_tgl_masuk)
							. '<br> '
							. '<br>Instruksi Disposisi : ' . $distribusi_to['instruksi']
							. '<br>Sifat : ' . $sifat
							. '<br>Target Penyelesaian : ' . db_to_human($disposisi->target_selesai)
						;

		$this->_send_mail_notification($asal_surat['email'], $subject, $body, array());

		$this->db->insert('notify', array('function_ref_id' => '12', 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $disposisi->from_user_id, 'agenda' => 'disposisi', 'note' => ('Disposisi dari ' . $asal_surat['jabatan'] . ', ' . $asal_surat['unit']), 'detail_link' => ('surat/disposisi/sheet_view/' . $disposisi->disposisi_id), 'status' => 0, 'read' => 0));
	}

	/**
	 * @param unknown $disposisi_id
	 *
	function init_kirim_disposisi($disposisi) {
		
		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'disposisi' ";
		$result = $this->db->query($sql);
		$function_ref = $result->row();
		
		$result = $this->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
		$ref = $result->row();
		
		$from = json_decode($disposisi->from_data, TRUE);
		
		$i = 1;
		$tujuan_surat = json_decode($disposisi->distribusi, TRUE);

		if($disposisi->parent_id != '-') {
			$this->db->delete('notify', array('agenda' => 'disposisi', 'ref_id' => $disposisi->parent_id, 'notify_user_id' => get_user_id()));
		}

		$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $disposisi->from_user_id));
		$lname = array();
		foreach($tujuan_surat as $row) {	
			$i++;
		
			$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row['user_id']));
			$subject = 'Notifikasi pengiriman Disposisi';
			$body = 'Disposisi Surat Masuk untuk ' . $row['name']  . ' telah dikirim.';
			$this->_send_mail_notification($from['email'], $subject, $body, array());
			$lname[] = $row['name'];
			
			// set workspace untuk Penerima
			$body = 'Anda menerima Disposisi Surat Masuk dari ' . $from['jabatan'] . ' ' . $from['unit']  ;
			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row['user_id'], 'agenda' => 'disposisi', 'note' => ('Disposisi dari ' . $from['jabatan'] . ' ' . $from['unit']), 'detail_link' => ('surat/disposisi/sheet_view/' . $disposisi->disposisi_id), 'status' => 0, 'read' => 0));
			$this->_send_mail_notification($row['email'], $subject, $body, array());
		}
		$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $disposisi->from_user_id, 'agenda' => 'disposisi', 'note' => ('Disposisi untuk ' . implode(', ', $lname)), 'detail_link' => ('surat/disposisi/sheet_view/' . $disposisi->disposisi_id), 'status' => 0, 'read' => 0));
		
		set_success_message('Disposisi Berhasil dikirim.');
		
	}*/
	
	/**
	 * @param unknown $disposisi_id
	 */
	function init_selesai($disposisi) {

		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'disposisi' ";
		$result = $this->db->query($sql);
		$function_ref = $result->row();

		$from_data = json_decode($disposisi->from_data, TRUE);

		$subject = 'Notifikasi Disposisi Selesai';
		$body = 'Disposisi dari ' . $from_data['nama']  . ', ' . $from_data['jabatan'] . ', ' . $from_data['unit'] . ' Telah Selesai.<br>';
		
		$distribusi = json_decode($disposisi->distribusi, TRUE);
		foreach ($distribusi as $key => $value) {
			$distribusi[$key]['status'] = 99;

			$this->db->update('notify', array('note' => $body), array('function_ref_id' => '12', 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $key) );
			
			$this->_send_mail_notification($value['email'], $subject, $body, array());
		}
		
		$distribusi = json_encode($distribusi);

		$this->db->update('disposisi', array('status' => 99, 'complete_time' => date('Y-m-d H:i:s'), 'distribusi' => $distribusi), array('disposisi_id' => $_POST['ref_id']));

		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['table'] = 'disposisi';
		$data['ref_id'] = $disposisi->disposisi_id;
		$data['flow_seq'] = 99;
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);

		set_success_message('Disposisi Berhasil diselesaikan.');
	}

	/**
	 * @param unknown $disposisi_id
	 *
	function terima_disposisi($disposisi_id) {
	
		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'disposisi' ";
		$result = $this->db->query($sql);
		$function_ref = $result->row();
	
		$result = $this->get_disposisi($disposisi_id);
		$disposisi = $result->row();
	
		$result = $this->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
		$ref = $result->row();
	
		$sql = "SELECT u.* FROM system_security.users u
				JOIN system_security.users_structure us ON(us.user_id = u.user_id AND us.status = 1 AND u.active = 1)
				WHERE us.organization_structure_id = " . $disposisi->surat_to_unit_id;
		$list_tujuan = $this->db->query($sql);
	
		$sql = "UPDATE disposisi
				   SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 1 END),
					   kirim_time = '" . date('Y-m-d H:i:s') . "'
				 WHERE disposisi_id = '" . $disposisi->disposisi_id . "'";
		$this->db->query($sql);
			
		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['table'] = 'disposisi';
		$data['ref_id'] = $disposisi->disposisi_id;
		$data['flow_seq'] = $disposisi->status + 1;
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
	
		$subject = 'Notifikasi Penerimaan Disposisi';
		$body = 'Disposisi Surat Masuk untuk ' . $disposisi->surat_to_unit  . ' telah diterima.<br>' .
				'<strong>Petugas Pengirim : </strong><br>' . $disposisi->petugas_pengirim . '<br>';
		// set workspace untuk pengirim
		$list_unit_staff = user_in_unit($disposisi->surat_from_unit_id);
		foreach ($list_unit_staff->result() as $row) {
			$this->db->update('notify', array('note' => ('Disposisi Surat Masuk Eksternal untuk ' . $disposisi->surat_to_unit . ' telah dikirim')), array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row->user_id));
			$this->_send_mail_notification($row->email, $subject, $body, array());
		}
/* 	
		$body = 'Anda menerima Disposisi Surat Masuk dari ' . $disposisi->surat_from_unit  . '.<br>' .
				'<strong>Petugas Pengirim : </strong><br>' . $disposisi->petugas_pengirim . '<br>';
	
		// set workspace untuk Penerima
		$list_unit_staff = user_in_unit($disposisi->surat_to_unit_id);
		foreach ($list_unit_staff->result() as $row) {
			$this->db->insert('notify', array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'agenda' => ($ref->jenis_agenda . '-' . $ref->agenda_id), 'note' => ('Disposisi Surat Masuk Eksternal dari ' . $disposisi->surat_from_unit), 'detail_link' => ('surat/disposisi/sheet/' . $disposisi->disposisi_id), 'notify_user_id' => $row->user_id, 'status' => 0, 'read' => 0));
			$this->_send_mail_notification($row->email, $subject, $body, array());
		}
 	
		set_success_message('Disposisi Berhasil diterima.');
	}

	/**
	 *
	 *
	function return_data() {
		$return = array('error' => '', 'message' => '', 'execute' => "location.assign('" . site_url() . "')");
	
		$sql = "UPDATE disposisi
				   SET status = (CASE WHEN status = 0 THEN 0 ELSE status - 1 END)
				 WHERE disposisi_id = '" . $_POST['ref_id'] . "'";
		$this->db->query($sql);
	
		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = -1;//return
		$data['table'] = 'disposisi';
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
	
		$sql = "SELECT d.*, fp.title process_title, fp.role_handle, fp.permission_handle FROM disposisi d
				JOIN system_security.flow_process fp ON(fp.function_ref_id = " . $_POST['function_ref_id'] . " AND fp.flow_seq = d.status AND fp.status = 1)
						WHERE d.disposisi_id = '" . $_POST['ref_id'] .  "' ";
		$result = $this->db->query($sql);
		$disposisi = $result->row();
	
// 		hapus semua notif
// 		$this->db->delete('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $_POST['ref_id']));

// 		$subject = 'Notifikasi pengiriman Disposisi';
// 		$body = 'Proses Disposisi Surat Masuk untuk ' . $disposisi->surat_to_unit  . ' telah dikembalikan.<br>';
// 		// set workspace untuk pengirim
// 		$list_unit_staff = user_in_unit($disposisi->surat_from_unit_id);
// 		foreach ($list_unit_staff->result() as $row) {
// 			$this->db->update('notify', array('note' => ('Disposisi Surat Masuk Eksternal untuk ' . $disposisi->surat_to_unit . ' telah dikirim')), array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row->user_id));
// 			$this->_send_mail_notification($row->email, $subject, $body, array());
// 		}
		
// 		$body = 'Anda menerima Disposisi Surat Masuk dari ' . $disposisi->surat_from_unit  . '.<br>' .
// 				'<strong>Petugas Pengirim : </strong><br>' . $disposisi->petugas_pengirim . '<br>';
		
// 		// set workspace untuk Penerima
// 		$list_unit_staff = user_in_unit($disposisi->surat_to_unit_id);
// 		foreach ($list_unit_staff->result() as $row) {
// 			$this->db->insert('notify', array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'agenda' => ($ref->jenis_agenda . '-' . $ref->agenda_id), 'note' => ('Disposisi Surat Masuk Eksternal dari ' . $disposisi->surat_from_unit), 'detail_link' => ('surat/disposisi/sheet/' . $disposisi->disposisi_id), 'notify_user_id' => $row->user_id, 'status' => 0, 'read' => 0));
// 			$this->_send_mail_notification($row->email, $subject, $body, array());
// 		}
		
		set_success_message('Berkas berhasil dikembalikan.');
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * 
	 *
	function save_response() {
		
		$this->db->update('disposisi', array('response_text' => $_POST['response']), array('disposisi_id' => $_POST['ref_id']));
		$return = array('error' => '', 'message' => 'Respon berhasil di simpan.', 'execute' => "");
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * @param unknown $disposisi_id
	 *
	function response_disposisi($disposisi_id) {

		$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
				JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
				JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
				WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'disposisi' ";
		$result = $this->db->query($sql);
		$function_ref = $result->row();
		
		$result = $this->get_disposisi($disposisi_id);
		$disposisi = $result->row();
		
		$result = $this->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
		$ref = $result->row();
		
		$sql = "SELECT u.* FROM system_security.users u
				JOIN system_security.users_structure us ON(us.user_id = u.user_id AND us.status = 1 AND u.active = 1)
				WHERE us.organization_structure_id = " . $disposisi->surat_to_unit_id;
		$list_tujuan = $this->db->query($sql);
		
		echo $sql = "UPDATE disposisi
				   SET status = (CASE WHEN status = " . $function_ref->max_flow . " THEN " . $function_ref->max_flow . " ELSE status + 1 END),
					   kirim_time = '" . date('Y-m-d H:i:s') . "'
				 WHERE disposisi_id = '" . $disposisi->disposisi_id . "'";
		$this->db->query($sql);
			
		$data = array_intersect_key($_POST, $this->data_object->process_notes);
		$data['organization_id'] = get_user_data('organization_id');
		$data['flow_type'] = 1; //forward
		$data['table'] = 'disposisi';
		$data['ref_id'] = $disposisi->disposisi_id;
		$data['flow_seq'] = $disposisi->status + 1;
		$data['user_id'] = get_user_id();
		$this->db->insert('process_notes', $data);
		
		$subject = 'Notifikasi Respon Disposisi';
		$body = 'Disposisi Surat Masuk untuk ' . $disposisi->surat_to_unit  . ' telah direspon oleh ' . get_user_data('user_name') . '.<br>' .
				'<strong>Respon : </strong><br>' . $disposisi->response_text . '<br>';
		// set workspace untuk pengirim
		$list_unit_staff = user_in_unit($disposisi->surat_from_unit_id);
		foreach ($list_unit_staff->result() as $row) {
			$this->db->update('notify', array('note' => ('Disposisi Surat Masuk Eksternal untuk ' . $disposisi->surat_to_unit . ' telah direspon')), array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row->user_id));
			$this->_send_mail_notification($row->email, $subject, $body, array());
		}
		
		$body = 'Disposisi Surat Masuk dari ' . $disposisi->surat_from_unit  . ' sudah direspon.<br>';
		
		// set workspace untuk Penerima
		$list_unit_staff = user_in_unit($disposisi->surat_to_unit_id);
		foreach ($list_unit_staff->result() as $row) {
			$this->db->update('notify', array('note' => ('Disposisi Surat Masuk Eksternal untuk ' . $disposisi->surat_to_unit . ' telah direspon')), array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row->user_id));
			$this->_send_mail_notification($row->email, $subject, $body, array());
		}
		
		set_success_message('Disposisi Berhasil direspon.');
	}
	
	function complete_disposisi($disposisi_id) {

		$result = $this->get_disposisi($disposisi_id);
		if($result->num_rows() > 0) {
			$disposisi = $result->row();
				
			$sql = "SELECT fr.*, max_flow FROM system_security.function_ref fr
					JOIN system_security.module_ref mr ON(mr.module_ref_id = fr.module_ref_id)
					JOIN (SELECT function_ref_id, MAX(flow_seq) max_flow FROM system_security.flow_process GROUP BY function_ref_id) fp ON(fp.function_ref_id = fr.function_ref_id)
					WHERE mr.organization_id = '" . get_user_data('organization_id') . "' AND fr.module_function = 'disposisi' ";
			$result = $this->db->query($sql);
			$function_ref = $result->row();

			$result = $this->get_ref_data($disposisi->ref_type, $disposisi->ref_id);
			$ref = $result->row();
			
			$sql = "SELECT u.* FROM system_security.users u
				JOIN system_security.users_structure us ON(us.user_id = u.user_id AND us.status = 1 AND u.active = 1)
				WHERE us.organization_structure_id = " . $disposisi->surat_to_unit_id;
			$list_tujuan = $this->db->query($sql);
			
			$this->db->update('disposisi', array('status' => 99, 'complete_time' => date('Y-m-d H:i:s')), array('disposisi_id' => $disposisi_id));
				
			$data = array_intersect_key($_POST, $this->data_object->process_notes);
			$data['organization_id'] = get_user_data('organization_id');
			$data['flow_type'] = 1; //forward
			$data['table'] = 'disposisi';
			$data['ref_id'] = $disposisi->disposisi_id;
			$data['flow_seq'] = 99;
			$data['user_id'] = get_user_id();
			$this->db->insert('process_notes', $data);

			$subject = 'Notifikasi Disposisi Selesai';
			$body = 'Disposisi Surat Masuk untuk ' . $disposisi->surat_to_unit  . ' telah selesai.<br>' .
			// set workspace untuk pengirim
			$list_unit_staff = user_in_unit($disposisi->surat_from_unit_id);
			foreach ($list_unit_staff->result() as $row) {
				$this->db->update('notify', array('note' => ('Disposisi Surat Masuk Eksternal untuk ' . $disposisi->surat_to_unit . ' telah direspon')), array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row->user_id));
				$this->_send_mail_notification($row->email, $subject, $body, array());
			}
			
			$body = 'Disposisi Surat Masuk dari ' . $disposisi->surat_from_unit  . ' Telah Selesai.<br>';
			
			// set workspace untuk Penerima
			$list_unit_staff = user_in_unit($disposisi->surat_to_unit_id);
			foreach ($list_unit_staff->result() as $row) {
				$this->db->update('notify', array('note' => ('Disposisi Surat Masuk Eksternal untuk ' . $disposisi->surat_to_unit . ' telah direspon')), array('function_ref_id' => $function_ref->function_ref_id, 'ref_id' => $disposisi->disposisi_id, 'notify_user_id' => $row->user_id));
				$this->_send_mail_notification($row->email, $subject, $body, array());
			}
			
		} else {
			set_error_message('Data tidak dikenali.');
			return;
				
		}
		
		set_success_message('Status disposisi telah selesai.');
			
	}
	*/
}

/**
 * End of file
 */