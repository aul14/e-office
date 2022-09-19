<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/wysiwyg_view.css">

<!-- Main content -->
<section class="content">
<?php
	$param = (array) $surat;
		
	echo form_open_multipart('', ' id="form_surat_internal" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('action', 'surat.surat_model.update_surat'); 
	echo form_hidden('surat_id', $surat->surat_id);
//	echo form_hidden('surat_no', $surat->surat_no);
//	echo form_hidden('surat_tgl', $surat->surat_tgl);
	echo form_hidden('surat_awal', $surat->surat_awal);
	echo form_hidden('jenis_agenda', 'SI');
	echo form_hidden('function_ref_id', 3);
	echo form_hidden('function_ref_name', 'Surat Internal');
	echo form_hidden('return', 'surat/internal/sheet_view/'); 
	
	echo form_hidden('surat_from_ref_id', $surat->surat_from_ref_id);
	
	$result = $this->admin_model->get_ref_internal($surat->surat_from_ref_id);
	$unit = $result->row();
	
	echo form_hidden('official_code', $unit->official_code);
	echo form_hidden('surat_from_ref', $surat->surat_from_ref);
	echo form_hidden('surat_from_ref_data[unit]', $unit->value);
	echo form_hidden('surat_from_ref_data[kode]', $unit->unit_code);
	echo form_hidden('surat_from_ref_data[dir]', $unit->instansi);
	echo form_hidden('surat_from_ref_data[jabatan]', $unit->jabatan);
	echo form_hidden('surat_from_ref_data[pangkat]', $unit->pangkat);
	echo form_hidden('surat_from_ref_data[nama]', $unit->nama_pejabat);
	echo form_hidden('surat_from_ref_data[nip]', $unit->nip_pejabat);
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
						<th>Note</th>
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
	<div class="box">
		<div class="box-body">
			<div class="form-group">
<?php 
	if($surat->surat_no == '{surat_no}') {
		$param['surat_no'] = trim($surat->kode_klasifikasi_arsip) . '/_/NOTA DINAS';

		$month = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$param['tanggal'] = date('d');
		$param['tahun'] = date('Y');
		$bulan = date('m');
		switch ($bulan) {
			case '01':
				$param['bulan'] = $month[0];
				break;
			case '02':
				$param['bulan'] = $month[1];
				break;
			case '03':
				$param['bulan'] = $month[2];
				break;
			case '04':
				$param['bulan'] = $month[3];
				break;
			case '05':
				$param['bulan'] = $month[4];
				break;
			case '06':
				$param['bulan'] = $month[5];
				break;
			case '07':
				$param['bulan'] = $month[6];
				break;
			case '08':
				$param['bulan'] = $month[7];
				break;
			case '09':
				$param['bulan'] = $month[8];
				break;
			case '10':
				$param['bulan'] = $month[9];
				break;
			case '11':
				$param['bulan'] = $month[10];
				break;
			case '12':
				$param['bulan'] = $month[11];
				break;									
		}
	}
?>
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-6 col-sm-3">
					<input type="text" id="surat_no" <?php echo ($surat->status < 5) ? '' : 'name="surat_no"'; ?> class="form-control" readonly="readonly" value="<?php echo $param['surat_no']; ?>">
				</div>
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label">Tanggal Konsep Surat</label>
				<div class="col-lg-2 col-sm-3">
					<div class="input-group">
						<input type="text" id="surat_awal" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_awal); ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label">Tanggal Surat</label>
				<div class="col-lg-2 col-sm-3">
					<div class="input-group">
						<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo ($surat->surat_tgl != '') ? db_to_human($surat->surat_tgl) : ''; ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled" rows="3"><?php echo $surat->surat_perihal; ?></textarea>
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
?>
					<input type="text" id="format_surat_id" class="form-control" disabled="disabled" value="<?php echo $opt_format[$surat->format_surat_id]; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_item_lampiran" class="col-lg-2 col-sm-3 control-label">Lampiran</label>
				<div class="col-lg-3 col-sm-9">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
					<input type="text" id="surat_item_lampiran" class="form-control" disabled="disabled" value="<?php echo $surat->surat_item_lampiran . ' ' . $opt_unit_lpr[$surat->surat_unit_lampiran]; ?>">
				</div>
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sifat Surat</label>
				<div class="col-lg-5 col-sm-9">
