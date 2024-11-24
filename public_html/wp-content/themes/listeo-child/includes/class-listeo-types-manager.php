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
        $file_path = get_stylesheet_directory() . '/includes/listeo-types/class-listeo-type-services2.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }

    private function init_hooks() {
        error_log('Listeo Types Manager: init_hooks started');
        
        // Kluczowe hooki dla integracji z Listeo
        add_filter('listeo_core_get_listing_type_taxonomies', array($this, 'add_listing_type_taxonomy'));
        add_filter('listeo_core_form_types', array($this, 'add_form_type'));
        
        // Pozostałe hooki
        add_filter('listeo_core_listing_types', array($this, 'register_listing_types'));
        add_filter('listeo_core_get_listing_types', array($this, 'add_listing_types'));
        add_filter('listeo_core_submit_form_templates', array($this, 'add_form_templates'));
        add_filter('rwmb_meta_boxes', array($this, 'register_meta_boxes'), 15);
        add_action('init', array($this, 'register_taxonomies'), 0);
    }

    public function add_listing_type_taxonomy($taxonomies) {
        if (!is_array($taxonomies)) {
            $taxonomies = array();
        }

        $taxonomies['services2'] = array(
            'taxonomy' => 'service2_category',
            'type' => 'listing',
            'listing_type' => 'services2',
            'hierarchical' => true,
            'labels' => array(
                'name' => __('Services 2 Categories', 'listeo_core'),
                'singular_name' => __('Service 2 Category', 'listeo_core')
            )
        );
        return $taxonomies;
    }

    public function add_form_type($types) {
        if (!is_array($types)) {
            $types = array();
        }

        $types['services2'] = array(
            'name' => __('Services 2', 'listeo_core'),
            'template' => 'services2',
            'taxonomy' => 'service2_category',
            'fields' => array(
                'service2_category' => array(
                    'label' => __('Category', 'listeo_core'),
                    'type' => 'term-select',
                    'taxonomy' => 'service2_category',
                    'required' => true
                ),
                'description' => array(
                    'label' => __('Description', 'listeo_core'),
                    'type' => 'wp-editor',
                    'required' => true
                )
            )
        );
        return $types;
    }

    public function register_listing_types($types) {
        if (!is_array($types)) {
            $types = array();
        }

        $types['services2'] = array(
            'name' => __('Services 2', 'listeo_core'),
            'singular_name' => __('Service 2', 'listeo_core'),
            'taxonomy' => 'service2_category',
            'orderby' => 'date',
            'order' => 'DESC',
            'template' => 'services2',
            'submit_form' => true,
            'category_taxonomy' => 'service2_category'
        );
        return $types;
    }

    public function add_listing_types($types) {
        if (!is_array($types)) {
            $types = array();
        }

        if (!isset($types['services2'])) {
            $types['services2'] = array(
                'post_type' => 'listing',
                'taxonomy' => 'service2_category',
                'labels' => array(
                    'name' => __('Services 2', 'listeo_core'),
                    'singular_name' => __('Service 2', 'listeo_core')
                )
            );
        }
        return $types;
    }

    public function register_taxonomies() {
        $labels = array(
            'name' => __('Services 2 Categories', 'listeo_core'),
            'singular_name' => __('Service 2 Category', 'listeo_core'),
            'menu_name' => __('Services 2', 'listeo_core'),
            'all_items' => __('All Services 2', 'listeo_core'),
            'parent_item' => __('Parent Service 2', 'listeo_core'),
            'parent_item_colon' => __('Parent Service 2:', 'listeo_core'),
            'new_item_name' => __('New Service 2 Name', 'listeo_core'),
            'add_new_item' => __('Add New Service 2', 'listeo_core'),
            'edit_item' => __('Edit Service 2', 'listeo_core'),
            'update_item' => __('Update Service 2', 'listeo_core'),
            'search_items' => __('Search Services 2', 'listeo_core'),
            'not_found' => __('Not Found', 'listeo_core'),
        );

        register_taxonomy('service2_category', 
            array('listing'),
            array(
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
                'show_in_rest' => true,
                'meta_box_cb' => array($this, 'listing_type_taxonomy_meta_box')
            )
        );
    }

    public function listing_type_taxonomy_meta_box($post, $box) {
        if (!isset($box['args']) || !is_array($box['args'])) {
            $args = array();
        } else {
            $args = $box['args'];
        }
        
        $defaults = array('taxonomy' => 'service2_category');
        $r = wp_parse_args($args, $defaults);
        $tax_name = esc_attr($r['taxonomy']);
        $taxonomy = get_taxonomy($tax_name);
        
        if (!is_null($taxonomy)) {
            ?>
            <div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv">
                <div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
                    <?php
                    $name = ($tax_name == 'service2_category') ? 'tax_input[' . $tax_name . ']' : 'tax_input[' . $tax_name . ']';
                    $term_selected = wp_get_object_terms($post->ID, $tax_name, array('fields' => 'ids'));
                    echo "<input type='hidden' name='{$name}[]' value='0' />";
                    ?>
                    <ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
                        <?php wp_terms_checklist($post->ID, array(
                            'taxonomy' => $tax_name,
                            'selected_cats' => is_array($term_selected) ? $term_selected : array()
                        )); ?>
                    </ul>
                </div>
            </div>
            <?php
        }
    }

    public function add_form_templates($templates) {
        if (!is_array($templates)) {
            $templates = array();
        }

        $templates['services2'] = array(
            'title' => __('Submit Service 2', 'listeo_core'),
            'template' => array(
                'basic_info' => array(
                    'title' => __('Basic Information', 'listeo_core'),
                    'icon' => 'sl sl-icon-doc',
                    'fields' => array(
                        'title' => array(
                            'label' => __('Title', 'listeo_core'),
                            'type' => 'text',
                            'required' => true
                        ),
                        'service2_category' => array(
                            'label' => __('Category', 'listeo_core'),
                            'type' => 'term-select',
                            'taxonomy' => 'service2_category',
                            'required' => true,
                            'class' => 'chosen-select',
                        ),
                        'description' => array(
                            'label' => __('Description', 'listeo_core'),
                            'type' => 'wp-editor',
                            'required' => true
                        )
                    )
                )
            )
        );
        return $templates;
    }

    public function register_meta_boxes($meta_boxes) {
        if (!is_array($meta_boxes)) {
            return array();
        }

        foreach ($meta_boxes as $key => $box) {
            if (isset($box['taxonomies']) && in_array('service_category', $box['taxonomies'])) {
                $new_box = $box;
                $new_box['taxonomies'][] = 'service2_category';
                $meta_boxes[] = $new_box;
            }
        }
        return $meta_boxes;
    }
}

// Initialize
function listeo_types_manager() {
    return Listeo_Types_Manager::instance();
}