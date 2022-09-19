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
                    var d = data[3].split("-");
                    var startDate = new Date(d[2] + "-" + d[1] + "-" + d[0]);

                    if (min == null && max == null) { return true; }
                    if (min == null && startDate <= max) { return true;}
                    if (max == null && startDate >= min) {return true;}
                    if (startDate >= min && startDate <= max) { return true; }
                    return false;
                }
            );

		 	$.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var minx = $('.minx').datepicker("getDate");
                    var d2 = $('.maxx').val();
                    var endDate = toEndDate(d2);
                    var maxx = (d2 == '') ? $('.maxx').datepicker("getDate") : endDate;

                    // need to change str order before making  date obect since it uses a new Date("mm-dd-yyyy") format for short date.
                    var d = data[4].split("-");
                    var startDate = new Date(d[2]+ "-" +  d[1] +"-" + d[0]);

                    if (minx == null && maxx == null) { return true; }
                    if (minx == null && startDate <= maxx) { return true;}
                    if (maxx == null && startDate >= minx) {return true;}
                    if (startDate >= minx && startDate <= maxx) { return true; }
                    return false;
                }
            );
       
            $('.min').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
            $('.max').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
    		
    		/*$('.min').datepicker({ autoclose : true, dateFormat : 'dd-mm-yy' });
    		$('.max').datepicker({ 
    			autoclose : true, 
    			dateFormat : 'dd-mm-yy'
    		});*/

    		$('.minx').datepicker({ autoclose : true, dateFormat : 'dd-mm-yy' });
    		$('.maxx').datepicker({
    			autoclose : true, 
    			dateFormat : 'dd-mm-yy'
    		});
    		
			//$('.max').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy', mindate: 2 });
          
		  	var table = $('#example').DataTable( {
				initComplete: function () {
					this.api().columns([0, 1, 2, 6, 7, 8]).every( function () {
						var column = this;
						var select = $('<select class="filterselect"><option value="">Semua</option></select>')
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
						});
					});
				}
			});

		 	$('.min, .max').change(function () {
                table.draw();
            });
		 
		 	$('.minx, .maxx').change(function () {
            	table.draw();
         	});
         
		}); //end document
	
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
			var l = $('#col-control').offset().left - 18;
			$('.box-detail').animate({ 'left': l + 'px'}, 300 );
			isShow = true;
		}
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
	
	function tglSelesaiChangex() {
		var date = $('.minx').val().split("-");
		var nextDate = new Date(date[2], (parseInt(date[1])-1), (parseInt(date[0])+1), 0, 0, 0, 0);

		$('.maxx').datepicker('option', 'minDate', new Date(nextDate));

		if(toJSDate($('.minx').val()) > toJSDate($('.maxx').val())) {
			$('.maxx').val(nextDate.getDate() + '-' + (nextDate.getMonth() + 1) + '-' + nextDate.getFullYear())
		}
	}

</script>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Monitoring<small> Surat Keputusan</small></font></h1>
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
		<div class="box-list">
				<tr>
					<td> Tanggal Input : &nbsp;&nbsp;&nbsp;&nbsp; </td>
				
					<td>Dari:&nbsp;&nbsp;</td>
	                <td><input  name="min" class="form-control2 min" type="text">&nbsp;&nbsp;</td> 
	            
					<td>Selesai:&nbsp;&nbsp;</td>
	                <td><input  name="max" class="form-control2 max" type="text"></td>
	           </tr>
	         <br><br>
	    	   <tr>
					<td> Tanggal SK &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp; </td>
				
					<td>Dari:&nbsp;&nbsp;</td>
	                <td><input  name="minx" class="form-control2 minx" type="text">&nbsp;&nbsp;</td> 
	            
					<td>Selesai:&nbsp;&nbsp;</td>
	                <td><input name="maxx" class="form-control2 maxx" type="text"></td>
	           </tr>
		   <br><br>
			<table class="display table table-heading table-datatable table-bordered" id="example" style= "font-size: 12px;" width="100%">
				<thead>
					<tr>
						<th width="2%" STYLE="display:none;">No</th>
						<th width="5%" STYLE="vertical-align:middle; text-align: center;">Asal Usulan</th>
						<th width="5%" STYLE="vertical-align:middle; text-align: center;">No. SK</th>
						<th width="10%" STYLE="vertical-align:middle; text-align: center;">Tgl. Input</th>
						<th width="10%" STYLE="vertical-align:middle; text-align: center; ">Tgl. SK</th>
						<th width="5%" STYLE="vertical-align:middle; text-align: center;">Tgl. Berlaku</th>
						<th width="5%" STYLE="vertical-align:middle; text-align: center;">Jenis</th>
						<th width="5%" STYLE="vertical-align:middle; text-align: center;">Perihal<br></th>
						<th width="5%" STYLE="vertical-align:middle; text-align: center;">Status</th>
						<th width="5%" STYLE="vertical-align:middle;"> </th>
					</tr>
				</thead>
				<tbody>
