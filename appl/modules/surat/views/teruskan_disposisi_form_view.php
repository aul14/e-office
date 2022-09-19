<style>
	.btn-hapus {
		display: none;
	}

	.priv_attach:HOVER > .btn-hapus {
		display: inline-block;
	}
</style>
<script type="text/javascript" src="<?php echo assets_url(); ?>/plugins/jQuery/jquery.form.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

	});

</script>

<!-- Content Header (Page header) 
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
						<label class="col-md-4 control-label"><input type="radio" id="sifat_0" name="sifat" value="0" <?php echo ($disposisi->sifat == 0) ? 'checked="checked"' : ''; ?> disabled="disabled"> Biasa</label>
						<label class="col-md-4 control-label"><input type="radio" id="sifat_1" name="sifat" value="1" <?php echo ($disposisi->sifat == 1) ? 'checked="checked"' : ''; ?> disabled="disabled"> Segera</label>
						<label class="col-md-4 control-label"><input type="radio" id="sifat_2" name="sifat" value="2" <?php echo ($disposisi->sifat == 2) ? 'checked="checked"' : ''; ?> disabled="disabled"> Sangat Segera</label>
					</div>
					<div class="form-group">
						<label for="tgl_disposisi" class="col-md-4 control-label">Tanggal Disposisi</label>
						<div class="col-md-8" style="margin-bottom: 15px;">
							<div class="input-group">
								<input type="text" id="tgl_disposisi" name="disposisi_tgl" class="form-control datepicker required" data-input-title="Tanggal Disposisi" value="<?php echo db_to_human($disposisi->disposisi_tgl); ?>" readonly="readonly">
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
								<input type="text" id="instruksi_target" name="target_selesai" class="form-control datetimepicker required" data-input-title="Target Penyelesaian" value="<?php echo $target_date . ' ' . $target_time; ?>" readonly="readonly">
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
			<div class="box box-primary">
				<div class="box-header with-border">
					<span class="h3 box-title">Lampiran Disposisi </span>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="attachment_list" class="box-body" style="min-height: 83px;">
<?php 
	$last_seq = 0;
	foreach ($attachment as $row) {
?>
			<div id="attachment_<?php echo $row->sort; ?>" class="col-md-12">
				<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> <label> <?php echo $row->title; ?> </label>
			</div>
<?php 
		$last_seq = $row->sort;
	}
?>
				</div>				
			</div>
		</div>
	</div>

