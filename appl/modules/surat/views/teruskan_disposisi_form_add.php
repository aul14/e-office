<style>
	.btn-hapus {
		display: none;
	}

	.priv_attach:HOVER > .btn-hapus {
		display: inline-block;
	}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		/* === */

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
 	$this->load->view('disposisi_ref_' . $ref->jenis_agenda . '_parent');
 
	$hidden = array('action' 			 => 'surat.disposisi_model.insert_disposisi',
					'parent_id' 		 => $parent->disposisi_id,
 					'ref_type' 			 => $type,
					'ref_id' 			 => $ref_id,
					'from_unit_id' 		 => get_user_data('unit_id'),
					'from_user_id' 		 => get_user_id(),
					'from_data[unit]' 	 => get_user_data('unit_name'),
					'from_data[kode]' 	 => get_user_data('unit_code'),
					'from_data[jabatan]' => get_user_data('jabatan'),
					'from_data[nama]' 	 => get_user_data('user_name'),
					'from_data[nip]' 	 => get_user_data('nip'),
					'from_data[email]' 	 => get_user_data('email'),
					'from_data[status]'  => 0,
					'function_ref_id' 	 => $function_ref_id
	);

	echo form_open_multipart('', ' id="form_disposisi" class="form-horizontal" onsubmit="return validateData($(this));"', $hidden);
?>

	<div class="row">
		<div class="col-md-6">
			<div id="distribusi" class="box">
				<div class="box-body" style="min-height: 125px;">
					<div class="form-group">
						<label class="col-md-4 control-label"><input type="radio" id="sifat_0" name="sifat" value="0"> Biasa</label>
						<label class="col-md-4 control-label"><input type="radio" id="sifat_1" name="sifat" value="1" checked="checked"> Segera</label>
						<label class="col-md-4 control-label"><input type="radio" id="sifat_2" name="sifat" value="2"> Sangat Segera</label>
					</div>
					<div class="form-group">
						<label for="tgl_disposisi" class="col-md-4 control-label">Tanggal Disposisi</label>
						<div class="col-md-8">
							<div class="input-group">
								<input type="text" id="tgl_disposisi" name="disposisi_tgl" class="form-control datepicker required" data-input-title="Tanggal Disposisi" value="<?php echo date('d-m-Y'); ?>">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="target_selesai" class="col-md-4 control-label">Target Penyelesaian</label>
						<div class="col-md-8">
							<div class="input-group">
								<input type="text" id="instruksi_target" name="target_selesai" class="form-control datetimepicker required" data-input-title="Target Penyelesaian" value="<?php echo date('d-m-Y H:i:s'); ?>">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<!-- Default box -->
			<div class="box">
				<div class="box-header with-border">
					<span class="h3 box-title">lampiran Disposisi </span> <span class="small">Max. 2MB (*.pdf, *.jpg, *.jpeg, *.png) </span>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="attachment_list" class="box-body">
