<?php 
// Simple debugging
add_action('init', function() {
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
        error_log('========== Start Debug ==========');
        error_log('Loading functions.php in listeo-child theme');
        
        // Check if manager file exists
        $manager_file = get_stylesheet_directory() . '/includes/class-listeo-types-manager.php';
        error_log('Looking for manager file at: ' . $manager_file);
        error_log('File exists: ' . (file_exists($manager_file) ? 'Yes' : 'No'));
    }
});



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





add_action('init', function() {
    error_log('functions.php: init action started');
    if (class_exists('Listeo_Types_Manager')) {
        error_log('Listeo_Types_Manager class exists');
    } else {
        error_log('Listeo_Types_Manager class NOT found');
    }
}, 0);







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




// Load main manager class
require_once get_stylesheet_directory() . '/includes/class-listeo-types-manager.php';
listeo_types_manager();

// Dodatkowe hooki dla Services2
add_action('init', 'register_services2_features_taxonomy', 0);
function register_services2_features_taxonomy() {
    $labels = array(
        'name'              => _x('Service 2 Features', 'taxonomy general name'),
        'singular_name'     => _x('Service 2 Feature', 'taxonomy singular name'),
        'search_items'      => __('Search Service 2 Features'),
        'all_items'         => __('All Service 2 Features'),
        'parent_item'       => __('Parent Service 2 Feature'),
        'parent_item_colon' => __('Parent Service 2 Feature:'),
        'edit_item'         => __('Edit Service 2 Feature'),
        'update_item'       => __('Update Service 2 Feature'),
        'add_new_item'      => __('Add New Service 2 Feature'),
        'new_item_name'     => __('New Service 2 Feature Name'),
        'menu_name'         => __('Service 2 Features'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'           => $labels,
        'show_ui'          => true,
        'show_admin_column' => true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'service2-feature'),
        'capabilities'     => array(
            'manage_terms' => 'manage_listing_settings',
            'edit_terms'   => 'manage_listing_settings',
            'delete_terms' => 'manage_listing_settings',
            'assign_terms' => 'edit_listings',
        ),
    );

    register_taxonomy('service2_features', array('listing'), $args);
}

// Dodaj Services2 do listy dostępnych typów w formularzu
add_filter('listeo_core_get_listing_types_options', 'add_services2_to_listing_types_options');
function add_services2_to_listing_types_options($options) {
    $options['services2'] = __('Service 2');
    return $options;
}

// Zapewnij, że formularze Services2 mają wszystkie potrzebne pola
add_filter('listeo_core_get_listing_fields', 'ensure_services2_fields', 10, 2);
function ensure_services2_fields($fields, $type) {
    if ($type === 'services2') {
        $fields = array_merge($fields, array(
            '_price_min',
            '_price_max',
            '_gallery',
            '_address',
            '_geolocation',
            '_phone',
            '_email',
            '_website'
        ));
    }
    return $fields;
}




