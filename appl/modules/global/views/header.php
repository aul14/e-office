<?php
	$menu_list = $this->admin_model->get_organization_function(1);
	$notification_list = $this->admin_model->get_header_notification();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>e-Office | My Task</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<link rel="shortcut icon" href="<?php echo assets_url(); ?>/media/icon_eoffice.png">
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/plugins/primitives/js/jquery/ui-lightness/jquery-ui-1.10.2.custom.min.css">
		<!-- Bootstrap 3.3.5 -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/bootstrap/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/plugins/font-awesome/css/font-awesome.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/plugins/ionicons/css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/AdminLTE.min.css">
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/tampilan.css">
		<!-- AdminLTE Skins. Choose a skin from the css/skins
		folder instead of downloading all of them to reduce the load. -->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/plugins/daterangepicker/daterangepicker-bs3.css">
		<!--<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/skins/_all-skins.min.css">-->
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/skins/skin-red-dark.css">
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/css/base.css">
		<link rel="stylesheet" href="<?php echo assets_url(); ?>/plugins/pie-menu/css/piemenu.css">
		
		<style type="text/css">
			
			.well {
				margin: 10px 0;
				padding: 10px;
				background: #f5efef;
			}
			
			legend {
				font-size: inherit;
				font-weight: 700;
			}
			
			.form-control {
	    		background-color: #f8f3ed;
	    	}
		</style>
<?php 
	foreach ($style_extras as $style_extra) { echo ' <link rel="stylesheet" type="text/css" media="screen" href="' . $style_extra . '" />'; } 
?>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
		<script src="<?php echo assets_url(); ?>/js/modernizr-2.6.2.min.js"></script>
				
		<!-- jQuery 2.1.4 -->
		<script src="<?php echo assets_url(); ?>/plugins/jQuery/jQuery-2.1.4.min.js"></script>
		<!-- Bootstrap 3.3.5 -->
		<script src="<?php echo assets_url(); ?>/bootstrap/js/bootstrap.min.js"></script>
	    <!-- jQuery UI 1.11.4 -->
	    <script src="<?php echo assets_url(); ?>/plugins/jQueryUI/jquery-ui.min.js"></script>
		<!-- Bootbox -->
		<script src="<?php echo assets_url(); ?>/plugins/bootbox/bootbox.min.js"></script>
		<!-- Noty -->
		<script src="<?php echo assets_url(); ?>/plugins/noty/jquery.noty.packaged.min.js"></script>
		<!-- daterange -->	
		<script src="<?php echo assets_url(); ?>/plugins/daterangepicker/daterangepicker.js"></script>
		<script src="<?php echo assets_url(); ?>/plugins/jquery_number/jquery.number.js"></script>
		
		<script src="<?php echo assets_url(); ?>/js/lx.js"></script>

<?php 
	foreach ($js_extras as $js_extra) { echo '<script type="text/javascript" src="' . $js_extra . '"></script>'; } 
	
	$angel_difference = ($menu_list->num_rows() > 0) ? (($menu_list->num_rows() * 30) + 30) : 90;
?>
		<script type="text/javascript">
			function read_notify(notify_id) {
				$.ajax({
					type: "POST",
					url: "<?php echo str_replace($this->config->item('url_suffix'), "", site_url('global/dashboard/notification')); ?>",
					data: { notify_id: notify_id },
					success: function(data) {
						if(data == 1) {
							return true;
						} else {
							bootbox.alert(data);
						}
					}
				});
			}

			function PieMenuInit() {		
				$('#outer_container').PieMenu({
					'starting_angel': 90,
					'angel_difference' : <?php echo $angel_difference; ?>,
					'radius': 100,
				});
			}

			function PieMenuPos() {
				$('.menu_button').css('top', (($(window).height() / 2) + $('.menu_button').offset().top) + 'px');
				$('.menu_option').css('top', (($(window).height() / 2) + $('.menu_option').offset().top) + 'px');
			}

			function addDirectory() {
				bootbox.prompt("Tambah Direktori Baru.", function(result) {	saveDirectory(result);	});
			}

			function saveDirectory(nm) {
				location.assign('<?php echo site_url('global/dashboard/add_dir/'); ?>/' + nm);
// 				alert(nm);
			}

			$( window ).scroll(function() {
				$('.menu_button').css('top', (($(window).height() / 2) + $(window).scrollTop()) + 'px'); 
				$('.menu_option').css('top', (($(window).height() / 2) + $(window).scrollTop() + 6) + 'px'); 
			});
			
			$(document).ready(function() {
				$( "#outer_container" ).draggable();
				PieMenuInit();
				PieMenuPos();
				
<?php 
	if(get_user_data('lx_error_msg')) {
?>
			noty({text: '<?php echo get_user_data('lx_error_msg'); ?>', type: 'error'});
<?php 
		$this->session->unset_userdata('lx_error_msg');
	}
	if(get_user_data('lx_warning_msg')) {
?>
			noty({text: '<?php echo get_user_data('lx_warning_msg'); ?>', type: 'warning'});
<?php 
		$this->session->unset_userdata('lx_warning_msg');
	}
	if(get_user_data('lx_success_msg')) {
?>
			noty({text: '<?php echo get_user_data('lx_success_msg'); ?>', type: 'success'});
<?php 
		$this->session->unset_userdata('lx_success_msg');
	}
?>
			});
		</script>
	</head>
	<body class="hold-transition skin-red-dark sidebar-mini fixed">
		<div class="slider">
			<div id='outer_container' class="outer_container">
				<a class="menu_button" href="#" title="Catat dokumen baru."><span>Catat dokumen baru.</span></a>
				<ul class="menu_option" style="z-index: 9998;">
					<li title="Input Arsip"></li>
					<!--<li title="Input Arsip"></li> -->
