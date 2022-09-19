<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li class="active"><a href="#">Keputusan</a></li>
	</ol>
</section>


<style>
	.select2-hidden-accessible {
		height: 34px !important;
		width: 100% !important;
		padding: 6px 12px !important;
		font-size: 14px !important;
		line-height: 1.4285713 !important;
		color: #555 !important;
		
	}
</style>

<!-- Main content -->
<section class="content">
<?php
	echo form_open_multipart('', ' id="form_kontrak" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'add');
	echo form_hidden('action', 'surat.keputusan_model.insert_keputusan'); 
	echo form_hidden('return', 'surat/keputusan/keputusan_list'); 	
	echo form_hidden('function_ref_id', $function_ref_id); 
	echo form_hidden('function_ref_name', 'Keputusan'); 
	echo form_hidden('jenis_agenda', 'SK'); 
	echo form_hidden('create_agenda', '1'); 
	echo form_hidden('surat_from_ref', 'surat');
	echo form_hidden('surat_from_ref_id', get_user_data('unit_id'));
?>

	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Identitas Surat</h3>
		</div>
		<div class="box-body">
			<div class="form-group"> 
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Keputusan</label>
				<div class="col-lg-4 col-sm-9">
<?php 
	$opt_jenis_keputusan = $this->keputusan_model->get_referensi('jenis_keputusan');
	echo form_dropdown('jenis_surat', $opt_jenis_keputusan, '', (' id="jenis_surat" class="form-control" '));
?>
				</div>
			
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sumber Usulan</label>
				<div class="col-lg-4 col-sm-9">
<?php 
	$opt_kode_keputusan = $this->keputusan_model->get_referensi('sumber_usulan');
	echo form_dropdown('sifat_surat', $opt_kode_keputusan, '', (' id="sifat_surat" class="form-control" '));
?>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor SK</label>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_no" name="surat_no" class="form-control required" placeholder="Nomor Surat" data-input-title="Nomor Surat" value="<?php echo set_value('surat_no'); ?>">
				</div>
				
				<label for="surat_perihal" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-4 col-sm-9">
					<input id="surat_perihal" name="surat_perihal" class="form-control required" rows="2" placeholder="Hal" data-input-title="Hal" value="<?php echo set_value('surat_perihal'); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. SK <br> (dd-mm-yyyy)</label>
				<div class="col-lg-2 col-sm-3">
					<div class="input-group">
						<input type="text" id="surat_tgl" name="surat_tgl" class="form-control datepicker required" data-input-title="Tgl Terima" value="<?php echo date('d-m-Y'); ?>" onchange="tglMulaiChange();">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_tgl_masuk" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Berlaku <br> (dd-mm-yyyy) </label>
				<div class="col-lg-2 col-sm-3">
					<div class="input-group">
						<input type="text" id="surat_tgl_masuk" name="surat_tgl_masuk" class="form-control datemulaipicker required" data-input-title="tgl Berlaku" value="<?php echo date('d-m-Y'); ?>" onchange="tglBerlakuChange();">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_unit_lampiran" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Berakhir <br> (dd-mm-yyyy) </label>
				<div class="col-lg-2 col-sm-3">
					<div class="input-group">
						<input type="text" id="surat_unit_lampiran" name="surat_unit_lampiran" class="form-control dateselesaipicker" data-input-title="tgl Berakhir" value="" onchange="tglSelesaiChange();">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title">lampiran </span> <span class="small">Max. 8MB (*.pdf, *.jpg, *.jpeg, *.png, *.zip, *.rar) </span>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="attachment_list" class="box-body">
			<div id="attachment_0" class="form-group">
				<div class="col-md-8">
					<input type="text" name="attachment[0][title]" id="title_0" class="form-control file-attachment" placeholder="Judul File ...">
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
	
	<div class="box box-primary">
		<div class="box-body">
			<button class="btn btn-app">
				<i class="fa fa-save"></i> Simpan
			</button>
			
			<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/keputusan/keputusan_list'); ?>');">
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
		
		$(".select2").select2();
		
		$('#price').number( true, 2 );
				
		$('.datepicker').datepicker({autoclose : true, dateFormat : 'dd-mm-yy', //maxDate: 0
		});

		$('.datetimepicker').datetimepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			maxDate : 0
		});
		
		$('.datemulaipicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			minDate : 0
		});
		
		$('.dateselesaipicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			minDate : 1
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

	});

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

	function klasifikasiChange() {
		$('#klasifikasi').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi'));
		$('#klasifikasi_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub'));
		$('#klasifikasi_sub_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub_sub'));
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
			'			<input type="text" id="title_'+attachmentRow+'" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="Judul File ...">' +
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

 	function tglSelesaiChange() {
 		var date = $('.datepicker').val().split("-");
 		var nextDate = new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);

 		$('.dateselesaipicker').datepicker('option', 'minDate', new Date(nextDate));

 		if(toJSDate($('.datemulaipicker').val()) > toJSDate($('.dateselesaipicker').val())) {
 			$('.dateselesaipicker').val(nextDate.getDate() + '-' + (nextDate.getMonth() + 1) + '-' + nextDate.getFullYear())
 		}
 	}
	

</script>