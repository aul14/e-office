<style>
	.btn-hapus {
		display: none;
	}

	.priv_attach:HOVER > .btn-hapus {
		display: inline-block;
	}
</style>
<?php
	$distribusi = json_decode($disposisi->distribusi);
	$list = $this->disposisi_model->get_subordinates(get_user_data('unit_id'), 1);
	
	$opt_subordinates = array();
	foreach ($list as $row) {
		$opt_subordinates[$row['user_id']] = $row['sub_name'];
	}

	$i 	 = $line;
	$row = $distribusi->{$distribusi_id};
?>					
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
<?php
	if($row->status == 0) {
?>
					<form id="dist_att_<?php echo $i; ?>" action="<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/" method="post" enctype="multipart/form-data" style="float: right;">
						<input type="hidden" name="action" value="surat.disposisi_model.upload_private_attachment"/>
						<input type="hidden" name="ref_id" value="<?php echo $disposisi->disposisi_id; ?>"/>
						<input type="hidden" name="key" value="<?php echo $distribusi_id; ?>"/>
						<div class="btn btn-xs btn-default btn-file">
							<i class="fa fa-plus"></i>
							<input type="file" name="distribusi_<?php echo $distribusi_id; ?>_attachment_file" onchange="$('#dist_att_<?php echo $i; ?>').submit()">
						</div>
					</form>
					<script type="text/javascript">
						var d_<?php echo $i; ?> = <?php echo count($row->attachment); ?>;
					
						$('#dist_att_<?php echo $i; ?>').ajaxForm({
							beforeSend: function(xhr) {
								d_<?php echo $i; ?>++;
								var bar = '<div id="distribusi_<?php echo $distribusi_id; ?>_attachment_' + d_<?php echo $i; ?> + '" class="priv_attach"><div class="progress progress-xxs">' +
											'	<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' +
											'	</div>' +
											' </div></div>';
								$('#distribusi_<?php echo $i; ?>_attachment_list').append(bar);
							},
							uploadProgress: function(event, position, total, percentComplete) {
								var percentVal = percentComplete + '%';
								$('#distribusi_<?php echo $distribusi_id; ?>_attachment_' + d_<?php echo $i; ?> + ' .progress-bar ').css({'width': percentVal});
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
								}else {
									var newFile = '<a class="btn btn-xs btn-default" href="' + xhr.responseJSON.file + '" target="_blank" title="' + '"><i class="fa fa-file-text-o"></i></a>' +
													'<label id="distribusi_<?php echo $i; ?>_flabel_' + d_<?php echo $i; ?> + '"> ' + xhr.responseJSON.file_name + '</label>' +
													'<input type="hidden" name="distribusi_attachment_<?php echo $i; ?>[' + d_<?php echo $i; ?> + '][title]" value="distribusi_attachment_<?php echo $i; ?>_' + d_<?php echo $i; ?> + '"/>' +
													'<input type="hidden" name="distribusi_attachment_<?php echo $i; ?>[' + d_<?php echo $i; ?> + '][file_name]" value="' + xhr.responseJSON.file_name + '"/>' +
													'<input type="hidden" name="distribusi_attachment_<?php echo $i; ?>[' + d_<?php echo $i; ?> + '][file]" value="' + xhr.responseJSON.file + '"/>' +
													'<button type="button" class="btn-hapus btn btn-xs btn-danger" title="Hapus lampiran.." onclick="removePersonalAttachment(\'distribusi_<?php echo $distribusi_id; ?>_attachment_' + d_<?php echo $i; ?> + '\', \'<?php echo $distribusi_id; ?>\', \'' + xhr.responseJSON.file + '\');"><i class="fa fa-minus"></i></button>';
									$('#distribusi_<?php echo $distribusi_id; ?>_attachment_' + d_<?php echo $i; ?>).html(newFile);
									pa[<?php echo $i; ?> - 1]++;
								}
							}
						});

					</script>
<?php
	}
?>
				</label>
				<div id="distribusi_<?php echo $i; ?>_attachment_list" class="col-md-8"></div>
<?php
	$dist_attach[] = count($row->attachment);
?>								
			</div>
		</div>
		<div class="col-md-7">
<?php 
	$this->load->view('diskusi', array('id' => get_user_id(), 'function_handle' => 'surat.disposisi_model.set_diskusi', 'script_handle' => $i, 'ref_id' => $disposisi->disposisi_id, 'diskusi' => $row->diskusi, 'active' => ($row->status == 0)));
?>
		</div>
		<div class="clearfix"></div>
	</div>