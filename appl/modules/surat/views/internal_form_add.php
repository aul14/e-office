
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li><a href="#"><?php echo $title; ?></a></li>
		<li class="active">Baru</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php
	echo form_open_multipart('', ' id="form_surat_internal" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'add');
	echo form_hidden('action', 'surat.surat_model.insert_surat'); 
	echo form_hidden('function_ref_id', 3); 
	echo form_hidden('function_ref_name', 'Surat Internal');
	echo form_hidden('jenis_agenda', 'SI');
	echo form_hidden('create_agenda', 1);
	echo form_hidden('return', 'surat/internal/sheet'); 
	
	echo form_hidden('surat_from_ref_id', get_user_data('unit_id'));
	echo form_hidden('surat_from_ref_data[unit]', get_user_data('unit_name'));
	echo form_hidden('surat_from_ref_data[kode]', get_user_data('unit_code'));
	
	$result = $this->admin_model->get_ref_internal(get_user_data('unit_id'));
	$unit = $result->row();
	
	echo form_hidden('official_code', $unit->official_code);
	echo form_hidden('surat_from_ref', $unit->official_code);
	echo form_hidden('surat_from_ref_data[dir]', $unit->instansi);
	echo form_hidden('surat_from_ref_data[jabatan]', $unit->jabatan);
	echo form_hidden('surat_from_ref_data[pangkat]', $unit->pangkat);
	echo form_hidden('surat_from_ref_data[nama]', $unit->nama_pejabat);
	echo form_hidden('surat_from_ref_data[nip]', $unit->nip_pejabat);
?>

	<!-- Default box -->
	<div class="box">
		<div class="box-body">
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" name="surat_perihal" class="form-control required" rows="3" placeholder="Perihal" data-input-title="Perihal" ><?php echo set_value('surat_perihal'); ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_format" class="col-lg-2 col-sm-3 control-label">Format Template</label>
				<div class="col-lg-10 col-sm-9">
<?php 
	$list = $this->admin_model->get_template_surat(3);
	$opt_format = array('' => '--');
	foreach ($list->result() as $row) {
		$opt_format[$row->format_surat_id] = $row->format_title;
	}

	echo form_dropdown('format_surat_id', $opt_format, '', (' id="format_surat_id" class="form-control required" data-input-title="Format Template"'));
?>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_item_lampiran" class="col-lg-2 col-sm-3 control-label">Lampiran</label>
				<div class="col-lg-3 col-sm-9">
					<div class="input-group">
						<input type="number" id="surat_item_lampiran" name="surat_item_lampiran" class="form-control" min="0" value="<?php echo (set_value('surat_item_lampiran')) ? set_value('surat_item_lampiran') : 0; ?>" onchange="setLampiran(parseInt($(this).val()));">
						<div class="input-group-addon">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
	echo form_dropdown('surat_unit_lampiran', $opt_unit_lpr, '', (' id="surat_unit_lampiran" class="no-border" '));
?>
						</div>
					</div>
				</div>
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sifat Surat</label>
				<div class="col-lg-5 col-sm-9">
<?php 
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	echo form_dropdown('sifat_surat', $opt_sifat_surat, '', (' id="sifat_surat" class="form-control" '));
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
					<h3 class="box-title">Tujuan Surat</h3>
				</div>
				<div class="box-body">
					<div class="form-group">
						<div class="col-sm-9 pull-right">
							<label class="col-sm-5">
								<input type="radio" id="opt_tujuan_1" name="opt_tujuan" value="1" checked="checked"> 1 Orang
							</label>
							<label class="col-sm-7">
								<input type="radio" id="opt_tujuan_2" name="opt_tujuan" value="2"> Lebih dari 1 Orang
							</label>
						</div>
					</div>
					<div id="multi_user_internal" style="display: none;">
						<div class="form-group">
							<div class="col-sm-12">
								<input type="text" id="surat_to_ref_multi" class="form-control" placeholder="Tujuan lebih dari 1 orang" data-input-title="Tujuan" value="<?php echo set_value('surat_to_ref_multi'); ?>">
								<input type="hidden" id="surat_to_ref_detail" name="surat_to_ref_detail" class="form-control">
								<input type="hidden" id="surat_to_ref_multi_id" name="surat_to_ref_multi_id" class="form-control">
							</div>
						</div>	
					</div>
					<div id="user_internal" style="display: block;">
					<div class="form-group">
						<label for="surat_to_unit" class="col-sm-3 control-label">Unit <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" id="surat_to_unit" name="surat_to_ref_data[unit]" class="form-control" data-input-title="Unit Tujuan" value="<?php echo set_value('surat_to_ref_data_unit'); ?>" placeholder="Bagian / Sub Bagian tujuan surat...">
								<div id="surat_to_unit_kode" class="input-group-addon"><?php echo (set_value('surat_to_kode')) ? set_value('surat_to_kode') : '________'; ?></div>	
								<input type="hidden" id="surat_to_ref" name="surat_to_ref" value="internal">
								<input type="hidden" id="surat_to_kode" name="surat_to_ref_data[kode]" value="<?php echo set_value('surat_to_ref_data_kode'); ?>">
								<input type="hidden" id="surat_to_unit_id" name="surat_to_ref_id" value="<?php echo set_value('surat_to_ref_id'); ?>">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_jabatan" class="col-sm-3 control-label">Jabatan</label>
						<div class="col-sm-9">
<?php 
	$opt_jabatan = array_merge(array('' => ' -- '), $this->admin_model->get_system_config('jabatan'));
	echo form_dropdown('surat_to_ref_data[jabatan]', $opt_jabatan, set_value('surat_to_jabatan'), (' id="surat_to_jabatan" class="form-control" data-input-title="Nama Jabatan" '));
?>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_nama" name="surat_to_ref_data[nama]" class="form-control" data-input-title="Nama Pejabat Tujuan" value="<?php echo set_value('surat_to_ref_data_nama'); ?>" placeholder="Nama Pejabat tujuan surat...">
							<input type="hidden" id="surat_to_pangkat" name="surat_to_ref_data[pangkat]" value="<?php echo set_value('surat_to_ref_data_pangkat'); ?>">
							<input type="hidden" id="surat_to_nip" name="surat_to_ref_data[nip]" value="<?php echo set_value('surat_to_ref_data_nip'); ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_dir" class="col-sm-3 control-label">Direktorat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_dir" name="surat_to_ref_data[dir]" class="form-control" readonly="readonly" data-input-title="Direktorat Tujuan" value="<?php echo set_value('surat_to_ref_data_dir'); ?>" placeholder="Direktorat tujuan surat...">
						</div>
					</div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-lg-6">
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Klasifikasi Arsip</h3>
				</div>
				<div class="box-body">
					<div class="form-group">
						<label for="no_surat" class="col-md-3 control-label">Kode</label>
						<div class="col-md-9">
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
				echo '<option data-klasifikasi_sub_sub="' . $sub_subrow->nama_klasifikasi . '" data-klasifikasi_sub="' . $sub_row->nama_klasifikasi . '" data-klasifikasi="' . $row->nama_klasifikasi . '" value="' . $sub_subrow->kode_klasifikasi . '" > ' . $sub_subrow->kode_klasifikasi . ' - ' . $sub_subrow->nama_klasifikasi . '</option>';
			}

			echo '</optgroup>';
		}
	}
	
