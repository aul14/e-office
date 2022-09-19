<?php 
	list($agenda_date, $agenda_time) = explode(' ', $surat->created_time);
	list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
	$agenda_date = db_to_human($agenda_date);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li class="active"><a href="#">Tugas</a></li>
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
	$param = (array) $surat;
	$approval = json_decode($surat->approval, TRUE);

	echo form_open_multipart('', ' id="form_kontrak" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('action', 'surat.tugas_model.update_surat'); 
	echo form_hidden('return', 'surat/tugas/tugas_view');
	
	echo form_hidden('jenis_agenda', 'ST'); 
	echo form_hidden('surat_id', $surat->surat_id);
	echo form_hidden('surat_no', $surat->surat_no);
//	echo form_hidden('surat_awal', db_to_human($surat->surat_awal));
	echo form_hidden('jenis_agenda', $surat->jenis_agenda);
	echo form_hidden('surat_id', $surat->surat_id);
	echo form_hidden('surat_to_ref_id', $surat->surat_to_ref_id);
	echo form_hidden('surat_from_ref_id', $surat->surat_from_ref_id);
	echo form_hidden('function_ref_id', $function_ref_id);
	
	echo form_hidden('surat_int_unit', get_user_data('unit_name'));
	echo form_hidden('surat_int_unit_id', get_user_data('unit_id'));
	echo form_hidden('surat_int_kode', get_user_data('unit_code'));
	
	echo form_hidden('surat_from_ref', 'surat');
	echo form_hidden('surat_from_ref_id', $surat->surat_from_ref_id);

	$result = $this->admin_model->get_ref_internal($surat->surat_from_ref_id);
	$unit = $result->row();

	echo form_hidden('official_code', $unit->official_code);
	echo form_hidden('surat_from_ref_data[dir]', $unit->instansi);
	echo form_hidden('surat_from_ref_data[jabatan]', $unit->jabatan);
	echo form_hidden('surat_from_ref_data[pangkat]', $unit->pangkat);
	echo form_hidden('surat_from_ref_data[nama]', $unit->nama_pejabat);
	echo form_hidden('surat_from_ref_data[nip]', $unit->nip_pejabat);

	$result_surat_ref = $this->admin_model->get_ref_surat_masuk($surat->surat_id);
	$surat_ref_num = $result_surat_ref->num_rows();
	
	if ($surat_ref_num > 0) {
		$surat_from_ref = $result_surat_ref->row();
	}else {
		$surat_from_ref = '';
	}

	$param['asal_surat'] = '';
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
		$param['asal_surat'] = $surat_from_ref_data['instansi'];
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
					<input type="text" id="surat_tgl_ref" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat_from_ref->surat_tgl); ?>">
				</div>
			</div>
<?php
	}
?>						
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_no" <?php echo ($surat->status < 5) ? '' : 'name="surat_no"'; ?> class="form-control" data-input-title="Nomor Surat" value="<?php echo $param['surat_no']; ?>" >
				</div>
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Surat <br> (dd-mm-yyyy)</label>
				<div class="col-lg-4 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl" class="form-control datemulaipicker" data-input-title="Tanggal Surat" value="<?php echo ($surat->surat_tgl != '') ? db_to_human($surat->surat_tgl) : ''; ?>">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>				
			</div>
			<div class="form-group">
				<label for="surat_tgl_masuk" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Konsep Surat <br> (dd-mm-yyyy) </label>
				<div class="col-lg-4 col-sm-9">

					<div class="input-group">
						<input type="text" id="surat_awal" name="surat_awal" class="form-control dateselesaipicker required" data-input-title="tgl Konsep Surat" value="<?php echo ($surat->surat_awal != '') ? db_to_human($surat->surat_awal) : ''; ?>">
						<div class="input-group-addon">
							<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" name="surat_perihal" class="form-control required" rows="3" placeholder="Perihal" data-input-title="Perihal" ><?php echo $surat->surat_perihal; ?></textarea>
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
	
	echo form_dropdown('format_surat_id', $opt_format, $surat->format_surat_id, (' id="format_surat_id" class="form-control required" data-input-title="Format Template" '));
