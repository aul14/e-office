<script type="text/javascript">
	$(document).ready(function() {
		$('#data_table').dataTable();
	});
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Dashboard <small>Search Result</small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li><?php echo get_user_data('user_name'); ?></li>
		<li class="active">Search Result</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	
	<!-- Default box -->
	<div class="box box-danger">
		<div class="box-header with-border">
			<h3 class="box-title">Search Result for : <i>"<?php echo $search_keyword; ?>"</i></h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
			</div>
		</div>

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
						
						</td>
					</tr>
<?php 
		}
	}
?>
				</tbody>
			</table>
		</div><!-- /.box-body -->

	</div><!-- /.box -->

</section><!-- /.content -->