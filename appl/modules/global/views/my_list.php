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
 * @filesource my_list.php
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
	<h1>My Task<small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-task"></i> My Task </a></li>
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<!-- Default box -->
	<div class="box box-primary">
		<!-- div class="box-header with-border">
			<h3 class="box-title">Surat Masuk belum disposisi</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div -->

		<div class="box-body">
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
				<thead>
					<tr>
						<th width="150">Agenda</th>
						<th>Surat</th>
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
							<strong><?php echo $row->no_agenda; ?></strong><br>
						</td>
						<td><?php echo $row->deskripsi; ?></td>
						<td>
							<a class="btn btn-info" href="<?php echo site_url($row->link); ?>" target="_blank" title="Lihat Surat"><i class="fa fa-eye "></i></a>
<?php 
			if(get_user_data('unit_id') && $row->status == 99) {
?>
							<a class="btn btn-warning" href="<?php echo site_url($row->link); ?>" target="_blank" title="simpan sebagai arsip"><i class="fa fa-archive "></i></a>
<?php 
			}
?>						
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