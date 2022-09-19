<?php
	$from = json_decode($surat->surat_from_ref_data, TRUE);
?>

	<!-- Default box -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Referensi</h3>
			
<?php 
	$opt_jenis_kontrak = $this->kontrak_model->get_referensi('jenis_kontrak');
	$opt_kode_kontrak = $this->kontrak_model->get_referensi('kode_kontrak');
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
	$opt_kode_add = $this->admin_model->get_system_config('kode_add');
	$opt_jenis_add = $this->admin_model->get_system_config('jenis_add');
?>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>

		<div class="box-body">
			<div class="row">
			
			<div class="col-md-6">
					<table class="table-hover table_ref" style="width: 100%">
						<tr>
							<td><label>Kode Kontrak</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($surat->sifat_surat != '-') ? $opt_kode_kontrak[$surat->sifat_surat] : '-'; ?></label></td>
						</tr>
						
						<tr>
							<td><label>Nomor Kontrak</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $surat->surat_no; ?></label></td>
						</tr>
						
						<tr>
							<td><label>Nilai Kontrak</label></td>
							<td><label style="font-weight: 400;"> : Rp <?php echo $surat->surat_ringkasan; ?> </label></td>
						</tr>
						
						<tr>
							<td><label>Tgl. Kontrak (dd-mm-yyyy)</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $surat->surat_unit_lampiran; ?></label></td>
						</tr>
					</table>
				</div>
			
				<div class="col-md-6">
					<table class="table-hover table_ref" style="width: 100%">
						<tr>
							<td><label>Jenis Kontrak</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($surat->status_berkas != '-') ? $opt_jenis_kontrak[$surat->jenis_surat] : '-'; ?></label></td>
						</tr>
						
						<tr>
							<td><label>Mitra</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($surat->status_berkas != '-') ? $opt_mitra[$surat->status_berkas] : '-'; ?></label></td>
						</tr>
						
						<tr>
							<td><label>Hal</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $surat->surat_perihal; ?></label></td>
						</tr>
						
						<tr>
							<td><label>Tgl. Berlaku (dd-mm-yyyy)</label></td>
							<td><label style="font-weight: 400;"> : <?php echo db_to_human($surat->surat_tgl); ?>   s.d.   <?php echo db_to_human($surat->surat_tgl_masuk); ?></label> </td>
						</tr>
					</table>
				</div>
			</div>
			
<?php
			if ($surat->status == 1) {
				$result = $this->kontrak_model->get_addendum($surat->surat_id);
				$addendum = $result->row();
?>
			<div class="row">
			<hr><hr>
				<div class="col-md-6">
					<table class="table_ref table-hover" style="width: 95%">
						<tr>
							<td><label>Kode Addendum 1</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($addendum->sifat_surat != '-') ? $opt_kode_add[$addendum->sifat_surat] : '-'; ?></label></td>
						</tr>
						<tr>
							<td><label>Nomor Addendum 1</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $addendum->surat_no; ?>
						</tr>
						<tr>
							<td><label>Nilai Kontrak</label></td>
							<td><label style="font-weight: 400;"> : Rp <?php echo $addendum->surat_ringkasan; ?> </label></td>
						</tr>
						<tr>
							<td><label>Tgl. Addendum 1 (dd-mm-yyyy)</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $addendum->surat_unit_lampiran; ?></label></td>
						</tr>
					</table>
				</div>
				<div class="col-md-6">
					<table class="table_ref table-hover" style="width: 100%">
						<tr>
							<td><label>Jenis Addendum 1</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($addendum->status_berkas != '-') ? $opt_jenis_add[$addendum->jenis_surat] : '-'; ?></label></td>
						</tr>
						<tr>
							<td><label>Mitra</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($surat->status_berkas != '-') ? $opt_mitra[$surat->status_berkas] : '-'; ?></label></td>
						</tr>
						<tr>
							<td><label>Hal</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $addendum->surat_perihal; ?></label></td>
						</tr>
						<tr>
							<td><label>Tgl. Berlaku (dd-mm-yyyy)</label></td>
							<td><label style="font-weight: 400;"> :  <?php echo db_to_human($addendum->surat_tgl); ?>    s.d.   <?php echo db_to_human($addendum->surat_tgl_masuk); ?> </label> </td>
						</tr>
					</table>
				</div>
			</div>
<?php
			}
?>
		</div><!-- /.box-body -->