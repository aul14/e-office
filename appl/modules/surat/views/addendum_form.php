<?php 
	list($agenda_date, $agenda_time) = explode(' ', $surat->created_time);
	$agenda_date = db_to_human($agenda_date);
?>

<style>
<!--
	input {
		border: none;
		background: transparent;
	}
-->
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Contract</a></li>
		<li><a href="#">Maintenance</a></li>
		<li class="active">Addendum I</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php
	$hidden = array('action' 			=> 'surat.kontrak_model.insert_addendum1',
					'surat_id' 			=> $surat->surat_id,
					'agenda_id' 		=> $surat->agenda_id,
					'ref_type' 			=> $surat->surat_from_ref,
					'ref_id' 			=> $surat->surat_id,
					'status_berkas' 	=> $surat->status_berkas,
					'sifat_surat' 		=> $surat->sifat_surat,
					'jenis_surat' 		=> $surat->jenis_surat,
					'surat_no' 			=> $surat->surat_no,
					'surat_tgl' 		=> $surat->surat_tgl,
					'surat_perihal' 	=> $surat->surat_perihal,
					'function_ref_id' 	=> $function_ref_id,
					'function_ref_name'	=>'addendum',
					'jenis_agenda' 		=> 'CM',
					'surat_from_ref'	=>'Contract',
	);

	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"', $hidden);
?>

<?php 
	$this->load->view('addendum_ref');
?>
	<!-- Default box -->
	
<?php 
	$state_flow = array(); 
	$last_flow = 0;
	foreach($flow as $row) {
		if($row->flow_seq == $surat->status) {
			$flow_pos = 'btn-success';
		} elseif ($row->flow_seq < $process->flow_seq) {
			$flow_pos = 'btn-danger';
		} else {
			$flow_pos = 'btn-warning disabled';
		}
	
		$last_flow = $row->flow_seq;
		$state_flow[$row->flow_seq] = $row->title;
	}

	$state_flow[99] = 'Arsip';
?>
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border" align="center">
			<h3 class="box-title" >ADDENDUM KONTRAK I</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>

		<div class="box-body">
			<div class="form-group">
			<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Kode Addendum</label>
				<div class="col-lg-4 col-sm-9">
<?php 
	$opt_kode_add = $this->admin_model->get_system_config('kode_add');
	echo form_dropdown('sifat_surat', $opt_kode_add, '', (' id="sifat_surat" class="form-control" '));
?>
				</div>
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Addendum</label>
				<div class="col-lg-4 col-sm-9">
<?php 
	$opt_jenis_add = $this->admin_model->get_system_config('jenis_add');
	echo form_dropdown('jenis_surat', $opt_jenis_add, '', (' id="jenis_surat" class="form-control" '));
?>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Addendum</label>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_no" name="surat_no" class="form-control" placeholder="Nomor Addendum" data-input-title="Nomor Surat" value="<?php echo set_value('surat_no'); ?>">
				</div>
				
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label">Mitra</label>
				<div class="col-lg-4 col-sm-9">
<?php 
	$opt_mitra = $this->kontrak_model->get_referensi('mitra');
	echo form_dropdown('status_berkas', $opt_mitra, $surat->status_berkas, (' disabled=disabled id="status_berkas" class="form-control" '));
