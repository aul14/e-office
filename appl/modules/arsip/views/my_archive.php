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

	var isShow = false;
	$(document).ready(function() {
	/*	$.fn.dataTable.ext.search.push(
            function (settings, data, dataIndex) {
                var month = $('#arc_month').val();
                var year = $('#arc_year').val();
                var tgl_cari = year + " - " + month;

                // need to change str order before making  date obect since it uses a new Date("mm-dd-yyyy") format for short date.
                var d = data[3].split("-");
                var startDate = new Date(d[2] + "-" + d[1]);

                if (startDate == tgl_cari) { return true;}
                
                return false;
            }
        );
    */

		var table = $('#data_table').dataTable({
			"dom":
				"<'row'<'col-xs-6'i><'col-xs-6'f>>" +
				"<'row'<'col-sm-12'tr>>",
			"bPaginate": false,
			"ordering": false, //[[ 0, "desc" ]],
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

		$('#form-cari').submit(function(){
			$('#btn-submit').trigger('click');
			return false;
		});	

		$('#btn-submit').click(function(){
			var month = $('#arc_month').val();
			var year = $('#arc_year').val();
			var tgl_arsip = month+'-'+year;
	    	
			filterArsip(tgl_arsip);
		});

	}); //end document

	function filterArsip(search_date) {
	    $('#data_table').DataTable().column(2).search(
	        search_date
	    ).draw();
	}

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
	if($type == 'surat_masuk_eksternal') {
		$tgl_header = 'Tanggal Terima Surat';
		$instansi_header = 'Instansi';
		$keterangan_header = 'Keterangan';
	}else if ($type == 'tugas') {
		$tgl_header = 'Tanggal Konsep Surat';
		$instansi_header = 'Keperluan';
		$keterangan_header = 'Menugaskan Kepada';
	}else {
		$tgl_header = 'Tanggal Konsep Surat';
		$instansi_header = 'Instansi';
		$keterangan_header = 'Keterangan';
	}

	// memanggil form dropdown untuk tahun
	echo form_open('', ' class="form-horizontal" id="form-cari"');
	$arc_year = (isset($year)) ? $year : date('Y');
	$opt_year = array();
	for($i = 0; $i < 3; $i++) {
		$k = date('Y') - $i;
		$opt_year[$k] = $k;
	}

	// memanggil dropdown untuk form bulan pada search
	$arc_month = (isset($month)) ? $month : date('m');
	$opt_month = $this->admin_model->get_system_cm_config('option_month_long');
?>
		<div class="form-group">
			<div class="col-xs-4">
				<?php echo form_dropdown('arc_year', $opt_year, $arc_year, (' id="arc_year" class="form-control" ')); ?>
			</div>
			<div class="col-xs-5">
				<?php echo form_dropdown('arc_month', $opt_month, $arc_month, (' id="arc_month" class="form-control" ')); ?>
			</div>
			<div class="col-xs-3">
				<button class="btn btn-info btn-block" id="btn-submit"> <i class="fa fa-search list-data"></i> Tampilkan</button>
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
						<th colspan="8"><?php echo $title; ?></th>
					</tr>
					<tr>	
						<th>Agenda</th>
						<th>No Surat</th>
						<th>Tanggal Surat</th>
						<th><?php echo $tgl_header; ?></th>
						<th>Perihal</th>
						<th><?php echo $instansi_header; ?></th>
						<th><?php echo $keterangan_header; ?></th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
<?php 
	if($list->num_rows() > 0) {
		foreach($list->result() as $row) {
			if ($row->status != 404) {
				$instansi = '';
				$user_distribusi = '';
				if($row->jenis_agenda == 'SME') {
					$surat_from_ref = json_decode($row->surat_from_ref_data, TRUE);
					$distribusi = json_decode($row->distribusi_disposisi, TRUE);
					$disposisi_tujuan = array();
					if (isset($distribusi)){
						foreach($distribusi as $key => $val) {
							//$disposisi_tujuan[] = (isset($row->to_user_id)) ? $distribusi[$val['user_id']] : '';
							$disposisi_tujuan[] = $distribusi[$val['user_id']];
						}
					}
					$instansi = $surat_from_ref['instansi'];
				} else if ($row->jenis_agenda == 'SKE') {
					$surat_to_ref = json_decode($row->surat_to_ref_data, TRUE);
					$instansi = $surat_to_ref['instansi'];
				} else if ($row->jenis_agenda == 'ST') {
					$i = 0;
					$distribusi_tujuan = json_decode($row->distribusi_tujuan, TRUE);
					foreach ($distribusi_tujuan as $distribusi) {
						$separator = ($i > 0) ? '&nbsp; | &nbsp;' : '';
						$user_distribusi = ($distribusi != '') ? $user_distribusi . $separator . $distribusi['nama'] : '';
						$instansi = $distribusi['keperluan'];

						$i++;
					}

					$tujuan_distribusi = $user_distribusi;
				}	
?>
					<tr>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>">
							<strong><?php echo $row->no_agenda; ?></strong></a>
						</td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>">
							<strong><?php echo $row->no_surat; ?></strong></a>
						</td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->tgl_surat; ?></a></td>
						<td width="17%" class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo ($row->tgl_terima_surat) ? $row->tgl_terima_surat : $row->awal_surat; ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->perihal; ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo ($instansi) ? $instansi : '-'; ?></a></td>
						<td class="data-list">
						<?php
							if(isset($disposisi_tujuan)) {
								$i = 0;
								$user_disposisi = '';
								foreach($disposisi_tujuan as $disposisi_to) {
									$separator = ($i > 0) ? '&nbsp; | &nbsp;' : '';
									$user_disposisi = ($disposisi_to != '') ? $user_disposisi . $separator . $disposisi_to['jabatan'] . ' ' . $disposisi_to['unit_name'] : '';
									
									$i++;
								} 

								echo $user_disposisi;
							} else if (isset($tujuan_distribusi)) {
								echo $tujuan_distribusi;
							}
						?>
						</td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->status_surat; ?></a></td>
					</tr>
					<!--
					<tr>
						<td class="list-data" data-link="<?php //echo site_url($row->link); ?>">
							<strong><?php //echo $row->no_agenda; ?></strong><br>
							<?php //echo $row->deskripsi; ?>
						</td>
					</tr>
					-->
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
	