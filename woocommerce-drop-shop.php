<?php
/**
 * Plugin Name: WooCommerce Drop Shop
 * Plugin URI: https://wordpress.org/plugins/woocommerce-drop-shop/
 * Description: Adds a drag and drop cart to your footer for easier and fun purchase.
 * Version: 1.0.5
 * Author: Roy Ho
 * Author URI: http://royho.me
 * Text Domain: woocommerce-drop-shop
 * Domain Path: /languages
 * Copyright: (c) 2017 Roy Ho
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // no direct access
}

if ( ! class_exists( 'WC_Drop_Shop' ) ) :

class WC_Drop_Shop {

	/**
	 * Init
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Required functions
		require_once( 'woo-includes/woo-functions.php' );

		if ( is_woocommerce_active() ) {
			
			if ( is_admin() ) {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-drop-shop-admin.php' );

				include_once( dirname( __FILE__ ) . '/includes/class-wc-drop-shop-frontend-ajax.php' );
			} else {
				include_once( dirname( __FILE__ ) . '/includes/class-wc-drop-shop-frontend.php' );
			}

		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );

		}

		return true;
	}

	/**
	 * load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'wc_drop_shop_plugin_locale', get_locale(), 'woocommerce-drop-shop' );

		load_textdomain( 'woocommerce-drop-shop', trailingslashit( WP_LANG_DIR ) . 'woocommerce-drop-shop/woocommerce-drop-shop' . '-' . $locale . '.mo' );

		load_plugin_textdomain( 'woocommerce-drop-shop', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		return true;
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Drop Shop Plugin requires WooCommerce to be installed and active. You can download %s here.', 'wocommerce-products-compare' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';

		return true;
	}
}

add_action( 'plugins_loaded', 'woocommerce_drop_shop_init', 0 );

/**
 * Init function
 *
 * @package  WC_Drop_Shop
 * @since 1.0.0
 * @return bool
 */
function woocommerce_drop_shop_init() {
	new WC_Drop_Shop();

	return true;
}

endif;
