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
<style>
	.data-list a {
		color: #333;
	}
</style>

<script type="text/javascript">
<!--
	$(document).ready(function() {
		$('#data_table').dataTable();
	});
	
	function read_notify(notify_id) {
		$.ajax({
			type: "POST",
			url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/notification')); ?>",
			data: { notify_id: notify_id },
			success: function(data) {
				if(data == 1) {
					return true;
				} else {
					bootbox.alert(data);
				}
			}
		});
	}
//-->

</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>User<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-cogs"></i> User </a></li>
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
				<!-- <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button> -->
			</div>
		</div>

		<div class="box-body">
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
				<thead>
					<tr>
						<th width="20">No</th>
						<th>Tanggal</th>
						<th>Agenda</th>
						<th>Note</th>
						<th width="80"></th>
					</tr>
				</thead>
				<tbody>
<?php 
	$n = 1;
	$list = $this->dashboard_model->get_my_notification();

	if(count($list) > 0) {
		foreach($list->result() as $row) {
			$tgl_notif = Date('d-m-Y H:i:s', strtotime($row->created_time));
?>
					<tr>
						<td align="center"><?php echo $n; ?></td>
						<td align="center"><?php echo $tgl_notif; ?></td>
						<td align="center"><?php echo $row->agenda; ?></td>
						<td class="data-list"><a href="<?php echo site_url() . $row->detail_link; ?>" onclick="read_notify(<?php echo $row->notify_id; ?>);"><?php echo $row->note; ?></a></td>
						<td><a class="btn btn-info btn-xs" href="<?php echo site_url() . $row->detail_link; ?>" onclick="read_notify(<?php echo $row->notify_id; ?>);" title="View">View</a></td>
					</tr>
<?php 
			$n++;
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