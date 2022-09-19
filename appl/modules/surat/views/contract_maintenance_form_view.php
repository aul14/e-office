<?php 

	list($agenda_date, $agenda_time) = explode(' ', $surat->created_time);
	list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
	$agenda_date = db_to_human($agenda_date);
	
	$add_id = 0;
	$result = $this->kontrak_model->get_addendum($surat->surat_id);
	if($result->num_rows() > 0) {
		$addendum = $result->row();
		$add_id = $addendum->addendum_id;
	}
?>
<style>

</style>
<section class="content">
<?php
	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'edit');
	echo form_hidden('surat_id', $surat->surat_id);
	if ($surat->status != 0)
	{
	echo form_hidden('addendum_id', $add_id);
	}
	
	echo form_hidden('function_ref_id', $function_ref_id); 
	echo form_hidden('function_ref_name', 'Contract Maintenance'); 
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
			<h3 class="box-title">Identitas Kontrak</h3>
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
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Kontrak</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_no" class="form-control" disabled="disabled" value="<?php echo $surat->surat_no; ?>">
					</div>
				<label for="surat_item_lampiran" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Kontrak <br> (dd-mm-yyyy)</label>
					<div class="col-lg-4 col-sm-9">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
						<input type="text" id="surat_unit_lampiran" class="form-control" disabled="disabled" value="<?php echo $surat->surat_unit_lampiran; ?>">
					</div>
			</div>
			
			<div class="form-group">
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Kode Kontrak</label>
					
<?php 
	$opt_kode_kontrak = $this->kontrak_model->get_referensi_full('kode_kontrak');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="status_berkas" name="status_berkas" disabled="disabled" class="form-control" data-input-title="sifat_surat" value="<?php echo ($surat->sifat_surat != '-') ? $opt_kode_kontrak[$surat->sifat_surat] : '-'; ?>">
				</div>
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Kontrak</label>
<?php 
	$opt_jenis_kontrak = $this->kontrak_model->get_referensi_full('jenis_kontrak');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="status_berkas" name="status_berkas" disabled="disabled" class="form-control" data-input-title="jenis_surat" value="<?php echo ($surat->jenis_surat != '-') ? $opt_jenis_kontrak[$surat->jenis_surat] : '-'; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label"> Mitra</label>
<?php 
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
?>
				<div class="col-lg-4 col-sm-9">
						<input type="text" id="status_berkas" name="status_berkas" disabled="disabled" class="form-control" data-input-title="status_berkas" value="<?php echo ($surat->status_berkas != '-') ? $opt_mitra[$surat->status_berkas] : '-'; ?>">
				</div>
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Nilai Kontrak</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="price" class="form-control" disabled="disabled" value="<?php echo $surat->surat_ringkasan; ?>">
					</div>
			</div>
			
			<div class="form-group">
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Berlaku <br> (dd-mm-yyyy)</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl); ?>">
					</div>
				<label for="surat_tgl_masuk" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label" valign= "middle">Tgl. Berakhir <br> (dd-mm-yyyy)</label>
<?php	
					if ($surat->status == 0){						
						$tgl_akhir = new dateTime($surat->surat_akhir);
						$tgl_skrng = new DateTime();
						$diff = $tgl_akhir->diff($tgl_skrng);
						$diff;
						if ($diff->days <= 30) {
?>								
									<div class="col-lg-4 col-sm-9 has-error">
									<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
									<label class="control-label" for="inputError"><i class="fa fa-bell-o"></i> <?php echo ($tgl_akhir > $tgl_skrng) ? $diff->days : "0" ?> Hari Lagi</label>
									</div>
<?php 							
						} else { 
							if ($diff->days <= 90) {
?>
									<div class="col-lg-4 col-sm-9 has-warning">
									<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
									<label class="control-label" for="inputWarning"><i class="fa fa-bell-o"></i> Kurang 3 Bulan Lagi</label>
									</div>									
<?php	 							
							} else {
								if ($diff->days <= 180){
?>
										<div class="col-lg-4 col-sm-9 has-success">
										<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
										<label class="control-label" for="inputSuccess"><i class="fa fa-bell-o"></i> Kurang 6 Bulan Lagi</label>
										</div>
<?php
								}else {
?>
										<div class="col-lg-4 col-sm-9">
										<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
										</div>
<?php
								}
							}
						}
					} else {
?>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl_masuk); ?>">
					</div>
