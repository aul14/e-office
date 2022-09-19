
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li class="active">Internal</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php
	$param = (array) $surat;
	$approval = json_decode($surat->approval, TRUE);
		
	echo form_open_multipart('', ' id="form_surat_internal" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('action', 'surat.surat_model.update_surat'); 
	echo form_hidden('surat_id', $surat->surat_id);
//	echo form_hidden('surat_no', $surat->surat_no);
//	echo form_hidden('surat_tgl', db_to_human($surat->surat_tgl));
	echo form_hidden('surat_awal', db_to_human($surat->surat_awal));
	echo form_hidden('jenis_agenda', 'SI'); 
	echo form_hidden('function_ref_id', 3);
	echo form_hidden('return', 'surat/internal/sheet_view/'); 

	echo form_hidden('surat_from_ref_id', $surat->surat_from_ref_id);
//	echo form_hidden('surat_from_ref_data[unit]', get_user_data('unit_name'));
//	echo form_hidden('surat_from_ref_data[kode]', get_user_data('unit_code'));
	
	$result = $this->admin_model->get_ref_internal($surat->surat_from_ref_id);
	$unit = $result->row();
	
	echo form_hidden('official_code', $unit->official_code);
	echo form_hidden('surat_from_ref', $unit->official_code);
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

	if($surat->status < 5) {
		echo form_hidden('surat_no', $surat->surat_no);
	}
?>
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-6 col-sm-3">
					<input type="text" id="surat_no" <?php echo ($surat->status < 5) ? '' : 'name="surat_no"'; ?> class="form-control" readonly="readonly" value="<?php echo $param['surat_no']; ?>">
				</div>
				<label for="surat_awal" class="col-lg-2 col-sm-3 control-label">Tanggal Konsep Surat</label>
				<div class="col-lg-2 col-sm-3">
					<input type="text" id="surat_awal" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_awal); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label">Tanggal Surat</label>
				<div class="col-lg-2 col-sm-3">
					<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo ($surat->surat_tgl != '') ? db_to_human($surat->surat_tgl) : ''; ?>">
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
	$list = $this->admin_model->get_template_surat(3);
	$opt_format = array('' => '--');
	foreach ($list->result() as $row) {
		$opt_format[$row->format_surat_id] = $row->format_title;
	}

	echo form_dropdown('format_surat_id', $opt_format, $surat->format_surat_id, (' id="format_surat_id" class="form-control required" data-input-title="Format Template" '));
?>
				</div>
			</div>			
			<div class="form-group">
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
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Sifat Surat</label>
				<div class="col-lg-5 col-sm-9">
<?php 
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	echo form_dropdown('sifat_surat', $opt_sifat_surat, $surat->sifat_surat, (' id="sifat_surat" class="form-control" '));
?>
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	<div class="row">
		<div class="col-lg-6">
