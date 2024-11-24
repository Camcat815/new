<?php
/**
 * Content partial for Services2
 */

$template_loader = new Listeo_Core_Template_Loader;
$gallery_style = get_post_meta($post->ID, '_gallery_style', true);
?>

<!-- Titlebar -->
<div id="titlebar" class="listing-titlebar">
    <div class="listing-titlebar-title">
        <h2><?php the_title(); ?>
            <?php if(get_the_listing_price_range()) : ?>
                <span class="listing-tag">
                    <?php echo get_the_listing_price_range(); ?>
                </span>
            <?php endif; ?>
        </h2>
        
        <!-- Categories -->
        <?php 
        $terms = get_the_terms(get_the_ID(), 'service2_category');
        if($terms) : ?>
            <span>
                <?php 
                foreach($terms as $term) {
                    echo '<a href="'.esc_url(get_term_link($term)).'">'.esc_html($term->name).'</a>';
                    if(end($terms) !== $term) echo ', ';
                }
                ?>
            </span>
        <?php endif; ?>
    </div>
</div>

<!-- Content -->
<div id="listing-overview" class="listing-section">
    <?php if($gallery_style == 'content') {
        $template_loader->get_template_part('single-partials/single-listing', 'gallery');
    } ?>

    <div class="listing-description">
        <?php the_content(); ?>
    </div>

    <?php if(get_post_meta($post->ID, '_price_min', true) || get_post_meta($post->ID, '_price_max', true)) : ?>
        <div class="listing-price-range">
            <h4><?php esc_html_e('Price Range', 'listeo_core'); ?></h4>
            <?php if(get_post_meta($post->ID, '_price_min', true)) : ?>
                <span class="min-price">
                    <?php esc_html_e('From', 'listeo_core'); ?>: 
                    <?php echo listeo_get_price(get_post_meta($post->ID, '_price_min', true)); ?>
                </span>
            <?php endif; ?>
            
            <?php if(get_post_meta($post->ID, '_price_max', true)) : ?>
                <span class="max-price">
                    <?php esc_html_e('To', 'listeo_core'); ?>: 
                    <?php echo listeo_get_price(get_post_meta($post->ID, '_price_max', true)); ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Features -->
<?php 
$terms = get_the_terms(get_the_ID(), 'service2_features');
if($terms) : ?>
    <div id="listing-features" class="listing-section">
        <h3 class="listing-desc-headline"><?php esc_html_e('Service Features', 'listeo_core'); ?></h3>
        <ul class="listing-features">
            <?php foreach($terms as $term) : ?>
                <li><?php echo esc_html($term->name); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Service Areas -->
<?php 
$service_cities = get_post_meta($post->ID, '_service_cities', true);
if(!empty($service_cities)) : ?>
    <div id="listing-service-areas" class="listing-section">
        <h3 class="listing-desc-headline"><?php esc_html_e('Service Areas', 'listeo_core'); ?></h3>
        <div class="service-areas">
            <?php
            $cities = explode("\n", $service_cities);
            foreach($cities as $city) {
                $city_data = explode(':', trim($city));
                if(count($city_data) == 2) {
                    echo '<div class="service-area">';
                    echo '<span class="area-name">'.esc_html($city_data[0]).'</span>';
                    echo '<span class="area-radius">'.esc_html($city_data[1]).' km</span>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
<?php endif; ?>

<!-- Video -->
<?php
$video = get_post_meta($post->ID, '_video', true);
if(!empty($video)) : ?>
    <div id="listing-video" class="listing-section">
        <h3 class="listing-desc-headline"><?php esc_html_e('Video', 'listeo_core'); ?></h3>
        <div class="listing-video">
            <?php echo wp_oembed_get($video); ?>
        </div>
    </div>
<?php endif; ?>