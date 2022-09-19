<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


	/**
	 * @return string
	 */
	function show_message(){
		$ci =& get_instance();
		
		$return = '';
	 	
		if($ci->lx->session_get_dec('lx_error_msg')){
			$return .= $ci->lx->session_get_dec('lx_error_msg') . '<br>';
			$ci->session->unset_userdata('lx_error_msg');
		}
	 
		if($ci->lx->session_get_dec('lx_warning_msg')){
			$return .= $ci->lx->session_get_dec('lx_warning_msg') . '<br>';
			$ci->session->unset_userdata('lx_warning_msg');
		}
		 
		if($ci->lx->session_get_dec('lx_success_msg')){
			$return .= $ci->lx->session_get_dec('lx_success_msg') . '<br>';
			$ci->session->unset_userdata('lx_success_msg');
		}
		
		return str_replace(array("\r\n", "\r", "\n"), "<br />", $return);
	}
	
	/**
	 * @param $msg
	 * @return void
	 */
	function set_error_message($msg){
		$ci =& get_instance();
		$ci->lx->session_save_enc("lx_error_msg", str_replace(array("\r\n", "\r", "\n"), "<br />", $msg));			
	}
	
	/**
	 * @param $msg
	 * @return void
	 */
	function set_warning_message($msg){
		$ci =& get_instance();
		$ci->lx->session_save_enc("lx_warning_msg", str_replace(array("\r\n", "\r", "\n"), "<br />", $msg));
	}
	
	/**
	 * @param $msg
	 * @return void
	 */
	function set_success_message($msg){
		$ci =& get_instance();
		$ci->lx->session_save_enc("lx_success_msg", str_replace(array("\r\n", "\r", "\n"), "<br />", $msg));
	}
	
	/**
	 * @return string
	 */
	function get_error_message(){
		$ci =& get_instance();
		return $ci->lx->session_get_dec('lx_error_msg');
	}
	
	/**
	 * @return string
	 */
	function get_warning_message(){
		$ci =& get_instance();
		return $ci->lx->session_get_dec('lx_warning_msg');
	}
	
	/**
	 * @return string
	 */
	function get_success_message(){
		$ci =& get_instance();
		return $ci->lx->session_get_dec('lx_success_msg');
	}
	
	/**
	 * @return boolean
	 */
	function message_exist(){
		$ci =& get_instance();
		return (($ci->lx->session_get_dec('lx_error_msg')) || ($ci->lx->session_get_dec('lx_warning_msg')) || ($ci->lx->session_get_dec('lx_success_msg')));
	}
	
?>