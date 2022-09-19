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
								<img src="<?php echo site_url('assets/media/logo_kemenkes.png'); ?>" width="78" height="80">  
							</td>
					
							<td align="center" width= "100%">
								<span style="font-size: small;"> <b>KEMENTRIAN KESEHATAN RI<Br>
								DIREKTORAT JENDRAL BINA UPAYA KESEHATAN</b><br></span>
								<SPAN> <h5>RUMAH SAKIT UMUM DAERAH BALARAJA</h5></SPAN>
									<span style="font-size: x-small;"> Jl. Rumah Sakit No. 88 Ds. Tobat, Kec. Balaraja Kabupaten Tangerang 15610<Br>
								Telp (021) 29508388, 29508250 Fax. (021) 295A8241</span>
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
					<h4 style="margin-bottom: 5px;">LEMBAR DISPOSISI</h4>
				</th>
			</tr>
		</thead>
		
		<tbody>
			<tr>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
					<table class="layout">
						<tr>
							<td width="35%"><label>Nomor Agenda<br> Registrasi Surat</label></td>
							<td valign="top"><label style="font-weight: 400; "> : <?php echo $ref->jenis_agenda . '-' . $ref->agenda_id; ?></label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
<?php  
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
?>
					<table class="layout">
						<tr>
							<td><label>Sifat</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_sifat_surat[$ref->sifat_surat]; ?></label></td>
						</tr>
					</table>
				</td>
			</tr>
	
			<tr>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td width="42%"><label>Tanggal Penerimaan</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($ref->surat_tgl_masuk); ?></label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td width="42%"><label>Tanggal Penyelesaian</label></td>
							<td><label style="font-weight: 400;"> : <?php //echo db_to_human($disposisi->target_selesai); ?>
						</tr>
					</table>
				</td>
			</tr>
		<?php 	
			/**<tr>
				<td colspan="2" class="center" style="padding: 8px 0; vertical-align: top;" >
					<label><?php echo ($disposisi->sifat == 2) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Sangat Segera</label> &nbsp; &nbsp; &nbsp;
					<label><?php echo ($disposisi->sifat == 1) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Segera</label> &nbsp; &nbsp; &nbsp;
					<label><?php echo ($disposisi->sifat == 0) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Biasa</label> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
			*/
		?>	
			<tr>
				<td colspan="3" style="padding: 5px;">
<?php
	$surat_from_ref_data = json_decode($ref->surat_from_ref_data, TRUE);
?>
					<table class="layout">
						<tr>
							<td width="25%" valign="top">
								<label class="col-md-2" style="vertical-align: top; ">Tanggal dan Nomor Surat</label>
							</td>
							<td valign="top" width="0.3in">
								<label class="col-md-2" style="font-weight: 200; vertical-align: top; ">: </label>
							</td>
							<td align = "justify">
								<label class="col-md-10" style="font-weight: 400; vertical-align: top; "> <?php echo db_to_human($ref->surat_tgl); ?> & <?php echo $ref->surat_no; ?></label>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<label class="col-md-2" style="vertical-align: top;">Dari</label>
							</td>
							<td valign="top">
								<label class="col-md-2" style="font-weight: 200; vertical-align: top;"> : </label>
							</td>
							<td align = "justify">
								<label class="col-md-10" style="font-weight: 400; vertical-align: top;"> 
								<?php echo ((isset($surat_from_ref_data['unit'])) ? ($surat_from_ref_data['jabatan'] . ', ' . $surat_from_ref_data['unit'] . ' | ') : '') . $surat_from_ref_data['dir']; ?></label>
							</td>
						</tr>
						<tr>
							<td  valign="top">
								<label class="col-md-2" style="vertical-align: top;  ">Ringkasan Isi</label>
							</td>
							<td valign="top">
								<label class="col-md-2" style="font-weight: 200; vertical-align: top; "> : </label>
							</td>
							<td align = "justify">
								<label class="col-md-10" style="font-weight: 400; vertical-align: top; "><?php echo $ref->surat_perihal; ?></label>
							</td>
						</tr>
						<tr>
							<td valign="top">	
								<label class="col-md-2" style="vertical-align: top; ">Lampiran</label>
							</td>
							<td valign="top">
								<label class="col-md-2" style="font-weight: 200; vertical-align: top; ">: </label>
							</td>
							<td align = "justify">
								<label class="col-md-10" style="font-weight: 400; vertical-align: top; "><?php echo $ref->surat_item_lampiran . ' ' . $opt_unit_lpr[$ref->surat_unit_lampiran]; ?></label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="50%" style="padding: 8px 0; vertical-align: top;" align="center">
					<table class="layout">
						<tr>
							<td width="42%" style="padding: 8px 0; vertical-align: top;"><label>Informasi / Instruksi</label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 0; vertical-align: top;" align="center">
					<table class="layout">
						<tr>
							<td width="42%" style="padding: 8px 0; vertical-align: top;"><label>Diteruskan Kepada</label></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="50%" style="padding: 8px 1; vertical-align: top; height: 500px; ">
					<table class="layout">
						<tr>
							<td align = "justify"><label style="font-weight: 400;"> <?php //echo $disposisi->instruksi; ?></label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 1; vertical-align: top; height: 500px; ">
					
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