<?php
					}
?>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled" rows="3" ><?php echo $surat->surat_perihal; ?></textarea>
				</div>
			</div>
		</div>
	</div><!-- /.box-body -->
			
<?php 
		$add_id = 0;
		$result = $this->kontrak_model->get_addendum($surat->surat_id);
		if($result->num_rows() > 0) {
		$addendum = $result->row();
		$add_id = $addendum->addendum_id;
		
		list($agenda_date, $agenda_time) = explode(' ', $addendum->created_time);
		list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
		$agenda_date = db_to_human($agenda_date);
		
		if ($surat->status != 0) {
?>
		<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Identitas Addendum 1</h3>
		</div>

		<div class="box-body">
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 col-xs-12 control-label">Agenda</label>
				<div class="col-lg-4 col-sm-4 col-xs-6">
					<input type="text" id="agenda_id" class="form-control" disabled="disabled" value="<?php echo strtoupper($addendum->jenis_agenda) . ' - ' . $addendum->agenda_id; ?>">
				</div>
				<div class="col-lg-6 col-sm-5 col-xs-6">
					<input type="text" id="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date . ' ' . $agenda_hours; ?>" style="text-align: right;">
				</div>
			</div>
			
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Addendum</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_no" class="form-control" disabled="disabled" value="<?php echo $addendum->surat_no; ?>">
					</div>
				<label for="surat_item_lampiran" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tanggal Addendum <br> (dd-mm-yyyy)</label>
					<div class="col-lg-4 col-sm-9">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
						<input type="text" id="surat_unit_lampiran" class="form-control" disabled="disabled" value="<?php echo $addendum->surat_unit_lampiran; ?>">
					</div>
			</div>
			
			<div class="form-group">
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Kode Addendum</label>
					
<?php 
	$opt_kode_add = $this->admin_model->get_system_config('kode_add');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="sifat_surat" name="sifat_surat" disabled="disabled" class="form-control" data-input-title="sifat_surat" value="<?php echo ($addendum->sifat_surat != '-') ? $opt_kode_add[$addendum->sifat_surat] : '-'; ?>">
				</div>
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Kontrak</label>
<?php 
	$opt_jenis_add = $this->admin_model->get_system_config('jenis_add');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="jenis_surat" name="jenis_surat" disabled="disabled" class="form-control" data-input-title="jenis_surat" value="<?php echo ($addendum->jenis_surat != '-') ? $opt_jenis_add[$addendum->jenis_surat] : '-'; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label"> Mitra</label>
<?php 
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
?>
				<div class="col-lg-4 col-sm-9">
						<input type="text" id="status_berkas" name="status_berkas" disabled="disabled" class="form-control" data-input-title="status_berkas" value="<?php echo ($surat->status_berkas != '-') ? $opt_mitra[$surat->status_berkas] : '-'; ?>">
				</div>
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Nilai Kontrak</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="price" class="form-control" disabled="disabled" value="<?php echo $addendum->surat_ringkasan; ?>">
					</div>
			</div>
			<div class="form-group">
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Berlaku <br> (dd-mm-yyyy)</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum->surat_tgl); ?>">
					</div>
				<label for="surat_tgl_masuk" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tanggal Berakhir <br> (dd-mm-yyyy)</label>
<?php	
					if ($surat->status == 1){
						
								$tgl_akhir = new dateTime($surat->surat_akhir);
								$tgl_skrng = new DateTime();
								$diff = $tgl_akhir->diff($tgl_skrng);
								$diff;
								if ($diff->days <= 30)
								{
?>								
									<div class="col-lg-4 col-sm-9 has-error">
									<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum->surat_tgl_masuk); ?>">
									<label class="control-label" for="inputError"><i class="fa fa-bell-o"></i> Kurang 1 Bulan Lagi</label>
									</div>
									
									
<?php 							} else
								{ if ($diff->days <= 90)
									{
?>
									<div class="col-lg-4 col-sm-9 has-warning">
									<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum->surat_tgl_masuk); ?>">
									<label class="control-label" for="inputWarning"><i class="fa fa-bell-o"></i> Kurang 3 Bulan Lagi</label>
									</div>
									
<?php	 							}
									else{
										if ($diff->days <= 180){
?>
										<div class="col-lg-4 col-sm-9 has-success">
										<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum->surat_tgl_masuk); ?>">
										<label class="control-label" for="inputSuccess"><i class="fa fa-bell-o"></i> Kurang 6 Bulan Lagi</label>
										</div>
										
<?php
										}
										else {
?>
										<div class="col-lg-4 col-sm-9">
										<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum->surat_tgl_masuk); ?>">
										</div>
<?php
										}
									}

								}
							
						}else {
?>
					<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum->surat_tgl_masuk); ?>">
					</div>
