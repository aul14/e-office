<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
	.select2-container {
		width: 100% !important;
	}
	.filterselect{
		max-width:90px !important;
	}
	.form-control2 {
		background-color: #f2e6d9;
	}
</style>

<script type="text/javascript">

	var isShow = false;
	$(document).ready(function() {
		
		 $.fn.dataTable.ext.search.push(
			function (settings, data, dataIndex) {
				var min = $('.min').datepicker("getDate");
				var max = $('.max').datepicker("getDate");
				// need to change str order before making  date obect since it uses a new Date("mm-dd-yyyy") format for short date.
				var d = data[4].split("-");
				var startDate = new Date(d[2]+ "-" +  d[1] +"-" + d[0]);

				if (min == null && max == null) { return true; }
				if (min == null && startDate <= max) { return true;}
				if(max == null && startDate >= min) {return true;}
				if (startDate <= max && startDate >= min) { return true; }
				return false;
			}
		);

		$('.min').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
		$('.max').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
		
		$('#data_table').dataTable();
		 var table = $('#example').DataTable( {
			initComplete: function () {
				this.api().columns([1, 6, 8]).every( function () {
					var column = this;
					var select = $('<select class="filterselect"><option value="">All</option></select>')
						.appendTo( $(column.header()))
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);
	 
							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} )
						.on( 'click', function() {
							return false;
						});
	 
					column.data().unique().sort().each( function ( d, j ) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					} );
				} );
			}
		} );

			 $('.min, .max').change(function () {
                table.draw();
            });
			
	});
		
	function showDetail(uri) {
		if(!isShow) {
			$('.box-detail .overlay').removeClass('hide');
			$('.box-detail').css('top', ($(window).scrollTop()));
			$.ajax({
				type: "POST",
				url: uri,
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
			var l = $('#col-control').offset().left - 15;
			$('.box-detail').animate({ 'left': l + 'px'}, 300 );
			isShow = true;
		}
	}
	
	function printKontrak() {
		window.open('<?php echo site_url('surat/kontrak/cetak_kontrak_selesai/') ?>');
	}
	
	function hideDetail() {
		$('.box-detail').animate({ 'left': '100%'}, 300 );
		$('.box-detail .overlay').addClass('hide');
		$('.box-detail>.box-header>.box-tools').css('right', '0px');
		$('.box-detail').css('height', $(document).height() - 80);
		document.location.reload();
		isShow = false;
	}
	

</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-cogs"></i> Surat </a></li>
		<li class="active"><?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">

	<!-- Default box -->
	<div class="box-list">
		<div class="box-body">
			<div id="col-control" class="pull-left">
		
			</div>
		<div class="box-list">
		
			<tr>
				<td>Dari:&nbsp;&nbsp;</td>
                <td><input name="min" class="min form-control2" type="text" onchange="tglSelesaiChange();">&nbsp;&nbsp;</td> 
            </tr>
			<tr>
				<td>Selesai:&nbsp;&nbsp;</td>
                <td><input name="max" class="max form-control2" type="text"></td>
           </tr>
		   <br><br>
		
			<table class=" table table-heading table-datatable" id="example" style= "font-size: 12px;" width="100%">
				<thead >
					<tr>
						<th rowspan= "2" width="2%" STYLE="display:none;"> No</th>
						<th rowspan= "2" width="20%" STYLE="vertical-align:middle; text-align: center;"> Mitra <br/></th>
						<th rowspan= "2" width="10%" STYLE="vertical-align:middle; text-align: center;"> Tanggal Kontrak</th>
						<th Colspan= "2" STYLE="text-align: center;"> Durasi</th>
						<th rowspan= "2" width="5%"  STYLE="vertical-align:middle; text-align: center;"> Nilai Kontrak</th>
						<th rowspan= "2" width="5%" STYLE="vertical-align:middle; text-align: center;"> Jenis<br>Kontrak</th>
						<th rowspan= "2" width="5%" STYLE="vertical-align:middle; text-align: center;"> Nomor<br>Kontrak</th>
						<th rowspan= "2" STYLE="vertical-align:middle; text-align: center;"> Kode<br>Kontrak</th>
						<th rowspan= "2" width="10%" STYLE="vertical-align:middle; text-align: center;"> Perihal</th>
						<th rowspan= "2" width="10%" STYLE="vertical-align:middle; text-align: center;"> Alasan</th>
						<th rowspan= "2" width="2%" STYLE="vertical-align:middle;"></th>
					</tr>
					<tr>
						<th width="5%" STYLE="text-align: center;"> Mulai</th>
						<th width="5%" STYLE="text-align: center;"> Selesai</th>
					</tr>
				</thead>
				<!--
				<tfoot>				
					<tr>
						<th> No</th>
						<th> Mitra</th>
						<th> Tanggal Kontrak</th>
						<th> Status</th>
						<th> Mulai</th>
						<th> Selesai</th>
						<th> Nilai Kontrak</th>
						<th> Jenis Kontrak</th>
						<th> Nomor Kontrak</th>
						<th> Kode Kontrak</th>
						<th>Perihal</th>
						<th> </th>
					</tr>
				</tfoot>
				-->
				<tbody>
<?php 

	
	$no=0; //variabel no
	$list = $this->kontrak_model->get_kontrak_selesai_list();
//	echo $this->db->last_query();
	if(count($list) > 0) {
		foreach($list as $row) {
			$no++;
?>	
						<tr>
							<?php 
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
?>
							<td STYLE="display:none;"> <?php echo "$no"; ?></td>
							<td title="<?php echo ($row->status_berkas != '-') ? $opt_mitra[$row->status_berkas] : '-'; ?>"> <?php echo substr(($row->status_berkas != '-') ? $opt_mitra[$row->status_berkas] : '-', 0, 30); ?></td>
							<td><?php echo $row->surat_unit_lampiran; ?></td>
							<td><?php echo db_to_human($row->surat_awal); ?></td>
							<td><?php echo db_to_human($row->surat_akhir); ?></td>
							<td><?php echo $row->surat_ringkasan; ?></td>
<?php 
	$opt_jenis_kontrak = $this->kontrak_model->get_referensi_full('jenis_kontrak');
?>
							<td><?php echo ($row->jenis_surat != '-') ? $opt_jenis_kontrak[$row->jenis_surat] : '-'; ?></td>
							<td><?php echo $row->surat_no; ?></td>
<?php 
	$opt_kode_kontrak = $this->kontrak_model->get_referensi_full('kode_kontrak');
?>
							<td><?php echo ($row->sifat_surat != '-') ? $opt_kode_kontrak[$row->sifat_surat] : '-'; ?></td>
							<td><?php echo $row->surat_perihal; ?></td>
<?php 
	$opt_alasan = $this->admin_model->get_system_cm_config('alasan');
?>
							<td> <?php echo ($row->catatan_pengiriman != '-') ? $opt_alasan[$row->catatan_pengiriman] : '-'; ?></td>
							
							<td>
								<a class="btn btn-info btn-xs list-data" onclick="showDetail('<?php echo site_url('surat/kontrak/kontrak_view/' . $row->surat_id); ?>')" title="Details">Details</a>
							</td>
						</tr>
<?php
						}
					}
?>
				</tbody>
			</table>
				<button type="button" class="btn btn-app" onclick="printKontrak();">
						<i class="fa fa-print"></i> Cetak
				</button>	
			</div>
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
		
	</div><!-- /.box -->
</section><!-- /.content -->