?>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Menugaskan Kepada</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Penerima Tugas.." onclick="addPenerima();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="list-penerima" class="box-body">
<?php 
	$param['penerima_tugas'] = '<table class="daftar" border="1" cellspacing="0" cellpadding="1"><thead>';
	$param['penerima_tugas'] .= '<tr>';
	$param['penerima_tugas'] .= '<th style="text-align: center;">NO.</th>';
	$param['penerima_tugas'] .= '<th style="text-align: center;">NAMA</th>';
	$param['penerima_tugas'] .= '<th style="text-align: center;">PANGKAT / GOL.</th>';
	$param['penerima_tugas'] .= '<th style="text-align: center;">NIP</th>';
	$param['penerima_tugas'] .= '<th style="text-align: center;">JABATAN</th>';
	$param['penerima_tugas'] .= '</tr></thead><tbody>';

	$a = 0;
	$last_ttd=  0;
	$count = array();
	$tanda_tangan = json_decode($surat->distribusi, TRUE);
	
	if(isset($tanda_tangan)) {
		foreach($tanda_tangan as $signed) {
			$no = $a + 1;
			$param['penerima_tugas'] .= '<tr>';
			$param['penerima_tugas'] .= '<td align="center">' . $no . '</td>';
			$param['penerima_tugas'] .= '<td>' . $signed['nama'] . '</td>';
			$param['penerima_tugas'] .= '<td>' . $signed['pangkat'] . '</td>';
			$param['penerima_tugas'] .= '<td>' . $signed['nip'] . '</td>';
			$param['penerima_tugas'] .= '<td>' . $signed['jabatan'] . ' ' . $signed['nama_unitkerja'] . '</td>';
			$param['penerima_tugas'] .= '</tr>';
?>
			<fieldset id="row_penerima_<?php echo $a; ?>" style="position: relative;">
				<legend></legend>				
<?php 
// $distribusi = json_decode($row->distribusi, TRUE);
// foreach ($distribusi as $dis_key => $dis_val) {
// 	foreach ($dis_val as $k => $v) {
// 		$list_distribusi = user_in_unit($v["unit_id"]);
// 		foreach ($list_distribusi->result() as $row_distribusi) {
// 			$this->db->insert('notify', array('function_ref_id' => $_POST['function_ref_id'], 'ref_id' => $row->surat_id, 'agenda' => ($row->jenis_agenda . ' - ' . $row->agenda_id), 'note' => $note, 'detail_link' => ($detail_link . $row->surat_id), 'notify_user_id' => $row_distribusi->user_id, 'read' => 0));
// 		}		
// 	}
// }				
?>				
				<div class="form-group">
				<label for="surat_to_unit_unit_<?php echo $a; ?>" class="col-sm-2 control-label">Nama Unit Kerja <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
					<div class="col-lg-10 col-sm-5">
						<div class="input-group">
							<input type="text" id="surat_to_unit_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][nama_unitkerja]"  class="surat_int_unit form-control required" data-row-id="<?php echo $a; ?>" data-input-title="Nama Unit Kerja Penerima Tugas" value="<?php echo $signed ['nama_unitkerja']; ?>" placeholder="Nama Unit Kerja Penerima Tugas...">
							<div id="surat_to_unit_kode_<?php echo $a; ?>" disabled="disabled"  class="input-group-addon"><?php echo $signed ['kode_unitkerja']; ?></div>	
							<input type="hidden" id="surat_to_kode_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][kode_unitkerja]" class="form-control required" data-input-title="Kode Instansi Unit" value="<?php echo $signed ['kode_unitkerja']; ?>">
							<input type="hidden" id="surat_to_unit_id_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][id]" value="<?php echo $signed ['id']; ?>">
							<input type="hidden" id="surat_to_dir_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][dir]" value="<?php echo $signed ['dir']; ?>">
						</div>
					</div>
				</div>				
				<div class="form-group">
					<label for="surat_to_nama_<?php echo $a; ?>" class="col-sm-2 control-label">Nama</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nama_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][nama]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="<?php echo $signed ['nama']; ?>" placeholder="Nama Pejabat Penerima Tugas...">
					</div>
					<label for="surat_to_nip_<?php echo $a; ?>" class="col-sm-2 control-label">NIP</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_nip_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][nip]" class="form-control" data-input-title="NIP Pejabat Penerima Tugas" value="<?php echo $signed ['nip']; ?>" placeholder="NIP Pejabat Penerima Tugas...">
					</div>
				</div>
				<div class="form-group">
					<label for="surat_to_jabatan_<?php echo $a; ?>" class="col-sm-2 control-label">Jabatan</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_to_jabatan_<?php echo $a; ?>" name="distribusi[<?php echo $a; ?>][jabatan]" class="form-control" data-input-title="Jabatan Penerima Tugas" value="<?php echo $signed ['jabatan']; ?>" placeholder="Jabatan Penerima Tugas...">
					</div>
					<label for="surat_to_jabatan_<?php echo $a; ?>" class="col-sm-2 control-label">Pangkat / Golongan</label>
					<div class="col-lg-4 col-sm-9">