<?php 
	if($surat->surat_from_ref == '2') {
		$surat_to_ref_multi 	 = $this->surat_model->get_tujuan_surat($surat->surat_to_ref_id);
		$surat_to_ref_multi_data = $surat_to_ref_multi->row();
		$surat_to_user_data 	 = json_decode($surat_to_ref_multi_data->to_user_data, TRUE);
		if (isset($surat_to_user_data)) {
			$i = count($surat_to_user_data);
			$param['lampiran'] = '<table style="font-size: 11pt;"><tbody><tr><td><strong>Lampiran Nota Dinas</strong></td></tr>';
			$param['lampiran'] .= '<tr><td><strong>Nomor : ' . $param['surat_no'] . '</strong></td></tr>';
			$param['lampiran'] .= '<tr><td>&nbsp;</td></tr></tbody></table>';
			$param['lampiran'] .= '<table class="daftar" border="1" cellspacing="0" cellpadding="5"><thead><tr>';
			$param['lampiran'] .= '<td style="text-align: center;"><strong>NAMA</strong></td>';
			$param['lampiran'] .= '<td style="text-align: center;"><strong>BAGIAN</strong></td>';
			$param['lampiran'] .= '<td style="text-align: center;"><strong>JADWAL</strong></td>';
			$param['lampiran'] .= '<td style="text-align: center;"><strong>TEMPAT</strong></td></tr></thead><tbody>';

			foreach ($surat_to_user_data as $dt => $to_ref_data) {
				$param['surat_to_ref_data|jabatan'] = $to_ref_data['jabatan'];
				$param['surat_to_ref_data|unit'] 	= humanize($to_ref_data['unit']);
				$param['surat_to_ref_data|kode'] 	= (isset($to_ref_data['kode'])) ? $to_ref_data['kode'] : '';
				$param['surat_to_ref_data|pangkat'] = (isset($to_ref_data['pangkat'])) ? $to_ref_data['pangkat'] : '';
				$param['surat_to_ref_data|nama'] 	= $to_ref_data['nama'];
				$param['surat_to_ref_data|nip'] 	= (isset($to_ref_data['nip'])) ? $to_ref_data['nip'] : '';
				$param['surat_to_ref_data|dir'] 	= $to_ref_data['dir'];	

				$param['lampiran'] .= '<tr><td>' . $param['surat_to_ref_data|nama'] . '</td>';
				$param['lampiran'] .= '<td>' . $param['surat_to_ref_data|jabatan'] . ' ' . $param['surat_to_ref_data|unit'] . '</td>';
				$param['lampiran'] .= ($dt == 0) ? '<td rowspan="'.$i.'" style="text-align: center;"></td>' : '';
				$param['lampiran'] .= ($dt == 0) ? '<td rowspan="'.$i.'" style="text-align: center;"></td>' : '';
				$param['lampiran'] .= '</tr>';
			}
			
			$param['lampiran'] .= '</tbody></table>';
		}
	} else {	
		$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
		$param['surat_to_ref_data|jabatan'] = $surat_to_ref_data['jabatan'];
		$param['surat_to_ref_data|unit'] 	= humanize($surat_to_ref_data['unit']);
		$param['surat_to_ref_data|kode'] 	= $surat_to_ref_data['kode'];
		$param['surat_to_ref_data|pangkat'] = (isset($surat_to_ref_data['pangkat'])) ? $surat_to_ref_data['pangkat'] : '';
		$param['surat_to_ref_data|nama'] 	= $surat_to_ref_data['nama'];
		$param['surat_to_ref_data|nip'] 	= (isset($surat_to_ref_data['nip'])) ? $surat_to_ref_data['nip'] : '';
		$param['surat_to_ref_data|dir'] 	= $surat_to_ref_data['dir'];
	}		
