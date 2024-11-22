<?php 
add_action( 'wp_enqueue_scripts', 'listeo_enqueue_styles' );
function listeo_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css',array('bootstrap','font-awesome-5','font-awesome-5-shims','simple-line-icons','listeo-woocommerce') );
}
 
function remove_parent_theme_features() {
   	
}
add_action( 'after_setup_theme', 'remove_parent_theme_features', 10 );

// Ładowanie plików wyszukiwarki
require_once get_stylesheet_directory() . '/includes/class-listeo-custom-search.php';

// Inicjalizacja wyszukiwarki
new Listeo_Custom_Search();

// Dodanie pól obszarów działania
add_action('listeo_core_meta_boxes_init', 'add_service_areas_fields_native');

function add_service_areas_fields_native() {
    $meta_boxes = array(
        'title' => esc_html__('Dodatkowe obszary działania', 'listeo'),
        'pages' => array('listing'),
        'context' => 'normal',
        'priority' => 'low',
        'fields' => array(
            array(
                'id' => '_service_cities',
                'name' => esc_html__('Miasta z promieniem działania', 'listeo'),
                'type' => 'textarea',
                'desc' => esc_html__('Wpisz miasta i ich promienie w formacie: "Miasto:promień", każde w nowej linii, np. "Gdańsk:20"', 'listeo'),
            ),
            array(
                'id' => '_excluded_areas',
                'name' => esc_html__('Obszary wykluczone', 'listeo'),
                'type' => 'textarea',
                'desc' => esc_html__('Wpisz wykluczone obszary i ich promienie w formacie: "Miasto:promień", każde w nowej linii, np. "Sopot:5"', 'listeo'),
            )
        )
    );

    return listeo_core_meta_boxes_add($meta_boxes);
}


// Shortcodes dla social media
function check_facebook_link_shortcode() {
    $facebook_link = get_post_meta(get_the_ID(), '_facebook', true);
    if ($facebook_link) {
        return '<a href="' . esc_url($facebook_link) . '" target="_blank" class="social-icon facebook-icon"><i class="fab fa-facebook"></i></a>';
    }
    return '';
}
add_shortcode('facebook_link', 'check_facebook_link_shortcode');

function check_instagram_link_shortcode() {
    $instagram_link = get_post_meta(get_the_ID(), '_instagram', true);
    if ($instagram_link) {
        return '<a href="' . esc_url($instagram_link) . '" target="_blank" class="social-icon instagram-icon"><i class="fab fa-instagram"></i></a>';
    }
    return '';
}
add_shortcode('instagram_link', 'check_instagram_link_shortcode');

function check_tiktok_link_shortcode() {
    $tiktok_link = get_post_meta(get_the_ID(), '_tiktok', true);
    if ($tiktok_link) {
        return '<a href="' . esc_url($tiktok_link) . '" target="_blank" class="social-icon tiktok-icon"><i class="fab fa-tiktok"></i></a>';
    }
    return '';
}
add_shortcode('tiktok_link', 'check_tiktok_link_shortcode');

function check_youtube_link_shortcode() {
    $youtube_link = get_post_meta(get_the_ID(), '_youtube', true);
    if ($youtube_link) {
        return '<a href="' . esc_url($youtube_link) . '" target="_blank" class="social-icon youtube-icon"><i class="fab fa-youtube"></i></a>';
    }
    return '';
}
add_shortcode('youtube_link', 'check_youtube_link_shortcode');

function check_x_link_shortcode() {
    $x_link = get_post_meta(get_the_ID(), '_x', true);
    if ($x_link) {
        return '<a href="' . esc_url($x_link) . '" target="_blank" class="social-icon x-icon"><i class="fab fa-x-twitter"></i></a>';
    }
    return '';
}
add_shortcode('x_link', 'check_x_link_shortcode');

// Shortcodes dla danych kontaktowych
function email_link_shortcode($atts) {
    $email = get_post_meta(get_the_ID(), '_email', true);
    if ($email) {
        return '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
    }
    return '';
}
add_shortcode('email_link', 'email_link_shortcode');

function phone_link_shortcode($atts) {
    $phone = get_post_meta(get_the_ID(), '_phone', true);
    if ($phone) {
        return '<a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a>';
    }
    return '';
}
add_shortcode('phone_link', 'phone_link_shortcode');

function website_link_shortcode($atts) {
    $website = get_post_meta(get_the_ID(), '_website', true);
    if ($website) {
        $display_url = preg_replace('/^https?:\/\//', '', $website);
        return '<a href="' . esc_url($website) . '" target="_blank">' . esc_html($display_url) . '</a>';
    }
    return '';
}
add_shortcode('website_link', 'website_link_shortcode');

// Zamykanie popupu
add_action('wp_footer', 'dodaj_skrypt_zamykania_popup');

function dodaj_skrypt_zamykania_popup() {
    ?>
    <script>
    jQuery(document).ready(function($){
        $(document).on('click', '.zamknij-popup', function(e){
            e.preventDefault();
            e.stopPropagation();
            if (typeof elementorProFrontend !== 'undefined') {
                elementorProFrontend.modules.popup.closePopup({}, e);
            }
            return false;
        });
    });
    </script>
    <?php
}