<?php
					}
?>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled" rows="3" ><?php echo $addendum->surat_perihal; ?></textarea>
				</div>
			</div>
		</div>
	</div><!-- /.box-body -->
<?php
		} 
	}
		
		$add_id2 = 0;
		$result = $this->kontrak_model->get_addendum2($surat->surat_id);
		if($result->num_rows() > 0) {
		$addendum2 = $result->row();
		$add_id2 = $addendum2->addendum_id;
		
		list($agenda_date, $agenda_time) = explode(' ', $addendum2->created_time);
		list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
		$agenda_date = db_to_human($agenda_date);
	
		if ($surat->status >= 2){
?>
		<div class="box box-primary">
			<div class="box-header with-border">
			<h3 class="box-title">Identitas Addendum 2</h3>
			</div>

		<div class="box-body">
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 col-xs-12 control-label">Agenda</label>
				<div class="col-lg-4 col-sm-4 col-xs-6">
					<input type="text" id="agenda_id" class="form-control" disabled="disabled" value="<?php echo strtoupper($addendum2->jenis_agenda) . ' - ' . $addendum2->agenda_id; ?>">
				</div>
				<div class="col-lg-6 col-sm-5 col-xs-6">
					<input type="text" id="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date . ' ' . $agenda_hours; ?>" style="text-align: right;">
				</div>
			</div>
			
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Addendum</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_no" class="form-control" disabled="disabled" value="<?php echo $addendum2->surat_no; ?>">
					</div>
				<label for="surat_item_lampiran" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl Addendum <br> (dd-mm-yyyy)</label>
					<div class="col-lg-4 col-sm-9">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
						<input type="text" id="surat_unit_lampiran" class="form-control" disabled="disabled" value="<?php echo $addendum2->surat_unit_lampiran; ?>">
					</div>
			</div>
			
			<div class="form-group">
				<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Kode Addendum</label>
					
<?php 
	$opt_kode_add = $this->admin_model->get_system_config('kode_add');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="sifat_surat" name="sifat_surat" disabled="disabled" class="form-control" data-input-title="sifat_surat" value="<?php echo ($addendum2->sifat_surat != '-') ? $opt_kode_add[$addendum2->sifat_surat] : '-'; ?>">
				</div>
				
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Kontrak</label>
<?php 
	$opt_jenis_add = $this->admin_model->get_system_config('jenis_add');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="jenis_surat" name="jenis_surat" disabled="disabled" class="form-control" data-input-title="jenis_surat" value="<?php echo ($addendum2->jenis_surat != '-') ? $opt_jenis_add[$addendum2->jenis_surat] : '-'; ?>">
				</div>
				
			</div>
	
			<div class="form-group">
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label"> Mitra</label>
<?php 
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
?>
				<div class="col-lg-4 col-sm-9">
						<input type="text" id="status_berkas" name="status_berkas" disabled="disabled" class="form-control" data-input-title="status_berkas" value="<?php echo ($surat->status_berkas != '-') ? $opt_mitra[$surat->status_berkas] : '-'; ?>">
				</div>
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Nilai Kontrak</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="price" class="form-control" disabled="disabled" value="<?php echo $addendum2->surat_ringkasan; ?>">
					</div>
			</div>
			
			<div class="form-group">
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Berlaku <br> (dd-mm-yyyy)</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum2->surat_tgl); ?>">
					</div>
				<label for="surat_tgl_masuk" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Berakhir <br> (dd-mm-yyyy)</label>
				
<?php	
					if ($surat->status == 2){
						
						$tgl_akhir = new dateTime($surat->surat_akhir);
						$tgl_skrng = new DateTime();
						$diff = $tgl_akhir->diff($tgl_skrng);
						$diff;
						if ($diff->days <= 30) {
?>								
									<div class="col-lg-4 col-sm-9 has-error">
									<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum2->surat_tgl_masuk); ?>">
									<label class="control-label" for="inputError"><i class="fa fa-bell-o"></i> Kurang 1 Bulan Lagi</label>
									</div>
<?php 							
						} else { 
							if ($diff->days <= 90) {
?>
									<div class="col-lg-4 col-sm-9 has-warning">
									<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum2->surat_tgl_masuk); ?>">
									<label class="control-label" for="inputWarning"><i class="fa fa-bell-o"></i> Kurang 3 Bulan Lagi</label>
									</div>
<?php	 					
							} else {
								if ($diff->days <= 180){
?>
									<div class="col-lg-4 col-sm-9 has-success">
									<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum2->surat_tgl_masuk); ?>">
									<label class="control-label" for="inputSuccess"><i class="fa fa-bell-o"></i> Kurang 6 Bulan Lagi</label>
									</div>
<?php
								}else {
?>
										<div class="col-lg-4 col-sm-9">
										<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum2->surat_tgl_masuk); ?>">
										</div>
<?php
								}
							}
						}
					} else {
?>
					<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($addendum2->surat_tgl_masuk); ?>">
					</div>
<?php
					}
