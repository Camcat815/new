<?php
$buttons = false;

if (isset($data)) :
	$buttons        = (isset($data->buttons)) ? $data->buttons : false;
endif;
if (!$buttons || $buttons == 'none') {
	return false;
}
$segments = explode('|', $buttons);

?>

<?php if (in_array('layout', $segments)) : ?>
	<div class="col-md-3 col-xs-6">
		<!-- Layout Switcher -->
		<div class="layout-switcher">
			<a href="#" data-layout="grid" class="grid"><i class="fa fa-th"></i></a>
			<a href="#" data-layout="list" class="list"><i class="fa fa-align-justify"></i></a>
		</div>
	</div>
<?php endif; ?>


<!-- Sorting / Layout Switcher -->
<?php if (in_array('layout', $segments)) : ?>
	<div class="col-md-9">
	<?php else : ?>
		<div class="col-md-12">
		<?php endif; ?>
		<div class="fullwidth-filters <?php if (get_option('listeo_ajax_browsing') == 'on') { ?> ajax-search <?php } ?>">

			<?php if (in_array('filters', $segments)) : ?>
				<!-- Panel Dropdown -->
				<div class="panel-dropdown wide float-right" id="tax-listing_feature-panel">
					<a href="#"><?php esc_html_e('More Filters', 'listeo_core'); ?></a>
					<div class="panel-dropdown-content checkboxes">
						<?php $dynamic_features = get_option('listeo_dynamic_features'); ?>
						<div class="row">

							<?php
							if ($dynamic_features == 'on') { ?>
								<div class="notification warning">
									<p><?php esc_html_e('Please choose category to display filters', 'listeo_core') ?></p>
								</div>

							<?php } else {
							?>
								<div class="panel-checkboxes-container">
									<?php
									$elements = listeo_core_get_options_array('taxonomy', 'listing_feature');

									$groups = array_chunk($elements, 4, true);
									foreach ($groups as $group) { ?>


										<?php
										if (isset($_GET['tax-listing_feature'])) {
											if (is_array($_GET['tax-listing_feature'])) {
												$selected = $_GET['tax-listing_feature'];
											} else {
												$selected = array(sanitize_text_field($_GET['tax-listing_feature']));
											}
										} else {
											$selected = array();
										}
										foreach ($group as $key => $value) { ?>
											<div class="panel-checkbox-wrap">
												<input <?php if (array_key_exists($value['slug'], $selected)) {
															echo 'checked="checked"';
														} ?> form="listeo_core-search-form" id="<?php echo esc_html($value['slug']) ?>" value="<?php echo esc_html($value['slug']) ?>" type="checkbox" name="tax-listing_feature<?php echo '[' . esc_html($value['slug']) . ']'; ?>">
												<label for="<?php echo esc_html($value['slug']) ?>"><?php echo esc_html($value['name']) ?></label>
											</div>

										<?php } ?>


									<?php } ?>
								</div>
							<?php } ?>

						</div>

						<!-- Buttons -->
						<div class="panel-buttons">
							<span class="panel-cancel"><?php esc_html_e('Cancel', 'listeo_core'); ?></span>
							<button class="panel-apply"><?php esc_html_e('Apply', 'listeo_core'); ?></button>
						</div>

					</div>
				</div>
				<!-- Panel Dropdown / End -->
			<?php endif; ?>

			<?php if (in_array('radius', $segments)) : ?>
				<!-- Panel Dropdown-->
				<div class="panel-dropdown float-right">
					<a href="#"><?php esc_html_e('Distance Radius', 'listeo_core'); ?></a>
					<div class="panel-dropdown-content radius-dropdown">
						<?php $default_radius = isset($_GET['search_radius']) ? $_GET['search_radius']  : get_option('listeo_maps_default_radius'); ?>
						<input form="listeo_core-search-form" name="search_radius" class="distance-radius" type="range" min="1" max="100" step="1" value="<?php echo esc_attr($default_radius); ?>" data-title="<?php esc_html_e('Radius around selected destination', 'listeo_core') ?>">
						<div class="panel-buttons">
							<span class="panel-disable" data-disable="<?php echo esc_attr_e('Disable', 'listeo_core'); ?>" data-enable="<?php echo esc_attr_e('Enable', 'listeo_core'); ?>"><?php esc_html_e('Disable'); ?></span>
							<button class="panel-apply"><?php esc_html_e('Apply', 'listeo_core'); ?></button>
						</div>
					</div>
				</div>
				<!-- Panel Dropdown / End -->
			<?php endif; ?>

			<?php if (in_array('order', $segments)) :

				$list_of_order = get_option('listeo_listings_sortby_options', array('highest-rated', 'reviewed', 'date-desc', 'date-asc', 'title', 'featured', 'views', 'verified', 'upcoming-event', 'rand'));
			?>
				<!-- Sort by -->
				<div class="sort-by">
					<div class="sort-by-select">
						<?php $default = isset($_GET['listeo_core_order']) ? (string) $_GET['listeo_core_order']  :  get_option('listeo_sort_by', 'date');
						?>
						<select form="listeo_core-search-form" name="listeo_core_order" data-placeholder="<?php esc_attr_e('Default order', 'listeo_core'); ?>" class="select2-sortby orderby">
							<option <?php selected($default, 'default'); ?> value="default"><?php esc_html_e('Default Order', 'listeo_core'); ?></option>
							<?php if (in_array('highest-rated', $list_of_order)) { ?> <option <?php selected($default, 'highest-rated'); ?> value="highest-rated"><?php esc_html_e('Highest Rated', 'listeo_core'); ?></option><?php } ?>
							<?php if (!get_option('listeo_disable_reviews')) : ?>
								<?php if (in_array('reviewed', $list_of_order)) { ?><option <?php selected($default, 'reviewed'); ?> value="reviewed"><?php esc_html_e('Most Reviewed', 'listeo_core'); ?></option><?php } ?>
							<?php endif; ?>
							<?php if (in_array('date-desc', $list_of_order)) { ?><option <?php selected($default, 'date-desc'); ?> value="date-desc"><?php esc_html_e('Newest Listings', 'listeo_core'); ?></option><?php } ?>
							<?php if (in_array('date-asc', $list_of_order)) { ?><option <?php selected($default, 'date-asc'); ?> value="date-asc"><?php esc_html_e('Oldest Listings', 'listeo_core'); ?></option><?php } ?>
							<?php if (in_array('title', $list_of_order)) { ?><option <?php selected($default, 'title'); ?> value="title"><?php esc_html_e('Alphabetically', 'listeo_core'); ?></option><?php } ?>
							<?php if (in_array('featured', $list_of_order)) { ?><option <?php selected($default, 'featured'); ?> value="featured"><?php esc_html_e('Featured', 'listeo_core'); ?></option><?php } ?>
							<?php if (in_array('views', $list_of_order)) { ?><option <?php selected($default, 'views'); ?> value="views"><?php esc_html_e('Most Views', 'listeo_core'); ?></option><?php } ?>
							<?php if (in_array('verified', $list_of_order)) { ?><option <?php selected($default, 'verified'); ?> value="verified"><?php esc_html_e('Verified', 'listeo_core'); ?></option><?php } ?>
							<?php if (in_array('upcoming-event', $list_of_order)) { ?><option <?php selected($default, 'upcoming-event'); ?> value="upcoming-event"><?php esc_html_e('Upcoming Event', 'listeo_core'); ?></option><?php } ?>
							<?php if (in_array('rand', $list_of_order)) { ?><option <?php selected($default, 'rand'); ?> value="rand"><?php esc_html_e('Random', 'listeo_core'); ?></option><?php } ?>
						</select>
					</div>
				</div>
				<!-- Sort by / End -->
			<?php endif; ?>

		</div>
		</div>