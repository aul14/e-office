<?php 
$a = 0;
$last_ttd=  0;
$count = array();
$signed = json_decode($ref->signed, TRUE);
$penerima = json_decode($ref->distribusi, TRUE);
$keperluan = '';

$result_surat_ref = $this->admin_model->get_ref_surat_masuk($ref->surat_id);
$surat_ref_num = $result_surat_ref->num_rows();

if ($surat_ref_num > 0) {
	$surat_from_ref = $result_surat_ref->row();
	$surat_from_ref_data = json_decode($surat_from_ref->surat_from_ref_data, TRUE);
	$asal_surat = $surat_from_ref_data['instansi'];
}else {
	$surat_from_ref = '';
	$asal_surat = '';
}

?>

<div>
	<table class="layout">
		<thead>
			<tr>
				<th>
					<table class="layout">
						<tr>
							<td align= "center" width="20%">
								<img src="<?php echo site_url('assets/media/logo_kab_tangerang.png'); ?>" width="70" height="80">  
							</td>
							<td align="center" width= "100%">
								<span style="font-size: 14px;"> <b>PEMERINTAH KABUPATEN TANGERANG</b><br></span>
								<span> <h4>RUMAH SAKIT UMUM DAERAH BALARAJA</h4></span>
									<span style="font-size: 11px;">Jl. Rumah Sakit No. 88 Ds. Tobat, Kec. Balaraja 
									Kabupaten Tangerang 15610<br>Telp. 021 29508388, 29508250 Fax. 021 29508241</span>
							</td>
							<td align= "center" width= "20%">
							<img src="<?php echo site_url ('assets/media/logo.png'); ?>" width="80" height="80">  
							</td>
						</tr>
					</table>
					<div><hr style="margin-top:8px;color:#000;" /></div>
				</th>
			</tr>
		</thead>
				
		<tbody>
			<tr>
				<td  style="border-bottom-style: none !important;">
					<table class="layout">
						<tr>
							<td style="padding: 0px; text-align: center;">
							<br>
								<h4 style="margin-bottom: 2px !important;"><u>SURAT PERINTAH</u></h4>
							</td>
						</tr>
						<tr>
							<td style="padding: 3px; text-align: center;">
								<h5 style="margin-bottom: 8px;font-weight: normal;">Nomor : <?php echo ($ref->surat_no == '{surat_no}') ? trim($ref->kode_klasifikasi_arsip) . '/_/SURAT PERINTAH' : $ref->surat_no; ?></h5>
							</td>
						</tr>
						<br>
					</table>
				</td>
			</tr>
			<tr colspan="3">
				<td style="text-align: justify; text-indent: 0.5in; padding: 8px; border-bottom-style: none; border-top-style: none;">
					<table class="layout">
						<tr>
							<td>
								<p> 
								Dasar Surat dari <?php echo ($asal_surat != '') ? $asal_surat : ''; ?> Perihal <?php echo $ref->surat_perihal; ?> memerintahkan : 
								</p>
							</td>
						</tr>
					</table>
				</td>				
			</tr>
			<tr colspan="3">
				<td style="text-align: justify; text-indent: 0.5in; padding: 8px; border-bottom-style: none; border-top-style: none;">
					<table width="100%" class="layout">
						<tr>
							<td>
								<table width="95%" class="tugas" style="width:95% !important;">
									<thead>
										<tr>
											<th>NO.</th>
											<th>NAMA</th>
											<th>PANGKAT / GOL.</th>
											<th>NIP</th>
											<th>JABATAN</th>
										</tr>
									</thead>
									<tbody>
									<?php
									if(isset($penerima)) {
										$no = 1;
										foreach($penerima as $tugas) {
											$keperluan = $tugas['keperluan'];
									?>	
										<tr>
											<td width="8%" align="center"><?php echo $no; ?></td>
											<td><?php echo $tugas['nama']; ?></td>
											<td><?php echo $tugas['pangkat']; ?></td>
											<td><?php echo $tugas['nip']; ?></td>
											<td><?php echo $tugas['jabatan'] . ' '. $tugas['nama_unitkerja']; ?></td>
										</tr>
									<?php
											$no++;
											$last_ttd=  $a;
										}
									}	
									?>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</td>				
			</tr>
			<tr colspan="3">
				<td style="text-align: justify; text-indent: 0.5in; padding: 8px; border-bottom-style: none; border-top-style: none;">
					<table class="layout">
						<tr>
							<td>
								<p> 
								<?php echo $keperluan; ?>
								</p>
							</td>
						</tr>
					</table>
				</td>				
			</tr>	
			<tr colspan="3">
				<td style="text-align: justify; text-indent: 0.5in; padding: 8px; border-bottom-style: none; border-top-style: none;">
					<table class="layout">
						<tr>
							<td>
								<p> 
								Demikian surat perintah ini dibuat, untuk dilaksanakan dengan penuh tanggung jawab.
								</p>
							</td>
						</tr>
					</table>
				</td>				
			</tr>
<br>
<?php
	$signed = json_decode($ref->signed, TRUE);
?>				
			<tr>
				<td class="center" style="border-top-style: none;">&nbsp;
				<table class="layout">
					<tbody>
						<tr>
							<td width="50%">&nbsp;</td>
							<td>Dikeluarkan di : Balaraja</td>
						</tr>
						<tr>
							<td width="50%">&nbsp;</td>
							<td>Pada tanggal : <?php echo ($ref->surat_tgl != '') ? db_to_human_local($ref->surat_tgl) : '__-________-____'; ?><br /><?php echo ($signed['jabatan'] == 'Direktur') ? $signed['jabatan'] : $signed['jabatan'] . '&nbsp;' . $signed['unit_name']; ?><br />
							<br />
							<br />
							<br />
							<strong><u style="color:#000;"><?php echo  $signed['nama_pejabat']; ?></u></strong><br />
							<?php echo $signed['pangkat']; ?><br />
							NIP : <strong><?php echo $signed['nip'];?> </strong></td>

						</tr>
					</tbody>
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
