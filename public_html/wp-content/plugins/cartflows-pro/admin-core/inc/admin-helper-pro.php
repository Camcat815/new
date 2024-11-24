<?php
/**
 * CartFlows Admin Helper.
 *
 * @package CartFlows
 */

namespace CartflowsProAdmin\AdminCore\Inc;

/**
 * Class AdminHelper.
 */
class AdminHelperPro {

	/**
	 * Meta_options.
	 *
	 * @var object instance
	 */
	public static $meta_options = array();

	/**
	 * Get_opt_steps
	 *
	 * @param string $flow_id flow id.
	 * @param array  $optgroup optgroup.
	 * @param string $step_id step id.
	 */
	public static function get_opt_steps( $flow_id, $optgroup, $step_id ) {

		$flow_steps    = get_post_meta( $flow_id, 'wcf-steps', true );
		$control_steps = array();
		$exclude_id    = $step_id;

		$steps = array();
		if ( is_array( $flow_steps ) ) {
			foreach ( $flow_steps as $f_index => $f_data ) {
				$control_steps[] = intval( $f_data['id'] );
			}
		}

		$cartflows_steps_args = array(
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_type'      => 'cartflows_step',
			'post_status'    => 'publish',
			'post__not_in'   => array( $exclude_id ),
			/** 'fields'           => 'ids', */
		);
		array_push(
			$steps,
			array(
				'value' => '',
				'label' => 'Default',
			)
		);

		foreach ( $optgroup as $optgroup_key => $optgroup_value ) {
			$cartflows_steps_args['tax_query'] = array( // phpcs:ignore
				'relation' => 'AND',
				array(
					'taxonomy' => 'cartflows_step_type',
					'field'    => 'slug',
					'terms'    => $optgroup_key,
				),
				array(
					'taxonomy' => 'cartflows_step_flow',
					'field'    => 'slug',
					'terms'    => 'flow-' . $flow_id,

				),
			);
			$cartflows_steps_query = new WP_Query( $cartflows_steps_args );
			$cartflows_steps       = $cartflows_steps_query->posts;

			if ( ! empty( $cartflows_steps ) ) {

				array_push(
					$steps,
					array(
						'isopt' => true,
						'title' => $optgroup_value,
					)
				);
				foreach ( $cartflows_steps as $key => $cf_step ) {

					if ( ! in_array( $cf_step->ID, $control_steps, true ) ) {
						continue;
					}

					array_push(
						$steps,
						array(
							'value' => $cf_step->ID,
							'label' => esc_attr( $cf_step->post_title ),
						)
					);

				}
			}
		}

		return $steps;

	}

}