<?php
	$from_data = json_decode($disposisi->from_data);
	$distribusi = json_decode($disposisi->distribusi);
	$dist_attach =  array();
	$allowed_user = array();
	foreach($distribusi as $key => $row) {
		$allowed_user[] = $row->user_id;
	}

	if(has_permission(7) || in_array(get_user_id(), $allowed_user)) {
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Disposisi Dari</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div id="parent_list" class="box-body">
			<div class="box-body">
				<div class="col-md-6" style="margin-bottom: 15px;">	
					<div class="form-group">
						<label for="disposisi_nama" class="col-md-3 control-label">Nama</label>
						<div class="col-md-8">
							<input type="text" class="form-control" value="<?php echo (isset($from_data->nama)) ? $from_data->nama : ''; ?>" disabled="disabled">
						</div>
					</div>
				</div>
				<div class="col-md-6" style="margin-bottom: 15px;">	
					<div class="form-group">
						<label for="disposisi_nip" class="col-md-3 control-label">NIP</label>
						<div class="col-md-8">
							<input type="text" class="form-control" value="<?php echo (isset($from_data->nip)) ? $from_data->nip : ''; ?>" disabled="disabled">
						</div>
					</div>
				</div>
				<div class="col-md-6">	
					<div class="form-group">
						<label for="disposisi_jabatan" class="col-md-3 control-label">Jabatan</label>
						<div class="col-md-8">
							<input type="text" class="form-control" value="<?php echo (isset($from_data->jabatan)) ? $from_data->jabatan : ''; ?>" disabled="disabled">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="disposisi_unit" class="col-md-3 control-label">Unit</label>
						<div class="col-md-8">
							<input type="text" class="form-control" value="<?php echo (isset($from_data->unit)) ? $from_data->unit : ''; ?>" disabled="disabled">
						</div>
					</div>
				</div>		
			</div>
		</div>
	</div>
<?php	
	}

	if((get_user_id() == $disposisi->from_user_id) || (in_array(get_user_id(), $allowed_user))) {
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Instruksi</h3>
			<div class="box-tools pull-right">
<?php
		if(get_user_id() == $disposisi->from_user_id) {
?>
				<button type="button" class="btn btn-box-tool" title="Tambah Instruksi.." onclick="addInstruksi();"><i class="fa fa-plus"></i></button>
<?php
		}
?>
				<!-- button class="btn btn-box-tool" data-toggle="tooltip" title="Detail Disposisi" onclick="$('#detailModal').modal('show');"><i class="fa fa-sitemap"></i></button -->
			</div>
		</div>
		
		<div id="instruksi_list" class="box-body">
<?php 
		
		if (get_user_data('unit_id') == 2 || get_user_data('unit_id') == 10) {
			$list = $this->disposisi_model->get_subordinates(get_user_data('unit_id'), 1, 3);
		}else {
			$list = $this->disposisi_model->get_subordinates(get_user_data('unit_id'), 1);
		}	
	
		$opt_subordinates = array();
		foreach ($list as $row) {
			$opt_subordinates[$row['user_id']] = $row['sub_name'];
		}
		
		if(get_user_id() == $disposisi->from_user_id) {			
			$i = 1;
			foreach($distribusi as $key => $row) {				
?>
			<!-- Default box -->
			<div id="instruksi_<?php echo $i; ?>" class="box box-primary">
				
				<div class="box-body" style="<?php echo ($row->status == 99) ? 'background-color: #f56954;' : ''; ?>">
		
					<div class="col-md-5">
						<div class="form-group">
							<input id="instruksi_<?php echo $i; ?>_text" name="distribusi[instruksi][<?php echo $i; ?>][name]" class="form-control" value="<?php echo $row->name; ?>" disabled="disabled" />
						</div>
						<div class="form-group">
							<input id="instruksi_<?php echo $i; ?>_jabatan" name="distribusi[instruksi][<?php echo $i; ?>][jabatan]" class="form-control" value="<?php echo $row->jabatan . ' ' . $row->unit_name; ?>" disabled="disabled" />
						</div>
						<div class="form-group">
							<textarea id="instruksi_<?php echo $i; ?>_text" name="distribusi[instruksi][<?php echo $i; ?>][note]" class="form-control" rows="6" placeholder="Instruksi" disabled="disabled"><?php echo $row->instruksi; ?></textarea>
						</div>
						<div class="form-group">
							<label for="instruksi_to_<?php echo $i; ?>" class="col-md-4 control-label" style="padding-top: 0;">
								Lampiran
<?php
				if($row->status == 0) {
?>
								<form id="dist_att_<?php echo $i; ?>" action="<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/" method="post" enctype="multipart/form-data" style="float: right;">
									<input type="hidden" name="action" value="surat.disposisi_model.upload_private_attachment"/>
									<input type="hidden" name="ref_id" value="<?php echo $disposisi->disposisi_id; ?>"/>
									<input type="hidden" name="key" value="<?php echo $key; ?>"/>
									<div class="btn btn-xs btn-default btn-file">
										<i class="fa fa-plus"></i>
										<input type="file" name="distribusi_<?php echo $key; ?>_attachment_file" onchange="$('#dist_att_<?php echo $i; ?>').submit()">
									</div>
								</form>
								<script type="text/javascript">
									var d_<?php echo $i; ?> = <?php echo count($row->attachment); ?>;
								
									$('#dist_att_<?php echo $i; ?>').ajaxForm({
										beforeSend: function(xhr) {
											d_<?php echo $i; ?>++;
											var bar = '<div id="distribusi_<?php echo $i; ?>_attachment_' + d_<?php echo $i; ?> + '"><div class="progress progress-xxs">' +
														'	<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' +
														'	</div>' +
														' </div></div>';
											$('#distribusi_<?php echo $i; ?>_attachment_list').append(bar);
										},
										uploadProgress: function(event, position, total, percentComplete) {
											var percentVal = percentComplete + '%';
											$('#distribusi_<?php echo $i; ?>_attachment_' + d_<?php echo $i; ?> + ' .progress-bar ').css({'width': percentVal});
											console.log('up > ' + percentVal);
										},
										success: function(xhr) {
											
										},
										error: function(xhr) {
											
										},
										complete: function(xhr) {
											if(xhr.responseJSON.error != 0) {
												bootbox.alert('upload failed : ' + xhr.responseJSON.message);
												$('#distribusi_<?php echo $i; ?>_attachment_' + d_<?php echo $i; ?>).remove();
											} else {
												var newFile = '<a class="btn btn-xs btn-default" href="' + xhr.responseJSON.file + '" target="_blank" title="' + '"><i class="fa fa-file-text-o"></i> </a>' +
																'<label id="distribusi_<?php echo $i; ?>_flabel_' + d_<?php echo $i; ?> + '"> ' + xhr.responseJSON.file_name + '</label>' +
																'<input type="hidden" name="distribusi_attachment_<?php echo $i; ?>[' + d_<?php echo $i; ?> + '][title]" value="distribusi_attachment_<?php echo $i; ?>_' + d_<?php echo $i; ?> + '"/>' +
																'<input type="hidden" name="distribusi_attachment_<?php echo $i; ?>[' + d_<?php echo $i; ?> + '][file_name]" value="' + xhr.responseJSON.file_name + '"/>' +
																'<input type="hidden" name="distribusi_attachment_<?php echo $i; ?>[' + d_<?php echo $i; ?> + '][file]" value="' + xhr.responseJSON.file + '"/>' +
																'<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment(\'distribusi_<?php echo $key; ?>_attachment_' + d_<?php echo $i; ?> + '\', \'<?php echo $key; ?>\', \'' + xhr.responseJSON.file + '\');"><i class="fa fa-minus"></i></button>';
												$('#distribusi_<?php echo $i; ?>_attachment_' + d_<?php echo $i; ?>).html(newFile);
												pa[<?php echo $i; ?> - 1]++;
											}
										}
									});

									function setSelesai_<?php echo $i; ?>() {
										bootbox.confirm('Set Status selesai disposisi ke <?php echo $row->jabatan . ' ' . $row->unit_name; ?>, <br> <?php echo $row->name; ?> ?', function(result){
											if(result) {
												$.ajax({
													type: "POST",
													url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
													data: {action: 'surat.disposisi_model.set_selesai', 
															ref_id: '<?php echo $disposisi->disposisi_id; ?>', 
															distribusi_id: '<?php echo $key; ?>', 
															row_id: '<?php echo $i; ?>',
															keterangan: $('#keterangan_<?php echo $i; ?>').val()
															},
													success: function(data){
														if(typeof(data.error) != 'undefined') {
															if(data.error == 1){
																bootbox.alert(data.message);
															}else {
																displaySelesai('<?php echo $i; ?>');
																bootbox.alert(data.message);
															}
														} else {
															bootbox.alert("Data transfer error! <br>Silahkan refresh halaman lalu ulangi.");
														}
													}
												});
											} else {
												
											}
										});										
									}
									
								</script>
<?php
				}
?>
							</label>
							<div id="distribusi_<?php echo $i; ?>_attachment_list" class="col-md-8">
<?php
				foreach($row->attachment as $j => $priv_attach) {
?>				
								<div id="distribusi_<?php echo $key; ?>_attachment_<?php echo $j; ?>" class="priv_attach" >
									<a class="btn btn-xs btn-default" href="<?php echo $priv_attach->file; ?>" target="_blank" title="<?php echo $priv_attach->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
									<label id="distribusi_<?php echo $key; ?>_flabel_<?php echo $j; ?>"><?php echo $priv_attach->file_name; ?></label>
									<input type="hidden" name="distribusi_attachment_<?php echo $key; ?>[<?php echo $j; ?>][title]" value="distribusi_attachment_<?php echo $i; ?>_<?php echo $j; ?>"/>
									<input type="hidden" name="distribusi_attachment_<?php echo $key; ?>[<?php echo $j; ?>][file_name]" value="<?php echo $priv_attach->file_name; ?>"/>
									<input type="hidden" name="distribusi_attachment_<?php echo $key; ?>[<?php echo $j; ?>][file]" value="<?php echo $priv_attach->file; ?>"/>
<?php 
					if($priv_attach->owner == get_user_id() && $row->status == 0) {
?>
									<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment('distribusi_<?php echo $key; ?>_attachment_<?php echo $j; ?>', '<?php echo $key; ?>', '<?php echo $priv_attach->file; ?>');"><i class="fa fa-minus"></i></button>
<?php 
					}
?>
								</div>
<?php
				}
				
				$dist_attach[] = count($row->attachment);
?>
							</div>
<?php

?>
						</div>
					</div>
					<div class="col-md-7">
<?php 
				$this->load->view('diskusi', array('id' => $key, 'function_handle' => 'surat.disposisi_model.set_diskusi', 'script_handle' => $i, 'ref_id' => $disposisi->disposisi_id, 'diskusi' => $row->diskusi, 'active' => (($row->status == 0) && ($disposisi->status == 1))));
?>
					</div>
					<div class="form-group clearfix"></div>
					<div class="col-md-12">
						<label>Keterangan / Tindaklanjut</label>
						<div class="form-group">
							<textarea id="keterangan_<?php echo $i; ?>" name="keterangan_<?php echo $i; ?>" class="form-control" rows="3" disabled="disabled"><?php echo (isset($row->keterangan)) ? $row->keterangan : ''; ?></textarea>
						</div>
					</div>
					<div class="col-md-12">
<?php
				if($row->status == 0) {
?>
						<!-- <button class="btn btn-danger btn-block complete_state_off_<?php echo $i; ?>" onclick="setSelesai_<?php echo $i; ?>();">Selesai</button> -->
<?php
				}
?>
					</div>					
				</div>
			</div>
<?php
				$i++;
			}
		} else {
			$i = 1;
			$row = $distribusi->{get_user_id()};			
?>		
		<!-- Default box -->
			<div id="instruksi_<?php echo $i; ?>" class="box box-primary">
				<div class="box-body">
					<div class="col-md-5">
						<div class="form-group">
							<input id="instruksi_<?php echo $i; ?>_text" name="distribusi[instruksi][<?php echo $i; ?>][name]" class="form-control" value="<?php echo $row->name; ?>" disabled="disabled" />
						</div>
						<div class="form-group">
							<input id="instruksi_<?php echo $i; ?>_jabatan" name="distribusi[instruksi][<?php echo $i; ?>][jabatan]" class="form-control" value="<?php echo $row->jabatan . ' ' . $row->unit_name; ?>" disabled="disabled" />
						</div>
						<div class="form-group">
							<textarea id="instruksi_<?php echo $i; ?>_text" name="distribusi[instruksi][<?php echo $i; ?>][note]" class="form-control" rows="5" placeholder="Instruksi" disabled="disabled"><?php echo $row->instruksi; ?></textarea>
						</div>
						<div class="form-group">
							<label for="instruksi_to_<?php echo $i; ?>" class="col-md-4 control-label" style="padding-top: 0;">
								Lampiran
								<form id="dist_att_<?php echo $i; ?>" action="<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/" method="post" enctype="multipart/form-data" style="float: right;">
									<input type="hidden" name="action" value="surat.disposisi_model.upload_private_attachment"/>
									<input type="hidden" name="ref_id" value="<?php echo $disposisi->disposisi_id; ?>"/>
									<input type="hidden" name="key" value="<?php echo get_user_id(); ?>"/>
									<div class="btn btn-xs btn-default btn-file">
										<i class="fa fa-plus"></i>
										<input type="file" name="distribusi_<?php echo get_user_id(); ?>_attachment_file" onchange="$('#dist_att_<?php echo $i; ?>').submit()">
									</div>
								</form>
								<script type="text/javascript">
									var d_<?php echo $i; ?> = <?php echo count($row->attachment); ?>;
								
									$('#dist_att_<?php echo $i; ?>').ajaxForm({
										beforeSend: function(xhr) {
											d_<?php echo $i; ?>++;
											var bar = '<div id="distribusi_<?php echo $i; ?>_attachment_' + d_<?php echo $i; ?> + '"><div class="progress progress-xxs">' +
														'	<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' +
														'	</div>' +
														' </div></div>';
											$('#distribusi_<?php echo $i; ?>_attachment_list').append(bar);
										},
										uploadProgress: function(event, position, total, percentComplete) {
											var percentVal = percentComplete + '%';
											$('#distribusi_<?php echo $i; ?>_attachment_' + d_<?php echo $i; ?> + ' .progress-bar ').css({'width': percentVal});
											console.log('up > ' + percentVal);
										},
										success: function(xhr) {
											
										},
										error: function(xhr) {
											
										},
										complete: function(xhr) {
											if(xhr.responseJSON.error != 0) {
												bootbox.alert('upload failed : ' + xhr.responseJSON.message);
												$('#distribusi_<?php echo get_user_id(); ?>_attachment_' + d_<?php echo $i; ?>).remove();
											} else {
												var newFile = '<a class="btn btn-xs btn-default" href="' + xhr.responseJSON.file + '" target="_blank" title="' + '"><i class="fa fa-file-text-o"></i> </a>' +
																'<label id="distribusi_<?php echo get_user_id(); ?>_flabel_' + d_<?php echo $i; ?> + '"> ' + xhr.responseJSON.file_name + '</label>' +
																'<input type="hidden" name="distribusi_attachment_<?php echo get_user_id(); ?>[' + d_<?php echo $i; ?> + '][title]" value="distribusi_attachment_<?php echo $i; ?>_' + d_<?php echo $i; ?> + '"/>' +
																'<input type="hidden" name="distribusi_attachment_<?php echo get_user_id(); ?>[' + d_<?php echo $i; ?> + '][file_name]" value="' + xhr.responseJSON.file_name + '"/>' +
																'<input type="hidden" name="distribusi_attachment_<?php echo get_user_id(); ?>[' + d_<?php echo $i; ?> + '][file]" value="' + xhr.responseJSON.file + '"/>' +
																'<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment(\'distribusi_<?php echo get_user_id(); ?>_attachment_' + d_<?php echo $i; ?> + '\', \'<?php echo get_user_id(); ?>\', \'' + xhr.responseJSON.file + '\');"><i class="fa fa-minus"></i></button>';
												$('#distribusi_<?php echo get_user_id(); ?>_attachment_' + d_<?php echo $i; ?>).html(newFile);
												pa[<?php echo $i; ?> - 1]++;
											}
										}
									});
								</script>
							</label>
							<div id="distribusi_<?php echo $i; ?>_attachment_list" class="col-md-8">
<?php
			foreach($row->attachment as $j => $priv_attach) {
?>
								<div id="distribusi_<?php echo get_user_id(); ?>_attachment_<?php echo $j; ?>" class="priv_attach" >
										<a class="btn btn-xs btn-default" href="<?php echo $priv_attach->file; ?>" target="_blank" title="<?php echo $priv_attach->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
										<label id="distribusi_<?php echo $key; ?>_flabel_<?php echo $j; ?>"><?php echo $priv_attach->file_name; ?></label>
										<input type="hidden" name="distribusi_attachment_<?php echo get_user_id(); ?>[<?php echo $j; ?>][title]" value="distribusi_attachment_<?php echo get_user_id(); ?>_<?php echo $j; ?>"/>
										<input type="hidden" name="distribusi_attachment_<?php echo get_user_id(); ?>[<?php echo $j; ?>][file_name]" value="<?php echo $priv_attach->file_name; ?>"/>
										<input type="hidden" name="distribusi_attachment_<?php echo get_user_id(); ?>[<?php echo $j; ?>][file]" value="<?php echo $priv_attach->file; ?>"/>
<?php 
				if($priv_attach->owner == get_user_id()) {
?>
										<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment('distribusi_<?php echo get_user_id(); ?>_attachment_<?php echo $j; ?>', '<?php echo get_user_id(); ?>', '<?php echo $priv_attach->file; ?>');"><i class="fa fa-minus"></i></button>
<?php 
				}
?>
								</div>
<?php
			}
			$dist_attach[] = count($row->attachment);
?>								
							</div>
						</div>
					</div>
					
					<div class="col-md-7">
<?php 
			$this->load->view('diskusi', array('id' => $key, 'function_handle' => 'surat.disposisi_model.set_diskusi', 'ref_id' => $disposisi->disposisi_id, 'diskusi' => $row->diskusi));
?>
					</div>
					<div class="clearfix"></div>
<?php
			$list = $this->disposisi_model->get_child_disposisi($disposisi->disposisi_id, get_user_id());
			if($list->num_rows() == 0) {
?>
					<div class="box box-primary">
						<div class="box-body">
							<div class="col-md-6">

							</div>
							<div class="col-md-6">
								<button type="button" class="btn btn-xs btn-primary pull-right" onclick="location.assign('<?php echo site_url('surat/disposisi/create_from/' . $disposisi->ref_type . '/' . $disposisi->ref_id . '/' . $disposisi->disposisi_id); ?>');">
									Teruskan Disposisi <i class="fa fa-sitemap"></i>
								</button>
							</div>
						</div>
					</div>
<?php
			}
?>
				</div>
			</div>
<?php
			if($list->num_rows() > 0) {
?>
			<div>
				
			</div>
<?php
			}
		}
?>

		</div><!-- /.box-body -->
	</div><!-- /.box -->
<?php 
	}