<?php 
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	echo form_dropdown('sifat_surat', $opt_sifat_surat, $surat->sifat_surat, (' disabled="disabled" id="sifat_surat" class="form-control" '));
?>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->

	<div class="row">
		<div class="col-lg-6">
<?php 
	if($surat->surat_from_ref == '2') {
		$surat_to_ref_multi = $this->surat_model->get_tujuan_surat($surat->surat_to_ref_id);
		$surat_to_ref_multi_data = $surat_to_ref_multi->row();
		$surat_to_user_data = json_decode($surat_to_ref_multi_data->to_user_data, TRUE);
		if (isset($surat_to_user_data)) {
			foreach ($surat_to_user_data as $dt => $to_ref_data) {
				$param['surat_to_ref_data|jabatan'] = $to_ref_data['jabatan'];
				$param['surat_to_ref_data|unit'] 	= humanize($to_ref_data['unit']);
				$param['surat_to_ref_data|kode'] 	= (isset($to_ref_data['kode'])) ? $to_ref_data['kode'] : '';
				$param['surat_to_ref_data|pangkat'] = (isset($to_ref_data['pangkat'])) ? $to_ref_data['pangkat'] : '';
				$param['surat_to_ref_data|nama'] 	= $to_ref_data['nama'];
				$param['surat_to_ref_data|nip'] 	= (isset($to_ref_data['nip'])) ? $to_ref_data['nip'] : '';
				$param['surat_to_ref_data|dir'] 	= $to_ref_data['dir'];	
			}
		}
	}else {	
		$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
		$param['surat_to_ref_data|jabatan'] = $surat_to_ref_data['jabatan'];
		$param['surat_to_ref_data|unit'] 	= humanize($surat_to_ref_data['unit']);
		$param['surat_to_ref_data|kode'] 	= $surat_to_ref_data['kode'];
		$param['surat_to_ref_data|pangkat'] = (isset($surat_to_ref_data['pangkat'])) ? $surat_to_ref_data['pangkat'] : '';
		$param['surat_to_ref_data|nama']    = $surat_to_ref_data['nama'];
		$param['surat_to_ref_data|nip'] 	= (isset($surat_to_ref_data['nip'])) ? $surat_to_ref_data['nip'] : '';
		$param['surat_to_ref_data|dir'] 	= $surat_to_ref_data['dir'];
	}		
