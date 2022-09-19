<?php
	switch($ref->jenis_agenda) {
		case 'SME' :
			$title = 'Surat Masuk Eksternal';
		break;
		case 'SKE' :
			$title = 'Surat Keluar Eksternal';
		break;
		case 'SI' :
			$title = 'Surat Internal';
		break;
		case 'ST' :
			$title = 'Surat Perintah';
		break;
		default :
			$title = 'Surat';
		break;
	}
	
	$to_ref_data = json_decode($ref->to_ref_data, TRUE);
	$title = $title . ' - ' . humanize($ref->to_ref);
?>
<style>
<!--
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		border: none;
		background-color: #3c8dbc;
	}
-->
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Ekspedisi <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Pengantar</a></li>
		<li><a href="#"> Surat</a></li>
		<li class="active"> <?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php 
	echo form_open_multipart('', ' id="form_user" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('mode', 'add');
	echo form_hidden('action', 'surat.ekspedisi_model.update_ekspedisi');
	echo form_hidden('ekspedisi_id', $ref->ekspedisi_id);
	echo form_hidden('function_ref_id', $function_ref_id); 
	echo form_hidden('jenis_agenda', $ref->jenis_agenda); 
	echo form_hidden('title', $title); 
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title"><?php echo $title; ?></h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
			</div>
		</div>
		<div class="box-body">
			<table id="list-pengantar" class="table table-bordered table-striped table-hover table-heading table-datatable">
				<thead>
					<tr>
						<th width="5"><input type="checkbox" id="check_all_detail" checked="checked"></th>
						<th width="10">No.</th>
						<th width="100">No. Agenda</th>
						<th width="120">Surat</th>
						<th width="200">Perintah Dari</th>
						<th>Perihal</th>
						<th width="100">Status Berkas</th>
					</tr>
				</thead>
				<tbody>
<?php 
	$i = 1;
	foreach($list_surat as $row) {
		if($row->surat_pengantar_id != '-' && $row->surat_id == $ref->ref_id){
			$surat_from_ref_data = json_decode($row->surat_from_ref_data, TRUE);
			$surat_signed 		 = json_decode($row->signed, TRUE);
?>
					<tr>
						<td><input type="checkbox" name="detail_ekspedisi[]" class="detail-check" value="<?php echo $row->surat_id?>" checked="checked"></td>
						<td></td>
						<td width="100"><a href="<?php echo ($row->function_ref_id != 3) ? site_url('surat/tugas/tugas_view/' . $row->surat_id) : site_url('surat/internal/sheet/' . $row->surat_id); ?>" target="_blank"> <?php echo strtoupper($row->jenis_agenda) . ' - ' . $row->agenda_id; ?></a></td>
						<td width="120">
							<?php echo $row->surat_no; ?><br>
							<?php echo db_to_human($row->surat_tgl); ?>
						</td>
						<td width="200">
<?php 
			if($row->surat_from_ref == 'eksternal') {		
				echo $surat_from_ref_data['title'] . '<br>' . $surat_from_ref_data['instansi']; 
			} else {
				// echo ((isset($surat_from_ref_data['unit'])) ? ($surat_from_ref_data['jabatan'] . ', ' . $surat_from_ref_data['unit'] . '<br>') : '') . $surat_from_ref_data['dir'];
				echo ((isset($surat_signed['unit_name'])) ? ($surat_signed['jabatan'] . ' ' . $surat_signed['unit_name'] . '<br>') : $surat_signed['unit_name']);
			}
?>
						</td>
						<td><?php echo $row->surat_perihal; ?></td>
						<td width="50"><?php echo humanize($row->status_berkas); ?></td>
					</tr>
<?php 
		}
	}
?>
				</tbody>
			</table>
		</div><!-- /.box-body -->
		<!-- <div class="box-footer">
		</div><!-- /.box-footer-->
	</div><!-- /.box -->

	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title"> Distribusi </h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
			</div>
		</div>
		<div class="box-body">
<?php
	if (isset($ref->to_ref_data)) {
		$distribusi_tujuan = json_decode($ref->to_ref_data, TRUE);
		$distribusi_unit 	   = $ref->to_ref;
		$distribusi_kode 	   = $distribusi_tujuan['kode'];
		$distribusi_jabatan    = $distribusi_tujuan['jabatan'];
		$distribusi_nama 	   = $distribusi_tujuan['nama'];
		$distribusi_direktorat = $distribusi_tujuan['dir'];
?>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="surat_int_unit" class="col-sm-3 control-label">Unit</label>
						<div class="col-sm-9">
							<div class="input-group">
								<input type="text" id="to_ref" name="to_ref" class="form-control required" data-input-title="Unit Tujuan" value="<?php echo $distribusi_unit; ?>" readonly="readonly">
								<div id="tujuan_unit_kode" class="input-group-addon"><?php echo $distribusi_kode; ?></div>	
								<input type="hidden" id="to_ref_data_kode" name="to_ref_data[kode]" class="form-control required" data-input-title="Kode Instansi Unit" value="<?php echo $distribusi_kode; ?>">
								<input type="hidden" id="to_ref_id" name="to_ref_id" value="<?php echo $ref->to_ref_id; ?>">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_int_jabatan" class="col-sm-3 control-label">Jabatan</label>
						<div class="col-sm-9">
<?php 
	$opt_jabatan = $this->admin_model->get_system_config('jabatan');
	echo form_dropdown('to_ref_data[jabatan]', $opt_jabatan, $distribusi_jabatan, (' id="to_ref_data_jabatan" class="form-control" data-input-title="Nama Jabatan" readonly="readonly" '));
?>
						</div>
					</div>
					<div class="form-group">
						<label for="surat_int_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="to_ref_data_nama" name="to_ref_data[nama]" class="form-control" data-input-title="Nama Pejabat Tujuan" value="<?php echo $distribusi_nama; ?>" readonly="readonly" >
						</div>
					</div>
					<div class="form-group">
						<label for="surat_int_dir" class="col-sm-3 control-label">Direktorat</label>
						<div class="col-sm-9">
							<input type="text" id="to_ref_data_dir" name="to_ref_data[dir]" class="form-control" readonly="readonly" data-input-title="Direktorat Tujuan" value="<?php echo $distribusi_direktorat; ?>" >
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<!--
					<div class="form-group">
						<label for="surat_tembusan" class="col-sm-3 control-label">Tembusan</label>
						<div class="col-sm-9">
<?php 
	$list = $this->user_model->get_user_role(4);
	$opt_pejabat = array();
	foreach ($list->result() as $row) {
		$opt_pejabat[$row->user_id] = $row->user_name; 
	}
	// echo form_multiselect('surat_tembusan[]', $opt_pejabat, $cc, (' id="surat_tembusan" class="form-control select2" '));
?>
						</div>
					</div>
					-->
					<div class="form-group">
						<label for="petugas_pengirim" class="col-sm-3 control-label">Pengirim</label>
						<div class="col-sm-9">
							<input type="text" id="petugas_pengirim" name="petugas_pengirim" class="form-control" data-input-title="Nama Pejabat Tujuan" value="<?php echo $ref->petugas_pengirim; ?>" placeholder="Petugas Pengirim">
						</div>
					</div>
					<div class="form-group">
						<label for="petugas_penerima" class="col-sm-3 control-label">Penerima</label>
						<div class="col-sm-9">
							<input type="text" id="petugas_penerima" name="petugas_penerima" class="form-control" data-input-title="Nama Penerima" value="<?php echo $ref->petugas_penerima; ?>" disabled="disabled" placeholder="Petugas Penerima">
						</div>
					</div>
				</div>
			</div>
			<hr>
			<!-- 
			<div class="row">
				<div class="col-md-6">
					<fieldset>
						<legend class="contro-label">Pengiriman</legend>
						<div class="form-group">
							<label for="catatan" class="col-lg-2 col-sm-3 control-label">Catatan</label>
							<div class="col-lg-10 col-sm-9">
								<textarea id="catatan_pengirim" name="catatan_pengirim" class="form-control required" rows="3" placeholder="Catatan Pengiriman" data-input-title="Catatan" ><?php echo $ref->catatan_pengirim; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="petugas_pengirim" class="col-lg-2 col-sm-3 control-label">Nama</label>
							<div class="col-lg-10 col-sm-9">
								<input type="text" id="petugas_pengirim" name="petugas_pengirim" class="form-control" data-input-title="Nama Pejabat Tujuan" value="<?php echo $ref->petugas_pengirim; ?>" >
							</div>
						</div>
					</fieldset>
				</div>
				<div class="col-md-6">
					<fieldset>
						<legend class="contro-label">Penerima</legend>
						<div class="form-group">
							<label for="catatan_penerima" class="col-lg-2 col-sm-3 control-label">Catatan</label>
							<div class="col-lg-10 col-sm-9">
								<textarea id="catatan_penerima" name="catatan_penerima" class="form-control" rows="3" placeholder="Catatan Penerima" data-input-title="Catatan Penerima" disabled="disabled"></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="petugas_penerima" class="col-lg-2 col-sm-3 control-label">Nama</label>
							<div class="col-lg-10 col-sm-9">
								<input type="text" id="petugas_penerima" name="petugas_penerima" class="form-control" data-input-title="Nama Penerima" value="" disabled="disabled" >
							</div>
						</div>
					</fieldset>
				</div>
			</div> 
			-->
<?php
		//}
	}
?>			
		</div><!-- /.box-body -->
		<!-- <div class="box-footer">
			Footer
		</div><!-- /.box-footer-->
	</div><!-- /.box -->
	
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-5">
					<button class="btn btn-app">
						<i class="fa fa-save"></i> Update
					</button>
				</div>
				<div class="col-xs-2" style="text-align: center;">
					<button type="button" class="btn btn-app" onclick="printPengantar();">
						<i class="fa fa-print"></i> Cetak
					</button>
				</div>
				<div class="col-xs-5">
					<button type="button" class="btn btn-app pull-right bg-green" onclick="kirimData();">
						<i class="fa fa-send"></i> Kirim
					</button>
<?php 
	if($ref->status != 0) {
?>
					<button type="button" class="btn btn-app pull-right bg-red" onclick="returnData();">
						<i class="fa fa-caret-square-o-left"></i> Return
					</button>
<?php 
	}
?>
				</div>
			</div>
		</div>
		<div class="overlay hide">
			<i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>

<?php 
	echo form_close();
?>

</section><!-- /.content -->

<script type="text/javascript">

	$(document).ready(function() {
		var table = $('#list-pengantar').DataTable({
			"aoColumnDefs" : [ {
	            'bSortable' : false,
	            'aTargets' : [ 0 ]	            
	        } ],
	        "aaSorting": [[ 2, 'asc' ]]	        
		});

		table.on( 'order.dt search.dt', function () {
		        table.column(1, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
		            cell.innerHTML = i+1;
		        } );
    		} ).draw();

		$('#check_all_detail').click(function(e) {
			if(this.checked) {
				$('.detail-check').each(function() {
					this.checked = true;
				});
			} else {
				$('.detail-check').each(function() {
					this.checked = false;
				});
			}
		});

		$('.select2').select2();

		$('#tujuan_unit').autocomplete({
			source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
			minLength: 3,
			select: function(event, ui) {
				$('#tujuan_kode').val(ui.item.unit_code);
				$('#tujuan_unit_kode').html(ui.item.unit_code);
				$('#tujuan_unit_id').val(ui.item.id);
				$('#tujuan_nama').val(ui.item.nama_pejabat);
				$('#tujuan_dir').val(ui.item.instansi);
			}
		});
		
		$('#tujuan_unit').keyup(function() {
			if($(this).val().trim() == '') {
				$('#tujuan_kode').val('');
				$('#tujuan_unit_kode').html('________');
				$('#tujuan_unit_id').val('');
				$('#tujuan_nama').val('');
				$('#tujuan_dir').val('');
			}
		});
		
	}); //end document

	function printPengantar() {
		window.open('<?php echo site_url('surat/ekspedisi/cetak/' . $ref->ekspedisi_id . '/' . $jenis_agenda); ?>');
	}
	
	function kirimData() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Kirim Surat Pengantar.', function (result) {
			if(result) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
					data: {action: 'surat.ekspedisi_model.kirim_ekspedisi', 
							function_ref_id: '<?php echo $function_ref_id; ?>',
							ref_id: '<?php echo $ref->ekspedisi_id; ?>',
							title: '<?php echo $title; ?>'
							},
					success: function(data){
						if(typeof(data.error) != 'undefined') {
							eval(data.execute);
						}else {
							bootbox.alert("Data transfer error!");
							$('#box-process-btn .overlay').addClass('hide');
						}
					}	
				});
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}

</script>