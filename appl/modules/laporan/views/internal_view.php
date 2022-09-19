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
	.form-control2 {
		background-color: #f2e6d9;
	}
	.btn-export {
	    background-color: #FFF;
	    border-color: #999;
	    color: #000;
	}
	.btn-export:hover {
		background-color: #444;
		border-color: #00acd6;
		color: #FFF;
	}
</style>

<script type="text/javascript">
<!--
	var isShow = false;
	$(document).ready(function() {
		// $.fn.dataTable.ext.search.push(
  //           function (settings, data, dataIndex) {
  //               var min = $('.min').datepicker("getDate");
  //               var d1 = $('.max').val();
  //               var endDate = toEndDate(d1);
  //               var max = (d1 == '') ? $('.max').datepicker("getDate") : endDate;
                
  //               // need to change str order before making  date obect since it uses a new Date("mm-dd-yyyy") format for short date.
  //               var d = data[3].split("-");
  //               var startDate = new Date(d[2] + "-" + d[1] + "-" + d[0]);

  //               if (min == null && max == null) { return true; }
  //               if (min == null && startDate <= max) { return true;}
  //               if (max == null && startDate >= min) {return true;}
  //               if (startDate >= min && startDate <= max) { return true; }
  //               return false;
  //           }
  //       );
       
        // $('.min').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
        // $('.max').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy' });
		/*$('.min').datepicker({ autoclose : true, dateFormat : 'dd-mm-yy' });
		$('.max').datepicker({ 
			autoclose : true, 
			dateFormat : 'dd-mm-yy'
		});*/
    		
		//$('.max').datepicker({ onSelect: function () { table.draw(); }, autoclose : true, dateFormat : 'dd-mm-yy', mindate: 2 });
        
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

        $('#generateExcel').click(function() {
        	// var tgl_awal = $('#min').val();
        	// var tgl_akhir = $('#max').val();
        	var month = $('#arc_month').val();
			var year = $('#arc_year').val();
        	// var export_url;
 	
        	$.ajax({
				type: "POST",
				url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('laporan/internal/export_excel')); ?>",
				data: {function_ref_id: '<?php echo 3; ?>',
						month: month,
						year: year
					},
				success: function(data) {
					if(data.error == 1) {
						bootbox.alert(data);
					} else {
						export_url = window.location.replace(data.execute);
						return;
					}
				}
			});
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
	    $('#data_table').DataTable().column(3).search(
	        search_date
	    ).draw();
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
<?php
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
	<div class="box-list">
		<div class="form-group">
			<label class="col-xs-2">Periode Nota Dinas</label>
			<div class="col-xs-3">
				<?php echo form_dropdown('arc_month', $opt_month, $arc_month, (' id="arc_month" class="form-control" ')); ?>
			</div>
			<div class="col-xs-2">
				<?php echo form_dropdown('arc_year', $opt_year, $arc_year, (' id="arc_year" class="form-control" ')); ?>
			</div>
			<div class="col-xs-2">
				<button class="btn btn-info btn-block" id="btn-submit"> <i class="fa fa-search list-data"></i> Tampilkan</button>
			</div>
			<div class="col-xs-2 pull-right">
				<button id="generateExcel" class="btn btn-export btn-block"><img src="<?php echo base_url(); ?>assets/media/export_excel.png" width="18" /> Cetak ke Excel</button>
			</div>
		</div>
		<!--
		<tr>
			<td> Tanggal Surat &nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td>Dari :&nbsp;&nbsp;</td>
            <td>
            	<input name="min" id="min" class="form-control2 min" type="text">
            	<div class="form-group input-group">
	            	<input name="min" id="min" class="form-control2 min" type="text">
	            	<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
				</div>
            &nbsp;&nbsp;</td> 
        		
			<td>Sampai dengan :&nbsp;&nbsp;</td>
            <td><input name="max" id="max" class="form-control2 max" type="text"></td>
        </tr>
        <br>
        <tr>
        	<td><div style="text-align: right;"><button id="generateExcel" class="btn btn-md"><img src="<?php echo base_url(); ?>assets/media/export_excel.png" width="28" /> Export to excel</button></div></td>
        </tr>
        -->        
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="data_table">
			<thead>
				<tr>
					<th colspan="8"><?php echo $title; ?></th>
				</tr>
				<tr>
					<th>Agenda</th>
					<th>No Surat</th>
					<th>Tanggal Surat</th>
					<th>Tanggal Konsep Surat</th>
					<th>Perihal</th>
					<th>Instansi</th>
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
				$tujuan_disposisi = "";
				if($row->jenis_agenda == 'SME') {
					$surat_from_ref = json_decode($row->surat_from_ref_data, TRUE);
					$distribusi = json_decode($row->distribusi_disposisi, TRUE);
					$tujuan_disposisi = (isset($row->to_user_id)) ? $distribusi[$row->to_user_id] : '';
					$instansi = $surat_from_ref['instansi'];
					$tgl_awal = $row->tgl_terima_surat;
					if (($row->status_disposisi == 1 || $row->status_disposisi == 99) && $row->status >= 4) {
						$disposisi_status = "#C39275 !important";
					}
				}else if ($row->jenis_agenda == 'SKE') {
					$surat_to_ref = json_decode($row->surat_to_ref_data, TRUE);
					$instansi = $surat_to_ref['instansi'];
					$tgl_awal = $row->awal_surat;
				}else if ($row->jenis_agenda == 'SI') {
					if (($row->status_disposisi == 1 || $row->status_disposisi == 99) && $row->status == 6) {
						$disposisi_status = "#C39275 !important";
					}
					$tgl_awal = $row->surat_awal;	
				}else if ($row->jenis_agenda == 'ST') {
					$tgl_awal = $row->surat_awal;
				}			
?>
					<tr>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php if($row->agenda_id != '-') { ?><strong><?php echo $row->no_agenda; ?></strong><br> <?php } else { ?><strong><?php echo $row->jenis_agenda; ?></strong><br><?php } ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><strong><?php echo $row->no_surat; ?></strong></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->tgl_surat; ?></a></td>
						<td class="data-list" width="17%"><a href="<?php echo site_url($row->link1); ?>"><?php echo $tgl_awal; ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->perihal_surat; ?></a></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo ($instansi) ? $instansi : '-'; ?></a></td>
						<td class="data-list"><?php echo ($tujuan_disposisi != '') ? $tujuan_disposisi['jabatan'] . ' ' . $tujuan_disposisi['unit_name'] : ''; ?></td>
						<td class="data-list"><a href="<?php echo site_url($row->link1); ?>"><?php echo $row->status_surat; ?></a></td>
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
	