<?php 
	$opt_pangkat = $this->admin_model->get_system_config('pangkat');
	echo form_dropdown('distribusi['.$a.'][pangkat]', $opt_pangkat, $signed ['pangkat'], (' id="surat_to_pangkat_'.$a.'" class="form-control" '));
?>						
					</div>
				</div>
				<div class="form-group">
					<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Keperluan</label>
					<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" name="distribusi[<?php echo $a; ?>][keperluan]" class="form-control required" rows="3" placeholder="Perihal" data-input-title="Perihal" ><?php echo $signed ['keperluan']; ?></textarea>
					</div>
				</div>					
			</fieldset>
<?php 
		$a++;
		$last_ttd=  $a;
		}		
?>
		</div><!-- /.box-body -->
	</div><!-- /.box -->

	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Klasifikasi Arsip</h3>
		</div>
		<div class="box-body">
			<div class="form-group">
				<label for="no_surat" class="col-md-3 control-label">Kode</label>
				<div class="col-md-9">
					<select name="kode_klasifikasi_arsip" id="kode_klasifikasi_arsip" class="form-control requiered" onchange="klasifikasiChange();">
						<option data-klasifikasi_sub_sub="" data-klasifikasi_sub="" data-klasifikasi="" value="">--</option>
<?php
	$opt_klasifikasi = $this->admin_model->get_parent_klasifikasi_arsip(0);
	
	foreach($opt_klasifikasi->result() as $row) {
		echo '<optgroup label="' . trim($row->kode_klasifikasi) . ' - ' . $row->nama_klasifikasi . '"></optgroup>';
		$opt_sub_klasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($row->entry_id);
		foreach($opt_sub_klasifikasi->result() as $sub_row) {
			echo '<optgroup label=" > ' . trim($sub_row->kode_klasifikasi) . ' - ' . $sub_row->nama_klasifikasi . '">';
			$opt_sub_subklasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($sub_row->entry_id);
			foreach($opt_sub_subklasifikasi->result() as $sub_subrow) {
				echo '<option data-klasifikasi_sub_sub="' . $sub_subrow->nama_klasifikasi . '" data-klasifikasi_sub="' . $sub_row->nama_klasifikasi . '" data-klasifikasi="' . $row->nama_klasifikasi . '" value="' . trim($sub_subrow->kode_klasifikasi) . '" ' . ((trim($sub_subrow->kode_klasifikasi) == trim($surat->kode_klasifikasi_arsip)) ? 'selected="selected"' : '') . ' > ' . trim($sub_subrow->kode_klasifikasi) . ' - ' . trim($sub_subrow->nama_klasifikasi) . '</option>';
			}
			
			echo '</optgroup>';
		}
	}
	// echo form_dropdown('kode_klasifikasi_arsip', $opt_klasifikasi, '', (' id="kode_klasifikasi_arsip" class="form-control" '));
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
				<label for="" class="col-md-3 control-label"></label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi_sub" class="form-control" disabled="disabled" value="">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-md-3 control-label"></label>
				<div class="col-md-9">
					<input type="text" id="klasifikasi_sub_sub" class="form-control" disabled="disabled" value="">
				</div>
			</div>
		</div>
	</div>
<?php
		$param['penerima_tugas'] .= '</tbody></table>';
		$param['keperluan'] = $signed['keperluan'];
	}
?>				
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
	$cek_approval = json_decode($surat->approval, TRUE);

	$p = 1;
	$list = $this->surat_model->get_all_parents_st($surat->surat_from_ref_id);
		
	$opt_sign = array();
	$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	// if($unit->ske_sign == 1) {
	// 	$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	// }
	
	$approval_type = (in_array($unit->level, array('L0', 'L1'))) ? 'direksi' : 'direksi';
	$status_appr = (isset($cek_approval[$approval_type][$unit->id]['status'])) ? $cek_approval[$approval_type][$unit->id]['status'] : 0;
	
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][index]', $p++);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][unit_name]', $unit->value);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][user_id]', $unit->user_id);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nip]', $unit->nip_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][jabatan]', $unit->jabatan);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][pangkat]', $unit->pangkat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nama_pejabat]', $unit->nama_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][email]', $unit->email);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][status]', $status_appr);
	echo form_hidden('approval[' . $approval_type. '][diskusi]', json_encode(array()));
	echo form_hidden('approval[' . $approval_type. '][status]', 0);
	
	foreach ($list as $parent) {	
		$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
		// if($parent['ske_sign'] == 1) {
		// 	$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
		// }
		
// 		if($parent['sub_id'] != 0) {
// 			$result = $this->admin_model->get_ref_internal($parent['sub_id']);
// 			$sub = $result->row();

// 			$status_appr_sub = (isset($cek_approval[$approval_type][$sub->id]['status'])) ? $cek_approval[$approval_type][$sub->id]['status'] : 0;
			
//			$approval_type = (in_array($sub->level, array('L0', 'L1'))) ? 'direksi' : 'direksi';
			
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][index]', $p++);
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][unit_name]', $sub->value);
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][user_id]', $sub->user_id);
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][nip]', $sub->nip_pejabat);
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][jabatan]', $unit->jabatan);
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][nama_pejabat]', $sub->nama_pejabat);
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][email]', $sub->email);
// 			echo form_hidden('approval[' . $approval_type. '][' . $sub->id . '][status]', $status_appr_sub);
			
