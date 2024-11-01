<?php

defined( 'ABSPATH' ) || exit;

class Alfabank_Buy_Easy_Main {

	/**
	 * Bootstraps the class and hooks required actions
	 */
	public static function init() {
		register_activation_hook( ALFABANK_BUY_EASY_FILE, array( __CLASS__, 'activation' ) );
		register_uninstall_hook( ALFABANK_BUY_EASY_FILE, array( __CLASS__, 'uninstall' ) );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::assets' );
		add_filter( 'plugin_action_links_' . plugin_basename( ALFABANK_BUY_EASY_FILE ), __CLASS__ . '::add_plugin_page_settings_link' );
	}

	// Add basic settings on first plugin activation
	public static function activation() {
		add_option( 'woocommerce_alfabank_buy_easy_buttonOpenOnNewWindow', 'yes' );
		add_option( 'woocommerce_alfabank_buy_easy_buttonOnCart', 'yes' );
		add_option( 'woocommerce_alfabank_buy_easy_buttonOnCartLocation', 'woocommerce_before_cart_collaterals' );
		add_option( 'woocommerce_alfabank_buy_easy_buttonOnCheckout' );
		add_option( 'woocommerce_alfabank_buy_easy_buttonOnCheckoutLocation', 'woocommerce_before_checkout_form' );
	}

	// Remove settings on the plugin uninstall
	public static function uninstall() {
		delete_option( 'woocommerce_alfabank_buy_easy_shopINN' );
		delete_option( 'woocommerce_alfabank_buy_easy_buttonName' );
		delete_option( 'woocommerce_alfabank_buy_easy_buttonOpenOnNewWindow' );
		delete_option( 'woocommerce_alfabank_buy_easy_buttonOnCart' );
		delete_option( 'woocommerce_alfabank_buy_easy_buttonOnCartLocation' );
		delete_option( 'woocommerce_alfabank_buy_easy_buttonOnCheckout' );
		delete_option( 'woocommerce_alfabank_buy_easy_buttonOnCheckoutLocation' );
	}

	// Enqueue assets on the cart page only
	public static function assets() {
		if ( is_cart() ) {
			wp_enqueue_script(
				ALFABANK_BUY_EASY_SLUG,
				plugin_dir_url( ALFABANK_BUY_EASY_FILE ) . 'assets/js/wc-alfabank-buy-easy.js',
				array( 'jquery', 'wc-add-to-cart-variation' ),
				ALFABANK_BUY_EASY_VERSION,
				true
			);
		}
	}

	// Add plugin page settings link
	public static function add_plugin_page_settings_link( $links ) {
		$links[] = '<a href="' .
		           admin_url( 'admin.php?page=wc-settings&tab=alfabank_buy_easy_settings' ) .
		           '">' . __( 'Settings', 'wc-alfabank-buy-easy' ) . '</a>';

		return $links;
	}
}

Alfabank_Buy_Easy_Main::init();