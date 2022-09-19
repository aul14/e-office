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
 * @filesource Lx.php
 * @copyright Copyright 2011-2015, laxono.us.
 * @author budi.lx
 * @package 
 * @subpackage	
 * @since Aug 14, 2015
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Lx {

	var $auth_namespace = "lx";
	var $field_username = "u.email";  //you can switch with email if login is email based (i.e. gmail login)
	var $password_encrypted = false;
	var $initialized = false;

	/**
	 * 
	 */
	function __construct() {
		$this->ci =& get_instance();
		$this->session =& $this->ci->session;
		$this->config =& $this->ci->config;
	}
	
	/**
	 * 
	 */
	function init() {
		if (!$this->initialized) {
			//load needed libraries incase unloaded
			$this->ci->load->database();
			$this->ci->load->library('encrypt');
			$this->ci->load->helper('cookie');
			$this->initialized = true;
		}
		$this->db =& $this->ci->db;
	}
	
	/**
	 * Enter description here ...
	 * @return string
	 */
	function get_auth_namespace() {
		return $this->auth_namespace;
	}
	
	function generate_unique_id($pre, $suf) {
		$this->init();
		
		$result = $this->db->query('SELECT (public.gen_random_uuid())::character varying(36) uuid');
		
		return $result->row()->uuid;
	}
	
	/**
	 * Try to validate a login, set user session data, and optionally store a persistence cookie (to autologin)
	 *
	 * @param  string  $username  Username to login
	 * @param  string  $password  Password to match user
	 * @param  bool  $session (true)  Set session data here. False to set your own
	 * @param  int   $max_role  is the max role_id needed to save cookie (1: save for all users, 3: only for web,
	 */
	function trylogin($username, $password, $cookie = false, $max_role = 1) {
		$this->init();
		
		// Check details in DB
 		$password_hash = ($this->password_encrypted)? $this->ci->encrypt->hash($password, 'md5'): $password;
// 		$this->db->join('system_security.security_role r', 'r.role_id = u.role_id');
// 		$this->db->join('system_security.users_structure us', 'us.user_id = u.user_id', 'LEFT');
// 		$this->db->join('system_security.organization_structure os', 'os.organization_structure_id = us.organization_structure_id', 'LEFT');
// 		$this->db->join('system_security.organizations o', 'o.organization_id = u.organization_id', 'LEFT');
// 		$query = $this->db->get_where('system_security.users u', array($this->field_username => $username, 'password' => $password_hash, 'u.active' => 1));
		
		$sql = "SELECT u.*, r.name, us.jabatan, us.structure_head, os.organization_structure_id, os.unit_code, os.unit_name, os.parent_id, os.level, os.abv, os.no_surat_internal, o.organization_id, o.organization_name
				  FROM system_security.users u 
				  JOIN system_security.security_role r ON(r.role_id = u.role_id)  
			 LEFT JOIN system_security.users_structure us ON(us.user_id = u.user_id) 
			 LEFT JOIN system_security.organization_structure os ON(os.organization_structure_id = us.organization_structure_id)
			 LEFT JOIN system_security.organizations o ON(o.organization_id = u.organization_id)  
				 WHERE " . $this->field_username . " = '$username' AND password = '$password_hash' AND u.active = 1 ";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$row = $query->row();
			
			// Set session data array
			$this->session_save_enc("user_name", $row->user_name);
			$this->session_save_enc("email", $row->email);
			$this->session_save_enc("role_id", $row->role_id);
			$this->session_save_enc("role_name", $row->name);
			$this->session_save_enc("user_id", $row->user_id);
 			$this->session_save_enc("nip", $row->external_id);
			$this->session_save_enc("organization_id", $row->organization_id);
			$this->session_save_enc("organization_name", $row->organization_name);
			$this->session_save_enc("unit_id", $row->organization_structure_id);
			$this->session_save_enc("unit_code", $row->unit_code);
			$this->session_save_enc("unit_name", $row->unit_name);
			$this->session_save_enc("unit_abv", $row->abv);
			$this->session_save_enc("unit_no_surat_internal", $row->no_surat_internal);
			$this->session_save_enc("unit_parent_id", $row->parent_id);
			$this->session_save_enc("unit_level", $row->level);
			$this->session_save_enc("structure_head", $row->structure_head);
			$this->session_save_enc("jabatan", $row->jabatan);
			$this->session_save_enc("ip_address", $this->ci->input->ip_address());
			$this->session_save_enc("last_login", $row->last_login);
			$this->session_save_enc("photo", $row->profile_picture);
			
			// update last login datetime
			$this->db->update('system_security.users u', array('last_login' => date("Y-m-d H:i:s")), array($this->field_username => $username));
	
			if( $cookie == TRUE && $max_role <= $row->role_id) {
				$this->_set_cookie($username, $password);
			}
			return TRUE;
	
		} else {
			return FALSE;
		}
	}

	function activate($activation_code) {
		$this->init();

		$this->db->join('system_security.security_role r', 'r.role_id = u.role_id');
		$this->db->join('system_security.organizations o', 'o.organization_id = u.organization_id', 'LEFT');
		$query = $this->db->get_where('system_security.users u', array('activation_code' => $activation_code, 'u.active' => 0));
	
		if($query->num_rows() > 0) {
			$row = $query->row();
			
			// Set session data array
			$this->session_save_enc("user_name", $row->user_name);
			$this->session_save_enc("email", $row->email);
			$this->session_save_enc("role_id", $row->role_id);
			$this->session_save_enc("role_name", $row->name);
			$this->session_save_enc("user_id", $row->user_id);
			$this->session_save_enc("report_to", $row->report_to);
			$this->session_save_enc("organization_id", $row->organization_id);
			$this->session_save_enc("organization_name", $row->organization_name);
			$this->session_save_enc("ip_address", $this->ci->input->ip_address());
			$this->session_save_enc("last_login", $row->last_login);
			$this->session_save_enc("photo", $row->profile_picture);
			
			$this->db->update('system_security.users u', array('u.active' => 1, 'activation_code' => '', 'last_login' => date("Y-m-d H:i:s")), array('activation_code' => $activation_code));

			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Logout user and reset session data
	 */
	function logout() {
	
		$this->_unset_cookie();
		$this->session->sess_destroy();
	}

	/**
	 * @param unknown $var
	 * @param unknown $val
	 */
	function session_save_enc($var, $val) {
		$ci =& get_instance();
		$val = $ci->encrypt->encode($val, $ci->config->item("encryption_key"));
		$this->session->set_userdata($var, $val);
	}
	
	/**
	 * @param string $var
	 * @return unknown
	 */
	function session_get_dec($var) {
		$ci =& get_instance();
		$val = $this->session->userdata($var);
		return $ci->encrypt->decode($val, $ci->config->item("encryption_key"));
		
	}
	
	/**
	 * @param unknown $username
	 * @param unknown $password
	 */
	function _set_cookie($username, $password) {
		$this->init();
	
		$auth_fields = array();
		$auth_fields['username'] = $username;
		$auth_fields['password'] = $password;
		$auth_data = serialize($auth_fields);
	
		$cookie = array('name' => $this->auth_namespace, 'value' => $auth_data, 'expire' => $this->cookie_expiration);
		set_cookie($cookie);
	}
	
	/**
	 * 
	 */
	function _unset_cookie() {
		$this->init();
		delete_cookie($this->auth_namespace);
	}

	/**
	 * Check stored user_id  (user is logged)
	 *
	 * @return  bool  user is logged
	 */
	function is_logged() {
		$this->init();
		$user_id 	= $this->session_get_dec('user_id');
		$ip_address = $this->session_get_dec('ip_address');
		//		log_message('info', "Logged : $user_id - $ip_address");
		//		log_message('info', "SESSION : " . $_SESSION['grease_tune']['user_id']);
	
		log_message('info', "login IP : $ip_address /// user IP : " . $this->ci->input->ip_address());
		if(!$user_id || !$ip_address) return false; //no valid session available;
	
		if($this->ci->config->item('sess_match_ip')) {
			if($ip_address != $this->ci->input->ip_address()) {//hacking attemp;
				$this->logout();
				return false;
			}
		}
		return isset($user_id);
	}
	
	
	/**
	 * Get stored user role
	 *
	 * @return  int role_id
	 */
	function get_role() {
		return $this->session_get_dec('role_id');
	}
	
	/**
	 * Get stored user_id
	 *
	 * @return  int user_id
	 */
	function get_user_id() {
		return $this->session_get_dec('user_id');
	}
	
	/**
	* Get stored user data 
	*
	* @return  mixed an array of logged user data, or the single value for the given key (i.e. get_user_data("user_name"))
	*/
	function get_user_data($key=null) {
		return $this->session_get_dec($key);
	}

	/**
	*
	*
	*/
	function get_user_modify($id=null) {
		$query = $this->db->query("SELECT DISTINCT u.user_name 
								FROM surat s
								INNER JOIN system_security.users u ON (s.modified_id = u.user_id)
								WHERE s.modified_id = '$id' ");
		
		if ($query->num_rows()) {
			$row = $query->row();
			$user_modify = $row->user_name;
		}else {
			$user_modify = '';
		}

		return $user_modify;
	}
	
	/**
	 * Check user role
	 *
	 * @param   int  $role_id
	 * @param   bool $strict ("root" is also "admin", "operator" etc..)
	 * @return  bool user has the role_id (or, if strict==false) or his role is more important
	 */
	function check_role($role_id, $strict=false) {
		$this->init();
	
		//not logged
		if (!$this->is_logged())  return false;
		$rid = $this->session_get_dec('role_id');
		if (($strict && ($rid == $role_id)) || (!$strict && ($rid <= $role_id))) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Checks if user exist
	 *
	 * @param  string  $username
	 * @return  bool  user exist
	 */
	function user_exists($username) {
		$this->init();
	
		$this->db->select("person_id");
		$this->db->from("person");
		$this->db->where($this->field_username, $username);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	
	/**
	 * Check if account is active
	 *
	 * @param  string  $username
	 * @return  bool  active
	 */
	function is_active($username) {
		$this->init();
	
		$this->db->select("active");
		$this->db->from("person");
		$this->db->where($this->field_username, $username);
		$this->db->where("active", 1);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	/**
	 * Check if user has a permission
	 *
	 * @param  int  $permission_id
	 * @return  bool  has permission
	 */
	function has_permission($permission_id) {
		$this->init();
	
		//not logged
		if (!$this->is_logged())  return false;
	
		//is root
		if ($this->check_role(1)) return true;
	
		//security
		$role = $this->db->escape($this->get_role());
		$permission = $this->db->escape($permission_id);
		$uid = $this->db->escape($this->get_user_id());
	
		//role-permission
		$role_permission = false; //by default we assume that it's not allowed
		$query = $this->db->query("SELECT allow_deny FROM system_security.security_role_permission WHERE (role_id=$role AND permission_id=$permission) OR (role_id=$role AND permission_id=1)");
	
		if ($query->num_rows()) {
			$row = $query->row();
			$role_permission = (bool)$row->allow_deny;
		}
	
		//user-permission (allow-deny)
		$query = $this->db->query("SELECT allow_deny FROM system_security.security_user_permission WHERE (user_id=$uid AND permission_id=$permission) OR (user_id=$uid AND permission_id=1)");
		if ($query->num_rows()) {
			$row = $query->row();
			$user_permission = (bool)$row->allow_deny;
			return $user_permission;
		}
	
		return $role_permission;
	
	}
	
	function user_with_permission($permission_id) {

		$sql = "SELECT u.* FROM system_security.users u
				JOIN system_security.security_role_permission rp ON(rp.role_id = u.role_id AND u.active = 1 AND rp.allow_deny = 1)
				WHERE rp.permission_id = $permission_id ";
		return $this->db->query($sql);
	}
	
	/**
	 * @param unknown $unit_id
	 */
	function user_in_unit($unit_id) {
		$sql = "SELECT u.* FROM system_security.users u
				JOIN system_security.users_structure us ON(us.user_id = u.user_id AND us.status = 1 AND u.active = 1)
				WHERE us.organization_structure_id = $unit_id";
		return $this->db->query($sql);
	}
	
	/**
	 * Check if user has a permission
	 *
	 * @param  string  $type
	 * @return  int  id
	 */
	function set_annual_sequence($type) {
		$this->init();
		$result = $this->db->get_where('system_security.seq_year', array('organization_id' => get_user_data('organization_id'), 'year' => date('Y'), 'seq_type' => $type));
		
		$number = 1;
		if($result->num_rows() > 0) {
			$data = $result->row();
			$number = $data->seq + 1;
			$this->db->update('system_security.seq_year', array('seq' => $number), array('organization_id' => get_user_data('organization_id'), 'year' => date('Y'), 'seq_type' => $type));
		} else {
			$this->db->insert('system_security.seq_year', array('organization_id' => get_user_data('organization_id'), 'year' => date('Y'), 'seq_type' => $type, 'seq' => $number));
		}
		
		return $number;
	}

	/**
	 * Check if user has a permission
	 *
	 * @param  string  $type
	 * @return  int  id
	 */
	function set_monthly_sequence($type) {
		$this->init();
		$result = $this->db->get_where('system_security.seq_month', array('organization_id' => get_user_data('organization_id'), 'year_month' => date('Y-m'), 'seq_type' => $type));
	
		$number = 1;
		if($result->num_rows() > 0) {
			$data = $result->row();
			$number = $data->seq + 1;
			$this->db->update('system_security.seq_month', array('seq' => $number), array('organization_id' => get_user_data('organization_id'), 'year_month' => date('Y-m'), 'seq_type' => $type));
		} else {
			$this->db->insert('system_security.seq_month', array('organization_id' => get_user_data('organization_id'), 'year_month' => date('Y-m'), 'seq_type' => $type, 'seq' => $number));
		}
	
		return $number;
	}
	
	/**
	 * Check if user has a permission
	 *
	 * @param  string  $type
	 * @return  int  id
	 */
	function set_daily_sequence($type) {
		$this->init();
		$result = $this->db->get_where('system_security.seq_day', array('organization_id' => get_user_data('organization_id'), 'date' => date('Y-m-d'), 'seq_type' => $type));
		
		$number = 1;
		if($result->num_rows() > 0) {
			$data = $result->row();
			$number = $data->seq + 1;
			$this->db->update('system_security.seq_day', array('seq' => $number), array('organization_id' => get_user_data('organization_id'), 'date' => date('Y-m-d'), 'seq_type' => $type));
		} else {
			$this->db->insert('system_security.seq_day', array('organization_id' => get_user_data('organization_id'), 'date' => date('Y-m-d'), 'seq_type' => $type, 'seq' => $number));
		}
		
		return $number;
	}
	

	function roman_numerals($num){
		$n = intval($num);
		$res = '';
	
		/*** roman_numerals array  ***/
		$roman_numerals = array(
				'M'  => 1000,
				'CM' => 900,
				'D'  => 500,
				'CD' => 400,
				'C'  => 100,
				'XC' => 90,
				'L'  => 50,
				'XL' => 40,
				'X'  => 10,
				'IX' => 9,
				'V'  => 5,
				'IV' => 4,
				'I'  => 1);
	
		foreach ($roman_numerals as $roman => $number){
			/*** divide to get  matches ***/
			$matches = intval($n / $number);
	
			/*** assign the roman char * $matches ***/
			$res .= str_repeat($roman, $matches);
	
			/*** substract from the number ***/
			$n = $n % $number;
		}
	
		/*** return the res ***/
		return $res;
	}
	
	function org_struc_abv($organization_structure_id) {
		$this->init();
		$result = $this->db->get_where('system_security.organization_structure', array('organization_id' => get_user_data('organization_id'), 'organization_structure_id' => $organization_structure_id));
		if($result->num_rows() > 0) {
			$data = $result->row();
			return $data->abv;
		} else {
			return '';
		}
		/*** return the res ***/
		
	}
	
	/**
	 * @param unknown $format
	 * @param unknown $value
	 * @return string
	 */
	function number_generator($format) {
		$part = json_decode($format, TRUE);
		$return = '';
		foreach ($part as $field) {
			switch($field['function']) {
				case 'date' :
					//$return .= date($field['value']);
				break;
				case 'date_roman' :
					$return .= $this->roman_numerals(date($field['value']));
				break;
				case 'annual_seq' :
					list($seq_type, $pad_length) = explode('|', $field['value']);
					if($pad_length == 0) {
						$return .= $this->set_annual_sequence($seq_type);
					} else {
						$return .= str_pad($this->set_annual_sequence($seq_type), $pad_length, '0', STR_PAD_LEFT);
					}
				break;
				case 'monthly_seq' :
					list($seq_type, $pad_length) = explode('|', $field['value']);
					if($pad_length == 0) {
						$return .= $this->set_monthly_sequence($seq_type);
					} else {
						$return .= str_pad($this->set_monthly_sequence($seq_type), $pad_length, '0', STR_PAD_LEFT);
					}
				break;
				case 'daily_seq' :
					list($seq_type, $pad_length) = explode('|', $field['value']);
					if($pad_length == 0) {
						$return .= $this->set_daily_sequence($seq_type);
					} else {
						$return .= str_pad($this->set_daily_sequence($seq_type), $pad_length, '0', STR_PAD_LEFT);
					}
					break;
				case 'org_struc_abv' :
					$return .= $this->org_struc_abv($field['value']);
				break;
				default :
					$return .= $field['value'];
				break;
			}
		}
		
		return $return;
	}

	/**
	 * @param unknown $function_ref_id
	 * @param unknown $role_id
	 * @param unknown $flow_seq
	 * @return boolean
	 */
	function editable_data($function_ref_id, $role_id, $ref_id) {
		$this->init();
		
		$sql = "SELECT s.*, fp.title, fp.title process_title, fp.role_handle, fp.permission_handle, fp.position_handle, fp.modify FROM surat s
				JOIN system_security.flow_process fp ON(fp.function_ref_id = $function_ref_id AND fp.flow_seq = s.status AND fp.status = 1)
				WHERE s.surat_id = '$ref_id' ";
		$result = $this->db->query($sql);
		
		if($result->num_rows() > 0) {
			$process = $result->row();
			switch($process->position_handle) {
				case 'X' :
					return $this->has_permission(1);
				break;
				case 'O' :
					if($process->modify == 1) {
						if(get_user_data('unit_id') == $process->surat_from_ref_id) {
							return TRUE;
						} else {
							return FALSE;
						}
					} else {
						return FALSE;
					}
				break;
				case 'D' :
					if($process->modify == 1) {
						if(get_user_data('unit_id') == $process->surat_from_ref_id) {
							return TRUE;
						} else {
							return FALSE;
						}
					} else {
						return FALSE;
					}
				break;
				default :
					return FALSE;
				break;
			}
			
		} else {
			return FALSE;
		}
	}
	
	/**
	 * @param unknown $table_field
	 * @param unknown $param
	 */
	function check_field_flow($table_field, $param) {
		$this->init();
		
		list($table, $field) = explode('.', $table_field);
		$result = $this->db->get_where($table, $param);
		$data = $result->row();
// 		var_dump($data);
		return $data->$field;
	}
	
	/**
	 * @param unknown $format_text
	 * @param array $param
	 */
	function sprintformat($format_text, $param = array()) {
		$ci =& get_instance();
		$this->ci->load->library('parser');
		
		return $this->ci->parser->parse_string($format_text, $param, TRUE);
	}
}

/**
 * End of file lx.php
 */