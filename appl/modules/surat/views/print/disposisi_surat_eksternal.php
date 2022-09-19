<?php 
	
	
?>
<div >
	<table class="daftar">
		<thead>
			<tr>
				<th colspan="3" style="padding: 8px;">
					<h4 style="margin-bottom: 5px;">Lembar Disposisi</h4>
					<span> <?php echo $disposisi->surat_from_unit; ?> </span>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="3" style="padding: 5px;">
					Perhatian : Dilarang memisahkan sehelai suratpun yang tergabung dalam berkas ini.
				</td>
			</tr>
			<tr>
				<td width="37%" style="padding: 8px 0; vertical-align: top;">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
					<table class="layout">
						<tr>
							<td width="40%"><label>Nomor Surat</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $ref->surat_no; ?></label></td>
						</tr>
						<tr>
							<td><label>Tanggal Surat</label></td>
							<td><label style="font-weight: 400;"> : <?php echo db_to_human($ref->surat_tgl); ?></label></td>
						</tr>
						<tr>
							<td><label>Lampiran</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $ref->surat_item_lampiran . ' ' . $opt_unit_lpr[$ref->surat_unit_lampiran]; ?></label></td>
						</tr>
					</table>
				</td>
				<td width="26%" style="padding: 8px 0; vertical-align: top;">
<?php 
	$opt_status_berkas = $this->admin_model->get_system_config('status_berkas');
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	$opt_jenis_surat = $this->admin_model->get_system_config('jenis_surat');
?>
					<table class="layout">
						<tr>
							<td width="30%"><label>Status</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_status_berkas[$ref->status_berkas]; ?></label></td>
						</tr>
						<tr>
							<td><label>Sifat</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_sifat_surat[$ref->sifat_surat]; ?></label></td>
						</tr>
						<tr>
							<td><label>Jenis</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_jenis_surat[$ref->jenis_surat]; ?></label></td>
						</tr>
					</table>
				</td>
				<td width="37%" style="padding: 8px 0; vertical-align: top;" >
					<table class="layout">
						<tr>
							<td width="45%"><label>Tanggal Terima</label></td>
							<td><label style="font-weight: 400;"> : <?php echo db_to_human($ref->surat_tgl_masuk); ?></label></td>
						</tr>
						<tr>
							<td><label>Nomor Agenda</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $ref->jenis_agenda . '-' . $ref->agenda_id; ?></label></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="padding: 5px;">
					<table class="layout">
						<tr>
							<td width="12%">
								<label class="col-md-2" style="vertical-align: top; ">Asal Surat</label>
							</td>
							<td>
								<label class="col-md-10" style="font-weight: 400; vertical-align: top; "> : <?php echo $ref->surat_ext_nama . ' &nbsp; | &nbsp; ' . $ref->surat_ext_title . ' &nbsp; | &nbsp; ' . $ref->surat_ext_instansi; ?></label>
							</td>
						</tr>
						<tr>
							<td>
								<label class="col-md-2" style="vertical-align: top;">Perihal</label>
							</td>
							<td>
								<label class="col-md-10" style="font-weight: 400; vertical-align: top;"> : <?php echo $ref->surat_perihal; ?></label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3" class="center" style="padding: 8px 0; vertical-align: top;" >
					<label><?php echo ($disposisi->sifat == 2) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Sangat Segera</label> &nbsp; &nbsp; &nbsp;
					<label><?php echo ($disposisi->sifat == 1) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Segera</label> &nbsp; &nbsp; &nbsp;
					<label><?php echo ($disposisi->sifat == 0) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Biasa</label> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="3"  style="padding: 8px 5px; vertical-align: top;" >
					Batas Maksimal Penyelesaian : <?php echo db_to_human($disposisi->target_selesai); ?>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="padding: 8px 5px; vertical-align: top; height: 120px; " >
					Instruksi : <br>
					<?php echo $disposisi->instruksi; ?>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="padding: 8px 5px; vertical-align: top;" >
					<table class="layout">
						<tr>
							<td class="center" style="padding: 8px 5px; vertical-align: top;">
								Sudah digunakan, harap segera dikembalikan
							</td>
						</tr>
					</table>
					Kepada &nbsp;: &nbsp; <?php echo $disposisi->surat_to_unit; ?><br>
					Tanggal&nbsp;: &nbsp; <?php echo date('d-m-Y'); ?> 
				</td>
			</tr>
		</tbody>
	</table>
	
</div>
<div style="font-size: 9pt; padding-top: 4px;">
	system id : <?php echo $disposisi->disposisi_id; ?>
</div>
<pagebreak>

<div class="center">
	<h5>Surat Masuk Eksternal</h5>
	<h4 style="margin-bottom: 5px;">Lembar Pengantar Disposisi</h4>
	<span> <?php echo $disposisi->surat_from_unit; ?> </span>
</div>
<br>
<div >
	<table>
		<tr>
			<td>Ditugaskan Kepada </td>
			<td> : <?php echo $disposisi->surat_to_unit; ?></td>
		</tr>
		<tr>
			<td>Tanggal Surat Pengantar </td>
			<td> : <?php echo date('d-m-Y'); ?> </td>
		</tr>
	</table>
</div>


<div>
	<table class="daftar">
		<thead>
			<tr>
				<th style="width: 35px;"> No. </th>
				<th style="width: 90px;"> No. Agenda </th>
				<th style="width: 100px;"> No. Surat </th>
				<th style="width: 120px;"> Dari </th>
				<th style="width: 80px;"> Tgl. Surat </th>
				<th style="width: 60px;"> Status Berkas </th>
				<th > Perihal </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="center">1.</td>
				<td><?php echo $ref->jenis_agenda . '-' . $ref->agenda_id; ?></td>
				<td><?php echo $ref->surat_no; ?></td>
				<td><?php echo $ref->surat_ext_nama . '<br>' . $ref->surat_ext_title . ' &nbsp; | &nbsp; ' . $ref->surat_ext_instansi; ?></td>
				<td><?php echo db_to_human($ref->surat_tgl); ?></td>
				<td style="text-align: center;"><?php echo $ref->status_berkas; ?></td>
				<td><?php echo $ref->surat_perihal; ?></td>
			</tr>
		</tbody>
	</table>
</div>
		

<div>
	<br><br>
	<table class="layout">
		<tr>
			<td class="center"> 
				Pengantar
				<br><br><br><br>
				(<?php echo underscore(str_pad($disposisi->petugas_pengirim, 30, '_', STR_PAD_BOTH)); ?>)<br><br>
				Diserahkan Pukul : __________
			</td>
			<td class="center">
				Penerima
				<br><br><br><br>
				(______________________________)<br><br>
				Diterima Pukul : _____________
			</td>
		</tr>
	</table>
</div>
<footer>
<div style="font-size: 9pt; padding-top: 4px;">
	system id : <?php echo $disposisi->disposisi_id; ?>
</div>
</footer>