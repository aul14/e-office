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
 * @filesource mail_model.php
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

class Mail_model extends LX_Model {

	function __construct() {
		parent::__construct();
	}

	function get_mail($mail_id = 0, $list = 'unread_inbox') {
		if($mail_id != 0) {
			$sql = 'SELECT m.*, u.user_name mail_from, u.profile_picture sender_photo FROM mail m 
					JOIN users u ON(u.user_id = m.created_id) 
					WHERE m.mail_id = '. $mail_id;
		} else {
			switch ($list) {
				case 'inbox':
					$sql = "SELECT mr.*, m.*, u.user_name mail_from, u.profile_picture sender_photo FROM mail_receipt mr 
							JOIN mail m ON(m.mail_id = mr.mail_id AND m.status = 9) 
							JOIN users u ON(u.user_id = m.created_id) 
							WHERE mr.receipt_user_id = '" . get_user_id() . "'";
					break;
				case 'outbox':
					$sql = "SELECT * FROM mail m 
							WHERE m.status = 9 AND m.created_id = '" . get_user_id() . "'";
					break;
				
				case 'draft':
					$sql = "SELECT * FROM mail m 
							WHERE m.status = 1 AND m.created_id = '" . get_user_id() . "'";
					break;
				default:
					$sql = "SELECT m.*, u.user_name mail_from, u.profile_picture sender_photo 
							FROM mail_receipt mr 
							JOIN mail m ON(m.mail_id = mr.mail_id AND m.status = 9) 
							JOIN users u ON(u.user_id = m.created_id) 
							WHERE mr.read_time IS NULL AND mr.receipt_user_id = '" . get_user_id() . "'";
					break;
			}
		}

		return $this->db->query($sql);
	}

	function set_mail($status = 1) {
		$subject = (isset($_POST['subject'])) ? $_POST['subject'] : '';
		$body = (isset($_POST['body'])) ? $_POST['body'] : '';
		$mail = array();
		$mail['subject'] = $subject;
		$mail['body'] = $body;
		$mail['status'] = $status;
		$mail['created_id'] = get_user_id();
		if($status == 9) {
			$mail['delivery_time'] = date('Y-m-d H:i:s');
		}

		if($_POST['mail_id'] == 0) {
			$this->db->insert('mail', $mail);
			$mail_id = $this->db->insert_id();
		} else {
			$mail['receipt_text'] = '';
			$this->db->update('mail', $mail, array('mail_id' => $_POST['mail_id']));
			$mail_id = $_POST['mail_id'];
		}

		$this->db->delete('mail_receipt', array('mail_id' => $mail_id));
		if(isset($_POST['mail_receipt'])) {
			$receipt = array();
			foreach ($_POST['mail_receipt'] as $key => $value) {
				$this->db->insert('mail_receipt', array('mail_id' => $mail_id, 'receipt_user_id' => $value));

				$result = $this->db->get_where('users', array('user_id' => $value));
				$row = $result->row();
				$receipt[] = $row->user_name;
			}

			$receipt_text = implode(', ', $receipt);
			$this->db->update('mail', array('receipt_text' => $receipt_text));
		}

		return $mail_id;

	}

	function draft_mail() {
		$return = array('error' => '', 'message' => 'successfully save draft', 'execute' => '');
		$mail_id = $this->set_mail(1);
		$return['execute'] = "$('#mail_id').val(" . $mail_id . ")";

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	function send_mail() {
		$return = array('error' => '', 'message' => 'successfully send memo', 'execute' => '');
		$mail_id = $this->set_mail(9);
		$return['execute'] = "location.assign('" . site_url('mail/outbox') . "')";

		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}

	function set_read($mail_id) {
		$this->db->update('mail_receipt', array('read_time' => date('Y-m-d H:i:s')), array('mail_id' => $mail_id, 'receipt_user_id' => get_user_id()));
	}
	
	function get_mail_recipt($mail_id) {
		$sql = "SELECT mr.*, u.user_name receipt_name FROM mail_receipt mr
				JOIN users u ON(u.user_id = mr.receipt_user_id) 
				WHERE mr.mail_id = $mail_id";

		return $this->db->query($sql); 
	}

}

/* End of file mail_model.php */
/* Location: ./appl/models/mail_model.php */