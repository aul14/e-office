<?php 
//	echo gettype($diskusi);
	if(!is_object($diskusi)) {
		$diskusi = new stdClass();
	}
?>
<div class="box box-primary direct-chat direct-chat-primary">
	<div class="box-header with-border">
		<h3 class="box-title">Diskusi</h3>
		<!-- div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" title="Menu" onclick="refreshChat_<?php echo $script_handle; ?>('<?php echo $id; ?>')"><i class="fa fa-refresh"></i></button>
		</div -->
	</div>
	<!-- /.box-header -->

	<div class="box-body" style="overflow: hidden;">
		<!-- Conversations are loaded here -->
		<div id="direct-chat-messages_<?php echo $id; ?>" class="direct-chat-messages" style="height: 190px;">
<?php
	foreach($diskusi as $key => $row) {
		if(file_exists( str_replace('/lx_media', './assets/media/', $row->profile_pic))) {
			$pp = $row->profile_pic;
		} else {
			$pp = '/lx_media/photo/m.jpg';
		}
?>
			<!-- Message. Default to the left -->
			<div class="direct-chat-msg <?php echo ($row->user_id == get_user_id()) ? 'right' : ''; ?>">
				<div class="direct-chat-info clearfix">
					<span class="direct-chat-name pull-left"><?php echo $row->name; ?></span>
					<span class="direct-chat-timestamp pull-right"><?php echo $key; ?></span>
				</div>
				<img class="direct-chat-img" src="<?php echo $pp; ?>" alt="Message User Image">
				<div class="direct-chat-text"><?php echo $row->text; ?></div>
			</div>
			<!-- /.direct-chat-msg -->
<?php
	}
?>
		</div>
		<!--/.direct-chat-messages-->

	</div>
	<!-- /.box-body -->
<?php 
	if($active) {
?>
	<div class="box-footer complete_state_off_<?php echo $script_handle; ?>">
		<div class="input-group">
			<input type="text" id="message_<?php echo $id; ?>" name="message_<?php echo $id; ?>" placeholder="Type Message ..." class="form-control">
			<span class="input-group-btn">
				<button type="button" class="btn btn-primary btn-flat" onclick="sendChat_<?php echo $script_handle; ?>('<?php echo $id; ?>')">Send</button>
			</span>
		</div>
	</div>
	<!-- /.box-footer -->
<?php
	}
?>
</div>
<!-- /.direct-chat -->
<script type="text/javascript">
	
	$(document).ready(function() {
		$('#direct-chat-messages_<?php echo $id; ?>').scrollTop($('#direct-chat-messages_<?php echo $id; ?>')[0].scrollHeight);
	
	}); //end document

<?php 
	if($active) {
		if(file_exists( str_replace('/lx_media', './assets/media/', get_user_data('photo')))) {
			$pp = get_user_data('photo');
		} else {
			$pp = '/lx_media/photo/m.jpg';
		}
?>
	function sendChat_<?php echo $script_handle; ?>(cid) {
		if($('#message_' + cid).val() != '') {
			$.ajax({
				type: "POST",
				url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
				data: {action: '<?php echo $function_handle; ?>', 
						ref_id: '<?php echo $ref_id; ?>', 
						distribusi_id: cid, 
						text: $('#message_' + cid).val(),
						user_id: '<?php echo get_user_id(); ?>', 
						name: '<?php echo get_user_data('user_name'); ?>', 
						profile_pic: '<?php echo get_user_data('photo'); ?>'
					},
				success: function(data) {
					if(typeof(data.error) != 'undefined') {
						if(data.error == 0) {
							var chat = '<div class="direct-chat-msg right">' +
										'	<div class="direct-chat-info clearfix">' +
										'		<span class="direct-chat-name pull-left"><?php echo get_user_data('user_name'); ?></span>' +
										'		<span class="direct-chat-timestamp pull-right">Just now.</span>' +
										'	</div>' +
										'	<img class="direct-chat-img" src="<?php echo $pp; ?>" alt="Message User Image">' +
										'	<div class="direct-chat-text">' +$('#message_' + cid).val() + '</div>' +
										'</div>';
							
							$('#direct-chat-messages_' + cid).append(chat);
							$('#direct-chat-messages_' + cid).scrollTop($('#direct-chat-messages_' + cid)[0].scrollHeight);
							$('#message_' + cid).val('');
						} else {
							switch(data.error) {
								case 1:
									break;
								case 2:
									eval(data.execute);
									break;
							}
							bootbox.alert(data.message);
						}
					} else {
						bootbox.alert("Data transfer error!");
					}
				}
			});
		}
	}
<?php
	}
?>

	function refreshChat_<?php echo $script_handle; ?>(cid) {
		$.ajax({
			type: "POST",
			url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/ajax_handler')); ?>/",
			data: {action: 'surat.disposisi_model.reload_diskusi', 
					ref_id: '<?php echo $ref_id; ?>', 
					distribusi_id: cid
				},
			success: function(data) {
				if(typeof(data.error) != 'undefined') {
					$('#direct-chat-messages_' + cid).html(data.diskusi);
				}else {
					bootbox.alert(data.message);
				}
			}
		});
	}

</script>