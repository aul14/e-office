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
 * @filesource LX_Model.php
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

/* load the MX_Loader class */
require APPPATH . "core/data_object.php";

class LX_Model extends CI_Model {
	
	var $data_object;
	
	/**
	 * Constructor
	 * @return unknown_type
	 */
	function __construct() {
		parent::__construct();
		/*
		if(is_logged()) {
			if($this->uri->segment(1) == 'system') {
				$this->environment_mode = 'admin';
			} elseif (strpos(uri_string(), '/preview') !== FALSE) {
				$this->environment_mode = 'preview';
			}
		}
		*/
		$this->data_object = new Data_object;
	}
	
	/**
	 * Enter description here ...
	 */
	function _system_config($type, $key = NULL) {
		if($key == NULL) {
			$return = array();
			$this->db->order_by("sort");
			$rows = $this->db->get_where('system_security.system_variables', array('type' => $type));
			
			foreach($rows->result() as $row) {
				$return[$row->val] = $row->val;
			}
			return $return;
			
		} else {
			$rows = $this->db->get_where('system_security.system_variables', array('type' => $type, 'key' => $key));
			if($rows->num_rows() > 0) {
				foreach($rows->result() as $row) {
					$return[$row->val] = $row->val;
				}
				return $return;
				//$row = $rows->row();
				//return $row->val;
			} else {
				return '';
			}
		}
	}

	/**
	 * Enter description here ...
	 */
	function _contract_config($type, $key = NULL) {
		if($key == NULL) {
			$return = array();
			$this->db->order_by("sort");
			$rows = $this->db->get_where('system_security.system_variables', array('type' => $type));
			
			foreach($rows->result() as $row) {
				$return[$row->val] = $row->val;
			}
			return $return;
			
		} else {
			$rows = $this->db->get_where('system_security.system_variables', array('type' => $type, 'key' => $key));
			if($rows->num_rows() > 0) {
				$row = $rows->row();
				return $row->val;
			} else {
				return '';
			}
		}
	}
	
	function _system_cm_config($type, $key = NULL) {
		if($key == NULL) {
			$return = array();
			$this->db->order_by("sort");
			$rows = $this->db->get_where('system_security.system_variables', array('type' => $type));
			
			foreach($rows->result() as $row) {
				$return[$row->key] = $row->val;
			}
			return $return;
			
		} else {
			$rows = $this->db->get_where('system_security.system_variables', array('type' => $type, 'key' => $key));
			if($rows->num_rows() > 0) {
				foreach($rows->result() as $row) {
					$return[$row->key] = $row->val;
				}
				
				return $return;
			} else {
				return '';
			}
		}
	}
	
	/**
	 * @param $to
	 * @param $subj
	 * @param $str
	 * @param $attach
	 * @param $mail_type
	 * @return unknown_type
	 */
	/*
	function _send_mail_notification($to, $subj, $str, $attach, $mail_type = 'Task Notification', $from = '') {	
		if($this->_contract_config('variables', 'send_mail') == 'direct') {
						
			$this->load->library('email');
			$this->email->set_newline("\r\n");
			$this->load->helper('file');
			$this->email->clear();

			if($from == '') {
	// 			list($mail_from, $name_from) = explode('/', $this->_system_config('variable', 'mail_from'));
				$mail_from = 'info@laxono.us';
				$name_from = 'eOffice Administrator';
				$this->email->from($mail_from, $name_from);
			} else {
				if(is_array($from)) {
					foreach($from as $email => $name) {
						$this->email->from($email, $name);
					}
				} else {
					$this->email->from($from);
				}
			}
			
			$this->email->to($to);
			$cc = $this->_contract_config('mail_cc');
			if(count($cc) > 0) {
				$cc = implode(', ', $cc);
				$this->email->cc($cc);
			}
			
			$bcc = $this->_contract_config('mail_bcc');
			if(count($bcc) > 0) {
				$bcc = implode(', ', $bcc);
				$this->email->bcc($bcc);
			}
			
			$this->email->subject($subj);
			
			$str = '<h1 style="font: 13px/20px normal Helvetica, Arial, sans-serif;
												color: #444;
												background-color: transparent;
												border-bottom: 1px solid #D0D0D0;
												font-size: 19px;
												font-weight: normal;
												margin: 0 0 14px 0;
												padding: 14px 15px 10px 15px;
												"> e-Office system - ' . $subj . '</h1>
								<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">' . $str . '</p>
								<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">
									Terima kasih<br/>
									eOffice Administrator
								</p>';
				
			$this->email->message($str);
			for($i=0;$i<count($attach);$i++) {
				$this->email->attach($attach[$i]);
			}

			if (!defined('LOGEMAIL_PATH')) 
				define("LOGEMAIL_PATH", APPPATH."/logs/log_".date('Y-m-d')."_email.php");

			if ($this->email->send()) {
				$str = "\n$mail_type Email has been sent to $to at " . date('Y-m-d H:i:s');
				//write_file(LOGEMAIL_PATH, $str, 'at');
			}else {
				$str .= "==========================================\n";
				$str .= "$mail_type Email failed to be sent to $to..\n";
				$str .= $this->email->print_debugger()."\n";		
				$str .= "==========================================\n";	
				//write_file(LOGEMAIL_PATH, $str, 'at');
				echo $this->email->print_debugger()."\n";
			}
		} else {
			
		}
	}
	*/
	
