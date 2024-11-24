<?php
/**
 * Sidebar partial for Services2
 */
?>

<!-- Widget -->
<div class="sidebar-widget">
    <div class="contact-widget-avatar">
        <?php
        $author_id = get_the_author_meta('ID');
        echo get_avatar($author_id, 100);
        ?>
        <div class="verified-badge"><i class="sl sl-icon-check"></i></div>
    </div>

    <div class="contact-widget-content">
        <div class="contact-name">
            <?php 
            the_author();
            $is_verified = get_the_author_meta('_is_verified');
            if($is_verified) {
                echo '<span class="verified-badge-small" title="' . esc_attr__('Verified Account', 'listeo_core') . '"></span>';
            }
            ?>
        </div>
        <?php if(get_the_author_meta('description')) : ?>
            <div class="listing-description">
                <?php echo get_the_author_meta('description'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contact Details -->
    <div class="contact-details">
        <?php
        $phone = get_post_meta(get_the_ID(), '_phone', true);
        $email = get_post_meta(get_the_ID(), '_email', true);
        $website = get_post_meta(get_the_ID(), '_website', true);
        
        if($phone) : ?>
            <div class="contact-item">
                <i class="sl sl-icon-phone"></i>
                <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
            </div>
        <?php endif;
        
        if($email) : ?>
            <div class="contact-item">
                <i class="sl sl-icon-envelope-open"></i>
                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            </div>
        <?php endif;
        
        if($website) : ?>
            <div class="contact-item">
                <i class="sl sl-icon-globe"></i>
                <a href="<?php echo esc_url($website); ?>" target="_blank"><?php echo esc_html($website); ?></a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Social Links -->
    <?php
    $social_links = array(
        'facebook' => array('icon' => 'fa fa-facebook', 'meta' => '_facebook'),
        'instagram' => array('icon' => 'fa fa-instagram', 'meta' => '_instagram'),
        'twitter' => array('icon' => 'fa fa-twitter', 'meta' => '_twitter'),
        'youtube' => array('icon' => 'fa fa-youtube', 'meta' => '_youtube'),
        'tiktok' => array('icon' => 'fa fa-tiktok', 'meta' => '_tiktok')
    );
    
    $has_social = false;
    foreach($social_links as $link) {
        if(get_post_meta(get_the_ID(), $link['meta'], true)) {
            $has_social = true;
            break;
        }
    }
    
    if($has_social) : ?>
        <div class="social-links">
            <?php
            foreach($social_links as $network => $data) {
                $url = get_post_meta(get_the_ID(), $data['meta'], true);
                if($url) {
                    echo '<a href="'.esc_url($url).'" class="'.esc_attr($network).'" target="_blank"><i class="'.esc_attr($data['icon']).'"></i></a>';
                }
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<!-- Location Widget -->
<?php
$location = get_post_meta(get_the_ID(), '_address', true);
if($location) : ?>
    <div class="sidebar-widget">
        <h3><?php esc_html_e('Location', 'listeo_core'); ?></h3>
        <div id="singleListingMap-container">
            <div id="singleListingMap" data-latitude="<?php echo get_post_meta(get_the_ID(), '_geolocation_lat', true); ?>" data-longitude="<?php echo get_post_meta(get_the_ID(), '_geolocation_long', true); ?>" data-map-icon="im im-icon-Map2"></div>
            <a href="#" id="streetView"><?php esc_html_e('Street View', 'listeo_core'); ?></a>
        </div>
    </div>
<?php endif; ?>

<!-- Opening Hours Widget -->
<?php if(get_post_meta(get_the_ID(), '_opening_hours_status', true)) : ?>
    <div class="sidebar-widget opening-hours">
        <h3><?php esc_html_e('Opening Hours', 'listeo_core'); ?></h3>
        <?php
        $template_loader = new Listeo_Core_Template_Loader;
        $template_loader->get_template_part('single-partials/single-listing-opening-hours');
        ?>
    </div>
<?php endif; ?>