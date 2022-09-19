<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @filesource login.php
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

class Login extends LX_Controller {

	var $output_data;

	/**
	 * Enter description here ...
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper(array('form'));
		$this->load->library(array('form_validation'));

		$dir = explode('/', str_replace('\\', '/', __DIR__));
		$module = end($dir); 

		$this->output_data = array('class' => __CLASS__, 'module' => $module);

	}

	/**
	 * Enter description here ...
	 */
	function index() {
		$this->authenticate();
	}

	/**
	 * Enter description here ...
	 */
	function check_login() {
		if(!is_logged()) {
			$this->output->set_output('TRUE');
		} else {
			$this->output->set_output('FALSE');
		}
	}

	/**
	 * Enter description here ...
	 */
	function authenticate() {
		$this->output_data['function'] = __FUNCTION__;
		
		if (is_logged()) {
			$this->home();

		} else {
			$this->form_validation->set_rules('email', 'Email Address', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|md5');

			$valid_user = FALSE;
			$msg = "";

			if ($this->form_validation->run() != FALSE){//	echo 'valid';
				$valid_user = $this->lx->trylogin($this->input->post("email"), $this->input->post("password")); 

				if(!$valid_user) {
					$msg = '<div class="alert">Invalid User Account</div>';                                    
				} else {
					$this->home();
				}
			}

			$this->output_data['msg'] = $msg;
			$this->load->view("login", $this->output_data);

		}

	}
	
	/**
	 * Enter description here ...
	 */
	function register() {
		$this->output_data['function'] = __FUNCTION__;
		$msg = "";
		
		$this->output_data['msg'] = $msg;
		$this->load->view("register", $this->output_data);

	}

	/**
	* Enter description here ...
	*/
	function logout() {     
		$this->lx->logout();
//		$this->system_model->clear_cache();
		redirect('login');
		exit;
	}

	/**
	* Enter description here ...
	*/
	function home() {
//		$item = (get_role() <= $this->config->item('ge_max_admin_role')) ? 'ge_admin_url' : 'ge_home_url';
//		redirect($this->config->item($item));
		redirect('dashboard');
		exit;
	}

	function activation($activation_code) {

		$valid_user = $this->lx->activate($activation_code); 

		if(!$valid_user) {
			$msg = '<div class="alert">Invalid Activation Code.</div>';
			$this->output_data['msg'] = $msg;
			$this->load->view("login", $this->output_data);

		} else {
			set_success_message('Successfully activate your account.');
			
			redirect('user/account');
			exit;
		}
		
	}

	/**
	* Enter description here ...
	*/
	function forgot_password() {
		$this->output_data['function'] = __FUNCTION__;
		$msg = "";
		$this->form_validation->set_rules('email', 'Email Address', 'trim|required');
		if ($this->form_validation->run() != FALSE) {//	echo 'valid';
			$this->load->model('user_model');

			$valid_user = $this->user_model->get_forgotten_user($this->input->post("email")); 
			
			if(!$valid_user) {
				$msg = "<div class=\"alert\">Undefined User</div>";                                    
			} else {
				
				$this->user_model->reset_password($valid_user->user_id);
				$msg = "<div class=\"alert\">Success reset password, please check your email.</div>"; 

			}
		}
		$this->output_data['msg'] = $msg;

		$this->load->view('forgot_password', $this->output_data);
	}

}

/* End of file login.php */
/* Location: ./appl/controllers/login.php */