?>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled" rows="3" ><?php echo $addendum2->surat_perihal; ?></textarea>
				</div>
			</div>
		</div>
	</div><!-- /.box-body -->
	
<?php
	}
}	
		$id_habis = 0;
		$result = $this->kontrak_model->get_kontrakhenti($surat->surat_id);
		if($result->num_rows() > 0) {
		$habis = $result->row();
		$id_habis = $habis->addendum_id;
		
		list($agenda_date, $agenda_time) = explode(' ', $habis->created_time);
		list($agenda_hours, $agenda_second) = explode('.', $agenda_time);
		$agenda_date = db_to_human($agenda_date);		
		
		if ($surat->status == 99) {
?>	
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Identitas Penghentian Kontrak</h3>
			</div>

			<div class="box-body">
				<div class="form-group">
					<label for="surat_no" class="col-lg-2 col-sm-3 col-xs-12 control-label">Agenda</label>
					<div class="col-lg-4 col-sm-4 col-xs-6">
						<input type="text" id="agenda_id" class="form-control" disabled="disabled" value="<?php echo strtoupper($habis->jenis_agenda) . ' - ' . $habis->agenda_id; ?>">
					</div>
					<div class="col-lg-6 col-sm-5 col-xs-6">
						<input type="text" id="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date . ' ' . $agenda_hours; ?>" style="text-align: right;">
					</div>
				</div>
			
				<div class="form-group">
					<label for="sifat_surat" class="col-lg-2 col-sm-3 control-label">Nomor Addendum</label>
						<div class="col-lg-4 col-sm-9">
							<input type="text" id="sifat_surat" class="form-control" disabled="disabled" value="<?php echo $habis->sifat_surat; ?>">
						</div>
						
					<label for="surat_perihal" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Perihal</label>
						<div class="col-lg-4 col-sm-9">
							<input type="text" id="surat_perihal" class="form-control" disabled="disabled" value="<?php echo $habis->surat_perihal; ?>">
						</div>
				</div>
			
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Penghentian</label>
				<div class="col-lg-4 col-sm-9">
							<input type="text" id="surat_no" class="form-control" disabled="disabled" value="<?php echo $habis->surat_no; ?>">
				</div>
				
				<label for="status_berkas" class="col-lg-2 col-sm-3 control-label"> Mitra</label>
<?php 
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
?>
				<div class="col-lg-4 col-sm-9">
						<input type="text" id="status_berkas" name="status_berkas" disabled="disabled" class="form-control" data-input-title="status_berkas" value="<?php echo ($surat->status_berkas != '-') ? $opt_mitra[$surat->status_berkas] : '-'; ?>">
				</div>
				
			</div>
			
			<div class="form-group">
				<label for="surat_tgl" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tgl. Penghentian <br> (dd-mm-yyyy)</label>
					<div class="col-lg-4 col-sm-9">
						<input type="text" id="surat_tgl" class="form-control" disabled="disabled" value="<?php echo db_to_human($habis->surat_tgl); ?>">
					</div>
				<label for="surat_tgl_masuk" title="Format dd-mm-yyyy" class="col-lg-2 col-sm-3 control-label">Tanggal Berlaku <br> (dd-mm-yyyy)</label>

					<div class="col-lg-4 col-sm-9">
					<input type="text" id="surat_tgl_masuk" class="form-control" disabled="disabled" value="<?php echo db_to_human($habis->surat_tgl_masuk); ?>">
					</div>
			</div>
	
			<div class="form-group">
				<label for="catatan_pengiriman" class="col-lg-2 col-sm-3 control-label"> Alasan</label>
<?php 
	$opt_alasan = $this->admin_model->get_system_cm_config('alasan');
?>
				<div class="col-lg-4 col-sm-9">
						<input type="text" id="catatan_pengiriman" name="catatan_pengiriman" disabled="disabled" class="form-control" data-input-title="Alasan" value="<?php echo ($habis->catatan_pengiriman != '-') ? $opt_alasan[$habis->catatan_pengiriman] : '-'; ?>">
				</div>
				<label for="jenis_surat" class="col-lg-2 col-sm-3 control-label">Jenis Penghentian</label>
<?php 
	$opt_jenis_henti = $this->admin_model->get_system_cm_config('jenis_henti');
?>
				<div class="col-lg-4 col-sm-9">
					<input type="text" id="jenis_surat" name="jenis_surat" disabled="disabled" class="form-control" data-input-title="jenis_surat" value="<?php echo ($habis->jenis_surat != '-') ? $opt_jenis_henti[$habis->jenis_surat] : '-'; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="surat_ringkasan" class="col-lg-2 col-sm-3 control-label">Keterangan</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_ringkasan" class="form-control" disabled="disabled" rows="3" ><?php echo $habis->surat_ringkasan; ?></textarea>
				</div>
			</div>
		</div>
	</div><!-- /.box-body -->
<?php
		}
?>	
	
<?php 
	}
