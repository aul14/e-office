
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Arsip <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li><a href="#">Eksternal</a></li>
		<li class="active">Arsip</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php
	echo form_open_multipart('', ' id="form_surat_masuk" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'add');
	echo form_hidden('action', 'arsip.arsip_model.insert_arsip'); 
	echo form_hidden('return', 'global/dashboard'); 
	
	echo form_hidden('function_ref_id', $function_ref_id); 
	echo form_hidden('function_ref_name', 'Surat Masuk Eksternal'); 
	echo form_hidden('jenis_agenda', 'SME'); 
	echo form_hidden('create_agenda', '1'); 
	
	echo form_hidden('surat_from_ref', 'eksternal');
	echo form_hidden('surat_from_ref_id', get_user_data('unit_id'));
?>

	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Identitas Surat</h3>
		</div>
		<div class="box-body">
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-5 col-sm-9">
					<input type="text" id="surat_no" name="surat_no" class="form-control" data-input-title="Nomor Surat" value="<?php echo set_value('surat_no'); ?>">
				</div>
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label">Tanggal Surat</label>
				<div class="col-lg-3 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl" name="surat_tgl" class="form-control datepicker required" data-input-title="Tanggal Surat" value="<?php echo date('d-m-Y'); ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl_masuk" class="col-lg-2 col-sm-3 control-label">Tanggal Terima</label>
				<div class="col-lg-5 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl_masuk" name="surat_tgl_masuk" class="form-control datetimepicker required" data-input-title="Tgl Terima" value="<?php echo date('d-m-Y H:i'); ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_item_lampiran" class="col-lg-2 col-sm-3 control-label">Lampiran</label>
				<div class="col-lg-3 col-sm-9">
					<div class="input-group">
						<input type="number" id="surat_item_lampiran" name="surat_item_lampiran" class="form-control" value="<?php echo (set_value('surat_item_lampiran')) ? set_value('surat_item_lampiran') : 0; ?>">
						<div class="input-group-addon">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
	echo form_dropdown('surat_unit_lampiran', $opt_unit_lpr, '', (' id="surat_unit_lampiran" class="no-border" '));
?>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_perihal" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" name="surat_perihal" class="form-control required" rows="2" placeholder="Perihal" data-input-title="Perihal"><?php echo set_value('surat_perihal'); ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_ringkasan" class="col-lg-2 col-sm-3 control-label">Ringkasan</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_ringkasan" name="surat_ringkasan" class="form-control required" rows="3" placeholder="Ringkasan" data-input-title="Ringkasan"><?php echo set_value('surat_ringkasan'); ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label">Status Berkas</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_status_berkas = $this->admin_model->get_system_config('status_berkas');
	echo form_dropdown('status_berkas', $opt_status_berkas, '', (' id="status_berkas" class="form-control" '));
?>
				</div>
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sifat Surat</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	echo form_dropdown('sifat_surat', $opt_sifat_surat, '', (' id="sifat_surat" onchange="rahasiaChange();" class="form-control" '));
?>
				</div>
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Surat</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_jenis_surat = $this->admin_model->get_system_config('jenis_surat');
	echo form_dropdown('jenis_surat', $opt_jenis_surat, '', (' id="jenis_surat" class="form-control" '));
?>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
	<div class="row">
		<div class="col-lg-6">
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Asal Surat</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" title="simpan sebagai referensi untuk data baru" onclick="saveRef('origin_external');"><i class="fa fa-check"></i></button>
					</div>
				</div>
				<div id="asal-area" class="box-body">
					<div class="form-group">
						<label for="surat_ext_title" class="col-sm-3 control-label">Jabatan <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_title" name="surat_from_ref_data[title]" class="form-control required" data-input-title="Jabatan" value="<?php echo set_value('surat_ext_title'); ?>" placeholder="Jabatan Pengirim surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_nama" name="surat_from_ref_data[nama]" class="form-control required" data-input-title="Nama" value="<?php echo set_value('surat_ext_nama'); ?>" placeholder="Nama pengirim surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_instansi" class="col-sm-3 control-label">Instansi</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_instansi" name="surat_from_ref_data[instansi]" class="form-control required" data-input-title="Instansi" value="<?php echo set_value('surat_ext_instansi'); ?>" placeholder="Instansi asal surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_alamat" class="col-sm-3 control-label">Alamat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_alamat" name="surat_from_ref_data[alamat]" class="form-control required" data-input-title="Alamat" value="<?php echo set_value('surat_ext_alamat'); ?>" placeholder="Alamat Instansi...">
						</div>
					</div>
					<!--
					<div class="form-group">
						<label for="surat_ext_telepon" class="col-sm-3 control-label">Telepon</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_telepon" name="surat_from_ref_data[telepon]" class="form-control required" data-input-title="Telepon" value="<?php //echo set_value('surat_ext_telepon'); ?>" placeholder="Telepon Instansi...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_fax" class="col-sm-3 control-label">Fax</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_fax" name="surat_from_ref_data[fax]" class="form-control required" data-input-title="Fax" value="<?php //echo set_value('surat_ext_fax'); ?>" placeholder="Fax Instansi...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_email" class="col-sm-3 control-label">Email</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_email" name="surat_from_ref_data[email]" class="form-control required" data-input-title="Email" value="<?php //echo set_value('surat_ext_email'); ?>" placeholder="Email Instansi...">
						</div>
					</div>
					-->
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-lg-6">
<?php
	$def_tujuan = json_decode($this->admin_model->get_default_tujuan_sme())[0];
?>
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Tujuan Surat</h3>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label for="surat_to_unit" class="col-sm-3 control-label">Unit <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" id="surat_to_unit" name="surat_to_ref_data[unit]" class="form-control required" data-input-title="Unit Tujuan" value="<?php echo $def_tujuan->value; ?>" placeholder="Bagian / Sub Bagian tujuan surat...">
								<div id="surat_to_unit_kode" class="input-group-addon"><?php echo (set_value('surat_to_kode')) ? set_value('surat_to_kode') : $def_tujuan->unit_code; ?></div>	
								<input type="hidden" id="surat_to_ref" name="surat_to_ref" value="internal">
								<input type="hidden" id="surat_to_kode" name="surat_to_ref_data[kode]" value="<?php echo $def_tujuan->unit_code; ?>">
								<input type="hidden" id="surat_to_unit_id" name="surat_to_ref_id" value="<?php echo $def_tujuan->id; ?>">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_jabatan" class="col-sm-3 control-label">Jabatan</label>
						<div class="col-sm-9">
<?php 
	$opt_jabatan = array_merge(array('' => ' -- '), $this->admin_model->get_system_config('jabatan'));
	echo form_dropdown('surat_to_ref_data[jabatan]', $opt_jabatan, $def_tujuan->jabatan, (' id="surat_to_jabatan" class="form-control" data-input-title="Nama Jabatan" '));
?>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_nama" name="surat_to_ref_data[nama]" class="form-control" data-input-title="Nama Pejabat Tujuan" value="<?php echo $def_tujuan->nama_pejabat; ?>" placeholder="Nama Pejabat tujuan surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_dir" class="col-sm-3 control-label">Direktorat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_dir" name="surat_to_ref_data[dir]" class="form-control" readonly="readonly" data-input-title="Direktorat Tujuan" value="<?php echo $def_tujuan->instansi; ?>" placeholder="Direktorat tujuan surat...">
						</div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>

	<!-- Default box -->
	<div  id="attachment-area" class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title">lampiran </span> <span class="small">Max. 8MB (*.pdf, *.jpg, *.jpeg, *.png) </span>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="attachment_list" class="box-body">
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
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title"> Arsip</span>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>
<?php 
		if(has_permission(9)) {
//			echo form_hidden('action', 'surat.surat_model.update_arsip_surat'); 
//			echo form_hidden('status', 99); 
?>
		<div class="box-body">
			<div class="form-group">
				<label for="no_surat" class="col-md-2 control-label">Kode</label>
				<div class="col-md-10">
					<select name="kode_klasifikasi_arsip" id="kode_klasifikasi_arsip" class="form-control required" data-input-title="Kode Klasifikasi Arsip" onchange="klasifikasiChange();">
						<option data-klasifikasi_sub_sub="" data-klasifikasi_sub="" data-klasifikasi="" value="">--</option>
<?php 
	$opt_klasifikasi = $this->admin_model->get_parent_klasifikasi_arsip(0);
	foreach($opt_klasifikasi->result() as $row) {
		echo '<optgroup label="' . $row->kode_klasifikasi . ' - ' . $row->nama_klasifikasi . '"></optgroup>';
		$opt_sub_klasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($row->entry_id);
		foreach($opt_sub_klasifikasi->result() as $sub_row) {
			echo '<optgroup label=" > ' . $sub_row->kode_klasifikasi . ' - ' . $sub_row->nama_klasifikasi . '">';
			$opt_sub_subklasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($sub_row->entry_id);
			foreach($opt_sub_subklasifikasi->result() as $sub_subrow) {
				echo '<option data-klasifikasi_sub_sub="' . $sub_subrow->nama_klasifikasi . '" data-klasifikasi_sub="' . $sub_row->nama_klasifikasi . '" data-klasifikasi="' . $row->nama_klasifikasi . '" value="' . $sub_subrow->kode_klasifikasi . '" ' . (($sub_subrow->kode_klasifikasi == $surat->kode_klasifikasi_arsip) ? 'selected="selected"' : '') . ' > ' . $sub_subrow->kode_klasifikasi . ' - ' . $sub_subrow->nama_klasifikasi . '</option>';
			}
			echo '</optgroup>';
		}
	}
?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-md-2 control-label">Klasifikasi</label>
				<div class="col-md-10">
					<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-md-2 control-label"></label>
				<div class="col-md-10">
					<input type="text" id="klasifikasi_sub" class="form-control" disabled="disabled" value="">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-md-2 control-label"></label>
				<div class="col-md-10">
					<input type="text" id="klasifikasi_sub_sub" class="form-control" disabled="disabled" value="">
				</div>
			</div>
		</div>

		<script type="text/javascript">

			$(document).ready(function() {
				klasifikasiChange();
			});
			
			function klasifikasiChange() {
				$('#klasifikasi').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi'));
				$('#klasifikasi_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub'));
				$('#klasifikasi_sub_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub_sub'));
			}
			
		</script>