<?php 
	$last_seq = 0;
	foreach ($parent_attachment as $row) {
?>
							<input type="hidden" name="parent_attachment[<?php echo $row->sort; ?>][file]" value="<?php echo $row->file; ?>">
							<input type="hidden" name="parent_attachment[<?php echo $row->sort; ?>][file_name]" value="<?php echo $row->file_name; ?>">
							<input type="hidden" id="parent_attachment_state_<?php echo $row->sort; ?>" name="parent_attachment[<?php echo $row->sort; ?>][state]" value="-">
							<div id="attachment_<?php echo $row->sort; ?>" class="form-group">
								<div class="col-md-8">
									<div class="input-group">
										<div class="input-group-btn">
											<button type="button" class="btn btn-danger" onclick="removeAttachment(<?php echo $row->sort; ?>)" title="Hapus file..."><i class="fa fa-minus"></i></button>
										</div>
										<input type="text" name="parent_attachment[<?php echo $row->sort; ?>][title]" class="form-control" placeholder="Judul File ..." value="<?php echo $row->title; ?>">
										<span class="input-group-addon">
											<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
										</span>
									</div>
								</div>
							</div>
<?php 
		$last_seq = $row->sort;
	}

	$parent_distribusi = json_decode($parent->distribusi);
	$parent_distribusi = $parent_distribusi->{get_user_id()};

	$i = $last_seq + 1;
	foreach($parent_distribusi->attachment as $j => $priv_attach) {
?>
							<input type="hidden" name="parent_attachment[<?php echo $i; ?>][file]" value="<?php echo $priv_attach->file; ?>">
							<input type="hidden" name="parent_attachment[<?php echo $i; ?>][file_name]" value="<?php echo $priv_attach->file_name; ?>">
							<input type="hidden" id="parent_attachment_state_<?php echo $i; ?>" name="parent_attachment[<?php echo $i; ?>][state]" value="-">
							<div id="attachment_<?php echo $i; ?>" class="form-group">
								<div class="col-md-8">
									<div class="input-group">
										<div class="input-group-btn">
											<button type="button" class="btn btn-danger" onclick="removeAttachment(<?php echo $i; ?>)" title="Hapus file..."><i class="fa fa-minus"></i></button>
										</div>
										<input type="text" name="parent_attachment[<?php echo $i; ?>][title]" class="form-control" placeholder="Judul File ..." value="<?php echo $priv_attach->file_name; ?>">
										<span class="input-group-addon">
											<a href="<?php echo $priv_attach->file; ?>" target="_blank" title="<?php echo $priv_attach->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
										</span>
									</div>
								</div>
							</div>
<?php
		$i++;
	}
?>
					<div id="attachment_0" class="form-group">
						<div class="col-md-8">
							<input type="text" name="attachment[0][title]" class="form-control file-attachment" placeholder="Judul File ..." id="title_0">
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<div class="btn btn-default btn-file">
									<i class="fa fa-paperclip"></i>
									<input type="file" name="attachment_file_0" onchange="$('#flabel_0').html($(this).val()); $('#title_0').val(getFilename($(this).val()))">
								</div>
								<label id="flabel_0"></label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Default box -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Teruskan Instruksi</h3>
			<div class="box-tools pull-right">
				<!--button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button-->
				<button type="button" class="btn btn-box-tool" title="Tambah Instruksi.." onclick="addInstruksi();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="instruksi_list" class="box-body">
			
<?php 
	if (get_user_data('unit_id') == 2 || get_user_data('unit_id') == 10) {
		$list = $this->disposisi_model->get_subordinates(get_user_data('unit_id'), 1, 3);
	}else {
		$list = $this->disposisi_model->get_subordinates(get_user_data('unit_id'), 1);
	}	
//	$list = $this->disposisi_model->get_subordinates(get_user_data('unit_id'), 1);
//	var_dump($list);
	$opt_subordinates = array();
	foreach ($list as $row) {
		$opt_subordinates[$row['user_id']] = $row['sub_name'];
	}
?>
			<!-- Default box -->
			<div id="instruksi_1" class="box box-primary">
				
				<div class="box-body">
					<div class="col-md-5">
						<div class="form-group">
							<textarea id="instruksi_1_text" name="distribusi[instruksi][1][note]" class="form-control" rows="5" placeholder="Instruksi" ></textarea>
						</div>
					</div>
					<div class="col-md-7">
						<div class="form-group">
							<label for="instruksi_to_1" class="col-md-4 control-label">Instruksi Kepada</label>
							<div class="col-md-8">
<?php 
	echo form_dropdown(('distribusi[instruksi][1][to]'), $opt_subordinates, '', (' id="instruksi_to_1" class="form-control select2" '));
