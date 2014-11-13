<?php
global $wp_widget_factory;
$layouts = apply_filters( 'siteorigin_panels_prebuilt_layouts', array() );
?>

<div id="panels" data-animations="<?php echo siteorigin_panels_setting('animations') ? 'true' : 'false' ?>">
	<?php do_action('siteorigin_panels_before_interface') ?>

	<div id="panels-container"></div>
	
	<div id="add-to-panels">
		<button class="panels-add" data-tooltip="Add Widget"><div class="dashicons dashicons-plus"></div></button>
		<button class="grid-add" data-tooltip="Add Row"><div class="dashicons dashicons-editor-insertmore"></div></button>
		<?php if(!empty($layouts)) : ?>
			<button class="prebuilt-set" data-tooltip="Prebuilt Layouts"><div class="dashicons dashicons-welcome-widgets-menus"></div></button>
		<?php endif; ?>
		<a href="#" class="switch-to-standard">Switch to Editor</a>
		<div class="clear"></div>
	</div>
	
	<div id="panels-dialog" data-title="Add New Module" class="panels-admin-dialog">
		<div id="panels-dialog-inner">
			<div class="panels-text-filter">
				<input type="search" class="widefat" placeholder="Filter" id="panels-text-filter-input" />
			</div>
			<ul class="panel-type-list">
				<?php foreach($wp_widget_factory->widgets as $class => $widget_obj) : ?>
					<li class="panel-type"
						data-class="<?= esc_attr($class) ?>"
						data-title="<?= esc_attr($widget_obj->name) ?>"
						>
						<div class="panel-type-wrapper">
							<h3><?= esc_html($widget_obj->name) ?></h3>
							<?php if(!empty($widget_obj->widget_options['description'])) : ?>
								<small class="description"><?= esc_html($widget_obj->widget_options['description']) ?></small>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
				<div class="clear"></div>
			</ul>
			<?php do_action('siteorigin_panels_after_widgets'); ?>
		</div>
	</div>

<!-- 	The add row dialog  -->
	<div id="grid-add-dialog" data-title="Add Row" class="panels-admin-dialog">
		<p><label><strong>Columns</strong></label></p>
		<p><input type="text" id="grid-add-dialog-input" name="column_count" class="small-text" value="3" /></p>
	</div>

<!--  The prebuilt layouts dialog  -->
	<?php if(!empty($layouts)) : ?>
		<div id="grid-prebuilt-dialog" data-title="Insert Prebuilt Page Layout" class="panels-admin-dialog">
			<p><label><strong>Page Layout</strong></label></p>
			<p>
				<select type="text" id="grid-prebuilt-input" name="prebuilt_layout" style="width:568px;" placeholder="Select Layout" >
					<option class="empty" <?php selected(true) ?> value=""></option>
					<?php foreach($layouts as $id => $data) : ?>
						<option id="panel-prebuilt-<?= esc_attr($id) ?>" data-layout-id="<?= esc_attr($id) ?>" class="prebuilt-layout">
							<?= isset($data['name']) ? $data['name'] : 'Untitled Layout' ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
	<?php endif; ?>

<!-- The styles dialog  -->
	<div id="grid-styles-dialog" data-title="Row Visual Style" class="panels-admin-dialog">
		<?php siteorigin_panels_style_dialog_form() ?>
	</div>

	<?php wp_nonce_field('save', '_sopanels_nonce') ?>
	<?php do_action('siteorigin_panels_metabox_end'); ?>
</div>