?>
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Tujuan Surat</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="list-tujuan" class="box-body" style="display: none;">
					<div class="form-group">
						<div class="col-sm-9 pull-right">
							<label class="col-sm-5">
								<input type="radio" id="opt_tujuan_1" name="opt_tujuan" value="1" disabled="disabled" <?php echo ($surat->surat_from_ref == '1' || $surat->surat_from_ref == '-') ? 'checked=checked' : ''; ?>> 1 Orang
							</label>
							<label class="col-sm-7">
								<input type="radio" id="opt_tujuan_2" name="opt_tujuan" value="2" disabled="disabled" <?php echo ($surat->surat_from_ref == '2') ? 'checked=checked' : ''; ?>> Lebih dari 1 Orang
							</label>
						</div>
					</div>
					<div id="multi_user_internal" style="<?php echo ($surat->surat_from_ref == '2') ? 'display: block' : 'display: none'; ?>">
						<div class="form-group">
							<div class="col-sm-12">								
								<input type="text" id="surat_to_ref_multi" name="surat_to_ref_multi" class="form-control" placeholder="Tujuan lebih dari 1 orang" data-input-title="Tujuan" value='<?php echo (isset($surat_to_ref_multi_data->title)) ? $surat_to_ref_multi_data->title : set_value('title'); ?>' disabled="disabled">
								<input type="hidden" id="surat_to_ref_detail" name="surat_to_ref_detail" class="form-control" value='<?php echo (isset($surat_to_ref_multi_data->to_user_data)) ? $surat_to_ref_multi_data->to_user_data : set_value('to_user_data'); ?>'>
								<input type="hidden" id="surat_to_ref_multi_id" name="surat_to_ref_multi_id" class="form-control" value='<?php echo (isset($surat_to_ref_multi_data->tujuan_surat_id)) ? $surat_to_ref_multi_data->tujuan_surat_id : set_value('tujuan_surat_id'); ?>'>
							</div>
						</div>	
					</div>
					<div id="user_internal" style="<?php echo ($surat->surat_from_ref == '1' || $surat->surat_from_ref == '-') ? 'display: block' : 'display: none'; ?>">
					<div class="form-group">
						<label for="surat_to_unit" class="col-sm-3 control-label">Unit <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" id="surat_to_unit" name="surat_to_ref_data[unit]" class="form-control required" data-input-title="Unit Tujuan" value="<?php echo (isset($surat_to_ref_data['unit'])) ? $surat_to_ref_data['unit'] : ''; ?>" placeholder="Bagian / Sub Bagian tujuan surat..." disabled="disabled">
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
							<input type="text" id="surat_to_unit" name="surat_to_ref_data[jabatan]" class="form-control required" data-input-title="Nama Jabatan" value="<?php echo (isset($surat_to_ref_data['jabatan'])) ? $surat_to_ref_data['jabatan'] : ''; ?>" disabled="disabled">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_nama" name="surat_to_ref_data[nama]" class="form-control" data-input-title="Nama Pejabat Tujuan" value="<?php echo (isset($surat_to_ref_data['nama'])) ? $surat_to_ref_data['nama'] : ''; ?>" placeholder="Nama Pejabat tujuan surat..." disabled="disabled">
							<input type="hidden" id="surat_to_pangkat" name="surat_to_ref_data[pangkat]" value="<?php echo (isset($surat_to_ref_data['pangkat'])) ? $surat_to_ref_data['pangkat'] : ''; ?>">
							<input type="hidden" id="surat_to_nip" name="surat_to_ref_data[nip]" value="<?php echo (isset($surat_to_ref_data['nip'])) ? $surat_to_ref_data['nip'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_dir" class="col-sm-3 control-label">Direktorat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_dir" name="surat_to_ref_data[dir]" class="form-control" readonly="readonly" data-input-title="Direktorat Tujuan" value="<?php echo (isset($surat_to_ref_data['dir'])) ? $surat_to_ref_data['dir'] : ''; ?>" placeholder="Direktorat tujuan surat..." disabled="disabled">
						</div>
					</div>
					</div>
				</div><!-- /.box-body -->
			</div>
		</div>
		<div class="col-lg-6">
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Klasifikasi Arsip</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body" style="display: none;">
					<div class="form-group">
						<label for="no_surat" class="col-md-3 control-label">Kode</label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="<?php echo (isset($surat->kode_klasifikasi_arsip)) ? trim($surat->kode_klasifikasi_arsip) . ' - ' . $klasifikasi->nama_klasifikasi : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="no_surat" class="col-md-3 control-label">Klasifikasi</label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="<?php echo (isset($klasifikasi->nama_klasifikasi)) ? $klasifikasi->nama_klasifikasi : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-md-3 control-label"></label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi_sub" class="form-control" disabled="disabled" value="<?php echo (isset($klasifikasi->nama_klasifikasi_sub)) ? $klasifikasi->nama_klasifikasi_sub : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-md-3 control-label"></label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi_sub_sub" class="form-control" disabled="disabled" value="<?php echo (isset($klasifikasi->nama_klasifikasi_sub_sub)) ? $klasifikasi->nama_klasifikasi_sub_sub : ''; ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Tembusan</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="tembusan_list" class="box-body" style="display: none;">