?>		
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

<?php
	if($surat->status != 99) {
			echo form_hidden('action', 'surat.kontrak_model.stop_kontrak'); 
			echo form_hidden('status', 99); 
		
		if(has_permission(1)|| has_permission(23)) {
?>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-8">
<?php 
		if($surat->status != 99){
		$enEdit = FALSE;
		if(!has_permission(1)) {
			if(editable_data($function_ref_id, get_role(), $surat->surat_id)) {
				$enEdit = TRUE;
			}
		} else {
			$enEdit = TRUE;
		}
		
			if($enEdit) {
?>
					<!-- <button type="submit" id="btnDelete" class="btn btn-app">
						<i class="fa fa-edit"></i> Hentikan Kontrak
					</button> -->					
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/kontrak/hentikan_kontrak/' . $surat->surat_id); ?>');">
						<i class="fa fa-trash-o"></i> Hentikan Kontrak
					</button>
<?php
				if($surat->status != 2){
					if($surat->status == 0){
?>
						<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/kontrak/ubah_kontrak/' . $surat->surat_id); ?>');">
							<i class="fa fa-file-text"></i> Ubah Kontrak
						</button>
						<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/kontrak/input_addendum1/' . $surat->surat_id); ?>');">
							<i class="fa fa-file-text"></i> Addendum Kontrak 1
						</button>
<?php
					}else if($surat->status == 1){
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/kontrak/ubah_addendum1/' . $surat->surat_id); ?>');">
						<i class="fa fa-file-text"></i> Ubah Addendum  1
					</button>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/kontrak/input_addendum2/' . $surat->surat_id); ?>');">
						<i class="fa fa-file-text"></i> Addendum Kontrak 2
					</button>
<?php 	
					}
				} else {
?>
					<button type="button" class="btn btn-app" onclick="location.assign('<?php echo site_url('surat/kontrak/ubah_addendum2/' . $surat->surat_id); ?>');">
						<i class="fa fa-file-text"></i> Ubah Addendum 2
					</button>
<?php
				}
