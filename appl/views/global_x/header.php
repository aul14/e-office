<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>e-Office | My Task</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.5 -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/bootstrap/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/AdminLTE.min.css">
		<!-- AdminLTE Skins. Choose a skin from the css/skins
		folder instead of downloading all of them to reduce the load. -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/skins/_all-skins.min.css">
		<style type="text/css">
			
			.well {
				margin-bottom: 0;
			}

		</style>
<?php 
	foreach ($style_extras as $style_extra) { echo '	<link rel="stylesheet" type="text/css" media="screen" href="' . $style_extra . '" />'; } 
?>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery 2.1.4 -->
		<script src="<?php echo assets_url(); ?>/plugins/jQuery/jQuery-2.1.4.min.js"></script>
		<!-- Bootstrap 3.3.5 -->
		<script src="<?php echo assets_url(); ?>/bootstrap/js/bootstrap.min.js"></script>
	    <!-- jQuery UI 1.11.4 -->
	    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
		<!-- Bootbox -->
		<script src="<?php echo assets_url(); ?>/plugins/bootbox/bootbox.min.js"></script>
		<!-- Noty -->
		<script src="<?php echo assets_url(); ?>/plugins/noty/jquery.noty.min.js"></script>

		<script src="<?php echo assets_url(); ?>/js/lx.js"></script>

<?php 
	foreach ($js_extras as $js_extra) { echo '	<script type="text/javascript" src="' . $js_extra . '"></script>'; } 
?>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<div class="wrapper">

			<header class="main-header">
				<!-- Logo -->
				<a href="<?php echo site_url(); ?>" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><i class="fa fa-fw fa-university"></i> <b>e</b></span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg"><img src="<?php echo assets_url(); ?>/img/logo.png" width="35px" /> <b>e-Office</b></span>
				</a>

				<!-- Header Navbar: style can be found in header.less -->
				<nav class="navbar navbar-static-top" role="navigation">
					<!-- Sidebar toggle button-->
					<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>

					<div class="navbar-custom-menu">
						<ul class="nav navbar-nav">
						<!-- Messages: style can be found in dropdown.less-->
<?php
	$list = $this->mail_model->get_mail();
?>
							<li class="dropdown messages-menu">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-sticky-note"></i> <?php echo ($list->num_rows() == 0) ? '' : ('<span class="label label-success">' . $list->num_rows() . '</span>'); ?>
								</a>
								<ul class="dropdown-menu">
									<li class="header">You have <?php echo $list->num_rows(); ?> memos</li>
									<li>
										<!-- inner menu: contains the actual data -->
										<ul class="menu">
<?php
	foreach ($list->result() as $row) {
?>
											<li><!-- start message -->
												<a href="<?php echo site_url('mail/read/' . $row->mail_id); ?>">
													<div class="pull-left">
														<img src="<?php echo $row->sender_photo; ?>" class="img-circle" alt="User Image">
													</div>
													<h4><?php echo $row->mail_from; ?> <small><i class="fa fa-clock-o"></i> <?php echo $row->delivery_time; ?></small></h4>
													<p><?php echo $row->subject; ?></p>
												</a>
											</li><!-- end message -->
<?php
	}
?>
										</ul>
									</li>
									<li class="footer"><a href="<?php echo site_url('mail/compose/'); ?>">Create Memo</a></li>
									<li class="footer"><a href="<?php echo site_url('mail'); ?>">See All Memos</a></li>
								</ul>
							</li>
<?php
	if(file_exists( str_replace('/lx_media', './assets/media/', get_user_data('photo')))) {
		$photo = get_user_data('photo');
	} else {
		$photo = '/lx_media/photo/m.jpg';
	}
?>
							<!-- User Account: style can be found in dropdown.less -->
							<li class="dropdown user user-menu">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="<?php echo $photo; ?>" class="user-image" alt="User Image">
									<span class="hidden-xs"><?php echo get_user_data('user_name'); ?></span>
								</a>
								<ul class="dropdown-menu">
								
									<!-- User image -->
									<li class="user-header">
										<img src="<?php echo $photo; ?>" class="img-circle" alt="User Image">
										<p><?php echo get_user_data('user_name'); ?> - <?php echo get_user_data('role_name'); ?><small><?php echo get_user_data('department_name'); ?></small></p>
									</li>

									<!-- Menu Footer-->
									<li class="user-footer">
										<div class="pull-left">
											<a href="<?php echo site_url('user/account'); ?>" class="btn btn-default btn-flat">Profile</a>
										</div>
										<div class="pull-right">
											<a href="<?php echo site_url('login/logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
										</div>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</nav>
			</header>

			<?php view_page('global/left_menu'); ?>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">