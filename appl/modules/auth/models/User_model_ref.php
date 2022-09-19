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
		$this->db->select("user_id, user_name, users.email, users.phone_mobile, security_role.name AS role")
			->from('users')
			->join('security_role', 'security_role.role_id = users.role_id')
			->order_by('security_role.role_id, user_name');
			
		return $this->db->get();
		
	}

	/**
	 * @return Object user data
	 */
	function get_dashboard_user_list($all = FALSE) {
		$this->db->select("user_id, user_name, users.email, users.phone_mobile, users.profile_picture, users.created_time, security_role.name AS role, unit.blok, unit.no")
		->from('users')
		->join('security_role', 'security_role.role_id = users.role_id')
		->join('unit', 'unit.unit_id = users.unit_id', 'LEFT');
		if(!$all) {
			$this->db->order_by('user_id', 'desc')->limit(8);
		} else {
			$this->db->order_by('unit.blok, unit.no');
		}
			
		return $this->db->get();
	
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
		return $this->db->get('security_role');
		
	}
	
	/**
	 * Enter description here ...
	 */
	function get_role($role_id) {
		return $this->db->get_where('security_role', array('role_id' => $role_id));
		
	}

	/**
	 *
	 */
	function get_user_role($role_id) {
		return $this->db->get_where('users', array('role_id' => $role_id, 'active' => 1));
	
	}

	/**
	 *
	 */
	function get_user_role_permission($permission_id) {
		$sql = "SELECT u.* FROM users u 
				JOIN security_role_permission srp ON(srp.role_id = u.role_id AND srp.allow_deny = 1) 
				WHERE u.active = 1 AND srp.permission_id = $permission_id";
		return $this->db->query($sql);
	
	}
	
	/**
	 * Enter description here ...
	 */
	function get_permission_list() {
		return $this->db->get('security_permission');
		
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
				if ($this->form_validation->run() != FALSE){//	echo 'valid';
					$this->db->insert('security_role', array('name' => $_POST['name']));
					$role_id = $this->db->insert_id();
		
					if(isset($_POST['security_permission'])) {
						foreach($_POST['security_permission'] as $v) {
							$this->db->insert('security_role_permission', array('role_id' => $role_id, 'permission_id' => $v));
						}
					}
					
					set_success_message('Successfully add role');
					redirect('user/role_data/' . $role_id);
					exit;
				}
			break;
			case 'edit':
				unset($_POST['mode']);
				if ($this->form_validation->run() != FALSE){//	echo 'valid';
					$this->db->update('security_role', array('name' => $_POST['name']), array('role_id' => $_POST['role_id']));
					
					$this->db->delete('security_role_permission', array('role_id' => $_POST['role_id']));
					if(isset($_POST['security_permission'])) {
						foreach($_POST['security_permission'] as $v) {
							$this->db->insert('security_role_permission', array('role_id' => $_POST['role_id'], 'permission_id' => $v));
						}
					}
					set_success_message('Successfully edit role');
					redirect('user/role_data/' . $_POST['role_id']);
					exit;
				}
			break;
			case 'delete':
				$this->db->delete('security_role_permission', array('role_id' => $_POST['role_id']));
				$this->db->delete('security_role', array('role_id' => $_POST['role_id']));
				
				set_success_message('Successfully relete role');
				redirect('user/role_permission/');
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
			
			$this->db->delete('security_role_permission', array('role_id' => $role_id));
			$this->db->delete('security_role', array('role_id' => $role_id));
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
		$sql = "SELECT security_permission.*, security_role_permission.entry_id, security_role_permission.allow_deny "
			.  "FROM security_permission "
			.  "LEFT JOIN security_role_permission ON(security_role_permission.permission_id = security_permission.permission_id AND security_role_permission.role_id = $role_id) "
			.  "ORDER BY security_permission.permission_id";
		return $this->db->query($sql);
	}
	
	/**
	 * Enter description here ...
	 * @param int $role_id
	 */
	function get_x_role_permission($role_id) {
		$sql = "SELECT * "
			.  "FROM security_permission "
			.  "WHERE permission_id NOT IN(SELECT permission_id FROM security_role_permission WHERE role_id = $role_id)";
		return $this->db->query($sql);
	}
	
	/**
	 * Enter description here ...
	 * @param int $content_category_id
	 */
	function get_role_content($content_category_id) {
		$sql = "SELECT security_role.*, security_role_content.entry_id, security_role_content.allow_deny "
			.  "FROM security_role "
			.  "LEFT JOIN security_role_content ON(security_role_content.role_id = security_role.role_id AND security_role_content.content_category_id = $content_category_id) "
			.  "WHERE security_role.role_id <> 1";
		return $this->db->query($sql);
	}
	
	/**
	 * Enter description here ...
	 * @param int $role_id
	 * @param int $permission_id
	 * @param int $allow
	 */
	function role_add_permission($role_id, $permission_id, $allow = 1) {
		$this->db->insert('security_role_permission', array('role_id' => $role_id, 'permission_id' => $permission_id, 'allow_deny' => $allow));
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
			$this->db->update('security_role_permission', array('allow_deny' => $allow));
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
		
		$this->db->insert('security_permission', $_POST);
		return $this->db->insert_id();
//		var_dump($_POST);
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
		$this->db->update('security_permission', $_POST);
//		var_dump($_POST);
	}
	
    /**
     * Enter description here ...
     */
    function delete_permission() {
		$this->db->where('permission_id', $_POST['id']);
		$this->db->delete('permission');
		echo "{error: '',msg: '" . sprintf(lang('success_delete'), 'permission') . "'}";
		
    }
    
	/**
	 * @param $user_id
	 * @return Object user data
	 */
	function get_user($user_id) {
		return $this->db->get_where('users', array('user_id' => $user_id));
	}

	/**
	 * @param $user_id
	 * @return Object user data
	 */
	function get_user_view($user_id) {
		$sql = "SELECT u.*, r.name role_name, unit.blok, unit.no FROM users u 
				JOIN security_role r ON(r.role_id = u.role_id) 
				JOIN unit ON(unit.unit_id = u.unit_id) 
				WHERE u.user_id = $user_id ";
		return $this->db->query($sql);
	}

	/**
	 * @param $user_id
	 * @return Object user data
	 */
	function get_user_tagihan($unit_id) {
		$sql = "SELECT tu.*, sv.val jenis_tagihan FROM tagihan_unit tu
				JOIN system_variables sv ON(sv.type = 'payment_type' AND sv.key = tu.tagihan_type) 
				WHERE tu.unit_id = $unit_id ORDER BY tu.tagihan_unit_id";
		return $this->db->query($sql);
	}

	/**
	 * Function to check if email active
	 *
	 * @param String $email
	 * @return boolean
	 */
	function get_forgotten_user($email) {
		 
		$user = $this->db->get_where('users', array('active' => 1, 'email' => $email));
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
		$sql = "SELECT users.user_id AS id, empl_id, nric, user_name AS value, email, hire_date, department_id, phone_home, phone_mobile, address1, address2, city, zip, profile_picture, role_id, active, sex 
		FROM users 
		LEFT JOIN empl_leave ON(empl_leave.user_id = users.user_id)
		WHERE active = 1 AND lower(user_name) LIKE '%$key%' ORDER BY user_name ";
		$list = $this->db->query($sql);
		
		return json_encode($list->result());
	}
	
	function register_user() {
		$this->load->library('form_validation');
		
		$orig_password = $_POST['password'];
		$this->form_validation->set_rules('user_name', 'User Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[conf_password]|md5');
		$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|md5');
		
		if ($this->form_validation->run() != FALSE) {//	echo 'valid';
				
			unset($_POST['btnSave']);
			unset($_POST['conf_password']);
			
			$_POST['created_id'] = get_user_id();
			$activation_code = $this->_suggest_password();
			$_POST['active'] = 0;
			$_POST['activation_code'] = $activation_code;
			
			$this->db->insert('users', $_POST);
			$user_id = $this->db->insert_id();

			$result = $this->db->get_where('unit', array('unit_id' => $_POST['unit_id']));
			$unit = $result->row();

			$sql_check = "SELECT * FROM tagihan_unit WHERE unit_id = " . $_POST['unit_id'];
			$check = $this->db->query($sql_check);
			if($check->num_rows() == 0) {
				$tagihan = array();
				$tagihan['unit_id'] = $_POST['unit_id'];
				if($unit->status == 'Huni') {
					$tagihan['amount'] = 100000;
					$tagihan['tagihan_type'] = 'bulanan_huni';
				} else {
					$tagihan['amount'] = 50000;
					$tagihan['tagihan_type'] = 'bulanan_nonhuni';
				}
				$tagihan['tagihan_month'] = str_pad((date('n') + 1), 2, '0', STR_PAD_LEFT);
				$tagihan['tagihan_year'] = date('Y');
				$this->db->insert('tagihan_unit', $tagihan);
			}
			
			$str = '<h1 style="font: 13px/20px normal Helvetica, Arial, sans-serif;
											color: #444;
											background-color: transparent;
											border-bottom: 1px solid #D0D0D0;
											font-size: 19px;
											font-weight: normal;
											margin: 0 0 14px 0;
											padding: 14px 15px 10px 15px;
											">Welcome to Indo Alam Residence internal system</h1>
							<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">
								Selamat bergabung  ' . $_POST['$orig_password'] . ', silahkan gunakan email ini untuk login ke Internal System Paguyuban Warga Indo Alam Residence.   
								<br>
								Password anda adalah \'<strong>' . $orig_password . '</strong>\'
								Silahkan lakukan aktivasi akun anda melalui link <a href="' . site_url('login/activation/' . $activation_code) . '">' . $activation_code . '</a>
							</p>
							<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">Thank You<br/>
							System Administrator</p>';
				
			$this->_send_mail_notification($_POST['email'], 'Account Activation', $str, array());
		
			set_success_message('Akun Anda berhasil dibuat silahkan cek email anda untuk aktivasi akun.');
			redirect('login');
			exit;
		} else {
			set_error_message(validation_errors());
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
				
//				$config_resize['image_library'] = 'gd';
//				$config_resize['source_image']		= 'assets/media/photo/' . $file['file_name'];
//				$config_resize['maintain_ratio']	= TRUE;
//				$config_resize['width']			= 128;
//				$this->load->library('image_lib', $config_resize);

				$this->db->update('users', array('profile_picture' => $profile_picture), array('user_id' => $_POST['user_id']));

				$this->lx->session_save_enc("photo", $profile_picture);

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
		if ($this->form_validation->run() != FALSE) {//	echo 'valid';

			$this->db->update('users', $_POST, array('user_id' => $user_id));

			$return['message'] = 'Profile berhasil diupdate';

		} else {
			$return['error'] = 1;
			$return['message'] = validation_errors();
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
	/**
	 * Enter description here ...
	 */
	function save_user() {
		$this->load->helper('form');
//		var_dump($_FILES); exit;
		if(!empty($_FILES['profile_picture']['name'])) {
			
			$config_file['upload_path'] = 'assets/media/photo/';
			$config['encrypt_name'] = TRUE;
			$config_file['max_size'] = 20000;
			$config_file['allowed_types'] = 'jpg|jpeg|png';
			$this->load->library('upload', $config_file);
			if (!$this->upload->do_upload('profile_picture')) {

				set_error_message($this->upload->display_errors());
			} else {
				$file = $this->upload->data();
				$_POST['profile_picture'] = '/lx_media/photo/' . $file['file_name'];
//				$this->db->insert('users', array('profile_picture' => $url_path . '/profile/' . $file['file_name']), array('user_id' => $user_id));
			}
		}
		unset($_POST['data_state']);

		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('user_name', 'User Name', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('role_id', 'Role_id', 'trim|required');
		
		switch ($_POST['mode']) {
			case 'add':
				unset($_POST['mode']);
				$this->form_validation->set_rules('empl_id', 'Employee ID', 'trim|required|callback_id_exist_check');
				$orig_password = $_POST['password'];
				$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[conf_password]|md5');
				$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|md5');
				
				if ($this->form_validation->run() != FALSE) {//	echo 'valid';
					
					if(isset($_POST['btnSave'])) {
						unset($_POST['btnSave']);
					}
					
					if(isset($_POST['conf_password'])) {
						unset($_POST['conf_password']);
					}
					$_POST['created_id'] = get_user_id();
					$activation_code = $this->_suggest_password();
					$_POST['active'] = 0;
					$_POST['activation_code'] = $activation_code;
					
					$users = $_POST;
					unset($users['leave_data']);

					$this->db->insert('users', $users);
					$user_id = $this->db->insert_id();

					list($hire_year, $hire_month, $hire_date) = explode('-', $_POST['hire_date']);

					$str = '<h1 style="font: 13px/20px normal Helvetica, Arial, sans-serif;
											color: #444;
											background-color: transparent;
											border-bottom: 1px solid #D0D0D0;
											font-size: 19px;
											font-weight: normal;
											margin: 0 0 14px 0;
											padding: 14px 15px 10px 15px;
											">Welcome to Mindmatics internal system</h1> 
							<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">Your login account name is <strong>' . ($hire_year . $_POST['department_id'] . $_POST['empl_id']) . '</strong><br>
							and your temporary password is \'<strong>' . $orig_password . '</strong>\'
							Please activate your account here <a href="' . site_url('login/activation/' . $activation_code) . '">' . $activation_code . '</a>, and click "Change Password" to change your password and "Update" accordingly</p>
							<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">Thank You<br/>
							System Administrator</p>';
					
					$this->_send_mail_notification($_POST['email'], 'Account Activation', $str, array());
					
					if(isset($_POST['leave_data']) && $_POST['leave_data']['last_leave_date_from'] != '-' && $_POST['leave_data']['last_leave_date_from'] != '0000-00-00') {
						$_POST['leave_data']['user_id'] = $user_id;
						$this->save_leave($_POST['leave_data']);
					}

					set_success_message('Successfully Add User');
    				redirect('user/user_edit/' . $user_id);
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
					unset($users['leave_data']);

					$this->db->update('users', $users);
					
					if(isset($_POST['leave_data']) && $_POST['leave_data']['last_leave_date_from'] != '-' && $_POST['leave_data']['last_leave_date_from'] != '0000-00-00') {
						$_POST['leave_data']['user_id'] = $user_id;
						$this->save_leave($_POST['leave_data']);
					}
					
					set_success_message('Successfully Update User');
    				redirect('user/user_edit/' . $user_id);
					exit;
				} else {
					set_error_message(validation_errors());
				}
			break;
		}
	}

	/**
	 * @param $user_id
	 * @return unknown_type
	 */
	function delete_user($user_id = NULL) {
		if($user_id == NULL && isset($_POST['user_id'])) {
			$user_id = $_POST['user_id'];
			$this->db->delete('users', array('user_id' => $user_id));
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
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|matches[conf_password]|md5');
		$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|md5');
		
		if ($this->form_validation->run() != FALSE){//	echo 'valid';
			$user_id = $this->input->post('user_id');
			
			$this->db->update('users', array('password' => $_POST['password']), array('user_id' => $user_id));
			
			set_success_message('Successfully Update User Password');

			redirect('login/logout');
			exit;
		} else {
			set_error_message(validation_errors());
		}
		
	}
	
	/**
	 *
	 */
	function reset_password($id) {
		$this->_reset_password($id);
	}
	

	/**
	 *
	 */
	function resend_activation() {
		
		$result = $this->get_user($_POST['user_id']);
		if($result->num_rows() > 0) {
			
			$user = $result->row();
			list($hire_year, $hire_month, $hire_date) = explode('-', $user->hire_date);
			
			$pass = $this->_suggest_password();

			$str = '<h1 style="font: 13px/20px normal Helvetica, Arial, sans-serif;
											color: #444;
											background-color: transparent;
											border-bottom: 1px solid #D0D0D0;
											font-size: 19px;
											font-weight: normal;
											margin: 0 0 14px 0;
											padding: 14px 15px 10px 15px;
											">Welcome to Mindmatics internal system</h1>
							<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">Your login account name is <strong>' . ($hire_year . $_user->department_id . $user->empl_id) . '</strong><br>
							and your temporary password is \'<strong>' . $pass . '</strong>\'
							Please activate your account here <a href="' . site_url('login/activation/' . $user->activation_code) . '">' . $user->activation_code . '</a>, and click "Change Password" to change your password and "Update" accordingly</p>
							<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">Thank You<br/>
							System Administrator</p>';
				
			$this->_send_mail_notification($user->email, 'Account re-Activation', $str, array());
			
			$this->db->update('users', array('active' => 0, 'password' => $this->encrypt->get_key($pass)), array('user_id' => $_POST['user_id']));
			
			$this->output->set_output('Successfully send activation mail');
		} else {
			$this->output->set_output('Undefined User data');
		}
	}
	
	/**
	 * @param unknown $email
	 */
	function check_email_exist($email) {
		return $this->db->get_where('users', array('email' => $email));
	}
	
	/**
	 * @param unknown $empl_id
	 */
	function check_empl_exist($empl_id) {
		return $this->db->get_where('users', array('empl_id' => $empl_id));
	}
	
	/**
	 * @param unknown $key
	 */
	function get_member_autocomplete($key) {
		$sql = "SELECT user_id AS id, external_id, user_name AS value, email FROM users WHERE active = 1 AND lower(user_name) LIKE '%$key%' ORDER BY user_name ";
		$list = $this->db->query($sql);
		
		return json_encode($list->result());
	}

}
	
/* End of file user_model.php */
/* Location: ./appl/models/user_model.php */