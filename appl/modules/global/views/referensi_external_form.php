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
 * @filesource referensi.php
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

	$(document).ready(function() {
//		CKEDITOR.disableAutoInline = true;
//		CKEDITOR.replace('format_text', { height: 500});
<?php 
	if($mode != 'add') {
?>
		$('#btnDelete').click(function() {
			if(confirm("Delete Referensi " + $('#format_surat_name').val() + " beserta sub.")) { 
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
					data: "entry_id=<?php echo $data->entry_id ?>&mode=delete&action=global.admin_model.delete_referensi",
					success: function(data){//alert(data);
						if(typeof(data.error) != 'undefined') {
							if(data.error != '') {
								alert(data.error);
							} else {
								alert(data.msg);
								location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/admin/tujuan_surat_eksternal')); ?>');
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
	echo form_open_multipart('', ' id="form_referensi_external" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', $mode);
	echo form_hidden('action', 'global.admin_model.save_referensi_external');
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
				<label for="instansi" class="col-lg-2 control-label">Nama</label>
				<div class="col-lg-10 ">
					<input type="text" maxlength="255" name="nama_pejabat" value="<?php echo ($mode == 'add') ? set_value('nama_pejabat') : $data->nama_pejabat; ?>" id="nama_pejabat" class="form-control required" data-input-title="nama pejabat" >
				</div>
			</div>
			<div class="form-group">
				<label for="instansi" class="col-lg-2 control-label">Jabatan</label>
				<div class="col-lg-10 ">
					<input type="text" maxlength="255" name="jabatan" value="<?php echo ($mode == 'add') ? set_value('jabatan') : $data->jabatan; ?>" id="jabatan" class="form-control required" data-input-title="jabatan" >
				</div>
			</div>
			<div class="form-group">
				<label for="instansi" class="col-lg-2 control-label">Instansi</label>
				<div class="col-lg-10 ">
					<input type="text" maxlength="255" name="instansi" value="<?php echo ($mode == 'add') ? set_value('instansi') : $data->instansi; ?>" id="instansi" class="form-control required" data-input-title="instansi" >
				</div>
			</div>
			<div class="form-group">
				<label for="instansi" class="col-lg-2 control-label">Alamat</label>
				<div class="col-lg-10 ">
					<input type="text" maxlength="255" name="address" value="<?php echo ($mode == 'add') ? set_value('address') : $data->address; ?>" id="address" class="form-control required" data-input-title="alamat" >
				</div>
			</div>
			<!--
			<div class="form-group">
				<div class="col-sm-10 col-sm-offset-2">
					<div class="toggle-switch toggle-switch-success">
						<label class="">
<?php 
		echo form_checkbox('status', '1', (($mode != 'add') ? (($data->status == '0') ? FALSE : TRUE) : TRUE), (($mode != 'add') ? '' : 'disabled="disabled"'));
?> &nbsp; Aktif
						</label>
					</div>
				</div>
			</div>
			-->
			<div class="well">
				<div class="col-md-6">
					<div class="btn-group">
						<button type="button" id="btnBack" class="btn btn-primary" name="btnBack" onclick="location.assign('<?php echo site_url('global/admin/tujuan_surat_eksternal'); ?>')" ><i class="fa fa-chevron-left"></i> Back</button>
<?php 
	if($mode != 'add') {
?>
						<button type="submit" id="btnSave" class="btn btn-primary" name="btnSave" ><i class="fa fa-check"></i> Update</button>
						<button type="button" id="btnAdd" class="btn btn-primary" name="btnAdd" onclick="location.assign('<?php echo site_url('global/admin/tujuan_surat_eksternal_detail'); ?>')" ><i class="fa fa-plus"></i> Add New</button>
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