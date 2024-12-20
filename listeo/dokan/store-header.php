<?php
$author = get_query_var('author');
if (empty($author)) {
    global $post;
    $authordata = get_userdata($post->post_author);
    $author = $authordata->ID;
}

$store_user               = dokan()->vendor->get($author);
$header_background        = $store_user->get_banner();
$store_info               = $store_user->get_shop_info();
$social_info              = $store_user->get_social_profiles();
$store_tabs               = dokan_get_store_tabs($store_user->get_id());
$social_fields            = dokan_get_social_profile_fields();
$store_url                = $store_user->get_shop_url();
$dokan_appearance         = get_option('dokan_appearance');
$profile_layout           = empty($dokan_appearance['store_header_template']) ? 'default' : $dokan_appearance['store_header_template'];
$store_address            = dokan_get_seller_short_address($store_user->get_id(), false);

$dokan_store_time_enabled = isset($store_info['dokan_store_time_enabled']) ? $store_info['dokan_store_time_enabled'] : '';
$store_open_notice        = isset($store_info['dokan_store_open_notice']) && !empty($store_info['dokan_store_open_notice']) ? $store_info['dokan_store_open_notice'] : __('Store Open', 'dokan-lite');
$store_closed_notice      = isset($store_info['dokan_store_close_notice']) && !empty($store_info['dokan_store_close_notice']) ? $store_info['dokan_store_close_notice'] : __('Store Closed', 'dokan-lite');
$show_store_open_close    = dokan_get_option('store_open_close', 'dokan_appearance', 'on');

$general_settings         = get_option('dokan_general', []);
$banner_width             = dokan_get_vendor_store_banner_width();



$parallax             = $store_user->get_banner();
$parallax_color       = get_option('listeo_shop_header_color');
$parallax_opacity     = get_option('listeo_shop_header_bg_opacity');


$parallax_output      = '';
$parallax_output .= (!empty($parallax)) ? ' data-background="' . esc_url($parallax) . '" ' : '';
$parallax_output .= (!empty($parallax_color)) ? ' data-color="' . esc_attr($parallax_color) . '" ' : '';
$parallax_output .= (!empty($parallax_opacity)) ? ' data-color-opacity="' . esc_attr($parallax_opacity) . '" ' : '';


if (('default' === $profile_layout) || ('layout2' === $profile_layout)) {
    $profile_img_class = 'profile-img-circle';
} else {
    $profile_img_class = 'profile-img-square';
}
?>





<div class="profile-info-box">
    <?php if ($header_background) { ?>
        <div class="profile-info-photo-bg"><img src="<?php echo $store_user->get_banner(); ?>"></div>
    <?php } ?>
    <?php if ($show_store_open_close == 'on' && $dokan_store_time_enabled == 'yes') : ?>
        <div class="dokan-store-open-close   <?php if (dokan_is_store_open($store_user->get_id())) { ?> dokan-store-is-open-status <?php } else { ?>dokan-store-is-closed-status<?php } ?>">
            <i class="fa fa-shopping-cart"></i>
            <?php if (dokan_is_store_open($store_user->get_id())) {
                echo esc_attr($store_open_notice);
            } else {
                echo esc_attr($store_closed_notice);
            } ?>
        </div>
    <?php endif ?>
    <div class="profile-info-summery-wrapper dokan-clearfix">
        <div class="profile-info-summery">
            <div class="profile-info-head">
                <div class="profile-img <?php echo esc_attr($profile_img_class); ?>">
                    <a href="<?php echo esc_url($store_url) ?>"><img src="<?php echo esc_url($store_user->get_avatar()) ?>" alt="<?php echo esc_attr($store_user->get_shop_name()) ?>" size="150"></a>
                </div>

            </div>

            <div class="profile-info">
                <?php if (!empty($store_user->get_shop_name())) { ?>
                    <h1 class="store-name"><a href="<?php echo esc_url($store_url) ?>"><?php echo esc_html($store_user->get_shop_name()); ?></a></h1>
                <?php } ?>

                <?php

                ?>

                <ul class="dokan-store-info">
                    <?php if (!dokan_is_vendor_info_hidden('address') && isset($store_address) && !empty($store_address)) { ?>
                        <li class="dokan-store-address"><i class="fa fa-map-marker"></i>
                            <?php echo wp_kses_post($store_address); ?>
                        </li>
                    <?php } ?>

                    <?php if (!dokan_is_vendor_info_hidden('phone') && !empty($store_user->get_phone())) { ?>
                        <li class="dokan-store-phone">
                            <i class="fa fa-mobile"></i>
                            <a href="tel:<?php echo esc_html($store_user->get_phone()); ?>"><?php echo esc_html($store_user->get_phone()); ?></a>
                        </li>
                    <?php } ?>

                    <?php if (!dokan_is_vendor_info_hidden('email') && $store_user->show_email() == 'yes') { ?>
                        <li class="dokan-store-email">
                            <i class="fa fa-envelope-o"></i>
                            <a href="mailto:<?php echo esc_attr(antispambot($store_user->get_email())); ?>"><?php echo esc_attr(antispambot($store_user->get_email())); ?></a>
                        </li>
                    <?php } ?>

                    <?php $rating = dokan_get_readable_seller_rating($store_user->get_id()); ?>

                    <li class="dokan-store-rating <?php if (!strpos($rating, 'seller-rating') == '<') {
                                                        echo "no-reviews-rating";
                                                    } ?>">
                        <i class="fa fa-star"></i>
                        <?php echo wp_kses_post($rating); ?>
                    </li>



                    <?php do_action('dokan_store_header_info_fields',  $store_user->get_id()); ?>
                </ul>

                <?php
                $show_socials = false;
                foreach ($social_fields as $key => $field) {
                    if (!empty($social_info[$key])) {
                        $show_socials = true;
                    }
                } ?>

                <?php
                if ($social_fields && $show_socials) { ?>
                    <div class="store-social-wrapper">
                        <ul class="store-social">
                            <?php foreach ($social_fields as $key => $field) {

                            ?>
                                <?php if (!empty($social_info[$key])) { ?>
                                    <li>
                                        <a href="<?php echo esc_url($social_info[$key]); ?>" target="_blank">
                                            <?php if ($field['icon'] == 'fa-brands fa-square-x-twitter') { ?>

                                                <i class="fa-brands fa-x-twitter"></i>
                                            <?php } else { ?>
                                                <i class="fa fa-<?php echo esc_attr($field['icon']); ?>"></i>
                                            <?php } ?>

                                        </a>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div> <!-- .profile-info -->
        </div><!-- .profile-info-summery -->

    </div><!-- .profile-info-summery-wrapper -->

    <a class="button vendors-listings-link" href="<?php echo esc_url(get_author_posts_url($author)); ?>"><?php echo esc_html_e("Vendor's Listings", "listeo") ?></a>

</div> <!-- .profile-info-box -->





<?php if ($store_tabs) { ?>
    <div class="dokan-store-tabs ">
        <ul class="dokan-list-inline">
            <?php foreach ($store_tabs as $key => $tab) { ?>
                <?php if ($tab['url']) : ?>
                    <li><a href="<?php echo esc_url($tab['url']); ?>"><?php echo esc_html($tab['title']); ?></a></li>
                <?php endif; ?>
            <?php } ?>
            <?php do_action('dokan_after_store_tabs', $store_user->get_id()); ?>
        </ul>
    </div>
<?php } ?>
</div>