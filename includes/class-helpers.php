<?php

defined( 'ABSPATH' ) || exit;

class Alfabank_Buy_Easy_Helpers {

	/**
	 * Validate checkbox option.
	 *
	 * @param string $option
	 *
	 * @return false|string
	 */
	public static function validate_checkbox_option( $option ) {

		if ( in_array( $option, array( 'yes', 'no' ), true ) ) {

			return $option;
		}

		return false;
	}

	/**
	 * Convert settings string to array.
	 *
	 * @param string $string
	 * @param string $type
	 *
	 * @return array
	 */
	public static function convert_settings_string_to_array( $string, $type = 'id' ) {

		// Explode the string to the array and leave only unique values.
		$array = array_unique( explode( ',', $string ) );

		// Filter only-non empty values of the array according to the settings type.
		if ( $type === 'id' ) {

			$array = array_filter( $array, function ( $value ) {
				$value = $value ? (int) $value > 0 : false;

				return ! empty( $value );
			} );

			// Leave only positive values converted to integer.
			$array = array_map( function ( $value ) {
				return abs( (int) $value );
			}, $array );

		} else {

			$array = array_filter( $array, function ( $value ) {
				return ! empty( $value );
			} );
		}

		// Reindex the array.
		$array = array_values( $array );

		return $array;
	}

	/**
	 * Get authorized user info.
	 */
	public static function get_auth_user_info() {
		$current_user      = wp_get_current_user();
		$user              = array();
		$user['firstname'] = ! empty( $current_user->first_name ) ? sanitize_text_field( $current_user->first_name ) : '';
		$user['lastname']  = ! empty( $current_user->last_name ) ? sanitize_text_field( $current_user->last_name ) : '';
		$user['email']     = ! empty( $current_user->user_email ) ? sanitize_email( $current_user->user_email ) : '';
		// TODO - max 10 digits, without +7.
		$user['phone'] = ! empty( get_user_meta( $current_user->ID, 'billing_phone', true ) ) ?
			get_user_meta( $current_user->ID, 'billing_phone', true ) :
			'';

		return $user;
	}

	/**
	 * Get options.
	 */
	public static function get_options() {
		$options                = array();
		$options['shopINN']     = ! empty( get_option( 'woocommerce_alfabank_buy_easy_shopINN' ) ) ?
			esc_attr( get_option( 'woocommerce_alfabank_buy_easy_shopINN' ) ) :
			'';
		$options['buttonName']  = get_option( 'woocommerce_alfabank_buy_easy_buttonName' ) ?
			esc_attr( get_option( 'woocommerce_alfabank_buy_easy_buttonName' ) ) :
			__( 'Buy Easy', 'wc-alfabank-buy-easy' );
		$options['onNewWindow'] = 'yes' === static::validate_checkbox_option( get_option( 'woocommerce_alfabank_buy_easy_buttonOpenOnNewWindow' ) ) ?
			'_blank' :
			'_self';

		return $options;
	}

	/**
	 * Check if a price has fraction.
	 */
	public static function is_fraction( $price ) {
		$fraction = $price - floor( $price );

		return $fraction ? true : false;
	}

}