<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/wysiwyg_view.css">

<?php 
	list($agenda_date, $agenda_time) = explode(' ', $surat->created_time);
	list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
	$agenda_date = db_to_human($agenda_date);
?>

<!-- Content Header (Page header) -->
<!--
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li class="active"><a href="#">Tugas</a></li>
	</ol>
</section>
-->

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
	$process_approved_L2 = TRUE;
	$process_approved_L1 = TRUE;
	$process_approved_TU = TRUE;
	$param = (array) $surat;
	
	$approval = json_decode($surat->approval, TRUE);
	echo form_open_multipart('', ' id="form_kontrak" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('function_ref_id', $function_ref_id); 
	echo form_hidden('function_ref_name', 'Tugas'); 
	echo form_hidden('surat_id', $surat->surat_id);
//	echo form_hidden('surat_awal', db_to_human($surat->surat_awal));
	echo form_hidden('jenis_agenda', $surat->jenis_agenda);
	echo form_hidden('surat_to_ref_id', $surat->surat_to_ref_id);
	echo form_hidden('surat_from_ref_id', $surat->surat_from_ref_id);
	echo form_hidden('surat_from_ref', 'surat');
	
	echo form_hidden('surat_int_unit', get_user_data('unit_name'));
	echo form_hidden('surat_int_unit_id', get_user_data('unit_id'));
	echo form_hidden('surat_int_kode', get_user_data('unit_code'));
		
	$result = $this->admin_model->get_ref_internal($surat->surat_from_ref_id);
	$unit = $result->row();
	
	echo form_hidden('surat_int_dir', $unit->instansi);
	echo form_hidden('surat_int_jabatan', $unit->jabatan);
	echo form_hidden('surat_int_pangkat', $unit->pangkat);
	echo form_hidden('surat_int_nama', $unit->nama_pejabat);
	echo form_hidden('surat_int_nip', $unit->nip_pejabat);

	$result_surat_ref = $this->admin_model->get_ref_surat_masuk($surat->surat_id);
	$surat_ref_num = $result_surat_ref->num_rows();
	
	if ($surat_ref_num > 0) {
		$surat_from_ref = $result_surat_ref->row();
	}else {
		$surat_from_ref = '';
	}
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
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Identitas Surat</h3>
		</div>

		<div class="box-body">
<?php 
	if($surat->surat_no == '{surat_no}') {
		$param['surat_no'] = trim($surat->kode_klasifikasi_arsip) . '/_/SURAT PERINTAH';

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
	
	if($surat->status < 5) {
		echo form_hidden('surat_no', $surat->surat_no);
	}
?>				
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
<?php
	if ($surat_from_ref != '') {
		$surat_from_ref_data = json_decode($surat_from_ref->surat_from_ref_data, TRUE);
?>
			<div class="form-group">
				<label for="asal_surat" class="col-lg-2 col-sm-3 control-label">Asal Surat</label>
				<div class="col-lg-10 col-sm-9">
					<input type="text" id="asal_surat" name="asal_surat" class="form-control" readonly="readonly" value="<?php echo $surat_from_ref_data['nama'] . ' ' . $surat_from_ref_data['title'] . ', ' . $surat_from_ref_data['instansi']; ?>">
				</div>
			</div>
			<div class="form-group">	
				<label for="surat_no_ref" class="col-lg-2 col-sm-3 control-label">Nomor Surat Ref</label>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_no_ref" name="surat_no_ref" class="form-control" readonly="readonly" value="<?php echo $surat_from_ref->surat_no; ?>">
				</div>
				<label for="surat_tgl_ref" class="col-lg-2 col-sm-3 control-label">Tanggal Surat Ref</label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl_ref" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat_from_ref->surat_tgl); ?>">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
<?php
	}
?>
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_no" <?php echo ($surat->status < 5) ? '' : 'name="surat_no"'; ?> class="form-control" disabled="disabled" data-input-title="Nomor Surat" value="<?php echo $param['surat_no']; ?>" >
				</div>
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Surat <br> (dd-mm-yyyy)</label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl" name="surat_tgl" class="form-control datemulaipicker required"  disabled="disabled" data-input-title="Tgl Terima" value="<?php echo ($surat->surat_tgl != '') ? db_to_human($surat->surat_tgl) : ''; ?>">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i> 
							<?php echo (($surat->status >= 4 && $surat->status < 6) && $surat->surat_no != '{surat_no}') ? ' &nbsp;<button type="button" onclick="ubahTglSurat();">edit</button>' : ''; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Konsep Surat <br> (dd-mm-yyyy)</label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_awal" name="surat_tgl" class="form-control datemulaipicker required"  disabled="disabled" data-input-title="Tgl Konsep Surat" value="<?php echo ($surat->surat_awal != '') ? db_to_human($surat->surat_awal) : ''; ?>">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled" rows="3" ><?php echo $surat->surat_perihal; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_format" class="col-lg-2 col-sm-3 control-label">Format Template</label>
				<div class="col-lg-10 col-sm-9">
<?php 
	$list = $this->admin_model->get_template_surat(13);
	$opt_format = array('' => '--');
	foreach ($list->result() as $row) {
		$opt_format[$row->format_surat_id] = $row->format_title;
	}
?>
					<input type="text" id="format_surat_id" class="form-control" disabled="disabled" value="<?php echo $opt_format[$surat->format_surat_id]; ?>">
				</div>
			</div>			
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Menugaskan Kepada</h3>
			</div>
		<div id="list-penerima" class="box-body">
<?php
	$a = 0;
	$last_ttd=  0;
	$count = array();
	$tanda_tangan = json_decode($surat->distribusi, TRUE);
	
	if(isset($tanda_tangan)) {
		foreach($tanda_tangan as $signed) {
?>
			<fieldset id="row_penerima_<?php echo $a; ?>" style="position: relative;">
				<legend></legend>
				<div class="form-group">
					<label for="surat_to_unit_<?php echo $a; ?>" class="col-lg-2 col-sm-3 control-label">Nama Unit Kerja</label>
					<div class="col-lg-10 col-sm-9">
						<input id="surat_to_unit_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][nama_unitkerja]" disabled="disabled" class="form-control required" placeholder="Nama Unit Kerja Penerima Tugas..." value="<?php echo $signed ['nama_unitkerja']; ?>" data-input-title="Nama Unit Kerja Penerima Tugas">
					</div>
				</div>
				<div class="form-group">
					<label for="surat_to_nama_<?php echo $a; ?>" class="col-sm-2 control-label">Nama</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nama_<?php echo $a; ?>" disabled="disabled" name="distribusi[<?php echo $a; ?>][nama]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="<?php echo $signed ['nama']; ?>" placeholder="Nama Pejabat Penandatangan surat...">
					</div>
					<label for="surat_to_nip_<?php echo $a; ?>" class="col-sm-2 control-label">NIP</label>
					<div class="col-sm-3">
						<input type="text" id="surat_to_int_nip_<?php echo $a; ?>" disabled="disabled" name="distribusi[<?php echo $a; ?>][nip]" class="form-control" data-input-title="NIP Pejabat Penerima Tugas" value="<?php echo $signed ['nip']; ?>" placeholder="NIP Pejabat Penandatangan surat...">
					</div>
				</div>
				<div class="form-group">
					<label for="surat_to_nama_<?php echo $a; ?>" class="col-sm-2 control-label">Jabatan</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_jabatan_<?php echo $a; ?>" disabled="disabled" name="distribusi[<?php echo $a; ?>][jabatan]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="<?php echo $signed ['jabatan']; ?>" placeholder="Jabatan Penandatangan surat...">
					</div>
					<label for="surat_to_nip_0" class="col-sm-2 control-label">Pangkat / Golongan</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_pangkat_<?php echo $a; ?>" disabled="disabled" name="distribusi[<?php echo $a; ?>][pangkat]" class="form-control" data-input-title="Pangkat Penerima Tugas" value="<?php echo $signed ['pangkat']; ?>" placeholder="Pangkat Penandatangan surat...">
					</div>
				</div>
				<div class="form-group">
					<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Keperluan</label>
					<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" name="distribusi[<?php echo $a; ?>][keperluan]" disabled="disabled" class="form-control required" rows="3" placeholder="Perihal" data-input-title="Perihal" ><?php echo $signed ['keperluan']; ?></textarea>
					</div>
				</div>
			</fieldset>		
<?php 
			$a++;
			$last_ttd = $a;
		}
?>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
<?php		
	}
?>		
	<!-- Default box -->
	<div class="box box-primary collapsed-box">
		<div class="box-header with-border">
			<h3 class="box-title">Klasifikasi Arsip</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>
<?php
	if ($surat->status != 99) {
?>
		<div class="box-body" style="display: none;">
<?php
	}else {
?>		
		<div class="box-body" style="display: block;">
<?php 
	} 
?>
			<div class="form-group">
				<label for="no_surat" class="col-md-3 control-label">Kode</label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="<?php echo trim($surat->kode_klasifikasi_arsip) . ' - ' . $klasifikasi->nama_klasifikasi; ?>">
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

	<!-- Default box -->
	<div class="box box-primary collapsed-box">
		<div class="box-header with-border">
			<h3 class="box-title">Penanda Tangan</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="list-tembusan" class="box-body">
			<div id="row_sign" class="form-group">
				<div class="col-sm-12">
<?php
	$p = 1;
	$list = $this->surat_model->get_all_parents_st($surat->surat_from_ref_id);
	$list2 = $this->surat_model->get_all_parents($surat->surat_from_ref_id);

	$opt_sign = array();
	$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	// if($unit->ske_sign == 1) {
	// 	$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	// }
	
	$approval_type = (in_array($unit->level, array('L0', 'L1'))) ? 'direksi' : 'direksi';
	
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][index]', $p++);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][unit_name]', $unit->value);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][user_id]', $unit->user_id);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nip]', $unit->nip_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][jabatan]', $unit->jabatan);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nama_pejabat]', $unit->nama_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][email]', $unit->email);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][status]', 0);
	echo form_hidden('approval[' . $approval_type. '][diskusi]', array());
	echo form_hidden('approval[' . $approval_type. '][status]', 0);
	
	foreach ($list2 as $parent2) {
		$appr_unit[] = $unit->value;
		$appr_unit[] = $parent2['unit_name'];
	}

	foreach ($list as $parent) {
		$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
		// if($parent['ske_sign'] == 1) {
		// 	$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
		// }
		
		/* if($parent['sub_id'] != 0) {
			$result = $this->admin_model->get_ref_internal($parent['sub_id']);
			$sub = $result->row();
			
			$approval_type = (in_array($sub->level, array('L0', 'L1'))) ? 'direksi' : 'non_direksi';
			
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][index]', $p++);
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][unit_name]', $sub->value);
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][user_id]', $sub->user_id);
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][nip]', $sub->nip_pejabat);
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][jabatan]', $unit->jabatan);
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][nama_pejabat]', $sub->nama_pejabat);
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][email]', $sub->email);
			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][status]', 0);
		} */
		
		$approval_type = (in_array($parent['level'], array('L0', 'L1'))) ? 'direksi' : 'direksi';
		
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][index]', $p++);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][unit_name]', $parent['unit_name']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][user_id]', $parent['user_id']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][nip]', $parent['nip']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][jabatan]', $parent['jabatan']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][nama_pejabat]', $parent['user_name']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][email]', $parent['email']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][status]', 0);
		echo form_hidden('approval[' . $approval_type. '][diskusi]', array());
		echo form_hidden('approval[' . $approval_type. '][status]', 0);
	}
	
	$signed = json_decode($surat->signed, TRUE);
	$param['signed|unit_id'] = $signed['unit_id'];
	$param['signed|unit_name'] = $signed['unit_name'];
	$param['signed|jabatan'] = $signed['jabatan'];
	$param['signed|nama_pejabat'] = $signed['nama_pejabat'];
	$param['signed|nip'] = $signed['nip'];

	echo form_dropdown('signed', $opt_sign, $signed['unit_id'], ' class="form-control" disabled="disabled"');
