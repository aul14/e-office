<?php 
	list($ekspedisi_d, $ekspedisi_t) = explode(' ', $ekspedisi->created_time); 
	$tgl_ekspedisi = db_to_human($ekspedisi_d);
	
	switch($ekspedisi->jenis_agenda) {
		case 'SME' :
			$title = 'Surat Masuk Eksternal';
		break;
		case 'SKE' :
			$title = 'Surat Keluar Eksternal';
		break;
		case 'SI' :
			$title = 'Surat Internal';
		break;
		default :
			$title = 'Surat';
		break;
	}
	
?>
<style type="text/css">
	body {
		font-family:'Source Sans Pro',sans-serif;
		font-size: 10pt;
	}
	
	h1,h2,h3,h4,h5,h6,
	.h1,.h2,.h3,.h4,.h5,.h6 {
		
	}
	
	h1, .h1 {
		font-size: 36px;
	}
	
	h2, .h2 {
		font-size: 30px;
	}
	
	h3, .h3 {
		font-size: 24px;
	}
	
	h4, .h4 {
		font-size: 18px;
	}
	
	h5, .h5 {
		font-size: 14px;
	}
	
	h6, .h6 {
		font-size: 12px;
	}
	
	.center {
		text-align: center;
	}
	
	table.layout {
		width: 100%;
		font-size: 10pt;
	}
	
	table.daftar {
		width: 100%;
		border-collapse: collapse;
		border: 1px solid #000000;
		font-size: 9pt;
	}
	
	table.daftar th, table.daftar td {
		border: 1px solid #000000;
		padding: 0 5px;
	}
	
	table.daftar th {
		background-color: #dedede;
	}
	
</style>
<?php
	foreach ($list_surat as $row) {
		$distribusi_title = ($row->status == 2) ? 'Distribusi Kepada' : 'Disposisi Kepada';

		$list_disposisi = $this->ekspedisi_model->get_list_disposisi($row->surat_id);
		if(isset($list_disposisi->distribusi)) {
			$distribusi = json_decode($list_disposisi->distribusi, TRUE);
			$distribusi = (isset($list_disposisi->to_user_id)) ? $distribusi[$list_disposisi->to_user_id] : '';
			
			if ($distribusi != '') {
				$to_ref = $distribusi['unit_name'];
				$to_jabatan = $distribusi['jabatan'];
			}else {
				$to_ref = '';
				$to_jabatan = '';
			}
		}else {	
			$distribusi_tujuan = json_decode($row->distribusi_tujuan, TRUE);

			$to_ref = $distribusi_tujuan['unit'];
			$to_jabatan = $distribusi_tujuan['jabatan'];
		}

		$surat_from_ref = json_decode($row->surat_from_ref_data, TRUE);
				
		if($row->surat_from_ref == 'eksternal') {
			$from_ref = 'Bagian Umum, Perencanaan, Evaluasi Dan Pelaporan';		
		}else {
			$from_ref = $surat_from_ref['unit'];
		}
		
	}
	
?>
<div class="center">
	<h5>Surat Pengantar <?php echo $title; ?></h5>
	<h4 style="margin-bottom: 5px;">Lembar Pengantar</h4>
	<span> Dari <?php echo ucwords(strtolower($from_ref)); ?></span>
	<!--<span> Dari Bagian Tata Usaha </span>-->
</div>
<br>
<div >
	<table>
		<tr>
			<td><?php echo $distribusi_title; ?> </td>
			<td> : <?php echo $to_jabatan . ' ' . $to_ref; ?></td>
		</tr>
		<tr>
			<td>Tanggal </td>
			<td> : <?php  echo $tgl_ekspedisi; ?></td>
		</tr>
	</table>
</div>

<div>
	<table class="daftar">
		<thead>
			<tr>
				<th style="width: 35px;"> No. </th>
				<th style="width: 80px;"> No. Agenda </th>
				<th style="width: 100px;"> No. Surat </th>
				<th style="width: 100px;"> Surat Dari </th>
				<th style="width: 80px;"> Tgl. Surat </th>
				<th style="width: 60px;"> Status Berkas </th>
				<th style="width: 70px;"> Perihal </th>
				<th style="width: 70px;"> Penerima </th>
				<th style="width: 70px;"> Paraf </th>
			</tr>
		</thead>
		<tbody>
<?php 
	$i = 1;
	foreach ($list_surat as $row) {
		$surat_from_ref_data = json_decode($row->surat_from_ref_data, TRUE);
		$surat_signed = json_decode($row->signed, TRUE);
?>
			<tr>
				<td class="center"><?php echo $i++; ?>.</td>
				<td><?php echo $row->jenis_agenda . '-' . $row->agenda_id; ?></td>
				<td><?php echo $row->surat_no; ?></td>
				<td>
<?php 
		if($row->surat_from_ref == 'eksternal') {
			//echo $surat_from_ref_data['nama'] . '<br>' . $surat_from_ref_data['title'] . ' &nbsp; | &nbsp; ' . $surat_from_ref_data['instansi'];
			echo $surat_from_ref_data['instansi'];
		} else {
			//echo ((isset($surat_from_ref_data['unit'])) ? ($surat_from_ref_data['jabatan'] . ', ' . $surat_from_ref_data['unit'] . '<br>') : '') . $surat_from_ref_data['dir'];
			echo ((isset($surat_signed['unit_name'])) ? ($surat_signed['jabatan'] . ', ' . $surat_signed['unit_name'] . '<br>') : $surat_signed['unit_name']);
		}
?>
				</td>
				<td><?php echo db_to_human($row->surat_tgl); ?></td>
				<td style="text-align: center;"><?php echo $row->status_berkas; ?></td>
				<td><?php echo $row->surat_perihal; ?></td>
				<td></td>
				<td></td>
			</tr>
<?php 
	}
?>
		</tbody>
	</table>
</div>

<div>
	<br><br>
	<table class="layout">
		<tr>
			<td class="center" width="30%"> 
				Pengirim
				<br><br><br><br>
				(<?php echo underscore(str_pad($ekspedisi->petugas_pengirim, 30, '_', STR_PAD_BOTH)); ?>)
			</td>
			<td width="70%">
				&nbsp;
			</td>
			<!--
			<td class="center">
				Penerima
				<br><br><br><br>
				(<?php echo underscore(str_pad($ekspedisi->petugas_penerima, 30, '_', STR_PAD_BOTH)); ?>)
			</td>
			-->
		</tr>
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