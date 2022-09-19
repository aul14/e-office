<?php 
	list($agenda_date, $agenda_time) = explode(' ', $surat->created_time);
	list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
	$agenda_date = db_to_human($agenda_date);
?>
<style>

</style>
<section class="content">
<?php
	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('surat_id', $surat->surat_id);	
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
	$state_flow[22] = 'Disposisi';
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
					<input type="text" id="agenda_id" class="form-control" disabled="disabled" value="<?php echo strtoupper($surat->jenis_agenda) . ' - ' . $surat->agenda_id; ?>">
				</div>
				<div class="col-lg-6 col-sm-5 col-xs-6">
					<input type="text" id="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date . ' ' . $agenda_hours; ?>" style="text-align: right;">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-10 col-sm-9">
					<input type="text" id="surat_no" class="form-control" disabled="disabled" value="<?php echo $surat->surat_no; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label">Tanggal Surat</label>
				<div class="col-lg-2 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl); ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_tgl_masuk" class="col-lg-2 col-sm-3 control-label">Tanggal Terima</label>
				<div class="col-lg-2 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
				<label for="surat_item_lampiran" class="col-lg-2 col-sm-3 control-label">Lampiran</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
					<input type="text" id="surat_item_lampiran" class="form-control" disabled="disabled" value="<?php echo $surat->surat_item_lampiran . ' ' . $opt_unit_lpr[$surat->surat_unit_lampiran]; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled" rows="3"><?php echo $surat->surat_perihal; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_ringkasan" class="col-lg-2 col-sm-3 control-label">Ringkasan</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_ringkasan" name="surat_ringkasan" class="form-control" disabled="disabled" rows="3" placeholder="Ringkasan" data-input-title="Ringkasan" ><?php echo $surat->surat_ringkasan; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label">Status Berkas</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_status_berkas = $this->admin_model->get_system_config('status_berkas');
?>
					<input type="text" id="status_berkas" class="form-control" disabled="disabled" value="<?php echo $opt_status_berkas[$surat->status_berkas]; ?>">
				</div>
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sifat Surat</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
?>
					<input type="text" id="sifat_surat" class="form-control" disabled="disabled" value="<?php echo $opt_sifat_surat[$surat->sifat_surat]; ?>">
				</div>
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Surat</label>
				<div class="col-lg-2 col-sm-9">
<?php 
	$opt_jenis_surat = $this->admin_model->get_system_config('jenis_surat');
?>
					<input type="text" id="jenis_surat" class="form-control" disabled="disabled" value="<?php echo $opt_jenis_surat[$surat->jenis_surat]; ?>">
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
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Asal Surat </h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="asal-area" class="box-body" style="display: none;">
					<div class="form-group">
						<label for="surat_ext_title" class="col-sm-3 control-label">Jabatan</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_title" class="form-control" disabled="disabled" value="<?php echo $surat_from_ref_data['title']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_nama" class="form-control" disabled="disabled" data-input-title="Nama" value="<?php echo $surat_from_ref_data['nama']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_instansi" class="col-sm-3 control-label">Instansi</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_instansi" class="form-control" disabled="disabled" value="<?php echo $surat_from_ref_data['instansi']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_alamat" class="col-sm-3 control-label">Alamat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_alamat" class="form-control" disabled="disabled" value="<?php echo $surat_from_ref_data['alamat']; ?>">
						</div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-md-6">
<?php 
	$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
	$param['surat_to_ref_data|jabatan'] 	= $surat_to_ref_data['jabatan'];
	$param['surat_to_ref_data|unit'] 		= humanize($surat_to_ref_data['unit']);
	$param['surat_to_ref_data|kode'] 		= $surat_to_ref_data['kode'];
	$param['surat_to_ref_data|nama'] 		= $surat_to_ref_data['nama'];
	$param['surat_to_ref_data|dir'] 		= $surat_to_ref_data['dir'];
