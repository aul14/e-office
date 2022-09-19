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
 * @filesource klasifikasi_arsip.php
 * @copyright Copyright 2011-2016, laxono.us.
 * @author blx
 * @package 
 * @subpackage	
 * @since Oct 17, 2016
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
	<h1>Administration<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-cogs"></i> Administration </a></li>
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">

	<!-- Default box -->
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title"><?php echo $title; ?></h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
			</div>
		</div>

		<div class="box-body">
			<div class="well">
				<div class="btn-group">
					<button type="button" name="btnAdd" class="btn btn-primary" onclick="location.assign('<?php echo site_url('global/admin/klasifikasi_arsip_detail'); ?>')"><i class="fa fa-plus"></i> Add</button>
				</div>
			</div>
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
				<thead>
					<tr>
						<th width="20">Kode</th>
						<th>Name</th>
						<th width="60">Parent ID</th>
						<th width="60">Sort</th>
						<th width="100">Action</th>
					</tr>
				</thead>
				<tbody>
<?php 
	$list = $this->admin_model->get_klasifikasi_list();

	if(count($list) > 0) {
		foreach($list as $row) {
?>
					<tr>
						<td><?php echo $row['kode_klasifikasi']; ?></td>
						<td><?php echo $row['klasifikasi_tree']; ?></td>
						<td><?php echo $row['parent_id']; ?></td>
						<td><?php echo $row['sort']; ?></td>
						<td>
							<a class="btn btn-info btn-xs" href="#" onclick="location.assign('<?php echo site_url('global/admin/klasifikasi_arsip_detail/' . $row['entry_id']); ?>')" title="Edit"><i class="fa fa-edit "></i></a>
						
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
	