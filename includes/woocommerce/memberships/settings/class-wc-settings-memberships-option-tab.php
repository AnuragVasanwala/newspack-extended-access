<?php
/**
 * Registers required scripts for SwG implementation
 * specific to Newspack functionality.
 *
 * @package Newspack\Extended_Access
 */

namespace Newspack\Extended_Access;

use Newspack;

/**
 * Registers filters required to integrate the plugin's option tab into WooCommerce 'Settings -> Memberships' page.
 */
class WC_Settings_Memberships_Option_Tab {

	/**
	 * Set up hooks and filters.
	 */
	public static function init() {

		try {
			// Adds a new option tab to WooCommerce 'Settings -> Memberships' page.
			add_filter( 'woocommerce_get_sections_memberships', array( __CLASS__, 'woocommerce_get_sections_memberships__add_option_tab' ) );
			add_filter( 'woocommerce_get_settings_memberships', array( __CLASS__, 'woocommerce_get_settings_memberships__add_option_tab' ), 10, 2 );
		} catch ( \Error $er ) {
			echo esc_html( $er );
		}

	}

	/**
	 * Add tab for this plugin's options page into WooCommerce 'Settings -> Memberships'.
	 *
	 * @param array $sections Array of the plugin sections.
	 * @return array Returns updated sections.
	 */
	public static function woocommerce_get_sections_memberships__add_option_tab( $sections ) {
		// Add 'Newspack Extended Access' to existing sections.
		$sections['newspack-extended-access'] = __( 'Newspack Extended Access', 'newspack-extended-access' );

		return $sections;
	}

	/**
	 * Add this plugin's options page into WooCommerce 'Settings -> Memberships'.
	 *
	 * @param array  $settings Array of the plugin settings.
	 * @param string $current_section the current section being output.
	 * @return array Returns updated options.
	 */
	public static function woocommerce_get_settings_memberships__add_option_tab( $settings, $current_section ) {
		// Add this plugin's option only for 'newspack-extended-access' key.
		if ( 'newspack-extended-access' === $current_section ) {

			// Prepare server protocol and domain name.
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Already validated.
			$protocol              = ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] || 443 == $_SERVER['SERVER_PORT'] ) ? 'https://' : 'http://';
			$sanitized_server_name = isset( $_SERVER['SERVER_NAME'] ) ? filter_var( $_SERVER['SERVER_NAME'], FILTER_SANITIZE_URL ) : '';
			$server_url_obj        = wp_parse_url( $sanitized_server_name );
			$allowed_referrers     = $protocol . array( ( array_key_exists( 'host', $server_url_obj ) && ! is_null( $server_url_obj['host'] ) ) ? $server_url_obj['host'] : $sanitized_server_name )[0];

			// Prepare title and input-box description.
			$title_desc = '<p>An integration for utilizing Google Extended Access.</p>';
			$input_desc = '<p>Refer <a href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid" target="_blank">Google Developer Documents</a> to setup and configure your Google Client API ID.</p><p>Make sure to add your domain <b><u>' . $allowed_referrers . '</u></b> to Authorized JavaScript origins.';

			// Override existing settings with out 'Newspack Extended Access' tab.
			$settings = array(

				array(
					'name' => __( 'Newspack Extended Access', 'newspack-extended-access' ),
					'type' => 'title',
					'desc' => $title_desc,
				),

				array(
					'type'    => 'textarea',
					'id'      => 'newspack_extended_access__google_client_api_id',
					'name'    => __( 'Google Client API ID', 'newspack-extended-access' ),
					'desc'    => $input_desc,
					'default' => '',
				),

				array(
					'type' => 'sectionend',
				),

			);

		}

		return $settings;
	}
}