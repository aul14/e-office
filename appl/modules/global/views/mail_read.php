
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		General <?php echo $title; ?><small> <?php ?></small>
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
			<a href="<?php echo site_url('mail/compose'); ?>" class="btn btn-primary btn-block margin-bottom">Compose</a>
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
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">MINDMATICS SDN BHD "GENERAL MEMO"</h3>
					<div class="box-tools pull-right">
						<a href="#" class="btn btn-box-tool" data-toggle="tooltip" title="Previous"><i class="fa fa-chevron-left"></i></a>
						<a href="#" class="btn btn-box-tool" data-toggle="tooltip" title="Next"><i class="fa fa-chevron-right"></i></a>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body no-padding">
					<div class="mailbox-read-info">
						<h3><?php echo $subject; ?></h3>
						<h5>From: <?php echo $mail_from; ?> <span class="mailbox-read-time pull-right"><?php echo $delivery_time; ?></span></h5>
					</div><!-- /.mailbox-read-info -->
					<div class="mailbox-controls with-border">
						<h5>Receipt:</h5>
						<div class="row">
<?php
	foreach($receipt as $row) {
?>
							<div class="col-md-3 col-sm-4"><?php echo ($row->read_time) ? ('<i class="fa fa-check" title="(' . $row->read_time . ')"></i> ' . $row->receipt_name) : ('<strong><i class="fa fa-clock-o"></i> ' . $row->receipt_name . '</strong>'); ?></div>
<?php
	}
?>
						</div>
						<!--div class="btn-group">
							<button class="btn btn-default btn-sm" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
							<button class="btn btn-default btn-sm" data-toggle="tooltip" title="Reply"><i class="fa fa-reply"></i></button>
							<button class="btn btn-default btn-sm" data-toggle="tooltip" title="Forward"><i class="fa fa-share"></i></button>
						</div>
						<button class="btn btn-default btn-sm" data-toggle="tooltip" title="Print"><i class="fa fa-print"></i></button-->
					</div><!-- /.mailbox-controls -->
					<div class="mailbox-read-message">
						<?php echo $body; ?>
					</div><!-- /.mailbox-read-message -->
				</div><!-- /.box-body -->
				
			</div><!-- /. box -->
		</div><!-- /.col -->
	</div><!-- /.row -->
</section><!-- /.content -->

