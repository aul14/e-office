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
			if(confirm("Delete Struktur " + $('#unit_name').val() + " beserta sub.")) { 
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
					data: "organization_structure_id=<?php echo $data->organization_structure_id ?>&mode=delete&action=global.admin_model.delete_org_structure",
					success: function(data){	//alert(data);
						if(typeof(data.error) != 'undefined') {
							if(data.error != '') {
								alert(data.error);
							} else {
								alert(data.msg);
								location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/admin/org_structure')); ?>');
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
	echo form_open_multipart('', ' id="form_org_structure" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', $mode);
	echo form_hidden('action', 'global.admin_model.save_org_structure');
	echo ($mode != 'add') ? form_hidden('organization_structure_id', $organization_structure_id) : '';
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
				<label for="unit_code" class="col-sm-2 control-label">Kode Unit *</label>
				<div class="col-sm-3">
					<input type="text" name="unit_code" value="<?php echo ($mode == 'add') ? set_value('unit_code') : $data->unit_code; ?>" id="unit_code" class="form-control required" data-input-title=" Kode Unit" maxlength="16">
				</div>
				<label for="sex" class="col-sm-2 control-label">Parent</label>
				<div class="col-sm-5">
<?php 
	$parent = $this->admin_model->get_parent_org_structure_list();
	echo form_dropdown('parent_id', $parent, (($mode == 'add') ? set_value('parent_id') : $data->parent_id), ' class="form-control" ');
?>
				</div>
			</div>
			<div class="form-group">
				<label for="unit_name" class="col-sm-2 control-label">Nama Unit *</label>
				<div class="col-sm-10">
					<input type="text" name="unit_name" value="<?php echo ($mode == 'add') ? set_value('unit_name') : $data->unit_name; ?>" id="nama_klasifikasi" class="form-control required" data-input-title=" Nama Unit" maxlength="100">
				</div>
			</div>
<?php 
	if($mode != 'add') {
?>
			<div class="form-group">
				<label for="unit_name" class="col-sm-2 control-label">Sub Unit </label>
				<div class="col-sm-10">
<?php 
	
		$list = $this->admin_model->get_subordinates($organization_structure_id, 1);
//		var_dump($list);
		$opt_subordinates = array('' => '--Pilih--');
		foreach ($list as $row) {
			$opt_subordinates[$row['organization_structure_id']] = $row['unit_name'];
		}
		
		echo form_dropdown('sub_id', $opt_subordinates, (($mode == 'add') ? set_value('sub_id') : $data->sub_id), (' id="sub_id" class="form-control select2" '));
?>
				</div>
			</div>
<?php 
	}
?>
			<div class="form-group">
				<label for="unit_name" class="col-sm-2 control-label">Kode Surat</label>
				<div class="col-sm-4">
					<input type="text" name="official_code" value="<?php echo ($mode == 'add') ? set_value('official_code') : $data->official_code; ?>" id="official_code" class="form-control" data-input-title=" Kode Surat" maxlength="10">
				</div>
				<label for="unit_name" class="col-sm-2 control-label">Singkatan</label>
				<div class="col-sm-4">
					<input type="text" name="abv" value="<?php echo ($mode == 'add') ? set_value('abv') : $data->abv; ?>" id="abv" class="form-control" data-input-title=" Singkatan" maxlength="10">
				</div>
			</div>
			<div class="form-group">
				<label for="description" class="col-lg-2 col-sm-3 control-label">Deskripsi</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="description" name="description" class="form-control" rows="3" placeholder="Deskripsi" data-input-title="Deskripsi" ><?php echo ($mode == 'add') ? set_value('description') : $data->description; ?></textarea>
				</div>
			</div>
			<!-- div class="form-group">
				<label for="no_surat_internal" class="col-lg-2 col-sm-3 control-label">Format No. Surat Internal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="no_surat_internal" name="no_surat_internal" class="form-control" rows="3" placeholder="Deskripsi" ><?php echo ($mode == 'add') ? set_value('no_surat_internal') : $data->no_surat_internal; ?></textarea>
				</div>
			</div -->
			<div class="well">
				<div class="col-md-6">
					<div class="btn-group">
						<button type="button" id="btnBack" class="btn btn-primary" name="btnBack" onclick="location.assign('<?php echo site_url('global/admin/org_structure'); ?>')" ><i class="fa fa-chevron-left"></i> Back</button>
			
<?php 
	if($mode != 'add') {
?>
						<button type="submit" id="btnSave" class="btn btn-primary" name="btnSave" ><i class="fa fa-check"></i> Update</button>
						<button type="button" id="btnAdd" class="btn btn-primary" name="btnAdd" onclick="location.assign('<?php echo site_url('global/admin/org_structure_detail'); ?>')" ><i class="fa fa-plus"></i> Add New</button>
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

</section>	<!-- /.content -->