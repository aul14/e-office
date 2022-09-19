<?php

?>
	<div class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
<?php 
	$hidden = array();
	echo form_open('', '', $hidden);
?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Buat Nomor Surat Keluar</h4>
				</div>
				
				<div class="modal-body">
					<p>One fine body&hellip;</p>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
					<button class="btn btn-primary">Simpan</button>
				</div>
				
			</div><!-- /.modal-content -->
<?php 
	echo form_close();
?>
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

<script type="text/javascript">
	function createNomorSuratKeluar() {
		$('#modal_nomor_surat_keluar').modal('show');
	}
</script>