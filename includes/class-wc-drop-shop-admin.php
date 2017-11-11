<?php
/**
 * Admin class
 * @package WC_Drop_Shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

/**
 *
 * @since 1.0.0
 */ 
class WC_Drop_Shop_Admin {
	private static $_this;

	/**
	 * init
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		$this->id    = 'wc_drop_shop';
		$this->label = __( 'Drop Shop', 'woocommerce-drop-shop' );


		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ) );
		add_action( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );

    	return true;
	}

	public static function instance() {
		return self::$_this;
	}

	/**
	 * Add setting section to products tab
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function add_section( $sections ) {
		$sections[ $this->id ] = $this->label;

		return $sections;
	}

	/**
	 * Add settings
	 *
	 * @return array
	 */
	public function add_settings( $settings, $current_section = '' ) {

		if ( $current_section === $this->id ) {
			$settings = apply_filters( 'woocommerce_drop_shop_settings', array(

				array(
					'title' => __( 'Drop Shop Options', 'woocommerce-drop-shop' ), 
					'type'  => 'title', 
					'desc'  => '', 
					'id'    => $this->id . '_options' 
				),

				array(
					'title'   => __( 'Show on Shop Pages', 'woocommerce-drop-shop' ),
					'desc'    => __( 'Enable to only show the drop cart on shop pages.', 'woocommerce-drop-shop' ),
					'id'      => $this->id . '_show_on_shop',
					'default' => 'yes',
					'type'    => 'checkbox'
				),

				array(
					'title'   => __( 'Shopping Cart Only', 'woocommerce-drop-shop' ),
					'desc'    => __( 'Enable to only display the cart and disables all drag and drop functionality.', 'woocommerce-drop-shop' ),
					'id'      => $this->id . '_cart_only',
					'default' => 'no',
					'type'    => 'checkbox'
				),

				array(
					'title'   => __( 'Show Product Quantity', 'woocommerce-drop-shop' ),
					'desc'    => __( 'Enable to show the quantity of the product in the cart via a bubble.', 'woocommerce-drop-shop' ),
					'id'      => $this->id . '_show_quantity',
					'default' => 'yes',
					'type'    => 'checkbox'
				),

				array(
					'title'   => __( 'Show Product Title', 'woocommerce-drop-shop' ),
					'desc'    => __( 'Enable to show the title of the product in a tooltip on hover in the cart.', 'woocommerce-drop-shop' ),
					'id'      => $this->id . '_show_title',
					'default' => 'yes',
					'type'    => 'checkbox'
				),

				array(
					'title'   => __( 'Autohide Duration', 'woocommerce-drop-shop' ),
					'desc'    => __( 'Enter how many seconds you want before the cart will autohide.  Enter 0 to disable which will always show.', 'woocommerce-drop-shop' ),
					'id'      => $this->id . '_autohide',
					'default' => '6',
					'type'    => 'text'
				),

				array(
					'type' => 'sectionend',
					'id'   => $this->id . '_section_end'
				),
			), $settings, $current_section );

			return $settings;

		} else {
			return $settings;
		}
	}
}

new WC_Drop_Shop_Admin();