//	echo form_dropdown('kode_klasifikasi_arsip', $opt_klasifikasi, '', (' id="kode_klasifikasi_arsip" class="form-control" '));
?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="no_surat" class="col-md-3 control-label">Klasifikasi</label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-md-3"></label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi_sub" class="form-control" disabled="disabled" value="">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-md-3"></label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi_sub_sub" class="form-control" disabled="disabled" value="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Tembusan</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" title="Tambah Tembusan.." onclick="addTembusan();"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="list-tembusan" class="box-body">
					<div id="row_tembusan_0" class="form-group">
						<div class="col-sm-12">
							<input type="text" id="tembusan_ext_nama" name="tembusan[0]" class="form-control tembusan_ext" data-input-title="Tembusan" value="<?php echo set_value('tembusan_ext_nama'); ?>" placeholder="Tembusan surat...">
							<input type="hidden" id="tembusan_to_ref_id" name="distribusi_tembusan[0][id]" value="<?php echo set_value('tembusan_to_ref_id'); ?>">
							<input type="hidden" id="tembusan_ext_unit" name="distribusi_tembusan[0][unit]" value="<?php echo set_value('tembusan_ext_unit'); ?>">
							<input type="hidden" id="tembusan_ext_unit_code" name="distribusi_tembusan[0][kode]" value="<?php echo set_value('tembusan_ext_unit_code'); ?>">
							<input type="hidden" id="tembusan_ext_instansi" name="distribusi_tembusan[0][dir]" value="<?php echo set_value('tembusan_ext_instansi'); ?>">
							<input type="hidden" id="tembusan_ext_jabatan" name="distribusi_tembusan[0][jabatan]" value="<?php echo set_value('tembusan_ext_jabatan'); ?>">
							<input type="hidden" id="tembusan_ext_nama" name="distribusi_tembusan[0][nama]" value="<?php echo set_value('tembusan_ext_instansi'); ?>">
							<input type="hidden" id="tembusan_ext_nip" name="distribusi_tembusan[0][nip]" value="<?php echo set_value('tembusan_ext_nip'); ?>">
						</div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-md-6">
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Penanda Tangan</h3>
					<div class="box-tools pull-right">
						<!-- button type="button" class="btn btn-box-tool" title="Tambah Tembusan.." onclick="addTembusan();"><i class="fa fa-plus"></i></button -->
					</div>
				</div>
				<div id="list-tembusan" class="box-body">
					<div id="row_sign" class="form-group">
						<div class="col-sm-12">