?>
			<!-- Default box -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Tujuan Surat</h3>
				</div>
				<div class="box-body">
					<div class="form-group">
						<div class="col-sm-9 pull-right">
							<label class="col-sm-5">
								<input type="radio" id="opt_tujuan_1" name="opt_tujuan" value="1" <?php echo ($surat->surat_from_ref == '1') ? 'checked=checked' : ''; ?>> 1 Orang
							</label>
							<label class="col-sm-7">
								<input type="radio" id="opt_tujuan_2" name="opt_tujuan" value="2" <?php echo ($surat->surat_from_ref == '2') ? 'checked=checked' : ''; ?>> Lebih dari 1 Orang
							</label>
						</div>
					</div>
					<div id="multi_user_internal" style="<?php echo ($surat->surat_from_ref == '2') ? 'display: block' : 'display: none'; ?>">
						<div class="form-group">
							<div class="col-sm-12">								
								<input type="text" id="surat_to_ref_multi" name="surat_to_ref_multi" class="form-control" placeholder="Tujuan lebih dari 1 orang..." data-input-title="Tujuan" value='<?php echo (isset($surat_to_ref_multi_data->title)) ? $surat_to_ref_multi_data->title : set_value('title'); ?>'>
								<input type="hidden" id="surat_to_ref_detail" name="surat_to_ref_detail" class="form-control" value='<?php echo (isset($surat_to_ref_multi_data->to_user_data)) ? $surat_to_ref_multi_data->to_user_data : set_value('to_user_data'); ?>'>
								<input type="hidden" id="surat_to_ref_multi_id" name="surat_to_ref_multi_id" class="form-control" value='<?php echo (isset($surat_to_ref_multi_data->tujuan_surat_id)) ? $surat_to_ref_multi_data->tujuan_surat_id : set_value('tujuan_surat_id'); ?>'>
							</div>
						</div>	
					</div>
					<div id="user_internal" style="<?php echo ($surat->surat_from_ref == '1') ? 'display: block' : 'display: none'; ?>">
					<div class="form-group">
						<label for="surat_to_unit" class="col-sm-3 control-label">Unit <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" id="surat_to_unit" name="surat_to_ref_data[unit]" class="form-control" data-input-title="Unit Tujuan" value="<?php echo (isset($surat_to_ref_data['unit'])) ? $surat_to_ref_data['unit'] : ''; ?>" placeholder="Bagian / Sub Bagian tujuan surat...">
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
							<input type="hidden" id="surat_to_pangkat" name="surat_to_ref_data[pangkat]" value="<?php echo (isset($surat_to_ref_data['pangkat'])) ? $surat_to_ref_data['pangkat'] : ''; ?>">
							<input type="hidden" id="surat_to_nip" name="surat_to_ref_data[nip]" value="<?php echo (isset($surat_to_ref_data['nip'])) ? $surat_to_ref_data['nip'] : ''; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_to_dir" class="col-sm-3 control-label">Direktorat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_to_dir" name="surat_to_ref_data[dir]" class="form-control" readonly="readonly" data-input-title="Direktorat Tujuan" value="<?php echo (isset($surat_to_ref_data['dir'])) ? $surat_to_ref_data['dir'] : ''; ?>" placeholder="Direktorat tujuan surat...">
						</div>
					</div>
					</div>
				</div><!-- /.box-body -->
			</div>
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
							<select name="kode_klasifikasi_arsip" id="kode_klasifikasi_arsip" class="form-control requiered" onchange="klasifikasiChange();">
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
				echo '<option data-klasifikasi_sub_sub="' . trim($sub_subrow->nama_klasifikasi) . '" data-klasifikasi_sub="' . trim($sub_row->nama_klasifikasi) . '" data-klasifikasi="' . trim($row->nama_klasifikasi) . '" value="' . trim($sub_subrow->kode_klasifikasi) . '" ' . ((trim($sub_subrow->kode_klasifikasi) == trim($surat->kode_klasifikasi_arsip)) ? 'selected="selected"' : '') . ' > ' . trim($sub_subrow->kode_klasifikasi) . ' - ' . trim($sub_subrow->nama_klasifikasi) . '</option>';
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
<?php 
	$param['tembusan1'] = '';
	$param['tembusan2'] = '';
	$i = 0;
	foreach (json_decode($surat->distribusi) as $distribusi) {
		$tembusan = ($distribusi->unit) ? $distribusi->jabatan . ' ' . $distribusi->unit : '';
?>
					<div id="row_tembusan_<?php echo $i; ?>" class="form-group">
						<div class="col-sm-12">
							<div class="input-group">
								<input type="text" id="tembusan_ext_nama_<?php echo $i; ?>" name="tembusan[<?php echo $i; ?>]" class="form-control tembusan_ext" data-input-title="Tembusan <?php echo $i; ?>" value="<?php echo $tembusan; ?>" placeholder="Tembusan surat...">
								<input type="hidden" id="tembusan_to_ref_id" name="distribusi_tembusan[<?php echo $i; ?>][id]" value="<?php echo $distribusi->id; ?>">
								<input type="hidden" id="tembusan_ext_unit" name="distribusi_tembusan[<?php echo $i; ?>][unit]" value="<?php echo $distribusi->unit; ?>">
								<input type="hidden" id="tembusan_ext_unit_code" name="distribusi_tembusan[<?php echo $i; ?>][kode]" value="<?php echo $distribusi->kode; ?>">
								<input type="hidden" id="tembusan_ext_instansi" name="distribusi_tembusan[<?php echo $i; ?>][dir]" value="<?php echo $distribusi->dir; ?>">
								<input type="hidden" id="tembusan_ext_jabatan" name="distribusi_tembusan[<?php echo $i; ?>][jabatan]" value="<?php echo $distribusi->jabatan; ?>">
								<input type="hidden" id="tembusan_ext_nama" name="distribusi_tembusan[<?php echo $i; ?>][nama]" value="<?php echo $distribusi->nama; ?>">
								<input type="hidden" id="tembusan_ext_nip" name="distribusi_tembusan[<?php echo $i; ?>][nip]" value="<?php echo $distribusi->nip; ?>">
								<div class="input-group-btn">
									<button type="button" class="btn btn-danger" onclick="removeTembusan(<?php echo $i; ?>)" title="Hapus Tembusan..."><i class="fa fa-minus"></i></button>
								</div>
							</div>
						</div>
					</div>
<?php 
		$tembusanrow = $i;

		$i++;	
		
		if($tembusan != '') {
			$param['tembusan1'] .= $i . '. ' . $tembusan . '<br>';
			$param['tembusan2'] .= $i . '. ' . $tembusan . '<br>';
		}	
	}
	
	if($param['tembusan1'] != '') {
		$param['tembusan1'] = $param['tembusan1'];
	}

	if($param['tembusan2'] != '') {
		$param['tembusan2'] = 'Tembusan : <br>' . $param['tembusan2'];
	}
