
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

	<div class="row">
		<div class="col-md-3">
<?php 
?>
			<!-- Profile Image -->
			<div class="box box-primary">
				<div class="box-body box-profile">
<?php
	echo form_open_multipart('global/dashboard/ajax_handler', ' id="form_pp" class="form-horizontal"');
	echo form_hidden('action', 'auth.user_model.update_profile_pic');
	echo form_hidden('user_id', $user_id); 

	$img_path = base_url() . "assets/media";
	//$img_path = "assets/media";

?>
					<img id="user_profile" class="profile-user-img img-responsive img-circle" src="<?php echo ($data->profile_picture) ? str_replace('/lx_media', $img_path, $data->profile_picture) : '/lx_media/photo/m.jpg'; ?>" alt="User profile picture" style="height:128px; width:128px;">
					<h3 class="profile-username text-center"><?php echo $data->user_name; ?></h3>
					<div class="btn btn-default btn-file bg-red pull-right" title="Max file size 400kb.">
						<i class="fa fa-edit"></i> Ganti
						<input type="file" name="profile_picture" onchange="confirmPPChange();">
					</div>
<?php
	echo form_close(); 
?>
				</div><!-- /.box-body -->
				<script type="text/javascript">
					function confirmPPChange() {
						bootbox.confirm("Ganti foto profile anda?", function(result) {
							if(result) { 
								$('#form_pp').submit();
							} else {
								
							} 
						 });
					}

					var xhr_pp;
					$('#form_pp').ajaxForm({
						beforeSend: function(xhr) {
							xhr_pp = xhr; 
						},
						success: function() {
							//$('#bar-pp').width('100%');
						},
						error: function(data) {
							bootbox.alert('Upload failed');
							// $('#bar-pp').width('0%');
							// $('#pp').val('');
						},
						complete: function(xhr) {
							if(xhr.responseJSON.error == '') {
								bootbox.alert(xhr.responseJSON.message);
								// $('#bar-pp').width('0%');
								$('#user_profile').attr('src', xhr.responseJSON.src);
								$('#header_user_profile').attr('src', xhr.responseJSON.src);
								$('#dd_header_user_profile').attr('src', xhr.responseJSON.src);
							}
						}
					}); 

				</script>
			</div><!-- /.box -->

			<!-- About Me Box -->
			<div class="box box-primary">
				<div class="box-body">

<?php
	echo form_open_multipart('global/dashboard/ajax_handler', 'id="form_profile" class="form-horizontal"');
	echo form_hidden('action', 'auth.user_model.update_profile');
	echo form_hidden('user_id', $user_id); 
	echo form_hidden('organization_id', get_user_data('organization_id'));
	$role = $this->admin_model->get_parent_role();
?>
					<div class="form-group">
						<div class="col-sm-12">
<?php 
		
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

?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12">
<?php 
	$field_user_name = array(
					'name'		=> 'user_name',
					'id'		=> 'user_name',
					'class'		=> 'form-control required',
					'data-input-title' => ' Nama User',
					'placeholder' => 'Nama User',
					'value'		=> $data->user_name,
					'maxlength'	=> '50'
				);
	echo form_input($field_user_name); 
?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12">
<?php 
	$field_external_id = array(
					'name'		=> 'external_id',
					'id'		=> 'external_id',
					'class'		=> 'form-control required',
					'data-input-title' => ' NIP',
					'placeholder' => 'NIP',
					'value'		=> $data->external_id,
					'maxlength'	=> '30'
				);
	echo form_input($field_external_id); 
?>
						</div>
					</div>
					
					<div class="form-group">
						<div class="col-sm-12">
<?php 
	$field_email = array(
					'name'		=> 'email',
					'id'		=> 'email',
					'class'		=> 'form-control required',
					'data-input-title' => ' Email',
					'placeholder' => ' Email',
					'value'		=> $data->email,
					'maxlength'	=> '150'
				);
	echo form_input($field_email); 
?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12">
<?php 
	$field_phone_mobile = array(
					'name'		=> 'phone_mobile',
					'id'		=> 'phone_mobile',
					'class'		=> 'form-control required',
					'data-input-title' => ' Email',
					'placeholder' => ' HP',
					'value'		=> $data->phone_mobile,
					'maxlength'	=> '16'
				);
	echo form_input($field_phone_mobile); 
?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12">
<?php 
	$field_phone_home = array(
					'name'		=> 'phone_home',
					'id'		=> 'phone_home',
					'class'		=> 'form-control',
					'placeholder' => ' HP',
					'value'		=> $data->phone_home,
					'maxlength'	=> '16'
				);
	echo form_input($field_phone_home); 
?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-6">
<?php
	if(has_permission(3)) {
?>		
							<div class="toggle-switch toggle-switch-success">
								<label class="<?php echo ($mode != 'add') ? '' : 'hide'; ?>">
<?php 
		echo form_checkbox('active', '1', (($mode != 'add') ? (($data->active == '0') ? FALSE : TRUE) : TRUE), (($mode != 'add') ? '' : 'disabled="disabled"'));
?> &nbsp; Active
								</label>
							</div>
<?php
	}
?>
						</div>
						<div class="col-sm-6">
							<button type="button" class="btn btn-danger pull-right" onclick="updateProfile();">Simpan</button>
						</div>
					</div>
<?php
	echo form_close();
