<script type="text/javascript">
	$(document).ready(function() {

	});
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
	$list_unread_inbox = $this->mail_model->get_mail();
?>
					<ul class="nav nav-pills nav-stacked">
						<li<?php ?>><a href="<?php echo site_url('mail/inbox'); ?>"><i class="fa fa-inbox"></i> Inbox <?php echo ($list_unread_inbox->num_rows() == 0) ? '' : ('<span class="label label-primary pull-right">' . $list->num_rows() . '</span>'); ?></a></li>
						<li<?php ?>><a href="<?php echo site_url('mail/outbox'); ?>"><i class="fa fa-envelope-o"></i> Sent</a></li>
						<li<?php ?>><a href="<?php echo site_url('mail/draft'); ?>"><i class="fa fa-file-text-o"></i> Drafts</a></li>
					</ul>
				</div><!-- /.box-body -->
			</div><!-- /. box -->
			
		</div><!-- /.col -->
		<div class="col-md-9">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">MINDMATICS SDN BHD "GENERAL MEMO" <?php /*echo $title;*/ ?></h3>
					<div class="box-tools pull-right">
						<div class="has-feedback">
							<button class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button>
							<!--input type="text" class="form-control input-sm" placeholder="Search Mail">
							<span class="glyphicon glyphicon-search form-control-feedback"></span-->
						</div>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body no-padding">
					<div class="table-responsive mailbox-messages">
						<table class="table table-hover table-striped">
							<tbody>
<?php
	foreach ($list->result() as $row) {
?>
								<tr>
									<td><input type="checkbox"></td>
									<td class="mailbox-name">
										<a href="<?php echo ($active_mail_menu == 'draft') ? site_url('mail/compose/' . $row->mail_id) : site_url('mail/read/' . $row->mail_id); ?>">
											<?php echo ($active_mail_menu == 'inbox') ? $row->mail_from : $row->receipt_text; ?>
										</a>
									</td>
									<td class="mailbox-subject">
<?php 
		if($active_mail_menu == 'inbox' && $row->read_time == '') {
?>
										<b><?php echo $row->subject; ?></b>
<?php 
		} else {
?>
										<?php echo $row->subject; ?>
<?php 
		}
?>
									</td>
									<td class="mailbox-date">
<?php echo ($active_mail_menu == 'draft') ? $row->created_time : $row->delivery_time; ?></td>
								</tr>
<?php
	}
?>							</tbody>
						</table><!-- /.table -->
					</div><!-- /.mail-box-messages -->
				</div><!-- /.box-body -->
				
			</div><!-- /. box -->
		</div><!-- /.col -->
	</div><!-- /.row -->
</section><!-- /.content -->
