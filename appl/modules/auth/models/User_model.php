<?php	if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * @filesource user_model.php
 * @copyright Copyright 2011-2015, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Aug 14, 2015
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class User_model extends LX_Model {

	function __construct() {
		parent::__construct();
	}
	
	/**
	 * @return Object user data
	 */
	function get_user_list() {
		$sql = "SELECT user_id, user_name, external_id, u.email, r.name AS role 
				FROM system_security.users u
				JOIN system_security.security_role r ON(r.role_id = u.role_id)
		--		WHERE u.organization_id = '" . get_user_data('organization_id') . "'
				ORDER BY r.role_id, user_name";
		
		return $this->db->query($sql);
	}

	function get_report_to() {
		$sql = "SELECT user_id, user_name, r.name AS role FROM system_security.users u
				JOIN system_security.security_role r ON(r.role_id = u.role_id)
				WHERE r.role_id NOT IN(1, 7, 8, 9, 10)
				ORDER BY r.role_id, user_name";
		$list = $this->db->query($sql);
		$return = array('' => array('0' => '--'));
		foreach ($list->result() as $row) {
			$return[$row->role][$row->user_id] = $row->user_name;
		}
		return $return;
	}
	
	/**
	 *
	 */
	function get_user_assoc() {
		$list = $this->get_user_list();
		$return = array('' => array('' => '--'));
		foreach ($list->result() as $row) {
			$return[$row->role][$row->user_id] = $row->user_name;
		}
		return $return;
	}
	
	/**
	 * Enter description here ...
	 */
	function get_role_list() {
		return $this->db->get('system_security.security_role');
	}
	
	/**
	 * Enter description here ...
	 */
	function get_role($role_id) {
		return $this->db->get_where('system_security.security_role', array('role_id' => $role_id));
	}

	/**
	 * Enter description here ...
	 */
	function get_user_role($role_id) {
		return $this->db->get_where('system_security.users', array('role_id' => $role_id, 'active' => 1));
	}
	
	/**
	 * Enter description here ...
	 */
	function get_permission_list() {
		return $this->db->get('system_security.security_permission');
	}
	
	/**
	 * Enter description here ...
	 */
	function save_role() {
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', 'Role Name', 'trim|required');
			
		switch ($_POST['mode']) {
			case 'add':
				unset($_POST['mode']);
				if ($this->form_validation->run() != FALSE) { //echo 'valid';
					$this->db->insert('system_security.security_role', array('name' => $_POST['name']));
					$role_id = $this->db->insert_id();
		
					if (isset($_POST['security_permission'])) {
						foreach ($_POST['security_permission'] as $v) {
							$this->db->insert('system_security.security_role_permission', array('role_id' => $role_id, 'permission_id' => $v));
						}
					}
					
					set_success_message('Successfully add role');
					redirect('auth/user/role_data/' . $role_id);
					exit;
				}
			break;
			case 'edit':
				unset($_POST['mode']);
				if ($this->form_validation->run() != FALSE)	{ //echo 'valid';
					$this->db->update('system_security.security_role', array('name' => $_POST['name']), array('role_id' => $_POST['role_id']));
					
					$this->db->delete('system_security.security_role_permission', array('role_id' => $_POST['role_id']));
					if(isset($_POST['security_permission'])) {
						foreach($_POST['security_permission'] as $v) {
							$this->db->insert('system_security.security_role_permission', array('role_id' => $_POST['role_id'], 'permission_id' => $v));
						}
					}

					set_success_message('Successfully edit role');
					redirect('auth/user/role_data/' . $_POST['role_id']);
					exit;
				}
			break;
			case 'delete':
				$this->db->delete('system_security.security_role_permission', array('role_id' => $_POST['role_id']));
				$this->db->delete('system_security.security_role', array('role_id' => $_POST['role_id']));
				
				set_success_message('Successfully relete role');
				redirect('auth/user/role_permission/');
				exit;
			break;
		}
	}
    
	/**
	 * @param $role_id
	 * @return unknown_type
	 */
	function delete_role($role_id = NULL) {
		if($role_id == NULL && isset($_POST['role_id'])) {
			$role_id = $_POST['role_id'];
			
			$this->db->delete('system_security.security_role_permission', array('role_id' => $role_id));
			$this->db->delete('system_security.security_role', array('role_id' => $role_id));
			$return = array('error' => '', 'msg' => sprintf(lang('success_delete'), $role_id . ' role'));
		} else {
			$return = array('error' => '1', 'msg' => 'Delete failed!');
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * Enter description here ...
	 * @param int $role_id
	 */
	function get_role_permission($role_id) {
		$sql = "SELECT p.*, rp.entry_id, rp.allow_deny "
			.  "FROM system_security.security_permission p "
			.  "LEFT JOIN system_security.security_role_permission rp ON(rp.permission_id = p.permission_id AND rp.role_id = $role_id) "
			.  "ORDER BY p.permission_id";
		return $this->db->query($sql);
	}
	
	/**
	 * Enter description here ...
	 * @param int $role_id
	 */
	function get_x_role_permission($role_id) {
		$sql = "SELECT * "
			.  "FROM system_security.security_permission "
			.  "WHERE permission_id NOT IN(SELECT permission_id FROM system_security.security_role_permission WHERE role_id = $role_id)";
		return $this->db->query($sql);
	}
	
	/**
	 * Enter description here ...
	 * @param int $role_id
	 * @param int $permission_id
	 * @param int $allow
	 */
	function role_add_permission($role_id, $permission_id, $allow = 1) {
		$this->db->insert('system_security.security_role_permission', array('role_id' => $role_id, 'permission_id' => $permission_id, 'allow_deny' => $allow));
		return $this->db->insert_id();
	}
	
	/**
	 * Enter description here ...
	 * @param int $role_id
	 * @param int $permission_id
	 * @param int $allow
	 */
	function update_role_permision() {
		$user_role = $this->get_role_permission($_POST['role_id']);
		foreach ($user_role->result() as $role) {
			
			$this->db->where('entry_id', $role->entry_id);
			$list = $_POST['role_permission'];
			$allow = isset($list[$role->entry_id]) ? 1 : 0;
			$this->db->update('system_security.security_role_permission', array('allow_deny' => $allow));
		}
	}
	
	/**
	 * Enter description here ...
	 */
	function save_permission() {
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('name', lang('label_name'), 'trim|required');
		$this->form_validation->set_rules('module', lang('label_module'), 'trim|required');
		$this->form_validation->set_rules('class', lang('label_class'), 'trim|required');
		$this->form_validation->set_rules('function', lang('label_function'), 'trim|required');
			
		switch ($_POST['mode']) {
			case 'add':
				unset($_POST['mode']);
				if ($this->form_validation->run() != FALSE){//	echo 'valid';
					$this->add_permission();
					set_success_message(sprintf(lang('success_add'), 'permission'));
					redirect('system/security');
					exit;
				}
			break;
			case 'edit':
				unset($_POST['mode']);
				if ($this->form_validation->run() != FALSE){//	echo 'valid';
					$this->update_permission();
					set_success_message(sprintf(lang('success_edit'), 'permission'));
				}
			break;
		}
	}
	
	/**
	 * @return unknown_type
	 */
	function add_permission(){
		
		if(isset($_POST['btnSave'])) {
			unset($_POST['btnSave']);
		}
		
		$this->db->insert('system_security.security_permission', $_POST);
		return $this->db->insert_id();
	}
	
	/**
	 * @return unknown_type
	 */
	function update_permission(){
		
		if(isset($_POST['btnSave'])) {
			unset($_POST['btnSave']);
		}
		
		$this->db->where('permission_id', $_POST['permission_id']);
		unset($_POST['permission_id']);
		$this->db->update('system_security.security_permission', $_POST);
	}
	
    /**
     * Enter description here ...
     */
    function delete_permission() {
		$this->db->where('permission_id', $_POST['id']);
		$this->db->delete('system_security.permission');
		echo "{error: '',msg: '" . sprintf(lang('success_delete'), 'permission') . "'}";
    }
    
	/**
	 * @param $user_id
	 * @return Object user data
	 */
	function get_user($user_id) {
		$sql = "SELECT u.*, o.organization_name FROM system_security.users u 
			 LEFT JOIN system_security.organizations o ON(o.organization_id = u.organization_id) 
				 WHERE u.user_id = '$user_id' ";
		return $this->db->query($sql);
	}
	
	/**
	 * @param $email
	 * @return Object user data
	 */
	function get_user_by_email($email) {
		$sql = "SELECT u.*, o.organization_name FROM system_security.users u 
			 LEFT JOIN system_security.organizations o ON(o.organization_id = u.organization_id) 
				 WHERE u.email = '$email' ";
		return $this->db->query($sql);
	}

	/**
	 * Function to check if email active
	 *
	 * @param String $email
	 * @return boolean
	 */
	function get_forgotten_user($email) {
		 
		$user = $this->db->get_where('system_security.users', array('active' => 1, 'email' => $email));
		if($user->num_rows() < 1) {
			return FALSE;
		} else {
			return $user->row();
		}
	}
	
	/**
	 * @param unknown $key
	 */
	function get_users_autocomplete($key) {
		$sql = "SELECT users.user_id AS id, user_name AS value, email, phone_home, phone_mobile, address1, address2, city, zip, profile_picture, role_id, active, sex 
		FROM users 
		WHERE active = 1 AND lower(user_name) LIKE '%$key%' ORDER BY user_name ";
		$list = $this->db->query($sql);
		
		return json_encode($list->result());
	}
	
	/**
	 * Enter description here ...
	 */
	function save_user() {
		$this->load->helper('form');
//		var_dump($_POST); exit;
		if(!empty($_FILES['profile_picture']['name'])) {
			
			$config_file['upload_path'] 	= 'assets/media/photo/';
			$config_file['encrypt_name'] 	= TRUE;
			$config_file['max_size'] 		= 20000;
			$config_file['allowed_types'] 	= 'jpg|jpeg|png';
			$this->load->library('upload', $config_file);
			if (!$this->upload->do_upload('profile_picture')) {

				set_error_message($this->upload->display_errors());
			} else {
				$file = $this->upload->data();
				
				$config['image_library'] 	= 'gd2';
				$config['source_image'] 	= $file['full_path'];
				$config['create_thumb'] 	= TRUE;
				$config['maintain_ratio'] 	= TRUE;
				$config['width']         	= 100;
				
				$this->load->library('image_lib', $config);
				
				$this->image_lib->resize();
				
				$_POST['profile_picture'] = '/lx_media/photo/' . $file['file_name'];
//				$this->db->insert('users', array('profile_picture' => $url_path . '/profile/' . $file['file_name']), array('user_id' => $user_id));
			}
		}

		unset($_POST['data_state']);

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_name', 'User Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('role_id', 'Role_id', 'trim|required');
		
		$check_email = $this->check_email_exist($_POST['email']);
		
		if ($check_email == 0) {
			switch ($_POST['mode']) {
				case 'add':
					unset($_POST['mode']);
					$orig_password = $_POST['password'];
					$this->form_validation->set_rules('password', 'Password', 'trim|required|md5');
					$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|matches[password]|md5');
	// 				$this->form_validation->set_rules('external_id', 'Employee ID', 'trim|required|callback_id_exist_check');
					$this->db->query('SET search_path TO system_security');
	//				$this->form_validation->set_rules('external_id', 'Employee ID', 'trim|required|is_unique[users.external_id]');
					
					if ($this->form_validation->run($this) != FALSE) {//	echo 'valid';
						
						if(isset($_POST['btnSave'])) {
							unset($_POST['btnSave']);
						}
						
						if(isset($_POST['conf_password'])) {
							unset($_POST['conf_password']);
						}
		
						$_POST['created_id'] 		= get_user_id();
						$activation_code 			= $this->_suggest_password();
						$_POST['active'] 			= 0;
						$_POST['activation_code'] 	= $activation_code;
						$_POST['user_id'] = $user_id = generate_unique_id();
						$users = $_POST;

						$this->db->insert('system_security.users', $users);

						$subject = 'Account Activation';
						$body = 'Your login account name is <strong>' . ($_POST['user_name']) . '</strong><br>
								and your temporary password is \'<strong>' . $orig_password . '</strong>\'
								Please activate your account here <a href="' . site_url('login/activation/' . $activation_code) . '">' . $activation_code . '</a>, and click "Change Password" to change your password and "Update" accordingly';
							
						$this->_send_mail_notification($_POST['email'],  $subject, $body, array());
						
						set_success_message('Successfully Add User');
						redirect('auth/user/user_edit/' . $user_id);
						exit;
					} else {
						set_error_message(validation_errors());
					}
				break;
				case 'edit':
					unset($_POST['mode']);
					unset($_POST['password']);
					unset($_POST['conf_password']);
					
					if ($this->form_validation->run() != FALSE){//	echo 'valid';
						
						if(isset($_POST['btnSave'])) {
							unset($_POST['btnSave']);
						}
						
						if(!isset($_POST['active'])) {
							$_POST['active'] = 0;
						}
						
						$user_id = $_POST['user_id'];
						$this->db->where('user_id', $user_id);
						unset($_POST['user_id']);
						$_POST['modified_id'] = get_user_id();
						$_POST['modified_time'] = date('Y-m-d H:i:s');
				
						$users = $_POST;
						$this->db->update('system_security.users', $users);
						
						set_success_message('Successfully Update User');
						redirect('auth/user/user_edit/' . $user_id);
						exit;
					} else {
						set_error_message(validation_errors());
					}
				break;
			}
		}else {
			$qry_user = $this->get_user_by_email($_POST['email']);
			$row_user = $qry_user->row();
			if (isset($row_user)){
				$user_name = $row_user->user_name;
				$user_nip = $row_user->external_id;
			}
			
			set_error_message('Email sudah digunakan oleh '.$user_name.' NIP: '.$user_nip);
		}
	}

	function update_profile_pic() {
		$return = array('error' => '', 'message' => '', 'execute' => '');
	
		if(!empty($_FILES['profile_picture']['name'])) {
				
			$config_file['upload_path'] = 'assets/media/photo/';
			$config_file['encrypt_name'] = TRUE;
			$config_file['max_size'] = 400;
			$config_file['allowed_types'] = 'jpg|jpeg|png';
			$this->load->library('upload', $config_file);
			if (!$this->upload->do_upload('profile_picture')) {
				$return['error'] = 1;
				$return['message'] = $this->upload->display_errors();
	
			} else {
				$file = $this->upload->data();
				$profile_picture = '/lx_media/photo/' . $file['file_name'];
	
				$this->_resize_lo('assets/media/photo/' . $file['file_name'], 'assets/media/photo/' . $file['file_name'], 128, 128);
	
				$this->db->update('system_security.users', array('profile_picture' => $profile_picture), array('user_id' => $_POST['user_id']));
	
// 				$this->lx->session_save_enc("photo", $profile_picture);
	
				$return['message'] = 'Foto berhasil diupdate';
				$return['src'] = $profile_picture;
				$return['path'] = $file['full_path'];
			}
		} else {
			$return['error'] = 1;
			$return['message'] = 'No File selected.';
	
		}
	
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	
	}
	
	function update_profile() {
		$return = array('error' => '', 'message' => '', 'execute' => '');
		$user_id = $_POST['user_id'];
		unset($_POST['user_id']);
	
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('user_name', 'User Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		if(!isset($_POST['active'])) {
			$_POST['active'] = 0;
		}
		
		$check_email = $this->check_email_exist($_POST['email']);
		$check_email_user = $this->check_email_exist_in_user($_POST['email'], $user_id);
		
		if ($check_email_user == 0){
			if($check_email == 0) {
				if ($this->form_validation->run() != FALSE) {//	echo 'valid';
			
					$this->db->update('system_security.users', $_POST, array('user_id' => $user_id));
			
					$return['message'] = 'Profile berhasil diupdate';
			
				} else {
					$return['error'] = 1;
					$return['message'] = validation_errors();
				}
			}else {
				$qry_user = $this->get_user_by_email($_POST['email']);
				$row_user = $qry_user->row();
				if (isset($row_user)){
					$user_name = $row_user->user_name;
					$user_nip = $row_user->external_id;
				}
				
				$return['message'] = 'Email sudah digunakan oleh '.$user_name.' NIP: '.$user_nip;
			}
		}else if ($check_email_user == 1) {
			if ($this->form_validation->run() != FALSE) {//	echo 'valid';
			
				$this->db->update('system_security.users', $_POST, array('user_id' => $user_id));
		
				$return['message'] = 'Profile berhasil diupdate';
		
			} else {
				$return['error'] = 1;
				$return['message'] = validation_errors();
			}
		}		
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * @param $user_id
	 * @return unknown_type
	 */
	function delete_user($user_id = NULL) {
		if($user_id == NULL && isset($_POST['user_id'])) {
			$user_id = $_POST['user_id'];
			$this->db->delete('system_security.users', array('user_id' => $user_id));
			$this->db->delete('system_security.users_structure', array('user_id' => $user_id));
			$return = array('error' => '', 'msg' => sprintf(lang('success_delete'), $user_id . ' user'));
		} else {
			$return = array('error' => '1', 'msg' => 'Delete failed!');
		}
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * @return Process
	 */
	function update_password() {
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|md5|callback_old_password_check');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|md5');
		$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|matches[password]|md5');
		
		if ($this->form_validation->run() != FALSE){//	echo 'valid';
			$user_id = $this->input->post('user_id');
			
			$this->db->update('system_security.users', array('password' => $_POST['password']), array('user_id' => $user_id));
			
			set_success_message('Successfully Update User Password');
			redirect('auth/login/logout');
			exit;
		} else {
			set_error_message(validation_errors());
		}
	
	}

	/**
	 * @return Process
	 */
	function update_password_adm() {
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('password', 'Password', 'trim|required|md5');
		$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|matches[password]|md5');
		
		if ($this->form_validation->run() != FALSE){//	echo 'valid';
			$user_id = $this->input->post('user_id');
			
			$this->db->update('system_security.users', array('password' => $_POST['password']), array('user_id' => $user_id));
			
			set_success_message('Successfully Update User Password');
			redirect('auth/login/logout');
			exit;
		} else {
			set_error_message(validation_errors());
		}
	
	}
	
	/**
	 *
	 */
	function reset_password($id) {
		if ($id == NULL && isset($_POST['user_id'])) {
			$id = $_POST['user_id'];
		}else {
			$id = $id; 
		}

		$this->_reset_password($id);
	}
	

	/**
	 *
	 */
	function resend_activation() {
		
		$result = $this->get_user($_POST['user_id']);
		if($result->num_rows() > 0) {
			
			$user = $result->row();
			$pass = $this->_suggest_password();

			$subject = 'Account re-Activation';
			$body = 'Your login account name is <strong>' . ($user->user_name) . '</strong><br>
							and your temporary password is \'<strong>' . $pass . '</strong>\'
							Please activate your account here <a href="' . site_url('login/activation/' . $user->activation_code) . '">' . $user->activation_code . '</a>, and click "Change Password" to change your password and "Update" accordingly';
			$this->_send_mail_notification($user->email,  $subject, $body, array());
			
			$this->db->update('system_security.users', array('active' => 0, 'password' => $this->encrypt->get_key($pass)), array('user_id' => $_POST['user_id']));
			
			$this->output->set_output('Successfully send activation mail');
		} else {
			$this->output->set_output('Undefined User data');
		}
	}
	
	/**
	 * @param $user_id
	 * @return Object user data
	 */
	function get_user_structure($user_id) {
		$sql = "SELECT os.organization_structure_id, os.unit_name, us.*, dir.unit_name AS instansi, os.unit_code, us.pangkat
				  FROM system_security.organization_structure os
				  JOIN system_security.users_structure us ON(us.organization_structure_id = os.organization_structure_id AND us.status = 1)
			 	  JOIN system_security.organization_structure dir ON(dir.unit_code = ( SUBSTRING(os.unit_code FROM 0 FOR 5) || '0101'))
					WHERE us.user_id = '$user_id'";
		return $this->db->query($sql);
	}
	
	function save_user_structure() {
		$return = array('error' => '', 'message' => '', 'execute' => '');
		
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('organization_structure_id', 'Posisi', 'trim|required');
		if ($this->form_validation->run() != FALSE) {//	echo 'valid';
	
			$data = array();
			$data['organization_structure_id'] = $_POST['organization_structure_id'];
			$data['user_id'] = $_POST['user_id'];
			$data['jabatan'] = $_POST['jabatan'];
			$data['pangkat'] = $_POST['pangkat'];
			if($_POST['jabatan'] == 'Staff') {
				$data['structure_head'] = 0;
			} else {
				$data['structure_head'] = 1;
				$this->db->update('system_security.users_structure', array('jabatan' => 'Staff', 'structure_head' => 0), array('organization_structure_id' => $_POST['organization_structure_id']));
			}
			
			if(isset($_POST['entry_id'])) {
				
				$this->db->update('system_security.users_structure', $data, array('entry_id' => $_POST['entry_id']));
			} else {
				$this->db->insert('system_security.users_structure', $data);
			}
	
			$return['message'] = 'Posisi berhasil diupdate';
	
		} else {
			$return['error'] = 1;
			$return['message'] = validation_errors();
		}
	
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	/**
	 * @param unknown $email
	 */
	function check_email_exist($email) {
		$result = $this->db->get_where('system_security.users', array('email' => $email));
		$num_row = $result->num_rows();
		return $num_row;
	}
	
	/**
	 * @param unknown $email user
	 */
	function check_email_exist_in_user($email, $user_id) {
		$result = $this->db->get_where('system_security.users', array('email' => $email, 'user_id' => $user_id));
		$num_row = $result->num_rows();
		return $num_row;
	}
	
	/**
	 * @param unknown $empl_id
	 */
	function check_empl_exist($empl_id) {
		return $this->db->get_where('system_security.users', array('empl_id' => $empl_id));
	}
	
	/**
	 * @param unknown $key
	 */
	function get_member_autocomplete($key) {
		$sql = "SELECT user_id AS id, external_id, user_name AS value, email FROM system_security.users WHERE active = 1 AND lower(user_name) LIKE '%$key%' ORDER BY user_name ";
		$list = $this->db->query($sql);
		
		return json_encode($list->result());
	}

}
	
/* End of file user_model.php */
/* Location: ./appl/models/user_model.php */