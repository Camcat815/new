<?php
// File: wp-content/themes/listeo-child/includes/listeo-types/class-listeo-type-services2.php

if (!defined('ABSPATH')) {
    exit;
}

class Listeo_Type_Services2 {
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_filter('listeo_core_form_fields', array($this, 'add_form_fields'));
        add_filter('listeo_core_get_listing_type_fields', array($this, 'get_type_fields'), 10, 2);
        add_filter('listeo_core_get_listing_type_options', array($this, 'get_type_options'), 10, 2);
        add_filter('listeo_core_get_listing_types_with_taxonomies', array($this, 'add_to_listing_types'));
        add_filter('listeo_core_get_listing_types', array($this, 'add_to_listing_types'));
    }

    /**
     * Dodaje pola do formularza
     */
    public function add_form_fields($fields) {
        $fields['services2'] = array(
            'service2_category' => array(
                'label'       => __('Service 2 Category'),
                'type'        => 'term-select',
                'taxonomy'    => 'service2_category',
                'required'    => true,
                'class'       => 'chosen-select',
                'priority'    => 1
            ),
            'service2_features' => array(
                'label'       => __('Service 2 Features'),
                'type'        => 'term-checkboxes',
                'taxonomy'    => 'service2_features',
                'required'    => false,
                'priority'    => 2
            ),
        );
        return $fields;
    }

    /**
     * Zwraca pola dla typu
     */
    public function get_type_fields($fields, $type) {
        if ($type === 'services2') {
            $fields = array(
                'basic_info' => array(
                    'service2_category',
                    'service2_features',
                    'description',
                    '_price_min',
                    '_price_max',
                    '_gallery'
                ),
                'location' => array(
                    '_address',
                    '_geolocation'
                ),
                'contact' => array(
                    '_phone',
                    '_email',
                    '_website'
                )
            );
        }
        return $fields;
    }

    /**
     * Zwraca opcje dla typu
     */
    public function get_type_options($options, $type) {
        if ($type === 'services2') {
            $options = array(
                'layout' => 'grid',
                'orderby' => 'date',
                'order' => 'DESC',
                'per_page' => 12,
                'categories_enabled' => true,
                'features_enabled' => true,
                'location_enabled' => true,
            );
        }
        return $options;
    }

    /**
     * Dodaje Services2 do typów listingów
     */
    public function add_to_listing_types($types) {
        $types['services2'] = array(
            'taxonomy' => 'service2_category',
            'labels' => array(
                'name' => 'Services 2',
                'singular_name' => 'Service 2'
            )
        );
        return $types;
    }
}

// Initialize
function listeo_type_services2() {
    return Listeo_Type_Services2::instance();
}

listeo_type_services2();