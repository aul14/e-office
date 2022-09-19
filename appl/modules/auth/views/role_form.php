<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>

<script type="text/javascript">
<!--
	$(document).ready(function() {
<?php 
	if($mode != 'add') {
?>
		$('#btnDelete').click(function() {
			if(confirm("Delete Role " + $('#name').val())) { 
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
					data: "role_id=<?php echo $data->role_id ?>&mode=delete&action=auth.user_model.delete_role",
					success: function(data){	//alert(data);
						if(typeof(data.error) != 'undefined') {
							if(data.error != '') {
								alert(data.error);
							} else {
								alert(data.msg);
								location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('auth/user/role_permission')); ?>');
							}
						} else {
							alert('Data transfer error!');
						}
					}
				});  
			}
		});

<?php
	}
?>		
	});

//-->
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Administration<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-cogs"></i> Administration </a></li>
		<li class="active">Role & Permission</li>
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<?php 
	$form_param['action'] = 'auth.user_model.save_role';
	$form_param['mode'] = $mode;
	$form_param['role_id'] = ($mode == 'edit') ? $data->role_id : '';
	echo form_open('', ' class="data-form"', $form_param);
	echo show_message();
?>
<!-- Main content -->
<section class="content">

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
					<label for="name" class="col-sm-2 control-label">Role Name</label>
					<div class="col-sm-10">
<?php 
	$field_name = array(
					'name'        		=> 'name',
					'id'          		=> 'name',
					'class'       		=> 'form-control required',
					'data-input-title' 	=> ' Name',
					'value'       		=> ($mode == 'add') ? set_value('name') : $data->name,
					'maxlength'   		=> '50'
				);
	echo form_input($field_name); 
?>
					</div>
				</div>
			</fieldset>
			<table class="table table-striped table-bordered bootstrap-datatable datatable" id="data_table">
				<thead>
					<tr>
						<th width="60px"><input type="checkbox" id="check-all" name="check-all"></th>
						<th width="60px">ID</th>
						<th>Permission Name</th>
					</tr>
				</thead>
				<tbody>
<?php 

	if($list->num_rows() > 0) {
		foreach($list->result() as $row) {
?>
						<tr>
							<td><input type="checkbox" id="check-<?php echo $row->permission_id; ?>" name="security_permission[<?php echo $row->permission_id; ?>]" value="<?php echo $row->permission_id; ?>" <?php echo ($row->entry_id) ? 'checked="checked"' : ''; ?> /></td>
							<td><?php echo $row->permission_id; ?></td>
							<td><?php echo $row->name; ?></td>
						</tr>
<?php 
		}
	}
?>
				</tbody>
			</table>
			<div class="well">
				<div class="col-md-6">
					<div class="btn-group">
						<button type="button" id="btnBack" class="btn btn-primary" name="btnBack" onclick="location.assign('<?php echo site_url('auth/user/role_permission'); ?>')" ><i class="fa fa-chevron-left"></i> Back</button>
			
<?php 
	if($mode != 'add') {
?>
						<button type="submit" id="btnSave" class="btn btn-primary" name="btnSave" ><i class="fa fa-check"></i> Update</button>
<?php 
	} else {
?>
						<button type="submit" id="btnSave" class="btn btn-primary" name="btnSave" ><i class="fa fa-check"></i> Save</button>
<?php 
	}
?>
					</div>
				</div>
				<div class="col-md-6">
					<div class="btn-group pull-right">
<?php 
	if($mode != 'add') {
		echo '<button type="button" id="btnDelete" class="btn btn-danger" name="btnDelete"><i class="fa fa-trash-o"></i> Delete</button>';
	}
?>								
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		
		</div>
	</div>
</section>
<?php 
	echo form_close();
?>		