<?php 
	$p = 1;
	$list = $this->surat_model->get_all_parents(get_user_data('unit_id'));
	
	$opt_sign = array();
	//$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	if($unit->ske_sign == 1) {
		$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	}
	
	$approval_type = (in_array($unit->level, array('L0', 'L1'))) ? 'direksi' : 'direksi';
	// $approval_type = 'direksi';
	
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][index]', $p++);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][unit_name]', $unit->value);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][user_id]', $unit->user_id);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nip]', $unit->nip_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][jabatan]', $unit->jabatan);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nama_pejabat]', $unit->nama_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][email]', $unit->email);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][status]', 0);
	echo form_hidden('approval[' . $approval_type. '][diskusi]', json_encode(array()));
	echo form_hidden('approval[' . $approval_type. '][status]', 0);
	
	foreach ($list as $parent) {
		$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
		// if($parent['ske_sign'] == 1) {
		// 	$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' '. $parent['unit_name'];
		// }

		$approval_type = (in_array($parent['level'], array('L0', 'L1'))) ? 'direksi' : 'direksi';
		
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][index]', $p++);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][unit_name]', $parent['unit_name']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][user_id]', $parent['user_id']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][nip]', $parent['nip']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][jabatan]', $parent['jabatan']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][pangkat]', $parent['pangkat']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][nama_pejabat]', $parent['user_name']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][email]', $parent['email']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][status]', 0);
		echo form_hidden('approval[' . $approval_type. '][diskusi]', json_encode(array()));
		echo form_hidden('approval[' . $approval_type. '][status]', 0);
	}
	
	echo form_dropdown('signed', $opt_sign, '', ' class="form-control"');
