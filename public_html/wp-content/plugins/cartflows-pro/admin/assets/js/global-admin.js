( function ( $ ) {
	var _show_image_field_bump_offer = function () {
		$( '.field-wcf-order-bump-style select' ).on( 'change', function ( e ) {
			e.preventDefault();

			var $this = $( this ),
				selected_value = $this.val();

			$( '.field-wcf-order-bump-image' ).removeClass( 'hide' );
		} );
	};

	var _show_image_field_bump_offer_event = function () {
		var get_wrap = $( '.wcf-product-order-bump' ),
			get_field_row = get_wrap.find( '.field-wcf-order-bump-style' ),
			get_field = get_field_row.find( 'select' ),
			get_value = get_field.val();

		var get_img_field = get_wrap.find( '.field-wcf-order-bump-image' );

		console.log( get_img_field );

		$( '.field-wcf-order-bump-image' ).removeClass( 'hide' );
	};

	var wcf_step_delete_sortable = function () {
		$( '.wcf-flow-steps-container' ).on(
			'wcf-step-deleted',
			function ( e, step_id ) {
				$(
					'.wcf-conditional-badge[data-no-step="' + step_id + '"]'
				).attr( 'data-no-step', '' );
				$(
					'.wcf-conditional-badge[data-yes-step="' + step_id + '"]'
				).attr( 'data-yes-step', '' );

				wcf_step_sorting();
			}
		);
	};

	/* Create default steps */
	var wcf_sort_flow = function () {
		$( '.wcf-flow-settings .wcf-flow-steps-container' ).on(
			'sortupdate',
			function ( event, ui ) {
				var target = ui.item;
				var step_type = $( target ).data( 'term-slug' );

				wcf_step_sorting();
			}
		);
	};

	var wcf_step_sorting = function () {
		var upsell_downsell_steps = $(
			'.wcf-step-wrap[data-term-slug="upsell"], .wcf-step-wrap[data-term-slug="downsell"] '
		);

		$.each( upsell_downsell_steps, function () {
			var $this = $( this );

			var next_step_element = $this.next();

			if ( $this.data( 'term-slug' ) === 'upsell' ) {
				var next_yes_upsell = $this.next();

				var next_upsell_found = false;

				var yes_step_badge_element = $this.find(
					'.wcf-yes-next-badge'
				);

				var render_yes_step = yes_step_badge_element.data( 'yes-step' );

				if ( render_yes_step === '' || render_yes_step === undefined ) {
					while ( ! next_upsell_found ) {
						if (
							next_yes_upsell.data( 'term-slug' ) === 'downsell'
						) {
							next_yes_upsell = next_yes_upsell.next();
						} else {
							var next_yes_upsell_step_name = next_yes_upsell
								.find( '.wcf-step-left-content' )
								.children()
								.eq( 1 )
								.text();

							yes_step_badge_element.remove();

							if ( next_yes_upsell.length !== 0 ) {
								var yes_label =
									cartflows_admin.add_yes_label +
									next_yes_upsell_step_name;
							} else {
								var yes_label =
									cartflows_admin.add_yes_label +
									cartflows_admin.not_found_label;
							}

							var yes_step_html =
								'<span class="wcf-flow-badge wcf-conditional-badge wcf-yes-next-badge">' +
								yes_label +
								'</span>';

							$this
								.find( '.wcf-badges' )
								.prepend( yes_step_html );

							next_upsell_found = true;
						}
					}
				}
			} else {
				var render_yes_step = $this
					.find( '.wcf-yes-next-badge' )
					.data( 'yes-step' );

				if ( render_yes_step === '' || render_yes_step === undefined ) {
					var next_step_name = next_step_element
						.find( '.wcf-step-left-content' )
						.children()
						.eq( 1 )
						.text();

					$this.find( '.wcf-yes-next-badge' ).remove();

					if ( next_step_element.length !== 0 ) {
						var yes_label =
							cartflows_admin.add_yes_label + next_step_name;
					} else {
						var yes_label =
							cartflows_admin.add_yes_label +
							cartflows_admin.not_found_label;
					}

					var yes_step_html =
						'<span class="wcf-flow-badge wcf-conditional-badge wcf-yes-next-badge">' +
						yes_label +
						'</span>';
					$this.find( '.wcf-badges' ).prepend( yes_step_html );
				}
			}

			var no_step_badge_element = $this.find( '.wcf-no-next-badge' );

			var render_no_step = no_step_badge_element.data( 'no-step' );

			if ( render_no_step === '' || render_no_step === undefined ) {
				var next_no_name = next_step_element
					.find( '.wcf-step-left-content' )
					.children()
					.eq( 1 )
					.text();

				no_step_badge_element.remove();

				if ( next_step_element.length !== 0 ) {
					var no_label = cartflows_admin.add_no_label + next_no_name;
				} else {
					var no_label =
						cartflows_admin.add_no_label +
						cartflows_admin.not_found_label;
				}

				var no_step_html =
					'<span class="wcf-flow-badge wcf-conditional-badge wcf-no-next-badge">' +
					no_label +
					'</span>';

				$this.find( '.wcf-badges' ).append( no_step_html );
			}
		} );

		$( '.wcf-conditional-badge' ).show();

		wcf_remove_step_tags();
	};

	var wcf_remove_step_tags = function () {
		$( '.wcf-step-wrap[data-term-slug="checkout"]' )
			.first()
			.prevAll()
			.find( '.wcf-conditional-badge' )
			.hide();
		$( '.wcf-step-wrap[data-term-slug="thankyou"]' )
			.first()
			.nextAll()
			.find( '.wcf-conditional-badge' )
			.hide();
	};

	var wcf_flow_analytics = function () {
		$( function () {
			var parent = $( '.wcf-flow-enable-analytics' ),
				analytics_checkbox = $(
					'.wcf-flow-enable-analytics input[type="checkbox"]'
				);
			wcf_show_hide_flow_analytics( parent, analytics_checkbox );
		} );

		$( '.wcf-flow-enable-analytics input[type="checkbox"]' ).on(
			'click',
			function () {
				var analytics_checkbox = $( this ),
					parent = analytics_checkbox.closest(
						'.wcf-flow-enable-analytics'
					);
				wcf_show_hide_flow_analytics( parent, analytics_checkbox );
			}
		);

		var wcf_show_hide_flow_analytics = function (
			parent,
			analytics_checkbox
		) {
			if ( analytics_checkbox.is( ':checked' ) ) {
				parent.next( '.wcf-flow-sandbox-table-container' ).show();
			} else {
				parent.next( '.wcf-flow-sandbox-table-container' ).hide();
			}
		};
	};

	var wcf_reset_flow_analytics = function ( e ) {
		$( '#wcf-reset-analytics-button' ).on( 'click', function ( e ) {
			e.preventDefault();
			let reset_btn = $( this ),
				reset_btn_txt = reset_btn.text(),
				reset_btn_prc = reset_btn.data( 'process' );

			if ( confirm( cartflows_admin.confirm_msg_for_analytics ) ) {
				var data = {
					action: 'wcf_reset_flow_analytics',
					security: cartflows_admin.wcf_reset_analytics_nonce,
					flow_id: cartflows_admin.flow_id,
				};

				reset_btn.text( reset_btn_prc );

				$.ajax( {
					url: ajaxurl,
					data: data,
					dataType: 'json',
					type: 'POST',
					success: function ( response ) {
						if ( response.success ) {
							reset_btn.text( reset_btn_txt );
							alert(
								cartflows_admin.succesful_msg_for_analytics
							);
						}
					},
				} );
			}
		} );
	};

	var wcf_ab_test_events = function () {
		$( '.wcf-flow-settings .wcf-step-abtest' ).on( 'click', function ( e ) {
			e.preventDefault();

			var $this = $( this ),
				step_id = $this.data( 'id' ),
				icon_span = $this.find( '.dashicons-forms' ),
				// text_span = $this.find('.wcf-step-act-btn-text'),
				wcf_step = $this.closest( '.wcf-step' ),
				parent = $this.parents( '.wcf-step-wrap' );

			var delete_status = confirm(
				'This action will enable split test for this step. Are you sure?'
			);

			if ( true == delete_status ) {
				// console.log( 'Step Deleting' );
				icon_span.addClass( 'wp-ui-text-notification' );
				// text_span.addClass('wp-ui-text-notification').text('Deleting..');
				//$this.text('Deleting..');

				var post_id = $( 'form#post #post_ID' ).val();

				wcf_step.addClass( 'wcf-loader' );

				$.ajax( {
					url: ajaxurl,
					data: {
						action: 'cartflows_create_ab_test_variation',
						flow_id: post_id,
						step_id: step_id,
						security:
							cartflows_admin.wcf_create_ab_test_variation_nonce,
					},
					dataType: 'json',
					type: 'POST',
					success: function ( data ) {
						setTimeout( function () {
							location.reload();
						}, 300 );

						console.log( data );
						// wcf_step.removeClass('wcf-loader');
					},
				} );
			}
		} );

		$( '.wcf-flow-settings .wcf-start-split-test' ).on(
			'click',
			function ( e ) {
				e.preventDefault();

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					text_span = $this.find( 'span' );

				text_span.text( 'Updating...' );

				var post_id = $( 'form#post #post_ID' ).val();

				$.ajax( {
					url: ajaxurl,
					data: {
						action: 'cartflows_start_ab_test',
						flow_id: post_id,
						step_id: step_id,
						security: cartflows_admin.wcf_start_ab_test_nonce,
					},
					dataType: 'json',
					type: 'POST',
					success: function ( data ) {
						$this.removeClass(
							'wcf-start-ab-test wcf-stop-ab-test'
						);
						if ( data.start ) {
							$this.addClass( 'wcf-stop-ab-test' );
						} else {
							$this.addClass( 'wcf-start-ab-test' );
						}

						text_span.text( data.text );
					},
				} );
			}
		);

		$( '.wcf-flow-settings .wcf-ab-test-step-delete' ).on(
			'click',
			function ( e ) {
				e.preventDefault();
				var current_target = $( e.target );

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					icon_span = $this.find( '.dashicons-trash' ),
					text_span = $this.find( '.wcf-step-act-btn-text' ),
					parent = $this.parents( '.wcf-step-wrap' ),
					wcf_step = $this.closest( '.wcf-step' );

				var delete_status = confirm(
					'Do you want to delete this variation and its data? Are you sure?'
				);

				if ( true == delete_status ) {
					console.log( 'Step Deleting' );
					icon_span.addClass( 'wp-ui-text-notification' );
					text_span
						.addClass( 'wp-ui-text-notification' )
						.text( 'Deleting..' );

					var post_id = $( 'form#post #post_ID' ).val();

					wcf_step.addClass( 'wcf-loader' );

					$.ajax( {
						url: ajaxurl,
						data: {
							action: 'cartflows_delete_ab_test_step',
							post_id: post_id,
							step_id: step_id,
							security:
								cartflows_admin.wcf_delete_ab_test_step_nonce,
						},
						dataType: 'json',
						type: 'POST',
						success: function ( data ) {
							location.reload();
						},
					} );
				}
			}
		);

		$( '.wcf-flow-settings .wcf-ab-test-step-archive' ).on(
			'click',
			function ( e ) {
				e.preventDefault();
				var current_target = $( e.target );

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					icon_span = $this.find( '.dashicons-trash' ),
					text_span = $this.find( '.wcf-step-act-btn-text' ),
					parent = $this.parents( '.wcf-step-wrap' ),
					wcf_step = $this.closest( '.wcf-step' );

				var delete_status = confirm(
					'Do you want to archive this variation? Are you sure?'
				);

				if ( true == delete_status ) {
					console.log( 'Archiving Step ' );
					icon_span.addClass( 'wp-ui-text-notification' );
					text_span
						.addClass( 'wp-ui-text-notification' )
						.text( 'Archiving...' );

					var post_id = $( 'form#post #post_ID' ).val();

					wcf_step.addClass( 'wcf-loader' );

					$.ajax( {
						url: ajaxurl,
						data: {
							action: 'cartflows_archive_ab_test_step',
							post_id: post_id,
							step_id: step_id,
							security:
								cartflows_admin.wcf_archive_ab_test_step_nonce,
						},
						dataType: 'json',
						type: 'POST',
						success: function ( data ) {
							location.reload();
						},
					} );
				}
			}
		);

		$( '.wcf-flow-settings .wcf-ab-test-step-clone' ).on(
			'click',
			function ( e ) {
				e.preventDefault();
				var current_target = $( e.target );

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					control_id = $this.data( 'control-id' ),
					icon_span = $this.find( '.dashicons-trash' ),
					text_span = $this.find( '.wcf-step-act-btn-text' ),
					parent = $this.parents( '.wcf-step-wrap' ),
					wcf_step = $this.closest( '.wcf-step' );

				var delete_status = confirm(
					'Do you want to clone this variation? Are you sure?'
				);

				if ( true == delete_status ) {
					console.log( 'Clonning Step' );
					icon_span.addClass( 'wp-ui-text-notification' );
					text_span
						.addClass( 'wp-ui-text-notification' )
						.text( 'Clonning...' );

					var post_id = $( 'form#post #post_ID' ).val();

					wcf_step.addClass( 'wcf-loader' );

					$.ajax( {
						url: ajaxurl,
						data: {
							action: 'cartflows_clone_ab_test_step',
							post_id: post_id,
							step_id: step_id,
							control_id: control_id,
							security:
								cartflows_admin.wcf_clone_ab_test_step_nonce,
						},
						dataType: 'json',
						type: 'POST',
						success: function ( data ) {
							location.reload();
						},
					} );
				}
			}
		);

		$( '.wcf-flow-settings .wcf-declare-winner' ).on(
			'click',
			function ( e ) {
				e.preventDefault();

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					icon_span = $this.find( '.dashicons-yes' ),
					wcf_step = $this.closest( '.wcf-step' ),
					parent = $this.parents( '.wcf-step-wrap' );

				var delete_status = confirm(
					'Do you want to declare this variation a winner and archive the other variations? Are you sure? '
				);

				if ( true == delete_status ) {
					icon_span.addClass( 'wp-ui-text-notification' );

					var post_id = $( 'form#post #post_ID' ).val();
					wcf_step.addClass( 'wcf-loader' );
					$.ajax( {
						url: ajaxurl,
						data: {
							action: 'cartflows_declare_ab_test_winner',
							flow_id: post_id,
							step_id: step_id,
							security:
								cartflows_admin.wcf_declare_ab_test_winner_nonce,
						},
						dataType: 'json',
						type: 'POST',
						success: function ( data ) {
							setTimeout( function () {
								location.reload();
							}, 100 );

							// wcf_step.removeClass('wcf-loader');
						},
					} );
				}
			}
		);

		/* Restore archive variation */
		$( '.wcf-flow-settings .wcf-step-archive-restore' ).on(
			'click',
			function ( e ) {
				e.preventDefault();

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					control_id = $this.data( 'control-id' ),
					wcf_step = $this.closest( '.wcf-step' );

				var restore_status = confirm(
					'Do you want to restore this archived variation? Are you sure?'
				);

				if ( true == restore_status ) {
					var post_id = $( 'form#post #post_ID' ).val();

					wcf_step.addClass( 'wcf-loader' );

					$.ajax( {
						url: ajaxurl,
						data: {
							action:
								'cartflows_restore_archive_ab_test_variation',
							flow_id: post_id,
							step_id: step_id,
							control_id: control_id,
							security:
								cartflows_admin.wcf_restore_archive_ab_test_variation_nonce,
						},
						dataType: 'json',
						type: 'POST',
						success: function ( data ) {
							location.reload();
						},
					} );
				}
			}
		);

		/* Delete archive variation */
		$( '.wcf-flow-settings .wcf-step-archive-delete' ).on(
			'click',
			function ( e ) {
				e.preventDefault();

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					control_id = $this.data( 'control-id' ),
					wcf_step = $this.closest( '.wcf-step' );

				var delete_status = confirm(
					'Do you want to delete this archived variation? Are you sure?'
				);

				if ( delete_status ) {
					var post_id = $( 'form#post #post_ID' ).val();

					wcf_step.addClass( 'wcf-loader' );

					$.ajax( {
						url: ajaxurl,
						data: {
							action:
								'cartflows_delete_archive_ab_test_variation',
							flow_id: post_id,
							step_id: step_id,
							control_id: control_id,
							security:
								cartflows_admin.wcf_delete_archive_ab_test_variation_nonce,
						},
						dataType: 'json',
						type: 'POST',
						success: function ( data ) {
							location.reload();
						},
					} );
				}
			}
		);

		$( document ).on(
			'click',
			'.wcf-analytics-table .wcf-ab-test-row',
			function ( e ) {
				e.preventDefault();

				var $this = $( this ),
					step_id = $this.data( 'id' ),
					icon_span = $this.find( '.dashicons' ),
					parent = $this.parents( '.wcf-analytics-table' ),
					show_rows = parent.find(
						'.wcf-ab-test-inner-row[data-parent-id="' +
							step_id +
							'"]'
					);

				icon_span.toggleClass(
					'dashicons-arrow-right-alt2 dashicons-arrow-down-alt2'
				);
				show_rows.toggle();
			}
		);

		$( document ).on( 'click', '#wcf-archived-button', function ( e ) {
			var $this = $( this ),
				archive_steps = $this.next( '.wcf-archived-steps' );
			archive_steps.toggle();
			$this
				.find( 'i' )
				.toggleClass( 'dashicons-arrow-right  dashicons-arrow-down' );
		} );

		var ab_settings_all_events = function () {
			var close_ab_settings_popup = function ( current_selector ) {
				current_selector
					.closest( '.wcf-ab-test-settings-overlay' )
					.hide();
			};

			/* Ab test settings */
			var show_close_ab_settings = function () {
				$( '.wcf-flow-settings .wcf-settings-split-test' ).on(
					'click',
					function ( e ) {
						e.preventDefault();

						var $this = $( this ),
							parent = $this.parents( '.wcf-ab-test-head' ),
							current_content_wrap_html = parent
								.find( '.wcf-ab-test-content-wrap' )
								.html(),
							settings_overlay = $(
								'.wcf-ab-test-settings-overlay'
							);

						settings_overlay
							.find( '.wcf-content-wrap' )
							.html( current_content_wrap_html );

						init_ab_traffic_fields( settings_overlay );

						settings_overlay.show();
					}
				);

				$( '.wcf-ab-test-settings-overlay' ).on(
					'click',
					'.wcf-popup-close-wrap, .wcf-ab-test-cancel',
					function ( e ) {
						e.preventDefault();

						var $this = $( this ),
							parent = $this.parents( '.wcf-ab-test-head' );

						$this.closest( '.wcf-ab-test-settings-overlay' ).hide();
					}
				);

				$( document ).on(
					'click',
					'.wcf-ab-test-settings-overlay',
					function ( e ) {
						if (
							$( event.target ).hasClass(
								'wcf-ab-test-settings-overlay'
							)
						) {
							$( '.wcf-ab-test-settings-overlay' ).hide();
						}
					}
				);
			};

			var init_ab_traffic_fields = function ( wrapper ) {
				var slider = wrapper.find( '.wcf-traffic-slider-wrap' ),
					variations = {};

				slider.each( function () {
					var $this = $( this ),
						variation_id = $this.data( 'variation-id' ),
						range = $this.find( '.wcf-traffic-range input' ),
						range_input = $this.find( '.wcf-traffic-value input' ),
						range_value = parseInt( range.val() );

					/* Set variation values */
					variations[ variation_id ] = parseInt( range_value );

					console.log( variations );

					range.on( 'input', function ( event ) {
						let current_this = $( this ),
							settings_parent = current_this.closest(
								'.wcf-ab-settings-content'
							),
							range_parent = current_this.closest(
								'.wcf-traffic-slider-wrap'
							),
							current_value = parseInt( this.value ),
							current_variation_id = range_parent.data(
								'variation-id'
							),
							prev_diff =
								variations[ current_variation_id ] -
								current_value;

						/* Set variation and it's value */
						variations[ current_variation_id ] = current_value;

						/* Update value and attr */
						current_this.attr( 'value', current_value );
						range_input
							.val( current_value )
							.attr( 'value', current_value );

						/* Update other variations traffic */
						update_variations_traffic(
							settings_parent,
							current_variation_id,
							prev_diff
						);
					} );

					range_input.on( 'input', function ( event ) {
						let current_this = $( this ),
							settings_parent = current_this.closest(
								'.wcf-ab-settings-content'
							),
							range_parent = current_this.closest(
								'.wcf-traffic-slider-wrap'
							),
							current_value = parseInt( this.value ),
							current_variation_id = range_parent.data(
								'variation-id'
							),
							prev_diff =
								variations[ current_variation_id ] -
								current_value;

						/* Set variation and it's value */
						variations[ current_variation_id ] = current_value;

						/* Update value and attr */
						current_this.attr( 'value', current_value );
						range
							.val( current_value )
							.attr( 'value', current_value );

						/* Update other variations traffic */
						update_variations_traffic(
							settings_parent,
							current_variation_id,
							prev_diff
						);
					} );
				} );

				var update_variations_traffic = function (
					parent,
					changed_variation_id,
					diff
				) {
					$.each( variations, function ( variation_id, traffic ) {
						//Ignores the selected variation
						if (
							parseInt( variation_id ) === changed_variation_id
						) {
							return true;
						}

						var new_value = traffic + diff;

						if ( new_value < 0 ) {
							diff = new_value;
							new_value = 0;
						} else if ( new_value > 100 ) {
							diff = new_value - 100;
							new_value = 100;
						} else {
							diff = 0;
						}

						variations[ variation_id ] = new_value;

						parent
							.find(
								'.wcf-traffic-range-' + variation_id + ' input'
							)
							.val( new_value )
							.attr( 'value', new_value );
						parent
							.find(
								'.wcf-traffic-value-' + variation_id + ' input'
							)
							.val( new_value )
							.attr( 'value', new_value );
					} );
				};
			};

			var save_ab_settings = function () {
				$( '.wcf-ab-test-settings-overlay .wcf-ab-test-save' ).on(
					'click',
					function ( e ) {
						e.preventDefault();

						var $this = $( this ),
							old_text = $this.text(),
							parent = $this.closest(
								'.wcf-ab-test-settings-overlay'
							),
							content_wrap_html = parent
								.find( '.wcf-content-wrap' )
								.html(),
							post_id = $( 'form#post #post_ID' ).val(),
							step_id = parent
								.find( '.wcf-ab-settings-content' )
								.attr( 'data-id' ),
							form_data = decodeURIComponent(
								parent
									.find(
										'input[name], select[name], textarea[name]'
									)
									.serialize()
							);

						$this.addClass( 'updating-message' );

						$.ajax( {
							url: ajaxurl,
							data: {
								action: 'cartflows_save_ab_test_settings',
								flow_id: post_id,
								step_id: step_id,
								form_data: form_data,
								security:
									cartflows_admin.wcf_save_ab_test_settings_nonce,
							},
							dataType: 'json',
							type: 'POST',
							success: function ( data ) {
								$( '.wcf-content-wrap-' + step_id ).html(
									content_wrap_html
								);

								$this.text( 'Updated' );
								$this.removeClass( 'updating-message' );

								setTimeout( function () {
									parent.hide();
									$this.text( old_text );
								}, 1000 );
							},
						} );
					}
				);
			};

			show_close_ab_settings();
			save_ab_settings();
		};

		ab_settings_all_events();
	};

	$( function () {
		_show_image_field_bump_offer_event();

		_show_image_field_bump_offer();

		wcf_sort_flow();
		wcf_step_delete_sortable();

		wcf_remove_step_tags();

		wcf_flow_analytics();
		wcf_reset_flow_analytics();

		wcf_ab_test_events();
	} );
} )( jQuery );
