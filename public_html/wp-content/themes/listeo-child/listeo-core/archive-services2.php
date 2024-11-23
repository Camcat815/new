<?php
/**
 * The template for displaying archive of Services2
 */

get_header();
$template_loader = new Listeo_Core_Template_Loader;

$layout = get_option('listeo_listing_archive_layout','list');
$current_layout = get_option('listeo_listing_archive_layout','list');

if(isset($_GET['list'])) {
    if($_GET['list'] == 'grid') { $layout = 'grid'; }
    if($_GET['list'] == 'list') { $layout = 'list'; }
}

$wrapper_class = ($layout == 'list') ? 'listing-list' : 'listing-grid'; ?>

<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?php post_type_archive_title(); ?></h1>
                <?php
                $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
                if($term) : ?>
                    <span><?php echo esc_html($term->description); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-9 col-md-8 padding-right-30">
            <!-- Sorting -->
            <div class="row margin-bottom-25 margin-top-30">
                <div class="col-md-6 col-xs-6">
                    <!-- Layout Switcher -->
                    <div class="layout-switcher">
                        <a href="?list=grid" class="grid <?php echo esc_attr($layout == 'grid' ? 'active' : ''); ?>"><i class="fa fa-th"></i></a>
                        <a href="?list=list" class="list <?php echo esc_attr($layout == 'list' ? 'active' : ''); ?>"><i class="fa fa-align-justify"></i></a>
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
            <div class="clearfix"></div>
            <div class="pagination-container margin-top-20 margin-bottom-60">
                <nav class="pagination">
                    <?php
                    echo paginate_links( array(
                        'base' => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
                        'format' => '?paged=%#%',
                        'current' => max( 1, get_query_var('paged') ),
                        'total' => $wp_query->max_num_pages,
                        'type' => 'list',
                        'prev_text' => '<i class="sl sl-icon-arrow-left"></i>',
                        'next_text' => '<i class="sl sl-icon-arrow-right"></i>'
                    ) );
                    ?>
                </nav>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4">
            <div class="sidebar">
                <?php get_sidebar('services2'); ?>
            </div>
        </div>
        <!-- Sidebar / End -->
    </div>
</div>

<?php get_footer(); ?>