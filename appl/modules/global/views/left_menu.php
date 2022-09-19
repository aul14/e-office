
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
				<!-- Sidebar user panel -->
					<!-- search form -->
					<!--
					<form action="<?php echo site_url('global/dashboard/search_keywords/' . $search_type); ?>" method="post" class="sidebar-form">
						<input type="hidden" name="search_type" value="<?php echo $search_type; ?>">
						
						<div class="input-group">
							<input type="text" name="search_keyword" class="form-control" placeholder="Search...">
							<span class="input-group-btn">
								<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</form>
					-->
					<!-- /.search form -->
					
					<div style="padding-top: 30px;width: 100%;text-align: center;">
						&nbsp;<!--<img style="width: 55%;" src="<?php echo base_url(); ?>assets/media/logo.png" />-->
					</div>
					
					<!-- sidebar menu: : style can be found in sidebar.less -->
					<ul class="sidebar-menu">
						<li class="header" style="background-color: #222d32;">&nbsp;</li>
						<li>
							<a href="<?php echo site_url('global/dashboard'); ?>">
								<i class="fa fa-tasks"></i> <span>Dashboard</span>
							</a>
						</li>
 <?php 
	if(has_permission(1) || has_permission(2) || has_permission(3) || has_permission(4) || has_permission(5) || has_permission(6) || has_permission(18) || has_permission(12)) {
?>
						<li class="treeview">
							<a href="#">
								<i class="fa fa-cogs"></i> <span>Administration</span> <i class="fa fa-angle-left pull-right"></i>
							</a>
							<ul class="treeview-menu">
<?php
		if(has_permission(5)) {
?>
								<li><a href="<?php echo site_url('global/admin/org_structure'); ?>"><i class="fa fa-building-o"></i> Struktur Organisasi </a></li>
								<li><a href="<?php echo site_url('global/admin/format_surat'); ?>"><i class="fa fa-envelope"></i> Format Surat </a></li>
<?php
		}
		if(has_permission(2)) {
?>
								<li><a href="<?php echo site_url('auth/user/role_permission'); ?>"><i class="fa fa-users"></i> Role & Permission </a></li>
<?php
		}
		if(has_permission(3)) {
?>
								<li><a href="<?php echo site_url('auth/user'); ?>"><i class="fa fa-user"></i> User </a></li>
<?php
		}
		if(has_permission(1)) {
?>
								<li><a href="<?php echo site_url('global/admin/referensi'); ?>"><i class="fa fa-briefcase"></i> Referensi </a></li>
<?php
		}
		if(has_permission(18)) {
?>
								<li><a href="<?php echo site_url('global/admin/tujuan_surat'); ?>"><i class="fa fa-briefcase"></i> Tujuan Surat </a></li>
<?php	
		}
		if(has_permission(18)) {
?>
								<li><a href="<?php echo site_url('global/admin/tujuan_surat_eksternal'); ?>"><i class="fa fa-briefcase"></i> Tujuan Surat Eksternal </a></li>								
<?php
		}				
/*		if(has_permission(12)) {
?>
								<li><a href="<?php echo site_url('global/dashboard/surat_log/surat_masuk_eksternal'); ?>"><i class="fa fa-envelope"></i> Surat Log </a></li>								
<?php
		}
/*		if(has_permission(4)) {
?>
								<li><a href="<?php echo site_url('admin/system_variables'); ?>"><i class="fa fa-key"></i> Variables </a></li>
<?php
		}
*/
		if(has_permission(12)) {
?>
								<li><a href="<?php echo site_url('global/admin/klasifikasi_arsip'); ?>"><i class="fa fa-archive"></i> Klasifikasi Arsip </a></li>
<?php
		}
?>
							</ul>
						</li>
<?php
	}
		if(has_permission(29) || has_permission(23)) {
 ?>
					<li class="treeview">
						<a href="#">
							<i class="fa fa-file-text"></i> <span>Contract Maintenance</span> <i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu">
<?php
			if(has_permission(23)) {
?>							
							<li><a href="<?php echo site_url('global/admin/mitra'); ?>"><i class="fa fa-briefcase"></i> Mitra </a></li>
<?php
			}
?>				
							<li><a href="<?php echo site_url('surat/kontrak/kontrak_aktif'); ?>"><i class="fa fa-file-text"></i> Kontrak Aktif </a></li>
							<li><a href="<?php echo site_url('surat/kontrak/kontrak_selesai'); ?>"><i class="fa fa-file-text"></i> Kontrak Selesai </a></li>
						</ul>
					</li>
<?php 
		}

	$list = $this->admin_model->get_organization_module();
	foreach($list->result() as $row) {
?>
					<li class="treeview">
<?php
		if($row->module_ref_id == 3) {
			if(has_permission(7)) {
?>						
						<a href="#" title="<?php echo $row->description; ?>">
							<i class="fa fa-<?php echo $row->icon; ?>"></i> <span><?php echo $row->module_ref_name; ?></span> <i class="fa fa-angle-left pull-right"></i>
						</a>
<?php
			}
		}else{
?>				
						<a href="#" title="<?php echo $row->description; ?>">
							<i class="fa fa-<?php echo $row->icon; ?>"></i> <span><?php echo $row->module_ref_name; ?></span> <i class="fa fa-angle-left pull-right"></i>
						</a>
<?php
		}
?>		
						<ul class="treeview-menu">
<?php 
		$sub_list = $this->admin_model->get_organization_function($row->module_ref_id);
		foreach($sub_list->result() as $sub_row) {			 	
//			if($sub_row->permission_access == 0 || has_permission($sub_row->permission_access)) {
			if ($sub_row->function_ref_name != 'Surat Keputusan') {
?>
							<li><a href="<?php echo site_url($sub_row->link); ?>" title="<?php echo $sub_row->description; ?>"><i class="fa fa-<?php echo $sub_row->icon; ?>"></i> <?php echo $sub_row->function_ref_name; ?> </a></li>
<?php				
			}else{
?>
							<li><a href="<?php echo site_url($sub_row->link); ?>" title="<?php echo $sub_row->description; ?>"><i class="fa fa-<?php echo $sub_row->icon; ?>"></i> Monitoring Surat Keputusan </a></li>
<?php
			}
		}
?>
						</ul>
					</li>
<?php 
	}
?>
					<!-- 
					<li class="treeview">
						<a href="#" title="">
							<i class="fa fa-archive"></i> <span>Arsip</span> <i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class="treeview-menu menu-open" style="display: block;">
							<li><a href="<?php //echo site_url('arsip/external/incoming'); ?>" title=""><i class="fa fa-download"></i> Eksternal Masuk </a></li>
							<li><a href="<?php //echo site_url('arsip/external/outgoing'); ?>" title=""><i class="fa fa-upload"></i> Eksternal Keluar </a></li>
							<li><a href="<?php //echo site_url('arsip/disposisi'); ?>" title=""><i class="fa fa-warning"></i> Disposisi </a></li>
							<li><a href="<?php //echo site_url('arsip/internal'); ?>" title=""><i class="fa fa-retweet"></i> Internal </a></li>
						</ul>
					</li> 
				    -->
				</ul>
				
			</section>
			<!-- /.sidebar -->
		</aside>