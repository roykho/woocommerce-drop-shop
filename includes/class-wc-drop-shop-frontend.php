<?php
/**
 * Frontend class
 * @package WC_Drop_Shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

/**
 *
 * @since 1.0.0
 */ 
class WC_Drop_Shop_Frontend {
	private static $_this;

	/**
	 * init
	 *
	 * @access public
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		add_action( 'wp_footer', array( $this, 'render_cart' ) );

		add_action( 'wp', array( $this, 'set_session' ) );

    	return true;
	}

	public static function instance() {
		return self::$_this;
	}

	/**
	 * Sets the WC customer session if one is not set.
	 * This is needed so nonces can be verified.
	 *
	 * @since 1.0.5
	 * @version 1.0.5
	 */
	public function set_session() {
		if ( ! is_user_logged_in() ) {
			$wc_session = new WC_Session_Handler();
			if ( ! $wc_session->has_session() ) {
				$wc_session->set_customer_session_cookie( true );
			}
		}
	}

	/**
	 * Loads the necessary frontend scripts
	 *
	 * @since 1.0.0
	 * @version 1.0.5
	 * @return boolean true
	 */
	public function load_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'jquery' );
		
		wp_enqueue_script( 'jquery-ui-core' );

		wp_enqueue_script( 'jquery-ui-draggable' );

		wp_enqueue_script( 'jquery-ui-droppable' );

		wp_enqueue_script( 'woocommerce-drop-shop-touch-punch-script', plugins_url( 'plugin-assets/js/vendor/jquery-touch-punch.min.js', dirname( __FILE__ ) ), array( 'jquery' ), '0.2.3', true );  

		wp_enqueue_script( 'woocommerce-drop-shop-script', plugins_url( 'plugin-assets/js/drop-shop' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0.0', true );  
		
		// set the localized variables
		$localized_vars = array(
			'ajaxurl'                              => admin_url( 'admin-ajax.php' ),
			'wc_drop_shop_ajax_add_to_cart_nonce'  => wp_create_nonce( 'wc_drop_shop_ajax_add_to_cart_nonce' ),
			'wc_drop_shop_ajax_refresh_cart_nonce' => wp_create_nonce( 'wc_drop_shop_ajax_refresh_cart_nonce' ),
			'wc_drop_shop_ajax_remove_item_nonce'  => wp_create_nonce( 'wc_drop_shop_ajax_remove_item_nonce' ),
			'select_all_options_msg'               => apply_filters( 'woocommerce_drop_shop_select_all_options_msg', __( 'Please select all options before clicking on add to cart.', 'woocommerce-drop-shop' ) ),
			'is_single'                            => is_product() ? 'true' : 'false'
		);
		
		wp_localize_script( 'woocommerce-drop-shop-script', 'woocommerce_drop_shop_local', $localized_vars );	
		
		wp_enqueue_style( 'woocommerce-drop-shop-styles', plugins_url( 'plugin-assets/css/drop-shop.css', dirname( __FILE__ ) ), array( 'dashicons' ), '1.0.0' );

		return true;
	}

	/**
	 * Renders the cart
	 *
	 * @since 1.0.0
	 * @version 1.0.2
	 * @return boolean true
	 */
	public function render_cart() {
		include( 'class-wc-drop-shop-helper.php' );
		
		$settings = WC_Drop_Shop_Helper::get_settings();

		if ( 'yes' === $settings['show_on_shop'] && ! is_shop() && ! is_product_category() && ! is_product() ) {
			return;
		}

		do_action( 'woocommerce_drop_shop_before_cart' );

		$cart = '<div class="woocommerce-drop-shop-wrapper ' . apply_filters( 'woocommerce_drop_shop_cart_initial_state', 'show' ) . '">' . PHP_EOL;

		ob_start();
		// check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-drop-shop/cart-html.php' ) ) {
			
			include( get_stylesheet_directory() . '/woocommerce-drop-shop/cart-html.php' );

		} else  {
			include( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/cart-html.php' );
		}
		
		$cart .= ob_get_clean();

		$cart .= '</div>' . PHP_EOL;

		do_action( 'woocommerce_drop_shop_after_cart' );
		
		echo $cart;
		
		return true;
	}
}

new WC_Drop_Shop_Frontend();
