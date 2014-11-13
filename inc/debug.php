<?php
/**  If we're in debug mode, display the panels data. */
function siteorigin_panels_dump(){
	echo "<!--\n\n";
	echo "// Builder Data dump\n\n";
		global $post;
		var_export( get_post_meta($post->ID, 'panels_data', true));
	echo "\n\n-->";
}
add_action('siteorigin_panels_metabox_end', 'siteorigin_panels_dump');