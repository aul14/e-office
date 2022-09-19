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
<style>
	.select2-container {
		width: 100% !important;
	}
	.data-list a {
		color: #333;
	}
	.header-title {
		text-align: center;
	}
	.table > thead > tr > th {
		vertical-align: middle !important;
	}
</style>

<script type="text/javascript">
<!--
	var isShow = false;
	$(document).ready(function() {
		$('#data_table').dataTable({
			"dom":
				"<'row'<'col-sm-6'i><'col-sm-6'f>>" +
				"<'row'<'col-sm-12'tr>>",
			"bPaginate": false,
 			"ordering": false,//[[ 0, "desc" ]],
// 			"columnDefs": [
// 			             { orderable: false, targets: 0 }
// 			          ],
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
						initPage();
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
		document.location.reload();
		isShow = false;
	}

//-->
</script>
<!-- Main content -->
<section class="content">
	
		<div id="col-control" class="pull-left">
			<h5><?php //echo $title; ?></h5>
		</div>
		<div class="box-list">
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
				<thead>
					<tr>
						<th colspan="9"><?php echo $title; ?></th>
					</tr>
					<tr>
						<th>Agenda</th>
						<th>No Surat</th>
						<th>Tanggal Surat</th>
						<th>Tanggal Terima Surat</th>
						<th>Perihal</th>
						<th>Instansi</th>
						<th>Status</th>
						<th>Tanggal Hapus</th>
						<th>Keterangan</th>
				</thead>
				<tbody>
				
<?php 
	if($list->num_rows() > 0) {
		foreach($list->result() as $row) {
			if ($row->status == 404) {
				$disposisi_status = "";
				$instansi = "";
				if($row->jenis_agenda == 'SME') {
					$surat_from_ref = json_decode($row->surat_from_ref_data, TRUE);
					$instansi = $surat_from_ref['instansi'];
					
				}else if ($row->jenis_agenda == 'SKE') {
					$surat_to_ref = json_decode($row->surat_to_ref_data, TRUE);
					$instansi = $surat_to_ref['instansi'];
				}			
?>
					<tr>
						<td class="data-list"><?php if($row->agenda_id != '-') { ?><strong><?php echo $row->no_agenda; ?></strong><br> <?php } else { ?><strong><?php echo $row->jenis_agenda; ?></strong><br><?php } ?></td>
						<td class="data-list"><strong><?php echo $row->no_surat; ?></strong></td>
						<td class="data-list"><?php echo $row->tgl_surat; ?></td>
						<td class="data-list"><?php echo ($row->tgl_terima_surat) ? $row->tgl_terima_surat : '-'; ?></td>
						<td class="data-list"><?php echo $row->perihal_surat; ?></td>
						<td class="data-list"><?php echo ($instansi) ? $instansi : '-'; ?></td>
						<td><?php echo $row->status_surat; ?></td>
						<td><?php echo $row->tgl_hapus_surat; ?></td>
						<td><?php echo $row->alasan_hapus; ?></td>
					</tr>
<?php 
			}
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
	