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

<?php
// header("Content-type: application/vnd-ms-excel");
// header("Content-Disposition: attachment; filename=Lap. Kontrak Yg Akan Berakhir.xls");
?>
<style>
	.select2-container {
		width: 100% !important;
	}
	.filterselect{
		max-width:110px !important;
	}
	.container {
       min-width: 0%;
       margin: 100 auto;
  	}
  	.form-control2 {
	    background-color: #f2e6d9;
	 }
  	.background3 {
	    background: #ddd9f1 ;
	 }
	
</style>

<script type="text/javascript">

	var isShow = false;
		$(document).ready(function() {
		 	$.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var min = $('.min').datepicker("getDate");
                    var d1 = $('.max').val();
                    var endDate = toEndDate(d1);
                    var max = (d1 == '') ? $('.max').datepicker("getDate") : endDate;
                    // need to change str order before making  date obect since it uses a new Date("mm-dd-yyyy") format for short date.
                    var d = data[4].split("-");
                    var startDate = new Date(d[2]+ "-" +  d[1] +"-" + d[0]);
                    
                    if (min == null && max == null) { return true; }
                    if (min == null && startDate <= max) { return true;}
                    if (max == null && startDate >= min) {return true;}
                    if (startDate >= min && startDate <= max) { return true; }
                    return false;
                }
            );
       
            $('.min').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
			$('.max').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
          
		  	var table = $('#example').DataTable( {

			initComplete: function () {
				this.api().columns([1, 7, 9]).every( function () {
					var column = this;
					var select = $('<select class="filterselect"><option value="">All</option></select>')
						.appendTo( $(column.header()) )
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
					}); 
				}); 
			}
		});

		// $('.filterselect').on('click', function() {
		// 	return false;
		// });
		 
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
		window.open('<?php echo site_url('surat/kontrak/cetak_kontrak/') ?>');
	}
	
	function hideDetail() {
		$('.box-detail').animate({ 'left': '100%'}, 300 );
		$('.box-detail .overlay').addClass('hide');
		$('.box-detail>.box-header>.box-tools').css('right', '0px');
		$('.box-detail').css('height', $(document).height() - 80);
		document.location.reload();
		isShow = false;
	}
	
	function toEndDate( date ) {
		var date = date.split("-");

		//(year, month, day, hours, minutes, seconds, milliseconds)
		//subtract 1 from month because Jan is 0 and Dec is 11
		return new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);
	}

	function toJSDate( date ) {
		var date = date.split("-");

		//(year, month, day, hours, minutes, seconds, milliseconds)
		//subtract 1 from month because Jan is 0 and Dec is 11
		return new Date(date[2], (parseInt(date[1])-1), date[0], 0, 0, 0, 0);
	}
			
	function tglSelesaiChange() {
		var date = $('.min').val().split("-");
		var nextDate = new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);

		$('.max').datepicker('option', 'minDate', new Date(nextDate));

		if(toJSDate($('.min').val()) > toJSDate($('.max').val())) {
			$('.max').val(nextDate.getDate() + '-' + (nextDate.getMonth() + 1) + '-' + nextDate.getFullYear())
		}		
	}
	
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Surat<small> <?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat </a></li>
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
		<div class="box-list ">
			<tr>
				<td>Dari:&nbsp;&nbsp;</td>
                <td><input name="min" class="min form-control2" type="text">&nbsp;&nbsp;</td> 
            </tr>            
			<tr>
				<td>Selesai:&nbsp;&nbsp;</td>
                <td><input name="max" class="max form-control2" type="text"></td>
           </tr>
		   <br><br>
			<table class="display table table-heading table-datatable" id="example" style="font-size: 12px;" width="100%">
				<thead >
					<tr>
						<th rowspan= "2" width="2%" STYLE="display:none;"> No</th>
						<th rowspan= "2" width="25%" STYLE="vertical-align:middle; text-align: center;"> Mitra </th>
						<th rowspan= "2" width="10%" STYLE="vertical-align:middle; text-align: center;"> Tanggal Kontrak</th>
						<th rowspan= "2" width="10%" STYLE="vertical-align:middle; text-align: center;"> Status</th>
						<th Colspan= "2" width="10%" STYLE="text-align: center;"> Durasi</th>
						<th rowspan= "2" width="10%" STYLE="vertical-align:middle; text-align: center;"> Nilai Kontrak</th>
						<th rowspan= "2" width="5%" STYLE="vertical-align:middle; text-align: center;"> Jenis<br>Kontrak</th>
						<th rowspan= "2" width="5%" STYLE="vertical-align:middle; text-align: center;"> Nomor<br>Kontrak</th>
						<th rowspan= "2" STYLE="vertical-align:middle; text-align: center;"> Kode<br>Kontrak</th>
						<th rowspan= "2" width="20%" STYLE="vertical-align:middle; text-align: center;"> Perihal</th>
						<th rowspan= "2" width="2%" STYLE="vertical-align:middle;"></th>
					</tr>
					<tr>
						<th width="5%" STYLE="text-align: center;"> Mulai</th>
						<th width="5%" STYLE="text-align: center;"> Selesai</th>
					</tr>
				</thead>
				<tbody>
