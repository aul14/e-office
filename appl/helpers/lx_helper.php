<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * @filesource lx_helper.php
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

function is_logged()
{
	$ci = &get_instance();
	return $ci->lx->is_logged();
}

function check_role($role_id, $strict = false)
{
	$ci = &get_instance();
	return $ci->lx->check_role($role_id, $strict);
}

function get_role()
{
	$ci = &get_instance();
	return $ci->lx->get_role();
}

function get_user_id()
{
	$ci = &get_instance();
	return $ci->lx->get_user_id();
}

function get_user_data($key = null)
{
	$ci = &get_instance();
	return $ci->lx->get_user_data($key);
}

function get_user_modify($key = null)
{
	$ci = &get_instance();
	return $ci->lx->get_user_modify($key);
}

function has_permission($permission_id)
{
	$ci = &get_instance();
	return $ci->lx->has_permission($permission_id);
}

function assets_url()
{
	return base_url() . 'assets';
}

function view_page($page)
{
	$ci = &get_instance();
	$ci->load->view($page);
}

function set_annual_sequence($type)
{
	$ci = &get_instance();
	return $ci->lx->set_annual_sequence($type);
}

function set_monthly_sequence($type)
{
	$ci = &get_instance();
	return $ci->lx->set_monthly_sequence($type);
}

function generate_unique_id($pre = '', $suf = '')
{
	$ci = &get_instance();
	return $ci->lx->generate_unique_id($pre, $suf);
}

function user_with_permission($permission_id)
{
	$ci = &get_instance();
	return $ci->lx->user_with_permission($permission_id);
}

function user_in_unit($unit_id)
{
	$ci = &get_instance();
	return $ci->lx->user_in_unit($unit_id);
}

function sprintformat($format_text, $param = array())
{
	$ci = &get_instance();
	return $ci->lx->sprintformat($format_text, $param);
}

function editable_data($function_ref_id, $role_id, $flow_seq)
{
	$ci = &get_instance();
	return $ci->lx->editable_data($function_ref_id, $role_id, $flow_seq);
}

function check_field_flow($table_field, $param)
{
	$ci = &get_instance();
	return $ci->lx->check_field_flow($table_field, $param);
}

function xrequestwithdata($url, $data, $reqmethod)
{
	$arrheader = array(
		'Content-type: application/json',
	);

	$custreq = strtoupper($reqmethod);

	// if ($reqmethod == 'put') {
	// $custreq = 'PUT';
	// } elseif ($reqmethod == 'delete') {
	// $custreq = 'DELETE';
	// } else {
	// $custreq = 'POST';
	// }

	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $url);
	curl_setopt($handle, CURLOPT_HTTPHEADER, $arrheader);
	curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $custreq);
	curl_setopt($handle, CURLOPT_POST, 1);
	curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($handle);
	if ($response === false) {
		$resp_arr = array();
		$resp_arr['metaData']['message'] = curl_error($handle);
		$resp_arr['metaData']['code'] = curl_errno($handle);
		$response = json_encode($resp_arr);
	}
	curl_close($handle);

	return $response;
}
