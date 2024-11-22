<!-- Features -->
<?php

$taxonomies = get_option('listeo_single_taxonomies_checkbox_list', array('listing_feature'));

if (empty($taxonomies)) {
	return;
}
foreach ($taxonomies as $tax) {
	$term_list = get_the_terms($post->ID, $tax);
	$tax_obj = get_taxonomy($tax);
	$taxonomy = get_taxonomy_labels($tax_obj);


	if (!empty($term_list)) { ?>
		<h3 class="listing-desc-headline"><?php echo $taxonomy->name; ?></h3>

		<ul class="listing-features checkboxes margin-top-0">
	<?php
		foreach ($term_list as $term) {
			echo '';
			$term_link = get_term_link($term);
			if (is_wp_error($term_link))
				continue;
			$t_id = $term->term_id;
			if (isset($t_id)) {
				$_icon_svg = get_term_meta($t_id, '_icon_svg', true);
				$_icon_svg_image = wp_get_attachment_image_src($_icon_svg, 'medium');
			}
			if (isset($_icon_svg_image) && !empty($_icon_svg_image)) {
				$icon = listeo_render_svg_icon($_icon_svg);
				//$icon = '<img class="listeo-map-svg-icon" src="'.$_icon_svg_image[0].'"/>';


			} else {

				if (empty($icon)) {
					$icon = get_post_meta($post->ID, '_icon', true);
				}	
			}
			if(!empty($icon)){
				echo '<li class="feature-has-icon"><span class="feature-svg-icon">'.$icon.'</span><a href="' . esc_url($term_link) . '">' . $term->name . '</a></li>';
			} else {
				echo '<li class="feature-no-icon"><a href="' . esc_url($term_link) . '">' . $term->name . '</a></li>';
			}
			$icon = false;
			
		}
		?>
		</ul>
		
	<?php }
};

	?>