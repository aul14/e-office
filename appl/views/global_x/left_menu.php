
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
				<!-- Sidebar user panel -->

					<!-- sidebar menu: : style can be found in sidebar.less -->
					<ul class="sidebar-menu">
						<li class="header">&nbsp;</li>
<?php
	if(has_permission(1) || has_permission(2) || has_permission(3) || has_permission(4) || has_permission(5) || has_permission(31)) {
?>
						<li class="treeview">
							<a href="#">
								<i class="fa fa-cogs"></i> <span>Administration</span> <i class="fa fa-angle-left pull-right"></i>
							</a>
							<ul class="treeview-menu">
<?php
		if(has_permission(5)) {
?>
								<li><a href="<?php echo site_url('admin/department'); ?>"><i class="fa fa-building-o"></i> Department </a></li>
<?php
		}
		if(has_permission(2)) {
?>
								<li><a href="<?php echo site_url('user/role_permission'); ?>"><i class="fa fa-users"></i> Role & Permission </a></li>
<?php
		}
		if(has_permission(3)) {
?>
								<li><a href="<?php echo site_url('user'); ?>"><i class="fa fa-user"></i> User </a></li>
<?php
		}
		if(has_permission(31)) {
?>
								<li><a href="<?php echo site_url('job/statistic/' . date('m/Y')); ?>"><i class="fa fa-line-chart"></i> Job Statistic </a></li>
<?php
		}
		if(has_permission(32)) {
?>
								<li><a href="<?php echo site_url('admin/repo'); ?>"><i class="fa fa-file-pdf-o"></i> Repository / Depository </a></li>
<?php
		}
		if(has_permission(4)) {
?>
								<li><a href="<?php echo site_url('admin/system_variables'); ?>"><i class="fa fa-key"></i> Variables </a></li>
<?php
		}
?>
							</ul>
						</li>
<?php
	}
?>
						<li>
							<a href="<?php echo site_url('dashboard'); ?>">
								<i class="fa fa-tasks"></i> <span>My Tasks</span>
							</a>
						</li>
<?php
		if(!has_permission(31)) {
?>
						<li><a href="<?php echo site_url('job/my_statistic'); ?>"><i class="fa fa-line-chart"></i> My Statistic </a></li>
<?php
		}
	
		if(has_permission(6) || has_permission(7)) {
?>

						<li>
							<a href="<?php echo site_url('job'); ?>">
								<i class="fa fa-plus-square"></i> <span>New Job Sheet</span>
							</a>
						</li>
<?php
	}
?>
						<li>
							<a href="<?php echo site_url('job/customer_claim'); ?>">
								<i class="fa fa-bug"></i> <span>Complain Form</span>
							</a>
						</li>

						<li class="treeview">
							<a href="#">
								<i class="fa fa-folder-open"></i> <span>Documents</span> <i class="fa fa-angle-left pull-right"></i>
							</a>
							<ul class="treeview-menu">
								<li>
									<a href="#"><i class="fa fa-folder-open-o"></i> Human Resource <i class="fa fa-angle-left pull-right"></i></a>
									<ul class="treeview-menu">
<?php
	$hr_doc = $this->admin_model->get_doc_group('hr');
	foreach ($hr_doc->result() as $row) {
		if(has_permission($row->permission_id) || has_permission(10)) {
?>
										<li><a href="#<?php //echo site_url('document/sheet/' . $row->doc_type_id); ?>"><i class="fa fa-file-o"></i> <?php echo $row->title; ?></a></li>
<?php
		}
	}
?>
									</ul>
								</li>
								<li>
									<a href="#"><i class="fa fa-folder-open-o"></i> Account <i class="fa fa-angle-left pull-right"></i></a>
									<ul class="treeview-menu">
<?php
	$acc_doc = $this->admin_model->get_doc_group('acc');
	foreach ($acc_doc->result() as $row) {
		if(has_permission($row->permission_id) || has_permission(17)) {
?>
										<li><a href="#<?php //echo site_url('document/sheet/' . $row->doc_type_id); ?>"><i class="fa fa-file-o"></i> <?php echo $row->title; ?></a></li>
<?php
		}
	}
?>
									</ul>
								</li>
								<li>
									<a href="#"><i class="fa fa-folder-open-o"></i> Purchasing <i class="fa fa-angle-left pull-right"></i></a>
									<ul class="treeview-menu">
<?php
	$pc_doc = $this->admin_model->get_doc_group('pc');
	foreach ($pc_doc->result() as $row) {
		if(has_permission($row->permission_id) || has_permission(22)) {
?>
										<li><a href="#<?php //echo site_url('document/sheet/' . $row->doc_type_id); ?>"><i class="fa fa-file-o"></i> <?php echo $row->title; ?></a></li>
<?php
		}
	}
?>
									</ul>
								</li>
							</ul>
						</li>
<?php
	if(has_permission(33)) {
?>
					
						<li>
							<a href="<?php echo site_url('document/repo/kr'); ?>">
								<i class="fa fa-file-pdf-o"></i> <span>Knowledge Repository</span><!--small class="label pull-right bg-red">3</small-->
							</a>
						</li>
<?php
	}
	
	if(has_permission(34)) {
?>

						<li>
							<a href="<?php echo site_url('document/repo/cd'); ?>">
								<i class="fa fa-file-pdf-o"></i> <span>Codes Depository</span><!--small class="label pull-right bg-red">3</small-->
							</a>
						</li>
<?php
	}
?>

						<li>
							<a href="<?php echo site_url('dashboard/calendar'); ?>">
								<i class="fa fa-calendar"></i> <span>Calendar</span><!--small class="label pull-right bg-red">3</small-->
							</a>
						</li>

					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>