<?php 
	foreach($menu_list->result() as $sub_row) {
		if($sub_row->permission_access == 0 || has_permission($sub_row->permission_access)) {
			//if ($sub_row->function_ref_name != "Masuk Eksternal Log") {
			if ($sub_row->function_ref_name != "Disposisi" && $sub_row->function_ref_name != "Masuk Eksternal Log") {	
?>
					<li title="<?php echo humanize($sub_row->function_ref_name); ?>"><a href="<?php echo site_url( 'surat/' . $sub_row->module_function); ?>"><span><i class="fa fa-<?php echo $sub_row->icon; ?>"></i> <?php //echo $sub_row->function_ref_name; ?> </a></li>
<?php 
			}
		}
	}
	//if(!has_permission(23)){
	if(has_permission(7)) {	
?>	
					<li title="Input Arsip SME"><a href="<?php echo site_url( 'surat/arsip/input_arsip'); ?>" ><span><i class="fa fa-archive"></i> Buat Arsip SME</a></li>
					
					<li title="Input Arsip SKE"><a href="<?php echo site_url( 'surat/arsip/input_arsip_SKE'); ?>" ><span><i class="fa fa-archive"></i> Buat Arsip SKE</a></li>
	
<?php	
	}
	//if(has_permission(23)) {
?>
					<!-- <li title="Pengantar Surat"><a href="<?php echo site_url( 'surat/pengantar'); ?>" ><span><i class="fa fa-chevron-right"></i> Pengantar </a></li> --> 
<?php 
	//}

	if(has_permission(23)|| has_permission(1)) {
?>
					<li title="Input Kontrak"><a href="<?php echo site_url( 'surat/kontrak/input_kontrak'); ?>" ><span><i class="fa fa-file-text"></i> </a></li>
<?php 
	}
?>				
					<!--li title="My Directory"><a href="javascript:void(0);" onclick="addDirectory();"><span><i class="fa fa-folder-open"></i> My Directory </a></li-->
				</ul>
			</div>	
		
			<header class="main-header">
				<!-- Logo -->
				<a href="<?php echo site_url(); ?>" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><!-- b>e</b --><img alt="e-Office" src="<?php echo assets_url(); ?>/media/Logo_e_baru.png" style="height: 35px;"></span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg"> <!-- b>e-Office</b--><img alt="e-Office" src="<?php echo assets_url(); ?>/media/Logo_text_baru.png" style="height: 35px;"> </span>
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
	/* $notify = $notification_list->result();
?>						
						<!-- Notifications: style can be found in dropdown.less -->
				          <li class="dropdown notifications-menu">
				            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
				              <i class="fa fa-bell-o"></i>
				              <span class="label label-warning"><?php echo (count($notify) > 0) ? count($notify) : ''; ?></span>
				            </a>
				            <ul class="dropdown-menu">
				              <li class="header">You have <?php echo count($notify); ?> notifications</li>
				              <li>
				                <!-- inner menu: contains the actual data -->
				                <ul class="menu">
<?php 
		if (count($notify) > 0) {
			foreach ($notify as $row)
			{
			//$notify = json_decode('notification_list', TRUE);
?>				                
				                  <li>
				                    <a href="<?php echo site_url() . $row->detail_link; ?>" onclick="read_notify(<?php echo $row->notify_id; ?>);" title="<?php echo $row->note; ?>">
				                      <i class="fa fa-envelope text-red"></i> <b><?php echo $row->note; ?></b>
				                    </a>
				                  </li>
<?php
			}
		}
?>
				                </ul>
				              </li>
				              <li class="footer"><a href="<?php echo site_url() . 'global/notification'; ?>">View all</a></li>
				            </ul>
				          </li>
<?php*/

	$file_pic = str_replace('/lx_media', 'assets/media', get_user_data('photo'));
	
	if( file_exists($file_pic) )
	{
		$photo = base_url() . $file_pic;
	}else {
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
										<p><?php echo get_user_data('user_name'); ?> - <?php echo get_user_data('jabatan'); ?><small><?php echo get_user_data('unit_name'); ?></small></p>
									</li>

									<!-- Menu Footer-->
									<li class="user-footer">
										<div class="pull-left">
											<a href="<?php echo site_url('auth/user/account'); ?>" class="btn btn-default btn-flat">Profile</a>
										</div>
										<div class="pull-right">
											<a href="<?php echo site_url('auth/login/logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
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