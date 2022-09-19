<script type="text/javascript">
	$(document).ready(function() {
	//Add text editor
		$("#mail_body").wysihtml5();

		$(".select2").select2();

	});

	function sendDraft() {
		$('#mail_action').val('mail_model.draft_mail');
		var fe = $('#form_mail');
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('admin/ajax_handler'); ?>',
			data: fe.serialize()
		})
		.done(function(data) {
			if(typeof(data.error) != 'undefined') {
				if(data.error != '') {
					bootbox.alert(data.message);
				} else {
					bootbox.alert(data.message);
					eval(data.execute);
				}
			} else {
				bootbox.alert("Data transfer error!");
			}
		}); 
		return false;
	}

	function sendMemo(fe) {
		$('#mail_action').val('mail_model.send_mail');
		sendData(fe);

		return false;
	}
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		General Memo <?php echo $title; ?><small> <?php ?></small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Memo</a></li>
		<li class="active"> <?php echo $title; ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-3">
			<a href="<?php echo site_url('mail/inbox'); ?>" class="btn btn-primary btn-block margin-bottom">Back To Inbox</a>
			<div class="box box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Folders</h3>
					<div class="box-tools">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body no-padding">
<?php
	$list = $this->mail_model->get_mail();
?>
					<ul class="nav nav-pills nav-stacked">
						<li<?php ?>><a href="<?php echo site_url('mail/inbox'); ?>"><i class="fa fa-inbox"></i> Inbox <?php echo ($list->num_rows() == 0) ? '' : ('<span class="label label-primary pull-right">' . $list->num_rows() . '</span>'); ?></a></li>
						<li<?php ?>><a href="<?php echo site_url('mail/outbox'); ?>"><i class="fa fa-envelope-o"></i> Sent</a></li>
						<li<?php ?>><a href="<?php echo site_url('mail/draft'); ?>"><i class="fa fa-file-text-o"></i> Drafts</a></li>
					</ul>
				</div><!-- /.box-body -->
			</div><!-- /. box -->

		</div><!-- /.col -->
		<div class="col-md-9">
<?php
	echo form_open('admin/ajax_handler', ' id="form_mail" class="" onsubmit="return sendMemo($(this));"'); 
?>
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">MINDMATICS SDN BHD "GENERAL MEMO"</h3>
				</div><!-- /.box-header -->
				<input type="hidden" id="mail_id" name="mail_id" value="0" />
				<input type="hidden" id="mail_action" name="action" value="<?php echo $mail_id; ?>" />
				<div class="box-body">
					<div class="form-group">
<?php
	$opt_receipt = $this->user_model->get_user_assoc();
	unset($opt_receipt[get_user_id()]);
	echo form_multiselect('mail_receipt[]', $opt_receipt, $receipt, 'id="mail_receipt" class="form-control select2" placeholder="To:"');
?>
					</div>
					<div class="form-group">
						<input type="text" id="mail_subject" name="subject" class="form-control" placeholder="Subject:" value="<?php echo $subject; ?>">
					</div>
					<div class="form-group">
						<textarea id="mail_body" name="body" class="form-control" style="height: 300px"><?php echo $body; ?></textarea>
					</div>
				</div><!-- /.box-body -->
				<div class="box-footer">
					<div class="pull-right">
						<button type="button" class="btn btn-default" onclick="sendDraft()"><i class="fa fa-pencil"></i> Draft</button>
						<button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Send</button>
					</div>
				</div><!-- /.box-footer -->
			</div><!-- /. box -->

<?php
	echo form_close();
?>

		</div><!-- /.col -->
	</div><!-- /.row -->
</section><!-- /.content -->