?>
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
		}
	} else {
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

<?php 

	if($surat->status != 99) {
?>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select2').select2();
		
		$('#price').number( true, 2 );
		
		// $('#btnDelete').click(function() {
			// if(confirm("<textarea id='surat_perihal' class='form-control' rows='3' >Alasan Dihentikan <br></textarea> ")) { 
				// $.ajax({
					// type: "POST",
					// url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>",
					// data: "action=surat.kontrak_model.stop_kontrak",
					// success: function(data){//alert(data);
						// if(typeof(data.error) != 'undefined') {
							// if(data.error != '') {
								// alert(data.error);
							// } else {
								// alert(data.msg);
								// location.assign('<?php echo str_replace($this->config->item('url_suffix'), "", site_url('surat/kontrak/kontrak_aktif')); ?>');
							// }
						// } else {
							// alert('Data transfer error!');
						// }
					// }
				// });  
			// }
		// });
		
		
	});

	function hentikanData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.prompt({
			title: 'Alasan Menghentikan Kontrak.', 
			inputType: 'textarea',
			callback: function(result){
				if(result) {
					$.ajax({
						type: "POST",
						url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
						data: {action: 'surat.kontrak_model.stop_kontrak', 
								surat_id: '<?php echo $surat->surat_id; ?>', 
								ref_id: '<?php echo $surat->surat_id; ?>', 
								note: result, 
								function_ref_id: <?php echo $function_ref_id; ?>,
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
			'			<input type="text" name="attachment[' + attachmentRow + '][title]" class="form-control" placeholder="File ...">' +
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
		$('#attachment_' + rid).addClass('hide');
		$('#attachment_state_' + rid).val('delete');
	}
	
	function prosesData() {
		$('#box-process-btn .overlay').removeClass('hide');

		bootbox.confirm('<?php echo $process->check_field_info; ?>', function(result) {
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.kontrak_model.proses_data', 
							ref_id: '<?php echo $surat->surat_id; ?>', 
							note: result, 
							last_flow: <?php echo $last_flow; ?>,
							function_ref_id: <?php echo $function_ref_id; ?>,
							function_ref_name: 'Contract Maintenance',
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

</script>
<?php 
	} else {
?>
<script type="text/javascript">
	$(document).ready(function() {
		$('.select2').select2();
		
	});
<?php 
		if(get_user_data('unit_id') == $surat->surat_to_ref_id && $surat->unit_archive_status != 99) {
?>
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
<?php 
		}
?>
</script>
<?php
	}
?>