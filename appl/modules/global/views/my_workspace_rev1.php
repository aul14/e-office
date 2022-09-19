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
 			"ordering": false,
// 			"order" : [[ 0, "desc" ]],
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
<?php 
	$tgl_header = 'Tanggal Terima';
	$tgl_disposisi = '';
	if($list->num_rows() > 0) {
		foreach($list->result() as $row) {
			if($row->jenis_agenda != 'SME') {
				$tgl_header = 'Tanggal Konsep Surat';
			}else {
				$tgl_header = 'Tanggal Terima Surat';
				$tgl_disposisi = 'Tanggal Disposisi';
			}
		}
	}
?>	
		<div id="col-control" class="pull-left">
			<h5><?php //echo $title; ?></h5>
		</div>
		<div class="box-list">
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
				<thead>
					<tr>
<?php	
	if($tgl_disposisi != '') {
?>
						<th colspan="9"><?php echo $title; ?></th>
<?php
	}else {
?>						
						<th colspan="8"><?php echo $title; ?></th>
<?php
	}
?>			
					</tr>
					<tr>
						<th>Agenda</th>
						<th>No Surat</th>
						<th>Tanggal Surat</th>
						<th><?php echo $tgl_header; ?></th>
						<th>Perihal</th>
						<th>Instansi</th>
<?php	
	if($tgl_disposisi != '') {
?>
						<th>Tanggal Disposisi</th>
<?php
	}
?>
						<th>Keterangan</th>
						<th>Status</th>
					</tr>	
				</thead>
				<tbody>
				
<?php 
	if($list->num_rows() > 0) {
		foreach($list->result() as $row) {
			if ($row->status != 404) {
				$disposisi_status = "";
				$instansi = "";
				if($row->jenis_agenda == 'SME') {
					$surat_from_ref = json_decode($row->surat_from_ref_data, TRUE);
					$distribusi = json_decode($row->distribusi_disposisi, TRUE);
					$disposisi_tujuan = array();
					if (isset($distribusi)){
						foreach($distribusi as $key => $val) {
							$disposisi_tujuan[] = $distribusi[$val['user_id']];
						}
					}
					$instansi = $surat_from_ref['instansi'];
					$tgl_awal = $row->tgl_terima_surat;
					if (($row->status_disposisi == 1 || $row->status_disposisi == 99) && $row->status >= 4) {
						$disposisi_status = "#C39275 !important";
					}
				}else if ($row->jenis_agenda == 'SKE') {
					$surat_to_ref = json_decode($row->surat_to_ref_data, TRUE);
					$instansi = $surat_to_ref['instansi'];
					$tgl_awal = $row->surat_awal;
				}else if ($row->jenis_agenda == 'SI') {
					if (($row->status_disposisi == 1 || $row->status_disposisi == 99) && $row->status == 6) {
						$disposisi_status = "#C39275 !important";
					}
					$tgl_awal = $row->surat_awal;	
				}else if ($row->jenis_agenda == 'ST') {
					$tgl_awal = $row->surat_awal;
				}			
?>
					<tr style="background-color:<?php echo $disposisi_status; ?>">
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php if($row->agenda_id != '-') { ?><strong><?php echo $row->no_agenda; ?></strong><br> <?php } else { ?><strong><?php echo $row->jenis_agenda; ?></strong><br><?php } ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><strong><?php echo $row->no_surat; ?></strong></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->tgl_surat; ?></a></td>
						<td class="data-list" width="13%"><a href="<?php echo site_url($row->link1); ?>"><?php echo $tgl_awal; ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->perihal_surat; ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo ($instansi) ? $instansi : '-'; ?></a></td>
<?php
	if($tgl_disposisi != '') {
?>
						<td class="data-list"><?php echo $row->tgl_disposisi; ?></td>
<?php
	}
?>						
						<td class="data-list">
						<?php
							if(isset($disposisi_tujuan)) {
								$i = 0;
								$user_disposisi = '';
								foreach($disposisi_tujuan as $disposisi_to){
									$separator = ($i > 0) ? '&nbsp; | &nbsp;' : '';
									$user_disposisi = ($disposisi_to != '') ? $user_disposisi . $separator . $disposisi_to['jabatan'] . ' ' . $disposisi_to['unit_name'] : '';
									
									$i++;
								} 

								echo $user_disposisi;
							}
						?>
						</td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>">
						<?php 
							if($row->type_disposisi == 'disposisi') {
								if(isset($disposisi_tujuan) && $row->status_disposisi == 1) {
									$i = 0;
									$status_disposisi = '';
									foreach($disposisi_tujuan as $disposisi_to){
										
										switch ($disposisi_to['status']) {
											case '0':
												$status = 'Kirim';
												break;
											case '99':
												$status = 'Selesai';
												break;
										}

										$separator = ($i > 0) ? '&nbsp; | &nbsp;' : '';
										$status_disposisi = ($disposisi_to != '') ? $status_disposisi . $separator . $status : '';
										
										$i++;
									} 

									echo $status_disposisi;
								}else {
									echo $row->status_surat;	
								}
							}else {
								echo $row->status_surat;
							} 
						?>
						</a></td>
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
	