?>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-5">
<?php
	if($disposisi->status == 0) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/disposisi/sheet/' . $disposisi->disposisi_id); ?>');">
						<i class="fa fa-edit"></i> Ubah
					</button>
<?php
	}
?>
				</div>
				<div class="col-xs-2" style="text-align: center;">
				
				</div>
				<div class="col-xs-5">
<?php 
	if($disposisi->status <= 1 && get_user_id() == $disposisi->from_user_id) {
		if($process->button_process != '-') {
?>
					<button id="btn-process" type="button" class="btn btn-app pull-right bg-green " onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
		}
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

?>
	<!-- Modal -->
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Detail Instruksi</h4>
				</div>
				<div class="modal-body">

				</div>
			</div>
		</div>
	</div>

</section><!-- /.content -->

<script src="<?php echo assets_url(); ?>/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	var dist = new Array('<?php echo implode("','", $allowed_user)?>');
	var i = <?php echo $i; ?>;
	var pa = new Array( <?php echo implode(',', $dist_attach)?> );
<?php 
// 	$process_param = array('ref_type' => 'disposisi',
// 					'ref_id[disposisi_id]' => $disposisi->disposisi_id,
// 					'function_ref_id' => $function_ref_id
// 	);

// 	echo form_open('global/admin/back_process', ' id="form-back-process" ', $process_param) . form_close();

// 	echo form_open('global/admin/next_process', ' id="form-next-process" ', $process_param) . form_close();

	if($disposisi->status != 99) {
?>		
	function printDisposisi() {
<?php 
		if($disposisi->status == 2 && (in_array(get_user_id(), $allowed_user))) {
// 			if($disposisi->response_text) {
?>
		if(responseSaved) {
			window.open('<?php echo site_url('surat/disposisi/cetak_response/' . $disposisi->disposisi_id); ?>');
<?php 
// 			} else {
?>
		} else {
			bootbox.alert('Respon Disposisi belum disimpan.');
		}
<?php 
// 			}
		} else {
?>
		window.open('<?php echo site_url('surat/disposisi/cetak_disposisi/' . $disposisi->disposisi_id); ?>');
<?php 
		}
?>
	}
	
	function returnData() {
		bootbox.prompt({
			title: 'Kembalikan berkas.', 
			inputType: 'textarea',
			callback: function(result){
				if(result){
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {action: 'surat.disposisi_model.return_data', 
								ref_id: '<?php echo $disposisi->disposisi_id; ?>', 
								note: result, 
								last_flow: <?php echo $last_flow; ?>,
								function_ref_id: <?php echo $function_ref_id; ?>,
								flow_seq: <?php echo $disposisi->status; ?>
							},
						success: function(data) {
							if(typeof(data.error) != 'undefined') {
								if(data.error == 0) {
									eval(data.execute);
								} else {
									bootbox.alert(data.message);
								}
							} else {
								bootbox.alert("Data transfer error! <br>Silahkan refresh halaman lalu ulangi.");
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
							if(data.error == 0) {
								eval(data.execute);
							} else {
								bootbox.alert(data.message);
							}
						} else {
							bootbox.alert("Data transfer error! <br>Silahkan refresh halaman lalu ulangi.");
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
	
	function addInstruksi() {
		
		row = '<div id="instruksi_' + i + '" class="box box-primary">' +
				'<form action="<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>" method="post" enctype="multipart/form-data" id="form_disposisi_' + i + '" class="form-horizontal" >' +	
				'	<input type="hidden" name="action" value="surat.disposisi_model.konfirm_partial_instruksi"/>' +
				'	<input type="hidden" name="ref_id" value="<?php echo $disposisi->disposisi_id; ?>"/>' +
				'	<input type="hidden" name="row" value="' + i + '"/>' +
					
				'	<div class="box-body">' +
				'		<div class="col-md-5">' +
				'			<div class="form-group">' +
				'				<textarea id="instruksi_' + i + '_text" name="note" class="form-control" rows="5" placeholder="Instruksi" ></textarea>' +
				'			</div>' +
				'		</div>' +
						
				'		<div class="col-md-7">' +
				'			<div class="form-group">' +
				'				<label for="instruksi_to_' + i + '" class="col-md-4 control-label">Instruksi Kepada</label>' +
				'				<div class="col-md-8">' +
				'				<?php echo str_replace("\n", "", form_dropdown(('to'), $opt_subordinates, '', (' id="instruksi_to_\' + i + \'" class="form-control select2" '))); ?>' +
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
				'				<div class="col-md-6"></div>' +
				'				<div class="col-md-6">' +
				'					<button type="button" class="btn btn-danger pull-right" onclick="removeInstruksi(' + i + ')" title="Hapus Instruksi..."><i class="fa fa-minus"></i></button>' +
				'					<button type="button" class="btn btn-success pull-right" style="margin-right: 20px;" onclick="konfirmInstruksi(' + i + ')" title="Konfirmasi Instruksi..."><i class="fa fa-check"></i></button>' +
				'				</div>' +
				'			</div>' +
				'		</div>' +
				'	</div>' +
				'</form>' +
				'</div>';
				
		$('#instruksi_list').prepend(row);
		pa.push(1);
	//	alert(pa.join('\n'));
		i++;
	}
	
	function removeInstruksi(rid) {
		$('#instruksi_' + rid).remove();
	}

	function konfirmInstruksi(rid) {

		var err = '';
		if($('#instruksi_' + rid + '_note').val() == '') {
			err += 'Instruksi belum di isi.<br>';
		}
		if(dist.includes($('#instruksi_to_' + rid).val())) {
			err += 'Instruksi ke ' + $('#instruksi_to_' + rid + ' option:selected').text() + ' sudah dipilih.<br>';
		}
		if(err == '') {
			bootbox.confirm('Konfirm Instruksi ', function(result){
				if(result) {
					
					$('#form_disposisi_' + rid).ajaxForm({
						complete: function(xhr) {
							if(xhr.responseJSON.error != 0) {
								bootbox.alert('Konfirm failed : ' + xhr.responseJSON.message);
							} else {
								$.ajax({
									type: "POST",
									url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('surat/disposisi/instruksi_part/' . $disposisi->disposisi_id)); ?>/" + $('#instruksi_to_' + rid).val() + '/' + rid,
									success: function(res){
										$('#instruksi_' + rid).html(res);
									}
								});
							}
						}
					});
					$('#form_disposisi_' + rid).submit();
				}
			});
		} else {
			bootbox.alert(err);
		}
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
	
<?php 
	} else {
?>

	function printDisposisi() {
		window.open('<?php echo site_url('surat/disposisi/cetak_disposisi/' . $disposisi->disposisi_id); ?>');
	}

<?php 
	}
?>

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

<?php 
	
?>
	
	function displaySelesai(id) {
		$('.complete_state_off_' + id).hide();
		$('#distribusi_' + id + '_attachment_list .priv_attach .btn-hapus').remove();
		$('#instruksi_' + id + ' .box-body').css('background-color', '#f56954');
	}

</script>
		