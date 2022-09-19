<?php
	$from = json_decode($ref->surat_from_ref_data, TRUE);
	
?>
	<!-- Default box -->
	<div class="box box-primary">
		<div class="box-header with-border">
			<h3 class="box-title">Referensi</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
				<label class="col-md-2">Asal Surat</label>
<?php
	switch($ref->jenis_agenda) {
		case 'I' :
?>
				<label class="col-md-10" style="font-weight: 400;"> : <?php echo $from['jabatan'] . ' ' . humanize($from['unit']) . ', ' . $from['dir']; ?></label>
<?php
		break;
		case 'SME' :
?>
				<label class="col-md-10" style="font-weight: 400;"> : <?php echo $from['nama'] . ' ' . humanize($from['title']) . ', ' . $from['instansi']; ?></label>
<?php
		break;
	}
?>
			</div>
			<div class="row">
				<label class="col-md-2">Perihal</label>
				<label class="col-md-10" style="font-weight: 400;"> : <?php echo $ref->surat_perihal; ?></label>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-4">
<?php 
	$opt_unit_lpr = $this->admin_model->get_system_config('unit_lpr');
?>
					<table class="table-hover" style="width: 100%">
						<tr>
							<td width="35%" style="padding-left:10px;"><label>Nomor Surat</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $ref->surat_no; ?></label></td>
						</tr>
						<tr>
							<td style="padding-left:10px;"><label>Tanggal Surat</label></td>
							<td><label style="font-weight: 400;"> : <?php echo db_to_human($ref->surat_tgl); ?></label></td>
						</tr>
						<tr>
							<td style="padding-left:10px;"><label>Lampiran</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $ref->surat_item_lampiran . ' ' . $opt_unit_lpr[$ref->surat_unit_lampiran]; ?></label></td>
						</tr>
					</table>
				</div>
				<div class="col-md-4">
<?php 
	$opt_status_berkas 	= $this->admin_model->get_system_config('status_berkas');
	$opt_sifat_surat 	= $this->admin_model->get_system_config('sifat_surat');
	$opt_jenis_surat 	= $this->admin_model->get_system_config('jenis_surat');
?>
					<table class="table-hover" style="width: 100%">
						<tr>
							<td width="30%" style="padding-left:10px;"><label>Status</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($ref->status_berkas != '-') ? $opt_status_berkas[$ref->status_berkas] : '-'; ?></label></td>
						</tr>
						<tr>
							<td style="padding-left:10px;"><label>Sifat</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($ref->sifat_surat != '-') ? $opt_sifat_surat[$ref->sifat_surat] : '-'; ?></label></td>
						</tr>
						<tr>
							<td style="padding-left:10px;"><label>Jenis</label></td>
							<td><label style="font-weight: 400;"> : <?php echo ($ref->jenis_surat != '-') ? $opt_jenis_surat[$ref->jenis_surat] : '-'; ?></label></td>
						</tr>
					</table>
				</div>
				<div class="col-md-4">
<?php 
	$surat_tgl_masuk 		= explode(' ', (($ref->surat_tgl_masuk) ? $ref->surat_tgl_masuk : $ref->terima_time ));
	$surat_tgl_masuk_date 	= db_to_human($surat_tgl_masuk[0]);
	$surat_tgl_masuk_time 	= isset($surat_tgl_masuk[1]) ? $surat_tgl_masuk[1] : '';
?>
					<table class="table-hover" style="width: 100%">
						<tr>
							<td width="35%" style="padding-left:10px;"><label>Tanggal Terima</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $surat_tgl_masuk_date . ' ' . $surat_tgl_masuk_time; ?></label></td>
						</tr>
						<tr>
							<td style="padding-left:10px;"><label>Nomor Agenda</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $ref->jenis_agenda . '-' . $ref->agenda_id; ?></label></td>
						</tr>
					</table>
				</div>
			</div>
<?php
	$list = $this->admin_model->get_file_attachment('surat', $ref->surat_id);
	if($list->num_rows() > 0) {
?>
			<hr>
			<div class="row">
<?php 
		$last_seq = 0;
		foreach ($list->result() as $row) {
?>
				<div id="attachment_<?php echo $row->sort; ?>" class="col-md-4">
					<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> &nbsp; <label> <?php echo $row->title; ?> </label>
				</div>
<?php 
			$last_seq = $row->sort;
		}
?>
			</div>
<?php 
	}
