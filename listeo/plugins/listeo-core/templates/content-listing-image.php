<?php 	

if(isset($data) && isset($data->size)){
	$size = $data->size;
} else {
	$size = 'listeo-listing-grid';

}

if(has_post_thumbnail()){ 
	the_post_thumbnail($size); 
} else { 
	
	$gallery = (array) get_post_meta( $id, '_gallery', true );

	$ids = array_keys($gallery);
	if(!empty($ids[0]) && $ids[0] !== 0){ 
		$image_url = wp_get_attachment_image_url($ids[0],$size); 
	} else {
		$image_url = get_listeo_core_placeholder_image();
		
	}

	if(empty($image_url)){
	

	// check if this post is assigned to a category, if it is, take first category and see if it has Category Image, if it doesn't check next one 
	$terms = get_the_terms($id, 'listing_category');
	if ($terms && !is_wp_error($terms)) {
		foreach ($terms as $term) {
			$term_meta = get_term_meta($term->term_id);
			if (isset($term_meta['_cover']) && !empty($term_meta['_cover'][0])) {
				$image_url = wp_get_attachment_image_url($term_meta['_cover'][0], $size);
				break;
			}
		}
		}
	}

	$image_url = apply_filters('listeo_category_image_fallback', $image_url);
	?>
	<img src="<?php echo esc_attr($image_url); ?>" alt="">
<?php
}