<?php 
	$i = 1;
	foreach (json_decode($surat->tembusan) as $tembusan) {
?>
					<div id="row_tembusan_<?php echo $i; ?>" class="form-group">
						<div class="col-sm-12">
							<input type="text" id="tembusan_<?php echo $i; ?>" name="tembusan[<?php echo $i; ?>]" class="form-control" data-input-title="Tembusan <?php echo $i; ?>" value="<?php echo $tembusan; ?>" disabled="disabled">
						</div>
					</div>
<?php 
	}
?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
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
		</div>
	</div>
	
	<!-- Default box -->
	<div id="box-konsep" class="box box-primary">
		<div id="all-konsep" class="hide">
<?php 
	$opt_konsep = array(0 => '--');
	$active_konsep = '';
	$konsep_text = '';
	if($konsep->num_rows() > 0) {
		$opt_konsep = array();
		foreach($konsep->result() as $row) {
			$opt_konsep[$row->konsep_surat_id] = $row->title . ' - Versi ' . $row->version;
			if($row->status == 1) {
				$active_konsep = $row->konsep_surat_id;
			}
?>
			<div id="konsep_<?php echo $row->konsep_surat_id; ?>" data-version="<?php echo $row->version; ?>" class="<?php echo ($row->status == 1) ? 'active ' : ''; ?>"><?php echo $row->konsep_text; ?></div>
<?php 
		}
		
		$konsep_text = '';
	}else {
		$result = $this->admin_model->get_template_surat($function_ref_id, $surat->format_surat_id);
		
		if($result->num_rows() > 0) {
			$template = $result->row();
		} else {
			$template = new stdClass();
			$template->format_title = '';
			$template->format_text  = '';
		}
		
		$konsep_text = sprintformat($template->format_text, $param);
	//	$konsep_text = sprintformat($template->format_text, $surat->surat_ext_nama, $surat->surat_ext_title, humanize($surat->surat_int_unit), $surat->surat_int_jabatan, humanize($surat->surat_int_unit), $surat->surat_int_nama, '');
	}
?>
		</div>
		<div class="box-header with-border">
			<h3 class="box-title"> Konsep Surat Nota Dinas </h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<form action="" class="form-horizontal" onsubmit="return false;">
				<div class="form-group">
					<label for="surat_ext_alamat" class="col-sm-2 control-label">Versi Konsep</label>
					<div class="col-sm-4">
<?php 
		echo form_dropdown('konsep_surat_id', $opt_konsep, $active_konsep, (' id="konsep_surat_id" class="form-control" onchange="viewKonsep($(this).val());" '));
?>
					</div>
					<div class="col-sm-6"></div>
				</div>
				<div class="form-group">
					<div id="konsep_text" class="col-md-12">
						<!--<textarea name="konsep_text" ><?php echo $konsep_text; ?></textarea>-->
					</div>
				</div>
			</form>
		</div>
	</div>	
