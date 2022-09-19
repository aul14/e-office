<?php 
	list($agenda_date, $agenda_time) = explode(' ', $surat->created_time);
	list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
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
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li><a href="#">Eksternal</a></li>
		<li class="active">Masuk</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php
	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('action', 'surat.surat_model.update_surat'); 
	echo form_hidden('surat_id', $surat->surat_id);
	echo form_hidden('return', 'surat/external/incoming/'); 
	echo form_hidden('function_ref_id', $function_ref_id); 
	echo form_hidden('function_ref_name', 'Surat Masuk Eksternal'); 
?>
	<!-- Default box -->
	<div class="box box-primary collapsed-box">
		<div class="box-header with-border pad table-responsive">
			<h3 class="box-title">Status Proses</h3>
			<table class="table text-center">
				<tr>
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
						<th>Keterangan</th>
					</tr>
				</thead>
				<tbody>
<?php 
	foreach($flow_notes as $row) {
		
		list($flow_date, $flow_time) = explode(' ', $row->created_time);
		$flow_date = db_to_human($flow_date);
		$flow_time = date('H:i:s', strtotime($flow_time));
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
	</div>
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Identitas Surat</h3>
		</div>
		<div class="box-body">
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 col-xs-12 control-label">Agenda</label>
				<div class="col-lg-4 col-sm-4 col-xs-6">
					<div class="input-group">
						<div class="input-group-addon"><?php echo strtoupper($surat->jenis_agenda); ?></div>
						<input type="text" id="agenda_id" name="agenda_id" class="form-control" disabled="disabled" value="<?php echo $surat->agenda_id; ?>">
					</div>
				</div>
				<div class="col-lg-6 col-sm-5 col-xs-6">
					<div class="input-group">
						<input type="text" id="created_time" name="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date; ?>" style="text-align: right;">
						<div class="input-group-addon"><?php echo $agenda_hours; ?></div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-5 col-sm-9">
					<input type="text" id="surat_no" name="surat_no" class="form-control" data-input-title="Nomor Surat" value="<?php echo $surat->surat_no; ?>" >
				</div>
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label">Tanggal Surat</label>
				<div class="col-lg-3 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl" name="surat_tgl" class="form-control datepicker required" data-input-title="Tanggal Surat" value="<?php echo db_to_human($surat->surat_tgl); ?>">
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
<?php 
	list($surat_tgl_masuk_date) = explode(' ', $surat->surat_tgl_masuk);
	$surat_tgl_masuk_date 		= db_to_human($surat_tgl_masuk_date);
?>
						<input type="text" id="surat_tgl_masuk" name="surat_tgl_masuk" class="form-control datetimepicker required" data-input-title="Tgl Terima" value="<?php echo $surat_tgl_masuk_date; ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_item_lampiran" class="col-lg-2 col-sm-3 control-label">Lampiran</label>
				<div class="col-lg-3 col-sm-9">
					<div class="input-group">
						<input type="number" id="surat_item_lampiran" name="surat_item_lampiran" class="form-control" min="0" value="<?php echo $surat->surat_item_lampiran; ?>">
						<div class="input-group-addon">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
	echo form_dropdown('surat_unit_lampiran', $opt_unit_lpr, $surat->surat_unit_lampiran, (' id="surat_unit_lampiran" class="no-border" '));
?>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_perihal" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" name="surat_perihal" class="form-control required" rows="2" placeholder="Perihal" data-input-title="Perihal" ><?php echo $surat->surat_perihal; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_ringkasan" class="col-lg-2 col-sm-3 control-label">Ringkasan</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_ringkasan" name="surat_ringkasan" class="form-control required" rows="3" placeholder="Ringkasan" data-input-title="Ringkasan" ><?php echo $surat->surat_ringkasan; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label">Status Berkas</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_status_berkas = $this->admin_model->get_system_config('status_berkas');
	echo form_dropdown('status_berkas', $opt_status_berkas, $surat->status_berkas, (' id="status_berkas" class="form-control" '));
?>
				</div>
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sifat Surat</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	echo form_dropdown('sifat_surat', $opt_sifat_surat, $surat->sifat_surat, (' id="sifat_surat" class="form-control" '));
?>
				</div>
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Surat</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_jenis_surat = $this->admin_model->get_system_config('jenis_surat');
	echo form_dropdown('jenis_surat', $opt_jenis_surat, $surat->jenis_surat, (' id="jenis_surat" class="form-control" '));
?>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->	
	<div class="row">
		<div class="col-md-6">
<?php 
	$surat_from_ref_data = json_decode($surat->surat_from_ref_data, TRUE);
	$param['surat_from_ref_data|title'] 	= $surat_from_ref_data['title'];
	$param['surat_from_ref_data|nama'] 		= $surat_from_ref_data['nama'];
	$param['surat_from_ref_data|instansi'] 	= $surat_from_ref_data['instansi'];
	$param['surat_from_ref_data|alamat'] 	= $surat_from_ref_data['alamat'];
?>
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Asal Surat</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" title="simpan sebagai referensi" onclick="saveRef('origin_external');"><i class="fa fa-check"></i></button>
					</div>
				</div>
				<div id="asal-area" class="box-body">
					<div class="form-group">
						<label for="surat_ext_title" class="col-sm-3 control-label">Jabatan <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_title" name="surat_from_ref_data[title]" class="form-control" data-input-title="Jabatan" value="<?php echo (isset($surat_from_ref_data['title'])) ? $surat_from_ref_data['title'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_from_nama" name="surat_from_ref_data[nama]" class="form-control" data-input-title="Nama" value="<?php echo (isset($surat_from_ref_data['nama'])) ? $surat_from_ref_data['nama'] : ''; ?>" placeholder="Nama Pejabat asal surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_instansi" class="col-sm-3 control-label">Instansi</label>
						<div class="col-sm-9">
							<input type="text" id="surat_from_instansi" name="surat_from_ref_data[instansi]" class="form-control" data-input-title="Instansi" value="<?php echo (isset($surat_from_ref_data['instansi'])) ? $surat_from_ref_data['instansi'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_alamat" class="col-sm-3 control-label">Alamat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_from_alamat" name="surat_from_ref_data[alamat]" class="form-control" data-input-title="alamat" value="<?php echo (isset($surat_from_ref_data['alamat'])) ? $surat_from_ref_data['alamat'] : ''; ?>">
						</div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		
		<div class="col-md-6">
<?php 
	$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
	$param['surat_to_ref_data|jabatan'] = $surat_to_ref_data['jabatan'];
	$param['surat_to_ref_data|unit'] 	= humanize($surat_to_ref_data['unit']);
	$param['surat_to_ref_data|kode'] 	= $surat_to_ref_data['kode'];
	$param['surat_to_ref_data|nama'] 	= $surat_to_ref_data['nama'];
	$param['surat_to_ref_data|dir'] 	= $surat_to_ref_data['dir'];

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
								<input type="text" id="surat_to_unit" name="surat_to_ref_data[unit]" class="form-control required" data-input-title="Unit Tujuan" value="<?php echo (isset($surat_to_ref_data['unit'])) ? $surat_to_ref_data['unit'] : ''; ?>" placeholder="Bagian / Sub Bagian tujuan surat...">
								<div id="surat_to_unit_kode" class="input-group-addon"><?php echo (isset($surat_to_ref_data['kode'])) ? $surat_to_ref_data['kode'] : '________'; ?></div>	
								<input type="hidden" id="surat_to_ref" name="surat_to_ref" value="internal">
								<input type="hidden" id="surat_to_kode" name="surat_to_ref_data[kode]" value="<?php echo (isset($surat_to_ref_data['kode'])) ? $surat_to_ref_data['kode'] : ''; ?>">
								<input type="hidden" id="surat_to_unit_id" name="surat_to_ref_id" value="<?php echo $surat->surat_to_ref_id; ?>">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_jabatan" class="col-sm-3 control-label">Jabatan</label>
						<div class="col-sm-9">
<?php 
	$opt_jabatan = array_merge(array('' => ' -- '), $this->admin_model->get_system_config('jabatan'));
	echo form_dropdown('surat_to_ref_data[jabatan]', $opt_jabatan, ((isset($surat_to_ref_data['jabatan'])) ? $surat_to_ref_data['jabatan'] : ''), (' id="surat_to_jabatan" class="form-control" data-input-title="Nama Jabatan" '));
?>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_nama" name="surat_to_ref_data[nama]" class="form-control" data-input-title="Nama Pejabat Tujuan" value="<?php echo (isset($surat_to_ref_data['nama'])) ? $surat_to_ref_data['nama'] : ''; ?>" placeholder="Nama Pejabat tujuan surat...">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_dir" class="col-sm-3 control-label">Direktorat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_dir" name="surat_to_ref_data[dir]" class="form-control" readonly="readonly" data-input-title="Direktorat Tujuan" value="<?php echo (isset($surat_to_ref_data['dir'])) ? $surat_to_ref_data['dir'] : ''; ?>" placeholder="Direktorat tujuan surat...">
						</div>
					</div>
				</div><!-- /.box-body -->
			</div>
		</div>
	</div>	
<?php
	if($surat->sifat_surat != 'rahasia') {
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title">lampiran </span> <span class="small">Max. 2MB (*.pdf, *.jpg, *.jpeg, *.png, *.zip, *.rar) </span>
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
						<input type="text" name="attachment[<?php echo $row->sort; ?>][title]" class="form-control lampiran-title" placeholder="Judul File ..." id="title_<?php echo $row->sort; ?>" value="<?php echo $row->title; ?>">
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
<?php
	}else{
?>	
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
	} 
	
	if($surat->status >= 1) {
		$distribusi = array();
		if($surat->distribusi != '') {
			$distribusi = json_decode($surat->distribusi, TRUE);
		}

		if(!isset($distribusi['direksi'])) {
			$distribusi['direksi'] = array();
		}
		
		if(!isset($distribusi['non_direksi'])) {
			$distribusi['non_direksi'] = array();
		}
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title"><?php echo ($surat->status == 1) ? 'Konsep Distribusi' : 'Konsep Disposisi'; ?></span>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		
		<div id="distribusi-area" class="box-body">
<?php 
	if(isset($surat->distribusi_tujuan) && $surat->distribusi_tujuan != 'null') {
		$distribusi_tujuan = json_decode($surat->distribusi_tujuan, TRUE);
		$param['distribusi_tujuan|jabatan'] = $distribusi_tujuan['jabatan'];
		$param['distribusi_tujuan|unit_id'] = $distribusi_tujuan['unit_id'];
		$param['distribusi_tujuan|unit'] 	= humanize($distribusi_tujuan['unit']);
		$param['distribusi_tujuan|kode'] 	= $distribusi_tujuan['kode'];
		$param['distribusi_tujuan|nama'] 	= $distribusi_tujuan['nama'];
		$param['distribusi_tujuan|dir'] 	= $distribusi_tujuan['dir'];
?>
			<div class="row">
				<div class="col-md-6">
				<!-- Untuk Menuliskan  Distribusi Tujuan-->
				<h5>Tujuan Surat</h5>
					<div class="box-body">
					<div class="form-group">
						<label for="unit" class="col-sm-1 control-label">Unit</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" id="distribusi_to_unit" name="distribusi_tujuan[unit]" class="form-control required" data-input-title="Unit Tujuan" value="<?php echo (isset($distribusi_tujuan['unit'])) ? $distribusi_tujuan['unit'] : ''; ?>" placeholder="Bagian / Sub Bagian tujuan surat...">
								<div id="distribusi_to_unit_kode" class="input-group-addon"><?php echo (isset($distribusi_tujuan['kode'])) ? $distribusi_tujuan['kode'] : '________'; ?></div>	
								<input type="hidden" id="surat_to_ref" name="surat_to_ref" value="internal">
								<input type="hidden" id="distribusi_to_kode" name="distribusi_tujuan[kode]" value="<?php echo (isset($distribusi_tujuan['kode'])) ? $distribusi_tujuan['kode'] : ''; ?>">
								<input type="hidden" id="distribusi_to_unit_id" name="surat_to_ref_id" value="<?php echo $surat->surat_to_ref_id; ?>">
								<input type="hidden" id="distribusi_to_id" name="distribusi_tujuan[unit_id]" value="<?php echo (isset($distribusi_tujuan['unit_id'])) ? $distribusi_tujuan['unit_id'] : ''; ?>">
								<input type="hidden" id="distribusi_to_jabatan" name="distribusi_tujuan[jabatan]" value="<?php echo (isset($distribusi_tujuan['jabatan'])) ? $distribusi_tujuan['jabatan'] : ''; ?>">
								<input type="hidden" id="distribusi_to_nama" name="distribusi_tujuan[nama]" value="<?php echo (isset($distribusi_tujuan['nama'])) ? $distribusi_tujuan['nama'] : ''; ?>">
								<input type="hidden" id="distribusi_to_dir" name="distribusi_tujuan[dir]" value="<?php echo (isset($distribusi_tujuan['dir'])) ? $distribusi_tujuan['dir'] : ''; ?>">
							</div>
						</div>
					</div>
					</div><!-- /.box-body -->
				</div>
			</div>
<?php 
	} else {
		$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
		$param['surat_to_ref_data|jabatan'] = $surat_to_ref_data['jabatan'];
		$param['surat_to_ref_data|unit_id'] = $surat->surat_to_ref_id;
		$param['surat_to_ref_data|unit'] 	= humanize($surat_to_ref_data['unit']);
		$param['surat_to_ref_data|kode'] 	= $surat_to_ref_data['kode'];
		$param['surat_to_ref_data|nama'] 	= $surat_to_ref_data['nama'];
		$param['surat_to_ref_data|dir'] 	= $surat_to_ref_data['dir'];
?>
			<div class="row">
				<div class="col-md-6">
				<!-- Untuk Menuliskan Distribusi Tujuan-->
				<h5>Tujuan Surat</h5>
					<div class="box-body">
					<div class="form-group">
						<label for="unit" class="col-sm-1 control-label">Unit</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" id="distribusi_to_unit" name="distribusi_tujuan[unit]" class="form-control required" data-input-title="Unit Tujuan" value="<?php echo (isset($surat_to_ref_data['unit'])) ? $surat_to_ref_data['unit'] : ''; ?>" placeholder="Bagian / Sub Bagian tujuan surat...">
								<div id="distribusi_to_unit_kode" class="input-group-addon"><?php echo (isset($surat_to_ref_data['kode'])) ? $surat_to_ref_data['kode'] : '________'; ?></div>	
								<input type="hidden" id="surat_to_ref" name="surat_to_ref" value="internal">
								<input type="hidden" id="distribusi_to_kode" name="distribusi_tujuan[kode]" value="<?php echo (isset($surat_to_ref_data['kode'])) ? $surat_to_ref_data['kode'] : ''; ?>">
								<input type="hidden" id="distribusi_to_id" name="distribusi_tujuan[unit_id]" value="<?php echo $surat->surat_to_ref_id; ?>">
								<input type="hidden" id="distribusi_to_unit_id" name="surat_to_ref_id" value="<?php echo $surat->surat_to_ref_id; ?>">
								<input type="hidden" id="distribusi_to_jabatan" name="distribusi_tujuan[jabatan]" value="<?php echo (isset($surat_to_ref_data['jabatan'])) ? $surat_to_ref_data['jabatan'] : ''; ?>">
								<input type="hidden" id="distribusi_to_nama" name="distribusi_tujuan[nama]" value="<?php echo (isset($surat_to_ref_data['nama'])) ? $surat_to_ref_data['nama'] : ''; ?>">
								<input type="hidden" id="distribusi_to_dir" name="distribusi_tujuan[dir]" value="<?php echo (isset($surat_to_ref_data['dir'])) ? $surat_to_ref_data['dir'] : ''; ?>">
							</div>
						</div>
					</div>
					</div><!-- /.box-body -->
				</div>
			</div>
<?php 
	}
?>			
			<div class="row">
				<div class="col-md-6">
					<h5>Direksi</h5>
<?php
		$list = $this->admin_model->get_direksi();
		if(count($distribusi['direksi']) > 0) {
			foreach($list->result() as $row) {
?>	
					<div class="checkbox"><label><input type="checkbox" id="konsep_disposisi_<?php echo $row->organization_structure_id; ?>" name="distribusi[direksi][]" value="<?php echo $row->organization_structure_id; ?>" <?php echo (isset($distribusi['direksi'][$row->organization_structure_id])) ? 'checked="checked"' : ''; ?> <?php echo (!has_permission(8) || $row->organization_structure_id == $surat->surat_to_ref_id) ? 'disabled="disabled"' : ''; ?>> <?php echo '( ' . $row->unit_code . ' ) ' . $row->jabatan . ' ' . $row->unit_name; ?></label></div>
<?php
			}
		} else {
			foreach($list->result() as $row) {
?>
					<div class="checkbox"><label><input type="checkbox" id="konsep_disposisi_<?php echo $row->organization_structure_id; ?>" name="distribusi[direksi][]" value="<?php echo $row->organization_structure_id; ?>" <?php echo (isset($distribusi['direksi'][$row->organization_structure_id])) ? 'checked="checked"' : ''; ?> <?php echo (!has_permission(8) || $row->organization_structure_id == $surat->surat_to_ref_id) ? 'disabled="disabled"' : ''; ?>> <?php echo '( ' . $row->unit_code . ' ) ' . $row->jabatan . ' ' . $row->unit_name; ?></label></div>
<?php
			}
		}
?>
				</div>
				<div class="col-md-6">
					<h5>Non Direksi</h5>
<?php 
	$distribusi_non_direksi = array_keys($distribusi['non_direksi']); 

	$list = $this->admin_model->get_non_direksi();
	$opt_pejabat = array();
	foreach ($list->result() as $row) {
		$opt_pejabat[$row->organization_structure_id] = '( ' . $row->unit_code . ' ) ' . $row->unit_name; 
	}

	echo form_multiselect('distribusi[non_direksi][]', $opt_pejabat, $distribusi_non_direksi, (' id="konsep_disposisi_non_direksi" class="form-control select2" ' . ((!has_permission(8)) ? 'disabled="disabled"' : '')));
?>
				</div>
			</div>
		</div>
	</div>
<?php 
	}
	
	if($surat->status >= 2) {
		if(has_permission(9)) {
?>
			<!-- Default box -->
			<div class="box box-primary">
			<div class="box-header with-border">
				<span class="h3 box-title"> Ekspedisi</span>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
				</div>
			</div>
			<div id="ekspedisi-area" class="box-body">
				<div class="row">
<?php
			if($surat->kirim_time == '') {
?>
					<div class="col-xs-4">
						<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/ekspedisi/sheet/' . $function_ref_id . '/' . $surat->surat_id); ?>');">
							<i class="fa fa-chevron-right"></i> Buat Ekspedisi Surat
						</button>
					</div>
					<div class="col-xs-8">
						
					</div>					
<?php 
			}
		}else {
			$list = $this->surat_model->get_list_ekspedisi($surat->jenis_agenda, $surat->surat_id);
?>
				<div class="col-xs-12">
					<dl class="dl-horizontal">
<?php
			foreach($list->result() as $row) {
				$status_terima = array(-1 => 'Tolak', 0 => 'Pending', 1 => 'Diterima');
?>
						<dt><?php echo $row->pengiriman_time ?></dt>
						<dd>Pengirim : <strong><?php echo $row->petugas_pengirim; ?></strong> / Penerima : <strong><?php echo $row->petugas_penerima; ?></strong></dd>
						<dd>Status : <strong><?php echo $status_terima[$row->status]; ?></strong></dd>
						<dd>Pengantar : <a href="<?php echo site_url('surat/ekspedisi/sheet/' . $row->ekspedisi_id); ?>" target="_blank"><?php echo $row->ekspedisi_id; ?></a></dd>
<?php
		}
?>					
					</dl>
				</div>
<?php 
		}
?>
			</div>
		</div>
	</div>	
<?php 			
	}
	
	if($surat->status != 99) {
		if(has_permission($process->permission_handle) && ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-')) {
?>	
	<div class="fixed-box-btn"></div>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4">
					<button class="btn btn-app">
						<i class="fa fa-save"></i> Update
					</button>
<?php 	
					if($surat->status == 0 && $surat->created_id == get_user_data('user_id')) {
?>					
						<button type="button" id="btnDelete" class="btn btn-app">
							<i class="fa fa-trash"></i> Hapus
						</button> 
<?php				
					}
?>
				</div>
				<div class="col-xs-8">
<?php
	if($surat->status != 4) {
		if(has_permission($process->permission_handle) && ($process->modify == 1 || $process->button_return != '-' || $process->button_process != '-')) {
			if($process->button_process != '-') {
				if($surat->status == 1) {
?>
					<button type="button" class="btn btn-app pull-right bg-green <?php echo ($surat->distribusi_tujuan == 'null' || $surat->distribusi_tujuan == null) ? 'hide' : ''; ?>" onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
				}else {
					if($surat->status == 0 && $surat->created_id == get_user_data('user_id')) {	
?>
						<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesData();">
							<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
						</button>
<?php 
					}
				}
			}
				
			if($process->button_return != '-') {
?>
					<button type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
						<i class="fa fa-caret-square-o-left"></i> <?php echo $process->button_return; ?>
					</button>
<?php 
			}
		
		}else{
			if(has_permission(9) && ($process->modify == 1 || $process->button_return != '-' || $process->button_process != '-')) {
				if($process->check_field == '-' || check_field_flow($process->check_field, array('surat_id' => $surat->surat_id)) ) {
					if($process->button_process != '-') {
?>
						<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesData();">
							<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
						</button>
<?php 
					}
				}
						
				if($process->button_return != '-') {
?>
						<button type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
							<i class="fa fa-caret-square-o-left"></i> <?php echo $process->button_return; ?>
						</button>
<?php 
				}
			}
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
	}

	echo form_close();
?>			

</section><!-- /.content -->

<script type="text/javascript">

	$(document).ready(function() {
		
		$('.select2').select2();
		
		$('#btnDelete').click(function() {
			//if(confirm("Hapus surat?")) { 
			bootbox.prompt({
				title: 'Alasan hapus.', 
				inputType: 'textarea',
				callback: function(result){
					if(result) {	
						$.ajax({
							type: "POST",
							url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
							data: "surat_id=<?php echo $surat->surat_id ?>&note="+result+"&action=surat.surat_model.delete_surat",
							success: function(data) {	//alert(data);
								if(typeof(data.error) != 'undefined') {
									if(data.error != '') {
										alert(data.error);
									} else {
										alert(data.msg);
										location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/workspace/surat_masuk_eksternal')); ?>');
									}
								} else {
									alert('Data transfer error!');
								}
							}
						}); 
					} 
				}
			});
		});

		$('.datepicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy', 
			maxDate: 0
		});

		$('.datetimepicker').datetimepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
// 			minDate : 0
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

		$('#distribusi_to_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#distribusi_to_kode').val(ui.item.unit_code);
				$('#distribusi_to_unit_kode').html(ui.item.unit_code);
				$('#distribusi_to_unit_id').val(ui.item.id);
				$('#distribusi_to_id').val(ui.item.id);
				$('#distribusi_to_jabatan').val(ui.item.jabatan);
				$('#distribusi_to_nama').val(ui.item.nama_pejabat);
				$('#distribusi_to_dir').val(ui.item.instansi);
			}
		});
		
		$('#distribusi_to_unit').keyup(function() {
			if($(this).val().trim() == '') {
				$('#distribusi_to_kode').val('');
				$('#distribusi_to_unit_kode').html('________');
				$('#distribusi_to_unit_id').val('');
				$('#distribusi_to_id').val('');
				$('#distribusi_to_nama').val('');
				$('#distribusi_to_dir').val('');
			}
		});

		$('#flabel_1').change(function(){
			var filename = ($('#file_' + attachmentRow).files && $('#file_' + attachmentRow).files.length) ? $('#file_' + attachmentRow).files[0].name.split('.')[0] : '';
			// console.log(filename);
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
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="File ..." id="title_'+attachmentRow+'">' +
			'		</div>' +
			'	</div>' +
			'	<div class="col-md-4">' +
			'		<div class="form-group">' +
			'			<div class="btn btn-default btn-file">' +
			'				<i class="fa fa-paperclip"></i> ' +
			'				<input type="file" name="attachment_file_' + attachmentRow + '" id="file_' + attachmentRow + '" onChange="$(\'#flabel_' + attachmentRow + '\').html($(this).val()); $(\'#title_' + attachmentRow + '\').val(getFilename($(this).val()));">' +
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
						data: {action: 'surat.surat_model.return_data', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								note: result, 
								last_flow: <?php echo $last_flow; ?>,
								function_ref_id: <?php echo $function_ref_id; ?>,
								flow_seq: <?php echo $surat->status; ?>
							},
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
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result){
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.proses_data', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							note: result, 
							last_flow: <?php echo $last_flow; ?>,
							flow_seq: <?php echo $surat->status; ?>,
							function_ref_id: <?php echo $function_ref_id; ?>,
							function_ref_name: 'Surat Masuk Eksternal',
							function_handler: '<?php echo $process->check_field_function; ?>'
							},
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
	
		function kirimData() {
			{
			bootbox.confirm('Kirim Surat Pengantar.', function (result) {
				if(result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: { action: 'surat.surat_model.kirim_ekspedisi', 
								function_ref_id: '<?php echo $function_ref_id; ?>',
								title: '<?php echo $title; ?>',
								ref_id: '<?php echo $surat->surat_id; ?>',
								surat_ext_title: '<?php echo $surat_from_ref_data['title'];?>',
								surat_ext_nama:'<?php echo htmlspecialchars($surat_from_ref_data['nama'], ENT_QUOTES); ?>',
								surat_ext_instansi:'<?php echo $surat_from_ref_data['instansi']; ?>' 
							},
						success: function(data) {
							if(typeof(data.error) != 'undefined') {
								eval(data.execute);
							}else {
								bootbox.alert("Data transfer error!");
							}
						}
					});
				}
			});
			}
		}

</script>