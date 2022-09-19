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
 * @filesource Mail.php
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

class Mail extends LX_Controller {
	
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
		
		$this->load->model(array('admin_model', 'mail_model', 'auth/user_model'));
		$this->output_head['search_type'] = 'global';
	}
	
	/**
	 * Enter description here ...
	 */
	function index() {
		$this->inbox();
	}
	
	/**
	 * Enter description here ...
	 */
	function inbox($page = 1) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
												assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);

		$list = $this->mail_model->get_mail(0, 'inbox');
		
		$this->output_data['list'] = $list;
		$this->output_data['active_mail_menu'] = 'inbox';
		$this->output_data['title'] = 'Inbox';
		$this->output_data['page'] = $page;
		$this->load->view('mail_box', $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	/**
	 * Enter description here ...
	 */
	function draft($page = 1) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
													assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
												assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);

		$list = $this->mail_model->get_mail(0, 'draft');
		
		$this->output_data['list'] = $list;
		$this->output_data['active_mail_menu'] = 'draft';
		$this->output_data['title'] = 'Draft';
		$this->output_data['page'] = $page;
		$this->load->view('mail_box', $this->output_data);
		
		$this->load->view('global/footer');
	}

	/**
	 * Enter description here ...
	 */
	function outbox($page = 1) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/iCheck/all.css',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/iCheck/icheck.min.js',
												assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js');
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
		
		$list = $this->mail_model->get_mail(0, 'outbox');

		$this->output_data['list'] = $list;
		$this->output_data['page'] = $page;
		$this->output_data['active_mail_menu'] = 'outbox';
		$this->output_data['title'] = 'Outbox';
		$this->load->view('mail_box', $this->output_data);
		
		$this->load->view('global/footer');
	}

	/**
	 * Enter description here ...
	 */
	function compose($mail_id = 0) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
												assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['mail_id'] = $mail_id;
		$this->output_data['receipt'] = array();
		$this->output_data['subject'] = '';
		$this->output_data['body'] = '';
		if($mail_id != 0) {
			$result = $this->mail_model->get_mail($mail_id);
			$row = $result->row();

			$this->output_data['subject'] = $row->subject;
			$this->output_data['body'] = $row->body;

			$result = $this->mail_model->get_mail_recipt($mail_id);
			foreach ($result->result() as $row) {
				$this->output_data['receipt'][] = $row->receipt_user_id;
			}
		}

		$this->output_data['active_mail_menu'] = 'outbox';
		$this->output_data['title'] = 'Compose';
		$this->load->view('mail_compose', $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	/**
	 * Enter description here ...
	 */
	function read($mail_id) {
		$this->output_head['function'] = __FUNCTION__;
		$this->output_head['style_extras'] = array();
		$this->output_head['js_extras'] = array();
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);

		$result = $this->mail_model->get_mail($mail_id);
		if($result->num_rows() > 0) {
			$row = $result->row();
			
			$this->mail_model->set_read($mail_id);
			$this->output_data['mail_id'] = $mail_id;
			$this->output_data['subject'] = $row->subject;
			$this->output_data['body'] = $row->body;
			$this->output_data['mail_from'] = $row->mail_from;
			$this->output_data['delivery_time'] = $row->delivery_time;
			$result = $this->mail_model->get_mail_recipt($mail_id);
			$this->output_data['receipt'] = $result->result();
		} else {
			redirect('mail/inbox');
			exit;
		}

		$this->output_data['active_mail_menu'] = '';
		$this->output_data['title'] = 'Read memo';
		$this->load->view('mail_read', $this->output_data);
		
		$this->load->view('global/footer');
	}
	
	
}

/**
 * End of file
 */