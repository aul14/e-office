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
	$hidden = array('action'	 		 => 'surat.disposisi_model.update_disposisi',
					'disposisi_id' 		 => $disposisi->disposisi_id,
					'ref_type' 			 => $disposisi->ref_type,
					'ref_id' 			 => $disposisi->ref_id,
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

	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"', $hidden);
?>

	<!-- Default box >
	<div class="box box-primary collapsed-box">
		<div class="box-header with-border pad table-responsive">
			<h3 class="box-title">Status Proses</h3>
			<table class="table text-center">
				<tr>
<?php 
	$state_flow = array(); 
	$last_flow = 0;
	foreach($flow as $row) {
		if($row->flow_seq == $disposisi->status) {
			$flow_pos = 'btn-success';
		} elseif ($row->flow_seq < $process->flow_seq) {
			$flow_pos = 'btn-danger';
		} else {
			$flow_pos = 'btn-warning disabled';
		}
		$last_flow = $row->flow_seq;
		$state_flow[$row->flow_seq] = $row->title;
?>
					<td>
						<button type="button" class="btn btn-block <?php echo $flow_pos; ?>"><?php echo $row->title; ?></button>
					</td>
<?php 
	}
	
	$state_flow[99] = 'Arsip';
?>
				</tr>
			</table>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>

		<div class="box-body" style="display: none;">
			<table class="table no-margin">
				<thead>
					<tr>
						<th width="180">Waktu</th>
						<th width="150">Status</th>
						<th>User</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>
<?php 
	foreach($flow_notes as $row) {
		list($flow_date, $flow_time) = explode(' ', $row->created_time);
		$flow_date = db_to_human($flow_date);
?>
					<tr>
						<td><?php echo $flow_date . ' ' . $flow_time; ?></td>
						<td><?php echo $state_flow[$row->flow_seq]; ?></td>
						<td><?php echo $row->user_name; ?></td>
						<td><?php echo $row->note; ?></td>
					</tr>
<?php 
	}
?>
				</tbody>
			</table>
		</div>
	</div -->
		
<?php 
 	$this->load->view('disposisi_ref_' . $ref->jenis_agenda . '_parent');
?>
	<!-- Default box -->
	<div class="row">
		<div class="col-md-6">
			<div id="distribusi" class="box">
				
				<div class="box-body" style="min-height: 125px;">
					<div class="form-group">
						<label class="col-md-4 control-label"><input type="radio" id="sifat_0" name="sifat" value="0" <?php echo ($disposisi->sifat == 0) ? 'checked="checked"' : ''; ?>> Biasa</label>
						<label class="col-md-4 control-label"><input type="radio" id="sifat_1" name="sifat" value="1" <?php echo ($disposisi->sifat == 1) ? 'checked="checked"' : ''; ?>> Segera</label>
						<label class="col-md-4 control-label"><input type="radio" id="sifat_2" name="sifat" value="2" <?php echo ($disposisi->sifat == 2) ? 'checked="checked"' : ''; ?>> Sangat Segera</label>
					</div>
					<div class="form-group">
						<label for="tgl_disposisi" class="col-md-4 control-label">Tanggal Disposisi</label>
						<div class="col-md-8">
							<div class="input-group">
								<input type="text" id="tgl_disposisi" name="disposisi_tgl" class="form-control datepicker required" data-input-title="Tanggal Disposisi" value="<?php echo db_to_human($disposisi->disposisi_tgl); ?>">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
<?php 
	list($target_date, $target_time) = explode(' ', $disposisi->target_selesai);
	$target_date = db_to_human($target_date);
?>
						<label for="target_selesai" class="col-md-4 control-label">Target Penyelesaian</label>
						<div class="col-md-8">
							<div class="input-group">
								<input type="text" id="instruksi_target" name="target_selesai" class="form-control datetimepicker required" data-input-title="Target Penyelesaian" value="<?php echo $target_date . ' ' . $target_time; ?>">
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
	foreach ($attachment as $row) {
?>
					<input type="hidden" name="attachment[<?php echo $row->sort; ?>][id]" value="<?php echo $row->file_attachment_id; ?>">
					<input type="hidden" name="attachment[<?php echo $row->sort; ?>][file]" value="<?php echo $row->file; ?>">
					<input type="hidden" id="attachment_state_<?php echo $row->sort; ?>" name="attachment[<?php echo $row->sort; ?>][state]" value="-">
					<div id="attachment_<?php echo $row->sort; ?>" class="form-group">
						<div class="col-md-8">
							<div class="input-group">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" onclick="removeAttachment(<?php echo $row->sort; ?>)" title="Hapus file..."><i class="fa fa-minus"></i></button>
								</div>
								<input type="text" name="attachment[<?php echo $row->sort; ?>][title]" class="form-control" placeholder="Judul File ..." id="title_<?php echo $row->sort; ?>" value="<?php echo $row->title; ?>">
								<span class="input-group-addon">
									<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
								</span>
							</div>
						</div>
						<div class="col-md-4" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
							<div class="form-group">
								<div class="btn btn-default btn-file">
									<i class="fa fa-paperclip"></i> 
									<input type="file" name="attachment_file_<?php echo $row->sort; ?>" onchange="$('#flabel_<?php echo $row->sort; ?>').html($(this).val()); $('#title_<?php echo $row->sort; ?>').val(getFilename($(this).val()))">
								</div>
								<label id="flabel_<?php echo $row->sort; ?>"></label>
							</div>
						</div>
					</div>
<?php 
		$last_seq = $row->sort;
	}
?>
				</div>
			</div>
		</div>
	</div>
			
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
	$distribusi  = json_decode($disposisi->distribusi);
	$dist_attach = array();
	
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
	
	$key = 0;
	foreach($distribusi as $row) {
?>
		<!-- Default box -->
		<div id="instruksi_<?php echo $key; ?>" class="box box-primary">
			<div class="box-body">
				<div class="col-md-5">
					<div class="form-group">
						<textarea id="instruksi_<?php echo $key; ?>_text" name="distribusi[instruksi][<?php echo $key; ?>][note]" class="form-control" rows="5" placeholder="Instruksi" ><?php echo $row->instruksi; ?></textarea>
					</div>
				</div>
				<div class="col-md-7">
					<div class="form-group">
						<label for="instruksi_to_<?php echo $key; ?>" class="col-md-4 control-label">Instruksi Kepada</label>
						<div class="col-md-8">
<?php 
	echo form_dropdown(('distribusi[instruksi][' . $key . '][to]'), $opt_subordinates, $row->user_id, (' id="instruksi_to_' . $key . '" class="form-control select2" '));
?>
						</div>
					</div>
					<div class="box-body">
						<div class="form-group">
							<label for="instruksi_to_<?php echo $key; ?>" class="col-md-4 control-label" style="padding-top: 0;">
								Lampiran Khusus
								<button type="button" class="btn btn-xs" title="Tambah lampiran.." onclick="addPersonalAttachment(<?php echo $key; ?>);"><i class="fa fa-plus"></i></button>
							</label>
							<div id="distribusi_<?php echo $key; ?>_attachment_list" class="col-md-8">
<?php
		foreach($row->attachment as $i => $priv_attach) {
?>								<div id="distribusi_<?php echo $key; ?>_attachment_<?php echo $i; ?>" class="priv_attach" >
									<div class="btn btn-xs btn-default btn-file">
										<i class="fa fa-paperclip"></i>
										<input type="file" name="distribusi_<?php echo $key; ?>_attachment_file_<?php echo $i; ?>" onchange="$('#distribusi_<?php echo $key; ?>_flabel_<?php echo $i; ?>').html($(this).val())">
									</div>
									<a class="btn btn-xs btn-default" href="<?php echo $priv_attach->file; ?>" target="_blank" title="<?php echo $priv_attach->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
									<label id="distribusi_<?php echo $key; ?>_flabel_<?php echo $i; ?>"><?php echo $priv_attach->file_name; ?></label>
									<input type="hidden" name="distribusi_attachment_<?php echo $key; ?>[<?php echo $i; ?>][title]" value="distribusi_attachment_<?php echo $key; ?>_<?php echo $i; ?>"/>
									<input type="hidden" name="distribusi_attachment_<?php echo $key; ?>[<?php echo $i; ?>][file_name]" value="<?php echo $priv_attach->file_name; ?>"/>
									<input type="hidden" name="distribusi_attachment_<?php echo $key; ?>[<?php echo $i; ?>][file]" value="<?php echo $priv_attach->file; ?>"/>
									<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment('distribusi_<?php echo $key; ?>_attachment_<?php echo $i; ?>');"><i class="fa fa-minus"></i></button>
								</div>
<?php
		}
		
		$dist_attach[] = count($row->attachment);
?>								
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-10"></div>
						<div class="col-md-2">
							<button type="button" class="btn btn-danger pull-right" onclick="removeInstruksi(<?php echo $key; ?>)" title="Hapus Instruksi..."><i class="fa fa-minus"></i></button>
						</div>
					</div>
				</div>
				
			</div>
		</div>
<?php
		$key++;
	}
?>		

		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-5">
					<button class="btn btn-app">
						<i class="fa fa-save"></i> Update
					</button>
				</div>
				<div class="col-xs-2" style="text-align: center;">
					<!--button type="button" class="btn btn-app" onclick="printDisposisi();">
						<i class="fa fa-print"></i> Cetak
					</button-->
				</div>
				<div class="col-xs-5">
<?php 
//		if($surat_eksternal->status < $last_flow) {
			if($process->button_process != '-') {
?>
					<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
			}
//		if($surat_eksternal->status != 0) {
			if($process->button_return != '-') {
?>
					<button type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
						<i class="fa fa-caret-square-o-left"></i> <?php echo $process->button_return; ?>
					</button>
<?php 
			}
?>
				</div>
			</div>
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

	function printDisposisi() {
		window.open('<?php echo site_url('surat/disposisi/cetak_disposisi/' . $disposisi->disposisi_id); ?>');
	}
	
	var i = <?php echo count($distribusi) + 1; ?>;
	var pa = new Array(<?php echo implode(',', $dist_attach)?>);
	
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

	var attachmentRow = <?php echo $last_seq; ?>;

	function addAttachment() {
		
		attachmentRow++;

		row = '<input type="hidden" id="attachment_state_' + attachmentRow + '" name="attachment[' + attachmentRow + '][state]" value="insert">' +
			'<div id="attachment_' + attachmentRow + '" class="form-group">' +
			'	<div class="col-md-8">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + attachmentRow + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="File ..." id="title_' + attachmentRow +'">' +
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
		$('#attachment_' + rid).addClass('hide');
		$('#attachment_state_' + rid).val('delete');
	}

	function getFilename(path) {
		var filename = path.split('\\').pop().split('/').pop().split('.')[0];
		return filename;
	}

	function addPersonalAttachment(rid) {
//		console.log(rid);
//		alert(pa[rid - 1]);
		row = '<div id="distribusi_' + rid + '_attachment_' + pa[rid - 1] + '">' +
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

	function returnData() {
		bootbox.prompt({
			title: 'Kembalikan berkas.', 
			inputType: 'textarea',
			callback: function(result){
				if(result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {action: 'surat.eksternal_model.return_data', 
								ref_id: '<?php echo $disposisi->disposisi_id; ?>', 
								note: result, 
								last_flow: <?php echo $last_flow; ?>,
								function_ref_id: <?php echo $function_ref_id; ?>,
								flow_seq: <?php echo $disposisi->status; ?>,
								function_handler: '<?php echo $process->check_field_function; ?>'
							},
						success: function(data) {
							if(typeof(data.error) != 'undefined') {
								eval(data.execute);
							} else {
								bootbox.alert("Data transfer error!");
							}
						}
					});
				}
			}
		});
	}

	function prosesData() {
		$('#box-process-btn .overlay').removeClass('hide');
<?php 
echo $process->check_field;
	$req_process = FALSE;
	if($process->check_field != '-') {
//		if(!$this->admin_model->check_field_flow($process->check_field, array('disposisi_id' => $disposisi->disposisi_id))) {
			$req_process = TRUE;
//		}
	}
	
	if($req_process) {
?>
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result){
			if(result) {
				location.assign('<?php echo site_url($process->check_field_function . '/' . $disposisi->disposisi_id); ?>');	 
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});

<?php 
	} else {
?>
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result){
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.disposisi_model.proses_data', 
							ref_id: '<?php echo $disposisi->disposisi_id; ?>', 
							note: result, 
							last_flow: <?php echo $last_flow; ?>,
							function_ref_id: <?php echo $function_ref_id; ?>,
							flow_seq: <?php echo $disposisi->status; ?>,
							function_handler: '<?php echo $process->check_field_function; ?>'
							},
					success: function(data){
						if(typeof(data.error) != 'undefined') {
							eval(data.execute);
						} else {
							bootbox.alert("Data transfer error!");
						}
						$('#box-process-btn .overlay').addClass('hide');
					}
				});
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
		
<?php 
	}
?>

	}


</script>