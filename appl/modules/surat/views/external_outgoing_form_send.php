<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/wysiwyg_view.css">
<!-- Content Header (Page header) 
<section class="content-header">
	<h1>Surat <small><?php echo $title; ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-envelope"></i> Surat</a></li>
		<li><a href="#">Eksternal</a></li>
		<li class="active">Keluar</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php
	$param = (array) $surat;
		
	echo form_open_multipart('', ' id="form_surat_keluar" class="form-horizontal" onsubmit="return validateData($(this));"');
	echo form_hidden('action', 'surat.surat_model.save_surat_keluar_send'); 
	echo form_hidden('surat_id', $surat->surat_id);
	echo form_hidden('agenda_id', $surat->agenda_id); 
	echo form_hidden('function_ref_id', 2);
	echo form_hidden('jenis_agenda', 'SKE'); 
	
?>
	<!-- Default box -->
	<div class="box">
		<div class="box-body">
			<div class="form-group">
				<label for="surat_no" class="col-lg-2 col-sm-3 control-label">Nomor Surat</label>
				<div class="col-lg-6 col-sm-5">
					<input type="text" id="surat_no" class="form-control" disabled="disabled" value="<?php echo $surat->surat_no; ?>">
				</div>
				<div class="col-lg-4 col-sm-4">
					<input type="text" id="surat_tgl" id="created_time" class="form-control" disabled="disabled" value="<?php echo db_to_human($surat->surat_tgl); ?>" style="text-align: right;">
				</div>
			</div>
			<div class="form-group">
				<label for="no_surat" class="col-lg-2 col-sm-3 control-label">Perihal</label>
				<div class="col-lg-10 col-sm-9">
					<textarea id="surat_perihal" class="form-control" disabled="disabled" rows="3" ><?php echo $surat->surat_perihal; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="surat_item_lampiran" class="col-lg-2 col-sm-3 control-label">Lampiran</label>
				<div class="col-lg-3 col-sm-9">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
					<input type="text" id="surat_item_lampiran" class="form-control" disabled="disabled" value="<?php echo $surat->surat_item_lampiran . ' ' . $opt_unit_lpr[$surat->surat_unit_lampiran]; ?>">
				</div>
				<label for="surat_format" class="col-lg-2 col-sm-3 control-label">Format Template</label>
				<div class="col-lg-5 col-sm-9">
<?php 
	$list = $this->admin_model->get_template_surat($function_ref_id);
	$opt_format = array('' => '--');
	foreach ($list->result() as $row) {
		$opt_format[$row->format_surat_id] = $row->format_title;
	}
?>
					<input type="text" id="format_surat_id" class="form-control" disabled="disabled" value="<?php echo $opt_format[$surat->format_surat_id]; ?>">
				</div>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
	<div class="row">
		<div class="col-md-6">
<?php 
	$surat_to_ref_data = json_decode($surat->surat_to_ref_data, TRUE);
?>
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Tujuan Surat</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="list-tujuan" class="box-body" style="display: none;">
					<div class="form-group">
						<label for="surat_ext_title" class="col-sm-3 control-label">Jabatan <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_title" name="surat_to_ref_data[title]" class="form-control" disabled="disabled" data-input-title="Jabatan Tujuan" value="<?php echo $surat_to_ref_data['title']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_nama" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_nama" name="surat_to_ref_data[nama]" class="form-control" disabled="disabled" data-input-title="Nama Tujuan" value="<?php echo $surat_to_ref_data['nama']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_instansi" class="col-sm-3 control-label">Instansi</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_instansi" name="surat_to_ref_data[instansi]" class="form-control" disabled="disabled" data-input-title="Instansi Tujuan" value="<?php echo $surat_to_ref_data['instansi']; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="surat_ext_alamat" class="col-sm-3 control-label">Alamat</label>
						<div class="col-sm-9">
							<input type="text" id="surat_ext_alamat" name="surat_to_ref_data[alamat]" class="form-control" disabled="disabled" data-input-title="Alamat Tujuan" value="<?php echo $surat_to_ref_data['alamat']; ?>">
						</div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
		<div class="col-md-6">
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Klasifikasi Arsip</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
		
				<div class="box-body" style="display: none;">
					<div class="form-group">
						<label for="no_surat" class="col-md-3 control-label">Kode</label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="<?php echo trim($surat->kode_klasifikasi_arsip); ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="no_surat" class="col-md-3 control-label">Klasifikasi</label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi" class="form-control" disabled="disabled" value="<?php echo $klasifikasi->nama_klasifikasi; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-md-3 control-label"></label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi_sub" class="form-control" disabled="disabled" value="<?php echo $klasifikasi->nama_klasifikasi_sub; ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-md-3 control-label"></label>
						<div class="col-md-9">
							<input type="text" id="klasifikasi_sub_sub" class="form-control" disabled="disabled" value="<?php echo $klasifikasi->nama_klasifikasi_sub_sub; ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Tembusan</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="tembusan_list" class="box-body" style="display: none;">