?>
						</div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title">lampiran </span> <span class="small">Max. 2MB (*.pdf, *.jpg, *.jpeg, *.png, *.doc, *.xls) </span>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="attachment_list" class="box-body form-group">
			<div id="attachment_0">
				<div class="col-md-7">
					<input type="text" name="attachment[0][title]" class="form-control file-attachment" placeholder="Judul File ..." id="title_0">
				</div>
				<div class="col-md-5">
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

		$('#opt_tujuan_1').click(function() {
			var pilih = $(this).val();

			if (pilih == 1) {
				$('#multi_user_internal').css('display', 'none');
				$('#user_internal').css('display', 'block');
			}
		});

		$('#opt_tujuan_2').click(function() {
			var pilih = $(this).val();
			
			if (pilih == 2) {
				$('#multi_user_internal').css('display', 'block');
				$('#user_internal').css('display', 'none');
			}
		});

		$('#surat_to_ref_multi').autocomplete({
			source: '<?php echo site_url('global/admin/tujuan_surat_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_to_ref_detail').val(ui.item.to_user_data);
				$('#surat_to_ref_multi_id').val(ui.item.id);
								
			}
		});
		
		$('#surat_to_ref_multi').keyup(function() {
			if($(this).val().trim() == '') {
				$('#surat_to_ref_detail').val('');
				$('#surat_to_ref_multi_id').val('');
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
				$('#surat_to_pangkat').val(ui.item.pangkat);
				$('#surat_to_nama').val(ui.item.nama_pejabat);
				$('#surat_to_nip').val(ui.item.nip_pejabat);
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

		$('.surat_ttd_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				r = $(this).attr('data-row-id');
				$('#surat_ttd_kode_' + r).val(ui.item.unit_code);
				$('#surat_ttd_unit_kode_' + r).html(ui.item.unit_code);
				$('#surat_ttd_unit_id_' + r).val(ui.item.id);
				$('#surat_ttd_jabatan_' + r).val(ui.item.jabatan);
				$('#surat_ttd_nama_' + r).val(ui.item.nama_pejabat);
				$('#surat_ttd_nip_' + r).val(ui.item.nip_pejabat);
				$('#surat_ttd_dir_' + r).val(ui.item.instansi);
			}
		});
		
		$('.surat_ttd_unit').keyup(function() {
			if($(this).val().trim() == '') {
				r = $(this).attr('data-row-id');
				$('#surat_ttd_kode_' + r).val('');
				$('#surat_ttd_unit_kode_' + r).html('________');
				$('#surat_ttd_unit_id_' + r).val('');
				$('#surat_ttd_nama_' + r).val('');
				$('#surat_ttd_nip_' + r).val('');
				$('#surat_ttd_dir_' + r).val('');
			}
		});
		
		$('.tembusan_ext').autocomplete({
			source: '<?php echo site_url('global/admin/tembusan_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#tembusan_to_ref_id').val(ui.item.id);
				$('#tembusan_ext_unit').val(ui.item.unit_name);
				$('#tembusan_ext_unit_code').val(ui.item.unit_code);
				$('#tembusan_ext_jabatan').val(ui.item.jabatan);
				$('#tembusan_ext_nama').val(ui.item.nama_pejabat);
				$('#tembusan_ext_instansi').val(ui.item.instansi);
				$('#tembusan_ext_nip').val(ui.item.nip_pejabat);
			}
		});
		
		$('.tembusan_ext').keyup(function() {
			if($(this).val().trim() == '') {
				$('#tembusan_ext_nama').val('');
				//$('#tembusan_ext_instansi').val('');
				//$('#tembusan_ext_alamat').val('');
			}
		});
	});

	function klasifikasiChange() {
		$('#klasifikasi').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi'));
		$('#klasifikasi_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub'));
		$('#klasifikasi_sub_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub_sub'));
	}
	
	var tembusanRow = 0;