?>			
				</div>
			</div>
			<div class="form-group">
				<label for="surat_ringkasan" class="col-lg-2 col-sm-3 control-label">Nilai Kontrak </label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">Rp</span>
							<input id="price" maxlength="21" name="surat_ringkasan" class="form-control required" rows="3" placeholder="Nilai Kontrak" data-input-title="Nilai Kontrak" value="<?php echo $surat->surat_ringkasan; ?>">
					</div>
				</div>
				<label for="surat_perihal" class="col-lg-2 col-sm-3 control-label">Hal</label>
				<div class="col-lg-4 col-sm-9">
					<input id="surat_perihal" name="surat_perihal" class="form-control required" readonly="readonly" rows="2" placeholder="Hal" data-input-title="Perihal" value="<?php echo $surat->surat_perihal; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_unit_lampiran" class="col-lg-2 col-sm-3 control-label" title="Format dd-mm-yyyy">Tgl. Addendum <br> (dd-mm-yyyy)</label>
				<div class="col-lg-2 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_unit_lampiran" name="surat_unit_lampiran" class="form-control datepicker required" data-input-title="Tanggal Surat" value="<?php echo $surat->surat_unit_lampiran; ?>" onchange="tglMulaiChange();">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label" title="Format dd-mm-yyyy">Tgl. Berlaku <br> (dd-mm-yyyy) </label>
				<div class="col-lg-2 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl" name="surat_tgl" class="form-control datemulaipicker required" data-input-title="Tgl Terima" value="<?php echo db_to_human($surat->surat_tgl); ?>"  onchange="tglBerlakuChange();">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_tgl_masuk" class="col-lg-2 col-sm-3 control-label" title="Format dd-mm-yyyy">Tgl. Berakhir <br> (dd-mm-yyyy)</label>
				<div class="col-lg-2 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl_masuk" name="surat_tgl_masuk" class="form-control dateselesaipicker required" data-input-title="tgl Berakhir" VALUE="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
		</div> <!-- /.box-body -->
	</div> <!-- /.box -->
	
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title">lampiran </span> <span class="small">Max. 8MB (*.pdf, *.jpg, *.jpeg, *.png, *.zip, *.rar) </span>
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
						<input type="text" name="attachment[<?php echo $row->sort; ?>][title]" class="form-control" placeholder="Judul File ..." value="<?php echo $row->title; ?>">
						<span class="input-group-addon">
							<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
						</span>
					</div>
				</div>
				<div class="col-md-4" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
					<div class="form-group">
						<div class="btn btn-default btn-file">
							<i class="fa fa-paperclip"></i> 
							<input type="file" name="attachment_file_<?php echo $row->sort; ?>" onchange="$('#flabel_<?php echo $row->sort; ?>').html($(this).val())">
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
	<div class="box box-primary">
		<div class="box-body">
			<button class="btn btn-app">
				<i class="fa fa-save"></i> Simpan
			</button>
			<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/kontrak/kontrak_aktif'); ?>');">
				<i class="fa fa-close"></i> Batal
			</button>
		</div>
	</div>
	
<?php
	echo form_close();
?>			

</section><!-- /.content -->

