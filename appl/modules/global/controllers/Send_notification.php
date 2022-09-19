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
		if(!defined('__DIR__') ) define('__DIR__', dirname(__FILE__));
		$dir = explode('/', str_replace('\\', '/', __DIR__));
		$module = end($dir); 

		$this->output_head = array('class' => strtolower(__CLASS__), 'module' => strtolower($module));
		
		$this->load->library('Excel_generator');
		$this->load->model(array('surat/kontrak_model', 'mail_model', 'auth/user_model'));
		$this->output_head['search_type'] = 'global';
		
		set_time_limit(0);
	}
	
	/**
	 * Enter description here ...
	 */
	function index() {
		$this->send_notification_mingguan();
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

	function send_notification_harian(){
		$list = $this->kontrak_model->get_kontrak_aktif_list();
		if(count($list) > 0) {
			foreach($list as $row) {
				$tgl_akhir = new DateTime($row->surat_akhir);
				$tgl_skrng = new DateTime();
				$diff = $tgl_akhir->diff($tgl_skrng);
				$diff;
				if ($diff->days <= 30)
				{
					echo $row->agenda_id . '<br>';
					$subject = "Notifikasi Proses Kontrak - " . $row->agenda_id;
					$body = 'Contract  - ' . $row->agenda_id . ' Berakhir Kurang dari 1 bulan lagi ';
					$note = 'Kontrak No. ' . $row->surat_no . ' Berakhir Kurang dari 1 bulan lagi ';
					
					$list_tujuan = user_in_unit(get_user_data('unit_id'));
					$recipients = array();
					foreach ($list_tujuan->result_array() as $key => $value) {
						$recipients[] = $value['email'];
						echo ' --- ' .$value['email'] . '<br>';
					}
						$this->mail_model->_send_mail_notification($value['email'], $subject, $body, array());
				}
			}
		}
	}
	
	function send_notification_mingguan() {
		$list = $this->kontrak_model->get_kontrak_aktif_list();
		if(count($list) > 0) {
			foreach($list as $row) {
				
				$tgl_akhir = new DateTime($row->surat_akhir);
				$tgl_skrng = new DateTime();
				$diff = $tgl_akhir->diff($tgl_skrng);
				if ($diff->days <= 90 && $diff->days > 30)
				{
					echo $row->agenda_id . '<br>';
					$subject = "Notifikasi Proses Kontrak - " . $row->agenda_id;
					$body = 'Kontrak No. ' . $row->surat_no . ' Berakhir Kurang dari 3 bulan lagi ';
					$note = 'Kontrak No. ' . $row->surat_no . ' Berakhir Kurang dari 3 bulan lagi ';
					
					// $list_tujuan = user_with_permission($row->surat_from_ref_id);
					
					$list_tujuan = user_in_unit(get_user_data('unit_id'));
					$recipients = array();
					foreach ($list_tujuan->result_array() as $key => $value) {
						$recipients[] = $value['email'];
						echo ' --- ' .$value['email'] . '<br>';
					}
						$this->mail_model->_send_mail_notification($value['email'], $subject, $body, array());
				}
			}
		}
	}
			
	function send_notification_bulanan(){
	
		$list = $this->kontrak_model->get_kontrak_aktif_list();
		if(count($list) > 0) {
			foreach($list as $row) {
				$tgl_akhir = new DateTime($row->surat_akhir);
				$tgl_skrng = new DateTime();
				$diff = $tgl_akhir->diff($tgl_skrng);
				$diff;
				if ($diff->days <=180 && $diff->days > 90)
				{
					echo $row->agenda_id . '<br>';
					$subject = "Notifikasi Proses Kontrak - " . $row->agenda_id;
					$body = 'Contract  - ' . $row->agenda_id . ' Berakhir Kurang dari 6 bulan lagi ';
					$note = 'Kontrak No. ' . $row->surat_no . ' Berakhir Kurang dari 6 bulan lagi ';
					
					$list_tujuan = user_in_unit(get_user_data('unit_id'));
					$recipients = array();
					foreach ($list_tujuan->result_array() as $key => $value) {
						$recipients[] = $value['email'];
						echo ' --- ' .$value['email'] . '<br>';
					}
						$this->mail_model->_send_mail_notification($value['email'], $subject, $body, array());
				}
			}
		}
	}
	
	function Send_laporan(){
		
		$query = $this->kontrak_model->get_kontrak_aktif_list()->result();
	    $this->excel_generator->set_header(array('Mitra', 'Tgl. Mulai', 'Tgl. Berakhir', 'No. Agenda', 'Tgl. Kontrak', 'No. Kontrak', 'Kode Konrak', 'Jenis Kontrak', 'Nilai Kontrak', 'Perihal'));
        $this->excel_generator->set_column(array('status_berkas', 'surat_awal', 'surat_akhir', 'agenda_id', 'surat_unit_lampiran','surat_no', 'sifat_surat', 'jenis_surat', 'surat_ringkasan','surat_perihal'));
        $this->excel_generator->exportTo2007('Laporan Users');
		$this->excel_generator->start_at(8);
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