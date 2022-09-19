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
 * @filesource LX_Form_validation.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author budi.lx
 * @package 
 * @subpackage	
 * @since Oct 3, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class LX_Form_validation extends CI_Form_validation {

//	function run($module = '', $group = '') {
//		(is_object($module)) AND $this->CI = &$module;
//		return parent::run($group);
//	}

	public $CI;
	
	function __construct($rules = array()) {
		parent::__construct($rules);
		
		log_message('debug', 'LX Form Validation Class Initialized');
	}
	
}