<?php	if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * PHP 5
 *
 * Application System Environment (X-ASE)
 * laxono :  Rapid Development Framework (http://www.laxono.us)
 * Copyright 2011-2015.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource pre_disposisi.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Oct 12, 2016
 * @version 
 * @modifiedby 
 * @lastmodified	
 *
 *
 */

?>
<script type="text/javascript">
<!--
	$(document).ready(function() {
		$('#data_table').dataTable();
	});
	
//-->
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat Masuk<small> belum disposisi</small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat </a></li>
		<li><a href="#">Disposisi</a></li>
		<li class="active">Surat Masuk belum disposisi</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">

	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Surat Masuk belum disposisi</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>

		<div class="box-body">
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
				<thead>
					<tr>
						<th width="150">Surat</th>
						<th>Asal Surat</th>
						<th width="100">Tindakan</th>
					</tr>
				</thead>
				<tbody>
<?php 
	if($list->num_rows() > 0) {
		foreach($list->result() as $row) {
?>
					<tr>
						<td>
							<strong><?php echo $row->jenis_agenda . '-' . $row->agenda_id; ?></strong><br>
							<?php echo $row->surat_no; ?><br>
							<?php echo db_to_human($row->surat_tgl); ?>
						</td>
						<td>
							<?php echo $row->surat_ext_title; ?><br>
							<?php echo $row->surat_ext_nama; ?><br>
							<?php echo $row->surat_perihal; ?>
						</td>
						<td>
							<a class="btn btn-info" href="<?php echo site_url('surat/disposisi/create_from/surat_eksternal/' . $row->surat_eksternal_id); ?>" target="_blank" title="Buat disposisi"><i class="fa fa-edit"></i></a>
							<a class="btn btn-info" href="<?php echo site_url('surat/external/incoming/' . $row->surat_eksternal_id); ?>" target="_blank" title="Lihat Surat"><i class="fa fa-eye "></i></a>						
						</td>
					</tr>
<?php 
		}
	}
?>
				</tbody>
			</table>
		</div><!-- /.box-body -->

		<div class="box-footer">
			
		</div><!-- /.box-footer-->
	</div><!-- /.box -->

</section><!-- /.content -->
	