<?php 
	$i = 1;
	foreach (json_decode($surat->tembusan) as $tembusan) {
?>
					<div id="row_tembusan_<?php echo $i; ?>" class="form-group">
						<div class="col-sm-12">
							<input type="text" id="tembusan_<?php echo $i; ?>" name="tembusan[<?php echo $i; ?>]" class="form-control" data-input-title="Tembusan <?php echo $i; ?>" value="<?php echo $tembusan; ?>" disabled="disabled">
						</div>
					</div>
<?php 
	}
?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<!-- Default box -->
			<div class="box box-primary collapsed-box">
				<div class="box-header with-border">
					<h3 class="box-title">Lampiran</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div id="attachment_list" class="box-body" style="display: none;">
<?php 
	$last_seq = 0;
	foreach ($attachment as $row) {
?>
					<div id="attachment_<?php echo $row->sort; ?>" class="col-md-12">
						<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> <label> <?php echo $row->title; ?> </label>
					</div>
<?php 
		$last_seq = $row->sort;
	}
?>
				</div>
			</div>
		</div>
	</div>
	<div id="box-konsep" class="box box-primary collapsed-box">
		<div class="box-header with-border">
			<h3 class="box-title"> Konsep Surat Keluar </h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<div class="box-body" style="display: none;">
			<div class="form-group">
				<div id="konsep_text" class="col-md-12">
					<?php echo $konsep->konsep_text; ?>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<span class="h3 box-title">Tanda Terima </span> <span class="small">Max. 2MB (*.pdf, *.jpg, *.jpeg, *.png) </span>
		</div>
		<div id="soft_copy_list" class="box-body">
			<input type="hidden" name="soft_copy[id]" value="<?php echo $copy_surat->file_attachment_id; ?>">
			<input type="hidden" name="soft_copy[file]" value="<?php echo $copy_surat->file; ?>">
			<input type="hidden" name="soft_copy[title]" value="<?php echo $copy_surat->title; ?>">
			<input type="hidden" name="soft_copy[state]" id="soft_copy_state" value="<?php echo $copy_surat->state; ?>">
			<div id="soft_copy_<?php echo $copy_surat->sort; ?>" >
				<div class="col-md-12">
					<div class="form-group">
						<div class="btn btn-default btn-file">
							<i class="fa fa-paperclip"></i>
							<input type="file" name="soft_copy" onchange="$('#flabel_soft_copy').html($(this).val())">
						</div>
						<label id="flabel_soft_copy">
<?php 
	if($copy_surat->file_attachment_id != '-') {
?>
							<?php echo $copy_surat->file_name; ?>
							<a href="<?php echo $copy_surat->file; ?>" target="_blank" title="<?php echo $copy_surat->file_name; ?>"><i class="fa fa-file-text-o"></i></a>
<?php 
	}
?>	
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Pengiriman</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
<?php 
	if($surat->kirim_time) {
		echo form_hidden('create_agenda', 0); 
		list($agenda_date, $agenda_time) = explode(' ', $surat->kirim_time);
		$agenda_date = db_to_human($agenda_date);
?>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="surat_no" class="col-sm-3 control-label">Agenda</label>
					<div class="col-sm-9">
						<div class="input-group">
							<div class="input-group-addon"><?php echo strtoupper($surat->jenis_agenda); ?></div>
							<input type="text" id="agenda_id" name="agenda_id" class="form-control" disabled="disabled" value="<?php echo $surat->agenda_id; ?>">
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="col-sm-12">
					<div class="input-group">
						<input type="text" id="created_time" name="created_time" class="form-control" disabled="disabled" value="<?php echo $agenda_date; ?>" style="text-align: right;">
						<div class="input-group-addon"><?php echo $agenda_time; ?></div>
					</div>
				</div>
			</div>
		</div>
<?php 
	} else {
		echo form_hidden('create_agenda', 1); 	
	}

	$distribusi = array();
	if($surat->distribusi != '') {
		$distribusi = json_decode($surat->distribusi, TRUE);
	}
?>
			<div class="col-md-6">
				<div class="form-group">
					<label for="kirim_time" class="col-sm-3 control-label">Tanggal</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" id="kirim_time" name="kirim_time" class="form-control datetimepicker required" data-input-title="Tgl Kirim" value="<?php echo date('d-m-Y H:i'); ?>">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="catatan_pengiriman-cara_pengiriman" class="col-sm-3 control-label">Cara Pengiriman</label>
					<div class="col-sm-9">
