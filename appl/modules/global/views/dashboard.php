<script type="text/javascript">

</script>

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Dashboard <small><?php echo get_user_data('user_name'); ?></small></h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
		<li class="active"><?php echo get_user_data('user_name'); ?></li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-lg-3 col-xs-6">
<?php
	$m_result = $this->dashboard_model->get_my_surat('SME');
?>
			<!-- small box -->
			<div class="small-box bg-little-red">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-upload hidden-sm hidden-md hidden-lg"></i> <?php echo $m_result->num_rows(); ?></h3>
					<p>Surat Masuk Eksternal</p>
				</div>
				<div class="icon">
					<!-- i class="ion ion-bag"></i-->
					<i class="fa fa-download"></i>
				</div>
				<a href="<?php echo site_url('global/dashboard/workspace/surat_masuk_eksternal'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
<?php 
	if(has_permission(23) || has_permission(1)) {
		$CM_result = $this->dashboard_model->get_my_contract('CM');
?>
			<!-- small box -->
			<div class="small-box bg-olive">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-upload hidden-sm hidden-md hidden-lg"></i> <?php echo $CM_result->num_rows(); ?></h3>
					<p>Contract Maintenance</p>
				</div>
				<div class="icon">
					<i class="fa fa-upload"></i>
				</div>
				<a href="<?php echo site_url('surat/kontrak/kontrak_aktif'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
<?php
	}else { 
		$SKE_result = $this->dashboard_model->get_my_surat('SKE');
?>
			<!-- small box -->
			<div class="small-box bg-olive">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-upload hidden-sm hidden-md hidden-lg"></i> <?php echo $SKE_result->num_rows(); ?></h3>
	
					<p>Surat Keluar Eksternal</p>
				</div>
				<div class="icon">
					<i class="fa fa-upload"></i>
				</div>
				<a href="<?php echo site_url('global/dashboard/workspace/surat_keluar_eksternal'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
<?php
	}
?>
		<div class="col-lg-3 col-xs-6">
<?php 
	$i_count = 0;
	if(get_user_data('unit_id') || has_permission(1)) {
		$i_result = $this->dashboard_model->get_my_surat('SI');
		$i_count = $i_result->num_rows();
	} 
?>
			<!-- small box -->
			<div class="small-box bg-light-blue">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-retweet hidden-sm hidden-md hidden-lg"></i> <?php echo $i_count; ?></h3>
					<p>Nota Dinas</p>
				</div>
				<div class="icon">
					<i class="fa fa-retweet"></i>
				</div>
				<a href="<?php echo site_url('global/dashboard/workspace/surat_internal'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
		
		<div class="col-lg-3 col-xs-6">
<?php 
	$d_count = 0;
	if(get_user_data('unit_id')) {
		$d_result = $this->dashboard_model->get_my_disposisi();
		$d_count = $d_result->num_rows();
	} 
?>
			<!-- small box -->
			<div class="small-box bg-brown-yellow">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-exclamation-triangle hidden-sm hidden-md hidden-lg"></i> <?php echo $d_count; ?></h3>
	
					<p>Disposisi</p>
				</div>
				<div class="icon">
					<i class="fa fa-exclamation-triangle"></i>
				</div>
				<a href="<?php echo site_url('global/dashboard/workspace/disposisi'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
<?php 
	$d_count = 0;
	if(get_user_data('unit_id') || has_permission(1)) {
		$d_result = $this->dashboard_model->get_my_surat('ST');
		$d_count = $d_result->num_rows();
	} 
?>
			<!-- small box -->
			<div class="small-box bg-violet">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-exclamation-triangle hidden-sm hidden-md hidden-lg"></i> <?php echo $d_count; ?></h3>
	
					<p>Surat Perintah</p>
				</div>
				<div class="icon">
					<i class="fa fa-briefcase"></i>
				</div>
				<a href="<?php echo site_url('global/dashboard/workspace/tugas'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
	</div>
	<!-- Default box -->
	
</section><!-- /.content -->