?>			
			<hr>
			<div class="row">
<?php
	$parent_from = json_decode($parent->from_data);
	$opt_sifat_disposisi = array('Biasa', 'Segera', 'Sangat Segera');

	list($target_date, $target_time) = explode(' ', $parent->target_selesai);
	$target_date = db_to_human($target_date);

	$parent_distribusi = json_decode($parent->distribusi);	//var_dump($parent);
	$parent_distribusi = $parent_distribusi->{get_user_id()};
?>
				<div class="col-md-12"><label style="font-size: 16pt;">Disposisi Asal</label><br></div>
				<div class="col-md-6">
					<table class="table-hover" style="width: 100%">
						<tr>
							<td width="35%" valign="top" style="padding-left:10px;"><label>Instruksi Dari</label></td>
							<td valign="top"><label style="font-weight: 400;"> : <?php echo $parent_from->nama . '<br>&nbsp; ' . $parent_from->jabatan . ', ' . $parent_from->unit; ?></label></td>
						</tr>
						<tr>
							<td width="35%" style="padding-left:10px;"><label>Sifat Instruksi</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $opt_sifat_disposisi[$parent->sifat]; ?></label></td>
						</tr>
						<tr>
							<td style="padding-left:10px;"><label>Target Penyelesaian</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $target_date . ' ' . $target_time; ?></label></td>
						</tr>
						<tr>
							<td width="35%" style="padding-left:10px;"><label>Instruksi</label></td>
							<td><label style="font-weight: 400;"> : <?php echo $parent_distribusi->instruksi; ?></label></td>
						</tr>
						<tr>
							<td width="35%" valign="top" style="padding-left:10px;"><label>Lampiran</label></td>
							<td valign="top">
<?php 
	$last_seq = 0;
	foreach ($parent_attachment as $row) {
?>
							<div id="parent_attachment_<?php echo $row->sort; ?>" class="">
								<a href="<?php echo $row->file; ?>" target="_blank" title="<?php echo $row->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> <label> <?php echo $row->title; ?> </label>
							</div>
<?php 
		$last_seq = $row->sort;
	}

	$i = $last_seq + 1;
	foreach($parent_distribusi->attachment as $j => $priv_attach) {
?>
							<div id="parent_attachment_<?php echo $i; ?>" class="">
								<a href="<?php echo $priv_attach->file; ?>" target="_blank" title="<?php echo $priv_attach->file_name; ?>"><i class="fa fa-file-text-o"></i> </a> <label> <?php echo $priv_attach->file_name; ?> </label>
							</div>
<?php
		$i++;
	}
?>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-md-6">
<?php 
	$this->load->view('diskusi', array('id' => get_user_id(), 'function_handle' => 'surat.disposisi_model.set_diskusi', 'script_handle' => 'parent', 'ref_id' => $parent->disposisi_id, 'diskusi' => $parent_distribusi->diskusi, 'active' => ($parent_distribusi->status == 0)));
?>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
<?php
	if(!has_permission(7) && $parent_distribusi->status == 0) {
?>					
					<button class="btn btn-danger btn-block complete_state_off_1" onclick="setSelesai();">Selesai</button>
<?php
	}
?>
				</div>
			</div>	

		</div><!-- /.box-body -->
	</div><!-- /.box -->

	<script type="text/javascript">
		
		function setSelesai() {
			bootbox.prompt({
				title: 'Keterangan / Tindaklanjut', 
				inputType: 'textarea',
				callback: function(result){
					if(result) {
						$.ajax({
							type: "POST",
							url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
							data: {action: 'surat.disposisi_model.set_selesai', 
									ref_id: '<?php echo $parent->disposisi_id; ?>', 
									distribusi_id: '<?php echo get_user_id(); ?>', 
									//row_id: '<?php echo $i; ?>',
									keterangan: result
									},
							success: function(data){
								if(typeof(data.error) != 'undefined') {
									if(data.error == 1) {
										bootbox.alert(data.message);
									}else {
										//displaySelesai('<?php echo $i; ?>');
										bootbox.alert(data.message, function(){ document.location.reload(); });
									}
								} else {
									bootbox.alert("Data transfer error! <br>Silahkan refresh halaman lalu ulangi.");
								}
							}
						});
					} else {
						
					}
				}
			});
		}
	</script>