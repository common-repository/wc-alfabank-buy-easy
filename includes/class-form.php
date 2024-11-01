<?php

defined( 'ABSPATH' ) || exit;

class Alfabank_Buy_Easy_Form {

	/**
	 * Minimal order value
	 */
	const PRICE_MIN_LIMIT = 10;

	/**
	 * Bootstraps the class and hooks required actions
	 */
	public static function init() {
		// Settings
		$buttonOnCartLocation     = get_option( 'woocommerce_alfabank_buy_easy_buttonOnCartLocation' ) ?
			esc_attr( get_option( 'woocommerce_alfabank_buy_easy_buttonOnCartLocation' ) ) :
			'woocommerce_before_cart_collaterals';
		$buttonOnCheckoutLocation = get_option( 'woocommerce_alfabank_buy_easy_buttonOnCheckoutLocation' ) ?
			esc_attr( get_option( 'woocommerce_alfabank_buy_easy_buttonOnCheckoutLocation' ) ) :
			'woocommerce_before_checkout_form';

		// Form integration
		add_action( $buttonOnCartLocation, __CLASS__ . '::cart' );
		add_action( $buttonOnCheckoutLocation, __CLASS__ . '::checkout' );

		// TODO - check
		// AJAX coupon handler
		add_action( 'wp_ajax_alfabank_buy_easy_when_coupon_apply', __CLASS__ . '::when_coupon_apply' );
		add_action( 'wp_ajax_nopriv_alfabank_buy_easy_when_coupon_apply', __CLASS__ . '::when_coupon_apply' );
	}

	/**
	 * Render Alfabank Buy Easy forms on cart page
	 */
	public static function cart() {

		if ( false !== Alfabank_Buy_Easy_Helpers::validate_checkbox_option( get_option( 'woocommerce_alfabank_buy_easy_buttonOnCart' ) ) ) {
			static::cart_and_checkout_forms();
		}
	}

	/**
	 * Render Alfabank Buy Easy forms on checkout page
	 */
	public static function checkout() {

		if ( false !== Alfabank_Buy_Easy_Helpers::validate_checkbox_option( get_option( 'woocommerce_alfabank_buy_easy_buttonOnCheckout' ) ) ) {
			static::cart_and_checkout_forms();
		}
	}

	/**
	 * Render Alfabank Buy Easy forms for cart and checkout pages
	 */
	public static function cart_and_checkout_forms() {

		$options = Alfabank_Buy_Easy_Helpers::get_options();
		// Total cart price with discounts but without delivery
		$cart_total = WC()->cart->get_cart_contents_total();

		if ( ! empty( $options['shopINN'] ) && $cart_total >= self::PRICE_MIN_LIMIT ) {

			$user      = Alfabank_Buy_Easy_Helpers::get_auth_user_info();
			$reference = date( 'dmy' ) . time();

			ob_start();
			?>

            <textarea name="InXML" style="display:none">
            <inParams>
              <companyInfo>
                <inn><?php echo substr( $options['shopINN'], 0, 12 ); ?></inn>
              </companyInfo>

              <creditInfo>
                <reference><?php echo $reference; ?></reference>
              </creditInfo>

              <clientInfo>
                <firstname><?php echo substr( $user['firstname'], 0, 35 ); ?></firstname>
                <lastname><?php echo substr( $user['lastname'], 0, 35 ); ?></lastname>
                <email><?php echo substr( $user['email'], 0, 140 ); ?></email>
              </clientInfo>

              <specificationList>
                <?php
                echo static::products_list(); ?>
              </specificationList>

            </inParams>
        </textarea>

			<?php

			$html = ob_get_contents();
			ob_end_clean();

			$button = '<input type="submit" class="alfabank_buy_easy_submit" value="' . $options['buttonName'] . '" formtarget="' . $options['onNewWindow'] . '"/>';

			$wrapper = '<form action="https://anketa.alfabank.ru/alfaform-pos/endpoint" method="post" enctype="application/x-www-form-urlencoded">%1$s%2$s</form>';
			$html    = sprintf( $wrapper, $html, $button );

			echo $html;
		}
	}

	/**
	 * Render products list on cart page
	 */
	public static function products_list() {

		$products        = '';
		$products_qty    = 0;
		$cart_items      = WC()->cart->get_cart();
		$applied_coupons = WC()->cart->get_applied_coupons();

		foreach ( $cart_items as $cart_item ) {

			if ( $products_qty > 30 ) {
				break;
			}

			$product_quantity = $cart_item['quantity'];

			// Price of the product
			if ( $applied_coupons ) {

				// Get the first applied coupon code
				$first_applied_coupon = $applied_coupons[0];
				// Get a new instance of the WC_Coupon object
				$coupon = new WC_Coupon( $first_applied_coupon );

				$line_unit_price = $cart_item['line_total'] / $product_quantity;
				$product_object  = wc_get_product( $cart_item['product_id'] );

				if ( $coupon->is_valid_for_product( $product_object ) ) {

					$cart_item_price = round( $line_unit_price, 2 );

				} else {
					$cart_item_price = $cart_item['data']->get_price();
				}

			} else {
				$cart_item_price = $cart_item['data']->get_price();
			}

			if ( $cart_item_price >= self::PRICE_MIN_LIMIT ) {

				$cart_item_image_id      = absint( $cart_item['data']->get_image_id() );
				$cart_item_image_url     = ! empty( $cart_item_image_id ) ?
					wp_get_attachment_image_src( $cart_item_image_id )[0] :
					'';
				$cart_item_category_id   = absint( $cart_item['data']->get_category_ids()[0] );
				$cart_item_category      = ! empty( $cart_item_category_id ) ?
					get_term_by( 'id', $cart_item_category_id, 'product_cat' ) :
					'';
				$cart_item_category_name = ! empty( $cart_item_category ) ?
					substr( wc_strtoupper( str_replace( ' ', '_', $cart_item_category->name ) ), 0, 40 ) :
					'DEFAULT';
				$cart_item_code          = substr( $cart_item['data']->get_sku(), 0, 20 );
				$cart_item_name          = substr( $cart_item['data']->get_name(), 0, 50 );

				$products .= '<specificationListRow>';
				$products .= '<category>' . $cart_item_category_name . '</category>';
				$products .= '<code>' . $cart_item_code . '</code>';
				$products .= '<description>' . $cart_item_name . '</description>';
				$products .= '<amount>' . $product_quantity . '</amount>';
				$products .= '<price>' . $cart_item_price . '</price>';
				$products .= '<image>' . $cart_item_image_url . '</image>';
				$products .= '</specificationListRow>';
			}

			$products_qty ++;
		}

		return $products;
	}

	// TODO - check
	// Coupon AJAX handler
	public static function when_coupon_apply() {
		check_ajax_referer( 'apply-coupon', 'security' );

		if ( ! empty( $_POST['coupon_code'] ) ) {
			WC()->cart->add_discount( wc_format_coupon_code( wp_unslash( $_POST['coupon_code'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			echo static::products_list();

			wp_die();
		}
	}

}

Alfabank_Buy_Easy_Form::init();