<?php
		} else {
			$result = $this->admin_model->get_klasifikasi_arsip($surat->kode_klasifikasi_arsip);
			if($result->num_rows() > 0) {
				$klasifikasi = $result->row();
			} else {
				$klasifikasi = new stdClass();
				$klasifikasi->kode_klasifikasi_arsip = '';
				$klasifikasi->nama_klasifikasi = '';
				$klasifikasi->nama_klasifikasi_sub = '';
				$klasifikasi->nama_klasifikasi_sub_sub = '';
			}
			
			//echo form_hidden('action', 'surat.surat_model.update_arsip_surat'); 
?>
		<div class="box-body">
			<div class="form-group">
				<label for="no_surat" class="col-md-3 control-label">Kode</label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi" class="form-control required" data-input-title="Kode Klasifikasi Arsip" disabled="disabled" value="<?php echo set_value('kode_klasifikasi_arsip'); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-md-3 control-label">Klasifikasi</label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="<?php echo $klasifikasi->nama_klasifikasi; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-md-3 control-label"></label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi_sub" class="form-control" disabled="disabled" value="<?php echo $klasifikasi->nama_klasifikasi_sub; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-md-3 control-label"></label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi_sub_sub" class="form-control" disabled="disabled" value="<?php echo $klasifikasi->nama_klasifikasi_sub_sub; ?>">
				</div>
			</div>
		</div>	
<?php
		}
?>
	</div>		
<?php 
		//}
	//}
?>	
	<div class="box box-primary">
		<div class="box-body">
			<button class="btn btn-app">
				<i class="fa fa-save"></i> Save
			</button>
		</div>
	</div>
	
<?php 
	echo form_close();
?>			

</section><!-- /.content -->

<script type="text/javascript">

	$(document).ready(function() {
		$('.datepicker').datepicker({autoclose : true, dateFormat : 'dd-mm-yy', maxDate: 0});

		$('.datetimepicker').datetimepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
//			minDate : 0
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

	}); //end document

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
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="Judul File ..." id="title_'+attachmentRow+'">' +
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
	
	function rahasiaChange() {
		if($("#sifat_surat").val() == 'rahasia') {
			$('#attachment-area').addClass('hide');
		} else {
			$('#attachment-area').removeClass('hide');
		}
	}

</script>
<!-- Main content -->