<?php 

	 $no=0;  //variabel no
	$list = $this->kontrak_model->get_kontrak_aktif_list();
	if(count($list) > 0) {
		foreach($list as $row) {
				 $no++;
				$tgl_akhir = new dateTime($row->surat_akhir);
				$tgl_skrng = new DateTime();
				$diff = $tgl_akhir->diff($tgl_skrng);
				$diff;
				
				// cek tanggal berakhir kontrak
				if($tgl_skrng > $tgl_akhir)
				{
					$this->kontrak_model->selesaikan_kontrak($row->surat_id);
				}

	$opt_kode_merah		= $this->admin_model->get_contract_config('kode_warna', 'kurang_dari_sebulan');
	$opt_kode_kuning	= $this->admin_model->get_contract_config('kode_warna', 'kurang_dari_3bulan');
	$opt_kode_hijau		= $this->admin_model->get_contract_config('kode_warna', 'kurang_dari_6bulan');
	
				if ($diff->days <= 30)
				{
?>								
				<tr bgcolor= <?php echo $opt_kode_merah; ?>>
<?php			}
				else
				{ 	
					if ($diff->days <= 90)
					{
?>
						<tr bgcolor= <?php echo $opt_kode_kuning; ?>>
<?php 				}
					else
					{
						if ($diff->days <= 180)
						{
?>
						<tr bgcolor= <?php echo $opt_kode_hijau; ?>>
<?php				     }
						else
						{
?>
							<tr>
<?php					}
					}
				}
?>			
					
<?php 
	$opt_mitra = $this->kontrak_model->get_referensi_full('mitra');
?>
					<td STYLE="display:none;"><?php echo "$no"; ?></td>
					<td title="<?php echo ($row->status_berkas != '-') ? $opt_mitra[$row->status_berkas] : '-'; ?>"> <?php echo substr(($row->status_berkas != '-') ? $opt_mitra[$row->status_berkas] : '-', 0, 300); ?></td>
					<td> <?php echo $row->surat_unit_lampiran; ?></td>
<?php
						if ($row->status == 2){
?>
							<td>Addendum 2</td>
<?php
						}else{
							if ($row->status == 1){
?>
								<td>Addendum 1</td>
<?php
							}else{
								if ($row->status == 0){
?>
									<td>Kontrak</td>
<?php
								}
							}
						}
?>			
					<td> <?php echo db_to_human($row->surat_awal); ?></td>
					<td> <?php echo db_to_human($row->surat_akhir); ?></td>
					<td> <?php echo $row->surat_ringkasan; ?></td>
<?php 
	$opt_jenis_kontrak = $this->kontrak_model->get_referensi_full('jenis_kontrak');
?>
					<td> <?php echo ($row->jenis_surat != '-') ? $opt_jenis_kontrak[$row->jenis_surat] : '-'; ?></td>
					<td> <?php echo $row->surat_no; ?></td>
<?php 
	$opt_kode_kontrak = $this->kontrak_model->get_referensi_full('kode_kontrak');
?>
					<td style="text-align: center;"> <?php echo ($row->sifat_surat != '-') ? $opt_kode_kontrak[$row->sifat_surat] : '-'; ?></td>
					<td> <?php echo $row->surat_perihal; ?></td>
					<td>
						<a class="btn btn-info btn-xs list-data" onclick="showDetail('<?php echo site_url($row->link); ?>')" title="Details">Details</a>
					</td>
				</tr>
<?php
		}
	}
?>
			</tbody>
	</table>
				<div class="box-body form-group">
				<TABLE>
					<tr>
						<td style="border: 0; padding: 10px; background-color: #c39275; text-align: left; width: 10%">
						</td>
						<td style="background-color: #c7cac0;">
						 &nbsp; : Kurang 1 Bulan Lagi &nbsp;&nbsp;
						</td>
						<td style="border: 0; padding: 10px; background-color: #d0b788; text-align: left; width: 10%">
						</td>
						<td style = "background-color: #c7cac0;">
						 &nbsp; : Kurang 3 Bulan Lagi &nbsp;&nbsp;
						</td>
						<td style="border: 0; padding: 10px; background-color: #a2be99; text-align: left; width: 10%">
						</td>
						<td style = "background-color: #c7cac0;">
						&nbsp; : Kurang 6 Bulan Lagi 
						</td>
					</tr>
				</TABLE>
				</div>		
				<br><br>
				<button type="button" class="btn btn-app" onclick="printKontrak();">
						<i class="fa fa-print"></i> Cetak
				</button>	
			</div>
		</div><!-- /.box-body -->
		
		<div class="box-detail">
			<div class="box" style="height: auto; background-color: #ecf0f5;">
				<div class=" box-header with-border" style="z-index: 51;">
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
