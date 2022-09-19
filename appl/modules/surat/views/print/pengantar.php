<?php 
	list($pengantar_d, $pengantar_t) = explode(' ', $pengantar->created_time); 
	$tgl_pengantar = db_to_human($pengantar_d);
	
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

<div class="center">
	<h5>Surat Pengantar Surat Masuk Eksternal</h5>
	<h4 style="margin-bottom: 5px;">Lembar Pengantar</h4>
	<span> Dari Bagian Tata Usaha </span>
</div>
<br>
<div>
	<table>
		<tr>
			<td>Ditujukan Kepada </td>
			<td> : <?php echo $pengantar->tujuan_unit; ?></td>
		</tr>
		<tr>
			<td>Tanggal </td>
			<td> : <?php  echo $tgl_pengantar; ?></td>
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
<?php 
	$i = 1;
	echo count($list_surat);
	foreach ($list_surat as $row) {	
?>
			<tr>
				<td class="center"><?php echo $i++; ?>.</td>
				<td><?php echo $row->jenis_agenda . '-' . $row->agenda_id; ?></td>
				<td><?php echo $row->surat_no; ?></td>
				<td><?php echo $row->surat_ext_nama . '<br>' . $row->surat_ext_title . ' &nbsp; | &nbsp; ' . $row->surat_ext_instansi; ?></td>
				<td><?php echo db_to_human($row->surat_tgl); ?></td>
				<td style="text-align: center;"><?php echo $row->status_berkas; ?></td>
				<td><?php echo $row->surat_perihal; ?></td>
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
			<td class="center"> 
				Pengantar
				<br><br><br><br>
				(<?php echo underscore(str_pad($pengantar->petugas_pengirim, 30, '_', STR_PAD_BOTH)); ?>)
			</td>
			<td class="center">
				Penerima
				<br><br><br><br>
				(<?php echo underscore(str_pad($pengantar->petugas_penerima, 30, '_', STR_PAD_BOTH)); ?>)
			</td>
		</tr>
	</table>
</div>
<footer>
<div style="font-size: 8pt; padding-top: 4px; margin-bottom: 1px;">
	<table class="layout">
		<tr>
			<td><br>system id : <?php echo $ekspedisi_id; ?></br></td>
			<td style="right: 20px;"><br>printed on : <?php echo date('Y-m-d H:i:s'); ?></br></td>
		</tr>
	</table>
</div>
</footer>