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
					<h4 style="margin-bottom: 5px;">LEMBARAN DISPOSISI DIREKTUR</h4>
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
	$opt_status_berkas = $this->admin_model->get_system_config('status_berkas');
	$opt_sifat_surat = $this->admin_model->get_system_config('sifat_surat');
	$opt_jenis_surat = $this->admin_model->get_system_config('jenis_surat');
?>
					<table class="layout">
						<tr>
							<td><label>Sifat</label></td>
							<td>
							<label style="font-weight: 400;"> : </label>
							<label style="font-weight: 400;"><span class="fa fa-square-o">&#xf096;</span> Rahasia</label><br/>&nbsp; 
							<label style="font-weight: 400;"> <span class="fa fa-square-o">&#xf096;</span> Segera</label><br/>&nbsp; 
							<label style="font-weight: 400;"> <span class="fa fa-square-o">&#xf096;</span> Sangat Segera</label><br/>&nbsp; 
							<label style="font-weight: 400;"> <span class="fa fa-square-o">&#xf096;</span> Biasa</label>
							<!--	
							<label style="font-weight: 400;"> : <?php echo $opt_sifat_surat[$ref->sifat_surat]; ?></label>--></td>
						</tr>
						<tr>
							<td width="23%"><label>Status</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_status_berkas[$ref->status_berkas]; ?></label></td>
						</tr>
						<tr>
							<td><label>Jenis</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_jenis_surat[$ref->jenis_surat]; ?></label></td>
						</tr>
					</table>
				</td>
			</tr>
	
			<tr>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td width="42%"><label>Tanggal Penerimaan</label></td>
							<td><label style="font-weight: 400;"> : <?php echo db_to_human($ref->surat_tgl_masuk); ?></label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 0; vertical-align: top;">
					<table class="layout">
						<tr>
							<td width="42%"><label>Tanggal Penyelesaian</label></td>
							<td><label style="font-weight: 400;"> : <?php echo db_to_human($disposisi->target_selesai); ?>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="center" style="padding: 8px 0; vertical-align: top;" >
					<label><?php echo ($disposisi->sifat == 2) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Sangat Segera</label> &nbsp; &nbsp; &nbsp;
					<label><?php echo ($disposisi->sifat == 1) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Segera</label> &nbsp; &nbsp; &nbsp;
					<label><?php echo ($disposisi->sifat == 0) ? '<i class="fa fa-check-square-o">&#xf046;</i>' : '<i class="fa fa-square-o">&#xf096;</i>'; ?> Biasa</label> &nbsp; &nbsp; &nbsp;
				</td>
			</tr>
		
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
								<label class="col-md-2" style="font-weight: 200; vertical-align: top;"> : </label></label>
							</td>
							<td align = "justify">
								<label class="col-md-10" style="font-weight: 400; vertical-align: top;"> <?php echo $surat_from_ref_data['nama'] . ' &nbsp; | &nbsp; ' . $surat_from_ref_data['title'] . ' &nbsp; | &nbsp; ' . $surat_from_ref_data['instansi']; ?></label></label>
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
				<td width="50%" style="padding: 8px 0; vertical-align: top;" align = "center">
					<table class="layout">
						<tr>
							<td width="42%" style="padding: 8px 0; vertical-align: top;"><label>Informasi / Instruksi</label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 0; vertical-align: top;" align = "center">
					<table class="layout">
						<tr>
							<td width="42%"><label>Diteruskan Kepada</label></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="50%" style="padding: 8px 1; vertical-align: top; height: 500px; ">
					<table class="layout">
						<tr>
							<td align = "justify"><label style="font-weight: 400;"> <?php echo $disposisi->instruksi; ?></label></td>
						</tr>
					</table>
				</td>
				<td width="50%" style="padding: 8px 1; vertical-align: top; height: 500px; ">
					<table class="layout">
<?php
	$distribusi = array();
	if($ref->distribusi != '') {
		$distribusi = json_decode($ref->distribusi, TRUE);
	}
	if(!isset($distribusi['direksi'])) {
		$distribusi['direksi'] = array();
	}
	if(!isset($distribusi['non_direksi'])) {
		$distribusi['non_direksi'] = array();
	}
	$i = 1;
	$list = $this->admin_model->get_direksi();
	foreach($list->result() as $row) {
?>					
						<tr>
							<td class="center" width="0.3in"><?php echo $i++;?>.</td>
							<td>
<?php 
//	echo (in_array($row->organization_structure_id, $distribusi['direksi'])) ? '<span class="fa fa-check-square-o">&#xf046;</span>' : '<span class="fa fa-square-o">&#xf096;</span>'; 
//	echo $to_check . ' -|- ' . $row->organization_structure_id;
	echo ($to_check == $row->organization_structure_id) ? '<span class="fa fa-check-square-o">&#xf046;</span>' : '<span class="fa fa-square-o">&#xf096;</span>'; 
?>
								&nbsp; &nbsp; <?php echo $row->unit_name;?>
							</td>
						</tr>
<?php
	}
	
	$list = $this->admin_model->get_non_direksi();
	$opt_pejabat = array();
	foreach ($list->result() as $row) {
		if(in_array($row->organization_structure_id, $distribusi['non_direksi'])) {
			$opt_pejabat[$row->organization_structure_id] = $row->unit_name; 
		}
	}
	
	foreach($opt_pejabat as $k => $v) {
?>
						<tr>
							<td class="center" width="0.3in"><?php echo $i++;?>.</td>
							<td>
<?php 
//	echo (in_array($row->organization_structure_id, $distribusi['direksi'])) ? '<span class="fa fa-check-square-o">&#xf046;</span>' : '<span class="fa fa-square-o">&#xf096;</span>'; 
//	echo $to_check . $row->organization_structure_id;
//	echo $to_check . ' -|- ' . $k;
	echo ($to_check == $k) ? '<span class="fa fa-check-square-o">&#xf046;</span> &nbsp; &nbsp; ' : '<span class="fa fa-square-o">&#xf096;</span> &nbsp; &nbsp; '; 
	echo $v;
?>
							</td>
						</tr>
<?php
	}
?>

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
			<td><BR>system id : <?php echo $disposisi->disposisi_id; ?></br></td>
			<td style="right: 15px;"><br>printed on : <?php echo date('Y-m-d H:i:s'); ?></br></td>
		</tr>
	</table>
	
</div>
</footer>