// 		}
		
		$approval_type = (in_array($parent['level'], array('L0', 'L1'))) ? 'direksi' : 'direksi';
		$status_appr_dir = (isset($cek_approval[$approval_type][$parent['organization_structure_id']]['status'])) ? $cek_approval[$approval_type][$parent['organization_structure_id']]['status'] : 0;
		
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][index]', $p++);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][unit_name]', $parent['unit_name']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][user_id]', $parent['user_id']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][nip]', $parent['nip']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][jabatan]', $parent['jabatan']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][pangkat]', $parent['pangkat']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][nama_pejabat]', $parent['user_name']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][email]', $parent['email']);
		echo form_hidden('approval[' . $approval_type. '][' . $parent['organization_structure_id'] . '][status]', $status_appr_dir);
		echo form_hidden('approval[' . $approval_type. '][diskusi]', json_encode(array()));
		echo form_hidden('approval[' . $approval_type. '][status]', 0);		
	}
	
	$signed = json_decode($surat->signed, TRUE);
	$param['signed|unit_id'] = $signed['unit_id'];
	$param['signed|unit_name'] = $signed['unit_name'];
	$param['signed|jabatan'] = $signed['jabatan'];
	$param['signed|pangkat'] = (isset($signed['pangkat'])) ? $signed['pangkat'] : '';
	$param['signed|nama_pejabat'] = $signed['nama_pejabat'];
	$param['signed|nip'] = $signed['nip'];

	echo form_dropdown('signed', $opt_sign, $signed['unit_id'], ' class="form-control"');
?>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
		
	<div id="box-konsep" class="box box-primary">
		<div id="all-konsep" class="hide">
