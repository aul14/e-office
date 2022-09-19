<?php
//	var_dump($ref);
	$from = json_decode($surat->surat_from_ref_data, TRUE);
	
?>
	<!-- Default box -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Referensi</h3>
			
<?php 
	$opt_jenis_kontrak = $this->kontrak_model->get_referensi_full('jenis_kontrak');
	$opt_kode_kontrak  = $this->kontrak_model->get_referensi_full('kode_kontrak');
	$opt_mitra 		   = $this->kontrak_model->get_referensi_full('mitra');
	$opt_kode_add 	   = $this->admin_model->get_system_config('kode_add');
	$opt_jenis_add 	   = $this->admin_model->get_system_config('jenis_add');
?>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
<?php
	if ($surat->status == 0){
?>
		<div class="box-body">
			<div class="row">
				<div class="col-md-6">
					<table class="table_ref table-hover" style="width: 100%">
						<tr>
							<td><label>No. Agenda</label></td>
							<td><label style="font-weight: 400;"> : <?php echo strtoupper($surat->jenis_agenda) . ' - ' . $surat->agenda_id; ?></label></td>
						</tr>
						<tr>
							<td><label>Kode Kontrak</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($surat->sifat_surat != '-') ? $opt_kode_kontrak[$surat->sifat_surat] : '-'; ?></label></td>
						</tr>
						<tr>
							<td><label>Nomor Kontrak</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $surat->surat_no; ?></label></td>
						</tr>
						<tr>
							<td><label>Tgl. Kontrak (dd-mm-yyyy)</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $surat->surat_unit_lampiran; ?></label></td>
						</tr>
						<tr>
							<td><label>Nilai Kontrak</label></td>
							<td><label style="font-weight: 400;"> : Rp <?php echo $surat->surat_ringkasan; ?> </label></td>
						</tr>
					</table>
				</div>
				<div class="col-md-6">
					<table class="table_ref table-hover" style="width: 100%">
						<tr>
						
						</tr>
						<tr>
							<td><label>Hal</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $surat->surat_perihal; ?></label></td>
						</tr>
						<tr>
							<td><label>Jenis Kontrak</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($surat->status_berkas != '-') ? $opt_jenis_kontrak[$surat->jenis_surat] : '-'; ?></label></td>
						</tr>
						<tr>
							<td><label>Mitra</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($surat->status_berkas != '-') ? $opt_mitra[$surat->status_berkas] : '-'; ?></label></td>
						</tr>
						<tr>
							<td><label>Tgl. Berlaku (dd-mm-yyyy)</label></td>
							<td><label style="font-weight: 400;"> : <?php echo db_to_human($surat->surat_tgl); ?>   s.d.   <?php echo db_to_human($surat->surat_tgl_masuk); ?></label></td>
						</tr>
					</table>
				</div>
			</div>
		</div><!-- /.box-body -->
<?php 	} else{
		if ($surat->status == 1){
			$result   = $this->kontrak_model->get_addendum($surat->surat_id);
			$addendum = $result->row();
?>
			<div class="box-body">
				<div class="row">
					<div class="col-md-6">
						<table class="table-hover" style="width: 100%">
							<tr>
								<td><label>No. Agenda</label></td>
								<td><label style="font-weight: 400;"> : <?php echo strtoupper($addendum->jenis_agenda) . ' - ' . $addendum->agenda_id; ?></label></td>
							</tr>
							<tr>
								<td><label>Kode Addendum</label></td>
								<td><label style="font-weight: 400;"> : <?php echo ($addendum->sifat_surat != '-') ? $opt_kode_add[$addendum->sifat_surat] : '-'; ?></label></td>
							</tr>
							<tr>
								<td><label>Nomor Addendum</label></td>
								<td><label style="font-weight: 400;"> : <?php echo $addendum->surat_no; ?></label></td>
							</tr>
							<tr>
								<td><label>Tgl. Kontrak (dd-mm-yyyy)</label></td>
								<td><label style="font-weight: 400;"> : <?php echo $addendum->surat_unit_lampiran; ?></label></td>
							</tr>
							<tr>
								<td><label>Nilai Kontrak</label></td>
								<td><label style="font-weight: 400;"> : Rp <?php echo $addendum->surat_ringkasan; ?> </label></td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">
						<table class="table-hover" style="width: 100%">
							<tr>
						
							</tr>
							<tr>
								<td><label>Hal</label></td>
								<td><label style="font-weight: 400;"> : <?php echo $addendum->surat_perihal; ?></label></td>
							</tr>
							<tr>
								<td><label>Jenis Addendum</label></td>
								<td><label style="font-weight: 400;"> : <?php echo ($addendum->status_berkas != '-') ? $opt_jenis_add[$addendum->jenis_surat] : '-'; ?></label></td>
							</tr>
							<tr>
								<td><label>Mitra</label></td>
								<td><label style="font-weight: 400;"> : <?php echo ($addendum->status_berkas != '-') ? $opt_mitra[$addendum->status_berkas] : '-'; ?></label></td>
							</tr>
							<tr>
								<td><label>Tgl. Berlaku (dd-mm-yyyy)</label></td>
								<td><label style="font-weight: 400;"> : <?php echo db_to_human($addendum->surat_tgl); ?>   s.d.   <?php echo db_to_human($addendum->surat_tgl_masuk); ?></label> </td>
							</tr>
						</table>
					</div>
				</div>
			</div><!-- /.box-body -->
<?php	 } else {
			if ($surat->status == 2) {
				$result = $this->kontrak_model->get_addendum2($surat->surat_id);
				$addendum2 = $result->row();
?>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<table class="table-hover" style="width: 100%">
								<tr>
									<td><label>No. Agenda</label></td>
									<td><label style="font-weight: 400;"> : <?php echo strtoupper($addendum2->jenis_agenda) . ' - ' . $addendum2->agenda_id; ?></label></td>
								</tr>
								<tr>
									<td><label>Kode Addendum</label></td>
									<td><label style="font-weight: 400;"> : <?php echo ($addendum2->sifat_surat != '-') ? $opt_kode_add[$addendum2->sifat_surat] : '-'; ?></label></td>
								</tr>
								<tr>
									<td><label>Nomor Addendum</label></td>
									<td><label style="font-weight: 400;"> : <?php echo $addendum2->surat_no; ?></label></td>
								</tr>
								<tr>
									<td><label>Tgl. Addendum (dd-mm-yyyy)</label></td>
									<td><label style="font-weight: 400;"> : <?php echo $addendum2->surat_unit_lampiran; ?></label></td>
								</tr>
								<tr>
									<td><label>Nilai Kontrak</label></td>
									<td><label style="font-weight: 400;"> : Rp <?php echo $addendum2->surat_ringkasan; ?> </label></td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<table class="table-hover" style="width: 100%">
								<tr>
							
								</tr>
								<tr>
									<td><label>Hal</label></td>
									<td><label style="font-weight: 400;"> : <?php echo $addendum2->surat_perihal; ?></label></td>
								</tr>
								<tr>
									<td><label>Jenis Addendum</label></td>
									<td><label style="font-weight: 400;"> : <?php echo ($addendum2->status_berkas != '-') ? $opt_jenis_add[$addendum2->jenis_surat] : '-'; ?></label></td>
								</tr>
								<tr>
									<td><label>Mitra</label></td>
									<td><label style="font-weight: 400;"> : <?php echo ($addendum2->status_berkas != '-') ? $opt_mitra[$addendum2->status_berkas] : '-'; ?></label></td>
								</tr>
								<tr>
									<td><label>Tgl. Berlaku (dd-mm-yyyy)</label></td>
									<td><label style="font-weight: 400;"> : <?php echo db_to_human($addendum2->surat_tgl); ?>   s.d.   <?php echo db_to_human($addendum2->surat_tgl_masuk); ?></label> </td>
								</tr>
							</table>
						</div>
					</div>
				</div><!-- /.box-body -->
<?php 		}
		}
	}
?>
		