?>
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Tujuan Surat</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body" style="display: none;">
					<div class="form-group">
						<label for="surat_int_unit" class="col-sm-3 control-label">Unit</label>
						<div class="col-sm-6">
							<input type="text" id="surat_int_unit" class="form-control" disabled="disabled" value="<?php echo humanize($surat_to_ref_data['unit']); ?>">
						</div>
						<div class="col-sm-3">
							<input type="text" id="surat_int_kode" class="form-control" disabled="disabled" value="<?php echo $surat_to_ref_data['kode']; ?>">
						</div>
						<input type="hidden" id="surat_int_unit_id" value="<?php echo $surat->surat_to_ref_id; ?>">
					</div>
					<div class="form-group">
						<label for="surat_int_jabatan" class="col-sm-3 control-label">Jabatan</label>
						<div class="col-sm-9">
<?php 
	$opt_jabatan = $this->admin_model->get_contract_config('jabatan');
?>
							<input type="text" id="surat_int_jabatan" class="form-control" disabled="disabled" value="<?php echo $surat_to_ref_data['jabatan']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_int_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_int_nama" class="form-control" disabled="disabled" value="<?php echo $surat_to_ref_data['nama']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_int_dir" class="col-sm-3 control-label">Direktorat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_int_dir" class="form-control" disabled="disabled" data-input-title="Direktorat Tujuan" value="<?php echo $surat_to_ref_data['dir']; ?>">
						</div>
					</div>					
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>			
<?php
	if($surat->status == 5) {
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title">Lampiran </span> <span class="small">Max. 8MB (*.pdf, *.jpg, *.jpeg, *.png) </span>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="attachment_list" class="box-body">
<?php 
	$last_seq = 0;
	foreach ($attachment as $row) {
?>
			<div id="attachment_<?php echo $row->sort; ?>" class="col-md-12">
				<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> <label> <?php echo $row->title; ?> </label>
			</div>
			<input type="hidden" name="attachment[<?php echo $row->sort; ?>][id]" value="<?php echo $row->file_attachment_id; ?>">
			<input type="hidden" name="attachment[<?php echo $row->sort; ?>][file]" value="<?php echo $row->file; ?>">
			<input type="hidden" id="attachment_state_<?php echo $row->sort; ?>" name="attachment[<?php echo $row->sort; ?>][state]" value="-">
			<div id="attachment_<?php echo $row->sort; ?>" class="form-group">
				<div class="col-md-8">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-danger" onclick="removeAttachment(<?php echo $row->sort; ?>)" title="Hapus file..."><i class="fa fa-minus"></i></button>
						</div>
						<input type="text" name="attachment[<?php echo $row->sort; ?>][title]" class="form-control" placeholder="Judul File ..." id="title_<?php echo $row->sort; ?>">
						<!--
						<span class="input-group-addon">
							<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
						</span>
						-->
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
	}else {
?>
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Lampiran</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div id="attachment_list" class="box-body" style="display: block;">
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
			<span class="h3 box-title">Distribusi Ke: </span>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div id="distribusi-area" class="box-body">
			<span class="h4 box-title">Konsep </span> 

<?php 
if(isset($surat->distribusi_tujuan) && $surat->distribusi_tujuan != 'null') {
	$distribusi_tujuan = json_decode($surat->distribusi_tujuan, TRUE);
		$param['distribusi_tujuan|jabatan']		= $distribusi_tujuan['jabatan'];
		$param['distribusi_tujuan|unit_id']		= $distribusi_tujuan['unit_id'];
		$param['distribusi_tujuan|unit'] 		= humanize($distribusi_tujuan['unit']);
		$param['distribusi_tujuan|kode'] 		= $distribusi_tujuan['kode'];
		$param['distribusi_tujuan|nama'] 		= $distribusi_tujuan['nama'];
		$param['distribusi_tujuan|dir'] 		= $distribusi_tujuan['dir'];
?>
			<div class="row">
				<div class="col-md-6">
				<!-- Untuk Menuliskan  Distribusi Tujuan -->
				<h5>Tujuan Surat</h5>
					<div class="box-body">
						<div class="col-sm-9" style="font-weight:600;">
							<div class="input-group">
								<?php echo (isset($distribusi_tujuan['unit'])) ? $distribusi_tujuan['unit'] : ''; ?>
<!--							<input type="text" id="surat_to_unit" name="distribusi[tujuan][unit]" class="form-control required" disabled="disabled" data-input-title="Unit Tujuan" value="<?php // echo (isset($distribusi_tujuan['unit'])) ? $distribusi_tujuan['unit'] : ''; ?>" placeholder="Bagian / Sub Bagian tujuan surat...">
								<div id="surat_to_unit_kode" class="input-group-addon"><?php // echo (isset($distribusi_tujuan['kode'])) ? $distribusi_tujuan['kode'] : '________'; ?></div>	
 								<input type="hidden" id="surat_to_ref" name="surat_to_ref" value="internal"> 
								<input type="hidden" id="surat_to_kode" name="distribusi[tujuan][kode]" value="<?php // echo (isset($distribusi_tujuan['kode'])) ? $distribusi_tujuan['kode'] : ''; ?>">
								<input type="hidden" id="surat_to_unit_id" name="surat_to_ref_id" value="<?php //echo $surat->surat_to_ref_id; ?>">
								<input type="hidden" id="surat_to_kode" name="distribusi[tujuan][jabatan]" value="<?php // echo (isset($distribusi_tujuan['jabatan'])) ? $distribusi_tujuan['jabatan'] : ''; ?>">
								<input type="hidden" id="surat_to_unit_id" name="distribusi[tujuan]['nama]" value="<?php // echo (isset($distribusi_tujuan['nama'])) ? $distribusi_tujuan['nama'] : ''; ?>">
								<input type="hidden" id="surat_to_kode" name="distribusi[tujuan][dir]" value="<?php //echo (isset($distribusi_tujuan['dir'])) ? $distribusi_tujuan['dir'] : ''; ?>">
								</div> -->
							</div>
						</div>
					</div>	<!-- /.box-body -->
				</div>
			</div>
<?php 
	}else {
		$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
		$param['surat_to_ref_data|jabatan'] = $surat_to_ref_data['jabatan'];
		$param['surat_to_ref_data|unit'] 	= humanize($surat_to_ref_data['unit']);
		$param['surat_to_ref_data|kode'] 	= $surat_to_ref_data['kode'];
		$param['surat_to_ref_data|nama'] 	= $surat_to_ref_data['nama'];
		$param['surat_to_ref_data|dir'] 	= $surat_to_ref_data['dir'];
?>
			<div class="row">
				<div class="col-md-6">
				<!-- Untuk Menuliskan  Distribusi Tujuan-->
				<h5>Tujuan Surat</h5>
					<div class="box-body">
						<div class="col-sm-9" style="font-weight:600;">
							<div class="input-group">
								<?php echo (isset($surat_to_ref_data['unit'])) ? $surat_to_ref_data['unit'] : ''; ?>
<!--							<input id="surat_to_unit" name="distribusi[tujuan][unit]" disabled="disabled" data-input-title="Unit Tujuan" value="<?php // echo (isset($surat_to_ref_data['unit'])) ? $surat_to_ref_data['unit'] : ''; ?>">
	 							<div id="surat_to_unit_kode" class="input-group-addon"><?php // echo (isset($surat_to_ref_data['kode'])) ? $surat_to_ref_data['kode'] : '________'; ?></div>	
								<input type="hidden" id="surat_to_ref" name="surat_to_ref" value="internal">
								<input type="hidden" id="surat_to_kode" name="distribusi[tujuan][kode]" value="<?php // echo (isset($surat_to_ref_data['kode'])) ? $surat_to_ref_data['kode'] : ''; ?>">
								<input type="hidden" id="surat_to_unit_id" name="surat_to_ref_id" value="<?php // echo $surat->surat_to_ref_id; ?>">
								<input type="hidden" id="surat_to_kode" name="distribusi[tujuan][jabatan]" value="<?php // echo (isset($surat_to_ref_data['jabatan'])) ? $surat_to_ref_data['jabatan'] : ''; ?>">
								<input type="hidden" id="surat_to_unit_id" name="distribusi[tujuan]['nama]" value="<?php // echo (isset($surat_to_ref_data['nama'])) ? $surat_to_ref_data['nama'] : ''; ?>">
								<input type="hidden" id="surat_to_kode" name="distribusi[tujuan][dir]" value="<?php // echo (isset($surat_to_ref_data['dir'])) ? $surat_to_ref_data['dir'] : ''; ?>">
-->
							</div>
						</div>
					</div> <!-- /.box-body -->
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
		foreach($list->result() as $row) {
?>
					<div class="checkbox <?php echo (isset($distribusi['direksi'][$row->organization_structure_id])) ? 'selected' : ''; ?>"><label><input type="checkbox" <?php echo (isset($distribusi['direksi'][$row->organization_structure_id]))? 'checked="checked"' : ''; ?> disabled="disabled"> <?php echo '( ' . $row->unit_code . ' ) ' . $row->jabatan . ' ' . $row->unit_name; ?></label></div>
<?php
		}
?>
				</div>
				<div class="col-md-6">
					<h5>Non Direksi</h5>
<?php 
		$list = $this->admin_model->get_non_direksi();
		$opt_pejabat = array();
		foreach ($list->result() as $row) {
			if (isset($distribusi['non_direksi'][$row->organization_structure_id])) {
				$opt_pejabat[$row->organization_structure_id] = '( ' . $row->unit_code . ' ) ' . $row->jabatan . ' ' . $row->unit_name; 
			}
		}
		
		foreach($opt_pejabat as $row) {
?>
					<div class="checkbox selected"><label><input type="checkbox" checked="checked" disabled="disabled"> <?php echo $row; ?></label></div>
<?php
		}
?>
				</div>
			</div>
<?php
// 		$list = $this->admin_model->get_direksi();
// 		foreach($list->result() as $row) {
			
		if($surat->status == 4 || $surat->status == 99) {
?>
			<hr>
			<div class="row">
				<div class="col-md-12">
<?php
			if(has_permission(13) && $surat->surat_to_ref_id == get_user_data('unit_id')) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/disposisi/create_from/surat/' . $surat->surat_id); ?>');">
						<i class="fa fa-exclamation-triangle"></i> Disposisi
					</button>
<?php
			}
?>
				</div>
			</div>
<?php
		}		
	//}
?>
		</div>
	</div>	
<?php 
	}
	
	if($surat->status == 2 || $surat->status == 5) {
		if($surat->status != 7 && $surat->status != 99) {
?>
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<span class="h3 box-title"> Ekspedisi</span>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div id="ekspedisi-area" class="box-body">
					<div class="row">
<?php
			if($surat->kirim_time == '' || $surat->baca_time != '') {
				if(has_permission(9)) {
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
					// buat setting penerima ekspedisi
					$penerima = json_decode($row->petugas_penerima, TRUE);
					$status_terima = array(-1 => 'Tolak', 0 => 'Pending', 1 => 'Diterima');
					
					if($row->status > 0) {
						if(isset($penerima[get_user_data('unit_id')])){
							$petugas= $penerima[get_user_data('unit_id')]['petugas'];
							$status_ekspedisi= $status_terima[$row->status];
						}else {
							$petugas= '-';
							$status_ekspedisi= $status_terima[$row->status - 1];
							if (has_permission(7))	{
								if (isset($penerima[$surat->surat_to_ref_id]))
								{
									$petugas= $penerima[$surat->surat_to_ref_id]['petugas'];
									$status_ekspedisi= $status_terima[$row->status];
								}
							}
						}
					}else {
						$petugas= '-';
						$status_ekspedisi = $status_terima[$row->status];
					}
?>						
					<dt><?php echo $row->pengiriman_time ?></dt>
							<dd>Pengirim : <strong><?php echo $row->petugas_pengirim; ?></strong> / Penerima : <strong><?php echo $petugas; ?></strong></dd>
							<dd>Status : <strong><?php echo $status_ekspedisi; ?></strong></dd>
						<!-- <dd>Pengantar : <a href="<?php echo site_url('surat/ekspedisi/sheet/' . $row->ekspedisi_id); ?>" target="_blank"><?php echo $row->ekspedisi_id; ?></a></dd>	-->		
						<br>		
<?php
					if (has_permission(7)) {
						$distribusi = json_decode($surat->distribusi, TRUE);
						//if (empty($distribusi)) { return; }
						foreach ($distribusi as $dis_key => $dis_val) {
							foreach ($dis_val as $k => $v) {
								if($row->status > 0) {
									if(isset($penerima[$v["unit_id"]])){
										$petugas= $penerima[$v["unit_id"]]['petugas'];
										$status_ekspedisi= $status_terima[$row->status];
									}else {
										$petugas= '-';
										$status_ekspedisi= $status_terima[$row->status - 1];
									}
								}else {
									$petugas= '-';
									$status_ekspedisi = $status_terima[$row->status];
								}
?>
							 	<dt><?php echo $row->pengiriman_time ?></dt>
								<dd>Pengirim : <strong><?php echo $row->petugas_pengirim; ?></strong> / Penerima : <strong><?php echo $petugas ?></strong></dd>
								<dd>Status : <strong><?php echo $status_ekspedisi; ?></strong></dd>
						<!-- 	<dd>Pengantar : <a href="<?php echo site_url('surat/ekspedisi/sheet/' . $row->ekspedisi_id); ?>" target="_blank"><?php echo $row->ekspedisi_id; ?></a></dd> -->
								<br>
 <?php								
							}
						}
					}
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
	}
	
	if($surat->status >= 7) {
		if ($surat->unit_archive_status != 99) {
?>
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
			echo form_hidden('action', 'surat.surat_model.update_arsip_surat'); 
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
<?php 		
		if($surat->status >= 6 && $surat->status != 99) {
			if(has_permission(7)) {
?>			
			<button type="submit" class="btn btn-app">
				<i class="fa fa-archive"></i> Simpan Arsip
			</button>
<?php
			}
		}
?>
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
			}else {
				$klasifikasi = new stdClass();
				$klasifikasi->kode_klasifikasi_arsip = '';
				$klasifikasi->nama_klasifikasi = '';
				$klasifikasi->nama_klasifikasi_sub = '';
				$klasifikasi->nama_klasifikasi_sub_sub = '';
			}
			
			echo form_hidden('action', 'surat.surat_model.update_arsip_surat'); 
?>
		<div class="box-body">
			<div class="form-group">
				<label for="no_surat" class="col-md-3 control-label">Kode</label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi" class="form-control required" data-input-title="Kode Klasifikasi Arsip" disabled="disabled" value="<?php echo $surat->kode_klasifikasi_arsip; ?>">
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
<?php 		
			if($surat->status > 6 && $surat->status != 99) {
				if(has_permission(7)) {
?>
			<button type="submit" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/external/register_arsip_sme/' . $surat->surat_id); ?>');">
				<i class="fa fa-archive"></i> Simpan Arsip
			</button>			
<?php
				}
			}
?>			
		</div>	
<?php
		}
?>
	</div>	
<?php 
		}
	}

	if($surat->unit_archive_status == 99) {
?>
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title"> Arsip</span>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
<?php 
		$result = $this->admin_model->get_klasifikasi_arsip(trim($surat->kode_klasifikasi_arsip));
		if($result->num_rows() > 0) {
			$klasifikasi = $result->row();
		}else {
			$klasifikasi = new stdClass();
			$klasifikasi->kode_klasifikasi_arsip   = '';
			$klasifikasi->nama_klasifikasi 		   = '';
			$klasifikasi->nama_klasifikasi_sub 	   = '';
			$klasifikasi->nama_klasifikasi_sub_sub = '';
		}
?>
		<div class="box-body">
			<div class="form-group">
				<label for="no_surat" class="col-md-3 control-label">Kode</label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi" class="form-control requiered" disabled="disabled" value="<?php echo trim($surat->kode_klasifikasi_arsip) . ' - ' . $klasifikasi->nama_klasifikasi; ?>">
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
	</div>			
<?php 
	}
		
	if($surat->status != 99) {
?>
	<div class="fixed-box-btn"></div>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4">
<?php 
		$enEdit = FALSE;
		if(!has_permission(1)) {
			if(editable_data($function_ref_id, get_role(), $surat->surat_id)) {
				$enEdit = TRUE;
			}
		}else {
			$enEdit = TRUE;
		}
		
		if($enEdit) {			
			//if(in_array($surat->status, array(0, 2)) || !has_permission(9))
			if(!has_permission(15)) {   // menghilangkan verifikasi kasubag TU
				if(in_array($surat->status, array(0, 1)) || has_permission($process->permission_handle)) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/external/incoming/' . $surat->surat_id); ?>');">
						<i class="fa fa-edit"></i> Edit
					</button>
<?php 	
				}
			}
				
			if($surat->status == 0 && $surat->created_id == get_user_data('user_id')) {
?>
				<!-- <button type="button" id="btnDelete" class="btn btn-app">
						<i class="fa fa-trash"></i> Hapus
					</button> -->
<?php 
			}
		}

		if($surat->status == 4 && $surat->surat_to_ref_id == get_user_data('unit_id')) {
			$disposisi_cek = $this->surat_model->cek_disposisi_sme($surat->surat_id);
			if($surat->sifat_surat == 'Rahasia' && $disposisi_cek == 0) {
?>
					<button type="button" id="btnSelesai" class="btn btn-app" onclick="selesaiData();">
						<i class="fa fa-caret-square-o-right"></i> Selesai
					</button>
<?php
			}
		}
