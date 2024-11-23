<?php
// File: wp-content/themes/listeo-child/listeo-core/single-partials/single-listing-services2-sidebar.php

$template_loader = new Listeo_Core_Template_Loader;
?>

<!-- Contact Details -->
<div class="boxed-widget margin-top-35">
    <div class="hosted-by-title">
        <h4><i class="sl sl-icon-phone"></i> <?php esc_html_e('Contact Details', 'listeo_core'); ?></h4>
    </div>
    <ul class="listing-details-sidebar">
        <?php
        $phone = get_post_meta($post->ID, '_phone', true);
        if($phone) : ?>
            <li>
                <i class="sl sl-icon-phone"></i> 
                <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
            </li>
        <?php endif; ?>
        
        <?php
        $email = get_post_meta($post->ID, '_email', true);
        if($email) : ?>
            <li>
                <i class="sl sl-icon-envelope-open"></i>
                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            </li>
        <?php endif; ?>
        
        <?php
        $website = get_post_meta($post->ID, '_website', true);
        if($website) : ?>
            <li>
                <i class="sl sl-icon-globe"></i>
                <a href="<?php echo esc_url($website); ?>" target="_blank"><?php echo esc_html($website); ?></a>
            </li>
        <?php endif; ?>
    </ul>

    <?php
    // Social Media Links
    $facebook = get_post_meta($post->ID, '_facebook', true);
    $instagram = get_post_meta($post->ID, '_instagram', true);
    $twitter = get_post_meta($post->ID, '_twitter', true);
    $youtube = get_post_meta($post->ID, '_youtube', true);
    $tiktok = get_post_meta($post->ID, '_tiktok', true);

    if($facebook || $instagram || $twitter || $youtube || $tiktok) : ?>
        <div class="social-contact">
            <h4><?php esc_html_e('Social Media', 'listeo_core'); ?></h4>
            <ul class="social-links">
                <?php if($facebook) : ?>
                    <li><a href="<?php echo esc_url($facebook); ?>" target="_blank" class="facebook"><i class="fab fa-facebook"></i></a></li>
                <?php endif; ?>
                
                <?php if($instagram) : ?>
                    <li><a href="<?php echo esc_url($instagram); ?>" target="_blank" class="instagram"><i class="fab fa-instagram"></i></a></li>
                <?php endif; ?>
                
                <?php if($twitter) : ?>
                    <li><a href="<?php echo esc_url($twitter); ?>" target="_blank" class="twitter"><i class="fab fa-twitter"></i></a></li>
                <?php endif; ?>
                
                <?php if($youtube) : ?>
                    <li><a href="<?php echo esc_url($youtube); ?>" target="_blank" class="youtube"><i class="fab fa-youtube"></i></a></li>
                <?php endif; ?>
                
                <?php if($tiktok) : ?>
                    <li><a href="<?php echo esc_url($tiktok); ?>" target="_blank" class="tiktok"><i class="fab fa-tiktok"></i></a></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<!-- Opening Hours -->
<?php 
$_opening_hours_status = get_post_meta($post->ID, '_opening_hours_status', true);
if(!empty($_opening_hours_status)) {
    $template_loader->get_template_part('single-partials/single-listing','opening-hours'); 
}
?>

<!-- Location -->
<?php
$latitude = get_post_meta($post->ID, '_geolocation_lat', true);
$longitude = get_post_meta($post->ID, '_geolocation_long', true);
$address = get_post_meta($post->ID, '_address', true);

if((!empty($latitude) && !empty($longitude)) || !empty($address)) : ?>
    <div class="boxed-widget margin-top-35">
        <div class="hosted-by-title">
            <h4><i class="sl sl-icon-location"></i> <?php esc_html_e('Location', 'listeo_core'); ?></h4>
        </div>
        <?php if(!empty($latitude) && !empty($longitude)) : ?>
            <div id="singleListingMap" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>" data-map-icon="im im-icon-Hamburger"></div>
        <?php endif; ?>
        <?php if(!empty($address)) : ?>
            <div class="address-box">
                <p><?php echo esc_html($address); ?></p>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>