?>

				</div><!-- /.box-body -->
				<!-- Loading (remove the following to stop the loading)-->
                <div id="profil-overlay" class="overlay hide">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
                <!-- end loading -->
                <script type="text/javascript">
					function updateProfile() {
						bootbox.confirm("Update Profile?", function(result) {
							if(result) { 
								$("#form_profile").submit();
								//return;
							} 
						 });
						 
					}

					$('#form_profile').ajaxForm({
						beforeSend: function(xhr) {
							$('#profil-overlay').removeClass('hide');
						},
						success: function() {
							//$('#bar-pp').width('100%');

						},
						error: function(xhr) {
							bootbox.alert('Upload failed');
							// $('#bar-pp').width('0%');
							// $('#pp').val('');
						},
						complete: function(xhr) {
							$('#profil-overlay').addClass('hide');
							if(xhr.responseJSON.error == '') {
								bootbox.alert(xhr.responseJSON.message);
								// $('#bar-pp').width('0%');
								$('#user_profile').attr('src', xhr.responseJSON.src);
								$('#header_user_profile').attr('src', xhr.responseJSON.src);
								$('#dd_header_user_profile').attr('src', xhr.responseJSON.src);
							}
						}
					}); 

				</script>
			</div><!-- /.box -->
			
		</div><!-- /.col -->

		<div class="col-md-9">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<!--li><a href="#personal" data-toggle="tab">Data Pribadi</a></li-->
					<li class="active"><a href="#posisi" data-toggle="tab">Posisi</a></li>
					<li><a href="#informasi" data-toggle="tab">Informasi</a></li>
<?php 
	if(get_user_id() == $user_id || has_permission(1)) {
?>
					<li><a href="#password" data-toggle="tab">Password</a></li>
<?php 
	}
?>
				</ul>
				<div class="tab-content">
					<div class="active tab-pane" id="posisi">
<?php
	$this->load->view('user_posisi');
?>
					</div><!-- /.tab-pane -->

					<div class="tab-pane" id="informasi">
<?php 
	if($mode != 'add') {
		$create_by = $this->user_model->get_user($data->created_id)->row();
// 		echo $this->db->last_query();
		$modified_by = !$data->modified_id ? FALSE : $this->user_model->get_user($data->modified_id)->row();
		
?>
						<table width="100%" class="info-data">
							<tr>
								<td valign="top" width="35%" style="padding-left:10px;">Organization</td>
								<td valign="top"><strong><?php echo $data->organization_name; ?></strong></td>
							</tr>
							<tr>
								<td valign="top" style="padding-left:10px;">User ID</td>
								<td valign="top"><strong><?php echo $data->user_id; ?></strong></td>
							</tr>
							<tr>
								<td valign="top" style="padding-left:10px;">Last Login</td>
								<td valign="top"><strong><?php echo $data->last_login; ?></strong></td>
							</tr>
							<tr>
								<td colspan="2"><hr></td>
							</tr>
							<tr>
								<td valign="top" style="padding-left:10px;">Created by</td>
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
								<td valign="top" style="padding-left:10px;">Modified</td>
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
								<td valign="top" style="padding-left:10px;"><a href="javascript:void()" onclick="resendActivation()">Resend activation code</a></td>
								<td valign="top" align="right">
									<!-- <button type="button" id="btnPassword" name="btnPassword" class="btn btn-primary"><i class="fa fa-asterisk"></i> Reset Password</button> -->
<?php 
			if(($user_id != get_user_id()) && (has_permission(3))) {
				echo '<button type="button" id="btnDelete" class="btn btn-danger" name="btnDelete" ><i class="fa fa-trash-o"></i> Delete</button>';
			}
?>
								</td>
							</tr>
<?php 
		}
?>
						</table>
<?php 
	}
?>
					</div><!-- /.tab-pane -->
<?php 
	if(($user_id != get_user_id()) && (has_permission(3))) {
?>
					<div class="tab-pane" id="password">
<?php
		$this->load->view('user_password_adm');
?>
					</div><!-- /.tab-pane -->
<?php 
	}
?>

<?php 
	if(get_user_id() == $user_id) {
?>
					<div class="tab-pane" id="password">
<?php
		$this->load->view('user_password');
?>
					</div><!-- /.tab-pane -->
<?php 
	}
?>
				</div><!-- /.tab-content -->
			</div><!-- /.nav-tabs-custom -->
			<!-- Loading (remove the following to stop the loading)-->
			<div id="profil-overlay" class="overlay hide">
			  <i class="fa fa-refresh fa-spin"></i>
			</div>
			<!-- end loading -->
		</div><!-- /.col -->
	</div><!-- /.row -->
	<script type="text/javascript">
<?php 
	if(has_permission(3)) {
?>
	
	$('#btnDelete').click(function() {	
		if(confirm("Delete User " + $('#user_name').val())) { 
			$.ajax({
				type: "POST",
				url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url("global/admin/ajax_handler")); ?>",
				data: {user_id: '<?php echo $user_id ?>', mode: 'delete', action: 'auth.user_model.delete_user'},
				success: function(data){ //alert(data);
					//eval('var data=' + data);
					if (typeof(data.error) != 'undefined') {
						if (data.error != '') {
							alert(data.error);
						} else {
							//alert(data.msg);
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
	}
?>
	</script>
</section><!-- /.content -->