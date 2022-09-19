<?php
	echo form_open_multipart('global/dashboard/ajax_handler', ' id="form_posisi" class="form-horizontal"');
	echo form_hidden('action', 'auth.user_model.save_user_structure');
	$result = $this->user_model->get_user_structure($user_id);
	if($result->num_rows() > 0) {
		$mode = 'edit';
		$posisi = $result->row();
		echo form_hidden('entry_id', $posisi->entry_id); 
//		var_dump($posisi);
	} else {
		$mode = 'add';
	}

	echo form_hidden('mode', $mode); 
	echo form_hidden('user_id', $user_id); 
?>

	<div class="form-group">
		<label for="unit_name" class="col-sm-3 control-label">Unit <i class="fa fa-question" title="masukan minimal 3 karakter keyword untuk pencarian automatis..." style="color: #3c8dbc;"></i> </label>
		<div class="col-sm-9">
			<div class="input-group">
				<input type="text" id="unit_name" class="form-control required" data-input-title="Unit" value="<?php echo ($mode == 'edit') ? $posisi->unit_name : ''; ?>" placeholder="Bagian / Sub Bagian...">
				<div id="unit_code" class="input-group-addon"><?php echo ($mode == 'edit') ? $posisi->unit_code : '________'; ?></div>	
				<input type="hidden" id="organization_structure_id" name="organization_structure_id" value="<?php echo ($mode == 'edit') ? $posisi->organization_structure_id : ''; ?>">
				<input type="hidden" id="pejabat_id" name="pejabat_id" value="<?php echo ($mode == 'edit') ? $user_id : ''; ?>">
				<input type="hidden" id="pejabat_name" name="pejabat_name" value="">
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="jabatan" class="col-sm-3 control-label">Jabatan</label>
		<div class="col-sm-9">
<?php 
	$opt_jabatan = array_merge(array('Staff' => 'Staff'), $this->admin_model->get_system_config('jabatan'));
	echo form_dropdown('jabatan', $opt_jabatan, (($mode == 'edit') ? $posisi->jabatan : ''), (' id="jabatan" class="form-control" data-input-title="Jabatan" ' . ((!has_permission(3)) ? ' readonly="readonly"' : '' )));
?>
		</div>
	</div>
	<div class="form-group">
		<label for="jabatan" class="col-sm-3 control-label">Pangkat / Golongan</label>
		<div class="col-sm-9">
<?php 
	$opt_pangkat = array_merge(array('-' => ''), $this->admin_model->get_system_config('pangkat'));
	echo form_dropdown('pangkat', $opt_pangkat, (($mode == 'edit') ? $posisi->pangkat : ''), (' id="pangkat" class="form-control" data-input-title="Pangkat" ' . ((!has_permission(3)) ? ' readonly="readonly"' : '' )));
?>
		</div>
	</div>
	<div class="form-group">
		<label for="instansi" class="col-sm-3 control-label">Direktorat</label>
		<div class="col-sm-9">
			<input type="text" id="instansi" class="form-control" readonly="readonly" data-input-title="Direktorat" value="<?php echo ($mode == 'edit') ? $posisi->instansi : ''; ?>" placeholder="Direktorat...">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-6">
		</div>
		<div class="col-sm-6">
			<button type="button" class="btn btn-danger pull-right" onclick="updatePosisi();">Simpan</button>
		</div>
	</div>

<?php
	echo form_close(); 
?>

	<script type="text/javascript">
		$(document).ready(function() {

			$('#unit_name').autocomplete({
				source: '<?php echo site_url('global/admin/internal_autocomplete')?>',
				minLength: 3,
				select: function(event, ui) {
					$('#unit_code').html(ui.item.unit_code);
					$('#organization_structure_id').val(ui.item.id);
					$('#jabatan').val(ui.item.jabatan);
					$('#instansi').val(ui.item.instansi);
					$('#pejabat_id').val(ui.item.user_id);
					$('#pejabat_name').val(ui.item.nama_pejabat);
				}
			});
			
			$('#unit_name').keyup(function() {
				if($(this).val().trim() == '') {
					$('#unit_code').html('________');
					$('#organization_structure_id').val('');
					$('#jabatan').val('Staff');
					$('#instansi').val('');
				}
			});
		});
		
		function updatePosisi() {
			if($('#pejabat_id').val() == '<?php echo $user_id; ?>' || $('#pejabat_name').val() == '') {
				$msg = "Update Posisi?";
			} else {
				$msg = "Update user sebagai pengganti " + $('#pejabat_name').val() + "?";
			}
			
			bootbox.confirm($msg, function(result) {
				if(result) { 
					$('#form_posisi').submit();
				} 
			});
		}

		$('#form_posisi').ajaxForm({
			beforeSend: function(xhr) {
				$('#posisi-overlay').removeClass('hide');
			},
			success: function() {
				//$('#bar-pp').width('100%');
			},
			error: function(data) {
				bootbox.alert('Upload failed');
				// $('#bar-pp').width('0%');
				// $('#pp').val('');
			},
			complete: function(xhr) {
				$('#posisi-overlay').addClass('hide');
				if(xhr.responseJSON.error == '') {
					bootbox.alert(xhr.responseJSON.message);
				}
			}
		}); 

	</script>