?>
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
				<div class="box-body">
					<div id="row_sign" class="form-group">
						<div class="col-sm-12">
<?php
	$cek_approval = json_decode($surat->approval, TRUE);

	$p = 1;
	$list = $this->surat_model->get_all_parents($surat->surat_from_ref_id);
	//$list = $this->surat_model->get_all_parents(get_user_data('unit_id'));
	
	$opt_sign = array();
	//$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	if($unit->ske_sign == 1) {
		$opt_sign[$unit->id] = $unit->jabatan . ' ' . $unit->value;
	}
	
	$approval_type = (in_array($unit->level, array('L0', 'L1'))) ? 'direksi' : 'direksi';
	$status_appr = (isset($cek_approval[$approval_type][$unit->id]['status'])) ? $cek_approval[$approval_type][$unit->id]['status'] : 0;
		
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][index]', $p++);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][unit_name]', $unit->value);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][user_id]', $unit->user_id);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nip]', $unit->nip_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][jabatan]', $unit->jabatan);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][nama_pejabat]', $unit->nama_pejabat);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][email]', $unit->email);
	echo form_hidden('approval[' . $approval_type. '][' . $unit->id . '][status]', $status_appr);
	echo form_hidden('approval[' . $approval_type. '][diskusi]', array());
	echo form_hidden('approval[' . $approval_type. '][status]', 0);
	
	foreach ($list as $parent) {
		$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
		// if($parent['ske_sign'] == 1) {
		// 	$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' '. $parent['unit_name'];
		// }
		
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
		echo form_hidden('approval[' . $approval_type. '][diskusi][]', array());
		echo form_hidden('approval[' . $approval_type. '][status]', 0);
	}

//	$approval = json_decode($surat->approval, TRUE);
//	if(isset($approval['direksi']['diskusi'])) {
//		echo form_hidden('approval[direksi][diskusi]', stripslashes(json_encode(array())));
//	}
//	if(isset($approval['direksi']['diskusi'])) {
//		echo form_hidden('approval[direksi][diskusi]', stripslashes(json_encode(array())));
//	}
	
	$signed = json_decode($surat->signed, TRUE);
	$param['signed|unit_id'] = $signed['unit_id'];
	$param['signed|unit_name'] = $signed['unit_name'];
	$param['signed|jabatan'] = $signed['jabatan'];
	$param['signed|pangkat'] = (isset($signed['pangkat'])) ? $signed['pangkat'] : '';
	$param['signed|nama_pejabat'] = $signed['nama_pejabat'];
	$param['signed|nip'] = $signed['nip'];
	$param['from_ref|unit'] = ucwords(strtolower($signed['unit_name']));
	echo form_dropdown('signed', $opt_sign, $signed['unit_id'], ' class="form-control"');
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
			<h3 class="box-title">Lampiran Surat</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" title="Tambah Lampiran.." onclick="addAttachment();"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div id="attachment_list" class="box-body form-group">
<?php 
	$last_att = 0;
	foreach ($attachment as $row) {
?>
			<input type="hidden" name="attachment[<?php echo $row->sort; ?>][id]" value="<?php echo $row->file_attachment_id; ?>">
			<input type="hidden" name="attachment[<?php echo $row->sort; ?>][file]" value="<?php echo $row->file; ?>">
			<input type="hidden" id="attachment_state_<?php echo $row->sort; ?>" name="attachment[<?php echo $row->sort; ?>][state]" value="-">
			<div id="attachment_<?php echo $row->sort; ?>" >
				<div class="col-md-7">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" class="btn btn-danger" onclick="removeAttachment(<?php echo $row->sort; ?>)" title="Hapus file..."><i class="fa fa-minus"></i></button>
						</div>
						<input type="text" name="attachment[<?php echo $row->sort; ?>][title]" class="form-control" placeholder="Judul File ..." id="title_<?php echo $row->sort; ?>" value="<?php echo $row->title; ?>">
						<span class="input-group-addon">
							<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a>
						</span>
					</div>
				</div>
				<div class="col-md-5" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
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
		$last_att = $row->sort;
	}
