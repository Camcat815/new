<?php

// if $data is not null
if (!empty($data)) {
	// extract $data as variables
	if (isset($data->show_email)) {
		$show_email = $data->show_email;
	} else {
		$show_email = true;
	}
} else {
	$show_email = true;
}
// check post status
if (get_post_status() == 'expired') {
	return;
}
$contacts = false;
$phone = get_post_meta(get_the_ID(), '_phone', true);
$mail = get_post_meta(get_the_ID(), '_email', true);
if (!$show_email) {
	$mail = false;
}
$website = get_post_meta(get_the_ID(), '_website', true);
if ($phone || $mail || $website) {
	$contacts = true;
}

$socials = false;
$facebook = get_post_meta(get_the_ID(), '_facebook', true);
$youtube = get_post_meta(get_the_ID(), '_youtube', true);
$twitter = get_post_meta(get_the_ID(), '_twitter', true);
$instagram = get_post_meta(get_the_ID(), '_instagram', true);
$skype = get_post_meta(get_the_ID(), '_skype', true);
$whatsapp = get_post_meta(get_the_ID(), '_whatsapp', true);
$linkedin = get_post_meta(get_the_ID(), '_linkedin', true);
$soundcloud = get_post_meta(get_the_ID(), '_soundcloud', true);
$pinterest = get_post_meta(get_the_ID(), '_pinterest', true);
$viber = get_post_meta(get_the_ID(), '_viber', true);
$_tiktok = get_post_meta(get_the_ID(), '_tiktok', true);
$_snapchat = get_post_meta(get_the_ID(), '_snapchat', true);
$_telegram = get_post_meta(get_the_ID(), '_telegram', true);
$_tumblr = get_post_meta(get_the_ID(), '_tumblr', true);
$_reddit = get_post_meta(get_the_ID(), '_reddit', true);
$_medium = get_post_meta(get_the_ID(), '_medium', true);
$_twitch = get_post_meta(get_the_ID(), '_twitch', true);
$_mixcloud = get_post_meta(get_the_ID(), '_mixcloud', true);
$_tripadvisor = get_post_meta(get_the_ID(), '_tripadvisor', true);
$_yelp = get_post_meta(get_the_ID(), '_yelp', true);
$_foursquare = get_post_meta(get_the_ID(), '_foursquare', true);
$_line = get_post_meta(get_the_ID(), '_line', true);


if ($facebook || $_line || $youtube || $twitter || $instagram || $skype || $whatsapp || $soundcloud || $pinterest || $viber || $_tiktok || $_snapchat || $_telegram || $_tumblr || $_reddit || $_medium || $_twitch || $_mixcloud || $_tripadvisor || $_yelp || $_foursquare) {
	$socials = true;
}

