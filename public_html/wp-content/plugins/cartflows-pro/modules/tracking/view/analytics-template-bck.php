<?php
/**
 * Analytics template.
 *
 * @package cartflows
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<script type="text/template" id="tmpl-cartflows-analytics-template">

	<div class="wcf-analytics-summary">
		<div class="wcf-gross-wrap">
			<span class="wcf-gross-label"><?php esc_html_e( 'Gross Sale', 'cartflows-pro' ); ?></span>
			<div class="wcf-gross-sale">
				<?php echo $currency_symbol; ?>{{ data.revenue.gross_sale }}
			</div>
		</div>
		<div class="wcf-order-value-wrap">
			<span class="wcf-order-value-label"><?php esc_html_e( 'Average Order Value', 'cartflows-pro' ); ?></span>
			<div class="wcf-order-value">
				<?php echo $currency_symbol; ?>{{ data.revenue.avg_order_value }}
			</div>
		</div>
		<div class="wcf-bump-offer-wrap">
			<span class="wcf-bump-offer-label"><?php esc_html_e( 'Bump Offer Revenue', 'cartflows-pro' ); ?></span>
			<div class="wcf-bump-offer-sale">
				<?php echo $currency_symbol; ?>{{ data.revenue.bump_offer }}
			</div>
		</div>
	</div>
	<div class="wcf-analytics-filter-wrap">
		<div class="wcf-filters">

			<div class="wcf-filter-col text-left">
				<button data-diff="30" class="button button-{{ (30 == data.report_type) ? 'primary' : 'secondary' }} btn-first"><?php esc_html_e( 'Last Month', 'cartflows-pro' ); ?></button>
				<button data-diff="7" class="button button-{{ (7 == data.report_type) ? 'primary' : 'secondary' }}"><?php esc_html_e( 'Last Week', 'cartflows-pro' ); ?></button>
				<button data-diff="0" class=" button button-{{ ( 0 == data.report_type) ? 'primary' : 'secondary' }}"><?php esc_html_e( 'Today', 'cartflows-pro' ); ?></button>
			</div>
			<div class="wcf-filter-col text-right">
				<input class="wcf-custom-filter-input" type="text" id="wcf_custom_filter_from" placeholder="YYYY-MM-DD" value="" readonly="readonly" >
				<input class="wcf-custom-filter-input" type="text" id="wcf_custom_filter_to" placeholder="YYYY-MM-DD" value="" readonly="readonly" >
				<button data-diff="-1" id="wcf_custom_filter" class="button button-{{ (-1 == data.report_type) ? 'primary' : 'secondary' }}">Custom Filter</button>

			</div>
		</div>
	</div>
	<# if( data.visits.length ) { #>
	<div class="wcf-analytics-table-wrap">
		<table class="wcf-analytics-table">
			<tbody>
				<tr>
					<th class="wp-ui-highlight"><?php esc_html_e( 'Step', 'cartflows-pro' ); ?></th>
					<th class="wp-ui-highlight"><?php esc_html_e( 'Total visits', 'cartflows-pro' ); ?></th>
					<th class="wp-ui-highlight"><?php esc_html_e( 'Unique Visits', 'cartflows-pro' ); ?></th>
					<th class="wp-ui-highlight"><?php esc_html_e( 'Revenue', 'cartflows-pro' ); ?></th>
				</tr>
				<# for ( key in data.visits ) { #>
					<tr class="wcf-analytics-row">
						<td> {{data.visits[ key ].title}}</td>
						<td>{{data.visits[ key ].total_visits}}</td>
						<td>{{data.visits[ key ].unique_visits}}</td>
						<td><?php echo $currency_symbol; ?>{{data.visits[ key ].revenue}}</td>
					</tr>
				<# } #>
			</tbody>
		</table>
	</div>
	<# } else { #>
	<div class="wcf-no-data-found"> <strong> <?php esc_html_e( 'No Data Found', 'cartflows-pro' ); ?> </strong></div>
	<# } #>
</script>

<input type="hidden" id="cf-steps-data" data-steps="<?php echo htmlspecialchars( wp_json_encode( self::$step_data ), ENT_COMPAT, 'utf-8' ); ?>">