	function _send_mail_notification($to, $subj, $str, $attach, $mail_type = 'Task Notification', $from = '') {	
		if($this->_contract_config('variables', 'send_mail') == 'direct') {
						
			$this->load->library('phpmailer_lib');
			$this->email = $this->phpmailer_lib->load();

			$this->email->isSMTP();                                     
			$this->email->Host = 'ssl://smtp.googlemail.com';  			
			$this->email->SMTPAuth = true;                              
			$this->email->SMTPAutoTLS = false;
			$this->email->Username = 'rsudbalaraja.eoffice@gmail.com';           
			$this->email->Password = 'Eoffice698!';                
			$this->email->SMTPSecure = 'ssl';                           
			$this->email->Port = 465;                                   

			$this->email->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);

			$this->load->helper('file');
			
			if($from == '') {
	// 			list($mail_from, $name_from) = explode('/', $this->_system_config('variable', 'mail_from'));
				$mail_from = 'info@laxono.us';
				$name_from = 'eOffice Administrator';
				$this->email->setFrom($mail_from, $name_from);
			} else {
				if(is_array($from)) {
					foreach($from as $email => $name) {
						$this->email->setFrom($email, $name);
					}
				} else {
					$this->email->setFrom($from);
				}
			}
			
			$this->email->addAddress($to);
			$cc = $this->_contract_config('mail_cc');
			if(count($cc) > 0) {
				$cc = implode(', ', $cc);
				$this->email->addCC($cc);
			}
			$bcc = $this->_contract_config('mail_bcc');
			if(count($bcc) > 0) {
				$bcc = implode(', ', $bcc);
				$this->email->addBCC($bcc);
			}

			$this->email->isHTML(true);

			$this->email->Subject = $subj;
			
			$str = '<h1 style="font: 13px/20px normal Helvetica, Arial, sans-serif;
												color: #444;
												background-color: transparent;
												border-bottom: 1px solid #D0D0D0;
												font-size: 19px;
												font-weight: normal;
												margin: 0 0 14px 0;
												padding: 14px 15px 10px 15px;
												"> e-Office system - ' . $subj . '</h1>
								<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;">' . $str . '</p>
								<p style="font: 13px/20px normal Helvetica, Arial, sans-serif; margin: 12px 15px 12px 15px;"><br/>
									Terima kasih<br/>
									eOffice Administrator
								</p>';
				
			
			$this->email->Body = $str;

			for($i=0;$i<count($attach);$i++) {
				$this->email->addAttachment($attach[$i]);
			}

			if (!defined('LOGEMAIL_PATH')) 
				define("LOGEMAIL_PATH", APPPATH."/logs/log_".date('Y-m-d')."_email.php");

			if ($this->email->send()) {
				$str = "\n$mail_type Email has been sent to $to at " . date('Y-m-d H:i:s');
				write_file(LOGEMAIL_PATH, $str, 'at');
				return 'success';
			}else {
				$str .= "==========================================\n";
				$str .= "$mail_type Email failed to be sent to $to..\n";
				$str .= $this->email->ErrorInfo."\n";		
				$str .= "==========================================\n";	
				write_file(LOGEMAIL_PATH, $str, 'at');
				return 'failed';
			}
		} else {
			return;
		}
		
	}
	
	/**
	 * Enter description here ...
	 * @return string
	 */
	function _suggest_password() {
		$pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ";
	    $passwordlength = 8;    // do we want that to be dynamic?  no, keep it simple :)
	    $password = '';
	
		for ($i = 0; $i < $passwordlength; $i++) {
			$password .= $pwchars[rand(0, (strlen($pwchars) - 1))];
		}
		return $password;
	}
	
	function _reset_password($id) {
		$pass = $this->_suggest_password();
		$this->db->where('user_id', $id);
		$this->db->update('system_security.users', array('password' => $this->encrypt->get_key($pass)));
		 
		$user = $this->db->get_where('system_security.users', array('user_id' => $id))->row();
		
		$to = $user->email;
		
		$subject = 'Reset Password';
		
		//$subject = 'Reset Password';
		$body = 'Your login account name is <strong>' . ($user->user_name) . '</strong><br>
							and your new password is \'<strong>' . $pass . '</strong>\'';
		$attach = array();
		if($this->_send_mail_notification($to, $subject, $body, $attach, 'Reset Password')){
			echo '0';
		} else {
			echo '1';
		}
		
	}
	
	/**
	 * @param unknown $obj
	 */
	function _validate_post_data($obj, $mode = 'add') {
		$this->load->library('form_validation');
		
		foreach ($obj as $k => $v) {
			if($v['validate'] == $mode || $v['validate'] == 'both') {
				$this->form_validation->set_rules($k, $v['label'], $v['rule']);
// 				echo 'validate ' . $k . ' - ' . $v['rule'];
			}
		}
		
		return $this->form_validation->run();
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $name
	 * @return mixed
	 */
	function _set_clean_name($name) {
		$void = array('& ', '/ ', '.', ',', '"', "'", "(", ")");
		return underscore(str_replace($void, '', $name));
	}
	
	function _dot2_longIP($ip_addr) {
	//	echo $ip_addr;
		return 0;
		if ($ip_addr == "") {
			return 0;
		} else {
			$ips = explode(".", "$ip_addr");
			return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
			
		}
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $file
	 * @param unknown_type $save
	 * @param unknown_type $maxwidth
	 * @param unknown_type $maxheight
	 */
	function _resize_hi($file,$save,$maxwidth,$maxheight) {
		list($width, $height) = getimagesize($file) ;
		$imgInfo = getimagesize($file) ;
	
		if($width>$height) {
			if($width>$maxwidth) {
				$modwidth = $maxwidth;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
			} else {
				$modwidth = $width;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
			}
				
			if($modheight>$maxheight) {
				if($height>$maxheight) {
					$modheight = $maxheight;
					$diff = $height / $modheight;
					$modwidth = $width / $diff;
				} else {
					$modheight = $height;
					$diff = $height / $modheight;
					$modwidth = $width / $diff;
				}
			}
		} else if($width==$height) {
			if($width>$maxwidth) {
				$modwidth = $maxwidth;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
			} else {
				$modwidth = $width;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
			}
				
			if($modheight>$maxheight) {
				if($height>$maxheight) {
					$modheight = $maxheight;
					$diff = $height / $modheight;
					$modwidth = $width / $diff;
				} else {
					$modheight = $height;
					$diff = $height / $modheight;
					$modwidth = $width / $diff;
				}
			}
		} else if($width<$height) {
			if($height>$maxheight) {
				$modheight = $maxheight;
				$diff = $height / $modheight;
				$modwidth = $width / $diff;
			} else {
				$modheight = $height;
				$diff = $height / $modheight;
				$modwidth = $width / $diff;
			} if($modwidth>$maxwidth) {
				if($width>$maxwidth) {
					$modwidth = $maxwidth;
					$diff = $width / $modwidth;
					$modheight = $height / $diff;
				} else {
					$modwidth = $width;
					$diff = $width / $modwidth;
					$modheight = $height / $diff;
				}
			}
		}
	
	
		switch ($imgInfo[2]) {
			case 1: $image = imagecreatefromgif($file); break;
			case 2: $image = imagecreatefromjpeg($file);  break;
			case 3: $image = imagecreatefrompng($file); break;
		}
	
		//$image=imagecreatefromjpeg($file);
	
		$tn=imagecreatetruecolor($modwidth,$modheight);
	
		if(($imgInfo[2] == 1) or ($imgInfo[2]==3)) {
			imagealphablending($tn, false);
			imagesavealpha($tn,true);
			$transparent = imagecolorallocatealpha($tn, 255, 255, 255, 127);
			imagefilledrectangle($tn, 0, 0, $modwidth, $modheight, $transparent);
		}
			
		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
	
	
		switch ($imgInfo[2]) {
			case 1: imagegif($tn,$save); break;
			case 2: imagejpeg($tn,$save);  break;
			case 3: imagepng($tn,$save); break;
	
		}
	
	}
	
	/**
	 * Enter description here ...
	 * @param unknown_type $file
	 * @param unknown_type $save
	 * @param unknown_type $maxwidth
	 * @param unknown_type $maxheight
	 */
	function _resize_lo($file, $save, $maxwidth, $maxheight) {
		list($width, $height) = getimagesize($file) ;
		$imgInfo = getimagesize($file) ;
	
		if($width > $height) {
			if($width > $maxwidth) {
				$modheight = $maxheight;
				$diff = $height / $modheight;
				$modwidth = $width / $diff;
	
				if($modwidth < $maxwidth) {
					$modwidth = $maxwidth;
					$diff = $width / $modwidth;
					$modheight = $height / $diff;
				}
			} else {
				$modwidth = $width;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
			}
		} else if($width == $height) {
			if($width > $maxwidth) {
				$modwidth = $maxwidth;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
			} else {
				$modwidth = $width;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
			}
		} else if($width < $height) {
			if($height > $maxheight) {
				$modwidth = $maxwidth;
				$diff = $width / $modwidth;
				$modheight = $height / $diff;
	
				if($modheight < $maxheight) {
					$modheight = $maxheight;
					$diff = $height / $modheight;
					$modwidth = $width / $diff;
				}
			} else {
				$modheight = $height;
				$diff = $height / $modheight;
				$modwidth = $width / $diff;
			}
		}
	
		switch ($imgInfo[2]) {
			case 1: $image = imagecreatefromgif($file); break;
			case 2: $image = imagecreatefromjpeg($file);  break;
			case 3: $image = imagecreatefrompng($file); break;
		}
	
		$tn = imagecreatetruecolor($modwidth, $modheight);
	
	
		if(($imgInfo[2] == 1) or ($imgInfo[2]==3)) {
			imagealphablending($tn, false);
			imagesavealpha($tn, true);
			$transparent = imagecolorallocatealpha($tn, 255, 255, 255, 127);
			imagefilledrectangle($tn, 0, 0, $modwidth, $modheight, $transparent);
		}
	
		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
	
		switch ($imgInfo[2]) {
	
			case 1: imagegif($tn,$save); break;
			case 2: imagejpeg($tn,$save);  break;
			case 3: imagepng($tn,$save); break;
	
		}
	}
	
	function update_notification($table, $ref_id, $status = NULL) {
		if($status == NULL) {
			$set = array('read' => 0, 'created_time' => date('Y-m-d H:i:s'));
		} else {
			$set = array('read' => 0, 'status' => $status, 'created_time' => date('Y-m-d H:i:s'));
		}
		$this->db->update('notify', $set, array('table' => $table, 'ref_id' => $ref_id));
	}

	function read_notification($table, $ref_id) {
		$this->db->update('notify', array('read' => 1), array('table' => $table, 'ref_id' => $ref_id, 'notify_user_id' => get_user_id()));
	}

}

/* End of file  */