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
	var isShow = false;
	$(document).ready(function() {
		$('#data_table').dataTable({
			"dom":
				"<'row'<'col-xs-6'i><'col-xs-6'f>>" +
				"<'row'<'col-sm-12'tr>>",
			"bPaginate": false,
			"order": [[ 0, "desc" ]],
			"columnDefs": [
			             { orderable: false, targets: 0 }
			          ],
			"oLanguage": {
				"sSearch": "Filter : ",
				"sInfo": "<strong> _TOTAL_ </strong> data "
			}
		});

		//$('.list-data').css('cursor', 'pointer');

		$('.list-data').click(function() {
			if(!isShow) {
				$('.box-detail .overlay').removeClass('hide');
				$('.box-detail').css('top', ($(window).scrollTop()));
				$.ajax({
					type: "POST",
					url: $(this).attr('data-link'),
					success: function(data){
						$('.box-detail .box-body').html(data + '<div class="clearfix"></div>');
					//	$(".gototop").trigger('click');
						if($('.box-detail').height() < $('.box-detail .box-body').height()) {
							$('.box-detail').css('height', ($('.box-detail .box-body').height() + 110) + 'px');
						}
						$('.box-detail .overlay').addClass('hide');
					}
				});
				$('.box-detail>.box-header>.box-tools').css('right', '10px');
				var l = $('#col-control').offset().left + 3;
				$('.box-detail').animate({ 'left': l + 'px'}, 300 );
				isShow = true;
			}
		});

	});

	function hideDetail() {
		$('.box-detail').animate({ 'left': '100%'}, 300 );
		$('.box-detail .overlay').addClass('hide');
		$('.box-detail>.box-header>.box-tools').css('right', '0px');
		$('.box-detail').css('height', $(document).height() - 80);
		isShow = false;
	}

//-->
</script>
<!-- Main content -->
<section class="content">
	
		<div id="col-control" class="pull-left">

		</div>
		<div id="col-option" class="">
		
<?php 
// memanggil form dropdown untuk tahun
	echo form_open('', ' class="form-horizontal"');
	$arc_year = (isset($year)) ? $year : date('Y');
	$opt_year = array();
	for($i = 0; $i < 3; $i++) {
		$k = date('Y') - $i;
		$opt_year[$k] = $k;
	}

// memanggil dropdown untuk form bulan pada search
	$arc_month = (isset($month)) ? $month : date('m');
	$opt_month = $this->admin_model->get_system_config('option_month_long');
	
?>
		<div class="form-group">
			<div class="col-xs-4">
				<?php echo form_dropdown('arc_year', $opt_year, $arc_year, (' id="arc_year" class="form-control" ')); ?>
			</div>
			<div class="col-xs-5">
				<?php echo form_dropdown('arc_month', $opt_month, $arc_month, (' id="arc_month" class="form-control" ')); ?>
			</div>
			<div class="col-xs-3">
				<button class="btn btn-info btn-block"> <i class="fa fa-search list-data"></i> Tampilkan</button>
			</div>
		</div>
<?php 
	echo form_close();
?>
		</div>
		<div class="box-list">
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
				<thead>
					<tr>
						<th><?php echo $title; ?></th>
					</tr>
				</thead>
				<tbody>
<?php 
	if($list->num_rows() > 0) {
		foreach($list->result() as $row) {
?>
					<tr>
						<td class="list-data" data-link="<?php echo site_url($row->link); ?>">
							<strong><?php echo $row->no_agenda; ?></strong><br>
							<?php echo $row->deskripsi; ?>
						</td>
					</tr>
<?php 
		}
	}
?>
				</tbody>
			</table>
		</div><!-- /.box-body -->
		<div class="box-detail">
			<div class="box" style="height: auto; background-color: #ecf0f5;">
				<div class="box-header with-border" style="z-index: 51;">
					<button class="btn btn-box-tool" title="Collapse" onclick="hideDetail();"><i class="fa fa-chevron-right"></i></button>
					<h3 class="box-title"></h3>
					<div class="box-tools pull-right"></div>
				</div>
		
				<div class="box-body">
					
					
				</div>
				<div class="overlay hide">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
			</div>
			
		</div>
</section><!-- /.content -->
	