<?php 
	$approval = json_decode($surat->approval, TRUE);
	$obj_approval = json_decode($surat->approval);
	
	$approval_status = 0;
	
	if(($surat->status > 0) && (isset($approval['direksi']))) {
		$non_dir = array($surat->created_id);
		foreach ($approval['direksi'] as $ak => $appr) {
			if(isset( $appr['unit_name'])) {
				$non_dir[] = $appr['user_id'];
			}
		}
?>
	<div id="box-comment-draft" class="box box-primary  <?php echo $surat->status == 1 ? '' : 'collapsed-box'; ?>">
		<div class="box-header with-border">
			<h3 class="box-title"> Approval Draft </h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa <?php echo $surat->status == 1 ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-6">
<?php 
		if(!isset($approval['direksi']['diskusi'])) {
			$obj_approval->direksi->diskusi = new stdClass();
		}

		$this->load->view('diskusi', array('id' => 'direksi', 'function_handle' => 'surat.surat_model.set_diskusi', 'script_handle' => 'draft', 'ref_id' => $surat->surat_id, 'diskusi' => $obj_approval->direksi->diskusi, 'active' => ($approval['direksi']['status'] == 0 && in_array(get_user_id(), $non_dir))));
?>				
				</div>
				<div class="col-md-6">
<?php 
		foreach ($approval['direksi'] as $ak => $appr) {
			if(isset( $appr['unit_name'])) {
				$approval_status += $appr['status'];
?>					
					<div class="form-group">
						<label id="pejabat_<?php $ak; ?>" class="col-md-12">
							<input type="checkbox" value="1" <?php echo ($appr['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 1) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'direksi\', ' . $ak. ')"'; ?>> 
							<?php echo $appr['jabatan'] . ' ' . $appr['unit_name']; ?>
						</label>
					</div>
<?php 
			}
		}
?>
				</div>
			</div>		
		</div>
	</div>
<?php 
	}
	
	if($surat->status >= 4) {
		if($surat->status != 99) {
?>
	<div class="box box-primary">
	<!-- Default box -->
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
				if(has_permission($process->permission_handle)) {
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
	}
	
/*	
//	echo $surat->status;
	if($surat->status != 99) {
		if($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-') {
?>	
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4">
<?php 
			$enEdit = FALSE;
			if(!has_permission(1)) {
				if($surat->status != 99) {
					if((get_role() == 5 && $surat->status <= 1) || (get_role() == 3 && $surat->status == 3)) {
						$enEdit = TRUE;
					}
				}
			} else {
				$enEdit = TRUE;
			}
			
			if($enEdit) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/internal/sheet/' . $surat->surat_id); ?>');">
						<i class="fa fa-edit"></i> Edit
					</button>
<?php 	
			}
			
			if($surat->status > 1) {
?>
					<button type="button" class="btn btn-app" onclick="printSurat();">
						<i class="fa fa-print"></i> Cetak
					</button>
<?php 
			}
			
			if($surat->status == 2 && $surat->surat_no == '{surat_no}' && in_array(get_role(), array(3, 5))) {
?>
					<button id="btn-set-no" type="button" class="btn btn-app" onclick="generateNomor();">
						<i class="fa fa-keyboard-o"></i> Set Nomor
					</button>
<?php 
			}

			if(has_permission(13) && ($surat->status >= 4)) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/disposisi/create_from/surat/' . $surat->surat_id); ?>');">
						<i class="fa fa-exclamation-triangle"></i> Disposisi
					</button>
<?php 
			}
?>
				</div>
				
				<div class="col-xs-8">
<?php 

			if(has_permission($process->permission_handle)) {
//		if($process->role_handle == get_role() && ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-')) {
				if($process->button_process != '-') {
					
					$approved = FALSE;
					
					if($surat->status == 2 && (isset($approval['direksi']) && $approval['direksi']['status'] == 1)) {
						$approved = TRUE;
					}
					
					if($surat->status == 3 && ($surat->surat_no != '{surat_no}')) {
						$approved = TRUE;
					}
					if(in_array($surat->status, array(4,5))) {
						$approved = TRUE;
					}
				
?>
					<button id="btn-process" type="button" class="btn btn-app pull-right bg-green <?php echo (!$approved) ? 'hide' : ''; ?>" onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
				}
			
				if($surat->status <= 6) {
//				$result = $this->disposisi_model->get_disposisi_from_ref('surat', $surat->surat_id);
// 				var_dump($result);
//				if($process->button_return != '-' && ($result->num_rows() == 0)) {
					if($process->button_return != '-') {
?>
					<button id="btn-return" type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
						<i class="fa fa-caret-square-o-left"></i> <?php echo $process->button_return; ?>
					</button>
<?php 
					}
				}
			}
	} else {
		if($surat->unit_archive_status != 99) {
?>
					<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesArsipUnit();">
						<i class="fa fa-caret-square-o-right"></i> Simpan Sebagai Arsip
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
	} else {
		if(get_user_data('unit_id') == $surat->surat_to_ref_id && $surat->unit_archive_status != 99) {
?>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-6">
<?php
		if(has_permission(13) && ($surat->status >= 4)) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/disposisi/create_from/surat/' . $surat->surat_id); ?>');">
						<i class="fa fa-exclamation-triangle"></i> Disposisi
					</button>
<?php 
		}
?>
				</div>
				
				<div class="col-xs-6">
					<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesArsipUnit();">
						<i class="fa fa-caret-square-o-right"></i> Simpan Sebagai Arsip
					</button>
				</div>
			</div>
		</div>
	</div>
<?php 
		}

	}
*/
	if($surat->status != 99) {
?>
	<div class="fixed-box-btn"></div>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4">
<?php 
		if((has_permission($process->permission_handle)) && ($process->modify == 1)) {
		//if ($approval_status == 0) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/internal/sheet/' . $surat->surat_id); ?>');">
						<i class="fa fa-edit"></i> Edit
					</button>
<?php 	
			//}
		}
		 
		if($surat->status >= 1 && has_permission(18)) {
?>
					<button type="button" class="btn btn-app" onclick="printSurat();">
						<i class="fa fa-print"></i> Cetak
					</button>
<?php 
		}
		
		if($surat->status == 3 && $surat->surat_no == '{surat_no}' && (has_permission($process->permission_handle))) {
?>
					<button id="btn-set-no" type="button" class="btn btn-app" onclick="generateNomor();">
						<i class="fa fa-keyboard-o"></i> Set Nomor
					</button>
<?php 
		}
		
		if(get_user_data('unit_id') == $surat->surat_to_ref_id && ($surat->status >= 6)) {
?>
				<!--
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/disposisi/create_from/surat/' . $surat->surat_id); ?>');">
						<i class="fa fa-exclamation-triangle"></i> Disposisi
					</button>
				-->	
<?php 
		}
?>
				</div>
				
				<div class="col-xs-8">
<?php 	
		if(has_permission($process->permission_handle) && $process->position_handle == 0) {	
			if($process->button_process != '-') {
				
				$signed = json_decode($surat->signed, TRUE);
				if($signed['jabatan'] == 'Direktur') {
					$level = 'L0';
				}else {
					$level = 'L1';
				}

				$approved = FALSE;
				if($level == 'L0'){
					if($surat->status == 1 && $approval_status >= 2) {
						$approved = TRUE;
					}
				}else {
					if($surat->status == 1 && $approval_status >= 1) {
						$approved = TRUE;
					}
				}

				if($surat->status == 1 && (in_array($surat->surat_from_ref_id, array(1, 2, 10)))) {
					$approved = TRUE;
				}

				if($surat->status == 2 && ($surat->surat_from_ref_id == get_user_data('unit_id') || has_permission(7))) {
					$approved = TRUE;
				}
				
				if($surat->status == 4 && ($surat->surat_no != '{surat_no}')) {
					$approved = TRUE;
				}

				if ($surat->status == 3 && $surat->surat_no != '{surat_no}') {	
					$approved = TRUE;
				}
				
				if ($surat->surat_to_ref_id == get_user_data('unit_id') && $surat->status == 5){
					$approved = TRUE;	
				}
				
				if ($surat->status == 6 && ($surat->surat_from_ref_id == get_user_data('unit_id'))) {
					$approved = TRUE;
				}
?>
					<button id="btn-process" type="button" class="btn btn-app pull-right bg-green <?php echo ($approved) ? '' : 'hide'; ?>" onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
			}
		}

			//if($surat->status < 6 && (get_role() == 2 || get_role() == 3 || get_role() == 4 || get_role() == 5 || get_role() == 6)) {
			if($surat->status < 6 && has_permission($process->permission_handle)) {	
				if($process->button_return != '-') {
?>
					<button id="btn-return" type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
						<i class="fa fa-caret-square-o-left"></i> <?php echo $process->button_return; ?>
					</button>
<?php 
				}
			}
			
			//if($surat->surat_from_ref_id == get_user_data('unit_id')) {
			if(has_permission(7)) {
				if (($surat->status >= 7 && $surat->unit_archive_status != 99) || ($surat->status == 5 && $surat->surat_from_ref == '2')) {	
?>				
					<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesArsipUnit();">
						<i class="fa fa-caret-square-o-right"></i> Simpan Sebagai Arsip
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
	} else {
?>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4">
<?php 
		if(get_user_data('unit_id') == $surat->surat_to_ref_id) {
?>
					<!-- <button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/disposisi/create_from/surat/' . $surat->surat_id); ?>');">
						<i class="fa fa-exclamation-triangle"></i> Disposisi
					</button> -->
<?php 
		}
?>
				</div>
				<div class="col-xs-8">
<?php 
		if ($surat->status != 99) {
		//if(get_user_data('unit_id') == $surat->surat_to_ref_id && $surat->status != 99) {
?>
					<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesArsipUnit();">
						<i class="fa fa-caret-square-o-right"></i> Simpan Sebagai Arsip
					</button>
<?php 
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
	
	echo form_close();
?>				
</section><!-- /.content -->

<script type="text/javascript">
	$(document).ready(function() {
		//$('#konsep_text').html( $('#konsep_' + $('#konsep_surat_id').val()).html() );
		viewKonsep($('#konsep_surat_id').val());
	});

	// function initPage() {
		// $('#konsep_text').html( $('#konsep_' + $('#konsep_surat_id').val()).html() );
	// }
	
	function viewKonsep(kid) {
		$('#konsep_text').html($('#konsep_' + kid).html());
	}

<?php 
	if ($surat->status != 99) {
?>
	function setApproved(e, cid, uid) {
		var ap = e.is(':checked') ? 1 : 0;
		$.ajax({
			type: "POST",
			url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
			data: {action: 'surat.surat_model.set_approve', 
					ref_id: '<?php echo $surat->surat_id; ?>', 
					function_ref_id: '<?php echo 3; ?>',
					distribusi_id: cid, 
					unit_id: uid,
					approval: ap
				},
			
			success: function(data) {
				if(typeof(data.error) != 'undefined') {
					if(data.error == 0) {
						bootbox.alert(data.message, function(){ document.location.reload(); });
					} else {
						bootbox.alert(data.message);
					}
				} else {
					bootbox.alert("Data transfer error!");
				}
			}
		});
		
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
<?php 
/*
	if($process->check_field_function != '-') {
?>
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result){
			if(result) {
				location.assign('<?php echo site_url($process->check_field_function . '/' . $surat->surat_id); ?>');	 
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});

<?php 
	} else {
*/
?>
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result){
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.proses_data', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							note: result, 
							last_flow: <?php echo $last_flow; ?>,
							function_ref_id: <?php echo 3; ?>,
							function_ref_name: 'Surat Internal',
							flow_seq: <?php echo ($surat->status == 5 || $surat->status == 6) ? $surat->status + 1 : $surat->status; ?>,
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
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}
<?php 
//	}
?>
	
<?php 
		if($surat->status >= 1) {
?>
	function printSurat() {
		window.open('<?php echo site_url('surat/internal/cetak_surat/' . $surat->surat_id); ?>');
	}
<?php 
		}
		
		if($surat->status == 3 && $surat->surat_no == '{surat_no}' && has_permission($process->permission_handle)) {
?>

	function generateNomor() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Buat Nomor Surat?', function(result){
			if(result) {
				$.ajax({
		 			type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
		 			data: {action: 'surat.surat_model.get_current_internal_no', 
							ref_id: '<?php echo $surat->surat_id; ?>',
							function_ref_id: <?php echo $function_ref_id; ?>
		 					},
		 			success: function(data){
		 				if(typeof(data.error) != 'undefined') {
		 					$('#surat_no').val(data.surat_no);
		 					$('#surat_tgl').val(data.surat_tgl);
		 					$('#btn-process').removeClass('hide');
		 					$('#btn-set-no').addClass('hide');
		 					
		 					bootbox.alert(data.message);
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

<?php 
		}
	} 
?>
	function prosesArsipUnit() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Simpan sebagai Arsip?', function(result){
			if(result) {
				location.assign('<?php echo site_url('surat/internal/register_arsip/' . $surat->surat_id); ?>');	 
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}

</script>