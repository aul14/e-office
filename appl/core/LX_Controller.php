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
 * @filesource LX_Controller.php
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

class LX_Controller extends MX_Controller {
	
	var $output_head;
	var $output_data;
	var $theme_dir;
	
	/**
	 * Enter description here ...
	 */
	function __construct() {
		parent::__construct();
		
		if(isset($_POST['action'])) {
			
			foreach($_POST as $k => $v) {
				$_POST[$k] = $this->input->post($k);
			}

			$this->load->library('form_validation');
			$this->form_validation->CI =& $this;
			
			list($module, $model, $action) = explode('.', $_POST['action']);
			unset($_POST['action']);
			if($module != '') {
				$this->load->model($module . '/' . $model);
			} else {
				$this->load->model($model);
			}
			
			$this->$model->$action();
		}
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $function
	 * @param unknown_type $class
	 * @param unknown_type $module
	 * @return number
	 */
	function get_permission_id($function = '*', $class = '*', $module = '*') {
		$function = strtolower($function);
		$class = strtolower($class);
		$module = strtolower($module) == 'controllers' ? '*' : strtolower($module);
		
		$this->db->select('permission_id')->from('system_security.security_permission')->where(array('module' => $module, 'class' => $class, 'function' => $function));
		$result = $this->db->get();
		if ($result->num_rows() > 0) {
			$row = $result->row();
			return $row->permission_id;
		} else {
			return 0;
		}
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $function
	 * @param unknown_type $class
	 * @param unknown_type $module
	 */
	function has_access($function = '*', $class = '*', $module = '*') {
		return has_permission($this->get_permission_id($function, $class, $module));
	}

	/**
	 * @param $str
	 * @return Callback Process
	 */
	function id_exist_check($empl_id) {
		echo 'called';
		$result = $this->user_model->check_empl_exist($empl_id);
		if ($result->num_rows() > 0) {
			$this->form_validation->set_message('id_exist_check', 'the {field} already registered');
			return false;
		} else {
			return true;
		}
	}
	
}

/* End of file  */