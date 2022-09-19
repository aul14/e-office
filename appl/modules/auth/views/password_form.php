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
 * @filesource password_form.php
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
		
	$(document).ready(function() {
	
	});
	
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Administration<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-cogs"></i> Administration </a></li>
		<li><a href="#">User</a></li>
		<li><a href="#">Edit User</a></li>
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">

	<div class="row">
<?php 
	
	echo form_open('', 'id="form_password" class="form-horizontal"');
	echo form_hidden('action', 'auth.user_model.update_password');
	echo form_hidden('user_id', $user_id); 
	echo show_message();
?>
		<section class="col-xs-9 col-sm-9">
			<!-- Default box -->
			<div class="box">
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
							<label for="password" class="col-sm-4 control-label">Old Password *</label>
							<div class="col-sm-8">
<?php 
		$field_password = array(
						'name'				=> 'old_password',
						'id'				=> 'old_password',
						'class'				=> 'form-control required',
						'data-input-title' 	=> ' Old Password'
					);
		echo form_password($field_password); 
?>
							</div>
						</div>
						<div class="clearfix"></div>
						<fieldset>
							<legend>&nbsp;</legend>
							<div class="form-group">
								<label for="password" class="col-sm-4 control-label">New Password *</label>
								<div class="col-sm-8">
<?php 
		$field_password = array(
						'name'				=> 'password',
						'id'				=> 'password',
						'class'				=> 'form-control required',
						'data-input-title' 	=> ' New Password'
					);
		echo form_password($field_password); 
?>
								</div>
							</div>
							<div class="form-group">
								<label for="conf_password" class="col-sm-4 control-label">Confirm Password *</label>
								<div class="col-sm-8">
<?php 
		$field_password = array(
						'name'				=> 'conf_password',
						'id'				=> 'conf_password',
						'class'				=> 'form-control required',
						'data-input-title' 	=> ' Confirm New Password'
					);
		echo form_password($field_password); 
?>
								</div>
							</div>
						</fieldset>
						
						<div class="clearfix"></div>
						<hr>
						
						<div class="well">
							<div class="btn-group">
								<button type="button" id="btnBack" class="btn btn-primary" name="btnBack" onclick="location.assign('<?php echo site_url('auth/user/user_edit/' . $user_id); ?>')" ><i class="fa fa-chevron-left"></i> Back</button>
								<button type="submit" id="btnSave" class="btn btn-primary" name="btnDelete" ><i class="fa fa-check"></i> Update</button>
							</div>
						</div>
					</fieldset>
				</div>
			</div><!-- /.box -->
		</section><!-- /.content -->
		
		<section class="col-xs-3 col-sm-3">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Information</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body">

				</div>
			</div>
		</section>
<?php 
	echo form_close(); 
?>
	</div>
</section>