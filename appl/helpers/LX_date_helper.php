<?php	if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * PHP 5
 *
 * GreenLabGroup Application System Environment (GreASE)
 * GreenLabGroup(tm) :  Rapid Development Framework (http://www.greenlabgroup.com)
 * Copyright 2011-2012, P.T. Green Lab Group.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource GE_date_helper.php
 * @copyright Copyright 2011-2012, P.T. Green Lab Group.
 * @author budi.lx
 * @package 
 * @subpackage	
 * @since Mar 19, 2012
 * @version 
 * @modifiedby budi.lx
 * @lastmodified	
 *
 *
 */

	/**
	 * Enter description here ...
	 * @param unknown_type $db_format
	 */
	function db_to_human($db_format) {
		if(strpos($db_format, ' ') !== FALSE) { 
			list($date, $time) = explode(' ', $db_format);
		} else {
			$date = $db_format;
		}
		list($y, $m, $d) = explode('-', $date);
		
		return "$d-$m-$y";
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $human_format
	 */
	function human_to_db($human_format) {
		if(strpos($human_format, ' ') !== FALSE) { 
			list($date, $time) = explode(' ', $human_format);
		} else {
			$date = $human_format;
			$time = '00:00:00';
		}
		list($d, $m, $y) = explode('-', $date);
		
		return "$y-$m-$d $time";
	}

	function db_to_human_local($db_format) {
		if(strpos($db_format, ' ') !== FALSE) { 
			list($date, $time) = explode(' ', $db_format);
		} else {
			$date = $db_format;
		}
		list($y, $m, $d) = explode('-', $date);
		$month = array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
		$m_index = strval($m) - 1;
		$m = $month[$m_index];

		return "$d $m $y";
	}
	

/**
 * End of file GE_date_helper.php 
 */