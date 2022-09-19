<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat<small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li class="active"><a href="#">Sppd</a></li>
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
	echo form_open_multipart('', ' id="form_surat_keluar" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'add');
	echo form_hidden('action', 'surat.sppd_model.insert_sppd'); 
	// echo form_hidden('return', 'surat/sppd/sppd'); 
	
	// echo form_hidden('function_ref_id', $function_ref_id); 
	// echo form_hidden('function_ref_name', 'SPPD'); 
	// echo form_hidden('jenis_agenda', 'SPPD'); 
	// echo form_hidden('create_agenda', 1); 
	
	// echo form_hidden('surat_from_ref', 'surat');
	// echo form_hidden('surat_from_ref_id', get_user_data('unit_id'));
	
	// echo form_hidden('surat_int_unit', get_user_data('unit_name'));
	// echo form_hidden('surat_int_unit_id', get_user_data('unit_id'));
	// echo form_hidden('surat_int_kode', get_user_data('unit_code'));
	
	$result = $this->admin_model->get_ref_internal(get_user_data('unit_id'));
	$unit = $result->row();
	
	echo form_hidden('official_code', $unit->official_code);
	echo form_hidden('surat_from_ref_data[dir]', $unit->instansi);
	echo form_hidden('surat_from_ref_data[jabatan]', $unit->jabatan);
	echo form_hidden('surat_from_ref_data[nama]', $unit->nama_pejabat);
	echo form_hidden('surat_from_ref_data[nip]', $unit->nip_pejabat);
?>

	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">SPPD</h3>
		</div>
		<div class="box-body">
			<div class="form-group">
				<label for="surat_sppd" class="col-lg-2 col-sm-3 control-label">No SPPD</label>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_sppd" name="surat_no" class="form-control" placeholder="No SPPD" data-input-title="Nomor Surat" value="<?php echo set_value('surat_no'); ?>">
				</div>
				
				<label for="surat_unit_lampiran" class="col-lg-2 col-sm-3 control-label">Kelas Perjalanan Dinas</label>
				<div class="col-lg-4 col-sm-9">
					<?php 
						$kelas_perjalanan_dinas = $this->sppd_model->get_referensi('kelas_perjalanan_dinas');
						echo form_dropdown('jenis_surat', $kelas_perjalanan_dinas, '', (' id="jenis_surat" class="form-control" '));
					?>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Mata Anggaran</label>
				<div class="col-lg-4 col-sm-9">
					<?php 
						$mata_anggaran = $this->sppd_model->get_referensi('mata_anggaran');
						echo form_dropdown('jenis_surat', $mata_anggaran, '', (' id="jenis_surat" class="form-control" '));
					?>
				</div>
				
				<label for="surat_unit_lampiran" class="col-lg-2 col-sm-3 control-label">Jenis Perjalanan</label>
				<div class="col-lg-4 col-sm-9">
					<?php 
						$jenis_perjalanan = $this->sppd_model->get_referensi('jenis_perjalanan');
						echo form_dropdown('jenis_surat', $jenis_perjalanan, '', (' id="jenis_surat" class="form-control" '));
					?>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Kegiatan</label>
				<div class="col-lg-4 col-sm-9">
					<textarea id="surat_perihal" name="surat_perihal" class="form-control required" rows="1" placeholder="Kegiatan" data-input-title="Perihal" ><?php echo set_value('surat_perihal'); ?></textarea>
				</div>
				
				<label for="surat_unit_lampiran" class="col-lg-2 col-sm-3 control-label">Transportasi</label>
				<div class="col-lg-4 col-sm-9">
					<?php 
						$transportasi = $this->sppd_model->get_referensi('transportasi');
						echo form_dropdown('jenis_surat', $transportasi, '', (' id="jenis_surat" class="form-control" '));
					?>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_unit_lampiran" class="col-lg-2 col-sm-3 control-label"></label>
				<div class="col-lg-4 col-sm-9">
				</div>
				<label for="surat_unit_lampiran" class="col-lg-2 col-sm-3 control-label">Tujuan</label>
				<div class="col-lg-4 col-sm-9">
					<input id="surat_unit_lampiran" name="surat_unit_lampiran" class="form-control required" placeholder="Tujuan" data-input-title="Nomor Surat Ref" value="<?php echo set_value('surat_unit_lampiran'); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="sppd_tgl_mulai" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tanggal Tugas <br> (dd-mm-yyyy)</label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl_mulai" name="surat_tgl_mulai" class="form-control datepicker required" data-input-title="Tgl Terima" value="<?php echo date('d-m-Y'); ?>">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="sppd_tgl_selesai" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">s/d</label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<input type="text" id="sppd_tgl_selesai" name="sppd_tgl_selesai" class="form-control datepicker required" data-input-title="Tgl Terima" value="<?php echo date('d-m-Y'); ?>">
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
			<h3 class="box-title">SPPD Kepada</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Penerima Tugas.." onclick="addPenerima();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="list-penerima" class="box-body">
			<fieldset id="row_penerima_0" style="position: relative;">
				<legend></legend>
				<div class="form-group">
					<label for="surat_to_nama_0" class="col-sm-2 control-label">Nama Pegawai</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nama_0" name="distribusi[0][nama]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Nama Pegawai">
					</div>
					<label for="surat_to_nip_0" class="col-sm-2 control-label">Jabatan</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nip_0" name="distribusi[0][nip]" class="form-control" data-input-title="NIP Pejabat Penerima Tugas" value="" placeholder="Jabatan">
					</div>
				</div>
				<div class="form-group">
					<label for="surat_to_nama_0" class="col-sm-2 control-label">Pangkat / Golongan</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nama_0" name="distribusi[0][nama]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Pangkat/Golongan">
					</div>
					<label for="surat_to_nip_0" class="col-sm-2 control-label">No Surat Tugas</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nip_0" name="distribusi[0][nip]" class="form-control" data-input-title="NIP Pejabat Penerima Tugas" value="" placeholder="No Surat Tugas">
					</div>
				</div>
				<div class="form-group">
					<label for="surat_to_nama_0" class="col-sm-2 control-label">Nip</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nama_0" name="distribusi[0][nama]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Nip">
					</div>
				</div>
			</fieldset>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">SPPD Tujuan</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Penerima Tugas.." onclick="addTujuan();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="list-tujuan" class="box-body">
			<fieldset id="row_penerima_0" style="position: relative;">
				<legend></legend>
				<div class="form-group">
					<label for="sppd_tujuan_di_0" class="col-sm-2 control-label">Tujuan di</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="sppd_tujuan_di_0" name="distribusi[0][nama]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Tujuan di">
					</div>
					<label for="sppd_tgl_berangkat_dari_0" class="col-sm-2 control-label">Tanggal berangkat dari</label>
					<div class="col-lg-4 col-sm-9">
						<div class="input-group">
							<input type="text" id="sppd_tgl_berangkat_dari_0" name="sppd_tgl_berangkat_dari_0" class="form-control datepicker required" data-input-title="Tgl Terima" value="<?php echo date('d-m-Y'); ?>">
							<div class="input-group-addon">
								<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">				
					<label for="sppd_tgl_tiba_di_0" class="col-sm-2 control-label">Tanggal tiba di</label>
					<div class="col-lg-4 col-sm-9">
						<div class="input-group">
							<input type="text" id="sppd_tgl_tiba_di_0" name="sppd_tgl_tiba_di_0" class="form-control datepicker required" data-input-title="Tgl Terima" value="<?php echo date('d-m-Y'); ?>">
							<div class="input-group-addon">
								<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
							</div>
						</div>
					</div>
				</div>
			</fieldset>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
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
		
		$('#price').number( true, 2 );
				
		$('.datepicker').datepicker({
				autoclose : true, 
				dateFormat : 'dd-mm-yy',
				//maxDate: 0
		});
		
		$('.cek').each(function(){
				$(this).datepicker();
		});
		
		//
		 // $('.surat_ext_title').autocomplete({
			// source: '<?php echo site_url('global/admin/eksternal_autocomplete')?>',
			// minLength: 3,
			// select: function(event, ui) {
				// $('#surat_ext_nama').val(ui.item.nama_pejabat);
				// $('#surat_ext_instansi').val(ui.item.instansi);
				// $('#surat_ext_alamat').val(ui.item.address);
				
			// }
		// });
				
		// $('.surat_ext_title').keyup(function() {
			// if($(this).val().trim() == '') {
				// $('#surat_ext_nama').val('');
				// $('#surat_ext_instansi').val('');
				// $('#surat_ext_alamat').val('');
			// }
		// });

		// $('.surat_int_unit').autocomplete({
			// source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			// minLength: 3,
			// select: function(event, ui) {
				// r = $(this).attr('data-row-id');
				// $('#surat_to_kode_' + r).val(ui.item.unit_code);
				// $('#surat_to_unit_kode_' + r).html(ui.item.unit_code);
				// $('#surat_to_unit_id_' + r).val(ui.item.id);
				// $('#surat_to_jabatan_' + r).val(ui.item.jabatan);
				// $('#surat_to_nama_' + r).val(ui.item.nama_pejabat);
				// $('#surat_to_nip_' + r).val(ui.item.nip_pejabat);
				// $('#surat_to_dir_' + r).val(ui.item.instansi);
			// }
		// });
		
		// $('.surat_int_unit').keyup(function() {
			// if($(this).val().trim() == '') {
				// r = $(this).attr('data-row-id');
				// $('#surat_to_kode_' + r).val('');
				// $('#surat_to_unit_kode_' + r).html('________');
				// $('#surat_to_unit_id_' + r).val('');
				// $('#surat_to_nama_' + r).val('');
				// $('#surat_to_nip_' + r).val('');
				// $('#surat_to_dir_' + r).val('');
			// }
		// });
	});

	// function saveRef(t) {
		// if(validateData($('#asal-area'))) {
		
			// $.ajax({
				// type: "POST",
				// url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
				// data: {action: 'global.admin_model.save_ref', 
						// jabatan: $('#surat_ext_title').val(),
						// nama_pejabat: $('#surat_ext_nama').val(),
						// instansi: $('#surat_ext_instansi').val(),
						// address: $('#surat_ext_alamat').val(),
						// ref_type: t},
				// success: function(data){
					// bootbox.alert(data.message);
				// }
			// });
		// }
		// return false;
	// }

	// function klasifikasiChange() {

		// $('#klasifikasi').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi'));
		// $('#klasifikasi_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub'));
		// $('#klasifikasi_sub_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub_sub'));
	// }

	var penerimaRow = 0;

	function addPenerima() {
		
		penerimaRow++;
		
		row = '<fieldset id="row_penerima_' + penerimaRow + '" style="position: relative;">' +
				'<legend></legend>' +
				'<button type="button" class="btn btn-danger" onclick="removePenerima(' + penerimaRow + ')" title="Hapus nama penerima ..." style="position: absolute; z-index:1; padding: 1px 6px; top: 5px;"><i class="fa fa-minus"></i></button>' +
				'<div class="form-group">' +
				'	<label for="surat_in_nama_' + penerimaRow + '" class="col-sm-2 control-label">Nama Pegawai</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<input type="text" id="surat_in_nama_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nama]" class="form-control" 		data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Nama Pegawai">' +
				'	</div>' +
				'	<label for="surat_in_nama_' + penerimaRow + '" class="col-sm-2 control-label">Jabatan</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<input type="text" id="surat_in_nama_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nama]" class="form-control" 		data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Jabatan">' +
				'	</div>' +
				'</div>' +
				'<div class="form-group">' +
				'	<label for="surat_in_nama_' + penerimaRow + '" class="col-sm-2 control-label">Pangkat/Golongan</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<input type="text" id="surat_in_nama_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nama]" class="form-control" 		data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Pangkat/Golongan">' +
				'	</div>' +
				'	<label for="surat_in_nama_' + penerimaRow + '" class="col-sm-2 control-label">No Surat Tugas</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				
				'		<input type="text" id="surat_in_nama_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nama]" class="form-control" 		data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="No Surat Tugas">' +
				'	</div>' +
				'</div>' +
				'<div class="form-group">' +
				'	<label for="surat_in_nama_' + penerimaRow + '" class="col-sm-2 control-label">Nip</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<input type="text" id="surat_in_nama_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nama]" class="form-control" 		data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Nip">' +
				'	</div>' +
				'</div>' +
			 '</fieldset>';

		$('#list-penerima').append(row);
	}

	function removePenerima(rid) {
		$('#row_penerima_' + rid).remove();
	}
	
	var tujuanRow =  0;

	function addTujuan() {
        $('.date_append:last').attr('id', tujuanRow);
		tujuanRow++;
		
		row = '<fieldset id="row_tujuan_' + tujuanRow + '" style="position: relative;">' +
			 	'<legend></legend>' +
				'<button type="button" class="btn btn-danger" onclick="removeTujuan(' + tujuanRow + ')" title="Hapus Tujuan ..." style="position: absolute; z-index:1; padding: 1px 6px; top: 5px;"><i class="fa fa-minus"></i></button>' +
				'<div class="form-group">' +
				'	<label for="surat_in_nama_' + tujuanRow + '" class="col-sm-2 control-label">Tujuan di</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<input type="text" id="surat_in_nama_' + tujuanRow + '" name="distribusi[' + tujuanRow + '][nama]" class="form-control" 		data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Tujuan di">' +
				'	</div>' +
				'	<label for="sppd_tgl_berangkat_dari_' + tujuanRow + '" class="col-sm-2 control-label">Tgl berangkat dari</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<div class="input-group">'+
				'			<input type="text" id="sppd_tgl_berangkat_dari_'+tujuanRow+'" name="sppd_tgl_berangkat_dari_'+tujuanRow+'" class="form-control date_append required" data-input-title="Tgl Tujuan" value="<?php echo date('d-m-Y'); ?>">'+
				'			<div class="input-group-addon">'+
				'				<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>'+
				'			</div>'+
				'		</div>'+
				'	</div>' +
				'</div>' +	
				'<div class="form-group">' +
				'	<label for="sppd_tgl_tiba_di_' + tujuanRow + '" class="col-sm-2 control-label">Tgl tiba di</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<div class="input-group">'+
				'			<input type="text" id="sppd_tgl_tiba_di_'+tujuanRow+'" name="sppd_tgl_tiba_di_'+tujuanRow+'" class="form-control date_append required" data-input-title="Tgl Terima" value="<?php echo date('d-m-Y'); ?>">'+
				'			<div class="input-group-addon">'+
				'				<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>'+
				'			</div>'+
				'		</div>'+
				'	</div>' +
				'</div>' +
			 	'</fieldset>';

		$('#list-tujuan').append(row);
	}
	
	$(document).on('focus', ".date_append", function() { 
		$(this).datepicker({
				autoclose : true, 
				dateFormat : 'dd-mm-yy',
		});
	});
	
	function removeTujuan(rid) {
		$('#row_tujuan_' + rid).remove();
	}

</script>