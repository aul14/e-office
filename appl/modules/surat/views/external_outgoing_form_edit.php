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
	$approval = json_decode($surat->approval, TRUE);
	$param = (array) $surat;

	echo form_open_multipart('', ' id="form_surat_keluar" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('action', 'surat.surat_model.update_surat');
	echo form_hidden('surat_id', $surat->surat_id);
	//	echo form_hidden('surat_no', $surat->surat_no);
	echo form_hidden('surat_awal', db_to_human($surat->surat_awal));
	echo form_hidden('jenis_agenda', 'SKE');
	echo form_hidden('function_ref_id', 2);
	echo form_hidden('return', 'surat/external/outgoing_view/');

	echo form_hidden('surat_from_ref', 'internal');
	echo form_hidden('surat_from_ref_id', $surat->surat_from_ref_id);

	$result = $this->admin_model->get_ref_internal($surat->surat_from_ref_id);
	$unit = $result->row();

	echo form_hidden('official_code', $unit->official_code);
	echo form_hidden('surat_from_ref', $surat->surat_from_ref);
	echo form_hidden('surat_from_ref_data[kode]', $unit->unit_code);
	echo form_hidden('surat_from_ref_data[dir]', $unit->instansi);
	echo form_hidden('surat_from_ref_data[jabatan]', $unit->jabatan);
	echo form_hidden('surat_from_ref_data[pangkat]', $unit->pangkat);
	echo form_hidden('surat_from_ref_data[nama]', $unit->nama_pejabat);
	echo form_hidden('surat_from_ref_data[nip]', $unit->nip_pejabat);

	$result_surat_ref = $this->admin_model->get_ref_surat_masuk($surat->surat_id);
	$surat_ref_num = $result_surat_ref->num_rows();

	if ($surat_ref_num > 0) {
		$surat_from_ref = $result_surat_ref->row();
	} else {
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
					foreach ($flow as $row) {
						if ($row->flow_seq == $surat->status) {
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
					foreach ($flow_notes as $row) {
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
			<?php
			if ($surat->surat_no == '{surat_no}') {
				$param['surat_no'] = trim($surat->kode_klasifikasi_arsip) . '/_/RSUD-BLJ';

				$month = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
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

			if ($surat->status < 5) {
				echo form_hidden('surat_no', $surat->surat_no);
			}

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
					<div class="col-lg-6 col-sm-3">
						<input type="text" id="surat_no_ref" name="surat_no_ref" class="form-control" readonly="readonly" value="<?php echo $surat_from_ref->surat_no; ?>">
					</div>
					<label for="surat_tgl_ref" class="col-lg-2 col-sm-3 control-label">Tanggal Surat Ref</label>
					<div class="col-lg-2 col-sm-3">
						<input type="text" id="surat_tgl_ref" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat_from_ref->surat_tgl); ?>">
					</div>
				</div>
			<?php
			}
			?>
			<div class="form-group">
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
					<textarea id="surat_perihal" name="surat_perihal" class="form-control required" rows="3" placeholder="Perihal" data-input-title="Perihal"><?php echo $surat->surat_perihal; ?></textarea>
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
			$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
			$param['surat_to_ref_data|title'] 	 = $surat_to_ref_data['title'];
			$param['surat_to_ref_data|nama'] 	 = $surat_to_ref_data['nama'];
			$param['surat_to_ref_data|instansi'] = $surat_to_ref_data['instansi'];
			$param['surat_to_ref_data|alamat'] 	 = $surat_to_ref_data['alamat'];
			?>
			<input type="hidden" id="surat_to_ref_id" name="surat_to_ref_id" value="<?php echo $surat->surat_to_ref_id; ?>">
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
				<div id="list-tujuan" class="box-body">
					<div class="form-group">
						<label for="surat_ext_title" class="col-sm-3 control-label">Jabatan <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_title" name="surat_to_ref_data[title]" class="form-control required" data-input-title="Jabatan Tujuan" value="<?php echo $surat_to_ref_data['title']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_nama" name="surat_to_ref_data[nama]" class="form-control" data-input-title="Nama Tujuan" value="<?php echo $surat_to_ref_data['nama']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_instansi" class="col-sm-3 control-label">Instansi</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_instansi" name="surat_to_ref_data[instansi]" class="form-control required" data-input-title="Instansi Tujuan" value="<?php echo $surat_to_ref_data['instansi']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_alamat" class="col-sm-3 control-label">Alamat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_alamat" name="surat_to_ref_data[alamat]" class="form-control required" data-input-title="Alamat Tujuan" value="<?php echo $surat_to_ref_data['alamat']; ?>">
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
							<select name="kode_klasifikasi_arsip" id="kode_klasifikasi_arsip" class="form-control requiered" onchange="klasifikasiChange();">
								<option data-klasifikasi_sub_sub="" data-klasifikasi_sub="" data-klasifikasi="" value="">--</option>
								<?php
								$opt_klasifikasi = $this->admin_model->get_parent_klasifikasi_arsip(0);

								foreach ($opt_klasifikasi->result() as $row) {
									echo '<optgroup label="' . trim($row->kode_klasifikasi) . ' - ' . $row->nama_klasifikasi . '"></optgroup>';
									$opt_sub_klasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($row->entry_id);
									foreach ($opt_sub_klasifikasi->result() as $sub_row) {
										echo '<optgroup label=" > ' . trim($sub_row->kode_klasifikasi) . ' - ' . $sub_row->nama_klasifikasi . '">';
										$opt_sub_subklasifikasi = $this->admin_model->get_parent_klasifikasi_arsip($sub_row->entry_id);
										foreach ($opt_sub_subklasifikasi->result() as $sub_subrow) {
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
					foreach (json_decode($surat->tembusan) as $tembusan) {
					?>
						<div id="row_tembusan_<?php echo $i; ?>" class="form-group">
							<div class="col-sm-12">
								<div class="input-group">
									<input type="text" id="tembusan_<?php echo $i; ?>" name="tembusan[<?php echo $i; ?>]" class="form-control tembusan_ext" data-input-title="Tembusan <?php echo $i; ?>" value="<?php echo $tembusan; ?>" placeholder="Tembusan surat...">
									<div class="input-group-btn">
										<button type="button" class="btn btn-danger" onclick="removeTembusan(<?php echo $i; ?>)" title="Hapus Tembusan..."><i class="fa fa-minus"></i></button>
									</div>
								</div>
							</div>
						</div>
					<?php
						$i++;

						if ($tembusan != '') {
							$param['tembusan1'] .= $i . '. ' . $tembusan . '<br>';
							$param['tembusan2'] .= $i . '. ' . $tembusan . '<br>';
						}
					}

					if ($param['tembusan1'] != '') {
						$param['tembusan1'] = $param['tembusan1'];
					}

					if ($param['tembusan2'] != '') {
						$param['tembusan2'] = 'Tembusan <br>' . $param['tembusan2'];
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
							$list = $this->surat_model->get_all_parents_st($surat->surat_from_ref_id);
							//$list = $this->surat_model->get_all_parents(get_user_data('unit_id'));

							$opt_sign = array();
							if ($unit->ske_sign == 1) {
								$opt_sign[$unit->id] = $unit->value;
							}

							$approval_type = (in_array($unit->level, array('L0', 'L1'))) ? 'direksi' : 'non_direksi';
							$status_appr = (isset($cek_approval[$approval_type][$unit->id]['status'])) ? $cek_approval[$approval_type][$unit->id]['status'] : 0;

							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][index]', $p++);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][unit_name]', $unit->value);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][user_id]', $unit->user_id);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][nip]', $unit->nip_pejabat);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][jabatan]', $unit->jabatan);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][nama_pejabat]', $unit->nama_pejabat);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][email]', $unit->email);
							echo form_hidden('approval[' . $approval_type . '][' . $unit->id . '][status]', $status_appr);
							echo form_hidden('approval[' . $approval_type . '][diskusi]', json_encode(array()));
							echo form_hidden('approval[' . $approval_type . '][status]', 0);

							foreach ($list as $parent) {
								// if($parent['ske_sign'] == 1 && ($parent['level'] == 'L0' || $parent['level'] == 'L1')) {
								if ($parent['ske_sign'] == 1) {
									$opt_sign[$parent['organization_structure_id']] = $parent['jabatan'] . ' ' . $parent['unit_name'];
								}

								$approval_type = (in_array($parent['level'], array('L0', 'L1'))) ? 'direksi' : 'non_direksi';
								$status_appr_dir = (isset($cek_approval[$approval_type][$parent['organization_structure_id']]['status'])) ? $cek_approval[$approval_type][$parent['organization_structure_id']]['status'] : 0;

								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][index]', $p++);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][unit_name]', $parent['unit_name']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][user_id]', $parent['user_id']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][nip]', $parent['nip']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][jabatan]', $parent['jabatan']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][pangkat]', $parent['pangkat']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][nama_pejabat]', $parent['user_name']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][email]', $parent['email']);
								echo form_hidden('approval[' . $approval_type . '][' . $parent['organization_structure_id'] . '][status]', $status_appr_dir);
								echo form_hidden('approval[' . $approval_type . '][diskusi]', json_encode(array()));
								echo form_hidden('approval[' . $approval_type . '][status]', 0);
							}

							//	$approval = json_decode($surat->approval, TRUE);
							//	if(isset($approval['non_direksi']['diskusi'])) {
							// echo form_hidden('approval[non_direksi][diskusi]', stripslashes(json_encode(array())));
							//	}
							//	if(isset($approval['direksi']['diskusi'])) {
							// echo form_hidden('approval[direksi][diskusi]', stripslashes(json_encode(array())));
							//	}

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
				<div id="attachment_<?php echo $row->sort; ?>">
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
			if ($konsep->num_rows() > 0) {
				$opt_konsep = array();
				foreach ($konsep->result() as $row) {
					$opt_konsep[$row->konsep_surat_id] = $row->title . ' - Versi ' . $row->version;
					$get_konsep[0] = $row->title . ' - Versi ' . $row->version;
					if ($row->status == 1) {
						$active_konsep = $row->konsep_surat_id;
					}
			?>
					<div id="konsep_<?php echo $row->konsep_surat_id; ?>" data-version="<?php echo $row->version; ?>" class="<?php echo ($row->status == 1) ? 'active ' : ''; ?>"><?php echo $row->konsep_text; ?></div>
			<?php
				}

				$konsep_text = '';
			} else {
				$result = $this->admin_model->get_template_surat(2, $surat->format_surat_id);

				if ($result->num_rows() > 0) {
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
				Konsep Surat Keluar
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
					<textarea id="konsep_text" name="konsep_text"><?php echo $konsep_text; ?></textarea>
				</div>
			</div>
			<!-- </form> -->
		</div>
	</div>

	<?php
	if ($surat->status != 1 && $surat->status != 2) {  //verifikasi direksi
		$approval = json_decode($surat->approval, TRUE);
		$obj_approval = json_decode($surat->approval);

		if (($surat->status > 0) && (isset($approval['non_direksi']))) {
			$non_dir = array($surat->created_id);
			foreach ($approval['non_direksi'] as $ak => $appr) {
				if (isset($appr['unit_name'])) {
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
							if (!isset($approval['non_direksi']['diskusi'])) {
								$obj_approval->non_direksi->diskusi = new stdClass();
							}
							$this->load->view('diskusi', array('id' => 'non_direksi', 'function_handle' => 'surat.surat_model.set_diskusi', 'script_handle' => 'draft', 'ref_id' => $surat->surat_id, 'diskusi' => $obj_approval->non_direksi->diskusi, 'active' => ($approval['non_direksi']['status'] == 0 && in_array(get_user_id(), $non_dir))));
							?>
						</div>
						<div class="col-md-6">
							<?php
							foreach ($approval['non_direksi'] as $ak => $appr) {
								if (isset($appr['unit_name'])) {
									//echo $appr['user_id'];
							?>
									<div class="form-group">
										<label id="pejabat_<?php $ak; ?>" class="col-md-12">
											<input type="checkbox" value="1" <?php echo ($appr['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 1) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'non_direksi\', ' . $ak . ')"'; ?>>
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

		if ($surat->status > 1) {

			$dir = array($surat->created_id);
			foreach ($approval['direksi'] as $ak => $appr) {
				if (isset($appr['unit_name'])) {
					$dir[] = $appr['user_id'];
				}
			}
		?>
			<div id="box-comment-verifikasi" class="box box-primary <?php echo $surat->status == 2 ? '' : 'collapsed-box'; ?>">
				<div class="box-header with-border">
					<h3 class="box-title"> Verifikasi Direksi </h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa  <?php echo $surat->status == 2 ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<?php
							if (!isset($approval['direksi']['diskusi'])) {
								$obj_approval->direksi->diskusi = new stdClass();
							}
							$this->load->view('diskusi', array('id' => 'direksi', 'function_handle' => 'surat.surat_model.set_diskusi', 'script_handle' => 'verifikasi', 'ref_id' => $surat->surat_id, 'diskusi' => $obj_approval->direksi->diskusi, 'active' => ($approval['direksi']['status'] == 0 && in_array(get_user_id(), $dir))));
							?>
						</div>
						<div class="col-md-6">
							<?php
							foreach ($approval['direksi'] as $ak => $appr) {
								//			echo $appr['user_id'] . '<br>' . get_user_id();
								if (isset($appr['unit_name'])) {
							?>
									<div class="form-group">
										<label id="pejabat_<?php $ak; ?>" class="col-md-12">
											<input type="checkbox" value="1" <?php echo ($appr['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 2) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'direksi\', ' . $ak . ')"'; ?>>
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
	} //end verifikasi direksi

	if ($surat->status != 99) {
		if ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-') {
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
							if (has_permission($process->permission_handle)) {
								if (has_permission($process->permission_handle) && ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-')) {
									if ($process->button_process != '-') {

										$approved = TRUE;
										if ($surat->status == 1 && (isset($approval['non_direksi']) && $approval['non_direksi']['status'] == 0)) {
											$approved = FALSE;
										}

										if ($surat->status == 2 && (isset($approval['direksi']) && $approval['direksi']['status'] == 0)) {
											$approved = FALSE;
										}

										if ($get_konsep[0] == '--') {
											$approved = FALSE;
										}
							?>
										<button type="button" class="btn btn-app pull-right bg-green <?php echo (!$approved) ? 'hide' : ''; ?>" onclick="prosesData();">
											<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
										</button>
										<?php
									}

									if ($surat->status != 6) {
										$result = $this->disposisi_model->get_disposisi_from_ref('surat', $surat->surat_id);

										if ($process->button_return != '-' && ($result->num_rows() == 0)) {
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
		if (get_user_data('unit_id') == $surat->surat_int_unit_id && $surat->unit_archive_status != 99) {
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

		CKEDITOR.replace('konsep_text', {
			height: 500
		});
		<?php
		if ($konsep->num_rows() > 0) {
		?>
			CKEDITOR.instances.konsep_text.setData($('#konsep_' + $('#konsep_surat_id').val()).html());
		<?php
		}
		?>
		$('#surat_ext_title').autocomplete({
			source: '<?php echo site_url('global/admin/eksternal_autocomplete') ?>',
			minLength: 3,
			select: function(event, ui) {
				$('#surat_ext_nama').val(ui.item.nama_pejabat);
				$('#surat_ext_instansi').val(ui.item.instansi);
				$('#surat_ext_alamat').val(ui.item.address);
			}
		});

		$('#surat_ext_title').keyup(function() {
			if ($(this).val().trim() == '') {
				$('#surat_ext_nama').val('');
				$('#surat_ext_instansi').val('');
				$('#surat_ext_alamat').val('');
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

		klasifikasiChange();

	}); // end document

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

	var tujuanRow = 0;

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

	var tembusanRow = <?php echo count(json_decode($surat->tembusan)); ?>;

	function addTembusan() {

		tembusanRow++;

		var val_tembusan = '<?php echo set_value('tembusan_ext_nama'); ?>';

		row = '<div id="row_tembusan_' + tembusanRow + '" class="form-group">' +
			'	<div class="col-sm-12">' +
			'		<div class="input-group">' +
			'				<input type="text" id="tembusan_ext_nama_' + tembusanRow + '" name="tembusan[' + tembusanRow + ']" class="form-control tembusan_ext" data-input-title="Tembusan ' + tembusanRow + '" value="' + val_tembusan + '" placeholder="Tembusan surat...">' +
			'				<div class="input-group-btn">' +
			'					<button type="button" class="btn btn-danger" onclick="removeTembusan(' + tembusanRow + ')" title="Hapus Tembusan..."><i class="fa fa-minus"></i></button>' +
			'				</div>' +
			'			</div>' +
			'	</div>' +
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

	var attachmentRow = <?php echo $last_att; ?>;

	function addAttachment() {

		attachmentRow++;

		row = '<input type="hidden" id="attachment_state_' + attachmentRow + '" name="attachment[' + attachmentRow + '][state]" value="insert">' +
			'<div id="attachment_' + attachmentRow + '" >' +
			'	<div class="col-md-6">' +
			'		<div class="input-group">' +
			'			<div class="input-group-btn">' +
			'				<button type="button" class="btn btn-danger" onclick="removeAttachment(' + attachmentRow + ')" title="Hapus file..."><i class="fa fa-minus"></i></button>' +
			'			</div>' +
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="File ..." id="title_' + attachmentRow + '">' +
			'		</div>' +
			'	</div>' +
			'	<div class="col-md-6">' +
			'		<div class="form-group">' +
			'			<div class="btn btn-default btn-file">' +
			'				<i class="fa fa-paperclip"></i> Attachment' +
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
		bootbox.confirm('Buat konsep surat baru dengan format \'' + $("#format_surat_id option:selected").text() + '\'?', function(result) {
			if (result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {
						action: 'surat.surat_model.add_konsep',
						table: 'surat',
						ref_id: '<?php echo $surat->surat_id; ?>',
						format_surat_id: $('#format_surat_id').val(),
						format_surat_text: $("#format_surat_id option:selected").text()
					},
					success: function(data) {
						if (typeof(data.error) != 'undefined') {
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
		bootbox.confirm('Simpan Konsep Surat ini?', function(result) {
			if (result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {
						action: 'surat.surat_model.save_konsep',
						konsep_surat_id: $('#konsep_surat_id').val(),
						table: 'surat',
						ref_id: '<?php echo $surat->surat_id; ?>',
						format_surat_id: $('#format_surat_id').val(),
						format_surat_text: $("#format_surat_id option:selected").text(),
						konsep_text: CKEDITOR.instances.konsep_text.getData()
					},
					success: function(data) {
						if (typeof(data.error) != 'undefined') {
							bootbox.alert(data.message, function() {
								document.location.reload();
							});
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
		bootbox.confirm('Simpan Konsep Surat ini sebagai versi baru?', function(result) {
			if (result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {
						action: 'surat.surat_model.save_konsep_as',
						table: 'surat',
						ref_id: '<?php echo $surat->surat_id; ?>',
						format_surat_id: $('#format_surat_id').val(),
						format_surat_text: $("#format_surat_id option:selected").text(),
						konsep_text: CKEDITOR.instances.konsep_text.getData()
					},
					success: function(data) {
						if (typeof(data.error) != 'undefined') {
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
		bootbox.confirm('Hapus Konsep Surat ini?', function(result) {
			if (result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {
						action: 'surat.surat_model.remove_konsep',
						konsep_surat_id: $('#konsep_surat_id').val(),
						table: 'surat',
						ref_id: '<?php echo $surat->surat_id; ?>',
						format_surat_id: $('#format_surat_id').val(),
						format_surat_text: $("#format_surat_id option:selected").text()
					},
					success: function(data) {
						if (typeof(data.error) != 'undefined') {
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

	function returnData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.prompt({
			title: 'Kembalikan berkas.',
			inputType: 'textarea',
			callback: function(result) {
				if (result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {
							action: 'surat.surat_model.return_data',
							ref_id: '<?php echo $surat->surat_id; ?>',
							note: result,
							last_flow: <?php echo $last_flow; ?>,
							function_ref_id: <?php echo 2; ?>,
							flow_seq: <?php echo $surat->status; ?>
						},
						success: function(data) {
							if (typeof(data.error) != 'undefined') {
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
		//	if($process->check_field_function != '-') {
		/*
?>
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result){
			if(result) {
				location.assign('<?php echo site_url($process->check_field_function . '/' . $surat->surat_id); ?>');	 
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});

<?php
*/
		//	} else {
		?>
		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result) {
			if (result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {
						action: 'surat.surat_model.proses_data',
						ref_id: '<?php echo $surat->surat_id; ?>',
						note: result,
						last_flow: <?php echo $last_flow; ?>,
						function_ref_id: <?php echo 2; ?>,
						flow_seq: <?php echo $surat->status; ?>,
						function_handler: '<?php echo $process->check_field_function; ?>'
					},
					success: function(data) {
						if (typeof(data.error) != 'undefined') {
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
</script>