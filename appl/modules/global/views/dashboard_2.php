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
	$m_result = $this->dashboard_model->get_my_surat_eksternal('M');
?>
			<!-- small box -->
			<div class="small-box bg-aqua">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-upload hidden-sm hidden-md hidden-lg"></i> <?php echo $m_result->num_rows(); ?></h3>
	
					<p>Surat Masuk</p>
				</div>
				<div class="icon">
					<!-- i class="ion ion-bag"></i-->
					<i class="fa fa-download"></i>
				</div>
				<a href="<?php echo site_url('global/dashboard/workspace/surat_eksternal_masuk'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
<?php 
	$k_result = $this->dashboard_model->get_my_surat_eksternal('K');
?>
			<!-- small box -->
			<div class="small-box bg-green">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-upload hidden-sm hidden-md hidden-lg"></i> <?php echo $k_result->num_rows(); ?></h3>
	
					<p>Surat Keluar</p>
				</div>
				<div class="icon">
					<i class="fa fa-upload"></i>
				</div>
				<a href="<?php echo site_url('global/dashboard/workspace/surat_eksternal_keluar'); ?>" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
			<!-- small box -->
			<div class="small-box bg-yellow">
				<div class="inner">
					<h3><i class="fa fa-envelope hidden-xs"></i><i class="fa fa-retweet hidden-sm hidden-md hidden-lg"></i> 0</h3>
	
					<p>Surat Internal</p>
				</div>
				<div class="icon">
					<i class="fa fa-retweet"></i>
				</div>
				<a href="#" class="small-box-footer">List <i class="fa fa-arrow-circle-right"></i></a>
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
			<div class="small-box bg-red">
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
	</div>
	<!-- Default box >
	<div class="box box-danger">
		<script type="text/javascript">

		var orgDiagram;
		
		$(window).load(function () {
			var options = new primitives.orgdiagram.Config();

			var buttons = [];
<?php
	if(has_permission(3)) {
?>
			buttons.push(new primitives.orgdiagram.ButtonConfig("add_user", "ui-icon-person", "User"));
<?php		
	}
	if(has_permission(8)) {
?>
			buttons.push(new primitives.orgdiagram.ButtonConfig("surat_masuk", "ui-icon-mail-open", "Surat Masuk"));
<?php
	}
?>	
			options.buttons = buttons;

// 			options.items = items;
// 			options.cursorItem = 0;
//			options.templates = [getContactTemplate(), getVacantTemplate()];
			options.templates = [getStructureTemplate()];
			options.onItemRender = onTemplateRender;
//			options.defaultTemplateName = "contactTemplate";
			options.hasSelectorCheckbox = primitives.common.Enabled.False;
			options.elbowType = primitives.common.ElbowType.Round;
			options.hasButtons = primitives.common.Enabled.Auto;
//			options.minimalVisibility = primitives.common.Visibility.Normal;
			options.onButtonClick = function (e, data) {
				switch(data.name) {
					case 'add_user' :
						location.assign('<?php echo site_url('global/admin/detail_posisi'); ?>/' + data.context.id);
						
					break;
					case 'surat_masuk' :
						location.assign('<?php echo site_url('surat/external/incoming_ins'); ?>/' + data.context.id);

					break;
/* 					case 'swap' :
						if(data.context.manning == '1') {
							if(data.context.empl_id != null) {
								if(data.context.tm_assign_id == null) {
									if($('#swap-0').html() == '') {
										addSwap(0, data.context.pos_id, data.context.empl_id);
									} else if($('#swap-1').html() == '') {
										addSwap(1, data.context.pos_id, data.context.empl_id);
									} else {
										bootbox.alert('Please remove talent from swap area before add new talent.');
									}
								} else {
									bootbox.alert('Cannot swap position, new talent is assign in this position.');
								}
							} else {
								bootbox.alert('Cannot swap vacant position.');
							}
						} else {
							bootbox.alert('Cannot swap position with more than 1 capacity.');
						}
						break;
 */						
				}

			};

			orgDiagram = $("#orgdiagram").orgDiagram(options);
			filterTree();

			function getTreeItem(sourceItem) {
				var result = new primitives.orgdiagram.ItemConfig();
				result.title = sourceItem.title;
				result.description = sourceItem.description;
				result.phone = sourceItem.phone;
				result.email = sourceItem.email;
				result.image = sourceItem.photo;
				result.groupTitle = sourceItem.title;
				result.id = sourceItem.id;
				result.href = "showdetails.php?recordid=" + result.id;
				if (sourceItem.children != null) {
					for (var index = 0; index < sourceItem.children.length; index += 1) {
						result.items.push(getTreeItem(sourceItem.children[index]));
					}
				}
				return result;
			}
			
			function getStructureTemplate() {
				var result = new primitives.orgdiagram.TemplateConfig();
				result.name = "structureTemplate";

				result.itemSize = new primitives.common.Size(220, 120);
				result.minimizedItemSize = new primitives.common.Size(3, 3);
				result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);

				var itemTemplate = jQuery(
				  '<div class="bp-item bp-corner-all bt-item-frame">'
					+ '<div class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 216px; height: 40px;">'
						+ '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 208px; height: 38px; white-space: normal;">'
						+ '</div>'
					+ '</div>'
					+ '<div class="bp-item bp-photo-frame" style="top: 46px; left: 2px; width: 50px; height: 60px;">'
						+ '<img name="photo" style="height:60px; width:50px;" />'
					+ '</div>'
					+ '<div name="nip" class="bp-item" style="top: 44px; left: 56px; width: 162px; height: 18px; font-size: 12px;"></div>'
					+ '<div name="user_name" class="bp-item" style="top: 62px; left: 56px; width: 162px; height: 36px; font-size: 10px;"></div>'
//					+ '<a name="readmore" class="bp-item" style="top: 104px; left: 4px; width: 212px; height: 12px; font-size: 10px; font-family: Arial; text-align: right; font-weight: bold; text-decoration: none; z-index:100;">Read more ...</a>'
				+ '</div>'
				).css({
					width: result.itemSize.width + "px",
					height: result.itemSize.height + "px"
				}).addClass("bp-item bp-corner-all bt-item-frame");
				result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

				return result;
			}

			function onTemplateRender(event, data) {
				var hrefElement = data.element.find("[name=readmore]");
				switch (data.renderingMode) {
					case primitives.common.RenderingMode.Create:
						/* Initialize widgets here */
						hrefElement.click(function (e)
						{
							/* Block mouse click propogation in order to avoid layout updates before server postback*/
							primitives.common.stopPropagation(e);
						});
						break;
					case primitives.common.RenderingMode.Update:
						/* Update widgets here */
						break;
				}

				var itemConfig = data.context;

//				if (data.templateName == "structureTemplate") {
					data.element.find("[name=photo]").attr({ "src": itemConfig.photo });

					var fields = ["title", "nip", "user_name"];
					for (var index = 0; index < fields.length; index += 1) {
						var field = fields[index];

						var element = data.element.find("[name=" + field + "]");
						if (element.text() != itemConfig[field]) {
							element.text(itemConfig[field]);
						}
					}
//				}
				hrefElement.attr({ "href": itemConfig.href });
			}
			
		});

		$(document).ready(function() {
			
		});

		function filterTree() {
//			loadingproses();
//			var dir = $('#select_direktorat').val();
			
			$.ajax({
				type: "POST",
				url: '<?php echo site_url('global/dashboard/ajax_handler')?>',
				data: {action: 'global.dashboard_model.get_structure_tree'},
				dataType: 'text',
				success: function(text) {
					var items = JSON.parse(decodeURIComponent(text));
					orgDiagram.orgDiagram({
						items: items,
						cursorItem: 0
					});
					orgDiagram.orgDiagram("update");
//					loadingproses_close();
				}
			});
		}

		</script>
	
		<div class="box-header with-border">
			<h3 class="box-title">Struktur Organisasi</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
				<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
			</div>
		</div>

		<div class="box-body" id="orgdiagram" style="height: 480px; ">
			
		</div>

	</div><!-- /.box -->

</section><!-- /.content -->