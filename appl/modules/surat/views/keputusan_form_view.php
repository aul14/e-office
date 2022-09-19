 <?php 

	list($agenda_date, $agenda_time) 	= explode(' ', $surat->created_time);
	list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
	$agenda_date = db_to_human($agenda_date);
	
// 	$add_id = 0;
// 	$result = $this->kontrak_model->get_addendum($surat->surat_id);
// 	if($result->num_rows() > 0) {
// 		$addendum = $result->row();
// 		$add_id = $addendum->addendum_id;
//	}
?>
<style>

</style>
<section class="content">
<?php
	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('surat_id', $surat->surat_id);
	echo form_hidden('return', 'surat/keputusan/keputusan_list'); 
	echo form_hidden('function_ref_id', $function_ref_id); 
	echo form_hidden('function_ref_name', 'Keputusan'); 
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Identitas Surat</h3>
		</div>

		<div class="box-body">
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 col-xs-12 control-label">Agenda</label>
				<div class="col-lg-4 col-sm-4 col-xs-6">
					<input type="text" id="agenda_id" class="form-control" disabled="disabled" value="<?php echo strtoupper($surat->jenis_agenda) . ' - ' . $surat->agenda_id; ?>">
				</div>
				<div class="col-lg-6 col-sm-5 col-xs-6">
					<input type="text" id="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date . ' ' . $agenda_hours; ?>" style="text-align: right;">
				</div>
			</div>			
			<div class="form-group">
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Keputusan</label>
<?php 
	$opt_jenis_Keputusan = $this->keputusan_model->get_referensi_full('jenis_keputusan');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="jenis_surat" name="jenis_surat" disabled="disabled" class="form-control" data-input-title="jenis_surat" value="<?php echo ($surat->jenis_surat != '-') ? $opt_jenis_Keputusan[$surat->jenis_surat] : '-'; ?>">
				</div>
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sumber Usulan</label>
<?php 
	$opt_kode_keputusan = $this->keputusan_model->get_referensi_full('sumber_usulan');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="sifat_surat" name="sifat_surat" disabled="disabled" class="form-control" data-input-title="sifat_surat" value="<?php echo ($surat->sifat_surat != '-') ? $opt_kode_keputusan[$surat->sifat_surat] : '-'; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Kontrak</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_no" class="form-control" disabled="disabled" value="<?php echo $surat->surat_no; ?>">
					</div>
<?php 
	if ($surat->status == 0)
	{
		$status = "Draft";
	}
	else
	{
		if ($surat->status == 1)
		{
			$status = "Aktif";
		}
		else 
		{
			if ($surat->status == 2)
			{
				$status = "Batal";
			}
		}
	}
?>					
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Status</label>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_perihal" class="form-control" disabled="disabled" value="<?php echo $status; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. SK <br> (dd-mm-yyyy)</label>
				<div class="col-lg-2 col-sm-3">
					<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl); ?>">
				</div>
				<label for="surat_tgl_masuk" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label" valign= "middle">Tgl. Berlaku <br> (dd-mm-yyyy)</label>
					<div class="col-lg-2 col-sm-3">
						<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
					</div>
				<label for="surat_unit_lampiran" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label" valign= "middle">Tgl. Berakhir <br> (dd-mm-yyyy)</label>
				<div class="col-lg-2 col-sm-3">
					<?php if ($surat->surat_unit_lampiran != NULL && $surat->surat_unit_lampiran != '-') { ?>
					<input type="text" id="surat_unit_lampiran" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_unit_lampiran); ?>">
					<?php }else { ?>
					<input type="text" id="surat_unit_lampiran" class="form-control" disabled="disabled" value="">
					<?php } ?>	
				</div>	
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled"><?php echo $surat->surat_perihal; ?></textarea>
				</div>
			</div>
		</div>
	</div><!-- /.box-body -->	
	
	<!-- Default box -->
	<div class="box box-primary collapsed-box">
		<div class="box-header with-border">
			<h3 class="box-title">Lampiran</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="attachment_list" class="box-body" style="display: none;">
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
<?php
	if($surat->status != 99) {
			echo form_hidden('action', 'surat.kontrak_model.stop_kontrak'); 
			echo form_hidden('status', 99); 
		
		if(has_permission(1) || has_permission(7)) {
?>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-8">
<?php 
		if($surat->status != 99){
			if($surat->status == 0){
?>					
				<!--	
					<button type="button" id="btnDelete" class="btn btn-app">
						<i class="fa fa-trash"></i> Hapus
					</button>
				-->
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/keputusan/ubah_keputusan/' . $surat->surat_id); ?>');">
							<i class="fa fa-file-text"></i> Ubah
					</button>
					<button type="button" class="btn btn-app" onclick="konfirmasi_sk();">
							<i class="fa fa-play"></i> Konfirmasi
					</button>
<?php
			}
			
			if($surat->status == 1){
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/keputusan/batalkan_keputusan/' . $surat->surat_id); ?>');">
						<i class="fa fa-book"></i> Batalkan SK
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
		}
	} else {
		/*		
//		echo '|' . $surat->unit_archive_status . '|';
		if(get_user_data('unit_id') == $surat->surat_to_ref_id && $surat->unit_archive_status != 99) {
//		if($surat->status == 5) {
?>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4"></div>
				
				<div class="col-xs-8">
					<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesArsipUnit();">
						<i class="fa fa-caret-square-o-right"></i> Simpan Sebagai Arsip
					</button>
				</div>
			</div>
		</div>
		<div class="overlay hide">
			<i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
<?php 
		}
		*/
	}
	
	echo form_close();