<?php
	$opt_konsep = array(0 => '--');
	$get_konsep = array(0 => '--');
	$active_konsep = '';
	$konsep_text = '';

	if($konsep->num_rows() > 0) {
		$opt_konsep = array();
		foreach($konsep->result() as $row) {
			$opt_konsep[$row->konsep_surat_id] = $row->title . ' - Versi ' . $row->version;
			$get_konsep[0] = $row->title . ' - Versi ' . $row->version;
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
		
		$param['sifat_surat'] = humanize($param['sifat_surat']);
		$param['surat_awal'] = db_to_human($surat->surat_awal);

		$konsep_text = sprintformat($template->format_text, $param);
	}	
?>
		</div>
		<div class="box-header with-border">
			<h3 class="box-title">
				Konsep Surat Perintah
			</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<!-- form action="" class="form-horizontal" onsubmit="return false;" -->
				<div class="form-group">
					<label for="surat_ext_alamat" class="col-sm-2 control-label">Versi Konsep</label>
					<div class="col-sm-4">
<?php 
		echo form_dropdown('konsep_surat_id', $opt_konsep, $active_konsep, (' id="konsep_surat_id" class="form-control" onchange="viewKonsep($(this).val());" '));
?>
					</div>
					<div class="col-sm-6">
						<div class="btn-group">
							<button type="button" class="btn btn-default" title="Konsep Baru" onclick="addKonsep();"><i class="glyphicon glyphicon-plus"></i></button>
							<button type="button" class="btn btn-default" title="Simpan Konsep" onclick="saveKonsep();"><i class="glyphicon glyphicon-floppy-disk"></i></button>
							<button type="button" class="btn btn-default" title="Simpan Konsep sebagai.." onclick="saveAsKonsep();"><i class="glyphicon glyphicon-floppy-saved"></i></button>
							<!-- button type="button" class="btn btn-default" title="Hapus Konsep" onclick="removeKonsep();"><i class="glyphicon glyphicon-remove"></i></button -->
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-12">
						<textarea id="konsep_text" name="konsep_text" ><?php echo $konsep_text; ?></textarea>
					</div>
				</div>
			<!-- /form -->
		</div>
	</div>

<?php
	if($surat->status != 2) {
	$approval = json_decode($surat->approval, TRUE);
	$obj_approval = json_decode($surat->approval);
	$approval_status = 0;

	if($surat->status > 1) {
?>
	<div id="box-comment-draft" class="box box-primary <?php echo $surat->status == 2 ? '' : 'collapsed-box'; ?>">
		<div class="box-header with-border">
			<h3 class="box-title">Verifikasi Administrasi </h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa <?php echo $surat->status == 1 ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="col-md-6">
<?php 
	if(($surat->status > 1) && (isset($approval['non_direksi']))) {
		$non_dir = array($surat->created_id);
		foreach ($approval['non_direksi'] as $ak => $appr) {
			if(isset( $appr['unit_name'])) {
				$non_dir[] = $appr['user_id'];
			}
		}

		foreach ($approval['non_direksi'] as $ak => $appr) {
			if(isset( $appr['unit_name'])) {
				$approval_status += $appr['status'];
?>					
				<div class="form-group">
					<label id="pejabat_<?php $ak; ?>" class="col-md-12">
						<input type="checkbox" value="1" <?php echo ($appr['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 2) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'direksi\', ' . $ak. ')"'; ?>> 
						<?php echo $appr['jabatan'] . ', ' . $appr['unit_name']; ?>
					</label>
				</div>
<?php 
			}
		}
	}

	if($surat->status > 1) {
		$dir = array($surat->created_id);
		foreach ($approval['direksi'] as $akdir => $apprdir) {
			if(isset($apprdir['unit_name'])) {
				$dir[] = $apprdir['user_id'];
			}
		}
		
		foreach ($approval['direksi'] as $akdir => $apprdir) {
			if(isset($apprdir['unit_name'])) {
				$approval_status += $apprdir['status'];
?>
				<div class="form-group">
					<label id="pejabat_<?php $akdir; ?>" class="col-md-12">
						<input type="checkbox" value="1" <?php echo ($apprdir['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($apprdir['user_id'] != get_user_id() || $surat->status > 2) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'direksi\', ' . $akdir. ')"'; ?>> 
						<?php echo $apprdir['jabatan'] . ', ' . $apprdir['unit_name']; ?>
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
	}
?>

	<!-- Default box -->
<!-- 	<div class="box box-primary"> -->
<!-- 		<div class="box-header with-border"> -->
<!-- 			<span class="h3 box-title">lampiran </span> <span class="small">Max. 2MB (*.pdf, *.jpg, *.jpeg, *.png, *.zip, *.rar)</span> -->
<!-- 			<div class="box-tools pull-right"> -->
			<!-- <button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button> -->
<!-- 			</div> -->
<!-- 		</div> -->
		
<!-- 		<div id="attachment_list" class="box-body"> -->
<!-- 			<div id="attachment_0" class="form-group"> -->
<!-- 				<div class="col-md-8"> -->
<!-- 					<input type="text" name="attachment[0][title]" class="form-control file-attachment" placeholder="Judul File ..."> -->
<!-- 				</div> -->
<!-- 				<div class="col-md-4"> -->
<!-- 					<div class="form-group"> -->
<!-- 						<div class="btn btn-default btn-file"> -->
<!-- 							<i class="fa fa-paperclip"></i> -->
<!-- 							<input type="file" name="attachment_file_0" onchange="$('#flabel_0').html($(this).val())"> -->
<!-- 						</div> -->
<!-- 						<label id="flabel_0"></label> -->
<!-- 					</div> -->
<!-- 				</div> -->
<!-- 			</div> -->
<!-- 		</div> -->
<!-- 	</div> -->
			
<?php 	
	if($surat->status != 99) {
		if($process->modify == 1 || $process->button_return != '-' || $process->button_process != '-') {
?>	
	<div class="fixed-box-btn"></div>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-6">
					<button class="btn btn-app">
						<i class="fa fa-save"></i> Update
					</button>
				</div>
				<div class="col-xs-6">
<?php 
//			if(in_array($surat->status, array(0, 3, 5, 6))) {
//			if((in_array($surat->status, array(0, 3, 5, 6))) || ($surat->status == 1 && $process_approved_L2) || ($surat->status == 2 && $process_approved_L1) || ($surat->status == 4 && $process_approved_TU)) {
 			if(has_permission($process->permission_handle)) {
				if(has_permission($process->permission_handle) && ($process->modify == 1 || $process->button_return != '-' || $process->button_process != '-')) {
					if($process->button_process != '-') {

						$approved = TRUE;
						// if($surat->status == 2 && $approval_status >= 0) {
						// 	$approved = FALSE;	
						// }

						if($surat->status == 2) {
							$approved = FALSE;	
						}

						if($get_konsep[0] == '--') {
							$approved = FALSE;
						}
?>
					<button id="btn-process" type="button" class="btn btn-app pull-right bg-green <?php echo (!$approved) ? 'hide' : ''; ?>" onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
					}
					
					if($surat->status != 6) {
						$result = $this->disposisi_model->get_disposisi_from_ref('surat', $surat->surat_id);
// 						var_dump($result);
						if($process->button_return != '-' && ($result->num_rows() == 0)) {
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
	} else {
		if(get_user_data('unit_id') == $surat->surat_int_unit_id && $surat->unit_archive_status != 99) {
?>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-6"></div>
				
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

	echo form_close();
?>
		
</section><!-- /.content -->

<script type="text/javascript">

	$(document).ready(function() {
		
		CKEDITOR.replace('konsep_text', { height: 500});
<?php 
	if($konsep->num_rows() > 0) {
?>
		CKEDITOR.instances.konsep_text.setData($('#konsep_' + $('#konsep_surat_id').val()).html());
<?php 	
	}
?>
		$('.datepicker').datepicker({autoclose : true, dateFormat : 'dd-mm-yy', //maxDate: 0
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
				$('#surat_to_pangkat_' + r).val(ui.item.pangkat);
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
				$('#surat_to_jabatan_' + r).val('');
				$('#surat_to_pangkat_' + r).val('');
				$('#surat_to_nip_' + r).val('');
				$('#surat_to_dir_' + r).val('');
			}
		});

		klasifikasiChange();
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

	//var penerimaRow = <?php echo $last_ttd; ?>;
	var penerimaRow = 0;
	
	function addPenerima() {
		
		penerimaRow++;
				
		row = '<fieldset id="row_penerima_' + penerimaRow + '" style="position: relative;">' +
				'<legend></legend>' +
				'<button type="button" class="btn btn-danger" onclick="removePenerima(' + penerimaRow + ')" title="Hapus penandatangan..." style="position: absolute; z-index:1; padding: 1px 6px; top: 25px;"><i class="fa fa-minus"></i></button>' +
				'<div class="form-group">' +
				'	<label for="surat_in_unit_unit_' + penerimaRow + '" class="col-sm-2 control-label">Nama Unit Kerja <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>' +
				'		<div class="col-lg-10 col-sm-5">' +
				'			<div class="input-group">' +
				'				<input type="text" id="surat_to_unit_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nama_unitkerja]"  class="form-control required" data-row-id="' + penerimaRow + '" data-input-title="Nama Unit Kerja Penerima Tugas" value="" placeholder="Nama Unit Kerja Penerima Tugas...">' +
				'				<div id="surat_in_unit_kode_' + penerimaRow + '" disabled="disabled"  class="input-group-addon">______</div>' +
				'				<input type="hidden" id="surat_in_kode_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][kode_unitkerja]" class="form-control required" data-input-title="Kode Instansi Unit" value="">' +
				'				<input type="hidden" id="surat_in_unit_id_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][id]" value="">' +
				'				<input type="hidden" id="surat_in_dir_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][dir]" value="">' +
				'			</div>' +
				'		</div>' +
				'</div>' +
				'<div class="form-group">' +
				'	<label for="surat_in_nama_' + penerimaRow + '" class="col-sm-2 control-label">Nama</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<input type="text" id="surat_in_nama_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nama]" class="form-control" data-input-title="Nama Pejabat Penerima Tugas" value="" placeholder="Nama Pejabat Penerima Tugas...">' +
				'	</div>' +
				'	<label for="surat_in_nip_' + penerimaRow + '" class="col-sm-2 control-label">Nama Unit Kerja</label>' +
				'	<div class="col-lg-4 col-sm-3">' +
				'		<input type="text" id="surat_in_nip_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][nip]" class="form-control" data-input-title="NIP Pejabat Penerima Tugas" value="" placeholder="NIP Pejabat Penerima Tugas...">' +
				'	</div>' +
				'</div>' +
				'<div class="form-group">' +
				'	<label for="surat_in_nama_' + penerimaRow + '" class="col-sm-2 control-label">Jabatan</label>' +
				'	<div class="col-lg-4 col-sm-9">' +
				'		<input type="text" id="surat_in_jabatan_' + penerimaRow + '" name="distribusi[' + penerimaRow + '][jabatan]" class="form-control" data-input-title="Jabatan Penerima Tugas" value="" placeholder="Jabatan Penerima Tugas...">' +
				'	</div>' +
				'	<label for="surat_in_pangkat_' + penerimaRow + '" class="col-sm-2 control-label">Pangkat</label>' +
				'	<div class="col-lg-4 col-sm-3">' +
				'		<?php echo str_replace("\n", "", form_dropdown(('distribusi[\' + penerimaRow + \'][pangkat]'), $opt_pangkat, '', (' id="surat_in_pangkat_\' + penerimaRow + \'" class="form-control" '))); ?>' +
				'	</div>' +
				'</div>' +
				'<div class="form-group">' +
				'	<label for="surat_in_nip_' + penerimaRow + '" class="col-sm-2 control-label">Keperluan</label>' +
				'	<div class="col-lg-10 col-sm-9">' +
				'		<textarea id="surat_perihal" name="distribusi[' + penerimaRow + '][keperluan]" class="form-control" rows="3"  data-input-title="Perihal" value="" placeholder="Perihal"></textarea>' +
				'	</div>' +
				'</div>' +
			'</fieldset>';

		$('#list-penerima').append(row);

		$('#surat_to_unit_' + penerimaRow).autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				r = $(this).attr('data-row-id');
				$('#surat_in_kode_' + r).val(ui.item.unit_code);
				$('#surat_in_unit_kode_' + r).html(ui.item.unit_code);
				$('#surat_in_unit_id_' + r).val(ui.item.id);
				$('#surat_in_jabatan_' + r).val(ui.item.jabatan);
				$('#surat_in_pangkat_' + r).val(ui.item.pangkat);
				$('#surat_in_nama_' + r).val(ui.item.nama_pejabat);
				$('#surat_in_nip_' + r).val(ui.item.nip_pejabat);
				$('#surat_in_dir_' + r).val(ui.item.instansi);				
			}
		});

		$('#surat_to_unit_' + penerimaRow).keyup(function() {
			if($(this).val().trim() == '') {
				r = $(this).attr('data-row-id');
				$('#surat_in_kode_' + r).val('');
				$('#surat_in_unit_kode_' + r).html('________');
				$('#surat_in_unit_id_' + r).val('');
				$('#surat_in_nama_' + r).val('');
				$('#surat_in_jabatan_' + r).val('');
				$('#surat_in_pangkat_' + r).val('');
				$('#surat_in_nip_' + r).val('');
				$('#surat_in_dir_' + r).val('');
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
						data: {action: 'surat.eksternal_model.return_data', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								note: result, 
								last_flow: <?php echo $last_flow; ?>,
								function_ref_id: <?php echo 2; ?>,
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
	
	function removePenerima(rid) {
		$('#row_penerima_' + rid).remove();
	}

	<?php 
//			if($active) {
		?>
			function setApproved(e, cid, uid) {
				var ap = e.is(':checked') ? 1 : 0;
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: { action: 'surat.surat_model.set_approve', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							distribusi_id: cid, 
							unit_id: uid,
							approval: ap
						},					
					success: function(data) {
						if(typeof(data.error) != 'undefined') {
							if(data.error == 0) {
								bootbox.alert(data.message);
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
	
	var attachmentRow = 0;

	function addAttachment() {
		attachmentRow++;

		row = '<div id="attachment_' + attachmentRow + '" class="form-group">' +
			'	<div class="col-md-8">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + attachmentRow + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="Judul File ...">' +
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
		$('#attachment_' + rid).remove();
	}

	function viewKonsep(kid) {
		CKEDITOR.instances.konsep_text.setData($('#konsep_' + kid).html());
	}
	
	function addKonsep() {
		bootbox.confirm('Buat konsep surat baru dengan format \'' + $("#format_surat_id option:selected").text() + '\'?', function(result){
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.add_konsep', 
							table: 'surat',
							ref_id: '<?php echo $surat->surat_id; ?>', 
							format_surat_id: $('#format_surat_id').val(),
							format_surat_text: $("#format_surat_id option:selected").text()
						},
					success: function(data) {
						if(typeof(data.error) != 'undefined') {
							bootbox.alert(data.message);
							$('#konsep_surat_id').html(data.new_option);
							CKEDITOR.instances.konsep_text.setData(data.konsep_text);
							$('#all-konsep').append(
									'<div id="konsep_' + $('#konsep_surat_id').val() + '" class="active">' + data.konsep_text + '</div>'
								);
						} else {
							bootbox.alert("Data transfer error!");
						}
					}
				});
			}
		});
	}
	
	function saveKonsep() {
		bootbox.confirm('Simpan Konsep Surat ini?', function(result){
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.save_konsep', 
							konsep_surat_id: $('#konsep_surat_id').val(),
							table: 'surat',
							ref_id: '<?php echo $surat->surat_id; ?>', 
							format_surat_id: $('#format_surat_id').val(),
							format_surat_text: $("#format_surat_id option:selected").text(),
							konsep_text: CKEDITOR.instances.konsep_text.getData()
						},
					success: function(data) {
						if(typeof(data.error) != 'undefined') {
							bootbox.alert(data.message, function(){ document.location.reload(); });
							$('#konsep_surat_id').html(data.new_option);
// 							$('#konsep_' + $('#konsep_surat_id').val()).html(CKEDITOR.instances.konsep_text.getData());
							$('#all-konsep').append(
									'<div id="konsep_' + $('#konsep_surat_id').val() + '" class="active">' + CKEDITOR.instances.konsep_text.getData() + '</div>'
								);
						} else {
							bootbox.alert("Data transfer error!");
						}
					}
				});
			}
		});
	}

	function saveAsKonsep() {
		bootbox.confirm('Simpan Konsep Surat ini sebagai versi baru?', function(result){
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.save_konsep_as', 
							table: 'surat',
							ref_id: '<?php echo $surat->surat_id; ?>', 
							format_surat_id: $('#format_surat_id').val(),
							format_surat_text: $("#format_surat_id option:selected").text(),
							konsep_text: CKEDITOR.instances.konsep_text.getData()
						},
					success: function(data) {
						if(typeof(data.error) != 'undefined') {
							bootbox.alert(data.message);
							$('#konsep_surat_id').html(data.new_option);
							
							$('#all-konsep > div').removeClass('active');
							$('#all-konsep').append(
									'<div id="konsep_' + data.konsep_surat_id + '"  data-version="" class="active">' + 
										CKEDITOR.instances.konsep_text.getData() + 
									'</div>'
								);
						} else {
							bootbox.alert("Data transfer error!");
						}
					}
				});
			}
		});
	}

	function removeKonsep() {
		bootbox.confirm('Hapus Konsep Surat ini?', function(result){
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.surat_model.remove_konsep', 
							konsep_surat_id: $('#konsep_surat_id').val(),
							table: 'surat',
							ref_id: '<?php echo $surat->surat_id; ?>', 
							format_surat_id: $('#format_surat_id').val(),
							format_surat_text: $("#format_surat_id option:selected").text()
						},
					success: function(data) {
						if(typeof(data.error) != 'undefined') {
							bootbox.alert(data.message);
							$('#konsep_surat_id').html(data.new_option);
						} else {
							bootbox.alert("Data transfer error!");
						}
					}
				});
			}
		});		
	}
	
// 	function toJSDate( date ) {

// 		var date = date.split("-");

// 		//(year, month, day, hours, minutes, seconds, milliseconds)
// 		//subtract 1 from month because Jan is 0 and Dec is 11
// 		return new Date(date[2], (parseInt(date[1])-1), date[0], 0, 0, 0, 0);

// 	}
	
// 	function tglBerlakuChange() {
// 		var date = $('.datemulaipicker').val().split("-");
// 		var nextDate = new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);

// 		$('.dateselesaipicker').datepicker('option', 'minDate', new Date(nextDate));

// 		if(toJSDate($('.datemulaipicker').val()) > toJSDate($('.dateselesaipicker').val())) {
// 			$('.dateselesaipicker').val(nextDate.getDate() + '-' + (nextDate.getMonth() + 1) + '-' + nextDate.getFullYear())
// 		}		
// 	}
	
// 	function tglMulaiChange() {
// 		var date = $('.datepicker').val().split("-");
// 		var nextDate = new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);

// 		$('.datemulaipicker').datepicker('option', 'minDate', new Date(nextDate));

// 		if(toJSDate($('.datepicker').val()) > toJSDate($('.datemulaipicker').val())) {
// 			$('.datemulaipicker').val(nextDate.getDate() + '-' + (nextDate.getMonth() + 1) + '-' + nextDate.getFullYear())
// 		}		
// 	}
	

</script>