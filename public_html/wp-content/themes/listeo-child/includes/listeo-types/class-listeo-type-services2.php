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
        add_filter('listeo_core_get_listing_types', array($this, 'add_to_listing_types'));
        add_filter('listeo_core_submit_form_content', array($this, 'submit_form_content'), 10, 2);
    }

    public function add_form_fields($fields) {
        $fields['services2'] = array(
            'service2_category' => array(
                'label'       => __('Service 2 Category', 'listeo_core'),
                'type'        => 'term-select',
                'taxonomy'    => 'service2_category',
                'required'    => true,
                'class'       => 'chosen-select',
                'default'     => '',
                'priority'    => 1
            ),
            'service2_features' => array(
                'label'       => __('Service 2 Features', 'listeo_core'),
                'type'        => 'term-checkboxes',
                'taxonomy'    => 'service2_features',
                'required'    => false,
                'priority'    => 2
            ),
            'service2_regions' => array(
                'label'       => __('Service 2 Regions', 'listeo_core'),
                'type'        => 'term-checkboxes',
                'taxonomy'    => 'region',
                'required'    => false,
                'priority'    => 3
            )
        );
        return $fields;
    }

    public function get_type_fields($fields, $type) {
        if ($type === 'services2') {
            $fields = array(
                'basic_info' => array(
                    'service2_category',
                    'service2_features',
                    'service2_regions',
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

    public function get_type_options($options, $type) {
        if ($type === 'services2') {
            $options = array(
                'layout' => 'grid',
                'orderby' => 'date',
                'order' => 'DESC',
                'per_page' => 12,
                'categories_enabled' => true,
                'features_enabled' => true,
                'regions_enabled' => true,
                'location_enabled' => true,
            );
        }
        return $options;
    }

    public function add_to_listing_types($types) {
        $types['services2'] = array(
            'taxonomy' => 'service2_category',
            'labels' => array(
                'name' => __('Services 2', 'listeo_core'),
                'singular_name' => __('Service 2', 'listeo_core')
            ),
            'submit_form' => true,
            'category_taxonomy' => 'service2_category',
            'template' => 'services2'
        );
        return $types;
    }

    public function submit_form_content($content, $type) {
        if ($type === 'services2') {
            ob_start();
            ?>
            <div class="submit-section">
                <div class="form-group">
                    <label><?php esc_html_e('Category', 'listeo_core'); ?></label>
                    <?php 
                    wp_dropdown_categories(array(
                        'taxonomy' => 'service2_category',
                        'hide_empty' => false,
                        'name' => 'service2_category',
                        'hierarchical' => true,
                        'show_option_none' => __('Select Category', 'listeo_core'),
                        'class' => 'chosen-select'
                    ));
                    ?>
                </div>
                
                <?php if(taxonomy_exists('service2_features')) : ?>
                <div class="form-group">
                    <label><?php esc_html_e('Features', 'listeo_core'); ?></label>
                    <?php
                    $features = get_terms(array(
                        'taxonomy' => 'service2_features',
                        'hide_empty' => false,
                    ));
                    if(!empty($features)) :
                        foreach($features as $feature) : ?>
                            <div class="checkbox">
                                <input type="checkbox" name="service2_features[]" id="feature-<?php echo esc_attr($feature->term_id); ?>" value="<?php echo esc_attr($feature->term_id); ?>">
                                <label for="feature-<?php echo esc_attr($feature->term_id); ?>"><?php echo esc_html($feature->name); ?></label>
                            </div>
                        <?php endforeach;
                    endif;
                    ?>
                </div>
                <?php endif; ?>
            </div>
            <?php
            $content = ob_get_clean();
        }
        return $content;
    }
}

// Initialize
function listeo_type_services2() {
    return Listeo_Type_Services2::instance();
}

listeo_type_services2();