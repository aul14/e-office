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
<!--
	$(document).ready(function() {
//		CKEDITOR.disableAutoInline = true;
//		CKEDITOR.replace('format_text', { height: 500});

		$('#data_table').dataTable({
			"bPaginate": false,
			"ordering": false,
			"bFilter": false,
			"bInfo": false
		});

		$('#surat_to_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_to_kode').val(ui.item.unit_code);
				$('#surat_to_unit_kode').html(ui.item.unit_code);
				$('#surat_to_unit_id').val(ui.item.id);
				$('#surat_to_jabatan').val(ui.item.jabatan);
				$('#surat_to_pangkat').val(ui.item.pangkat);
				$('#surat_to_nama').val(ui.item.nama_pejabat);
				$('#surat_to_nip').val(ui.item.nip_pejabat);
				$('#surat_to_dir').val(ui.item.instansi);
			}
		});
		
		$('#surat_to_unit').keyup(function() {
			if($(this).val().trim() == '') {
				$('#surat_to_kode').val('');
				$('#surat_to_unit_kode').html('________');
				$('#surat_to_unit_id').val('');
				$('#surat_to_nama').val('');
				$('#surat_to_nip').val('');
				$('#surat_to_dir').val('');
			}
		});

<?php
	if($mode == 'add') {
?>
		var listArr = [];
		$('#data_table tbody').html('');
<?php
	}else {
?>
		var listArr = '<?php echo (isset($data->to_user_data) && $data->to_user_data != '') ? $data->to_user_data : ''; ?>';
		listArr = (listArr) ? JSON.parse(listArr) : listArr;
<?php
	}
?>

		$('#add_tujuan').click(function(){
			var data_tujuan = {};
			var unit_id = $('#surat_to_unit_id').val();
			if (unit_id != '') {
				unit_id = unit_id;
			}else {
				unit_id = '00' + 1;
				unit_id = unit_id + 1;
			}
			var unit 	= $('#surat_to_unit').val();
			var nama 	= $('#surat_to_nama').val();
			var nip 	= $('#surat_to_nip').val();
			var jabatan = $('#surat_to_jabatan').val();
			var pangkat = $('#surat_to_pangkat').val();
			var dir 	= $('#surat_to_dir').val();
			
			if (unit != '') {
				data_tujuan['unit'] 	= unit;
				data_tujuan['unit_id'] 	= unit_id;
				data_tujuan['nama'] 	= nama;
				data_tujuan['nip'] 		= nip;
				data_tujuan['jabatan'] 	= jabatan;
				data_tujuan['pangkat'] 	= pangkat;
				data_tujuan['dir'] 		= dir;
				
				listArr.push(data_tujuan);
							
				var txtList = JSON.stringify(listArr);				
				$('#to_user_data').text(txtList);
				
				$('#data_table tbody').append('<tr id=' + unit_id + '>' +
										'<td>' + nama + '</td>' +
										'<td>' + jabatan + '</td>' +
										'<td>' + unit + '</td>' +
										'<td>' + dir + '</td>' +
										'<td><div style="cursor: pointer;" onClick="hapus_tujuan(' + unit_id + ')"><i class="fa fa-minus-circle" title="hapus"></i> Hapus</div></td>' +
										'</tr>' 
									);				
				
				$('#surat_to_unit_id').val('');
				$('#surat_to_unit_kode').html('________');
				$('#surat_to_unit').val('');
				$('#surat_to_nama').val('');
				$('#surat_to_jabatan').val('');
				$('#surat_to_pangkat').val('');
				$('#surat_to_dir').val('');
				$('#surat_to_unit').focus();
			}else {
				alert('Data tidak lengkap.');
			}
		});

	}); //end document
	
	function hapus_tujuan(unit_id) {
		$('#' + unit_id).remove();

		var list = $('#to_user_data').text();
		
		list = JSON.parse(list);
		
		for( var i = 0; i < list.length; i++){ 
		   if (list[i].unit_id == unit_id) {
		    	list.splice(i, 1); 
		   		
		   		var txtList = JSON.stringify(list);
		   		$('#to_user_data').text(txtList);
		   }
		}
	}

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
	echo form_open_multipart('', ' id="form_referensi" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', $mode);
	echo form_hidden('action', 'global.admin_model.save_tujuan_surat');
	echo ($mode != 'add') ? form_hidden('tujuan_surat_id', $tujuan_surat_id) : '';
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
				<label for="nama_pejabat" class="col-sm-1 control-label">Judul *</label>
				<div class="col-sm-7">
					<input type="text" id="title" name="title" class="form-control required" data-input-title="Judul" value="<?php echo ($mode == 'add') ? set_value('title') : $data->title; ?>">
				</div>
				<label for="fungsi" class="col-sm-1 control-label">Fungsi *</label>
				<div class="col-sm-3">
<?php 
	$function_ref = $this->admin_model->get_parent_function_internal();
	echo form_dropdown('function_ref_id', $function_ref, (($mode == 'add') ? set_value('function_ref_id') : $data->function_ref_id), ' class="form-control required" ');
