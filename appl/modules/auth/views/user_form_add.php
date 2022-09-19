
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Administration<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>"><i class="fa fa-cogs"></i> Administration </a></li>
		<li><a href="<?php echo site_url('auth/user'); ?>">User</a></li>
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php 
	
	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', $mode);
	echo form_hidden('action', 'auth.user_model.save_user');
	echo form_hidden('user_id', $user_id); 
	echo show_message();
?>
			<!-- About Me Box -->
			<div class="box box-primary">
				<div class="box-body">
					<div class="form-group">
						<label for="role_id" class="col-sm-4 control-label">Role *</label>
						<div class="col-sm-8">
<?php 
	$role = $this->admin_model->get_parent_role();
	if(get_role() != 1) {
		foreach($role as $k => $v) {
			if ($k < get_role()) {
				unset($role[$k]);
			}
		}
	}
	echo form_dropdown('role_id', $role, set_value('role_id'), 'id="role_id" class="form-control"');
?>
						</div>
					</div>
					<div class="form-group">
						<label for="user_name" class="col-sm-4 control-label">Nama *</label>
						<div class="col-sm-8">
<?php 
	$field_user_name = array(
					'name'			   => 'user_name',
					'id'			   => 'user_name',
					'class'			   => 'form-control required',
					'data-input-title' => ' Nama User',
					'placeholder' 	   => 'Nama User',
					'value'			   => set_value('user_name'),
					'maxlength'		   => '50'
				);
	echo form_input($field_user_name); 
?>
						</div>
					</div>
					<div class="form-group">
						<label for="external_id" class="col-sm-4 control-label">NIP *</label>
						<div class="col-sm-8">
<?php 
	$field_external_id = array(
					'name'			   => 'external_id',
					'id'			   => 'external_id',
					'class'			   => 'form-control required',
					'data-input-title' => ' NIP',
					'placeholder' 	   => 'NIP',
					'value'			   => set_value('external_id'),
					'maxlength'		   => '30'
				);
	echo form_input($field_external_id); 
?>
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-4 control-label">Email *</label>
						<div class="col-sm-8">
<?php 
	$field_email = array(
					'name'			   => 'email',
					'id'			   => 'email',
					'class'			   => 'form-control required',
					'data-input-title' => ' Email',
					'placeholder' 	   => ' Email',
					'value'			   => set_value('email'),
					'maxlength'		   => '150'
				);
	echo form_input($field_email); 
?>
						</div>
					</div>
					<fieldset>
						<legend>&nbsp;</legend>
						<div class="form-group">
							<label for="password" class="col-sm-4 control-label">Password *</label>
							<div class="col-sm-8">
<?php 
		$field_password = array(
						'name'			   => 'password',
						'id'			   => 'password',
						'class'			   => 'form-control required',
						'data-input-title' => ' Password'
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
						'name'			   => 'conf_password',
						'id'			   => 'conf_password',
						'class'			   => 'form-control required',
						'data-input-title' => 'Confirm Password'
					);
		echo form_password($field_password); 
?>
							</div>
						</div>
					</fieldset>
					<div class="form-group">
						<div class="col-sm-6 ">
						
						</div>
						<div class="col-sm-6 ">
							<button class="btn btn-danger pull-right" onclick="">Simpan</button>
						</div>
					</div>
					<hr>
					
				</div><!-- /.box-body -->
			</div><!-- /.box -->
			
<?php 
	echo form_close(); 
?>

</section><!-- /.content -->