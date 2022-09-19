<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li><a href="#">Eksternal</a></li>
		<li class="active">Keluar</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<?php
	echo form_open_multipart('', ' id="form_surat_keluar" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'add');
	echo form_hidden('action', 'surat.surat_model.insert_surat');
	echo form_hidden('function_ref_id', 2);
	echo form_hidden('function_ref_name', 'Surat Keluar Eksternal');
	echo form_hidden('jenis_agenda', 'SKE');
	echo form_hidden('create_agenda', 1);
	echo form_hidden('return', 'surat/external/outgoing');
	echo form_hidden('surat_from_ref_id', get_user_data('unit_id'));
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
				<label for="asal_surat" class="col-lg-2 col-sm-3 control-label">Asal Surat</label>
				<div class="col-lg-10 col-sm-9">
					<input type="text" name="asal_surat" id="asal_surat" class="asal_surat form-control" placeholder="Asal Surat ..."></input>
					<input type="hidden" name="ref_surat_masuk_id" id="ref_surat_masuk_id" class="form-control"></input>
				</div>
			</div>
			<div class="form-group">
				<label for="asal_surat" class="col-lg-2 col-sm-3 control-label">Lampiran Surat</label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<input type="text" name="attachment_ref" class="form-control" placeholder="Judul File" id="attachment_ref" readonly="readonly">
						<span class="input-group-addon">
							<a id="attachment_link" target="_blank"><i class="fa fa-file-text-o"></i> </a>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" name="surat_perihal" class="form-control required" rows="3" placeholder="Perihal" data-input-title="Perihal"><?php echo set_value('surat_perihal'); ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_format" class="col-lg-2 col-sm-3 control-label">Format Template</label>
				<div class="col-lg-10 col-sm-9">
					<?php
					$list = $this->admin_model->get_template_surat(2);
					$opt_format = array('' => '--');
					foreach ($list->result() as $row) {
						$opt_format[$row->format_surat_id] = $row->format_title;
					}

					echo form_dropdown('format_surat_id', $opt_format, '', (' id="format_surat_id" class="form-control required" data-input-title="Format Template" '));
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
					<!-- div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" title="Tambah Tujuan.." onclick="addTujuan();"><i class="fa fa-plus"></i></button>
					</div -->
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" title="simpan sebagai referensi untuk data baru" onclick="saveRef('origin_external');"><i class="fa fa-check"></i></button>
					</div>
				</div>
				<input type="hidden" id="surat_to_ref_id" name="surat_to_ref_id" value="">
				<div id="list-tujuan" class="box-body">
					<div class="form-group">
						<label for="surat_ext_title" class="col-sm-3 control-label">Jabatan <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<input type="text" name="surat_to_ref_data[title]" id="surat_ext_title" class="surat_ext_title form-control required" data-row-id="Jabatan Tujuan" data-input-title="Jabatan" value="<?php echo set_value('surat_ext_title'); ?>" placeholder="Jabatan Tujuan surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" name="surat_to_ref_data[nama]" id="surat_ext_nama" class="form-control required" data-input-title="Nama Tujuan" value="<?php echo set_value('surat_ext_nama'); ?>" placeholder="Nama Tujuan surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_instansi" class="col-sm-3 control-label">Instansi</label>
						<div class="col-sm-9">
							<input type="text" name="surat_to_ref_data[instansi]" id="surat_ext_instansi" class="form-control required" data-input-title="Instansi Tujuan" value="<?php echo set_value('surat_ext_instansi'); ?>" placeholder="Instansi Tujuan surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_alamat" class="col-sm-3 control-label">Alamat</label>
						<div class="col-sm-9">
							<input type="text" name="surat_to_ref_data[alamat]" id="surat_ext_alamat" class="form-control required" data-input-title="Instansi Alamat" value="<?php echo set_value('surat_ext_instansi'); ?>" placeholder="Instansi Tujuan surat...">
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
								foreach ($opt_klasifikasi->result() as $row) {
									echo '<optgroup label="' . $row->kode_klasifikasi . ' - ' . $row->nama_klasifikasi . '"></optgroup>';
									$opt_sub_klasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($row->entry_id);
									foreach ($opt_sub_klasifikasi->result() as $sub_row) {
										echo '<optgroup label=" > ' . $sub_row->kode_klasifikasi . ' - ' . $sub_row->nama_klasifikasi . '">';
										$opt_sub_subklasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($sub_row->entry_id);
										foreach ($opt_sub_subklasifikasi->result() as $sub_subrow) {
											echo '<option data-klasifikasi_sub_sub="' . $sub_subrow->nama_klasifikasi . '" data-klasifikasi_sub="' . $sub_row->nama_klasifikasi . '" data-klasifikasi="' . $row->nama_klasifikasi . '" value="' . $sub_subrow->kode_klasifikasi . '" > ' . $sub_subrow->kode_klasifikasi . ' - ' . $sub_subrow->nama_klasifikasi . '</option>';
										}

										echo '</optgroup>';
									}
								}

								echo form_dropdown('kode_klasifikasi_arsip', $opt_klasifikasi, '', (' id="kode_klasifikasi_arsip" class="form-control" '));
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
							<?php
							// $list = $this->user_model->get_user_role(4);
							// $opt_pejabat = array();
							// foreach ($list->result() as $row) {
							// $opt_pejabat[$row->user_id] = $row->user_name; 
							// }

							//echo form_multiselect('tembusan', $opt_pejabat, '', (' id="tembusan_0" class="form-control select2" '));
							?>
							<input type="text" id="tembusan_ext_nama" name="tembusan[0]" class="form-control tembusan_ext" data-input-title="Tembusan 0" value="<?php echo set_value('tembusan_ext_nama'); ?>" placeholder="Tembusan surat...">
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
							$list = $this->surat_model->get_all_parents_st(get_user_data('unit_id'));

							$opt_sign = array();
							if ($unit->ske_sign == 1) {
								$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
							}

							$approval_type = (in_array($unit->level, array('L0', 'L1'))) ? 'direksi' : 'non_direksi';

							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][index]', $p++);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][unit_name]', $unit->value);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][user_id]', $unit->user_id);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][nip]', $unit->nip_pejabat);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][jabatan]', $unit->jabatan);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][nama_pejabat]', $unit->nama_pejabat);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][email]', $unit->email);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][status]', 0);
							echo form_hidden('approval[' . $approval_type . '][diskusi]', json_encode(array()));
							echo form_hidden('approval[' . $approval_type . '][status]', 0);

							foreach ($list as $parent) {
								if ($parent['ske_sign'] == 1 && ($parent['level'] == 'L0' || $parent['level'] == 'L1')) {
									$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
								}

								$approval_type = (in_array($parent['level'], array('L0', 'L1'))) ? 'direksi' : 'non_direksi';

								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][index]', $p++);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][unit_name]', $parent['unit_name']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][user_id]', $parent['user_id']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][nip]', $parent['nip']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][jabatan]', $parent['jabatan']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][pangkat]', $parent['pangkat']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][nama_pejabat]', $parent['user_name']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][email]', $parent['email']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][status]', 0);
								echo form_hidden('approval[' . $approval_type . '][diskusi]', json_encode(array()));
								echo form_hidden('approval[' . $approval_type . '][status]', 0);
							}

							echo form_dropdown('signed', $opt_sign, '', ' class="form-control"');
							//	var_dump($list);
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
			<span class="h3 box-title">lampiran </span> <span class="small">Max. 8MB (*.pdf, *.jpg, *.jpeg, *.png, *.doc, *.xls) </span>
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
		var base_url = '<?php echo base_url(); ?>';

		$('.surat_ext_title').autocomplete({
			source: '<?php echo site_url('global/admin/eksternal_autocomplete') ?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_to_ref_id').val(ui.item.id);
				$('#surat_ext_nama').val(ui.item.nama_pejabat);
				$('#surat_ext_instansi').val(ui.item.instansi);
				$('#surat_ext_alamat').val(ui.item.address);
			}
		});

		$('.surat_ext_title').keyup(function() {
			if ($(this).val().trim() == '') {
				$('#surat_ext_nama').val('');
				$('#surat_ext_instansi').val('');
				$('#surat_ext_alamat').val('');
			}
		});

		$('.surat_int_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete') ?>',
			minLength: 3,
			select: function(event, ui) {
				r = $(this).attr('data-row-id');
				$('#surat_int_kode_' + r).val(ui.item.unit_code);
				$('#surat_int_unit_kode_' + r).html(ui.item.unit_code);
				$('#surat_int_unit_id_' + r).val(ui.item.id);
				$('#surat_int_jabatan_' + r).val(ui.item.jabatan);
				$('#surat_int_pangkat_' + r).val(ui.item.pangkat);
				$('#surat_int_nama_' + r).val(ui.item.nama_pejabat);
				$('#surat_int_nip_' + r).val(ui.item.nip_pejabat);
				$('#surat_int_dir_' + r).val(ui.item.instansi);
			}
		});

		$('.surat_int_unit').keyup(function() {
			if ($(this).val().trim() == '') {
				r = $(this).attr('data-row-id');
				$('#surat_int_kode_' + r).val('');
				$('#surat_int_unit_kode_' + r).html('________');
				$('#surat_int_unit_id_' + r).val('');
				$('#surat_int_nama_' + r).val('');
				$('#surat_int_pangkat_' + r).val('');
				$('#surat_int_nip_' + r).val('');
				$('#surat_int_dir_' + r).val('');
			}
		});

		$('.tembusan_ext').autocomplete({
			source: '<?php echo site_url('global/admin/tembusan_autocomplete') ?>',
			minLength: 3,
			select: function(event, ui) {
				//$('#tembusan_to_ref_id').val(ui.item.id);
				$('#tembusan_ext_nama').val(ui.item.nama_pejabat);
				//$('#tembusan_ext_instansi').val(ui.item.instansi);
				//$('#tembusan_ext_alamat').val(ui.item.address);
			}
		});

		$('.tembusan_ext').keyup(function() {
			if ($(this).val().trim() == '') {
				$('#tembusan_ext_nama').val('');
				//$('#tembusan_ext_instansi').val('');
				//$('#tembusan_ext_alamat').val('');
			}
		});

		$('.asal_surat').autocomplete({
			source: '<?php echo site_url('global/admin/asal_surat_autocomplete') ?>',
			minLength: 3,
			select: function(event, ui) {
				$('#asal_surat').val(ui.item.surat_id);
				$('#ref_surat_masuk_id').val(ui.item.surat_id);
				$('#attachment_ref').val(ui.item.title);
				$('#attachment_link').attr('title', ui.item.file_name);
				$('#attachment_link').attr('href', ui.item.file);
			}
		});

		$('.asal_surat').keyup(function() {
			if ($(this).val().trim() == '') {
				$('#asal_surat').val('');
				$('#ref_surat_masuk_id').val('');
				$('#attachment_ref').val('');
			}
		});

		$('.select2').select2();

	});

	function klasifikasiChange() {
		$('#klasifikasi').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi'));
		$('#klasifikasi_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub'));
		$('#klasifikasi_sub_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub_sub'));
	}

	function saveRef(t) {
		if (validateData($('#asal-area'))) {

			$.ajax({
				type: "POST",
				url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
				data: {
					action: 'global.admin_model.save_ref',
					jabatan: $('#surat_ext_title').val(),
					nama_pejabat: $('#surat_ext_nama').val(),
					instansi: $('#surat_ext_instansi').val(),
					address: $('#surat_ext_alamat').val(),
					ref_type: t
				},
				success: function(data) {
					bootbox.alert(data.message);
				}
			});
		}

		return false;
	}

	var tujuanRow = tembusanRow = 0;

	function addTujuan() {

		tujuanRow++;

		row = '<fieldset id="row_tujuan_' + tujuanRow + '" style="position: relative;">' +
			'<legend></legend>' +
			'<button type="button" class="btn btn-danger" onclick="removeTujuan(' + tujuanRow + ')" title="Hapus penandatangan..." style="position: absolute; padding: 1px 6px; top: 5px;"><i class="fa fa-minus"></i></button>' +
			'<div class="form-group">' +
			'	<label for="surat_ext_title_' + tujuanRow + '" class="col-sm-2 control-label">Jabatan <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>' +
			'	<div class="col-sm-4">' +
			'		<input type="text" id="surat_ext_title_' + tujuanRow + '" name="surat_ext_title[' + tujuanRow + ']" class="surat_ext_title form-control required" data-row-id="' + tujuanRow + '" data-input-title="Jabatan" value="" placeholder="Jabatan Tujuan surat...">' +
			'	</div>' +
			'	<label for="surat_ext_nama_' + tujuanRow + '" class="col-sm-2 control-label">Nama</label>' +
			'	<div class="col-sm-4">' +
			'		<input type="text" id="surat_ext_nama_' + tujuanRow + '" name="surat_ext_nama[' + tujuanRow + ']" class="form-control required" data-input-title="Nama" value="" placeholder="Nama Tujuan surat...">' +
			'	</div>' +
			'</div>' +
			'<div class="form-group">' +
			'	<label for="surat_ext_instansi_' + tujuanRow + '" class="col-sm-2 control-label">Instansi</label>' +
			'	<div class="col-sm-10">' +
			'		<input type="text" id="surat_ext_instansi_' + tujuanRow + '" name="surat_ext_instansi[' + tujuanRow + ']" class="form-control required" data-input-title="Instansi" value="" placeholder="Instansi Tujuan surat...">' +
			'	</div>' +
			'</div>' +
			'</fieldset>';

		$('#list-tujuan').append(row);

		$('#surat_ext_title_' + tujuanRow).autocomplete({
			source: '<?php echo site_url('global/admin/eksternal_autocomplete') ?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_ext_nama_' + tujuanRow).val(ui.item.nama_pejabat);
				$('#surat_ext_instansi_' + tujuanRow).val(ui.item.instansi);
				$('#surat_ext_alamat_' + tujuanRow).val(ui.item.address);
			}
		});
	}

	function removeTujuan(rid) {
		$('#row_tujuan_' + rid).remove();
	}

	var optSrtJab = '<?php // echo str_replace("\n", '', form_dropdown('surat_eksternal_ttd[%0%][type_ttd]', $opt_ttd, (set_value('surat_int_penandatangan') ? set_value('surat_int_penandatangan') : 'Penandatangan' ), (' id="surat_int_penandatangan_%0%" class="form-control" ')) ); 
						?>';

	function addTembusan() {
		tembusanRow++;

		var val_tembusan = '<?php echo set_value('tembusan_ext_nama'); ?>';

		row = '<div id="row_tembusan_' + tembusanRow + '" class="form-group">' +
			'	 <div class="col-sm-12">' +
			'		<div class="input-group">' +
			'			<input type="text" id="tembusan_ext_nama_' + tembusanRow + '" name="tembusan[' + tembusanRow + ']" data-input-title="Tembusan ' + tembusanRow + '" class="form-control tembusan_ext" value="' + val_tembusan + '" placeholder="Tembusan surat...">' +
			'		<div class="input-group-btn">' +
			'			<button type="button" class="btn btn-danger" onclick="removeTembusan(' + tembusanRow + ')" title="Hapus Tembusan..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'		</div>' +
			'	 </div>' +
			'</div>';

		$('#list-tembusan').append(row);

		$('.tembusan_ext').autocomplete({
			source: '<?php echo site_url('global/admin/tembusan_autocomplete') ?>',
			minLength: 3,
			select: function(event, ui) {
				//$('#tembusan_to_ref_id').val(ui.item.id);
				$('#tembusan_ext_nama_' + tembusanRow).val(ui.item.nama_pejabat);
				//$('#tembusan_ext_instansi').val(ui.item.instansi);
				//$('#tembusan_ext_alamat').val(ui.item.address);
			}
		});
	}

	function removeTembusan(rid) {
		$('#row_tembusan_' + rid).remove();
	}

	function setLampiran(l) {
		/*
		if(l > 0) {
			$('#list-lampiran').html('');
			
			for(i = 1; i <= l; i++) {
				row = '<div id="attachment_' + i + '" class="form-group">' +
					'	<div class="col-md-6">' +
					'		<input type="text" name="attachment[' + i + '][title]" class="form-control" placeholder="Judul File ...">' +
					'	</div>' +
					'	<div class="col-md-6">' +
					'		<div class="form-group">' +
					'			<div class="btn btn-default btn-file">' +
					'				<i class="fa fa-paperclip"></i> Attachment' +
					'				<input type="file" name="attachment_file_' + i + '" onchange="$(\'#flabel_' + i + '\').html($(this).val())">' +
					'			</div>' +
					'			<label id="flabel_' + i + '"></label>' +
					'		</div>' +
					'	</div>' +
					'</div>';
				$('#list-lampiran').append(row);
			}
		} else {
			bootbox.alert('Jumlah lampiran tidak sesuai.');
		}
		*/
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
</script>