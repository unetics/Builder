<?php
/**
 * Get the settings
 */
function siteorigin_panels_setting($key = ''){

	if( has_action('after_setup_theme') ) {
		// Only use static settings if we've initialized the theme
		static $settings;
	}
	else {
		$settings = false;
	}

	if( empty($settings) ){
		$display_settings = get_option('siteorigin_panels_display', array());

		$settings = get_theme_support('siteorigin-panels');
		if(!empty($settings)) $settings = $settings[0];
		else $settings = array();

		$settings = wp_parse_args( $settings, array(
			'home-page' => false,																								// Is the home page supported
			'home-page-default' => false,																						// What's the default layout for the home page?
			'home-template' => 'home-panels.php',																				// The file used to render a home page.
			'post-types' => get_option('siteorigin_panels_post_types', array('page', 'post')),									// Post types that can be edited.
			'responsive' => !isset( $display_settings['responsive'] ) ? true : $display_settings['responsive'],				    // Should we use a responsive layout
			'mobile-width' => !isset( $display_settings['mobile-width'] ) ? 780 : $display_settings['mobile-width'],			// What is considered a mobile width?

			'margin-bottom' => !isset( $display_settings['margin-bottom'] ) ? 30 : $display_settings['margin-bottom'],			// Bottom margin of a cell
			'margin-sides' => !isset( $display_settings['margin-sides'] ) ? 30 : $display_settings['margin-sides'],				// Spacing between 2 cells
			'affiliate-id' => false,																							// Set your affiliate ID
			'copy-content' => !isset( $display_settings['copy-content'] ) ? true : $display_settings['copy-content'],			// Should we copy across content
			'animations' => !isset( $display_settings['animations'] ) ? true : $display_settings['animations'],					// Do we need animations
			'inline-css' => !isset( $display_settings['inline-css'] ) ? true : $display_settings['inline-css'],				    // How to display CSS
		) );

		// Filter these settings
		$settings = apply_filters('siteorigin_panels_settings', $settings);
		if( empty( $settings['post-types'] ) ) $settings['post-types'] = array();
	}

	if( !empty( $key ) ) return isset( $settings[$key] ) ? $settings[$key] : null;
	return $settings;
}

/**
 * Add the options page
 */
function siteorigin_panels_options_admin_menu() {
	add_options_page( 'siteorigin-panels', 'Page Builder', 'manage_options', 'siteorigin_panels', 'siteorigin_panels_options_page' );
}
add_action( 'admin_menu', 'siteorigin_panels_options_admin_menu' );

/**
 * Display the admin page.
 */
function siteorigin_panels_options_page(){
	include plugin_dir_path(__FILE__) . 'options-page.php';
}

/**
 * Register all the settings fields.
 */
function siteorigin_panels_options_init() {
	register_setting( 'siteorigin-panels', 'siteorigin_panels_post_types', 'siteorigin_panels_options_sanitize_post_types' );
	register_setting( 'siteorigin-panels', 'siteorigin_panels_display', 'siteorigin_panels_options_sanitize_display' );

	add_settings_section( 'general', 'General', '__return_false', 'siteorigin-panels' );
	add_settings_section( 'display', 'Display', '__return_false', 'siteorigin-panels' );

	add_settings_field( 'post-types', 'Post Types', 'siteorigin_panels_options_field_post_types', 'siteorigin-panels', 'general' );
	add_settings_field( 'copy-content','Copy Content to Post Content', 'siteorigin_panels_options_field_display', 'siteorigin-panels', 'general', array( 'type' => 'copy-content' ) );
	add_settings_field( 'animations', 'Animations', 'siteorigin_panels_options_field_display', 'siteorigin-panels', 'general', array(
		'type' => 'animations',
		'description' => 'Disable animations to improve Page Builder interface performance',
	) );

	// The display fields
	add_settings_field( 'post-types', 'Responsive', 'siteorigin_panels_options_field_display', 'siteorigin-panels', 'display', array( 'type' => 'responsive' ) );
	add_settings_field( 'mobile-width', 'Mobile Width', 'siteorigin_panels_options_field_display', 'siteorigin-panels', 'display', array( 'type' => 'mobile-width' ) );
	add_settings_field( 'margin-sides', 'Margin Sides', 'siteorigin_panels_options_field_display', 'siteorigin-panels', 'display', array( 'type' => 'margin-sides' ) );
	add_settings_field( 'margin-bottom', 'Margin Bottom', 'siteorigin_panels_options_field_display', 'siteorigin-panels', 'display', array( 'type' => 'margin-bottom' ) );
	add_settings_field( 'inline-css', 'Inline CSS', 'siteorigin_panels_options_field_display', 'siteorigin-panels', 'display', array(
		'type' => 'inline-css',
		'description' => 'Disabling this will generate CSS using a separate query.',
	));
}
add_action( 'admin_init', 'siteorigin_panels_options_init' );