?>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->

<?php
	if($surat->status != 99) {
?>
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
	
	} else {
		$result = $this->admin_model->get_template_surat(13, $surat->format_surat_id);
		
		if($result->num_rows() > 0) {
			$template = $result->row();
		} else {
			$template = new stdClass();
			$template->format_title = '';
			$template->format_text = '';
		}
		
		$konsep_text = sprintformat($template->format_text, $param);
//		$konsep_text = sprintformat($template->format_text, $surat->surat_ext_nama, $surat->surat_ext_title, humanize($surat->surat_int_unit), $surat->surat_int_jabatan, humanize($surat->surat_int_unit), $surat->surat_int_nama, '');
	}
?>
		</div>
		<div class="box-header with-border">
			<h3 class="box-title"> Konsep Surat Perintah </h3>
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
				</div>
			</div>
			</form>
		</div>
	</div>		
<?php
	}

	$approval = json_decode($surat->approval, TRUE);
	$obj_approval = json_decode($surat->approval);

	$approved_non_direksi = 0;
	$approved_direksi = 0;
	$approved_status = 0;
	$approved_jabatan = '';

	if($surat->status > 1) {	
?>
	<div id="box-comment-draft" class="box box-primary  <?php echo $surat->status == 2 ? '' : 'collapsed-box'; ?>">
		<div class="box-header with-border">
			<h3 class="box-title">Verifikasi Administrasi </h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa <?php echo $surat->status == 2 ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="col-md-6">
<?php 
	if( ($surat->status > 1) && (isset($approval['non_direksi'])) ) {
		$non_dir = array($surat->created_id);
		foreach ($approval['non_direksi'] as $ak => $appr) {
			if(isset( $appr['unit_name'])) {
				$non_dir[] = $appr['user_id'];
			}
		}

		foreach ($approval['non_direksi'] as $ak => $appr) {
			if(isset($appr['unit_name']) && in_array($appr['unit_name'], $appr_unit)) {
				$approved_status += $appr['status'];
?>					
				<div class="form-group">
					<label id="pejabat_<?php $ak; ?>" class="col-md-12">
						<input type="checkbox" value="1" <?php echo ($appr['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 2) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'non_direksi\', ' . $ak. ')"'; ?>> 
						<?php echo $appr['jabatan'] . ' ' . $appr['unit_name']; ?>
					</label>
				</div>
<?php 
			}
		}
	}
	
	if(($surat->status > 1) && (isset($approval['direksi']))) {
		$dir = array($surat->created_id);
		foreach ($approval['direksi'] as $akdir => $apprdir) {
			if(isset($apprdir['unit_name'])) {
				$dir[] = $apprdir['user_id'];
			}
		}
		
		foreach ($approval['direksi'] as $akdir => $apprdir) {
			if(isset($apprdir['unit_name']) && in_array($apprdir['unit_name'], $appr_unit)) {
				$approved_status += $apprdir['status'];
				$approved_jabatan = $apprdir['jabatan'];
?>
				<div class="form-group">
					<label id="pejabat_<?php $akdir; ?>" class="col-md-12">
						<input type="checkbox" value="1" <?php echo ($apprdir['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($apprdir['user_id'] != get_user_id() || $surat->status > 2) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'direksi\', ' . $akdir. ')"'; ?>> 
						<?php echo $apprdir['jabatan'] . ' ' . $apprdir['unit_name']; ?>
					</label>
				</div>
<?php 
			}
		}
	}	
?>
			</div>
		</div>
	</div>
<?php 	
	}

	if($surat->status >= 5) {
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
			} else {
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
?>

<?php
	if($surat->status == 7 && has_permission(24)) {
		echo form_hidden('action', 'surat.tugas_model.update_lampiran');
?>	
	<!-- Default box -->
 	<div class="box box-primary"> 
 		<div class="box-header with-border"> 
 			<span class="h3 box-title">lampiran </span> <span class="small">Max. 2MB (*.pdf, *.jpg, *.jpeg, *.png, *.zip, *.rar)</span> 
 			<div class="box-tools pull-right"> 
			 <button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button> 
 			</div> 
 		</div> 
 		<div id="attachment_list" class="box-body">
<?php 
	if(isset($attachment)) {
		$last_seq = 0;
		foreach ($attachment as $row) {
?>
			<div id="attachment_<?php echo $row->sort; ?>" class="col-md-12">
				<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> <label> <?php echo $row->title; ?> </label>
			</div>
<?php
			$last_seq = $row->sort;
		}
	}else {
?> 		 
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
<?php
	}
?> 			 
 		</div> 
 	</div> 

<?php
		echo form_close();
	}
?>			

	<!-- <div class="fixed-box-btn"></div> -->
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4">
<?php 
	$enEdit = FALSE;
	if(!has_permission(1)) {
		if($surat->status != 99) {
//			if((get_role() == 5 && $surat->status <= 2) || (get_role() == 3 && $surat->status == 3)) {
			if(has_permission($process->permission_handle) && $process->modify == 1) {
				$enEdit = TRUE;
			}
		}
	} else {
		$enEdit = TRUE;
	}
	
	if($enEdit) {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/tugas/tugas/' . $surat->surat_id); ?>');">
						<i class="fa fa-edit"></i> Edit
					</button>
<?php 	
	}
	if($surat->status >= 1 && $surat->status < 99) {
?>
					<button type="button" class="btn btn-app" onclick="printSurat();">
						<i class="fa fa-print"></i> Cetak
					</button>
<?php 
	}
	if($surat->status == 4 && $surat->surat_no == '{surat_no}' && $process->role_handle == get_role()) {
?>
					<button id="btn-set-no" type="button" class="btn btn-app" onclick="generateNomor();">
						<i class="fa fa-keyboard-o"></i> Set Nomor
					</button>
<?php
	}	
	if($surat->status == 7 && has_permission(24)) {
?>
					<button type="submit" class="btn btn-app">
						<i class="fa fa-file"></i> Simpan Lampiran
					</button>
<?php
	}
?>				
				</div>
				<div class="col-xs-8">
<?php
	if($surat->status != 99) {
		if(has_permission($process->permission_handle)) {
//		if($process->role_handle == get_role() && ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-')) {
			
			$signed = json_decode($surat->signed, TRUE);
			if($signed['jabatan'] == 'Direktur') {
				$level = 'L0';
			}else {
				$level = 'L1';
			}
			
			$approved = FALSE;
			if ($surat->status == 0 && $approval['direksi']['status'] == 1) {
				$approved = TRUE;
			}
			
			if($level == 'L0'){ 
				if ($surat->status == 2 && $approved_status >= 2) {
					$approved = TRUE;
				}
			}else {
				if ($surat->status == 2 && $approved_status >= 1) {
					$approved = TRUE;
				}
			}

			if ($surat->status == 2 && (!$approved_jabatan)) {
				$approved = TRUE;
			}

			if($surat->status == 2 && (in_array($surat->surat_from_ref_id, array(1, 2, 10)))) {
				$approved = TRUE;
			}
			
			if($surat->status == 3 && ($surat->surat_from_ref_id == get_user_data('unit_id') || has_permission(7))) {
				$approved = TRUE;
			}

			if ($surat->status == 4 && $surat->surat_no != '{surat_no}') {
				$approved = TRUE;
			}
			
			if (in_array($surat->status, array(6, 7))) {
				$approved = TRUE;	
			}
			
			if ($surat->status == 5) {
				$tujuan = json_decode($surat->distribusi, TRUE);
				foreach ($tujuan as $st) {
					if (get_user_data('unit_id') == $st['id']) {
						$approved = TRUE;	
					}
				}
			}
			
			if($process->button_process != '-') {
?>
					<button id="btn-process" type="button" class="btn btn-app pull-right bg-green <?php echo (!$approved) ? 'hide' : ''; ?>" onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
			}
		}

		if($surat->status <= 6) {
			if(has_permission($process->permission_handle)) {	
				$result = $this->disposisi_model->get_disposisi_from_ref('surat', $surat->surat_id);
				if($process->button_return != '-' && ($result->num_rows() == 0)) {
?>
					<button id="btn-return" type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
						<i class="fa fa-caret-square-o-left"></i> <?php echo $process->button_return; ?>
					</button>
<?php 
				}
			}
		}

		if((get_user_data('unit_id') == $surat->surat_from_ref_id && $surat->status == 8) || ($surat->status == 8 && has_permission(7))) {
?>
					<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesArsipUnit();">
						<i class="fa fa-caret-square-o-right"></i> Simpan Sebagai Arsip
					</button>
<?php 
		}
	} else {
		if ($surat->unit_archive_status != 99 && $surat->status != 99) {
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
	echo form_close();
?>			

</section><!-- /.content -->

<script type="text/javascript">

	$(document).ready(function() {
		
		$(".select2").select2();
		
		$('#price').number( true, 2 );
				
		$('.datepicker').datepicker({autoclose : true, dateFormat : 'dd-mm-yy', //maxDate: 0
		});
		
		$('.datemulaipicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			//minDate : 1
		});
		
		$('.dateselesaipicker').datepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
			//minDate : 2
		});
		
		$('.surat_ext_title').autocomplete({
			source: '<?php echo site_url('global/admin/eksternal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_ext_nama').val(ui.item.nama_pejabat);
				$('#surat_ext_instansi').val(ui.item.instansi);
				$('#surat_ext_alamat').val(ui.item.address);
			}
		});
		
		$('.surat_ext_title').keyup(function() {
			if($(this).val().trim() == '') {
				$('#surat_ext_nama').val('');
				$('#surat_ext_instansi').val('');
				$('#surat_ext_alamat').val('');
			}
		});

		$('.surat_int_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				r = $(this).attr('data-row-id');
				$('#surat_to_kode_' + r).val(ui.item.unit_code);
				$('#surat_to_unit_kode_' + r).html(ui.item.unit_code);
				$('#surat_to_unit_id_' + r).val(ui.item.id);
				$('#surat_to_jabatan_' + r).val(ui.item.jabatan);
				$('#surat_to_nama_' + r).val(ui.item.nama_pejabat);
				$('#surat_to_nip_' + r).val(ui.item.nip_pejabat);
				$('#surat_to_dir_' + r).val(ui.item.instansi);				
			}
		});
		
		$('.surat_int_unit').keyup(function() {
			if($(this).val().trim() == '') {
				r = $(this).attr('data-row-id');
				$('#surat_to_kode_' + r).val('');
				$('#surat_to_unit_kode_' + r).html('________');
				$('#surat_to_unit_id_' + r).val('');
				$('#surat_to_nama_' + r).val('');
				$('#surat_to_nip_' + r).val('');
				$('#surat_to_dir_' + r).val('');
			}
		});

		viewKonsep($('#konsep_surat_id').val());

	}); //end document

	function initPage() {
		$('#konsep_text').html($('#konsep_' + $('#konsep_surat_id').val()).html());	
	}

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

	function viewKonsep(kid) {
		$('#konsep_text').html($('#konsep_' + kid).html());
	}

	function removePenerima(rid) {
		$('#row_penerima_' + rid).remove();
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
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="Judul File ..." id="title_' + attachmentRow + '">' +
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

	function BootboxDate() {
	    var frm_str = '<form id="some-form">'
	        + '<div class="form-group">'
	        + '<label for="date">Tgl. Surat</label>'
	        + '<div class="input-group">'
	        + '<input id="date" class="date form-control input-sm" size="16" placeholder="dd-mm-yyyy" type="text">'
	        + '<div class="input-group-addon">'
			+ '<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>'
			+ '</div>'
	        + '</div></div>'
	        + '</form>';

	    var object = $('<div/>').html(frm_str).contents();

	    object.find('.date').datepicker({
	        dateFormat: 'dd-mm-yy',
	        autoclose: true
	    }).on('changeDate', function (ev) {
	        $(this).blur();
	        $(this).datepicker('hide');
	    });

	    return object
	}
	
	function ubahTglSurat() {
		$('#box-process-btn .overlay').removeClass('hide');

		bootbox.dialog({
		    message: BootboxDate,
		    title: "Ubah Tanggal Surat",
		    buttons: {
		    	cancel: {
		    		label: "Batal",
		    		callback: function() {
		                $('#box-process-btn .overlay').addClass('hide');
		            }
		    	},
		        main: {
		            label: "Simpan",
		            className: "btn-primary",
		            callback: function(result) {
		            	var tgl_surat = $('#date').val();
		            	if(result) {
							$.ajax({
								type: "POST",
								url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
								data: {action: 'surat.tugas_model.ubah_tgl', 
										ref_id: '<?php echo $surat->surat_id; ?>', 
										surat_tgl: tgl_surat, 
										last_flow: <?php echo $last_flow; ?>,
										function_ref_id: <?php echo 13; ?>,
										flow_seq: <?php echo $surat->status; ?>
									},
								success: function(data) {
									if(typeof(data.error) != 'undefined') {
										eval(data.execute);
									}else {
										// bootbox.alert("Data transfer error!");
										bootbox.alert(data.message);
										$('#box-process-btn .overlay').addClass('hide');
									}
								}
							});
						}
		            }
		        }
		    }
		});
	}

	function returnData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.prompt({
			title: 'Kembalikan berkas.', 
			inputType: 'textarea',
			callback: function(result) {
				if(result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {action: 'surat.tugas_model.return_data', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								note: result, 
								last_flow: <?php echo $last_flow; ?>,
								function_ref_id: <?php echo 13; ?>,
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

	function generateNomor() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Buat Nomor Surat?', function(result){
			if(result) {
				$.ajax({
		 			type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
		 			data: {action: 'surat.surat_model.get_current_tugas_no', 
							ref_id: '<?php echo $surat->surat_id; ?>',
							function_ref_id: <?php echo 13; ?>
		 					},
		 			success: function(data){
		 				if(typeof(data.error) != 'undefined') {
		 					$('#surat_no').val(data.surat_no);
		 					$('#surat_tgl').val(data.surat_tgl);
		 					$('#btn-process').removeClass('hide');
		 					$('#btn-set-no').addClass('hide');
		 					
		 					bootbox.alert(data.message, function(){ document.location.reload(); });
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
	
	function prosesArsipUnit() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Simpan sebagai Arsip?', function(result){
			if(result) {
				location.assign('<?php echo site_url('surat/tugas/register_arsip/' . $surat->surat_id); ?>');	 
			} else {
				$('#box-process-btn .overlay').addClass('hide');
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
					data: {action: 'surat.tugas_model.proses_data', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							note: result, 
							last_flow: <?php echo $last_flow; ?>,
							function_ref_id: <?php echo 13; ?>,
							flow_seq: <?php echo $surat->status; ?>,
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
//			if($active) {
		?>
			function setApproved(e, cid, uid) {
				var ap = e.is(':checked') ? 1 : 0;

				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.set_approve', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							function_ref_id: '<?php echo 13; ?>',
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
		<?php
//			}
		?>
<?php 
			if($surat->status >= 1) {
?>
				function printSurat() {
					window.open('<?php echo site_url('surat/tugas/cetak/' . $surat->surat_id . '/' . strtoupper($surat->jenis_agenda)); ?>');
				}
<?php 
			}
?>

</script>