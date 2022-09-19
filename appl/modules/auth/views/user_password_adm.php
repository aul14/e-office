
<?php 
	
	echo form_open('', 'id="form_password" class="form-horizontal"');
	echo form_hidden('action', 'auth.user_model.update_password_adm');
	echo form_hidden('user_id', $user_id); 
	echo show_message();

?>
		
					<fieldset>
					<!--	
						<div class="form-group">
							<label for="password" class="col-sm-3 control-label">Old Password *</label>
							<div class="col-sm-9">
<?php 
		// $field_password = array(
		// 				'name'		=> 'old_password',
		// 				'id'		=> 'old_password',
		// 				'class'		=> 'form-control required',
		// 				'data-input-title' => ' Old Password'
		// 			);
		// echo form_password($field_password); 
?>
							</div>
						</div>
						<div class="clearfix"></div>
					-->	
						<fieldset>
							<!--<legend>&nbsp;</legend>-->
							<div class="form-group">
								<label for="password" class="col-sm-3 control-label">New Password *</label>
								<div class="col-sm-9">
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
								<label for="conf_password" class="col-sm-3 control-label">Confirm Password *</label>
								<div class="col-sm-9">
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
								<button type="submit" id="btnSave" class="btn btn-primary" name="btnDelete" ><i class="fa fa-check"></i> Update</button>
							</div>
						</div>

					</fieldset>

<?php 
	echo form_close(); 
?>