<?php 
	$opt_pengiriman = $this->admin_model->get_system_config('delivery_method');
	echo form_dropdown('distribusi[cara_pengiriman]', $opt_pengiriman, (isset($distribusi['cara_pengiriman']) ? $distribusi['cara_pengiriman'] : ''), (' id="distribusi-cara_pengiriman" class="form-control" data-input-title="Cara Pengiriman" onchange="caraChange($(this).val());"'));
?>	
					</div>
				</div>
				<div class="form-group">
					<label for="catatan_pengiriman-catatan" class="col-sm-3 control-label">Catatan</label>
					<div class="col-sm-9">
						<textarea id="catatan_pengiriman" name="catatan_pengiriman" class="form-control required" rows="2" placeholder="Catatan Pengiriman" data-input-title="Catatan" ><?php echo $surat->catatan_pengiriman; ?></textarea>
					</div>
				</div>
			</div>
			<div class="col-md-6">					
<?php 
	foreach($opt_pengiriman as $k => $v) {
?>
				<div id="cara_pengiriman-<?php echo $k; ?>" class="extra-param hide"> 
<?php 
		$extra_param = $this->admin_model->get_system_cm_config('delivery_param_' . $k);
		foreach($extra_param as $p_k => $p_v) {	
?>
				<div class="form-group">
					<label for="catatan_pengiriman-<?php echo $p_k; ?>" class="col-sm-3 control-label"><?php echo $p_v; ?></label>
					<div class="col-sm-9">
						<input type="text" id="distribusi-<?php echo $p_k; ?>" name="distribusi[<?php echo $p_k; ?>]" class="form-control" placeholder="<?php echo $p_v; ?>" data-input-title="<?php echo $p_v; ?>" data-default="<?php echo (isset($distribusi[$p_k]) ? $distribusi[$p_k] : ''); ?>" value="<?php echo (isset($distribusi[$p_k]) ? $distribusi[$p_k] : ''); ?>" >
					</div>
				</div>	
<?php 			
		}
?>
				</div>
<?php 
	}
?>				
			</div>
		</div>
	</div>
	<div id="box-process-btn" class="box box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-xs-4">
<?php 
	if(isset($distribusi['cara_pengiriman'])) {
?>		
					<button class="btn btn-app" onclick="$('#box-process-btn .overlay').removeClass('hide');">
						<i class="fa fa-save"></i> Update
					</button>
<?php
	}else {
?>
					<button class="btn btn-app" onclick="$('#box-process-btn .overlay').removeClass('hide');">
						<i class="fa fa-save"></i> Save
					</button>
<?php
	}
?>					
					<button type="button" class="btn btn-app" onclick="printSurat();">
						<i class="fa fa-print"></i> Cetak
					</button>
				</div>
				<div class="col-xs-8">
<?php 
	if(isset($distribusi['cara_pengiriman'])) {
?>
	<!--
				<button type="button" class="btn btn-app pull-right bg-green" onclick="prosesArsip();">
					<i class="fa fa-caret-square-o-right"></i> Simpan Sebagai Arsip
				</button>
	-->			
<?php 
	}
?>					
				</div>
			</div>
			<div class="overlay hide">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
		</div>
	</div>

<?php 
	echo form_close();
?>

</section><!-- /.content -->

<script type="text/javascript">

	$(document).ready(function() {
		$('.datetimepicker').datetimepicker({
			autoclose : true, 
			dateFormat : 'dd-mm-yy',
//			minDate : 0
		});
		
		caraChange($('#distribusi-cara_pengiriman').val());
	
	}); //end document

	function caraChange(v) {
		$('.extra-param').addClass('hide');
		$('.extra-param').find('input').each( function() { $(this).val($(this).attr('data-default')) });
		$('.extra-param').find('input').prop('disabled', true);
		$('#cara_pengiriman-' + v).removeClass('hide');
		$('#cara_pengiriman-' + v).find('input').prop('disabled', false);
	}
	
	function printSurat() {
		window.open('<?php echo site_url('surat/external/cetak_surat_eksternal/' . $surat->surat_id); ?>');
	}

<?php 
	if(isset($distribusi['cara_pengiriman']) ) {
?>
	function prosesArsip() {
		$('#box-process-btn .overlay').removeClass('hide');
		bootbox.confirm('Simpan sebagai Arsip?', function(result){
			if(result) {
				location.assign('<?php echo site_url('surat/external/register_arsip/' . $surat->surat_id); ?>');	 
			} else {
				$('#box-process-btn .overlay').addClass('hide');
			}
		});
	}

<?php 
	}
?>

</script>