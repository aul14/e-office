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
 * @filesource Dashboard.php
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

class Tugas extends LX_Controller {
	
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
		
		$this->load->model(array('laporan_model', 'auth/user_model', 'global/admin_model'));
		//$this->output_head['search_type'] = 'global';
	}
	
	/**
	 * Enter description here ...
	 */
	function index() {
		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
													assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		$this->output_head['js_function'] = array();
		$this->output_head['search_type'] = 'tugas';
		
		$this->load->view('global/header', $this->output_head);

		$list = $this->laporan_model->get_my_list('tugas');
		$this->output_data['list'] = $list;
		$this->output_data['title'] = 'Surat Perintah';
		
		$this->load->view('tugas_view', $this->output_data);
		$this->load->view('global/footer');
	}
	
	/**
	 * 
	 */
	function search_keywords() {
		$this->output_head['function'] = __FUNCTION__;
		
		$this->output_head['style_extras'] = array(assets_url() . '/plugins/datatables/dataTables.bootstrap.css',
												   assets_url() . '/plugins/select2/select2.min.css');
		$this->output_head['js_extras'] = array(assets_url() . '/plugins/datatables/jquery.dataTables.min.js',
												assets_url() . '/plugins/datatables/dataTables.bootstrap.min.js',
												assets_url() . '/plugins/select2/select2.full.min.js');
		
		$this->output_head['js_function'] = array();
		$this->load->view('global/header', $this->output_head);
		
		$this->output_data['title'] = 'Search Result ';
		$this->output_data['search_keyword'] = $_POST['search_keyword'];
		$list = $this->dashboard_model->get_keyword_result($_POST['search_keyword'], $_POST['search_type']);
		$this->output_data['list'] = $list;
		
		$this->load->view('search_keywords_result', $this->output_data);
		
		$this->load->view('global/footer');
	}

	/**
	 * @param unknown $type
	 */
	function export_excel() {
		$this->load->library('excel_lib');
		
		if ($_POST['month'] != '' && $_POST['year'] != '') {
			$month = $_POST['month'];
			$year = $_POST['year'];
			$function_ref_id = $_POST['function_ref_id'];
			$month_arr = $this->admin_model->get_system_cm_config('option_month_long');

			switch ($function_ref_id) {
				case '1':
					$jenis_agenda = 'SURAT MASUK';
					$tgl_surat = 'TANGGAL MASUK';
					$file = 'surat_masuk';
					break;
				case '2':
					$jenis_agenda = 'SURAT KELUAR';
					$tgl_surat = 'TANGGAL KONSEP SURAT';
					$file = 'surat_keluar';
					break;
				case '3':
					$jenis_agenda = 'NOTA DINAS';
					$tgl_surat = 'TANGGAL KONSEP SURAT';
					$file = 'nota_dinas';
					break;
				case '13':
					$jenis_agenda = 'SURAT PERINTAH';
					$tgl_surat = 'TANGGAL KONSEP SURAT';
					$file = 'surat_perintah';
					break;		
			}
			
			$file_name = md5(uniqid(mt_rand())).'.xls';
			$filename = $file_name;

			$query = $this->laporan_model->get_surat_list($function_ref_id, $month, $year);
						
	        $objPHPExcel = new PHPExcel();
	        $objPHPExcel->setActiveSheetIndex(0);
	        
	        $styleArray = array(
				'borders' => array(
				    'allborders' => array(
				      'style' => PHPExcel_Style_Border::BORDER_THIN
				    )
				)
			);

			$styleHeaderColor = array(
		        'fill' => array(
		            'type' => PHPExcel_Style_Fill::FILL_SOLID,
		            'color' => array('rgb' => '#CCC')
		        )
		    );

	        // set Header
	        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'LAPORAN KEGIATAN KEARSIPAN');
	        $objPHPExcel->getActiveSheet()->SetCellValue('A2', $jenis_agenda);
	        $objPHPExcel->getActiveSheet()->SetCellValue('A3', 'RUMAH SAKIT UMUM DAERAH BALARAJA');
	        $objPHPExcel->getActiveSheet()->SetCellValue('A4', 'BULAN ' . strtoupper($month_arr[$month]) . ' ' . $year);

	        for($rowTitle = 1; $rowTitle < 6; $rowTitle++) {
	        	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $rowTitle . ':G' . $rowTitle);
	        	$objPHPExcel->getActiveSheet()->getStyle('A' . $rowTitle . ':G' . $rowTitle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		        $objPHPExcel->getActiveSheet()->getStyle('A' . $rowTitle)->getFont()->setBold(TRUE);
		    }

	        $objPHPExcel->getActiveSheet()->SetCellValue('A6', 'No.');
	        $objPHPExcel->getActiveSheet()->SetCellValue('B6', 'KODE SURAT');
	        $objPHPExcel->getActiveSheet()->SetCellValue('C6', 'NOMOR SURAT');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D6', 'TANGGAL SURAT');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E6', $tgl_surat);
	        $objPHPExcel->getActiveSheet()->SetCellValue('F6', 'PERIHAL');  
	        $objPHPExcel->getActiveSheet()->SetCellValue('G6', 'ASAL SURAT');
	        $objPHPExcel->getActiveSheet()->SetCellValue('H6', 'STATUS SURAT');
	        
	        $objPHPExcel->getActiveSheet()->SetCellValue('A7', '1');
	        $objPHPExcel->getActiveSheet()->SetCellValue('B7', '2');
	        $objPHPExcel->getActiveSheet()->SetCellValue('C7', '3');
	        $objPHPExcel->getActiveSheet()->SetCellValue('D7', '4');
	        $objPHPExcel->getActiveSheet()->SetCellValue('E7', '5');
	        $objPHPExcel->getActiveSheet()->SetCellValue('F7', '6');  
	        $objPHPExcel->getActiveSheet()->SetCellValue('G7', '7');
	        $objPHPExcel->getActiveSheet()->SetCellValue('H7', '8'); 

            for($rowHeader = 6; $rowHeader < 8; $rowHeader++) {
            	$objPHPExcel->getActiveSheet()->getStyle('A' . $rowHeader . ':H' . $rowHeader)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            	$objPHPExcel->getActiveSheet()->getStyle('A' . $rowHeader . ':H' . $rowHeader)->applyFromArray($styleArray);
	            $objPHPExcel->getActiveSheet()->getStyle('A' . $rowHeader . ':H' . $rowHeader)->getFont()->setBold(TRUE);
	            // $objPHPExcel->getActiveSheet()->getStyle('A' . $rowHeader . ':G' . $rowHeader)->applyFromArray($styleHeaderColor);
            }

	        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
	        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
	        
	        // set Row
	        $rowCount = 8;
	        $no = 1;
	        foreach ($query as $element) {
	        	$instansi = '-';
	        	if ($element['function_ref_id'] == 1) {
	        		$surat_from_ref_data = json_decode($element['surat_from_ref_data'], TRUE);
	        		$instansi = $surat_from_ref_data['instansi'];
	        		$tgl_surat = db_to_human_local($element['surat_tgl']);
	        		$tgl_input = db_to_human_local($element['surat_tgl_masuk']);
	        	}else {
	        		$tgl_surat = (isset($element['surat_tgl'])) ? db_to_human_local($element['surat_tgl']) : '';
	        		$tgl_input = db_to_human_local($element['surat_awal']);
	        		$element['surat_no'] = ($element['surat_no'] == '{surat_no}') ? trim($element['kode_klasifikasi_arsip']) . '/_/SURAT PERINTAH' : $element['surat_no'];
	        	}

	            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $element['agenda_id']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $element['kode_klasifikasi_arsip']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $element['surat_no']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $tgl_surat);
	            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $tgl_input);
	            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $element['surat_perihal']);
	            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $instansi);
	            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $element['status_surat']);

	            $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':E' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	            $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':H' . $rowCount)->applyFromArray($styleArray);

	            $rowCount++;
	            $no++;
	        }
	        
	        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	        $objWriter->save('assets/media/doc/' . $filename);
			
			// download file
	        header("Content-Type: application/vnd.ms-excel");
	        header("Content-Disposition: attachment;filename='".$filename."'");
        	header("Cache-Control: max-age=0");
	        
	        $path = '/lx_media/doc/' . $filename;
	        
			$return = array('error' => '', 'message' => '', 'execute' => $path);
		
		}else {
			$msg = 'Periode tanggal surat belum diisi';
			
			$return = array('error' => 1, 'message' => $msg);
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($return));	
	}
		
	/**
	 * Function to handle request from javascript ajax
	 * do nothing 
	 */
	function ajax_handler() {
		return;
	}
	
	function my_task_board() {
		$return = array('new_task' => 1, 'items' => array());
		
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	
}

/**
 * End of file
 */