?>
							</div>
						</div>
						<div class="box-body">
							<div class="form-group">
								<label for="instruksi_to_1" class="col-md-4 control-label" style="padding-top: 0;">
									Lampiran Khusus
									<button type="button" class="btn btn-xs" title="Tambah lampiran.." onclick="addPersonalAttachment(1);"><i class="fa fa-plus"></i></button>
								</label>
								<div id="distribusi_1_attachment_list" class="col-md-8">
									<div id="distribusi_1_attachment_0" class="priv_attach">
										<div class="btn btn-xs btn-default btn-file">
											<i class="fa fa-paperclip"></i>
											<input type="file" name="distribusi_1_attachment_file_0" onchange="$('#distribusi_1_flabel_0').html($(this).val())">
										</div>
										<label id="distribusi_1_flabel_0"></label>
										<input type="hidden" name="distribusi_attachment_1[0][title]" value="distribusi_attachment_1_0"/>
										<input type="hidden" name="distribusi_attachment_1[0][file_name]" value=""/>
										<input type="hidden" name="distribusi_attachment_1[0][file]" value=""/>
										<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment('distribusi_1_attachment_0');"><i class="fa fa-minus"></i></button>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-10"></div>
							<div class="col-md-2">
								<button type="button" class="btn btn-danger pull-right" onclick="removeInstruksi(1)" title="Hapus Instruksi..."><i class="fa fa-minus"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
<?php
	/* === */
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

<?php 
// 	$process_param = array('ref_type' => 'disposisi',
// 					'ref_id[disposisi_id]' => $disposisi->disposisi_id,
// 					'function_ref_id' => $function_ref_id
// 	);

// 	echo form_open('global/admin/back_process', ' id="form-back-process" ', $process_param) . form_close();

// 	echo form_open('global/admin/next_process', ' id="form-next-process" ', $process_param) . form_close();
?>

<script type="text/javascript">
<!--
	$(document).ready(function() {

		$('.datetimepicker').datetimepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			minDate : 0
		});

		$('.datepicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			// minDate : 0
		});
		
		$('.select2').select2();

		$('#instruksi').focus();
		
	});
	
	var i = 2;
	var pa = new Array();
	pa.push(1);
	
	function addInstruksi() {

		row =	'<div id="instruksi_' + i + '" class="box box-primary">' +
				
				'	<div class="box-body">' +
			
				'		<div class="col-md-5">' +
				'			<div class="form-group">' +
				'				<textarea id="instruksi_' + i + '_text" name="distribusi[instruksi][' + i + '][note]" class="form-control" rows="5" placeholder="Instruksi" ></textarea>' +
				'			</div>' +
				'		</div>' +
						
				'		<div class="col-md-7">' +
				'			<div class="form-group">' +
				'				<label for="instruksi_to_' + i + '" class="col-md-4 control-label">Instruksi Kepada</label>' +
				'				<div class="col-md-8">' +
				'				<?php echo str_replace("\n", "", form_dropdown(('distribusi[instruksi][\' + i + \'][to]'), $opt_subordinates, '', (' id="instruksi_to_\' + i + \'" class="form-control select2" '))); ?>' +
				'				</div>' +
				'			</div>' +
				'			<div class="box-body">' +
				'				<div class="form-group">' +
				'					<label for="instruksi_to_' + i + '" class="col-md-4 control-label" style="padding-top: 0;">' +
				'						Lampiran Khusus' +
				'						<button type="button" class="btn btn-xs" title="Tambah lampiran.." onclick="addPersonalAttachment(' + i + ');"><i class="fa fa-plus"></i></button>' +
				'					</label>' +
				'					<div id="distribusi_' + i + '_attachment_list" class="col-md-8">' +
				'						<div id="distribusi_' + i + '_attachment_0" class="priv_attach" >' +
				'							<div class="btn btn-xs btn-default btn-file">' +
				'								<i class="fa fa-paperclip"></i>' +
				'								<input type="file" name="distribusi_' + i + '_attachment_file_0" onchange="$(\'#distribusi_' + i + '_flabel_0\').html($(this).val())">' +
				'							</div>' +
				'							<label id="distribusi_' + i + '_flabel_0"></label>' +
				'							<input type="hidden" name="distribusi_attachment_' + i + '[0][title]" value="distribusi_attachment_' + i + '_0"/>' +
				'							<input type="hidden" name="distribusi_attachment_' + i + '[0][file_name]" value=""/>' +
				'							<input type="hidden" name="distribusi_attachment_' + i + '[0][file]" value=""/>' +
				'							<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment(\'distribusi_' + i + '_attachment_0\');"><i class="fa fa-minus"></i></button>' +
				'						</div>' +
				'					</div>' +
				'				</div>' +
				'			</div>' +
				'			<div class="row">' +
				'				<div class="col-md-10"></div>' +
				'				<div class="col-md-2">' +
				'					<button type="button" class="btn btn-danger pull-right" onclick="removeInstruksi(' + i + ')" title="Hapus Instruksi..."><i class="fa fa-minus"></i></button>' +
				'				</div>' +
				'			</div>' +
				'		</div>' +
				'	</div>' +
				'</div>';

		$('#instruksi_list').append(row);
		pa.push(1);
