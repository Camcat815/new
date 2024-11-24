<?php
/**
 * Single listing template for Services2
 */

get_header();
$template_loader = new Listeo_Core_Template_Loader;

while ( have_posts() ) : the_post();
    
    $layout = get_post_meta( $post->ID, '_layout', true );
    if(empty($layout)) { $layout = 'right-sidebar'; }
    
    $gallery_style = get_post_meta( $post->ID, '_gallery_style', true );
    if(empty($gallery_style)) { $gallery_style = get_option('listeo_gallery_type','top'); }
    
    switch ($gallery_style) {
        case 'top':
            $template_loader->get_template_part( 'single-partials/single-listing','gallery' );    
            break;
        
        case 'content':
            break;
        
        default:
            $template_loader->get_template_part( 'single-partials/single-listing','gallery' );    
            break;
    }

    ?>
    <div class="container <?php echo esc_attr($layout); ?>">
        <div class="row sticky-wrapper">
            <div class="col-lg-8 col-md-8 padding-right-30">
                <?php 
                // Get specific Services2 content
                $template_loader->get_template_part( 'single-partials/single-listing','services2-content' ); 

                // Reviews section
                if( listeo_check_if_review_allowed() || comments_open() ) { ?>
                    <div id="listing-reviews" class="listing-section">
                        <?php comments_template(); ?>
                    </div>
                <?php } ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-md-4 margin-top-75 sticky">
                <?php $template_loader->get_template_part( 'single-partials/single-listing','services2-sidebar' );  ?>
            </div>
        </div>
    </div>
<?php endwhile; 

get_footer();