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
	?>
	<img src="<?php echo esc_attr($image_url); ?>" alt="">
<?php
}