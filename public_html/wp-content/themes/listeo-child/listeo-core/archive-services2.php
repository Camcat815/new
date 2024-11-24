<?php
/**
 * Archive template for Services2
 */

get_header();
$template_loader = new Listeo_Core_Template_Loader;

$layout = get_option('listeo_listing_archive_layout', 'list');
$wrapper_class = ($layout == 'list') ? 'listing-list' : 'listing-grid';

// Get current taxonomy term
$term = get_queried_object();
?>

<div id="titlebar" class="listing-titlebar">
    <div class="listing-titlebar-title">
        <h2><?php 
            if(is_tax()): 
                single_term_title(); 
            else:
                esc_html_e('Services 2','listeo_core');
            endif;
        ?> 
            <?php if(is_tax()) : ?>
                <span class="listing-tag">
                    <?php echo esc_html($term->count); ?> 
                    <?php printf( _n( 'listing', 'listings', $term->count, 'listeo_core' ) ); ?>
                </span>
            <?php endif; ?>
        </h2>
    </div>
</div>

<div class="container">
    <div class="row sticky-wrapper">
        <div class="col-lg-9 col-md-8 padding-right-30">
            <!-- Sorting / Layout Switcher -->
            <div class="row margin-bottom-25">
                <div class="col-md-6 col-xs-6">
                    <div class="layout-switcher">
                        <a href="#" data-layout="grid" class="grid <?php echo esc_attr($layout == 'grid' ? 'active' : ''); ?>"><i class="fa fa-th"></i></a>
                        <a href="#" data-layout="list" class="list <?php echo esc_attr($layout == 'list' ? 'active' : ''); ?>"><i class="fa fa-align-justify"></i></a>
                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div class="sort-by">
                        <?php listeo_core_sorting(); ?>
                    </div>
                </div>
            </div>

            <!-- Listings -->
            <div class="listings-container <?php echo esc_attr($wrapper_class); ?>">
                <?php
                if ( have_posts() ) :
                    while ( have_posts() ) : the_post();
                        $template_loader->get_template_part( 'content-listing' ); 
                    endwhile;
                else :
                    $template_loader->get_template_part( 'archive/no-found' );
                endif;
                ?>
            </div>

            <!-- Pagination -->
            <?php listeo_core_pagination(); ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
            <div class="sidebar">
                <?php get_sidebar('services2'); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>