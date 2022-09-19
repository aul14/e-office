<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/wysiwyg_view.css">

<!-- Main content -->
<section class="content">
	<?php
	$param = (array) $surat;

	echo form_open_multipart('', ' id="form_surat_keluar" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('action', 'surat.eksternal_model.update_surat');
	echo form_hidden('surat_id', $surat->surat_id);
	//	echo form_hidden('surat_no', $surat->surat_no);
	echo form_hidden('surat_tgl', $surat->surat_tgl);
	echo form_hidden('jenis_agenda', 'SKE');
	echo form_hidden('function_ref_id', 2);
	echo form_hidden('return', 'surat/external/outgoing_view/');

	echo form_hidden('surat_from_ref', 'internal');
	echo form_hidden('surat_from_ref_id', $surat->surat_from_ref_id);

	$result = $this->admin_model->get_ref_internal($surat->surat_from_ref_id);
	$unit = $result->row();

	echo form_hidden('official_code', $unit->official_code);
	echo form_hidden('surat_from_ref_data[kode]', $unit->unit_code);
	echo form_hidden('surat_from_ref_data[dir]', $unit->instansi);
	echo form_hidden('surat_from_ref_data[jabatan]', $unit->jabatan);
	echo form_hidden('surat_from_ref_data[nama]', $unit->nama_pejabat);
	echo form_hidden('surat_from_ref_data[nip]', $unit->nip_pejabat);

	$result_surat_ref = $this->admin_model->get_ref_surat_masuk($surat->surat_id);
	$surat_ref_num = $result_surat_ref->num_rows();

	if ($surat_ref_num > 0) {
		$surat_from_ref = $result_surat_ref->row();
	} else {
		$surat_from_ref = '';
	}

	$level = '';
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
					$r_title = "";
					foreach ($flow as $row) {
						if ($row->flow_seq == $surat->status) {
							$flow_pos = 'btn-success';
							$r_title = $row->title;
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

			if ($surat_from_ref != '') {
				$surat_from_ref_data = json_decode($surat_from_ref->surat_from_ref_data, TRUE);
				$list_attachment = $this->admin_model->get_file_attachment('surat', $surat_from_ref->surat_id);

				if ($list_attachment->num_rows() > 0) {
					$attachment_ref = $list_attachment->result();
				} else {
					$attachment_ref = array();
				}
			?>
				<div class="form-group">
					<label for="asal_surat" class="col-lg-2 col-sm-3 control-label">Asal Surat</label>
					<div class="col-lg-10 col-sm-9">
						<input type="text" id="asal_surat" name="asal_surat" class="form-control" readonly="readonly" value="<?php echo $surat_from_ref_data['nama'] . ' ' . $surat_from_ref_data['title'] . ', ' . $surat_from_ref_data['instansi']; ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="asal_surat" class="col-lg-2 col-sm-3 control-label">Lampiran Surat</label>
					<div class="col-lg-10 col-sm-9">
						<?php
						$last_seq = 0;
						foreach ($attachment_ref as $row_ref) {
						?>
							<div id="attachment_<?php echo $row_ref->sort; ?>" class="col-md-12">
								<a href="<?php echo $row_ref->file; ?>" target="_blank" title="<?php echo $row_ref->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> <label> <?php echo $row_ref->title; ?> </label>
							</div>
						<?php
							$last_seq = $row_ref->sort;
						}
						?>
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
					<div class="input-group">
						<input type="text" id="surat_awal" class="form-control" disabled="disabled" value="<?php echo ($surat->surat_awal) ? db_to_human($surat->surat_awal) : ''; ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" class="col-lg-2 col-sm-3 control-label">Tanggal Surat</label>
				<div class="col-lg-3 col-sm-9">
					<div class="input-group">
						<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo ($surat->surat_tgl != '') ? db_to_human($surat->surat_tgl) : ''; ?>">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
							<?php echo (($surat->status >= 4 && $surat->status < 6) && $surat->surat_no != '{surat_no}') ? '<button type="button" onclick="ubahTglSurat();">edit</button>' : ''; ?>
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
					$list = $this->admin_model->get_template_surat(2);
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
			$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
			$param['surat_to_ref_data|title'] 	 	= $surat_to_ref_data['title'];
			$param['surat_to_ref_data|nama'] 	 	= $surat_to_ref_data['nama'];
			$param['surat_to_ref_data|instansi'] 	= $surat_to_ref_data['instansi'];
			$param['surat_to_ref_data|alamat'] 	 	= $surat_to_ref_data['alamat'];
			?>
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Tujuan Surat</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<?php
				if ($surat->status != 99) {
				?>
					<div id="list-tujuan" class="box-body" style="display: none;">
					<?php
				} else {
					?>
						<div id="list-tujuan" class="box-body" style="display: block;">
						<?php } ?>
						<div class="form-group">
							<label for="surat_ext_title" class="col-sm-3 control-label">Jabatan <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
							<div class="col-sm-9">
								<input type="text" id="surat_ext_title" name="surat_to_ref_data[title]" class="form-control" disabled="disabled" data-input-title="Jabatan Tujuan" value="<?php echo $surat_to_ref_data['title']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="surat_ext_nama" class="col-sm-3 control-label">Nama</label>
							<div class="col-sm-9">
								<input type="text" id="surat_ext_nama" name="surat_to_ref_data[nama]" class="form-control" disabled="disabled" data-input-title="Nama Tujuan" value="<?php echo $surat_to_ref_data['nama']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="surat_ext_instansi" class="col-sm-3 control-label">Instansi</label>
							<div class="col-sm-9">
								<input type="text" id="surat_ext_instansi" name="surat_to_ref_data[instansi]" class="form-control" disabled="disabled" data-input-title="Instansi Tujuan" value="<?php echo $surat_to_ref_data['instansi']; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="surat_ext_alamat" class="col-sm-3 control-label">Alamat</label>
							<div class="col-sm-9">
								<input type="text" id="surat_ext_alamat" name="surat_to_ref_data[alamat]" class="form-control" disabled="disabled" data-input-title="Alamat Tujuan" value="<?php echo $surat_to_ref_data['alamat']; ?>">
							</div>
						</div>
						</div><!-- /.box-body -->
					</div><!-- /.box -->
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
					<?php
					if ($surat->status != 99) {
					?>
						<div class="box-body" style="display: none;">
						<?php
					} else {
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
			<!-- 
	<div class="box box-primary">
		<div class="box-body">
			<button class="btn btn-app">
				<i class="fa fa-save"></i> Save
			</button>
		</div>
	</div> 
	-->
			<?php
			if ($surat->status != 99) {
			?>
				<div id="box-konsep" class="box box-primary">
					<div id="all-konsep" class="hide">
						<?php
						$opt_konsep = array(0 => '--');
						$active_konsep = '';
						$konsep_text = '';
						if ($konsep->num_rows() > 0) {
							$opt_konsep = array();
							foreach ($konsep->result() as $row) {
								$opt_konsep[$row->konsep_surat_id] = $row->title . ' - Versi ' . $row->version;

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
								$template->format_text 	= '';
							}

							$konsep_text = sprintformat($template->format_text, $param);
							//		$konsep_text = sprintformat($template->format_text, $surat->surat_ext_nama, $surat->surat_ext_title, humanize($surat->surat_int_unit), $surat->surat_int_jabatan, humanize($surat->surat_int_unit), $surat->surat_int_nama, '');
						}
						?>
					</div>
					<div class="box-header with-border">
						<h3 class="box-title">Konsep Surat Keluar</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
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
								<!-- textarea name="konsep_text"><?php echo $konsep_text; ?></textarea -->
							</div>
						</div>
					</div>
				</div>

				<?php
				$approval 	  = json_decode($surat->approval, TRUE);
				$obj_approval = json_decode($surat->approval);

				$appr_non_dir_status = 0;
				$appr_dir_status 	 = 0;

				if (($surat->status > 0) && (isset($approval['non_direksi']))) {
					$non_dir = array($surat->created_id);

					foreach ($approval['non_direksi'] as $ak => $appr) {
						if (isset($appr['unit_name'])) {
							$non_dir[] = $appr['user_id'];
						}
					}
				?>
					<div id="box-comment-draft" class="box box-primary <?php echo $surat->status == 1 ? '' : 'collapsed-box'; ?>">
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
											$appr_non_dir_status += $appr['status'];
									?>
											<div class="form-group">
												<label id="pejabat_<?php $ak; ?>" class="col-md-12">
													<input type="checkbox" value="1" <?php echo ($appr['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 1) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'non_direksi\', ' . $ak . ')"'; ?>>
													<?php echo $appr['jabatan'] . ' ' . $appr['unit_name']; ?>
												</label>
											</div>

											<!--
				<div class="form-group">
					<label class="col-lg-2 col-sm-3 control-label">Keterangan</label>
					<div class="col-lg-9 col-sm-3">
						<textarea name="approval_note_<?php echo $ak; ?>" id="approval_note_<?php echo $ak; ?>" rows="3" class="form-control" <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 1) ? 'disabled="disabled"' : ''; ?>></textarea>
						<button <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 1) ? 'disabled="disabled"' : ''; ?>>Submit</button>
					</div>
				</div>
				-->
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
										// var_dump(isset($appr['unit_name']) ? "true" : "false");
										// die;
										if (isset($appr['unit_name'])) {
											$appr_dir_status += $appr['status'];
									?>
											<div class="form-group">
												<label id="pejabat_<?php $ak; ?>" class="col-md-12">
													<input type="checkbox" value="1" <?php echo ($appr['status'] == 1) ? 'checked="checked"' : ''; ?> <?php echo ($appr['user_id'] != get_user_id() || $surat->status > 2) ? 'disabled="disabled"' : ' onclick="setApproved($(this), \'direksi\', ' . $ak . ')"'; ?>>
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
			}

			if ($surat->kirim_time) {
				?>
				<!-- Default box -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<span class="h3 box-title">Tanda Terima </span>
					</div>
					<div id="soft_copy_list" class="box-body">
						<div id="soft_copy_<?php echo $copy_surat->sort; ?>">
							<div class="col-md-12">
								<div class="form-group">
									<label id="flabel_soft_copy">
										<?php
										if ($copy_surat->file_attachment_id != '-') {
										?>
											<?php echo $copy_surat->file_name; ?>
											<a href="<?php echo $copy_surat->file; ?>" target="_blank" title="<?php echo $copy_surat->file_name; ?>"><i class="fa fa-file-text-o"></i></a>
										<?php
										}
										?>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Default box -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Pengiriman</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
						<?php
						list($agenda_date, $agenda_time) = explode(' ', $surat->kirim_time);
						$agenda_date = db_to_human($agenda_date);
						?>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="surat_no" class="col-sm-3 control-label">Agenda</label>
									<div class="col-sm-9">
										<div class="input-group">
											<div class="input-group-addon"><?php echo strtoupper($surat->jenis_agenda); ?></div>
											<input type="text" id="agenda_id" name="agenda_id" class="form-control" disabled="disabled" value="<?php echo $surat->agenda_id; ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="col-sm-12">
									<div class="input-group">
										<input type="text" id="created_time" name="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date; ?>" style="text-align: right;">
										<div class="input-group-addon"><?php echo $agenda_time; ?></div>
									</div>
								</div>
							</div>
						</div>
						<?php
						$distribusi = json_decode($surat->distribusi, TRUE);
						?>
						<div class="col-md-6">
							<div class="form-group">
								<label for="kirim_time" class="col-sm-3 control-label">Tanggal</label>
								<div class="col-sm-9">
									<div class="input-group">
										<input type="text" id="kirim_time" name="kirim_time" class="form-control datetimepicker " disabled="disabled" data-input-title="Tgl Kirim" value="<?php echo date('d-m-Y H:i'); ?>">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="catatan_pengiriman-cara_pengiriman" class="col-sm-3 control-label">Cara Pengiriman</label>
								<div class="col-sm-9">
									<?php
									$opt_pengiriman = $this->admin_model->get_system_config('delivery_method');
									echo form_dropdown('distribusi[cara_pengiriman]', $opt_pengiriman, (isset($distribusi['cara_pengiriman']) ? $distribusi['cara_pengiriman'] : ''), (' id="distribusi-cara_pengiriman" class="form-control" disabled="disabled" data-input-title="Cara Pengiriman" onchange="caraChange($(this).val());"'));
									?>
								</div>
							</div>
							<div class="form-group">
								<label for="catatan_pengiriman-catatan" class="col-sm-3 control-label">Catatan</label>
								<div class="col-sm-9">
									<textarea id="catatan_pengiriman" name="catatan_pengiriman" class="form-control required" rows="2" disabled="disabled" placeholder="Catatan Pengiriman" data-input-title="Catatan"><?php echo $surat->catatan_pengiriman; ?></textarea>
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<?php
							foreach ($opt_pengiriman as $k => $v) {
							?>
								<div id="cara_pengiriman-<?php echo $k; ?>" class="extra-param hide">
									<?php
									$extra_param = $this->admin_model->get_system_config('delivery_param_' . $k);

									foreach ($extra_param as $p_k => $p_v) {
									?>
										<div class="form-group">
											<label for="catatan_pengiriman-<?php echo $p_k; ?>" class="col-sm-3 control-label"><?php echo $p_v; ?></label>
											<div class="col-sm-9">
												<input type="text" id="distribusi-<?php echo $p_k; ?>" name="distribusi[<?php echo $p_k; ?>]" class="form-control" placeholder="<?php echo $p_v; ?>" data-input-title="<?php echo $p_v; ?>" data-default="<?php echo (isset($distribusi[$p_k]) ? $distribusi[$p_k] : ''); ?>" disabled="disabled" value="<?php echo (isset($distribusi[$p_k]) ? $distribusi[$p_k] : ''); ?>">
											</div>
										</div>
									<?php
									}
									?>
								</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}

			echo form_close();
			?>
			<div class="fixed-box-btn"></div>
			<div id="box-process-btn" class="box box-primary">
				<div class="box-body">
					<div class="row">
						<div class="col-xs-4">
							<?php
							$enEdit = FALSE;
							if (!has_permission(1)) {
								if ($surat->status != 99) {
									//			if((get_role() == 4 && $surat->status <= 2) || (get_role() == 5 && $surat->status <= 2) || (get_role() == 3 && $surat->status == 3)) {
									// if($surat->status != 1 || !has_permission(9)) {
									// if ($process->modify == 1) {
									if (has_permission($process->permission_handle) && $process->modify == 1) {
										$enEdit = TRUE;
									}
								}
							} else {
								if ($surat->status != 99) {
									$enEdit = TRUE;
								}
							}

							if ($enEdit) {
							?>
								<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/external/outgoing/' . $surat->surat_id); ?>');">
									<i class="fa fa-edit"></i> Edit
								</button>
							<?php
							}

							if ($surat->status >= 1 && $surat->status != 99) {
							?>
								<?php if ($r_title == 'Pemberian Nomor' || $r_title == "Kirim" || $r_title == "Selesai") : ?>
									<button type="button" class="btn btn-app" onclick="printSuratDigital();">
										<i class="fa fa-print"></i> Cetak
									</button>
								<?php else : ?>
									<button type="button" class="btn btn-app" onclick="printSurat();">
										<i class="fa fa-print"></i> Cetak
									</button>
								<?php endif; ?>
							<?php
							}

							if ($surat->status == 4 && $surat->surat_no == '{surat_no}' && $process->role_handle == get_role()) {
							?>
								<button id="btn-set-no" type="button" class="btn btn-app" onclick="generateNomor();">
									<i class="fa fa-keyboard-o"></i> Set Nomor
								</button>
							<?php
							}

							if ($surat->status == 5 && has_permission($process->permission_handle)) {
							?>
								<button id="btn-set-no" type="button" class="btn btn-app" onclick="sendSurat();">
									<i class="fa fa-send"></i> Kirim Surat
								</button>
							<?php
							}
							?>
						</div>
						<div class="col-xs-8">
							<?php
							if ($surat->status != 99) {
								if (has_permission($process->permission_handle)) {
									//		if($process->role_handle == get_role() && ($process->modify == 1  || $process->button_return != '-' || $process->button_process != '-')) {
									if ($process->button_process != '-') {
										$signed = json_decode($surat->signed, TRUE);
										if ($signed['jabatan'] == 'Direktur') {
											$level = 'L0';
										} else {
											$level = 'L1';
										}

										$approved = FALSE;
										if ($surat->status == 1 && (isset($approval['non_direksi']) && $approval['non_direksi']['status'] == 1)) {
											$approved = TRUE;
										}

										if ($level == 'L0') {
											if ($surat->status == 1 && $appr_non_dir_status >= 1) {
												$approved = TRUE;
											}
										} else {
											if ($surat->status == 1 && $appr_non_dir_status >= 1) {
												$approved = TRUE;
											}
										}

										if ($level == 'L0') {
											if ($surat->status == 2 && $appr_dir_status == 1) {
												$approved = TRUE;
											}
										} else {
											if ($surat->status == 2) {
												$approved = TRUE;
											}
										}

										if ($surat->status == 2 && (in_array($surat->surat_from_ref_id, array(1, 2, 10)))) {
											$approved = TRUE;
										}

										if ($surat->status == 3 && ($surat->surat_from_ref_id == get_user_data('unit_id') || has_permission(7))) {
											$approved = TRUE;
										}

										if ($surat->status == 4 && ($surat->surat_no != '{surat_no}')) {
											$approved = TRUE;
										}

										if ($surat->kirim_time != '' || isset($surat->kirim_time)) {
											$approved = TRUE;
										}

										// if(in_array($surat->status, array(6))) {
										// 	$approved = TRUE;
										// }
							?>
										<?php if ($r_title != 'Tanda Tangan Surat') : ?>
											<button id="btn-process" type="button" class="btn btn-app pull-right bg-green <?php echo (!$approved) ? 'hide' : ''; ?>" onclick="prosesData();">
												<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
											</button>
										<?php else : ?>
											<button id="btn-process" type="button" class="btn btn-app pull-right bg-green <?php echo (!$approved) ? 'hide' : ''; ?>" onclick="prosesDataDigital();">
												<i class="fa fa-caret-square-o-right"></i> <?php echo $process->button_process; ?>
											</button>
										<?php endif; ?>
									<?php
									}
								}

								if ($surat->status <= 6 || get_role() == 6) {
									//			$result = $this->disposisi_model->get_disposisi_from_ref('surat', $surat->surat_id);
									//			if($process->button_return != '-' && ($result->num_rows() == 0)) {
									if ($process->button_return != '-') {
									?>
										<button id="btn-return" type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
											<i class="fa fa-caret-square-o-left"></i> <?php echo $process->button_return; ?>
										</button>
									<?php
									}
								}

								if ($surat->status >= 6 && $surat->status < 99) {
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


</section><!-- /.content -->

<script type="text/javascript">
	$(document).ready(function() {
		//console.log('#konsep_surat_id : ' + $('#konsep_surat_id').val());

		viewKonsep($('#konsep_surat_id').val());
	});

	function initPage() {
		$('#konsep_text').html($('#konsep_' + $('#konsep_surat_id').val()).html());

		<?php
		if ($surat->kirim_time) {
		?>
			caraChange('<?php echo $distribusi['cara_pengiriman']; ?>');
		<?php
		}
		?>
	}

	<?php
	if ($surat->kirim_time) {
	?>

		function caraChange(v) {
			$('.extra-param').addClass('hide');
			$('.extra-param').find('input').each(function() {
				$(this).val($(this).attr('data-default'))
			});

			$('#cara_pengiriman-' + v).removeClass('hide');
		}

	<?php
	}
	?>

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
				data: {
					action: 'surat.surat_model.set_approve',
					ref_id: '<?php echo $surat->surat_id; ?>',
					function_ref_id: '<?php echo 2; ?>',
					distribusi_id: cid,
					unit_id: uid,
					approval: ap
				},
				success: function(data) {
					if (typeof(data.error) != 'undefined') {
						if (data.error == 0) {
							bootbox.alert(data.message, function() {
								document.location.reload();
							});
						} else {
							bootbox.alert(data.message);
						}
					} else {
						bootbox.alert("Data transfer error!");
					}
				}
			});
		}

		function BootboxDate() {
			var frm_str = '<form id="some-form">' +
				'<div class="form-group">' +
				'<label for="date">Tgl. Surat</label>' +
				'<div class="input-group">' +
				'<input id="date" class="date form-control input-sm" size="16" placeholder="dd-mm-yyyy" type="text">' +
				'<div class="input-group-addon">' +
				'<i title="Format dd-mm-yyyy" class="fa fa-calendar"></i>' +
				'</div>' +
				'</div></div>' +
				'</form>';

			var object = $('<div/>').html(frm_str).contents();

			object.find('.date').datepicker({
				dateFormat: 'dd-mm-yy',
				autoclose: true
			}).on('changeDate', function(ev) {
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

							if (result) {
								$.ajax({
									type: "POST",
									url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
									data: {
										action: 'surat.surat_model.ubah_tgl',
										ref_id: '<?php echo $surat->surat_id; ?>',
										surat_tgl: tgl_surat,
										last_flow: <?php echo $last_flow; ?>,
										function_ref_id: <?php echo 2; ?>,
										flow_seq: <?php echo $surat->status; ?>
									},
									success: function(data) {
										if (typeof(data.error) != 'undefined') {
											eval(data.execute);
										} else {
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

		function prosesDataDigital() {
			$('#box-process-btn .overlay').removeClass('hide');
			<?php
			if ($surat->status == 2 && $level == 'L1') {
				$check_field_info = $process->check_field_info . ' Wakil Direktur';
			} else if ($surat->status == 2 && $level == 'L0') {
				$check_field_info = $process->check_field_info . ' Direktur';
			} else {
				$check_field_info = $process->check_field_info;
			}
			?>
			bootbox.confirm('<?php echo $check_field_info; ?>', function(result) {
				if (result) {
					console.log("test");
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {
							action: 'surat.surat_model.post_test',
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
					// $.ajax({
					// 	type: "POST",
					// 	url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					// 	data: {
					// 		action: 'surat.surat_model.proses_data',
					// 		ref_id: '<?php echo $surat->surat_id; ?>',
					// 		note: result,
					// 		last_flow: <?php echo $last_flow; ?>,
					// 		function_ref_id: <?php echo 2; ?>,
					// 		flow_seq: <?php echo $surat->status; ?>,
					// 		function_handler: '<?php echo $process->check_field_function; ?>'
					// 	},
					// 	success: function(data) {
					// 		console.log(data);
					// 		if (typeof(data.error) != 'undefined') {
					// 			eval(data.execute);
					// 		} else {
					// 			bootbox.alert("Data transfer error!");
					// 			$('#box-process-btn .overlay').addClass('hide');
					// 		}
					// 	}
					// });
				} else {
					$('#box-process-btn .overlay').addClass('hide');
				}
			});
			<?php
			//	}
			?>
		}

		function prosesData() {
			$('#box-process-btn .overlay').removeClass('hide');
			<?php
			if ($surat->status == 2 && $level == 'L1') {
				$check_field_info = $process->check_field_info . ' Wakil Direktur';
			} else if ($surat->status == 2 && $level == 'L0') {
				$check_field_info = $process->check_field_info . ' Direktur';
			} else {
				$check_field_info = $process->check_field_info;
			}
			?>
			bootbox.confirm('<?php echo $check_field_info; ?>', function(result) {
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

		<?php
		if ($surat->status >= 1) {
		?>
			<?php if ($r_title == 'Pemberian Nomor' || $r_title == "Kirim" || $r_title == "Selesai") : ?>

				function printSuratDigital() {
					window.open('<?php echo site_url('surat/external/cetak_surat_eksternal_esign/' . $surat->surat_id); ?>');
				}
			<?php else : ?>

				function printSurat() {
					window.open('<?php echo site_url('surat/external/cetak_surat_eksternal/' . $surat->surat_id); ?>');
				}
			<?php endif; ?>
		<?php
		}

		if ($surat->status == 4 && $surat->surat_no == '{surat_no}') {
		?>

			function generateNomor() {
				$('#box-process-btn .overlay').removeClass('hide');
				bootbox.confirm('Buat Nomor Surat?', function(result) {
					if (result) {
						$.ajax({
							type: "POST",
							url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
							data: {
								action: 'surat.surat_model.get_current_eksternal_no',
								ref_id: '<?php echo $surat->surat_id; ?>',
								function_ref_id: <?php echo 2; ?>
							},
							success: function(data) {
								if (typeof(data.error) != 'undefined') {
									$('#surat_no').val(data.surat_no);
									$('#surat_tgl').val(data.surat_tgl);
									$('#btn-process').removeClass('hide');
									$('#btn-set-no').addClass('hide');

									bootbox.alert(data.message, function() {
										document.location.reload();
									});
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
		if ($surat->status == 5 && has_permission($process->permission_handle)) {
		?>

			function sendSurat() {
				location.assign('<?php echo site_url('surat/external/kirim_surat_keluar/' . $surat->surat_id); ?>');
			}

	<?php
		}
	}
	?>

	function prosesArsipUnit() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Simpan sebagai Arsip?', function(result) {
			if (result) {
				location.assign('<?php echo site_url('surat/external/register_arsip/' . $surat->surat_id); ?>');
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}
</script>