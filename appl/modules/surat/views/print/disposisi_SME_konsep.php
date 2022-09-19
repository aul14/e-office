<?php 
	
?>
<div >
	<table class="daftar">
		<thead>
			<tr>
				<th colspan="3" style="padding: 8px;">
					<table class="layout">
						<tr>
							<td align= "center" width="20%" >
								<img src="<?php echo site_url('assets/media/logo_kab_tangerang.png'); ?>" width="70" height="80">  
							</td>
							<td align="center" width= "100%">
								<span style="font-size: 14px;"> <b>PEMERINTAH KABUPATEN TANGERANG</b><br></span>
								<SPAN> <h4>RUMAH SAKIT UMUM DAERAH BALARAJA</h4></SPAN>
									<span style="font-size: 12px;"> Jl. Rumah Sakit No. 88, Desa Tobat, Kec. Balaraja<br> 
									Kabupaten Tangerang</span>
							</td>
							<td align= "center" width= "20%">
							<img src="<?php echo site_url ('assets/media/logo.png'); ?>" width="80" height="80">  
							</td>
						</tr>
					</table>
				</th>
			</tr>
			<tr>
				<th colspan="3" style="padding: 8px;">
					<h4 style="margin-bottom: 5px;">LEMBARAN DISPOSISI</h4>
				</th>
			</tr>
		</thead>
		
		<tbody>
			<tr>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
	$surat_from_ref = json_decode($ref->surat_from_ref_data, TRUE);
?>
					<table class="layout">
						<tr>
							<td width="30%"><label>Surat Dari</label></td>
							<td valign="top"><label style="font-weight: 400; "> : <?php echo $surat_from_ref['instansi']; ?></label></td>
						</tr>
						<tr>	
							<td style="padding-top: 10px;"><label>Nomor Surat</label></td>
							<td valign="top" style="padding-top: 10px;"><label style="font-weight: 400; "> : <?php echo $ref->surat_no; ?></label></td>
						</tr>
						<tr>	
							<td style="padding-top: 10px;"><label>Tanggal Surat</label></td>
							<td valign="top" style="padding-top: 10px;"><label style="font-weight: 400; "> : <?php echo db_to_human($ref->surat_tgl); ?></label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
<?php  
	$opt_status_berkas = $this->admin_model->get_system_config('status_berkas');
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	$opt_jenis_surat = $this->admin_model->get_system_config('jenis_surat');
?>
					<table class="layout">
						<tr>
							<td width="35%"><label>Diterima Tanggal</label></td>
							<td valign="top"><label style="font-weight: 400;"> : <?php echo db_to_human($ref->surat_tgl_masuk); ?></label><br/></td>
						</tr>
						<tr>
							<td style="padding-top: 10px;"><label>No. Agenda</label></td>
							<td valign="top" style="padding-top: 10px;"><label style="font-weight: 400;"> : <?php echo $ref->jenis_agenda . '-' . $ref->agenda_id; ?></label></td>
						</tr>
						<tr>
							<td valign="top" style="padding-top: 10px;"><label>Sifat Surat</label></td>
							<td>
							<label style="font-weight: 400;"> : </label><br/></td>
						</tr>
						<tr>	
							<td colspan="4" style="padding-top: 5px;">
								<label style="font-weight: 400;"> <span class="fa fa-square-o">&#xf096;</span> Segera</label>&nbsp; 
								<label style="font-weight: 400;"> <span class="fa fa-square-o">&#xf096;</span> Sangat Segera</label>&nbsp; 
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;</span> Rahasia</label>&nbsp; 
								<label style="font-weight: 400;"> <span class="fa fa-square-o">&#xf096;</span> Biasa</label>
							<!--	
							<label style="font-weight: 400;"> : <?php echo $opt_sifat_surat[$ref->sifat_surat]; ?></label>-->
							</td>
						</tr>
						<!--
						<tr>
							<td width="30%"><label>Status</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_status_berkas[$ref->status_berkas]; ?></label></td>
						</tr>
						<tr>
							<td><label>Jenis</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_jenis_surat[$ref->jenis_surat]; ?></label></td>
						</tr>
						-->
					</table>
				</td>
			</tr>
	
			<tr>
				<td colspan="2" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td width="9%" valign="top"><label>Perihal</label></td>
							<td width="90%" valign="top" style="height: 90px;"><label style="font-weight: 400;"> : <?php echo $ref->surat_perihal; ?></label></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>	
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td><label>Diteruskan / Disposisi Kepada Sdr.</label></td>
						</tr>
<?php 
	if(isset($distribusi_disposisi)) {
?>						
						<tr>	
							<td style="padding: 10px 5px;">
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> <?php echo $distribusi_disposisi['jabatan'] . ' ' . $distribusi_disposisi['unit_name']; ?> </label><br/><br/>
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> ............................................................................. </label><br/><br/>
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> ............................................................................. </label><br/><br/>
								<label style="font-weight: 400;">Dan Seterusnya  ........................................................ </label>
							</td>
						</tr>
<?php 
	}else {
?>
						<tr>	
							<td style="padding: 10px 5px;">
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> ............................................................................. </label><br/><br/>
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> ............................................................................. </label><br/><br/>
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> ............................................................................. </label><br/><br/>
								<label style="font-weight: 400;">Dan Seterusnya  ........................................................ </label>
							</td>
						</tr>
<?php
	}
?>						
					</table>
				</td>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td><label>Dengan Hormat Harap</label></td>
						</tr>
						<tr>	
							<td style="padding: 10px 5px;">
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> Tanggapan dan Saran</label><br/><br/> 
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> Proses Lebih Lanjut</label><br/><br/> 
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> Koordinasi / Konfirmasi</label><br/><br/> 
								<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;&nbsp;</span> ............................................................................. </label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>	
				<td colspan="2" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td style="font-size: 12px;"><label><b><i>Catatan :</i></b></label></td>
						</tr>
<?php 
	if(isset($distribusi_disposisi)) {
?>						
						<tr>
							<td style="padding-top: 8px;"><?php echo $distribusi_disposisi['instruksi']; ?></td>
							<td colspan="4" align="right" style="padding-right: 30px;"><label>Paraf dan Tanggal</label></td>
						</tr>
<?php 
	}else {
?>
						<tr>
							<td colspan="5" align="right" style="padding-right: 30px;"><label>Paraf dan Tanggal</label></td>
						</tr>
<?php 
	}
?>						
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="5" align="right" style="padding-right: 50px;"><label>Nama Pejabat</label></td>
						</tr>
						<tr>
							<td style="font-size: 12px;padding: 10px 5px;"><label><b><i>Instruksi Berjenjang / Lanjutan :</i></b></label></td>
						</tr>
						<tr>
							<td colspan="5" align="right" style="padding-right: 30px;"><label>Paraf dan Tanggal</label></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td colspan="5" align="right" style="padding-right: 50px;"><label>Nama Pejabat</label></td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	
</div>
<footer>
<div style="font-size: 8pt; padding-top: 4px;">
	<table class="layout">
		<tr>
			<td><br>system id : <?php echo $ekspedisi_id; ?></br></td>
			<td align="right"><br>printed on : <?php echo date('Y-m-d H:i:s'); ?></br></td>
		</tr>
	</table>	
</div>
</footer>