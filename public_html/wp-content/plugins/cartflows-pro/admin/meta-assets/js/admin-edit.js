( function ( $ ) {
	/* Custom Fields Type, change, update */
	var wcf_custom_field_editor_setup = function () {
		var wcf_on_ready_toggle_options = function () {
			$( '.wcf-field-item-settings-require input[type="checkbox"]' ).each(
				function ( e ) {
					var $this = $( this ),
						parent = $this.closest( '.wcf-field-item-settings' );

					if ( this.checked ) {
						parent
							.find( '.wcf-field-item-settings-optimized' )
							.hide();
					}
				}
			);

			$( '.wcf-field-item-settings-require input[type="checkbox"]' ).on(
				'change',
				function ( e ) {
					var $this = $( this ),
						parent = $this.closest( '.wcf-field-item-settings' );

					if ( this.checked ) {
						parent
							.find( '.wcf-field-item-settings-optimized' )
							.hide();
					} else {
						parent
							.find( '.wcf-field-item-settings-optimized' )
							.show();
					}
				}
			);
		};

		var wcf_custom_fields_select_option_events = function () {
			/* Ready */
			wcf_custom_fields_select_option();

			/* Change Custom Field*/
			$(
				'.wcf-column-right .wcf-custom-field-box .wcf-cpf-type select'
			).on( 'change', function ( e ) {
				wcf_custom_fields_select_option();
			} );
		};

		var wcf_custom_fields_select_option = function () {
			var wrap = $( '.wcf-custom-field-box' ),
				data_key = wrap
					.find( '.wcf-cpf-wrap .wcf-cpf-row' )
					.attr( 'data-key' ),
				select_field = wrap.find( '.wcf-cpf-type select' ),
				select_value = select_field.val(),
				option_field = wrap.find( '.wcf-cpf-options' ),
				placeholder_field = wrap.find( '.wcf-cpf-placeholder' );

			var default_field = wrap.find(
				'.wcf-cpf-default .wcf-cpf-row-setting-field'
			);
			var add_default_checkbox_select_field =
				'<select name="wcf-checkout-custom-fields[' +
				data_key +
				'][default]" class="wcf-cpf-default-checkbox"><option value="1">Checked</option><option value="0">Un-Checked</option></select>';
			var add_default_field =
				'<input type="text" value="" name="wcf-checkout-custom-fields[' +
				data_key +
				'][default]" class="wcf-cpf-default"><span id="wcf-cpf-default-error-msg"></span>';

			var optimized_field = wrap.find( '.wcf-cpf-optimized' );

			wrap.find(
				'.wcf-cpf-fields.wcf-cpf-required input[type="checkbox"].wcf-cpf-required'
			).on( 'change', function () {
				this.checked ? optimized_field.hide() : optimized_field.show();
			} );

			if ( 'select' == select_value ) {
				option_field.show();
				placeholder_field.hide();
				default_field.empty();
				default_field.append( add_default_field );
			} else if ( 'checkbox' == select_value ) {
				placeholder_field.hide();
				option_field.hide();
				default_field.empty();
				default_field.append( add_default_checkbox_select_field );
			} else if ( 'hidden' == select_value ) {
				placeholder_field.hide();
				option_field.hide();
				default_field.empty();
				default_field.append( add_default_field );
			} else {
				option_field.hide();
				placeholder_field.show();
				default_field.empty();
				default_field.append( add_default_field );
			}
		};

		var wcf_field_reordering = function () {
			// For Billing Fields
			$( '#wcf-billing-field-sortable' ).sortable( {
				forcePlaceholderSize: true,
				placeholder: 'sortable-placeholder',
			} );
			$( '#wcf-billing-field-sortable' ).disableSelection();

			// For Shipping Fields
			$( '#wcf-shipping-field-sortable' ).sortable( {
				forcePlaceholderSize: true,
				placeholder: 'sortable-placeholder',
			} );

			$( '#wcf-shipping-field-sortable' ).disableSelection();

			$( this )
				.closest( '.wcf-field-item' )
				.toggleClass( 'wcf-field-item-edit-active' );
			$( this )
				.closest( '.wcf-field-item' )
				.siblings()
				.removeClass( 'wcf-field-item-edit-active' );
			var action_btn = $( this )
				.closest( '.wcf-field-item' )
				.find( '.item-edit' );
			action_btn.removeClass( '.item-edit' );
			action_btn.addClass( 'item-edit-close' );
		};

		var enable_diable_custom_field = function () {
			var get_label_class = $( this )
					.closest( 'label' )
					.hasClass( 'dashicons-visibility' ),
				get_label = $( this ).closest( 'label' ),
				get_item_bar = get_label.closest( '.wcf-field-item-bar' );

			if ( get_label_class ) {
				$( this )
					.closest( 'label' )
					.removeClass( 'dashicons-visibility' );
				$( this ).closest( 'label' ).addClass( 'dashicons-hidden ' );
				get_item_bar.addClass( 'disable' );
			} else {
				$( this ).closest( 'label' ).removeClass( 'dashicons-hidden ' );
				$( this ).closest( 'label' ).addClass( 'dashicons-visibility' );
				get_item_bar.removeClass( 'disable' );
			}
		};

		var wcf_add_pro_custom_field = function ( e ) {
			e.preventDefault();

			var $this = $( this ),
				save_field_name = $this.attr( 'data-name' ),
				wrap = $this.closest( '.wcf-cpf-wrap' ),
				post_id = $( '#post_ID' ).val(),
				add_to =
					wrap
						.find( '.wcf-cpf-fields.wcf-cpf-add_to select' )
						.val() || '',
				type =
					wrap.find( '.wcf-cpf-fields.wcf-cpf-type select' ).val() ||
					'',
				options =
					wrap
						.find( '.wcf-cpf-fields.wcf-cpf-options textarea' )
						.val() || '',
				label =
					wrap.find( '.wcf-cpf-fields.wcf-cpf-label input' ).val() ||
					'',
				width =
					wrap.find( '.wcf-cpf-fields.wcf-cpf-width select' ).val() ||
					'',
				placeholder = '',
				is_required =
					wrap
						.find(
							'.wcf-cpf-fields.wcf-cpf-required input[type="checkbox"]:checked'
						)
						.val() || '',
				is_optimized =
					wrap
						.find(
							'.wcf-cpf-fields.wcf-cpf-optimized input[type="checkbox"]:checked'
						)
						.val() || '',
				default_value = '';
			// name    	= wrap.find( '.wcf-cpf-fields.wcf-cpf-name input' ).val() || '';

			if ( 'checkbox' == type ) {
				default_value =
					wrap
						.find( '.wcf-cpf-fields.wcf-cpf-default select' )
						.val() || '';
			} else {
				default_value =
					wrap
						.find( '.wcf-cpf-fields.wcf-cpf-default input' )
						.val() || '';
			}

			if ( 'select' != type ) {
				placeholder =
					wrap
						.find( '.wcf-cpf-fields.wcf-cpf-placeholder input' )
						.val() || '';
			}

			if ( '' === label ) {
				wrap.find( '.wcf-cpf-fields.wcf-cpf-label input' ).trigger(
					'focus'
				);
				wrap.find( '.wcf-cpf-fields.wcf-cpf-label input' ).addClass(
					'error'
				);
				wrap.find(
					'.wcf-cpf-fields.wcf-cpf-label #wcf-cpf-label-error-msg'
				)
					.text( ' This field is required' )
					.css( 'color', 'red' );
				return;
			}

			// if( '' === name ) {
			// 	wrap.find( '.wcf-cpf-fields.wcf-cpf-name input' ).focus();
			// 	return;
			// }

			$this.addClass( 'updating-message' ).text( 'Adding Field..' );

			$.ajax( {
				url: wcf.ajax_url,
				data: {
					action: 'wcf_pro_add_custom_checkout_field',
					post_id: post_id,
					save_field_name: save_field_name,
					add_to: add_to,
					type: type,
					options: options,
					label: label,
					// name    : name,
					placeholder: placeholder,
					width: width,
					default: default_value,
					required: is_required,
					security:
						cartflows_admin.wcf_pro_add_custom_checkout_field_nonce,
					optimized: is_optimized,
				},
				dataType: 'json',
				type: 'POST',
				success: function ( data ) {
					console.log( data );
					if ( $( '.' + data.add_to_class ).length ) {
						$( '.' + data.add_to_class ).append( data.markup );

						var new_row = $(
							'.wcf-field-row.field-' + data.field_args.name
						);

						$this
							.removeClass( 'updating-message' )
							.text( 'Added Field!' );
						setTimeout( function () {
							wrap.find(
								'.wcf-cpf-fields input[type="text"]'
							).val( '' );
							wrap.find( '.wcf-cpf-fields textarea' ).val( '' );
							$this.text( 'Add New Field' );
						}, 1500 );

						if (
							$( '.wcf-field-row.field-' + data.field_args.name )
								.length
						) {
							$( 'html, body' ).animate(
								{
									scrollTop: $(
										'.wcf-field-row.field-' +
											data.field_args.name
									).offset().top,
								},
								800
							);
						}
					}
				},
			} );
		};

		var wcf_remove_custom_field = function ( e ) {
			e.preventDefault();

			var $this = $( this ),
				wrap = $this.closest( '.wcf-cpf-actions' ),
				post_id = $( '#post_ID' ).val(),
				type = wrap.data( 'type' ),
				key = wrap.data( 'key' ),
				row = $this.parents( '.wcf-field-item-edit-active' );

			if ( row.length < 1 ) {
				row = $this.parents( '.wcf-field-row' );
			}

			var delete_status = confirm(
				'This action will delete this field. Are you sure?'
			);
			if ( true == delete_status ) {
				$this
					.addClass( 'wp-ui-text-notification' )
					.text( 'Removing..' );

				$.ajax( {
					url: wcf.ajax_url,
					data: {
						action: 'wcf_pro_delete_custom_checkout_field',
						post_id: post_id,
						type: type,
						key: key,
						security:
							cartflows_admin.wcf_pro_delete_custom_checkout_field_nonce,
					},
					dataType: 'json',
					type: 'POST',
					success: function ( data ) {
						row.slideUp( 400, 'swing', function () {
							row.remove();
						} );
					},
				} );
			}
		};

		/* Init functions */
		wcf_field_reordering();
		wcf_on_ready_toggle_options();
		wcf_custom_fields_select_option_events();

		/* On change event */
		$( document ).on(
			'click',
			'.wcf-field-item-edit-inactive .wcf-field-item-handle .item-controls a',
			wcf_field_reordering
		);
		$( document ).on(
			'click',
			'.wcf-field-item-edit-inactive .wcf-field-item-handle label.dashicons',
			enable_diable_custom_field
		);

		$( document ).on(
			'click',
			'.wcf-pro-custom-field-add',
			wcf_add_pro_custom_field
		);
		$( document ).on(
			'click',
			'.wcf-pro-custom-field-remove',
			wcf_remove_custom_field
		);
	};

	var wcf_optin_fields_toggle = function () {
		/* Custom Fields Hide / Show */
		var wcf_custom_fields_events = function () {
			/* Ready */
			wcf_custom_fields();

			/* Change Custom Field*/
			$(
				'.wcf-optin-custom-fields .field-wcf-optin-enable-custom-fields input:checkbox'
			).on( 'change', function ( e ) {
				wcf_custom_fields();
			} );
		};

		/* Disable/Enable Custom Field section*/
		var wcf_custom_fields = function () {
			var wrap = $( '.wcf-optin-custom-fields' ),
				custom_fields = wrap.find(
					'.field-wcf-optin-enable-custom-fields input:checkbox'
				);

			var field_names = [
				'.wcf-optin-custom-fields-editor',
				'.wcf-custom-field-box',
			];

			if ( custom_fields.is( ':checked' ) ) {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).show();
				} );
			} else {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).hide();
				} );
			}
		};

		/* Start Events */
		wcf_custom_fields_events();
	};

	/* Bump Order Fields Hide / Show */
	var wcf_bump_order_fields_events = function () {
		var wcf_toggle_bump_product_quantity = function () {
			if (
				$( '.field-wcf-order-bump-product' ).find(
					'span.select2-selection__clear'
				).length
			) {
				$( '.field-wcf-order-bump-product-quantity' ).show();
			} else {
				$( '.field-wcf-order-bump-product-quantity' ).hide();
				$( 'input[name="wcf-order-bump-product-quantity"]' ).val( 1 );
			}
		};

		var wcf_bump_order_fields = function ( on_ready ) {
			var wrap = $( '.wcf-checkout-table' ),
				bump_order = wrap.find(
					'.field-wcf-order-bump input:checkbox'
				),
				enable_bump_arrow = wrap.find(
					'.wcf-product-order-bump .field-wcf-show-bump-arrow input:checkbox'
				);

			var field_names = [
				'.field-wcf-order-bump-style',
				'.field-wcf-order-bump-product',
				'.field-wcf-order-bump-position',
				'.field-wcf-order-bump-image',
				'.field-wcf-show-bump-image-mobile',
				'.field-wcf-order-bump-label',
				'.field-wcf-order-bump-hl-text',
				'.field-wcf-order-bump-desc',
				'.field-wcf-order-bump-discount',
				'.field-wcf-order-bump-discount-value',
				'.field-wcf-order-bump-discount-coupon',
				'.wcf-cs-bump-options',
				'.wcf-discount-price-notice',
				'.field-wcf-bump-original-price',
				'.field-wcf-bump-discount-price',
				'.field-wcf-order-bump-product-quantity',
				'.wcf-field-section',
				'.field-wcf-order-bump-replace',
				'.field-wcf-ob-yes-next-step',
				'.wcf-order-bump-replace-note',
			];

			if ( bump_order.is( ':checked' ) ) {
				$.each( field_names, function ( i, val ) {
					if ( '.field-wcf-order-bump-discount-value' === val ) {
						var discount_type = wrap
							.find( '.field-wcf-order-bump-discount select' )
							.val();

						if (
							'discount_percent' === discount_type ||
							'discount_price' === discount_type
						) {
							wrap.find( val ).show();
						} else {
							wrap.find( val ).hide();
						}
					} else if (
						'.field-wcf-order-bump-discount-coupon' === val
					) {
						var discount_type = wrap
							.find( '.field-wcf-order-bump-discount select' )
							.val();

						if ( 'coupon' === discount_type ) {
							wrap.find( val ).show();
						} else {
							wrap.find( val ).hide();
						}
					} else if (
						'.field-wcf-order-bump-product-quantity' === val
					) {
						wcf_toggle_bump_product_quantity();
					} else {
						wrap.find( val ).show();
					}
				} );

				$(
					'.wcf-checkout-design-table [data-tab=wcf-product-order-bump]'
				).show();
				if ( on_ready !== true ) {
					$(
						'.wcf-checkout-design-table [data-tab=wcf-product-order-bump]'
					).trigger( 'click' );
				}
			} else {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).hide();
				} );

				$(
					'.wcf-checkout-design-table [data-tab=wcf-product-order-bump]'
				).hide();
				if ( on_ready !== true ) {
					$(
						'.wcf-checkout-design-table [data-tab=wcf-checkout-style]'
					).trigger( 'click' );
				}
			}

			/* Enable & disable the Bump order's blinking arrow checkboxes. */

			/* Pre load the setting */
			enable_blinking_arrow_setting();

			$( enable_bump_arrow ).on( 'change', function ( e ) {
				enable_blinking_arrow_setting();
			} );

			/* Enable the blinking arrow and its settings */
			function enable_blinking_arrow_setting() {
				var bump_arrow_fields = [
					'.field-wcf-show-bump-animate-arrow',
				];

				if ( enable_bump_arrow.is( ':checked' ) ) {
					$.each( bump_arrow_fields, function ( i, val ) {
						wrap.find( val ).show();
					} );
				} else {
					$.each( bump_arrow_fields, function ( i, val ) {
						wrap.find( val ).hide();
					} );
				}
			}
			/* Enable the blinking arrow and its settings */

			/* Enable & disable the Bump order's blinking arrow checkboxes. */
		};

		/* Ready */
		wcf_bump_order_fields( true );

		/* Change Order Bump*/
		$( '.wcf-checkout-table .field-wcf-order-bump input:checkbox' ).on(
			'change',
			function ( e ) {
				wcf_bump_order_fields();
			}
		);

		/* Change Discount Type*/
		$( '.wcf-checkout-table .field-wcf-order-bump-discount select' ).on(
			'change',
			function ( e ) {
				wcf_bump_order_fields();
			}
		);

		/* Quantity */
		// $(".field-wcf-order-bump-product").on('change', function () {
		// 	wcf_toggle_bump_product_quantity();
		// });
	};

	var wcf_offer_fields_events = function () {
		var wcf_offer_fields = function () {
			var wrap = $( '.wcf-offer-table' );

			if ( wrap.length < 1 ) {
				return;
			}

			var field_names = [ '.field-wcf-offer-discount-value' ];

			$.each( field_names, function ( i, val ) {
				if ( '.field-wcf-offer-discount-value' === val ) {
					var discount_type = wrap
						.find( '.field-wcf-offer-discount select' )
						.val();

					if (
						'discount_percent' === discount_type ||
						'discount_price' === discount_type
					) {
						wrap.find( val ).show();
					} else {
						wrap.find( val ).hide();
					}
				}
			} );
		};

		/* Ready */
		wcf_offer_fields();

		/* Change Discount Type*/
		$( '.wcf-offer-table .field-wcf-offer-discount select' ).on(
			'change',
			function ( e ) {
				wcf_offer_fields();
			}
		);
	};

	/* Hide / Show Product Variation Fields Event */
	var wcf_product_variation_fields_events = function () {
		/* Disable/Enable Product Variation Field section */
		var wcf_product_variation_fields = function ( on_ready ) {
			var wrap = $( '.wcf-checkout-table' ),
				custom_fields = wrap.find(
					'.wcf-column-right .wcf-product-options .wcf-pv-checkboxes .field-wcf-enable-product-options input:checkbox'
				);

			var field_names = [ '.wcf-pv-fields' ];

			var pv_options_chkbox = wrap.find(
				'.wcf-column-right .wcf-product-options .wcf-pv-fields .field-wcf-enable-product-variation input:checkbox'
			);

			var pv_options = [ '.field-wcf-product-variation-options' ];

			if ( pv_options_chkbox.is( ':checked' ) ) {
				$.each( pv_options, function ( i, val ) {
					wrap.find( val ).show();
				} );
			} else {
				$.each( pv_options, function ( i, val ) {
					wrap.find( val ).hide();
				} );
			}

			if ( custom_fields.is( ':checked' ) ) {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).show();
				} );

				$(
					'.wcf-checkout-design-table [data-tab=wcf-product-options]'
				).show();
				if ( on_ready !== true ) {
					$(
						'.wcf-checkout-design-table [data-tab=wcf-product-options]'
					).trigger( 'click' );
				}
			} else {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).hide();
				} );

				$(
					'.wcf-checkout-design-table [data-tab=wcf-product-options]'
				).hide();
				if ( on_ready !== true ) {
					$(
						'.wcf-checkout-design-table [data-tab=wcf-checkout-style]'
					).trigger( 'click' );
				}
			}
		};

		/* Ready */
		wcf_product_variation_fields( true );

		/* Change product variation Fields*/
		$(
			'.wcf-column-right .wcf-product-options .wcf-pv-checkboxes .field-wcf-enable-product-options input:checkbox'
		).on( 'change', function ( e ) {
			wcf_product_variation_fields();
		} );

		$(
			'.wcf-column-right .wcf-product-options .wcf-pv-fields .field-wcf-enable-product-variation input:checkbox'
		).on( 'change', function ( e ) {
			wcf_product_variation_fields();
		} );
	};

	/* Advance Style Fields Hide / Show */
	var wcf_pro_advance_style_fields_events = function () {
		/* Disable/Enable Pro Advance Style Field section for the two step */
		var wcf_two_step_style_fields = function () {
			var wrap = $( '.wcf-checkout-design-table' );

			var field_names = [ '.wcf-checkout-two-step' ];

			var layout_style = $( 'select[name="wcf-checkout-layout"]' ).val();

			if ( layout_style === 'two-step' ) {
				$.each( field_names, function ( i, val ) {
					wrap.find( '[data-tab=wcf-checkout-two-step]' ).show();
				} );
			} else {
				$.each( field_names, function ( i, val ) {
					wrap.find( '[data-tab=wcf-checkout-two-step]' ).hide();
				} );
			}
		};

		/* Show/Hide Note text box */
		var wcf_two_step_note_box = function () {
			var wrap = $( '.wcf-checkout-design-table' ),
				custom_fields = wrap.find(
					'.wcf-column-right .wcf-checkout-two-step .field-wcf-checkout-box-note input:checkbox'
				);

			var field_names = [
				'.field-wcf-checkout-box-note-text',
				'.field-wcf-checkout-box-note-text-color',
				'.field-wcf-checkout-box-note-bg-color',
			];

			if ( custom_fields.is( ':checked' ) ) {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).show();
				} );
			} else {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).hide();
				} );
			}
		};

		/* Ready */
		wcf_two_step_style_fields();

		wcf_two_step_note_box();

		/* Select Advance Style Field */
		$(
			'.wcf-column-right .wcf-checkout-style .wcf-cs-fields .field-wcf-checkout-layout select[name="wcf-checkout-layout"]'
		).on( 'change', function ( e ) {
			wcf_two_step_style_fields();
		} );

		/* Hide/Show checkout note box on check of enable checkout note checkbox */
		$(
			'.wcf-column-right .wcf-checkout-two-step .field-wcf-checkout-box-note input:checkbox'
		).on( 'change', function ( e ) {
			wcf_two_step_note_box();
		} );
	};

	/* Bump Order Fields Hide / Show */
	var wcf_pre_checkout_offer_fields_events = function () {
		var wcf_pre_checkout_offer_fields = function ( on_ready ) {
			var wrap = $( '.wcf-checkout-table' ),
				pre_checkout_offer = wrap.find(
					'.field-wcf-pre-checkout-offer input:checkbox'
				);

			var field_names = [
				'.field-wcf-pre-checkout-offer-product',
				'.field-wcf-pre-checkout-offer-product-title',
				'.field-wcf-pre-checkout-offer-desc',
				'.field-wcf-pre-checkout-offer-popup-title',
				'.field-wcf-pre-checkout-offer-popup-sub-title',
				'.field-wcf-pre-checkout-offer-discount',
				'.field-wcf-pre-checkout-offer-discount-value',
				'.wcf-pre-checkout-offer-price-notice',
				'.field-wcf-pre-checkout-offer-original-price',
				'.field-wcf-pre-checkout-offer-discount-price',
				'.field-wcf-pre-checkout-offer-popup-btn-text',
				'.field-wcf-pre-checkout-offer-popup-skip-btn-text',
				'.field-wcf-pre-checkout-offer-bg-color',
				'.wcf-cs-pre-checkout-offer-options',
			];

			if ( pre_checkout_offer.is( ':checked' ) ) {
				$.each( field_names, function ( i, val ) {
					if (
						'.field-wcf-pre-checkout-offer-discount-value' === val
					) {
						var discount_type = wrap
							.find(
								'.field-wcf-pre-checkout-offer-discount select'
							)
							.val();

						if (
							'discount_percent' === discount_type ||
							'discount_price' === discount_type
						) {
							wrap.find( val ).show();
						} else {
							wrap.find( val ).hide();
						}
					} else {
						wrap.find( val ).show();
					}
				} );

				/* Design tab */
				$(
					'.wcf-checkout-design-table [data-tab=wcf-pre-checkout-offer]'
				).show();
				if ( on_ready !== true ) {
					$(
						'.wcf-checkout-design-table [data-tab=wcf-pre-checkout-offer]'
					).trigger( 'click' );
				}
			} else {
				$.each( field_names, function ( i, val ) {
					wrap.find( val ).hide();
				} );

				$(
					'.wcf-checkout-design-table [data-tab=wcf-pre-checkout-offer]'
				).hide();
				if ( on_ready !== true ) {
					$(
						'.wcf-checkout-design-table [data-tab=wcf-checkout-style]'
					).trigger( 'click' );
				}
			}
		};

		/* Ready */
		wcf_pre_checkout_offer_fields( true );

		/* Change Pre-checkout Offer*/
		$(
			'.wcf-checkout-table .field-wcf-pre-checkout-offer input:checkbox'
		).on( 'change', function ( e ) {
			wcf_pre_checkout_offer_fields();
		} );

		/* Change Discount Type*/
		$(
			'.wcf-checkout-table .field-wcf-pre-checkout-offer-discount select'
		).on( 'change', function ( e ) {
			wcf_pre_checkout_offer_fields();
		} );
	};

	var wcf_toggle_optimize_fields = function () {
		var optimized_fields = {
			'wcf-show-coupon-field': 'field-wcf-optimize-coupon-field',
			'wcf-checkout-additional-fields':
				'field-wcf-optimize-order-note-field',
		};

		$.each(
			optimized_fields,
			function ( optimized_field_parent, optimized_field_child ) {
				$( '.' + optimized_field_child ).toggle(
					$( '#' + optimized_field_parent ).is( ':checked' )
				);
			}
		);

		$( '#wcf-show-coupon-field' ).on( 'change', function () {
			$( '.field-wcf-optimize-coupon-field' ).toggle(
				$( '#wcf-show-coupon-field' ).is( ':checked' )
			);
		} );

		$( '#wcf-checkout-additional-fields' ).on( 'change', function () {
			$( '.field-wcf-optimize-order-note-field' ).toggle(
				$( '#wcf-checkout-additional-fields' ).is( ':checked' )
			);
		} );
	};

	/* Animate browser tab toggle fields */
	var wcf_animate_tab_toggle_fields = function () {
		var wrap = $( '.wcf-checkout-table' );

		if ( wrap.length < 1 ) {
			return;
		}

		var wcf_animate_tab_fields = function () {
			var field_names = [ '.field-wcf-animate-browser-tab-title' ];

			$.each( field_names, function ( i, val ) {
				var field = wrap.find( 'input#wcf-animate-browser-tab' );

				if ( field.is( ':checked' ) ) {
					wrap.find( val ).show();
				} else {
					wrap.find( val ).hide();
				}
			} );
		};

		/* Ready */
		wcf_animate_tab_fields();

		/* Change Discount Type*/
		wrap.find( 'input#wcf-animate-browser-tab' ).on(
			'change',
			function ( e ) {
				wcf_animate_tab_fields();
			}
		);
	};

	/* Offer Upsell / Downsell variation product fields */

	/* Hide / Show Product Variation Fields Event */
	var wcf_offer_product_variation_field_events = function () {
		/* Hide/Show product variation vield section */
		var wcf_offer_product_variation_fields = function () {
			var wrap = $( '.wcf-offer-table' ),
				pv_options_chkbox = wrap.find(
					'.wcf-column-right .wcf-offer-general .field-wcf-enable-offer-product-variation input:checkbox'
				);

			var pv_options = [ '.field-wcf-offer-product-variation-options' ];

			if ( pv_options_chkbox.is( ':checked' ) ) {
				$.each( pv_options, function ( i, val ) {
					wrap.find( val ).show();
				} );
			} else {
				$.each( pv_options, function ( i, val ) {
					wrap.find( val ).hide();
				} );
			}
		};

		/* Ready */
		wcf_offer_product_variation_fields();

		/* Change product variation Fields*/
		$(
			'.wcf-column-right .wcf-offer-general .field-wcf-enable-offer-product-variation input:checkbox'
		).on( 'change', function ( e ) {
			wcf_offer_product_variation_fields();
		} );
	};

	var wcf_hide_highlight_field = function () {
		$( document ).on( 'ready', function ( e ) {
			var wrap = $( '.wcf-product-option-fields' );
			wrap.find( '.wcf-field-enable-highlight-option-field input' ).each(
				function ( e ) {
					var $this = $( this ),
						parent = $this.closest( '.wcf-product-option-fields' );
					show_hide_highlight_field( $this, parent );
				}
			);
		} );

		$( document ).on(
			'change',
			'.wcf-field-enable-highlight-option-field input',
			function ( e ) {
				var $this = $( this ),
					parent = $this.closest( '.wcf-product-option-fields' );
				show_hide_highlight_field( $this, parent );
			}
		);

		var show_hide_highlight_field = function ( $this, parent ) {
			if ( $this.is( ':checked' ) ) {
				parent.find( '.wcf-field-highlight-text-field' ).show();
			} else {
				parent.find( '.wcf-field-highlight-text-field' ).hide();
			}
		};
	};

	var wcf_toggle_product_options = function () {
		$( document.body ).on(
			'click',
			'.wcf-product-field-item-bar',
			function ( e ) {
				var $this = $( this ),
					parent = $this.next( '.wcf-product-field-item-settings' );
				$( parent ).toggle();
			}
		);
	};

	var wcf_sync_products = function () {
		$( document.body ).on(
			'click',
			'[data-tab=wcf-product-options]',
			function () {
				var product_options_obj = [];

				$(
					'.wcf-product-option-fields .wcf-product-field-item-settings'
				).each( function ( e ) {
					var $this = $( this ),
						id = $this.parent().attr( 'data-product-id' ),
						title = $this
							.find( '.wcf-field-product-title-field input' )
							.val(),
						subtext = $this
							.find( '.wcf-field-subtext-field input' )
							.val(),
						highlight = $this.find(
							'.wcf-field-enable-highlight-option-field input[type="checkbox"]'
						),
						highlight_text = $this
							.find( '.wcf-field-highlight-text-field input' )
							.val(),
						po_unique_id = $this
							.find( '.wcf-product-options-unique-id' )
							.val();

					if ( highlight.is( ':checked' ) ) {
						highlight = 'yes';
					} else {
						highlight = 'no';
					}

					product_options_obj[ po_unique_id ] = {
						id: id,
						title: title,
						subtext: subtext,
						enable_highlight: highlight,
						highlight_text: highlight_text,
					};
				} );

				$( '#wcf-product-options-fields' ).empty();

				$( '.wcf-repeatable-row' ).each( function ( e ) {
					var repeatable_row = $( this ),
						product_input = repeatable_row.find(
							'select.wcf-product-search'
						),
						product_id = product_input.val(),
						name = product_input.find( 'option:selected' ).text(),
						template = $(
							'#tmpl-wcf-product-option-repeater'
						).html(),
						unique_id = repeatable_row
							.find(
								'.wcf-repeatable-row-unique-id-field input[type="hidden"]'
							)
							.val();

					template = template.replace(
						/{{original_product_id}}/g,
						product_id
					);
					template = template.replace(
						/{{original_product_name}}/g,
						name
					);
					template = template.replace( /{{unique_id}}/g, unique_id );

					var input_product_name = '',
						input_subtext = '',
						input_enable_highlight = 'no',
						input_highlight_text = '';

					if ( unique_id in product_options_obj ) {
						input_product_name =
							product_options_obj[ unique_id ][ 'title' ];
						input_subtext =
							product_options_obj[ unique_id ][ 'subtext' ];
						input_enable_highlight =
							product_options_obj[ unique_id ][
								'enable_highlight'
							];
						input_highlight_text =
							product_options_obj[ unique_id ][
								'highlight_text'
							];
					}

					template = template.replace(
						/{{input_product_name}}/g,
						input_product_name
					);
					template = template.replace(
						/{{input_subtext}}/g,
						input_subtext
					);
					template = template.replace(
						/{{input_highlight_text}}/g,
						input_highlight_text
					);

					var template_node = $( template );

					if ( 'yes' == input_enable_highlight ) {
						template_node
							.find(
								'.wcf-field-enable-highlight-option-field input[type="checkbox"]'
							)
							.attr( 'checked', 'checked' );
					} else {
						template_node
							.find( '.wcf-field-highlight-text-field' )
							.hide();
					}

					$( '#wcf-product-options-fields' ).append( template_node );
				} );
			}
		);
	};

	$( function () {
		/* Bump Order Show Hide Fields */
		wcf_bump_order_fields_events();

		/* Pre-checkout Offer Show Hide Fields */
		wcf_pre_checkout_offer_fields_events();

		/* Custom Fields Add Text area if the select option is selected */
		wcf_custom_field_editor_setup();

		/* Hide/Show Optin fields */
		wcf_optin_fields_toggle();

		/* Hide Show advance options of pro */
		wcf_pro_advance_style_fields_events();

		/* Upsell/Downsell Show Hide Fields */
		wcf_offer_fields_events();
		/* Enable/Disable Offer Product Variations Fields */
		wcf_offer_product_variation_field_events();

		/* Enable/Disable Product Variations Fields */
		wcf_product_variation_fields_events();

		/* Toggle coupon optimize field */
		wcf_toggle_optimize_fields();

		/* Animate FIelds */
		wcf_animate_tab_toggle_fields();

		/* Show/Hide Highlight product option */
		wcf_hide_highlight_field();

		wcf_toggle_product_options();

		wcf_sync_products();
	} );
} )( jQuery );
