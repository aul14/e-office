<script type="text/javascript">
	$(document).ready(function() {


	});
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Disposisi <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li><a href="#"> Disposisi</a></li>
		<li class="active"> <?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php
	$hidden = array('action' 			 => 'surat.disposisi_model.insert_disposisi',
 					'ref_type' 			 => $type,
					'ref_id' 			 => $ref_id,
					'surat_from_unit_id' => get_user_data('unit_id'),
					'surat_from_unit' 	 => get_user_data('unit_name'),
					'surat_from_kode' 	 => get_user_data('unit_code'),
					'surat_from_jabatan' => get_user_data('jabatan'),
					'surat_from_nama' 	 => get_user_data('user_name'),
					'function_ref_id' 	 => $function_ref_id
	);

	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"', $hidden);
 	$this->load->view('disposisi_ref_' . $type);
?>
	<!-- Default box -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Instruksi</h3>
			<div class="box-tools pull-right">
				<!--button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button-->
				<button type="button" class="btn btn-box-tool" title="Tambah Instruksi.." onclick="addInstruksi();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="instruksi_list" class="box-body">
<?php 
	if($parent_id == NULL) {

		$distribusi = array();
		if($ref->distribusi != '') {
			$distribusi = json_decode($ref->distribusi, TRUE);
		}
		if(!isset($distribusi['direksi'])) {
			$distribusi['direksi'] = array();
		}
		if(!isset($distribusi['non_direksi'])) {
			$distribusi['non_direksi'] = array();
		}
		
		$distribusi = array_merge($distribusi['direksi'], $distribusi['non_direksi']);
		if(($key = array_search(get_user_data('unit_id'), $distribusi)) !== false) {
			unset($distribusi[$key]);
		}
		
		$list = $this->admin_model->get_subordinates(get_user_data('unit_id'), 1);
		$opt_subordinates = array();
		foreach ($list as $row) {
			$opt_subordinates[$row['organization_structure_id']] = $row['unit_tree'];
		}
		
		$i = 1;
		foreach ($distribusi as $row) {
?>
			<!-- Default box -->
			<div id="instruksi_<?php echo $i; ?>" class="box box-primary">
				
				<div class="box-body">
		
					<div class="col-md-5">
						<div class="form-group">
							<textarea id="instruksi_<?php echo $i; ?>_text" name="instruksi[<?php echo $i; ?>][instruksi]" class="form-control" rows="5" placeholder="Instruksi" ></textarea>
						</div>
					</div>
					
					<div class="col-md-7">
						<div class="form-group">
							<label for="instruksi_to_<?php echo $i; ?>" class="col-md-4 control-label">Instruksi Kepada</label>
							<div class="col-md-8">
<?php 
			echo form_multiselect(('instruksi[' . $i . '][instruksi_to]'), $opt_subordinates, array($row), (' id="instruksi_to_' . $i . '" class="form-control select2" '));
?>
							</div>
						</div>
						<div class="form-group">
							<label for="target_selesai" class="col-md-4 control-label">Target Penyelesaian</label>
							<div class="col-md-8">
								<div class="input-group">
									<input type="text" id="instruksi_<?php echo $i; ?>_target" name="instruksi[<?php echo $i; ?>][target]" class="form-control datetimepicker required" data-input-title="Target Penyelesaian" value="<?php echo date('d-m-Y H:i:s'); ?>">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-10">
								<div class="form-group">
									<label class="col-md-4 control-label"><input type="radio" id="sifat_<?php echo $i; ?>_2" name="instruksi[<?php echo $i; ?>][sifat]" value="2"> Sangat Segera</label>
									<label class="col-md-4 control-label"><input type="radio" id="sifat_<?php echo $i; ?>_1" name="instruksi[<?php echo $i; ?>][sifat]" value="1" checked="checked"> Segera</label>
									<label class="col-md-4 control-label"><input type="radio" id="sifat_<?php echo $i; ?>_0" name="instruksi[<?php echo $i; ?>][sifat]" value="0"> Biasa</label>
								</div>
							</div>
							<div class="col-md-2">
								<button type="button" class="btn btn-danger pull-right" onclick="removeInstruksi(<?php echo $i; ?>)" title="Hapus Instruksi..."><i class="fa fa-minus"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
<?php
			$i++;
		}

	} else {
?>	
		
<?php
	}
?>		

		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<button class="btn btn-app">
				<i class="fa fa-save"></i> Save
			</button>
		</div>
		<div class="overlay hide">
			<i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
	
<?php 
	echo form_close();
?>
</section><!-- /.content -->

<script type="text/javascript">
<!--
	$(document).ready(function() {
		
		$('.datetimepicker').datetimepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			minDate : 0
		});
		
		$('.select2').select2();

		$('#instruksi').focus();
	});
	
	var i = <?php echo $i; ?>;
	
	function addInstruksi() {

		row = '<div id="attachment_' + i + '" class="form-group">' +
			'	<div class="col-md-8">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + i + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + i + '][title]" class="form-control" placeholder="Judul File ...">' +
			'		</div>' +
			'	</div>' +
			'	<div class="col-md-4">' +
			'		<div class="form-group">' +
			'			<div class="btn btn-default btn-file">' +
			'				<i class="fa fa-paperclip"></i> ' +
			'				<input type="file" name="attachment_file_' + i + '" onchange="$(\'#flabel_' + i + '\').html($(this).val())">' +
			'			</div>' +
			'			<label id="flabel_' + i + '"></label>' +
			'		</div>' +
			'	</div>' +
			'</div>';
		$('#instruksi_list').append(row);
		
		i++;
	}

	function removeInstruksi(rid) {
		$('#instruksi_' + rid).remove();
	}

-->
</script>