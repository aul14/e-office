<?php 
	
	
?>
<div >
	<table class="daftar">
		<thead>
			<tr>
				<th colspan="10" style="padding: 8px;">
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
				<th colspan="10" style="padding: 8px;">
					<h4 style="margin-bottom: 5px;">LEMBAR KONTRAK</h4>
				</th>
			</tr>
			<tr>
				<th rowspan= "2" width="5%" STYLE="vertical-align:middle;" STYLE="text-align: center;"> No</th>
				<th rowspan= "2" width="10%" STYLE="vertical-align:middle;"> Mitra</th>
				<th rowspan= "2" width="11%" STYLE="vertical-align:middle;"> Tanggal Kontrak</th>
				<th Colspan= "2" STYLE="text-align: center;"> Durasi</th>
				<th rowspan= "2" width="11%"  STYLE="vertical-align:middle;"> Nilai Kontrak</th>
				<th rowspan= "2" width="10%" STYLE="vertical-align:middle;"> Jenis Kontrak</th>
				<th rowspan= "2" width="10%" STYLE="vertical-align:middle;"> Nomor Kontrak</th>
				<th rowspan= "2" width="10%"  STYLE="vertical-align:middle;"> Kode Kontrak</th>
				<th rowspan= "2" width="10%"  STYLE="vertical-align:middle;"> Perihal</th>
			</tr>
			<tr>
				<th width="10%" STYLE="text-align: center;"> Mulai</th>
				<th width="10%" STYLE="text-align: center;"> Selesai</th>
			</tr>
		</thead>
		
		<tbody>
<?php
	$no=0;//variabel no
	$list = $this->kontrak_model->get_kontrak_selesai_list();
//	echo $this->db->last_query();
	if(count($list) > 0) {
		foreach($list as $row) {
				$no++;
?>
				<tr>
					<td> <?php echo "$no"; ?></td>
<?php 
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
?>
					<td><?php echo ($row->status_berkas != '-') ? $opt_mitra[$row->status_berkas] : '-'; ?></td>
					<td><?php echo $row->surat_unit_lampiran; ?></td>
					<td width="11%"><?php echo db_to_human($row->surat_awal); ?></td>
					<td width="11%"><?php echo db_to_human($row->surat_akhir); ?></td>
					<td><?php echo $row->surat_ringkasan; ?></td>
<?php 
	$opt_jenis_kontrak = $this->kontrak_model->get_referensi_full('jenis_kontrak');
?>
							<td><?php echo ($row->jenis_surat != '-') ? $opt_jenis_kontrak[$row->jenis_surat] : '-'; ?></td>
					<td><?php echo $row->surat_no; ?></td>
<?php 
	$opt_kode_kontrak = $this->kontrak_model->get_referensi_full('kode_kontrak');
?>
					<td><?php echo ($row->sifat_surat != '-') ? $opt_kode_kontrak[$row->sifat_surat] : '-'; ?></td>
					<td><?php echo $row->surat_perihal; ?></td>
				</tr>
<?php
		}
	}
?>
		</tbody>
	</table>
	
</div>