//		alert(pa.join('\n'));
		i++;
	}

	function removeInstruksi(rid) {
		$('#instruksi_' + rid).remove();
	}

	var attachmentRow = 0;

	function addAttachment() {
		attachmentRow++;

		row = '<div id="attachment_' + attachmentRow + '" class="form-group">' +
			'	<div class="col-md-8">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + attachmentRow + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="Judul File ..." id="title_' + attachmentRow +'">' +
			'		</div>' +
			'	</div>' +
			'	<div class="col-md-4">' +
			'		<div class="form-group">' +
			'			<div class="btn btn-default btn-file">' +
			'				<i class="fa fa-paperclip"></i> ' +
			'				<input type="file" name="attachment_file_' + attachmentRow + '" onchange="$(\'#flabel_' + attachmentRow + '\').html($(this).val()); $(\'#title_' + attachmentRow + '\').val(getFilename($(this).val()));">' +
			'			</div>' +
			'			<label id="flabel_' + attachmentRow + '"></label>' +
			'		</div>' +
			'	</div>' +
			'</div>';
		
		$('#attachment_list').append(row);
	}

	function removeAttachment(rid) {
		$('#attachment_' + rid).remove();
	}

	function getFilename(path) {
		var filename = path.split('\\').pop().split('/').pop().split('.')[0];
		return filename;
	}
	
	function addPersonalAttachment(rid) {
//		console.log(rid);
//		alert(pa[rid - 1]);
		row = '<div id="distribusi_' + rid + '_attachment_' + pa[rid - 1] + '" class="priv_attach">' +
				'	<div class="btn btn-xs btn-default btn-file">' +
				'		<i class="fa fa-paperclip"></i>' +
				'		<input type="file" name="distribusi_' + rid + '_attachment_file_' + pa[rid - 1] + '" onchange="$(\'#distribusi_' + rid + '_flabel_' + pa[rid - 1] + '\').html($(this).val())">' +
				'	</div>' +
				'	<label id="distribusi_' + rid + '_flabel_' + pa[rid - 1] + '"></label>' +
				'	<input type="hidden" name="distribusi_attachment_' + rid + '[' + pa[rid - 1] + '][title]" value="distribusi_attachment_' + rid + '_' + pa[rid - 1] + '"/>' +
				'	<input type="hidden" name="distribusi_attachment_' + rid + '[' + pa[rid - 1] + '][file_name]" value=""/>' +
				'	<input type="hidden" name="distribusi_attachment_' + rid + '[' + pa[rid - 1] + '][file]" value=""/>' +
				'	<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment(\'distribusi_' + rid + '_attachment_' + pa[rid - 1] + '\');"><i class="fa fa-minus"></i></button>' +
				'</div>';
		
		$('#distribusi_' + rid + '_attachment_list').append(row);
		pa[rid - 1]++;
	}
	
	function removePersonalAttachment(rid) {
		$('#' + rid).remove();
	}

-->
</script>