?>			

</section><!-- /.content -->

<?php 

	if($surat->status != 99) {
?>

<script type="text/javascript">
	
	$(document).ready(function() {
		
		$('.select2').select2();
		
		$('#price').number(true, 2);

		$('#btnDelete').click(function() {
			if(confirm("Hapus surat?")) { 
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
					data: "surat_id=<?php echo $surat->surat_id ?>&action=surat.surat_model.delete_surat",
					success: function(data) { //alert(data);
						if(typeof(data.error) != 'undefined') {
							if(data.error != '') {
								alert(data.error);
							} else {
								alert(data.msg);
								location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('surat/keputusan/keputusan_list')); ?>');
							}
						} else {
							alert('Data transfer error!');
						}
					}
				});  
			}
		});

	}); //end document

	function hentikanData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.prompt({
			title: 'Alasan Menghentikan Kontrak.', 
			inputType: 'textarea',
			callback: function(result) {
				if(result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {action: 'surat.kontrak_model.stop_kontrak', 
								surat_id: '<?php echo $surat->surat_id; ?>', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								note: result, 
								function_ref_id: <?php echo $function_ref_id; ?>,
							},
						success: function(data) {
							if(typeof(data.error) != 'undefined') {
								eval(data.execute);
							} else {
								bootbox.alert("Data transfer error!");
								$('#box-process-btn .overlay').addClass('hide');
							}
						}
					});
				} else {
					$('#box-process-btn .overlay').addClass('hide');
				}
			}
		});
	}

	var attachmentRow = <?php echo $last_seq; ?>;

	function addAttachment(){
		attachmentRow++;

		row = '<input type="hidden" id="attachment_state_' + attachmentRow + '" name="attachment[' + attachmentRow + '][state]" value="insert">' +
			'<div id="attachment_' + attachmentRow + '" class="form-group">' +
			'	<div class="col-md-8">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + attachmentRow + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="File ...">' +
			'		</div>' +
			'	</div>' +
			'	<div class="col-md-4">' +
			'		<div class="form-group">' +
			'			<div class="btn btn-default btn-file">' +
			'				<i class="fa fa-paperclip"></i> ' +
			'				<input type="file" name="attachment_file_' + attachmentRow + '" onchange="$(\'#flabel_' + attachmentRow + '\').html($(this).val())">' +
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

	function konfirmasi_sk() {
		$('#box-process-btn .overlay').removeClass('hide');

		bootbox.confirm("Konfirmasi surat?", function(result) {
			if(result){
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.keputusan_model.konfirmasi', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							note: result, 
							function_ref_id: <?php echo $function_ref_id; ?>,
							function_ref_name: 'Keputusan',
							},
					success: function(data) {
						if(typeof(data.error) != 'undefined') {
							eval(data.execute);
						} else {
							bootbox.alert("Data transfer error!");
							$('#box-process-btn .overlay').addClass('hide');
						}
					}
				});
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}

</script>

<?php 
	} else {
?>

<script type="text/javascript">
	
	$(document).ready(function() {
	
		$('.select2').select2();
		
	}); // end document
<?php 
	if(get_user_data('unit_id') == $surat->surat_to_ref_id && $surat->unit_archive_status != 99) {
?>
	
	function prosesArsipUnit() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Simpan sebagai Arsip?', function(result){
			if(result) {
				location.assign('<?php echo site_url('surat/external/register_arsip/' . $surat->surat_id); ?>');	 
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}

<?php 
	}
?>

</script>

<?php
	}
?>