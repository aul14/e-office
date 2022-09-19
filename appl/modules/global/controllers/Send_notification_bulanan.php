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
 * @filesource admin.php
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

class Send_notification extends LX_Controller {
	
	/**
	 * Enter description here ...
	 */
	function __construct() {
		parent::__construct();
		if(!is_logged()) {
			redirect('auth/login/authenticate');
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
		$this->send_notification_bulanan();
	}
	
	/**
	 * Enter description here ...
	 */
	// function dashboard() {
		// $this->load->view('global/header');
		
		// $this->load->view('dashboard');
		
		// $this->load->view('global/footer');
	// }
	
	/**
	 * Enter description here ...
	 */

	
function send_notification_bulanan(){
		$list = $this->kontrak_model->get_kontrak_selesai_list();
		if(count($list) > 0) {
			foreach($list as $row) {
				$tgl_akhir = new dateTime($row->surat_akhir);
				$tgl_skrng = new DateTime();
				$diff = $tgl_akhir->diff($tgl_skrng);
				$diff;
				if ($diff->days <= 180){
					$note = 'Kontrak No. ' . $_POST['surat_no'] . ' Berakhir Kurang dari 6 bulan lagi ';
					$subject = "Notifikasi Proses Kontrak - " . $surat->agenda_id;
					$body = 'Contract  - ' . $surat->agenda_id ;
					
					foreach ($list->result() as $row) {
					$this->db->insert('notify', array('function_ref_id' => $surat->function_ref_id, 'ref_id' =>  $surat_id, 'agenda' => ('CM -' . $data['agenda_id']), 'note' => $note, 'detail_link' => ('surat/kontrak/kontrak_view/' . $surat_id), 'notify_user_id' => $row->user_id, 'read' =>0));
					$this->_send_mail_notification($row_tujuan->email, $subject, $body, array());
					}
				}
			}
		}
	}
	
	
	/**
	 * Function to handle request from javascript ajax
	 * do nothing 
	 */
	function ajax_handler() {
		
	}
	
}

/**
 * End of file
 */