?>
		</div>
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
		$result = $this->admin_model->get_template_surat($function_ref_id, $surat->format_surat_id);
		
		if($result->num_rows() > 0) {
			$template = $result->row();
		} else {
			$template = new stdClass();
			$template->format_title = '';
			$template->format_text = '';
		}
		
		$month = array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
		$d = date('d', strtotime($surat->surat_awal));
		$m = $month[date('m', strtotime($surat->surat_awal)) - 1];
		$y = date('Y', strtotime($surat->surat_awal));
		$param['surat_awal'] = $d . ' ' . $m . ' ' . $y;
		
		$konsep_text = sprintformat($template->format_text, $param);
	//	$konsep_text = sprintformat($template->format_text, $surat->surat_ext_nama, $surat->surat_ext_title, humanize($surat->surat_to_unit), $surat->surat_to_jabatan, humanize($surat->surat_to_unit), $surat->surat_to_nama, '');
	}
?>
		</div>
		<div class="box-header with-border">
			<h3 class="box-title">
				Konsep Surat Nota Dinas
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
							<!-- <button type="button" class="btn btn-default" title="Hapus Konsep" onclick="removeKonsep();"><i class="glyphicon glyphicon-remove"></i></button> -->
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
	if($surat->status != 1) {
	$approval 		= json_decode($surat->approval, TRUE);
	$obj_approval 	= json_decode($surat->approval);
	
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
							<?php echo $appr['jabatan'] . ', ' . $appr['unit_name']; ?>
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
	}

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
<?php
			if($surat->status == 2 && $surat->surat_no == '{surat_no}' && in_array(get_role(), array(3, 5))) {
?>
					<button id="btn-set-no" type="button" class="btn btn-app" onclick="generateNomor();">
						<i class="fa fa-keyboard-o"></i> Set Nomor
					</button>
<?php 
			}

			if(has_permission(13) && ($surat->status >= 4)) {
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
				<div class="col-xs-6">
<?php

//			if((in_array($surat->status, array(0, 3, 5, 6))) || ($surat->status == 1 && $process_approved_L2) || ($surat->status == 2 && $process_approved_L1) || ($surat->status == 4 && $process_approved_TU)) {
// 				var_dump(has_permission($process->permission_handle));
			if(has_permission($process->permission_handle)) {
				if($process->modify == 1 || $process->button_return != '-' || $process->button_process != '-') {
					if($process->button_process != '-') {

						$approved = TRUE;
						if($surat->status == 1) {
							$approved = FALSE;
						}
						
						// if($surat->status == 2 && (isset($approval['direksi']) && $approval['direksi']['status'] == 0)) {
						// 	$approved = FALSE;
						// }

						if($get_konsep[0] == '--') {
							$approved = FALSE;
						}
?>
					<button type="button" class="btn btn-app pull-right bg-green <?php echo (!$approved) ? 'hide' : ''; ?>" onclick="prosesData();">
						<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
					</button>
<?php 
					}
					
					if($surat->status < 6) {
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
	}else {
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

		$('.surat_int_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				r = $(this).attr('data-row-id');
				$('#surat_int_kode_' + r).val(ui.item.unit_code);
				$('#surat_int_unit_kode_' + r).html(ui.item.unit_code);
				$('#surat_int_unit_id_' + r).val(ui.item.id);
				$('#surat_int_jabatan_' + r).val(ui.item.jabatan);
				$('#surat_int_nama_' + r).val(ui.item.nama_pejabat);
				$('#surat_int_dir_' + r).val(ui.item.instansi);
			}
		});
		
		$('.surat_int_unit').keyup(function() {
			if($(this).val().trim() == '') {
				r = $(this).attr('data-row-id');
				$('#surat_int_kode_' + r).val('');
				$('#surat_int_unit_kode_' + r).html('________');
				$('#surat_int_unit_id_' + r).val('');
				$('#surat_int_nama_' + r).val('');
				$('#surat_int_dir_' + r).val('');
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
				$('#tembusan_to_ref_id').val('');
				$('#tembusan_ext_unit').val('');
				$('#tembusan_ext_unit_code').val('');
				$('#tembusan_ext_jabatan').val('');
				$('#tembusan_ext_nama').val('');
				$('#tembusan_ext_instansi').val('');
				$('#tembusan_ext_nip').val('');
			}
		});
		
		klasifikasiChange();
		
	});

	function klasifikasiChange() {

		$('#klasifikasi').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi'));
		$('#klasifikasi_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub'));
		$('#klasifikasi_sub_sub').val($('#kode_klasifikasi_arsip option:selected').attr('data-klasifikasi_sub_sub'));
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

	var tujuanRow = penandatanganRow = tembusanRow = 0;

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
			source: '<?php echo site_url('global/admin/eksternal_autocomplete')?>',
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