?>					
				</div>
			</div>
			<div class="col-sm-12">
				<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
					<thead>
						<tr>
							<th>Nama</th>
							<th>Jabatan</th>
							<th>Unit</th>
							<th>Direktorat</th>
							<th width="50">Action</th>
						</tr>
					</thead>
					<tbody>	
<?php
	if($mode != 'add') {
		if(isset($data->to_user_data) && $data->to_user_data != '') {
			$no = 1;
			$distribusi_tujuan = json_decode($data->to_user_data, TRUE);
			foreach($distribusi_tujuan as $distribusi) {
?>
					<tr id="<?php echo $distribusi['unit_id']; ?>">
						<td><?php echo $distribusi['nama']; ?></td>
						<td><?php echo $distribusi['jabatan']; ?></td>
						<td><?php echo $distribusi['unit']; ?></td>
						<td><?php echo $distribusi['dir']; ?></td>
						<td><div style="cursor: pointer;" onclick="hapus_tujuan(<?php echo $distribusi['unit_id']; ?>);"><i class="fa fa-minus-circle" title="hapus"></i> Hapus</div></td>
					</tr>	
<?php				
				$no++;
			}
		}
	}
?>						
					</tbody>	
				</table>
				<br/>
			</div>
			<div class="form-group">
				<label for="instansi" class="col-lg-1 control-label">Unit</label>
				<div class="col-lg-4">
					<!-- <div class="input-group"> -->
						<input type="text" maxlength="255" id="surat_to_unit" value="" class="form-control" placeholder="Unit ...." data-input-title="unit">
						<!--
						<div id="surat_to_unit_kode" class="input-group-addon"><?php echo (isset($surat_to_ref_data['kode'])) ? $surat_to_ref_data['kode'] : '________'; ?></div>
						-->
					<!-- </div> -->
				</div>
				<label for="instansi" class="col-lg-2 control-label">Jabatan</label>
				<div class="col-lg-4">
<?php
	$opt_jabatan = array_merge(array('' => ' -- '), $this->admin_model->get_system_config('jabatan'));
	echo form_dropdown('', $opt_jabatan, '', (' id="surat_to_jabatan" class="form-control" data-input-title="Nama Jabatan" '));
?>					
				</div>
			</div>
			<div class="form-group">
				<label for="instansi" class="col-lg-1 control-label">Nama</label>
				<div class="col-lg-4">
					<input type="text" maxlength="255" id="surat_to_nama" value="" class="form-control" placeholder="Nama" data-input-title="nama">
				</div>
				<label for="instansi" class="col-lg-2 control-label">Direktorat</label>
				<div class="col-lg-4">
					<input type="text" maxlength="255" id="surat_to_dir" value="" class="form-control" placeholder="Direktorat" data-input-title="direktorat">
				</div>
			</div>
			<div class="form-group">
				<input type="hidden" id="surat_to_unit_id" value="">
				<input type="hidden" id="surat_to_nip" value="">
				<input type="hidden" id="surat_to_pangkat" value="">	
				<div class="col-sm-12"><button type="button" id="add_tujuan" class="btn btn-info pull-right">Apply</button></div>
			</div>
			<div class="form-group">
				<textarea id="to_user_data" name="to_user_data" class="form-control required" data-input-title="unit tujuan" style="display: none;"><?php echo ($mode == 'add') ? set_value('to_user_data') : $data->to_user_data; ?></textarea>
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
						<button type="button" id="btnBack" class="btn btn-primary" name="btnBack" onclick="location.assign('<?php echo site_url('global/admin/tujuan_surat'); ?>')" ><i class="fa fa-chevron-left"></i> Back</button>
			
<?php 
	if($mode != 'add') {
?>
						<button type="submit" id="btnSave" class="btn btn-primary" name="btnSave" ><i class="fa fa-check"></i> Update</button>
						<button type="button" id="btnAdd" class="btn btn-primary" name="btnAdd" onclick="location.assign('<?php echo site_url('global/admin/tujuan_surat_detail'); ?>')" ><i class="fa fa-plus"></i> Add New</button>
<?php 
	} else {
?>
						<button type="submit" id="btnSave" class="btn btn-primary" name="btnSave" ><i class="fa fa-check"></i> Save</button>
<?php 
	}
?>
					</div>
				</div>
				<!-- 
				<div class="col-md-6">
					<div class="btn-group pull-right">
<?php 
	// if($mode != 'add') {
		// echo '<button type="button" id="btnDelete" class="btn btn-danger" name="btnDelete" ><i class="fa fa-trash-o"></i> Delete</button>';
	// }
?>								
					</div>
				</div> 
				-->
				<div class="clearfix"></div>
			</div>
		
		</div><!-- /.box-body -->
	</div><!-- /.box -->

<?php 
	echo form_close();
?>

</section><!-- /.content -->