if ($socials || $contacts) :
?>

	<div class="listing-links-container">
		<?php
		$visibility_setting = get_option('listeo_user_contact_details_visibility'); // hide_all, show_all, show_logged, show_booked,  
		if ($visibility_setting == 'hide_all') {
			$show_details = false;
		} elseif ($visibility_setting == 'show_all') {
			$show_details = true;
		} else {
			if (is_user_logged_in()) {
				if ($visibility_setting == 'show_logged') {
					$show_details = true;
				} else {
					$show_details = false;
				}
			} else {
				$show_details = false;
			}
		}


		if ($contacts) :

			if ($show_details) { ?>

				<ul class="listing-links contact-links">
					<?php if (isset($phone) && !empty($phone)) : ?>
						<li><a href="tel:<?php echo esc_attr($phone); ?>" class="listing-links"><i class="fa fa-phone"></i> <?php echo esc_html($phone); ?></a></li>
					<?php endif; ?>
					<?php if (isset($mail) && !empty($mail)) : ?>
						<li><a href="mailto:<?php echo esc_attr($mail); ?>" class="listing-links"><i class="fa fa-envelope-o"></i> <?php echo esc_html($mail); ?></a>
						</li>
					<?php endif; ?>
					<?php if (isset($website) && !empty($website)) :
						$url =  wp_parse_url($website); ?>
						<li><a rel=nofollow href="<?php echo esc_url($website) ?>" target="_blank" class="listing-links"><i class="fa fa-link"></i> <?php
																																					if (isset($url['host'])) {
																																						echo esc_html($url['host']);
																																					} else {
																																						esc_html_e('Visit website', 'listeo_core');
																																					} ?></a></li>
					<?php endif; ?>
				</ul>
				<div class="clearfix"></div>
				<?php
			} else {
				if ($visibility_setting != 'hide_all') { ?>
					<p><?php if (get_option('listeo_popup_login', true) != 'ajax') {
							printf(
								esc_html__('Please %s sign %s in to see contact details.', 'listeo_core'),
								sprintf('<a href="%s" class="sign-in">', wp_login_url(apply_filters('the_permalink', get_permalink(get_the_ID()), get_the_ID()))),
								'</a>'
							);
						} else {
							printf(esc_html__('Please %s sign %s in to see contact details.', 'listeo_core'), '<a href="#sign-in-dialog" class="sign-in popup-with-zoom-anim">', '</a>');
						}
						?></p>
				<?php } ?>
		<?php }
		endif; ?>

		​<?php if ($show_details && $socials) : ?>
		<ul class="listing-links">
			<?php if (isset($facebook) && !empty($facebook)) : ?>
				<li><a href="<?php echo esc_url($facebook); ?>" target="_blank" class="listing-links-fb"><i class="fa fa-facebook-square"></i> Facebook</a></li>
			<?php endif; ?>
			<?php if (isset($youtube) && !empty($youtube)) : ?>
				<li><a href="<?php echo esc_url($youtube); ?>" target="_blank" class="listing-links-yt"><i class="fa fa-youtube-play"></i> YouTube</a></li>
			<?php endif; ?>
			<?php if (isset($instagram) && !empty($instagram)) : ?>
				<li><a href="<?php echo esc_url($instagram); ?>" target="_blank" class="listing-links-ig"><i class="fa fa-instagram"></i> Instagram</a></li>
			<?php endif; ?>
			<?php if (isset($twitter) && !empty($twitter)) : ?>
				<li><a href="<?php echo esc_url($twitter); ?>" target="_blank" class="listing-links-tt"><i class="fa-brands fa-x-twitter"></i> Share</a></li>
			<?php endif; ?>
			<?php if (isset($linkedin) && !empty($linkedin)) : ?>
				<li><a href="<?php echo esc_url($linkedin); ?>" target="_blank" class="listing-links-linkedit"><i class="fa fa-linkedin"></i> LinkedIn</a></li>
			<?php endif; ?>
			<?php if (isset($viber) && !empty($viber)) : ?>
				<li><a href="<?php echo esc_url($viber); ?>" target="_blank" class="listing-links-viber"><i class="fab fa-viber"></i> Viber</a></li>
			<?php endif; ?>
			<?php if (isset($skype) && !empty($skype)) : ?>
				<li><a href="<?php if (strpos($skype, 'http') === 0) {
									echo esc_url($skype);
								} else {
									echo "skype:+" . $skype . "?call";
								} ?>" target="_blank" class="listing-links-skype"><i class="fa fa-skype"></i> Skype</a></li>
			<?php endif; ?>
			<?php if (isset($whatsapp) && !empty($whatsapp)) : ?>
				<li><a href="<?php if (strpos($whatsapp, 'http') === 0) {
									echo esc_url($whatsapp);
								} else {
									echo "https://wa.me/" . $whatsapp;
								} ?>" target="_blank" class="listing-links-whatsapp"><i class="fa fa-whatsapp"></i> WhatsApp</a></li>
			<?php endif; ?>
			<?php if (isset($soundcloud) && !empty($soundcloud)) : ?>
				<li><a href="<?php if (strpos($soundcloud, 'http') === 0) {
									echo esc_url($soundcloud);
								} else {
									echo "https://soundcloud.com/" . $soundcloud;
								} ?>" target="_blank" class="listing-links-soundcloud"><i class="fa fa-soundcloud"></i> Soundcloud</a></li>
			<?php endif; ?>
			<?php if (isset($pinterest) && !empty($pinterest)) : ?>
				<li><a href="<?php if (strpos($pinterest, 'http') === 0) {
									echo esc_url($pinterest);
								} else {
									echo "https://pinterest.com/" . $pinterest;
								} ?>" target="_blank" class="listing-links-pinterest"><i class="fa fa-pinterest"></i> Pinterest</a></li>
			<?php endif; ?>
			<?php if (isset($_tiktok) && !empty($_tiktok)) : ?>
				<li><a href="<?php if (strpos($_tiktok, 'http') === 0) {
									echo esc_url($_tiktok);
								} else {
									echo "https://tiktok.com/@" . $_tiktok;
								} ?>" target="_blank" class="listing-links-tiktok"><i class="fab fa-tiktok"></i> TikTok</a></li>
			<?php endif; ?>
			<?php if (isset($_snapchat) && !empty($_snapchat)) : ?>
				<li><a href="<?php if (strpos($_snapchat, 'http') === 0) {
									echo esc_url($_snapchat);
								} else {
									echo "https://snapchat.com/add/" . $_snapchat;
								} ?>" target="_blank" class="listing-links-snapchat"><i class="fab fa-snapchat"></i> Snapchat</a></li>
			<?php endif; ?>
			<?php if (isset($_telegram) && !empty($_telegram)) : ?>
				<li><a href="<?php if (strpos($_telegram, 'http') === 0) {
									echo esc_url($_telegram);
								} else {
									echo "https://telegram.me/" . $_telegram;
								} ?>" target="_blank" class="listing-links-telegram"><i class="fab fa-telegram"></i> Telegram</a></li>
			<?php endif; ?>
			<?php if (isset($_tumblr) && !empty($_tumblr)) : ?>
				<li><a href="<?php if (strpos($_tumblr, 'http') === 0) {
									echo esc_url($_tumblr);
								} else {
									echo "https://tumblr.com/" . $_tumblr;
								} ?>" target="_blank" class="listing-links-tumblr"><i class="fab fa-tumblr"></i> Tumblr</a></li>
			<?php endif; ?>
			<?php if (isset($_reddit) && !empty($_reddit)) : ?>
				<li><a href="<?php if (strpos($_reddit, 'http') === 0) {
									echo esc_url($_reddit);
								} else {
									echo "https://reddit.com/u/" . $_reddit;
								} ?>" target="_blank" class="listing-links-reddit"><i class="fab fa-reddit"></i> Reddit</a></li>
			<?php endif; ?>
			<?php if (isset($_medium) && !empty($_medium)) : ?>
				<li><a href="<?php if (strpos($_medium, 'http') === 0) {
									echo esc_url($_medium);
								} else {
									echo "https://medium.com/@" . $_medium;
								} ?>" target="_blank" class="listing-links-medium"><i class="fab fa-medium"></i> Medium</a></li>
			<?php endif; ?>
			<?php if (isset($_twitch) && !empty($_twitch)) : ?>
				<li><a href="<?php if (strpos($_twitch, 'http') === 0) {
									echo esc_url($_twitch);
								} else {
									echo "https://twitch.tv/" . $_twitch;
								} ?>" target="_blank" class="listing-links-twitch"><i class="fab fa-twitch"></i> Twitch</a></li>
			<?php endif; ?>
			<?php if (isset($_mixcloud) && !empty($_mixcloud)) : ?>
				<li><a href="<?php if (strpos($_mixcloud, 'http') === 0) {
									echo esc_url($_mixcloud);
								} else {
									echo "https://mixcloud.com/" . $_mixcloud;
								} ?>" target="_blank" class="listing-links-mixcloud"><i class="fab fa-mixcloud"></i> Mixcloud</a></li>
			<?php endif; ?>
			<?php if (isset($_tripadvisor) && !empty($_tripadvisor)) : ?>
				<li><a href="<?php if (strpos($_tripadvisor, 'http') === 0) {
									echo esc_url($_tripadvisor);
								} else {
									echo "https://tripadvisor.com/" . $_tripadvisor;
								} ?>" target="_blank" class="listing-links-tripadvisor"><i class="fab fa-tripadvisor"></i> TripAdvisor</a></li>
			<?php endif; ?>
			<?php if (isset($_yelp) && !empty($_yelp)) : ?>
				<li><a href="<?php if (strpos($_yelp, 'http') === 0) {
									echo esc_url($_yelp);
								} else {
									echo "https://yelp.com/" . $_yelp;
								} ?>" target="_blank" class="listing-links-yelp"><i class="fab fa-yelp"></i> Yelp</a></li>
			<?php endif; ?>
			<?php if (isset($_foursquare) && !empty($_foursquare)) : ?>
				<li><a href="<?php if (strpos($_foursquare, 'http') === 0) {
									echo esc_url($_foursquare);
								} else {
									echo "https://foursquare.com/" . $_foursquare;
								} ?>" target="_blank" class="listing-links-foursquare"><i class="fab fa-foursquare"></i> Foursquare</a></li>
			<?php endif; ?>

			<?php if (isset($_line) && !empty($_line)) : ?>
				<li><a href="<?php if (strpos($_line, 'http') === 0) {
									echo esc_url($_line);
								} else {
									echo "https://line.me/" . $_line;
								} ?>" target="_blank" class="listing-links-line"><i class="fab fa-line"></i> Line</a></li>
			<?php endif; ?>


		</ul>
		<div class="clearfix"></div>
	<?php endif; ?>

	</div>
	<div class="clearfix"></div>
<?php endif; ?>