//	var optSrtJab = '<?php //echo str_replace("\n", '', form_dropdown('surat_eksternal_ttd[%0%][type_ttd]', $opt_ttd, (set_value('surat_int_penandatangan') ? set_value('surat_int_penandatangan') : 'Penandatangan' ), (' id="surat_int_penandatangan_%0%" class="form-control" ')) ); ?>';
	
	tembusanRow = <?php echo $tembusanrow; ?>;
	
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

	var attachmentRow = <?php echo $last_att; ?>;

	function addAttachment() {
		attachmentRow++;

		row = '<input type="hidden" id="attachment_state_' + attachmentRow + '" name="attachment[' + attachmentRow + '][state]" value="insert">' +
			'<div id="attachment_' + attachmentRow + '" >' +
			'	<div class="col-md-7">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + attachmentRow + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="File ..." id="title_'+attachmentRow+'">' +
			'		</div>' +
			'	</div>' +
			'	<div class="col-md-5">' +
			'		<div class="form-group">' +
			'			<div class="btn btn-default btn-file">' +
			'				<i class="fa fa-paperclip"></i> ' +
			'				<input type="file" name="attachment_file_' + attachmentRow + '" onchange="$(\'#flabel_' + attachmentRow + '\').html($(this).val()); $(\'#title_' + attachmentRow + '\').val(getFilename($(this).val()));">' +
			'			</div>' +
			'			<label id="flabel_' + attachmentRow + '"></label>' +
			'		</div>' +
// 			'		<input type="file" name="attachment_file_' + attachmentRow + '" class="form-control">' +
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
							if($('#konsep_' + $('#konsep_surat_id').val()) != undefined) {
								$('#konsep_' + $('#konsep_surat_id').val()).html(CKEDITOR.instances.konsep_text.getData());
							} else {
								$('#all-konsep').append(
									'<div id="konsep_' + $('#konsep_surat_id').val() + '" class="active">' + CKEDITOR.instances.konsep_text.getData() + '</div>'
								);
							}
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

	function commentLevel(lid) {
		
		$.ajax({
			type: "POST",
			url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
<?php 
	if(in_array(get_role(), array(2, 4))) {
?>			
			data: {action: 'surat.surat_model.comment_approval', 
					ref_id: '<?php echo $surat->surat_id; ?>', 
					level: lid,
					note: $('#konsep_text_' + lid).val(), 
					function_ref_id: <?php echo $function_ref_id; ?>,
					status: ($('#approve_' + lid).is(':checked')) ? 1 : 0
				},
<?php 
	} else {
?>
			data: {action: 'surat.surat_model.comment_approval', 
					ref_id: '<?php echo $surat->surat_id; ?>',  
					level: lid,
					note: $('#konsep_text_' + lid).val(), 
					function_ref_id: <?php echo $function_ref_id; ?>
				},
<?php 
	}
?>
			success: function(data) {
				if(typeof(data.error) != 'undefined') {
					var addComment = '<dt><?php echo get_user_data('user_name'); ?></dt>' +
										'<dd>' + $('#konsep_text_' + lid).val() + '</dd>';
					$('#box-comment-' + lid + ' .list-comment').append(addComment);
					$('#konsep_text_' + lid).val('');
					bootbox.alert(data.message);
					if(typeof(data.reload) != 'undefined') {
						location.reload();
					}
					//eval(data.execute);
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
<?php 
//	}
?>
	}

<?php 
	if($surat->status > 1) {
?>
	function printSurat() {
		window.open('<?php echo site_url('surat/internal/cetak_surat/' . $surat->surat_id); ?>');
	}
<?php 
	}
		if($surat->status == 2 && $surat->surat_no == '{surat_no}' && in_array(get_role(), array(3, 5))) {
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
?>

</script>