?>
				</div>
					<div class="col-xs-8">
<?php
		if($surat->status != 4 && $surat->status != 6) {
			if(has_permission($process->permission_handle) && ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-')) {
				
			if($process->check_field == '-' || check_field_flow($process->check_field, array('surat_id' => $surat->surat_id)) ) {
				if ( $surat->surat_to_ref_id == get_user_data('unit_id')) {
					if($process->button_process != '-') {

						//	untuk menerima surat hilang ketika status_terima > 1
						if($surat->status == 3) { 
?>
							<button type="button" class="btn btn-app pull-right bg-green" onclick="terimaData();">
								<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
							</button>
<?php
						}else{	
?>							
							<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesData();">
								<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
							</button>
<?php
						}
					}
				}
			}
				
				if ($surat->surat_from_ref_id == get_user_data('unit_id') || $surat->surat_to_ref_id == get_user_data('unit_id')) { 
					if (($surat->status == 2 && $surat->created_id == get_user_data('user_id')) || $surat->status == 3) {
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
		}else {
			if(has_permission(9)) {
				if(has_permission($process->permission_handle) && ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-')) {
					if($process->check_field == '-' || check_field_flow($process->check_field, array('surat_id' => $surat->surat_id)) ) {
						if($process->button_process != '-') {	

							$cek_disposisi = $this->surat_model->cek_disposisi_sme($surat->surat_id);
							
							if ($surat->status == 4 && $cek_disposisi > 0) {
?>
							<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesData();">
								<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
							</button>
<?php 
							}else if ($surat->status != 4) {
?>
							<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesData();">
								<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
							</button>
<?php
							}
						}
					}
					
					if($surat->status < 4 && $process->button_return != '-') {
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
	}else {
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
</div>

<?php 
	if($surat->status != 99) {
?>

<script type="text/javascript">
	$(document).ready(function() {
	
		$('.select2').select2();
		
		$('#btnDelete').click(function() {
			if(confirm("Hapus surat.")) { 
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
					data: "surat_id=<?php echo $surat->surat_id ?>&action=surat.surat_model.delete_surat",
					success: function(data){	//alert(data);
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
		});
		
	}); // end document

	function initPage() {
	
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
						data: {	action: 'surat.surat_model.return_data', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								note: result, 
								last_flow: <?php echo $last_flow; ?>,
								function_ref_id: <?php echo $function_ref_id; ?>,
								flow_seq: <?php echo $surat->status; ?>
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
			'				<input type="file" name="attachment_file_' + attachmentRow + '" onchange="$(\'#flabel_' + attachmentRow + '\').html($(this).val()); $(\'#title_' + attachmentRow + '\').val(getFilename($(this).val()));">' +
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
	
	function prosesData() {
		$('#box-process-btn .overlay').removeClass('hide');

		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result) {
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.proses_data', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							note: result, 
							last_flow: <?php echo $last_flow; ?>,
							flow_seq: <?php echo ($surat->status == $last_flow) ? $last_flow - 1 : $last_flow; ?>,
							function_ref_id: <?php echo $function_ref_id; ?>,
							function_ref_name: 'Surat Masuk Eksternal',
							function_handler: '<?php echo $process->check_field_function; ?>'
							},
					success: function(data){
						if(typeof(data.error) != 'undefined') {
							eval(data.execute);
						} else {
							bootbox.alert("Data transfer error!");
							$('#box-process-btn .overlay').addClass('hide');
						}
					}
				});
			}else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}
	
	function terimaData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.prompt({
			title: 'Penerima Surat.', 
			inputType: 'textarea',
			callback: function(result){
				if(result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {	action: 'surat.surat_model.proses_data', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								penerima: result, 
								note: 'Surat Sudah diterima',
								last_flow: <?php echo $last_flow; ?>,
								flow_seq: 4,
								function_ref_id: <?php echo $function_ref_id; ?>,
								function_ref_name: 'Surat Masuk Eksternal',
								function_handler: '<?php echo $process->check_field_function; ?>'
							},
						success: function(data) {
							if(typeof(data.error) != 'undefined') {
								eval(data.execute);
							}else {
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
	
	function kirimData() {
		{
		bootbox.confirm('Kirim Surat Pengantar.', function (result) {
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.kirim_ekspedisi', 
					function_ref_id: '<?php echo $function_ref_id; ?>',
							
							title: '<?php echo $title; ?>',
							ref_id: '<?php echo $surat->surat_id; ?>',
							surat_ext_title: '<?php echo $surat_from_ref_data['title'];?>',
							surat_ext_nama:'<?php echo htmlspecialchars($surat_from_ref_data['nama'], ENT_QUOTES); ?>',
							surat_ext_instansi:'<?php echo $surat_from_ref_data['instansi']; ?>' 
							},
					success: function(data){
						if(typeof(data.error) != 'undefined') {
							eval(data.execute);
						} else {
							bootbox.alert("Data transfer error!");
						}
					}
				});
			}
		});
		}
	}

	function selesaiData() {
		{
		bootbox.confirm('Selesaikan Proses?', function (result) {
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.selesai_data', 
							function_ref_id: '<?php echo $function_ref_id; ?>',
							title: '<?php echo $title; ?>',
							surat_id: '<?php echo $surat->surat_id; ?>',
							},
					success: function(data){
						if(typeof(data.error) != 'undefined') {
							eval(data.execute);
						} else {
							bootbox.alert("Data transfer error!");
						}
					}
				});
			}
		});
		}
	}	

</script>
<?php 
	} else {
?>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select2').select2();
	});
/*
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
*/
</script>
<?php
	}
?>