<script type="text/javascript">

	$(document).ready(function() {
		$('.select2').select2();
		$('#price').number( true, 2 );

		// $('.datepicker').datepicker({autoclose : true, dateFormat : 'dd-mm-yy', //maxDate: 0
		// });

		$('.datetimepicker').datetimepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
// 			minDate : 0
		});
		// $('.datemulaipicker').datepicker({
			// autoclose : true, 
			// dateFormat : 'dd-mm-yy',
		// //	maxDate : 0
		// });
		
		<?php 
			  $waktu = db_to_human($surat->surat_tgl); 
			  list($y, $m, $d) = explode('-', $waktu); 
		?>
		
		var nextDate = new Date( <?php echo "$d"; ?>, (parseInt(<?php echo "$m"; ?>)-1), (parseInt(<?php echo "$y"; ?>)+1), 0, 0, 0, 0);

		$('.dateselesaipicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			minDate : nextDate
		});
				
		<?php 
			  $waktu_mulai = db_to_human($surat->surat_tgl); 
			  list($y, $m, $d) = explode('-', $waktu_mulai); 
		?>
		
		var nextDate = new Date( <?php echo "$d"; ?>, (parseInt(<?php echo "$m"; ?>)-1), (parseInt(<?php echo "$y"; ?>)+1), 0, 0, 0, 0);
		
		<?php 
			  $waktu_selesai = db_to_human($surat->surat_tgl_masuk); 
			  list($y2, $m2, $d2) = explode('-', $waktu_selesai); 
		?>
		
		var nextMaxDate = new Date( <?php echo "$d2"; ?>, (parseInt(<?php echo "$m2"; ?>)-1), (parseInt(<?php echo "$y2"; ?>)+1), 0, 0, 0, 0);
		
		$('.datepicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			minDate : nextDate,
			maxDate : nextMaxDate			
		});
	
		<?php 
			  $waktu_berlaku = db_to_human($surat->surat_tgl); 
			  list($y, $m, $d) = explode('-', $waktu_berlaku); 
		?>
		
		var nextDate = new Date( <?php echo "$d"; ?>, (parseInt(<?php echo "$m"; ?>)-1), (parseInt(<?php echo "$y"; ?>)+1), 0, 0, 0, 0);
		
		<?php 
			  $waktu_batas = db_to_human($surat->surat_tgl_masuk); 
			  list($y2, $m2, $d2) = explode('-', $waktu_batas); 
		?>
		
		var nextMaxDate = new Date( <?php echo "$d2"; ?>, (parseInt(<?php echo "$m2"; ?>)-1), (parseInt(<?php echo "$y2"; ?>)+1), 0, 0, 0, 0);
		
		$('.datemulaipicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			minDate : nextDate,
			maxDate : nextMaxDate
		});
			
		$('#surat_ext_title').autocomplete({
			source: '<?php echo site_url('global/admin/eksternal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_ext_nama').val(ui.item.nama_pejabat);
				$('#surat_ext_instansi').val(ui.item.instansi);
				$('#surat_ext_alamat').val(ui.item.address);
				if (event.keyCode == 13) event.stopPropagation();
			}
		});
		
		$('#surat_ext_title').keyup(function() {
			if($(this).val().trim() == '') {
				$('#surat_ext_nama').val('');
				$('#surat_ext_instansi').val('');
				$('#surat_ext_alamat').val('');
			}
		});
		
		$('#surat_to_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_to_kode').val(ui.item.unit_code);
				$('#surat_to_unit_kode').html(ui.item.unit_code);
				$('#surat_to_unit_id').val(ui.item.id);
				$('#surat_to_jabatan').val(ui.item.jabatan);
				$('#surat_to_nama').val(ui.item.nama_pejabat);
				$('#surat_to_dir').val(ui.item.instansi);
			}
		});
		
		$('#surat_to_unit').keyup(function() {
			if($(this).val().trim() == '') {
				$('#surat_to_kode').val('');
				$('#surat_to_unit_kode').html('________');
				$('#surat_to_unit_id').val('');
				$('#surat_to_nama').val('');
				$('#surat_to_dir').val('');
			}
		});

	}); // end document

	function saveRef(t) {
		if(validateData($('#asal-area'))) {
		
			$.ajax({
				type: "POST",
				url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
				data: {action: 'global.admin_model.save_ref', 
						jabatan: $('#surat_ext_title').val(),
						nama_pejabat: $('#surat_ext_nama').val(),
						instansi: $('#surat_ext_instansi').val(),
						address: $('#surat_ext_alamat').val(),
						ref_type: t},
				success: function(data){
					bootbox.alert(data.message);
				}
			}); 
		}

		return false;
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

	function returnData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.prompt({
			title: 'Kembalikan berkas.', 
			inputType: 'textarea',
			callback: function(result){
				if(result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: { action: 'surat.surat_model.return_data', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								note: result, 
								last_flow: <?php echo $last_flow; ?>,
								function_ref_id: <?php echo $function_ref_id; ?>,
								flow_seq: <?php echo $surat->status; ?> },
						success: function(data) {
							if(typeof(data.error) != 'undefined') {
								eval(data.execute);
								$('#box-process-btn .overlay').addClass('hide');
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

	function prosesData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result) {
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: { action: 'surat.surat_model.proses_data', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							note: result, 
							last_flow: <?php echo $last_flow; ?>,
							function_ref_id: <?php echo $function_ref_id; ?>,
							flow_seq: <?php echo $surat->status; ?>,
							function_ref_id: <?php echo $function_ref_id; ?>,
							function_ref_name: 'Surat Masuk Eksternal',
							function_handler: '<?php echo $process->check_field_function; ?>' },
					success: function(data){
						if(typeof(data.error) != 'undefined') {
							eval(data.execute);
							$('#box-process-btn .overlay').addClass('hide');
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
	
	function toJSDate( date ) {
		var date = date.split("-");

		//(year, month, day, hours, minutes, seconds, milliseconds)
		//subtract 1 from month because Jan is 0 and Dec is 11
		return new Date(date[2], (parseInt(date[1])-1), date[0], 0, 0, 0, 0);
	}
	
	function tglBerlakuChange() {
		var date = $('.datemulaipicker').val().split("-");
		var nextDate = new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);

		$('.dateselesaipicker').datepicker('option', 'minDate', new Date(nextDate));

		if(toJSDate($('.datemulaipicker').val()) > toJSDate($('.dateselesaipicker').val())) {
			$('.dateselesaipicker').val(nextDate.getDate() + '-' + (nextDate.getMonth() + 1) + '-' + nextDate.getFullYear())
		}
	}
		
	function tglMulaiChange() {
		var date = $('.datepicker').val().split("-");
		var nextDate = new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);

		$('.datemulaipicker').datepicker('option', 'minDate', new Date(nextDate));

		if(toJSDate($('.datepicker').val()) > toJSDate($('.datemulaipicker').val())) {
			$('.datemulaipicker').val(nextDate.getDate() + '-' + (nextDate.getMonth() + 1) + '-' + nextDate.getFullYear())
		}
	}

</script>