<?php
if (!defined('ABSPATH')) {
	exit;
}

$template_loader = new Listeo_Core_Template_Loader;

$full_width_header = get_post_meta($post->ID, 'listeo_full_width_header', TRUE);
if (empty($full_width_header)) {
	$full_width_header = 'use_global';
};


if ($full_width_header == 'use_global') {
	$full_width_header = get_option('listeo_full_width_header');
}


if ($full_width_header == 'enable' || $full_width_header == 'true') {
	get_header('fullwidth');
} else {
	get_header(get_option('header_bar_style', 'standard'));
}

$layout = get_option('listeo_single_layout', 'right-sidebar');
$mobile_layout = get_option('listeo_single_mobile_layout', 'right-sidebar');


$listing_logo = get_post_meta($post->ID, '_listing_logo', true);


$packages_disabled_modules = get_option('listeo_listing_packages_options', array());
if (empty($packages_disabled_modules)) {
	$packages_disabled_modules = array();
}

$user_package = get_post_meta($post->ID, '_user_package_id', true);

if ($user_package) {
	$package = listeo_core_get_user_package($user_package);
} else {
	$package = false;
}


$load_gallery = false;
if (in_array('option_gallery', $packages_disabled_modules)) {
	if ($package && $package->has_listing_gallery() == 1) {
		$load_gallery = true;
	}
} else {
	$load_gallery = true;
}

$load_video = false;
if (in_array('option_video', $packages_disabled_modules)) {
	if ($package && $package->has_listing_video() == 1) {
		$load_video = true;
	}
} else {
	$load_video = true;
}

$load_pricing_menu = false;

if (in_array('option_pricing_menu', $packages_disabled_modules)) {
	if ($package && $package->has_listing_pricing_menu() == 1) {
		$load_pricing_menu = true;
	}
} else {
	$load_pricing_menu = true;
}

$load_reviews = false;
if (in_array('option_reviews', $packages_disabled_modules)) {
	if ($package && $package->has_listing_reviews() == 1) {
		$load_reviews = true;
	}
} else {
	$load_reviews = true;
}

if (have_posts()) :



	$listing_type = get_post_meta(get_the_ID(), '_listing_type', true);

?>
	<!-- Gradient-->
	<div class="single-listing-page-titlebar"></div>


	<!-- Content
