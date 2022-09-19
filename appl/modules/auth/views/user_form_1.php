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
 * @filesource user_form.php
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

	$role = $this->admin_model->get_parent_role();
?>
<script type="text/javascript" charset="utf-8">
		
	var active_uid = '<?php echo get_user_id(); ?>';

	$(document).ready(function() {

		$('#user_name').focus();
		
	});
	
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Administration<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>"><i class="fa fa-cogs"></i> Administration </a></li>
		<li><a href="<?php echo site_url('auth/user'); ?>">User</a></li>
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<section class="content">

<div class="row">
<?php 
	
	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', $mode);
	echo form_hidden('action', 'auth.user_model.save_user');
//	echo ($mode != 'add') ? form_hidden('user_id', $user_id) : ''; 
	echo form_hidden('user_id', $user_id); 
	echo show_message();
?>
	<section class="col-xs-9 col-sm-9">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $title; ?></h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>

			<div class="box-body">

				<fieldset>
					<div class="form-group">
						<label for="user_name" class="col-sm-2 control-label">Name *</label>
						<div class="col-sm-4">
<?php 
	$field_user_name = array(
					'name'				=> 'user_name',
					'id'				=> 'user_name',
					'class'				=> 'form-control required',
					'data-input-title' 	=> ' User Name',
					'value'				=> ($mode == 'add') ? set_value('user_name') : $data->user_name,
					'maxlength'			=> '50'
				);
	if($mode == 'edit' && !has_permission(3)) {
		$field_user_name['readonly'] = 'readonly';
	}
	echo form_input($field_user_name); 
?>
						</div>
						<label for="sex" class="col-sm-2 control-label">Gender</label>
						<div class="col-sm-4">
<?php 
	if($mode == 'edit' && !has_permission(3)) {
		echo '<label id="label_sex_m" class="label_sex> ' . (($data->sex == 'm') ? 'Male' : 'Female') . ' </label>'; 
	} else {
?>
							<label id="label_sex_m" class="label_sex "> <input type="radio" id="sex_m" name="sex" class="minimal" value="m" <?php echo ($mode == 'edit') ? (($data->sex == 'm') ? 'checked="checked" ' : '') : 'checked="checked" '; ?>> &nbsp; Male </label>
							 &nbsp;  &nbsp; 
							<label id="label_sex_f" class="label_sex "> <input type="radio" id="sex_f" name="sex" class="minimal" value="f" <?php echo ($mode == 'edit') ? (($data->sex == 'f') ? 'checked="checked" ' : '') : ''; ?>> &nbsp; Female </label>
<?php 
	}
?>
						</div>
					</div>
					<div class="form-group">
						<label for="empl_id" class="col-sm-2 control-label">Employee ID</label>
						<div class="col-sm-4">
<?php 
	$field_ex_id = array(
					'name'		=> 'external_id',
					'id'		=> 'external_id',
					'class'		=> 'form-control required',
					'data-input-title' => ' Employee ID',
					'value'		=> ($mode == 'add') ? set_value('external_id') : $data->external_id,
					'maxlength'	=> '255'
				);
	if($mode == 'edit' && !has_permission(3)) {
		$field_ex_id['readonly']	= 'readonly';
	}
	echo form_input($field_ex_id); 
?>

						</div>
						<label for="email" class="col-sm-2 control-label">Email *</label>
						<div class="col-sm-4">
<?php 
	$field_email = array(
					'name'		=> 'email',
					'id'		=> 'email',
					'class'		=> 'form-control required',
					'data-input-title' => ' Email',
					'value'		=> ($mode == 'add') ? set_value('email') : $data->email,
					'maxlength'	=> '255'
				);
	if($mode == 'edit' && !has_permission(3)) {
		$field_email['readonly']	= 'readonly';
	}
	echo form_input($field_email); 
?>
						</div>
					</div>
<?php 
	if($mode == 'add') { 
?>
					<fieldset id="field_password" class="<?php echo ($mode == 'edit') ? 'hide' : ''; ?>">
						<legend>&nbsp;</legend>
						<div class="form-group">
							<label for="password" class="col-sm-2 control-label">Password *</label>
							<div class="col-sm-10">
<?php 
		$field_password = array(
						'name'		=> 'password',
						'id'		=> 'password',
						'class'		=> 'form-control ' . (($mode == 'edit') ? '' : 'required'),
						'data-input-title' => ' Password'
					);
		echo form_password($field_password); 
?>
							</div>
						</div>
						<div class="form-group">
							<label for="conf_password" class="col-sm-2 control-label">Confirm *</label>
							<div class="col-sm-10">
<?php 
		$field_password = array(
						'name'		=> 'conf_password',
						'id'		=> 'conf_password',
						'class'		=> 'form-control ' . (($mode == 'edit') ? '' : 'required'),
						'data-input-title' => ' Confirm Password'
					);
		echo form_password($field_password); 
?>
							</div>
						</div>
					</fieldset>
<?php 
	}
?>
					<fieldset>
						<legend>&nbsp;</legend>
						<div class="form-group">
							<label for="phone_home" class="col-sm-2 control-label">Phone home</label>
							<div class="col-sm-4">