/**
 * Display the field for selecting the post types
 *
 * @param $args
 */
function siteorigin_panels_options_field_post_types($args){
	$panels_post_types = siteorigin_panels_setting('post-types');

	$all_post_types = get_post_types(array('_builtin' => false));
	$all_post_types = array_merge(array('page' => 'page', 'post' => 'post'), $all_post_types);
	unset($all_post_types['ml-slider']);

	foreach($all_post_types as $type){
		$info = get_post_type_object($type);
		if(empty($info->labels->name)) continue;
		$checked = in_array(
			$type,
			$panels_post_types
		);
		
		?>
		<label>
			<input type="checkbox" name="siteorigin_panels_post_types[<?= esc_attr($type) ?>]" value="<?= esc_attr($type) ?>" <?php checked($checked) ?> />
			<?= esc_html($info->labels->name) ?>
		</label><br/>
		<?php
	}
	
	?><p class="description">Post types that will have the page builder available</p><?php
}

/**
 * Display the fields for the other settings.
 *
 * @param $args
 */
function siteorigin_panels_options_field_display($args){
	$settings = siteorigin_panels_setting();
	switch($args['type']) {
		case 'responsive' :
		case 'copy-content' :
		case 'animations' :
		case 'inline-css' :
		case 'bundled-widgets' :
			?><label><input type="checkbox" name="siteorigin_panels_display[<?= esc_attr($args['type']) ?>]" <?php checked($settings[$args['type']]) ?> /> Enabled</label><?php
			break;
		case 'margin-bottom' :
		case 'margin-sides' :
		case 'mobile-width' :
			?><input type="text" name="siteorigin_panels_display[<?= esc_attr($args['type']) ?>]" value="<?= esc_attr($settings[$args['type']]) ?>" class="small-text" /> px<?php
			break;
	}

	if(!empty($args['description'])) {
		?><p class="description"><?= esc_html($args['description']) ?></p><?php
	}
}

/**
 * Check that we have valid post types
 *
 * @param $types
 * @return array
 */
function siteorigin_panels_options_sanitize_post_types($types){
	if(empty($types)) return array();
	$all_post_types = get_post_types(array('_builtin' => false));
	$all_post_types = array_merge(array('post' => 'post', 'page' => 'page'), $all_post_types);
	foreach($types as $type => $val){
		if(!in_array($type, $all_post_types)) unset($types[$type]);
		else $types[$type] = !empty($types[$type]);
	}
	
	// Only non empty items
	return array_keys(array_filter($types));
}

/**
 * Sanitize the other options fields
 *
 * @param $vals
 * @return mixed
 */
function siteorigin_panels_options_sanitize_display($vals){
	foreach($vals as $f => $v){
		switch($f){
			case 'inline-css' :
			case 'responsive' :
			case 'copy-content' :
			case 'animations' :
			case 'bundled-widgets' :
				$vals[$f] = !empty($vals[$f]);
				break;
			case 'margin-bottom' :
			case 'margin-sides' :
			case 'mobile-width' :
				$vals[$f] = intval($vals[$f]);
				break;
		}
	}
	$vals['responsive'] = !empty($vals['responsive']);
	$vals['copy-content'] = !empty($vals['copy-content']);
	$vals['animations'] = !empty($vals['animations']);
	$vals['inline-css'] = !empty($vals['inline-css']);
	$vals['bundled-widgets'] = !empty($vals['bundled-widgets']);
	return $vals;
}