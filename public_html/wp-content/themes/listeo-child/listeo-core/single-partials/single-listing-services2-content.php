<?php
// File: wp-content/themes/listeo-child/listeo-core/single-partials/single-listing-services2-content.php

$template_loader = new Listeo_Core_Template_Loader;
$gallery_style = get_post_meta($post->ID, '_gallery_style', true);

if($gallery_style == 'content') {
    $template_loader->get_template_part('single-partials/single-listing', 'gallery'); 
}
?>

<!-- Listing Nav -->
<div id="listing-nav" class="listing-nav-container">
    <ul class="listing-nav">
        <li><a href="#listing-overview" class="active"><?php esc_html_e('Overview', 'listeo_core'); ?></a></li>
        <?php if(get_the_content() != '') : ?>
            <li><a href="#listing-description"><?php esc_html_e('Description', 'listeo_core'); ?></a></li>
        <?php endif; ?>
        
        <?php if(has_term('', 'service2_features')): ?>
            <li><a href="#listing-features"><?php esc_html_e('Features', 'listeo_core'); ?></a></li>
        <?php endif; ?>
        
        <?php if(!empty(get_post_meta($post->ID, '_service_cities', true))): ?>
            <li><a href="#listing-service-areas"><?php esc_html_e('Service Areas', 'listeo_core'); ?></a></li>
        <?php endif; ?>
        
        <?php if(listeo_check_if_review_allowed() || comments_open()) : ?>
            <li><a href="#listing-reviews"><?php esc_html_e('Reviews', 'listeo_core'); ?></a></li>
        <?php endif; ?>
    </ul>
</div>

<!-- Overview -->
<div id="listing-overview" class="listing-section">
    <?php the_content(); ?>
    
    <!-- Service Details -->
    <div class="service-details margin-top-30">
        <?php 
        $price_min = get_post_meta($post->ID, '_price_min', true);
        $price_max = get_post_meta($post->ID, '_price_max', true);
        if($price_min || $price_max) : ?>
            <div class="service-pricing">
                <h3><?php esc_html_e('Pricing:', 'listeo_core'); ?></h3>
                <?php if($price_min) : ?>
                    <span class="price-min"><?php echo esc_html(listeo_get_price($price_min)); ?></span>
                <?php endif; ?>
                <?php if($price_max) : ?>
                    <span class="price-separator"> - </span>
                    <span class="price-max"><?php echo esc_html(listeo_get_price($price_max)); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Features -->
<?php if(has_term('', 'service2_features')): ?>
    <div id="listing-features" class="listing-section">
        <h3 class="listing-desc-headline"><?php esc_html_e('Service Features', 'listeo_core'); ?></h3>
        <ul class="service-features">
            <?php
            $terms = get_the_terms($post->ID, 'service2_features');
            foreach($terms as $term) : ?>
                <li><?php echo esc_html($term->name); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Service Areas -->
<?php 
$service_cities = get_post_meta($post->ID, '_service_cities', true);
if(!empty($service_cities)): ?>
    <div id="listing-service-areas" class="listing-section">
        <h3 class="listing-desc-headline"><?php esc_html_e('Service Areas', 'listeo_core'); ?></h3>
        <div class="service-areas-list">
            <?php
            $cities = explode("\n", $service_cities);
            foreach($cities as $city) {
                $city_data = explode(':', trim($city));
                if(count($city_data) == 2) {
                    echo '<div class="service-area-item">';
                    echo '<span class="city">'.esc_html($city_data[0]).'</span>';
                    echo '<span class="radius">'.esc_html($city_data[1]).' km</span>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
<?php endif; ?>