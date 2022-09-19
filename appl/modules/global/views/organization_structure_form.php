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
 * @filesource klasifikasi_arsip.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Oct 17, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

?>
<script type="text/javascript">
<!--
	$(document).ready(function() {
<?php 
	if($mode != 'add') {
?>
		$('#btnDelete').click(function() {
			if(confirm("Delete Kelasifikasi " + $('#nama_klasifikasi').val() + " beserta sub.")) { 
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
					data: "entry_id=<?php echo $data->entry_id ?>&mode=delete&action=global.admin_model.delete_klasifikasi_arsip",
					success: function(data){//alert(data);
						if(typeof(data.error) != 'undefined') {
							if(data.error != '') {
								alert(data.error);
							} else {
								alert(data.msg);
								location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/admin/klasifikasi_arsip')); ?>');
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
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">

<?php 
	echo form_open_multipart('', ' id="form_klasifikasi" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', $mode);
	echo form_hidden('action', 'global.admin_model.save_klasifikasi_arsip');
	echo ($mode != 'add') ? form_hidden('entry_id', $entry_id) : '';
	echo show_message();
?>
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
			<div class="form-group">
				<label for="kode_klasifikasi" class="col-sm-2 control-label">Kode Klasifikasi *</label>
				<div class="col-sm-3">
					<input type="text" name="kode_klasifikasi" value="<?php echo ($mode == 'add') ? set_value('kode_klasifikasi') : $data->kode_klasifikasi; ?>" id="kode_klasifikasi" class="form-control required" data-input-title=" Kode Klasifikasi" maxlength="16">
				</div>
				<label for="sex" class="col-sm-2 control-label">Parent</label>
				<div class="col-sm-5">
<?php 
	$parent = $this->admin_model->get_parent_klasifikasi_arsip_list();
	echo form_dropdown('parent_id', $parent, (($mode == 'add') ? set_value('parent_id') : $data->parent_id), ' class="form-control required" ');
?>
				</div>
			</div>
			<div class="form-group">
				<label for="user_name" class="col-sm-2 control-label">Nama Klasifikasi *</label>
				<div class="col-sm-6">
					<input type="text" name="nama_klasifikasi" value="<?php echo ($mode == 'add') ? set_value('nama_klasifikasi') : $data->nama_klasifikasi; ?>" id="nama_klasifikasi" class="form-control required" data-input-title=" Nama Klasifikasi" maxlength="100">
				</div>
				<label for="sort" class="col-sm-2 control-label">Sort</label>
				<div class="col-sm-2">
					<input type="number" name="sort" value="<?php echo ($mode == 'add') ? '1' : $data->sort; ?>" id="nama_klasifikasi" class="form-control" >
				</div>
			</div>
			<div class="well">
				<div class="col-md-6">
					<div class="btn-group">
						<button type="button" id="btnBack" class="btn btn-primary" name="btnBack" onclick="location.assign('<?php echo site_url('global/admin/klasifikasi_arsip'); ?>')" ><i class="fa fa-chevron-left"></i> Back</button>
			
<?php 
	if($mode != 'add') {
?>
						<button type="submit" id="btnSave" class="btn btn-primary" name="btnSave" ><i class="fa fa-check"></i> Update</button>
						<button type="button" id="btnAdd" class="btn btn-primary" name="btnAdd" onclick="location.assign('<?php echo site_url('global/admin/klasifikasi_arsip_detail'); ?>')" ><i class="fa fa-plus"></i> Add New</button>
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
		echo '<button type="button" id="btnDelete" class="btn btn-danger" name="btnDelete" ><i class="fa fa-trash-o"></i> Delete</button>';
	}
?>								
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		
		</div><!-- /.box-body -->
	</div><!-- /.box -->
<?php 
	echo form_close();
?>
</section><!-- /.content -->