//	var optSrtJab = '<?php //echo str_replace("\n", '', form_dropdown('surat_ttd[%0%][type_ttd]', $opt_ttd, (set_value('surat_to_penandatangan') ? set_value('surat_to_penandatangan') : 'Penandatangan' ), (' id="surat_to_penandatangan_%0%" class="form-control" ')) ); ?>';
	
	function addTembusan() {
		
		tembusanRow++;
		
		row = '<div id="row_tembusan_' + tembusanRow + '" class="form-group">' +
				'	<div class="col-sm-12">' +
				'		<div class="input-group">' +
				'			<input type="text" id="tembusan_ext_nama_' + tembusanRow + '" name="tembusan[' + tembusanRow + ']" class="form-control tembusan_ext" placeholder="Tembusan surat...">' +
				'			<input type="hidden" id="tembusan_to_ref_id_' + tembusanRow + '" name="distribusi_tembusan[' + tembusanRow + '][id]">' +
				'			<input type="hidden" id="tembusan_ext_unit_' + tembusanRow + '" name="distribusi_tembusan[' + tembusanRow + '][unit]">' +
				'			<input type="hidden" id="tembusan_ext_unit_code_' + tembusanRow + '" name="distribusi_tembusan[' + tembusanRow + '][kode]">' +
				'			<input type="hidden" id="tembusan_ext_instansi_' + tembusanRow + '" name="distribusi_tembusan[' + tembusanRow + '][dir]">' +
				'			<input type="hidden" id="tembusan_ext_jabatan_' + tembusanRow + '" name="distribusi_tembusan[' + tembusanRow + '][jabatan]">' +
				'			<input type="hidden" id="tembusan_ext_nama_' + tembusanRow + '" name="distribusi_tembusan[' + tembusanRow + '][nama]">' +
				'			<input type="hidden" id="tembusan_ext_nip_' + tembusanRow + '" name="distribusi_tembusan[' + tembusanRow + '][nip]">' +
				'			<div class="input-group-btn">' +
				'				<button type="button" class="btn btn-danger" onclick="removeTembusan(' + tembusanRow + ')" title="Hapus Tembusan..."><i class="fa fa-minus"></i></button>' +
				'			</div>' +
				'		</div>' +
				'	</div>' +
				'</div>';

		$('#list-tembusan').append(row);
		
		$('.tembusan_ext').autocomplete({
			source: '<?php echo site_url('global/admin/tembusan_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#tembusan_to_ref_id_' + tembusanRow).val(ui.item.id);
				$('#tembusan_ext_unit_' + tembusanRow).val(ui.item.unit_name);
				$('#tembusan_ext_unit_code_' + tembusanRow).val(ui.item.unit_code);
				$('#tembusan_ext_instansi_' + tembusanRow).val(ui.item.instansi);
				$('#tembusan_ext_nama_' + tembusanRow).val(ui.item.nama_pejabat);
				$('#tembusan_ext_jabatan_' + tembusanRow).val(ui.item.jabatan);
				$('#tembusan_ext_nip_' + tembusanRow).val(ui.item.nip_pejabat);
			}
		});
	}

	function removeTembusan(rid) {
		$('#row_tembusan_' + rid).remove();
	}

	function setLampiran(l) {
		
	}

	var attachmentRow = 0;

	function addAttachment() {
		
		attachmentRow++;

		row = '<div id="attachment_' + attachmentRow + '">' +
			'	<div class="col-md-7">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + attachmentRow + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="Judul File ..." id="title_' + attachmentRow + '">' +
			'		</div>' +
			'	</div>' +
			'	<div class="col-md-5">' +
			'		<div class="form-group">' +
			'			<div class="btn btn-default btn-file">' +
			'				<i class="fa fa-paperclip"></i>' +
			'				<input type="file" name="attachment_file_' + attachmentRow + '" onchange="$(\'#flabel_' + attachmentRow + '\').html($(this).val()); ; $(\'#title_' + attachmentRow + '\').val(getFilename($(this).val()));">' +
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
	
</script>