<?php 
	$field_phone_home = array(
					'name'		=> 'phone_home',
					'id'		=> 'phone_home',
					'class'		=> 'form-control',
					'value'		=> ($mode == 'add') ? set_value('phone_home') : $data->phone_home,
					'maxlength'	=> '100'
				);
	echo form_input($field_phone_home); 
?>
							</div>
							<label for="phone_mobile" class="col-sm-2 control-label">Mobile</label>
							<div class="col-sm-4">
<?php 
	$field_phone_mobile = array(
					'name'		=> 'phone_mobile',
					'id'		=> 'phone_mobile',
					'class'		=> 'form-control',
					'value'		=> ($mode == 'add') ? set_value('phone_mobile') : $data->phone_mobile,
					'maxlength'	=> '100'
				);
	echo form_input($field_phone_mobile); 
?>
							</div>
						</div>
						<div class="form-group">
							<label for="address1" class="col-sm-2 control-label">Address</label>
							<div class="col-sm-10">
<?php 
	$field_address1 = array(
					'name'		=> 'address1',
					'id'		=> 'address1',
					'class'		=> 'form-control',
					'data-input-title' => ' Address',
					'value'		=> ($mode == 'add') ? set_value('address1') : $data->address1,
					'maxlength'	=> '100'
				);
	echo form_input($field_address1); 
?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2">
<?php 
	$field_address2 = array(
					'name'		=> 'address2',
					'id'		=> 'address2',
					'class'		=> 'form-control',
					'value'		=> ($mode == 'add') ? set_value('address2') : $data->address2,
					'maxlength'	=> '100'
				);
	echo form_input($field_address2); 
?>
							</div>
						</div>
						<div class="form-group">
							<label for="city" class="col-sm-2 control-label">City</label>
							<div class="col-sm-6">
<?php 
	$field_city = array(
					'name'		=> 'city',
					'id'		=> 'city',
					'class'		=> 'form-control',
					'value'		=> ($mode == 'add') ? set_value('city') : $data->city,
					'maxlength'	=> '200'
				);
	echo form_input($field_city); 
?>
							</div>
							<label for="zip" class="col-sm-2 control-label">Post Code</label>
							<div class="col-sm-2">
<?php 
	$field_zip = array(
					'name'		=> 'zip',
					'id'		=> 'zip',
					'class'		=> 'form-control',
					'value'		=> ($mode == 'add') ? set_value('zip') : $data->zip,
					'maxlength'	=> '5'
				);
	echo form_input($field_zip); 
?>
							</div>
						</div>
						
					</fieldset>
					<fieldset>
						<legend>&nbsp;</legend>
						

					</fieldset>
						<div class="well">
							<div class="col-md-6">
								<div class="btn-group">
<?php
	if($mode != 'add') {
		$val = ($user_id == get_user_id()) ? 'Change Password' : 'Reset Password';
		echo '<button type="button" id="btnPassword" name="btnPassword" class="btn btn-primary"><i class="fa fa-asterisk"></i> ' . $val . '</button>';
		echo '<button type="submit" id="btnSave" name="btnSave" class="btn btn-primary"><i class="fa fa-check"></i> Update</button>';
		echo '<button type="button" id="btnAdd" name="btnAdd" class="btn btn-primary" onclick="location.assign(\'' . site_url('auth/user/user_add') . '\')"><i class="fa fa-plus"></i> Add New</button>';
	} else {
		echo '<button type="submit" id="btnSave" name="btnSave" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>';
	}
?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="btn-group pull-right">

<?php
	if($mode != 'add') {
		if(($user_id != get_user_id()) && (has_permission(3))) {
			echo '<button type="button" id="btnDelete" class="btn btn-danger" name="btnDelete" ><i class="fa fa-trash-o"></i> Delete</button>';
		}
	}
?>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>

				</fieldset>
				
				<script type="text/javascript">
<?php 
	if($mode != 'add' && has_permission(3)) {
?>
	$('#btnDelete').click(function() {	
		if(confirm("Delete User " + $('#user_name').val())) { 
			$.ajax({
				type: "POST",
				url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url("admin/ajax_handler")); ?>",
				data: {user_id: '<?php echo $user_id ?>', mode: 'delete', action: 'auth.user_model.delete_user'},
				success: function(data){//alert(data);
					eval('var data=' + data);
					if(typeof(data.error) != 'undefined') {
						if(data.error != '') {
							alert(data.error);
						} else {
							alert(data.msg);
							location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('auth/user')); ?>');
						}
					} else {
						alert('Data transfer error!');
					}
				}
			});  
		}
	});

	$('#btnPassword').click(function() {
		if(active_uid == $('input[name=user_id]').val()) {
			location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('auth/user/change_password')); ?>/' + $('input[name=user_id]').val());
		} else { 	
			if(confirm("Reset password '" + $('#user_name').val() + "'?")){
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('auth/user/reset_password')); ?>/" + $('input[name=user_id]').val(),
					data: {user_id: '<?php echo $user_id ?>'},
					success: function(data){
						bootbox.alert(data);
					}
				}); 
			}
		}

	});

	function resendActivation() {
		if(confirm("Resend Activation mail to " + $('#user_name').val() + "'?")){
			$.ajax({
				type: "POST",
				url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/admin/ajax_handler')); ?>/" + $('input[name=user_id]').val(),
				data: {action: 'auth.user_model.resend_activation', user_id: '<?php echo $user_id ?>'},
				success: function(data){
					bootbox.alert(data);
				}
			}); 
		}
	}

