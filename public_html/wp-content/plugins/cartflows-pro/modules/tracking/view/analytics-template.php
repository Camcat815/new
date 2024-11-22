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
	<# if( data.all_steps.length ) { #>
	<div class="wcf-analytics-table-wrap">
		<div class="wcf-analytics-table">
				<div class="wcf-analytics-row wcf-analytics-thead">
					<div class="wp-ui-highlight wcf-analytics-th"><?php esc_html_e( 'Step', 'cartflows-pro' ); ?></div>
					<div class="wp-ui-highlight wcf-analytics-th"><?php esc_html_e( 'Total visits', 'cartflows-pro' ); ?></div>
					<div class="wp-ui-highlight wcf-analytics-th"><?php esc_html_e( 'Unique Visits', 'cartflows-pro' ); ?></div>
					<div class="wp-ui-highlight wcf-analytics-th"><?php esc_html_e( 'Conversions', 'cartflows-pro' ); ?></div>
					<div class="wp-ui-highlight wcf-analytics-th"><?php esc_html_e( 'Conversion Rate', 'cartflows-pro' ); ?></div>
					<div class="wp-ui-highlight wcf-analytics-th"><?php esc_html_e( 'Revenue', 'cartflows-pro' ); ?></div>
				</div>
				<# for ( key in data.all_steps ) { #>
					<# 
						var is_ab_test = false;

						if( data.all_steps[ key ]['ab-test'] !== undefined ) {
							is_ab_test = ( data.all_steps[ key ]['ab-test'] === true ) ? true : false; 
						}
					#>
					<div class="wcf-analytics-row wcf-analytics-step <# if( is_ab_test ) { #> wcf-ab-test-row <# } #>" data-id="{{data.all_steps[ key ]['id']}}">
						<div class="wcf-analytics-td">
						<# if( is_ab_test ) { #><span class="dashicons dashicons-arrow-right-alt2"></span><# } #>
							{{data.all_steps[ key ]['visits'].title}}
						</div>
						<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits'].total_visits}}</div>
						<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits'].unique_visits}}</div>
						<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits'].conversions}}</div>
						<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits'].conversion_rate}} %</div>
						<div class="wcf-analytics-td"><?php echo $currency_symbol; ?>{{data.all_steps[ key ]['visits'].revenue}}</div>
					</div>
					<# if( is_ab_test ) { #>
						<# for ( inner_key in data.all_steps[ key ]['visits-ab'] ) { #>
							<div class="wcf-analytics-row wcf-ab-test-inner-row" style="display: none;" data-parent-id="{{data.all_steps[ key ]['id']}}">
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab'][ inner_key ].title}}
								<# if( data.all_steps[ key ]['visits-ab'][ inner_key ].note !== '' ) { #>
									<span class="dashicons dashicons-editor-help" id="wcf-tooltip">
										<span class="wcf-ab-test-note-badge">{{data.all_steps[ key ]['visits-ab'][ inner_key ].note}}</span>
									</span>
								<# } #>
								</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab'][ inner_key ].total_visits}}</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab'][ inner_key ].unique_visits}}</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab'][ inner_key ].conversions}}</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab'][ inner_key ].conversion_rate}} %</div>
								<div class="wcf-analytics-td"><?php echo $currency_symbol; ?>{{data.all_steps[ key ]['visits-ab'][ inner_key ].revenue}}</div>
							</div>
						<# } #>
						<# for ( inner_key in data.all_steps[ key ]['visits-ab-archived'] ) { #>
							<div class="wcf-analytics-row wcf-ab-test-inner-row" style="display: none;" data-parent-id="{{data.all_steps[ key ]['id']}}">
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].title}}
								<# if( data.all_steps[ key ]['visits-ab-archived'][ inner_key ].note !== '' ) { #>
									<span class="dashicons dashicons-editor-help" id="wcf-tooltip">
										<span class="wcf-ab-test-note-badge">{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].note}}</span>
									</span>
								<# } #>	
									<span class="wcf-archived-date">{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].archived_date}}</span>
								</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].total_visits}}</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].unique_visits}}</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].conversions}}</div>
								<div class="wcf-analytics-td">{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].conversion_rate}} %</div>
								<div class="wcf-analytics-td"><?php echo $currency_symbol; ?>{{data.all_steps[ key ]['visits-ab-archived'][ inner_key ].revenue}}</div>
							</div>
						<# } #>
					<# } #>
				<# } #>
		</div>
	</div>
	<# } else { #>
	<div class="wcf-no-data-found"> <strong> <?php esc_html_e( 'No Data Found', 'cartflows-pro' ); ?> </strong></div>
	<# } #>
</script>
