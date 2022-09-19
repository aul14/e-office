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
 * @filesource Internal.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Sep 22, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

class Tugas extends LX_Controller {

	/**
	 * Enter description here ...
	 */
	function __construct() {
		parent::__construct();

		if(!defined('__DIR__') ) define('__DIR__', dirname(__FILE__));
		$dir = explode('/', str_replace('\\', '/', __DIR__));
		$module = end($dir);
		
		$this->output_head = array('class' => strtolower(__CLASS__), 'module' => strtolower($module));

		$this->output_head['search_type'] = 'surat_internal';
		$this->load->model(array('auth/user_model', 'global/admin_model', 'arsip_model'));
	}
	
	/**
	 * 
	 */
	function index() {
		$list = $this->arsip_model->get_my_list('surat_tugas');
		$this->output_data['list'] = $list;
		
		$this->output_data['title'] = 'Surat Tugas';
		$this->output_data['type'] = 'tugas';
		
		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
											assets_url() . '/plugins/select2/select2.min.css');
		
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
				assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
				assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		
		$this->load->view('global/header', $this->output_head);
		
		$this->load->view('my_archive', $this->output_data);
		$this->load->view('global/footer');
	}

	
}

/**
 * End of file
 */