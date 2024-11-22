<?php
/**
 * View flow inner step
 *
 * @package CartFlows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$inner_step_title = get_the_title( $inner_step_id );
$note             = get_post_meta( $inner_step_id, 'wcf-step-note', true );
$note             = isset( $variation['note'] ) ? $variation['note'] : $note;
$deleted          = isset( $variation['deleted'] ) ? $variation['deleted'] : false;
?>

<div class="wcf-archived-step">
	<div class="wcf-step">
		<div class="wcf-step-left-content">
			<span title="<?php echo esc_attr( $inner_step_title ); ?>" style="vertical-align: middle;"><?php echo wp_trim_words( $variation['title'], 3 ); ?></span>
			<?php
			if ( ! empty( $note ) ) {
				?>
				<span class="dashicons dashicons-editor-help" id="wcf-tooltip">
					<span class="wcf-ab-test-note-badge"><?php echo $note; ?></span>
				</span>	
				<?php
			}
			?>
			<span class="wcf-step-badge">
			<?php

				$archive_text = __( 'Archived on: ', 'cartflows-pro' );

			if ( $deleted ) {
				$archive_text = __( 'Deleted on: ', 'cartflows-pro' );
			}

				echo $archive_text . $variation['date'];
			?>
			</span>
		</div>
		<div class="wcf-steps-action-buttons">
			<?php if ( $ab_test_ui ) { ?>
				<?php if ( ! $deleted ) { ?>
					<?php
					foreach ( $action_buttons as $action_slug => $action_data ) {

						$action_attr = ' ';
						if ( isset( $action_data['attr'] ) && is_array( $action_data['attr'] ) ) {
							foreach ( $action_data['attr'] as $attr_key => $attr_value ) {
								$action_attr .= $attr_key . '="' . $attr_value . '"';
							}
						}

						?>
						<a href="<?php echo $action_data['link']; ?>" class="<?php echo $action_data['class']; ?>" title="<?php echo $action_data['tooltip']; ?>" <?php echo $action_attr; ?>>
							<?php if ( isset( $action_data['icon_html'] ) ) { ?>
								<?php echo $action_data['icon_html']; ?>
							<?php } else { ?>
								<span class="dashicons <?php echo $action_data['icon']; ?>"></span>
							<?php } ?>
							<span class="wcf-step-act-btn-text"><?php echo $action_data['label']; ?></span>
						</a>
					<?php } ?>
				<?php } else { ?>
					<span class="wcf-archieved-deletd-msg" style="align:right">
					<?php

						esc_html_e( 'Deleted variation can\'t be restored.', 'cartflows-pro' );

					?>
					</span>
				<?php } ?>

			<?php } ?>
		</div>
	</div>
</div>	