<?php 	
	} else {
?>
	$('#btnPassword').click(function() {
		location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('auth/user/change_password/')); ?>/' + $('input[name=user_id]').val());
		
	});

<?php
	}
?>

				</script>
			</div>
		</div>
	</section>
	
	<section class="col-xs-3 col-sm-3">

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-image"></i> Profile Picture</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>

			<div class="box-body">
				<div class="form-group">
					<div id="profile_picture_view" class="col-sm-12" style="text-align: center;">
<?php

	if(($mode != 'add') && ($data->profile_picture)) {
		echo '<img src="' . $data->profile_picture . '" style="width:100px;"/> <br/>';
	}
?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
<?php 
	$field_pp = array(
		'name'		=> 'profile_picture',
		'id'		=> 'profile_picture',
		'class'		=> 'form-control',
		'maxlength'	=> '10'
		);
	echo form_upload($field_pp); 
?>
					</div>
				</div>
			</div>
		</div>

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">State</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="form-group">
					<label for="role_id" class="col-sm-3 control-label">Role</label>
					<div class="col-sm-9">
<?php 
		if($mode != 'add') {
			if(get_role() == 1) {
				if($data->role_id < get_role()) {
					echo "<strong>" . $role[$data->role_id] . "</strong>";
				} else {
					if(get_role() != 1) {
						foreach($role as $k => $v) {
							if ($k < get_role()) {
								unset($role[$k]);
							}
						}
					}
					echo form_dropdown('role_id', $role, $data->role_id, 'id="role_id" class="form-control"');
				} 
			} else {
				echo form_hidden('role_id', $data->role_id);
				echo "<strong>" . $role[$data->role_id] . "</strong>";
			}
		} else {
			if(get_role() != 1) {
				foreach($role as $k => $v) {
					if ($k < get_role()) {
						unset($role[$k]);
					}
				}
			}
			echo form_dropdown('role_id', $role, set_value('role_id'), 'id="role_id" class="form-control"');
		}
?>
					</div>
				</div>

<?php
	if(has_permission(3)) {
?>		
				<div class="form-group">
					<div class="col-sm-6 col-sm-offset-3">
						<div class="toggle-switch toggle-switch-success">
							<label class="<?php echo ($mode != 'add') ? '' : 'hide'; ?>">
<?php 
		echo form_checkbox('active', '1', (($mode != 'add') ? (($data->active == '0') ? FALSE : TRUE) : TRUE), (($mode != 'add') ? '' : 'disabled="disabled"'));
?> Active
							</label>
						</div>
					</div>
				</div>
<?php
		echo form_hidden('organization_id', get_user_data('organization_id'));
	}
?>
			</div>
		</div>
		
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-info"></i> Information</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			
			<div class="box-body">
<?php 
	if($mode != 'add') {
		$create_by = $this->user_model->get_user($data->created_id)->row();
		$modified_by = !$data->modified_id ? FALSE : $this->user_model->get_user($data->modified_id)->row();
		
?>
				<table width="100%" class="info-data">
					<tr>
						<td valign="top" width="45%">Organization</td>
						<td valign="top"><strong><?php echo $data->organization_name; ?></strong></td>
					</tr>
					<tr>
						<td valign="top" width="45%">User ID</td>
						<td valign="top"><strong><?php echo $data->user_id; ?></strong></td>
					</tr>
					<tr>
						<td valign="top">Last Login</td>
						<td valign="top"><strong><?php echo $data->last_login; ?></strong></td>
					</tr>
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr>
						<td valign="top">Created by</td>
						<td valign="top"><strong><?php echo ($create_by) ? $create_by->user_name : ''; ?></strong></td>
					</tr>
					<tr>
						<td valign="top"></td>
						<td valign="top"><strong><?php echo $data->created_time; ?></strong></td>
					</tr>
<?php 
		
		if($modified_by) {
?>					
					<tr>
						<td valign="top">Modified</td>
						<td valign="top"><strong><?php echo $modified_by->user_name; ?></strong></td>
					</tr>
					<tr>
						<td valign="top"></td>
						<td valign="top"><strong><?php echo $data->modified_time; ?></strong></td>
					</tr>
<?php 
		}
		if(has_permission(3)) {
?>
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr>
						<td valign="top" colspan="2"><a href="javascript:void()" onclick="resendActivation()">Resend activation code</a></td>
					</tr>
<?php 
		}
?>
				</table>
<?php 
	}
?>
			</div>
		</div>
	</section>
<?php 
	echo form_close(); 
?>
</div>
</section>