================================================== -->

	<div class="container <?php echo esc_attr($listing_type); ?>">
		<!-- Titlebar -->
		<div class="row">
			<div id="listing-gallery" class="col-md-12 listeo-grid-gallery-title">


				<div id="titlebar" class="listing-titlebar <?php if ($listing_logo) {
																echo " listing-titlebar-has-logo";
															} ?>">
					<?php
					if ($listing_logo) { ?>
						<div class="listing-logo"> <img src="<?php echo $listing_logo; ?>" alt=""></div>
					<?php } ?>
					<div class="listing-titlebar-title">
						<div class="listing-titlebar-tags">
							<?php
							$terms = get_the_terms(get_the_ID(), 'listing_category');
							if ($terms && !is_wp_error($terms)) :
								$categories = array();
								foreach ($terms as $term) {

									$categories[] = sprintf(
										'<a href="%1$s">%2$s</a>',
										esc_url(get_term_link($term->slug, 'listing_category')),
										esc_html($term->name)
									);
								}

								$categories_list = join(", ", $categories);
							?>
								<span class="listing-tag">
									<?php echo ($categories_list) ?>
								</span>
							<?php endif; ?>
							<?php
							switch ($listing_type) {
								case 'service':
									$type_terms = get_the_terms(get_the_ID(), 'service_category');
									$taxonomy_name = 'service_category';
									break;
								case 'rental':
									$type_terms = get_the_terms(get_the_ID(), 'rental_category');
									$taxonomy_name = 'rental_category';
									break;
								case 'event':
									$type_terms = get_the_terms(get_the_ID(), 'event_category');
									$taxonomy_name = 'event_category';
									break;
								case 'classifieds':
									$type_terms = get_the_terms(get_the_ID(), 'classifieds_category');
									$taxonomy_name = 'classifieds_category';
									break;


								default:
									# code...
									break;
							}
							if (isset($type_terms)) {
								if ($type_terms && !is_wp_error($type_terms)) :
									$categories = array();
									foreach ($type_terms as $term) {
										$categories[] = sprintf(
											'<a href="%1$s">%2$s</a>',
											esc_url(get_term_link($term->slug, $taxonomy_name)),
											esc_html($term->name)
										);
									}

									$categories_list = join(", ", $categories);
							?>
									<span class="listing-tag">
										<?php echo ($categories_list) ?>
									</span>
								<?php endif;
							}

							$region_terms = get_the_terms(get_the_ID(), 'region');
							$region_name = 'region';
							if (isset($region_terms)) {
								if ($region_terms && !is_wp_error($region_terms)) :
									$categories = array();
									foreach ($region_terms as $term) {
										$categories[] = sprintf(
											'<a href="%1$s">%2$s</a>',
											esc_url(get_term_link($term->slug, $region_name)),
											esc_html($term->name)
										);
									}

									$categories_list = join(", ", $categories);
								?>
									<span class="listing-tag">
										<?php echo ($categories_list) ?>
									</span>
							<?php endif;
							}

							?>
							<?php if (get_the_listing_price_range()) : ?>
								<span class="listing-pricing-tag"><i class="fa fa-<?php echo esc_attr(get_option('listeo_price_filter_icon', 'tag')); ?>"></i><?php echo get_the_listing_price_range(); ?></span>
							<?php endif;

							do_action('listeo/single-listing/tags');

							?>

						</div>

						<h1><?php the_title(); ?></h1>
						<?php if (get_the_listing_address()) : ?>
							<span>
								<a href="#listing-location" class="listing-address">
									<i class="fa fa-map-marker"></i>
									<?php the_listing_address(); ?>
								</a>
							</span> <br>
							<?php endif;


						if (!get_option('listeo_disable_reviews')) {
							$rating = get_post_meta($post->ID, 'listeo-avg-rating', true);
							if (!$rating && get_option('listeo_google_reviews_instead')) {
								$reviews = listeo_get_google_reviews($post);
								if (!empty($reviews['result']['reviews'])) {
									$rating = number_format_i18n($reviews['result']['rating'], 1);
									$rating = str_replace(',', '.', $rating);
								}
							}
							if (isset($rating) && $rating > 0) :
								$rating_type = get_option('listeo_rating_type', 'star');
								if ($rating_type == 'numerical') { ?>
									<div class="numerical-rating" data-rating="<?php $rating_value = esc_attr(round($rating, 1));
																				printf("%0.1f", $rating_value); ?>">
									<?php } else { ?>
										<div class="star-rating" data-rating="<?php echo $rating; ?>">
										<?php } ?>
										<?php $number = listeo_get_reviews_number($post->ID);
										if (!get_post_meta($post->ID, 'listeo-avg-rating', true) && get_option('listeo_google_reviews_instead')) {
											$number = $reviews['result']['user_ratings_total'];
										}  ?>

										<div class="rating-counter"><a href="#listing-reviews"><strong><?php esc_attr(round($rating, 1));
																										printf("%0.1f", $rating);  ?></strong> (<?php printf(_n('%s review', '%s reviews', $number, 'listeo_core'), number_format_i18n($number));  ?>)</a></div>
										</div>
								<?php endif;
						} ?>

									</div>
									<div class="listing-widget widget listeo_core widget_buttons">
										<div class="listing-share margin-top-40 margin-bottom-40 no-border">
											<?php
											$classObj = new Listeo_Core_Bookmarks;
											$nonce = wp_create_nonce("listeo_core_bookmark_this_nonce");

											if ($classObj->check_if_added($post->ID)) {                                                      ?>
												<button onclick="window.location.href='<?php echo get_permalink(get_option('listeo_bookmarks_page')) ?>'" class="like-button save liked"><span class="like-icon liked"></span> <b class="bookmark-btn-title"><?php esc_html_e('Bookmarked', 'listeo_core') ?></b>
												</button>
												<?php } else {
												if (is_user_logged_in()) { ?>
													<button class="like-button listeo_core-bookmark-it" data-post_id="<?php echo esc_attr($post->ID); ?>" data-confirm="<?php esc_html_e('Bookmarked!', 'listeo_core'); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><span class="like-icon"></span> <b class="bookmark-btn-title"><?php esc_html_e('Bookmark this listing', 'listeo_core') ?></b>
													</button>
													<?php } else {
													$popup_login = get_option('listeo_popup_login', 'ajax');
													if ($popup_login == 'ajax') { ?>
														<button href="#sign-in-dialog" class="like-button-notlogged sign-in popup-with-zoom-anim"><span class="like-icon"></span> <b class="bookmark-btn-title"><?php esc_html_e('Login To Bookmark Items', 'listeo_core') ?></b></button>
													<?php } else {
														$login_page = get_option('listeo_profile_page'); ?>
														<a href="<?php echo esc_url(get_permalink($login_page)); ?>" class="like-button-notlogged"><span class="like-icon"></span> <b class="bookmark-btn-title"><?php esc_html_e('Login To Bookmark Items', 'listeo_core') ?></b></a>
													<?php } ?>
												<?php }
											}

											$count = get_post_meta($post->ID, 'bookmarks_counter', true);
											if ($count) :
												if ($count < 0) {
													$count = 0;
												} ?>
												<span id="bookmarks-counter"><?php printf(_n('%s person bookmarked this place', '%s people bookmarked this place', $count, 'listeo_core'), number_format_i18n($count)); ?> </span>
											<?php endif; ?>
										</div>
									</div>
					</div> <!-- Titlebar -->
				</div> <!-- e/ col -->
			</div> <!-- e/ row -->

			<?php if ($load_gallery == true) { ?>
				<?php $template_loader->get_template_part('single-partials/single-listing', 'gallery-grid');  ?>

			<?php } ?>
			<div class="row sticky-wrapper">
				<!-- Sidebar
		================================================== -->
				<!-- " -->

				<?php if ($layout == "left-sidebar" || ($layout == 'right-sidebar' && $mobile_layout == "left-sidebar")) : ?>
					<div class="col-lg-4 col-md-4 listeo-single-listing-sidebar <?php if ($layout == 'right-sidebar' && $mobile_layout == "left-sidebar") echo "col-lg-push-8"; ?> sticky">
						<?php do_action('listeo/single-listing/sidebar-start'); ?>
						<?php if ($listing_type == 'classifieds') {
							$currency_abbr = get_option('listeo_currency');
							$currency_postion = get_option('listeo_currency_postion');
							$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

						?>
							<span id="classifieds_price"><?php if ($currency_postion == "before") {
																echo $currency_symbol;
															}
															echo get_post_meta($post->ID, '_classifieds_price', true);
															if ($currency_postion == "after") {
																echo $currency_symbol;
															} ?></span>
						<?php } ?>

						<?php $template_loader->get_template_part('single-partials/single-listing', 'claim');  ?>
						<?php get_sidebar('listing'); ?>
						<?php do_action('listeo/single-listing/sidebar-end'); ?>
					</div>
					<!-- Sidebar / End -->
				<?php endif; ?>

				<?php while (have_posts()) : the_post();  ?>
					<!--  -->
					<div id="post-<?php the_ID(); ?>" class="col-lg-8 col-md-8 listeo-single-listing-content <?php if ($layout == 'right-sidebar' && $mobile_layout == "left-sidebar") {
																													echo "col-lg-pull-4";
																												}  ?> padding-right-30">


						<?php
						if ($listing_type == 'classifieds') {
							$load_reviews = false;
						}
						?>

						<!-- Content
			================================================== -->


						<!-- Listing Nav -->
						<div id="listing-nav" class="listing-nav-container">
							<ul class="listing-nav">
								<?php do_action('listeo/single-listing/navigation-start'); ?>
								<li><a href="#listing-overview" class="active"><?php esc_html_e('Overview', 'listeo_core'); ?></a></li>
								<?php if ($load_gallery == true) : ?><li><a href="#listing-gallery"><?php esc_html_e('Gallery', 'listeo_core'); ?></a></li>
									<?php endif;
								$_menu = get_post_meta(get_the_ID(), '_menu_status', 1);
								if ($load_pricing_menu) {
									if (!empty($_menu)) {
										$_bookable_show_menu =  get_post_meta(get_the_ID(), '_hide_pricing_if_bookable', true);
										if (!$_bookable_show_menu) { ?>
											<li id="listing-nav-pricing"><a href="#listing-pricing-list"><?php esc_html_e('Pricing', 'listeo_core'); ?></a></li>
										<?php } ?>

								<?php }
								} ?>
								<?php if (class_exists('WeDevs_Dokan') && get_post_meta(get_the_ID(), '_store_section_status', 1)) : ?><li><a href="#listing-store"><?php esc_html_e('Store', 'listeo_core'); ?></a></li><?php endif; ?>
								<?php $video = get_post_meta($post->ID, '_video', true);
								if ($load_video && !empty($video)) :  ?>
									<li id="listing-nav-video"><a href="#listing-video"><?php esc_html_e('Video', 'listeo_core'); ?></a></li>
								<?php endif;
								$latitude = get_post_meta($post->ID, '_geolocation_lat', true);
								if (!empty($latitude)) :  ?>
									<li id="listing-nav-location"><a href="#listing-location"><?php esc_html_e('Location', 'listeo_core'); ?></a></li>
									<?php
								endif;
								if ($listing_type != 'classifieds') {
									if ($load_reviews && !get_option('listeo_disable_reviews')) {
										$reviews = get_comments(array(
											'post_id' => $post->ID,
											'status' => 'approve' //Change this to the type of comments to be displayed
										));
										if ($reviews) : ?>
											<li id="listing-nav-reviews"><a href="#listing-reviews"><?php esc_html_e('Reviews', 'listeo_core'); ?></a></li>
										<?php endif; ?>
										<?php
										$usercomment = false;
										if (is_user_logged_in()) {
											$usercomment = get_comments(array(
												'user_id' => get_current_user_id(),
												'post_id' => $post->ID,
											));
										}
										//TODO if open comments
										if (!$usercomment) { ?>
											<li id="listing-nav-add-review"><a href="#add-review"><?php esc_html_e('Add Review', 'listeo_core'); ?></a></li>
										<?php } ?>
								<?php }
								}
								do_action('listeo/single-listing/navigation-end');
								?>


							</ul>
						</div>
						<?php


						// 		$d = DateTime::createFromFormat('d-m-Y', $expires);
						// 		echo $d->getTimestamp(); 
						?>
						<!-- Overview -->
						<div id="listing-overview" class="listing-section">
							<?php $template_loader->get_template_part('single-partials/single-listing', 'main-details');  ?>

							<!-- Description -->
							<?php do_action('listeo/single-listing/before-content'); ?>
							<?php the_content(); ?>
							<?php do_action('listeo/single-listing/after-content'); ?>
							<?php
							if (in_array('option_social_links', $packages_disabled_modules)) {
								if ($package && $package->has_listing_social_links() == 1) {
									$template_loader->get_template_part('single-partials/single-listing', 'socials');
								}
							} else {
								$template_loader->get_template_part('single-partials/single-listing', 'socials');
							}
							?>
							<?php $template_loader->get_template_part('single-partials/single-listing', 'features');  ?>
						</div>

						<?php
						if ($load_pricing_menu) {
							$template_loader->get_template_part('single-partials/single-listing', 'pricing');
						} ?>
						<?php if (class_exists('WeDevs_Dokan') &&  get_post_meta(get_the_ID(), '_store_section_status', 1)) :   $template_loader->get_template_part('single-partials/single-listing', 'store');
						endif; ?>
						<?php if ($load_video) {
							$template_loader->get_template_part('single-partials/single-listing', 'video');
						} ?>
						<?php $template_loader->get_template_part('single-partials/single-listing', 'location');  ?>

						<?php
						if (in_array($listing_type, array('rental', 'service'))) {
							if (get_option('listeo_show_calendar_single')) {
								$template_loader->get_template_part('single-partials/single-listing', 'calendar');
							}
						} ?>
						<?php
						if (get_option('listeo_related_listings_status')) {
							$template_loader->get_template_part('single-partials/single-listing', 'related');
						}
						?>
						<?php $template_loader->get_template_part('single-partials/single-listing', 'google-reviews'); ?>
						<?php if ($load_reviews && !get_option('listeo_disable_reviews')) {

							$template_loader->get_template_part('single-partials/single-listing', 'reviews');
						} ?>
						<?php do_action('listeo/single-listing/end-content'); ?>
					</div>
				<?php endwhile; // End of the loop. 
				?>

				<?php

				if ($layout == "right-sidebar" && $mobile_layout != "left-sidebar") : ?>
					<div class="col-lg-4 col-md-4  listeo-single-listing-sidebar  sticky">
						<?php do_action('listeo/single-listing/sidebar-start'); ?>
						<?php
						$classifieds_price = get_post_meta($post->ID, '_classifieds_price', true);

						if ($listing_type == 'classifieds' && !empty($classifieds_price)) {

							$currency_abbr = get_option('listeo_currency');
							$currency_postion = get_option('listeo_currency_postion');
							$currency_symbol = Listeo_Core_Listing::get_currency_symbol($currency_abbr);

						?>
							<span id="classifieds_price">
								<?php if ($currency_postion == "before") {
									echo $currency_symbol;
								}
								$decimals = get_option('listeo_number_decimals', 2);
								echo number_format_i18n(get_post_meta($post->ID, '_classifieds_price', true), $decimals);
								if ($currency_postion == "after") {
									echo $currency_symbol;
								} ?>
							</span>
						<?php } ?>
						<?php $template_loader->get_template_part('single-partials/single-listing', 'claim');  ?>
						<?php get_sidebar('listing'); ?>
						<?php do_action('listeo/single-listing/sidebar-end'); ?>
					</div>
					<!-- Sidebar / End -->
				<?php endif; ?>
			</div>
		</div>



	<?php else : ?>

		<?php get_template_part('content', 'none'); ?>

	<?php endif; ?>


	<?php get_footer(); ?>