<?php 

	$no=0; // variabel no
	$list = $this->keputusan_model->get_keputusan_list();
	if(count($list) > 0) {
		foreach($list as $row) {
			list($agenda_date, $agenda_time) = explode(' ', $row->created_time);
			$agenda_date = db_to_human($agenda_date);
			
			$no++;
			
			if (isset($row->surat_akhir) && $row->status == 1) {
				// $tgl_akhir = new dateTime($row->surat_akhir);
				// $tgl_skrng = new DateTime();
				// $diff = $tgl_akhir->diff($tgl_skrng);
				// $diff;

				$tgl_akhir = strtotime($row->surat_akhir);
				$tgl_skrng = time();
				$diff = $tgl_akhir - $tgl_skrng;
				$diff_days = floor($diff / (60 * 60 * 24));
				
	$opt_sk_berakhir = '#A9A9A9';			
	$opt_kode_merah = $this->admin_model->get_contract_config('kode_warna', 'kurang_dari_sebulan');
	$opt_kode_kuning = $this->admin_model->get_contract_config('kode_warna', 'kurang_dari_3bulan');
	$opt_kode_hijau = $this->admin_model->get_contract_config('kode_warna', 'kurang_dari_6bulan');
	
				if ($diff_days < 0) {
					$row->status = $diff_days;
?>
				<tr>
<?php					
				}
				else
				{
					if ($diff_days <= 30)
					{
?>								
					<tr bgcolor= <?php echo $opt_kode_merah; ?>>
<?php				}
					else 
					{	
						if ($diff_days <= 93)
						{
?>
							<tr bgcolor= <?php echo $opt_kode_kuning; ?>>
<?php 					}
						else
						{
							if ($diff_days <= 186)
							{
?>
								<tr bgcolor= <?php echo $opt_kode_hijau; ?>>
<?php				    	}
							else
							{
?>
								<tr>
<?php						}
						}
					}
				}

			}else {
?>
				<tr>
<?php		}
		
?>			
					<td STYLE="display:none;"> <?php echo "$no"; ?></td>
<?php 
	$opt_kode_keputusan = $this->keputusan_model->get_referensi_full('sumber_usulan');
?>
					<td> <?php echo ($row->sifat_surat != '-') ? $opt_kode_keputusan[$row->sifat_surat] : '-'; ?></td>
					<td> <?php echo $row->surat_no; ?></td>			
					<td> <?php echo $agenda_date ; ?></td>
					<td> <?php echo db_to_human($row->surat_tgl); ?></td>
					<td> <?php echo db_to_human($row->surat_tgl_masuk); ?></td>
<?php 
	$opt_jenis_keputusan = $this->keputusan_model->get_referensi_full('jenis_keputusan');
?>
					<td> <?php echo ($row->jenis_surat != '-') ? $opt_jenis_keputusan[$row->jenis_surat] : '-'; ?></td>	
					<td> <?php echo $row->surat_perihal; ?></td>	
<?php
						if ($row->status == 2){
?>
							<td>Batal</td>
<?php
						}else {
							if ($row->status == 1){
?>
								<td>Aktif</td>
<?php
							}else {
								if ($row->status == 0){
?>
									<td>Draft</td>
<?php
								}else {
									if ($row->status < 0){
?>
										<td>Selesai</td>
<?php										
									}
								}
							}
						}
?>			
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
						<td style="border: 0; padding: 10px; background-color: #c39275; text-align: left; width: 7%">
						</td>
						<td style="background-color: #c7cac0;">
						 &nbsp; : Kurang 1 Bulan Lagi &nbsp;&nbsp;
						</td>
						<td style="border: 0; padding: 10px; background-color: #d0b788; text-align: left; width: 7%">
						</td>
						<td style = "background-color: #c7cac0;">
						 &nbsp; : Kurang 3 Bulan Lagi &nbsp;&nbsp;
						</td>
						<td style="border: 0; padding: 10px; background-color: #a2be99; text-align: left; width: 7%">
						</td>
						<td style = "background-color: #c7cac0;">
						&nbsp; : Kurang 6 Bulan Lagi &nbsp;&nbsp;
						</td>
					</tr>
				</TABLE>
				</div>
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