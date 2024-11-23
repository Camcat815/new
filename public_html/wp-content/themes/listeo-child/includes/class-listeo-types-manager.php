<?php
// File: wp-content/themes/listeo-child/includes/class-listeo-types-manager.php

if (!defined('ABSPATH')) {
    exit;
}

class Listeo_Types_Manager {
    private static $instance = null;
    protected $types = array();
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        // Załączamy definicję nowego typu
        require_once get_stylesheet_directory() . '/includes/listeo-types/class-listeo-type-services2.php';
    }

    private function init_hooks() {
        // Hooki do Listeo Core
        add_filter('listeo_core_listing_types', array($this, 'register_listing_types'));
        add_filter('listeo_core_get_listing_types', array($this, 'add_listing_types'));
        add_filter('listeo_core_get_listing_types_with_taxonomies', array($this, 'add_types_with_taxonomies'));
        
        // Hooki do formularzy
        add_filter('listeo_core_submit_form_templates', array($this, 'add_form_templates'));
        add_filter('listeo_core_get_listing_types_form_fields', array($this, 'add_form_fields'));
        
        // Hooki do meta boxów
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'), 15);
        
        // Hooki do kategorii
        add_action('init', array($this, 'register_taxonomies'), 0);
        
        // Hooki do szablonów
        add_filter('single_template', array($this, 'load_single_template'));
        add_filter('archive_template', array($this, 'load_archive_template'));
    }

    public function register_listing_types($types) {
        $types['services2'] = array(
            'name' => 'Services 2',
            'singular_name' => 'Service 2',
            'taxonomy' => 'service2_category',
            'orderby' => 'date',
            'order' => 'DESC',
            'template' => 'services2',
        );
        return $types;
    }

    public function add_listing_types($types) {
        if (!isset($types['services2'])) {
            $types['services2'] = array(
                'post_type' => 'listing',
                'taxonomy' => 'service2_category',
                'labels' => array(
                    'name' => 'Services 2',
                    'singular_name' => 'Service 2'
                )
            );
        }
        return $types;
    }

    public function add_types_with_taxonomies($types) {
        $types['services2'] = array(
            'taxonomy' => 'service2_category',
            'labels' => array(
                'name' => 'Services 2',
                'singular_name' => 'Service 2'
            )
        );
        return $types;
    }

    public function register_taxonomies() {
        $labels = array(
            'name' => 'Services 2 Categories',
            'singular_name' => 'Service 2 Category',
            'menu_name' => 'Services 2',
            'all_items' => 'All Services 2',
            'parent_item' => 'Parent Service 2',
            'parent_item_colon' => 'Parent Service 2:',
            'new_item_name' => 'New Service 2 Name',
            'add_new_item' => 'Add New Service 2',
            'edit_item' => 'Edit Service 2',
            'update_item' => 'Update Service 2',
            'view_item' => 'View Service 2',
            'separate_items_with_commas' => 'Separate services with commas',
            'add_or_remove_items' => 'Add or remove services',
            'choose_from_most_used' => 'Choose from the most used',
            'popular_items' => 'Popular Services 2',
            'search_items' => 'Search Services 2',
            'not_found' => 'Not Found',
        );

        register_taxonomy('service2_category', array('listing'), array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'rewrite' => array('slug' => 'service2-category'),
            'capabilities' => array(
                'manage_terms' => 'manage_listing_settings',
                'edit_terms' => 'manage_listing_settings',
                'delete_terms' => 'manage_listing_settings',
                'assign_terms' => 'edit_listings',
            ),
        ));
    }

    public function add_form_fields($fields) {
        $fields['services2'] = array(
            'service2_category',
            'service2_features',
            'description',
            '_gallery',
            '_price_min',
            '_price_max',
            '_address',
            '_geolocation',
            '_phone',
            '_email',
            '_website'
        );
        return $fields;
    }

    public function add_form_templates($templates) {
        $templates['services2'] = array(
            'title' => 'Submit Service 2',
            'template' => array(
                'basic_info' => array(
                    'title' => 'Basic Information',
                    'icon' => 'sl sl-icon-doc',
                    'fields' => array(
                        'title' => array(
                            'label' => 'Title',
                            'type' => 'text',
                            'required' => true
                        ),
                        'service2_category' => array(
                            'label' => 'Category',
                            'type' => 'term-select',
                            'taxonomy' => 'service2_category',
                            'required' => true,
                            'class' => 'chosen-select',
                        ),
                        'description' => array(
                            'label' => 'Description',
                            'type' => 'wp-editor',
                            'required' => true
                        ),
                        '_price_min' => array(
                            'label' => 'Minimum Price',
                            'type' => 'number',
                            'required' => false
                        ),
                        '_price_max' => array(
                            'label' => 'Maximum Price',
                            'type' => 'number',
                            'required' => false
                        ),
                        '_gallery' => array(
                            'label' => 'Gallery',
                            'type' => 'gallery',
                            'required' => false
                        ),
                    )
                ),
                'location' => array(
                    'title' => 'Location',
                    'icon' => 'sl sl-icon-location',
                    'fields' => array(
                        '_address' => array(
                            'label' => 'Address',
                            'type' => 'text',
                            'required' => true
                        ),
                        '_geolocation' => array(
                            'label' => 'Geolocation',
                            'type' => 'geolocated',
                            'required' => true
                        ),
                    )
                ),
                'contact_details' => array(
                    'title' => 'Contact Details',
                    'icon' => 'sl sl-icon-phone',
                    'fields' => array(
                        '_phone' => array(
                            'label' => 'Phone',
                            'type' => 'text',
                            'required' => false
                        ),
                        '_email' => array(
                            'label' => 'Email',
                            'type' => 'text',
                            'required' => false
                        ),
                        '_website' => array(
                            'label' => 'Website',
                            'type' => 'text',
                            'required' => false
                        ),
                    )
                ),
            )
        );
        return $templates;
    }

    public function register_meta_boxes($meta_boxes) {
        // Kopiujemy meta boxy z services do services2
        foreach ($meta_boxes as $key => $box) {
            if (isset($box['taxonomies']) && in_array('service_category', $box['taxonomies'])) {
                $new_box = $box;
                $new_box['taxonomies'][] = 'service2_category';
                $meta_boxes[] = $new_box;
            }
        }
        return $meta_boxes;
    }

    public function load_single_template($template) {
        global $post;
        
        if (has_term('', 'service2_category', $post)) {
            $new_template = locate_template(array(
                'listeo-core/single-listing-services2.php',
                'listeo-core/single-listing.php'
            ));
            if (!empty($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }

    public function load_archive_template($template) {
        if (is_tax('service2_category')) {
            $new_template = locate_template(array(
                'listeo-core/archive-services2.php',
                'listeo-core/archive-listing.php'
            ));
            if (!empty($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }
}

// Initialize
function listeo_types_manager() {
    return Listeo_Types_Manager::instance();
}