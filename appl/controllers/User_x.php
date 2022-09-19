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
 * @filesource user.php
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

class User extends LX_Controller {

	/**
	 * Enter description here ...
	 */
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('login/authenticate');
			exit;
		}
		
		if(!defined('__DIR__') ) define('__DIR__', dirname(__FILE__));
		$dir = explode('/', str_replace('\\', '/', __DIR__));
		$module = end($dir); 

		$this->output_head = array('class' => strtolower(__CLASS__), 'module' => strtolower($module));
		
		$this->load->model(array('admin_model', 'mail_model', 'user_model'));
	}
	
	/**
	 * Enter description here ...
	 */
	function index() {
		$this->user_add();
	}

	/**
	 * Enter description here ...
	 */
	function show_list() {
		if(!has_permission(3)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('admin');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
		$this->load->model('user_model');
		
		$this->load->view('admin/header', $this->output_head);
		
		$this->output_data['title'] = 'User Manager';
		$this->load->view('admin/users', $this->output_data);
		$this->load->view('admin/footer');
	}
	
	/**
	 * Enter description here ...
	 */
	function user_add($role = NULL, $view = NULL) {
		if(!has_permission(3)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('admin');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
 													assets_url() . '/plugins/datepicker/datepicker3_3.css',
 													assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
											);
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
 												assets_url() . '/plugins/datepicker/bootstrap-datepicker3.js'
											);
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		if($role == NULL) {
			$this->output_head['function'] = __FUNCTION__;
			$this->load->model('user_model');
			
			$this->load->view('global/header', $this->output_head);
			
			$this->output_data['title'] = 'Manage User';
			$this->output_data['mode'] = 'add';
			$this->output_data['user_id'] = 0;
			$this->load->view('user_form', $this->output_data);
			$this->load->view('global/footer');
		} else {
			
		}
	}
	
	/**
	 * Enter description here ...
	 * @param int $user_id
	 */
	function user_edit($user_id, $view = NULL) {
		$result = $this->user_model->get_user($user_id);
		if($result->num_rows() > 0) {
			$data = $result->row();
		} else {
			set_warning_message('Undefined user data');
			redirect('dashboard');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													'//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',
													assets_url() . '/plugins/datepicker/datepicker3_3.css',
													assets_url() . '/plugins/timepicker/bootstrap-timepicker.min.css'
											);
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
 												assets_url() . '/plugins/datepicker/bootstrap-datepicker3.js'
											);
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Edit User ' . get_user_data('name');
		$this->output_data['data'] = $data;
		$this->output_data['user_id'] = $user_id;
		$this->output_data['mode'] = 'edit';
		$this->load->view('user_form', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * @return Page Redirect
	 */
	function account() {
		$this->user_edit(get_user_id());
	}

	/**
	 * Enter description here ...
	 * @param int $user_id
	 */
	function user_autocomplete() {
		$json = $this->user_model->get_users_autocomplete(str_replace('+', ' ', strtolower($_GET['term'])));
		
		$this->output->set_content_type('application/json')->set_output($json);
	}

	/**
	 * Enter description here ...
	 * @param int $user_id
	 */
	function user_delete($user_id) {
		
		if(!$this->has_access(__FUNCTION__, __CLASS__, $this->output_data['module'])) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('admin');
			exit;
		} elseif($user_id == get_user_id()){
			set_warning_message('Sory you cannot delete your own account.');
			redirect('admin');
			exit;
		} else { 
			$this->user_model->delete_user($user_id);
			set_success_message('Successfully delete user');
			$this->show_list();
		}
	}
	
	/**
	 * @return Page
	 */
	function change_password() {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
    	
		/* @var unknown_type */
		$this->output_data['user_id'] = get_user_id();
		
		$this->load->helper('form');
		/* @var JavaScript Functions */
		$this->load->view('global/header', $this->output_head);
		
		/* @title unknown_type */
		$this->output_data['title'] = 'Change Password ' . get_user_data('name');
		
		/* @action form action */
		$this->load->view('password_form', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * AJAX process
	 * @param int $user_id
	 */
	function reset_password($user_id = NULL) {
		$this->output_head['function'] = __FUNCTION__;
    	
		if(!$this->has_access(__FUNCTION__, __CLASS__, $this->output_head['module'])) {
			$this->output->set_output(lang('alert_no_permission'));
			return;
		}
		
		$this->user_model->reset_password($user_id);
		$this->output->set_output(sprintf(lang('success_reset_password'), $user_id));
		
	}
	
	/**
	 * @param $str
	 * @return Callback Process
	 */
	function old_password_check($str) {
		
		$user_data = $this->user_model->get_user(get_user_id())->row();
		if ($str != $user_data->password) {
			$this->form_validation->set_message('old_password_check', 'the %s not match');
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * @param $str
	 * @return Callback Process
	 */
	function email_exist_check($email) {
		
		$result = $this->user_model->check_email_exist($email);
		if ($result->num_rows() > 0) {
			$this->form_validation->set_message('email_exist_check', 'the {field} already registered');
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * @param $str
	 * @return Callback Process
	 */
	function id_exist_check($empl_id) {
		
		$result = $this->user_model->check_empl_exist($empl_id);
		if ($result->num_rows() > 0) {
			$this->form_validation->set_message('id_exist_check', 'the {field} already registered');
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Enter description here ...
	 */
	function role_permission() {
		if(!has_permission(2)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('admin');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;

		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->model('user_model');
		
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Role & Permission Manager';
		$this->load->view('role_permission', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * Enter description here ...
	 */
	function role_data($role_id = 0) {
		if(!has_permission(2)) {
			set_warning_message('Sory you\'re not allowed to access this page.');
			redirect('admin');
			exit;
		}
		
		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'New Role ';
		$this->output_data['role_id'] = $role_id;
		$this->output_data['mode'] = 'add';
		if($role_id != 0) {
			$result = $this->user_model->get_role($role_id);
			if($result->num_rows() > 0) {
				$data = $result->row();
				$this->output_data['title'] = 'Edit Role';
				$this->output_data['mode'] = 'edit';
				$this->output_data['data'] = $data;
			}
		}
		
		$list = $this->user_model->get_role_permission($role_id);
		$this->output_data['list'] = $list;
		
		$this->load->view('role_form', $this->output_data);
		
		$this->load->view('global/footer');
		
	}
	
}

/**
 * End of file user.php  
 */