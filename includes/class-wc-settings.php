<?php

defined( 'ABSPATH' ) || exit;

class Alfabank_Buy_Easy_WC_Settings {

	/**
	 * Bootstraps the class and hooks required actions
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_alfabank_buy_easy_settings', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_alfabank_buy_easy_settings', __CLASS__ . '::update_settings' );
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Alfabank Buy Easy tab.
	 *
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Alfabank Buy Easy tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['alfabank_buy_easy_settings'] = __( 'Alfabank Buy Easy', 'wc-alfabank-buy-easy' );

		return $settings_tabs;
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	public static function settings_tab() {
		woocommerce_admin_fields( self::get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @return array Array of settings for @see woocommerce_admin_fields() function.
	 *
	 * @see woocommerce_admin_fields() function.
	 *
	 */
	public static function get_settings() {
		$settings = array(
			// Alfabank Buy Easy API section.
			'api_section_title'        => array(
				'id'   => 'woocommerce_alfabank_buy_easy_api_section_title',
				'name' => __( 'API Settings', 'wc-alfabank-buy-easy' ),
				'type' => 'title',
				'desc' => __( 'Unique identifiers of the store.', 'wc-alfabank-buy-easy' ),
			),
			'shopINN'                  => array(
				'id'   => 'woocommerce_alfabank_buy_easy_shopINN',
				'name' => __( 'Shop INN', 'wc-alfabank-buy-easy' ),
				'desc' => __( '12 characters maximum.', 'wc-alfabank-buy-easy' ),
				'type' => 'text',
			),
			'api_section_end'          => array(
				'id'   => 'woocommerce_alfabank_buy_easy_api_section_end',
				'type' => 'sectionend',
			),

			// Front settings section.
			'front_section_title'      => array(
				'id'   => 'woocommerce_alfabank_buy_easy_front_section_title',
				'name' => __( 'Customization', 'wc-alfabank-buy-easy' ),
				'type' => 'title',
				'desc' => __( 'Buy Easy button appearance settings.', 'wc-alfabank-buy-easy' ),
			),
			'buttonName'               => array(
				'id'       => 'woocommerce_alfabank_buy_easy_buttonName',
				'name'     => __( 'Buy Easy Button Name', 'wc-alfabank-buy-easy' ),
				'desc_tip' => __( 'If you leave this field empty, the default name is \'Buy Easy\'.', 'wc-alfabank-buy-easy' ),
				'type'     => 'text',
			),
			'buttonOpenOnNewWindow'    => array(
				'id'      => 'woocommerce_alfabank_buy_easy_buttonOpenOnNewWindow',
				'title'   => __( 'Open On New Window', 'wc-alfabank-buy-easy' ),
				'desc'    => __( 'Open loan processing on a new window (tab)?', 'wc-alfabank-buy-easy' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			'front_section_end'        => array(
				'type' => 'sectionend',
				'id'   => 'woocommerce_alfabank_buy_easy_front_section_end',
			),

			// Output settings section.
			'display_section_title'    => array(
				'id'   => 'woocommerce_alfabank_buy_easy_output_section_title',
				'name' => __( 'Output', 'wc-alfabank-buy-easy' ),
				'type' => 'title',
				'desc' => __( 'Buy Easy button location settings.', 'wc-alfabank-buy-easy' ),
			),
			'buttonOnCart'             => array(
				'id'      => 'woocommerce_alfabank_buy_easy_buttonOnCart',
				'title'   => __( 'Cart Page', 'wc-alfabank-buy-easy' ),
				'desc'    => __( 'Display button on cart page?', 'wc-alfabank-buy-easy' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			'buttonOnCartLocation'     => array(
				'id'       => 'woocommerce_alfabank_buy_easy_buttonOnCartLocation',
				'title'    => __( 'Location on Cart Page', 'wc-alfabank-buy-easy' ),
				'desc_tip' => __( 'Button location on the cart page', 'wc-alfabank-buy-easy' ),
				'type'     => 'select',
				'default'  => 'woocommerce_before_cart_collaterals',
				'options'  => array(
					'woocommerce_before_cart'                    => __( 'Before the cart', 'wc-alfabank-buy-easy' ),
					'woocommerce_before_cart_collaterals'        => __( 'After the table of goods', 'wc-alfabank-buy-easy' ),
					'woocommerce_before_cart_totals'             => __( 'Before the cart totals title',
						'wc-alfabank-buy-easy' ),
					'woocommerce_cart_totals_before_order_total' => __( 'Before the cart totals', 'wc-alfabank-buy-easy' ),
					'woocommerce_proceed_to_checkout'            => __( 'Before the checkout button',
						'wc-alfabank-buy-easy' ),
					'woocommerce_after_cart'                     => __( 'After the cart', 'wc-alfabank-buy-easy' ),
				),
			),
			'buttonOnCheckout'         => array(
				'id'      => 'woocommerce_alfabank_buy_easy_buttonOnCheckout',
				'title'   => __( 'Checkout Page', 'wc-alfabank-buy-easy' ),
				'desc'    => __( 'Display button on checkout page?', 'wc-alfabank-buy-easy' ),
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			'buttonOnCheckoutLocation' => array(
				'id'       => 'woocommerce_alfabank_buy_easy_buttonOnCheckoutLocation',
				'title'    => __( 'Location on Checkout Page', 'wc-alfabank-buy-easy' ),
				'desc_tip' => __( 'Button location on the checkout page', 'wc-alfabank-buy-easy' ),
				'type'     => 'select',
				'default'  => 'woocommerce_after_checkout_form',
				'options'  => array(
					'woocommerce_before_checkout_form' => __( 'Before the checkout', 'wc-alfabank-buy-easy' ),
					'woocommerce_after_checkout_form'  => __( 'After the checkout', 'wc-alfabank-buy-easy' ),
				),
			),
			'display_section_end'      => array(
				'type' => 'sectionend',
				'id'   => 'woocommerce_alfabank_buy_easy_output_section_end',
			),
		);

		return apply_filters( 'woocommerce_alfabank_buy_easy_settings', $settings );
	}
}

Alfabank_Buy_Easy_WC_Settings::init();