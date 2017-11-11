<?php
/**
 * @package WC_Drop_Shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

class WC_Drop_Shop_Frontend_Ajax {
	private static $_this;

	/**
	 * init
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_action( 'wp_ajax_nopriv_wc_drop_shop_refresh', array( $this, 'refresh' ) );
		add_action( 'wp_ajax_wc_drop_shop_refresh', array( $this, 'refresh' ) );

		add_action( 'wp_ajax_nopriv_wc_drop_shop_remove_item', array( $this, 'remove_item' ) );
		add_action( 'wp_ajax_wc_drop_shop_remove_item', array( $this, 'remove_item' ) );
		
		add_action( 'wp_ajax_nopriv_wc_drop_shop_ajax_add_to_cart', array( $this, 'add_to_cart' ) );
		add_action( 'wp_ajax_wc_drop_shop_ajax_add_to_cart', array( $this, 'add_to_cart' ) );	

		return true;
	}

	public static function instance() {
		return self::$_this;
	}

	/**
	 * Cart display on ajax refresh
	 *
	 * @since 1.0.0
	 * @return string $output the HTML of the cart
	 */
	public function refresh() {
		$nonce = $_POST['wc_drop_shop_ajax_refresh_cart'];

		// bail if nonce don't match
		if ( ! wp_verify_nonce( $nonce, 'wc_drop_shop_ajax_refresh_cart_nonce' ) ) {
			wp_die( __( 'Cheatin&#8217; huh?', 'woocommerce-drop-shop' ) );
		}

		include( dirname( __FILE__ ) . '/class-wc-drop-shop-helper.php' );

		do_action( 'woocommerce_drop_shop_before_cart' );

		$cart = '';
		
		if ( version_compare( WC_VERSION, '3.2', '<' ) ) {
			WC()->cart->init();
		}
		
		// get the dropshop template
		ob_start();
		
		// check if template has been overriden
		if ( file_exists( get_stylesheet_directory() . '/woocommerce-drop-shop/templates/cart-html.php' ) ) {
			
			include( get_stylesheet_directory() . '/woocommerce-drop-shop/templates/cart-html.php' );

		} else  {
			include( plugin_dir_path( dirname( __FILE__ ) ) . 'templates/cart-html.php' );
		}

		$cart .= ob_get_clean();

		do_action( 'woocommerce_drop_shop_after_cart' );

		// get the woocommerce mini cart template
		ob_start();
		woocommerce_mini_cart();
		$woocart_output = ob_get_clean();

		$output = array( 'drop_shop' => $cart, 'woocart' => $woocart_output );
		
		wp_send_json( $output );
	}

	/**
	 * Remove item function
	 *
	 * @since 1.0.0
	 * @return boolean true
	 */
	public function remove_item() {
		$nonce = $_POST['wc_drop_shop_ajax_remove_item'];

		// bail if nonce don't match
		if ( ! wp_verify_nonce( $nonce, 'wc_drop_shop_ajax_remove_item_nonce' ) ) {
			wp_die( __( 'Cheatin&#8217; huh?', 'woocommerce-drop-shop' ) );
		}

		$cart_id = $_POST['cart_id'];
		$qty = absint( $_POST['qty'] );

		// decrement qty by 1
		$qty = $qty - 1;

		// make sure qty is not negative
		if ( $qty < 0 ) { 
		  $qty = 0;        
		}	

		// sets the new quantity
		WC()->cart->set_quantity( $cart_id, $qty );

		echo true;
		exit; 
	}	

	/**
	 * Add to cart when no add to cart button is found
	 *
	 * @since 1.0.0
	 * @return boolean true
	 */
	public function add_to_cart() {
		global $product, $post;

		$nonce = $_POST['wc_drop_shop_ajax_add_to_cart'];

		// bail if nonce don't match
		if ( ! wp_verify_nonce( $nonce, 'wc_drop_shop_ajax_add_to_cart_nonce' ) ) {
			wp_die( __( 'Cheatin&#8217; huh?', 'woocommerce-drop-shop' ) );
		}

		$product_id   = apply_filters( 'woocommerce_add_to_cart_product_id', $_POST['product_id'] );
		$quantity     = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
		$variation_id = empty( $_POST['variation_id'] ) ? '' : absint( $_POST['variation_id'] );
		$variations   = empty( $_POST['variations'] ) ? '' : $_POST['variations'];

		// get the product type
		$product = version_compare( WC_VERSION, '3.0', '<' ) ? get_product( $product_id ) : wc_get_product( $product_id );

		// set post global
		$post = get_post( $product_id );

		$product_type = version_compare( WC_VERSION, '3.0', '<' ) ? $product->product_type : $product->get_type();

		switch ( $product_type ) {
			case 'simple' :
				// Add to cart validation
				$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

				if ( $passed_validation ) {	
					// if add to cart successfully
					if ( WC()->cart->add_to_cart( $product_id, $quantity ) ) {
						WC()->cart->maybe_set_cart_cookies();

						$output = json_encode( array( 'added' => true, 'productType' => 'simple' ) );
					} else {
						$notices = wc_get_notices();

						$html = '';
						$html .= '<div class="woocommerce-drop-shop-variation-popup-container">' . PHP_EOL;
						$html .= '<p class="woocommerce-drop-shop-cart-msg">' . PHP_EOL;
						$html .= is_array( $notices ) && isset( $notices['error'] ) ? $notices['error'][0] : __( 'Product cannot be added', 'woocommerce-drop-shop' ) . PHP_EOL;
						$html .= '</p>' . PHP_EOL;
						$html .= '<a href="#" title="' . esc_attr__( 'Close', 'woocommerce-drop-shop' ) . '" class="woocommerce-drop-shop-close-button dashicons dashicons-dismiss"></a>' . PHP_EOL;
						$html .= '</div>' . PHP_EOL;	

						$output = json_encode( array( 'added' => false, 'productType' => 'simple', 'html' => $html ) );

						wc_clear_notices();
					}
				}
				break;
			case 'variable' :
				// check if variation is set
				if ( empty( $variation_id ) ) {
					remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );

					// get the variation select dropdown html
					ob_start();
					wc_get_template( 'single-product/add-to-cart/variable.php', array(
						'available_variations' => $product->get_available_variations(),
						'attributes'           => $product->get_variation_attributes(),
						'selected_attributes'  => $product->get_variation_default_attributes()
					) );

					$woo_variations = ob_get_clean();

	        		$html = '';
	        		$html .= '<div class="woocommerce-drop-shop-variation-popup-container">' . PHP_EOL;
	        		$html .= '<p class="woocommerce-drop-shop-cart-msg">' . PHP_EOL;
	        		$html .= apply_filters( 'woocommerce_drop_shop_options_not_set_text', __( 'This product has options. Please select the options below.', 'woocommerce-drop-shop' ) ) . PHP_EOL;
	        		$html .= '</p>' . PHP_EOL;
	        		$html .= $woo_variations . PHP_EOL;
	        		$html .= '<a href="#" class="woocommerce-drop-shop-add-button">' . apply_filters( 'woocommerce_drop_shop_add_to_cart_button_text', __( 'Add to Cart', 'woocommerce-drop-shop' ) ) . '</a>' . PHP_EOL;
	        		$html .= '<a href="' . get_permalink( $product_id ) . '" class="woocommerce-drop-shop-detail-link">' . apply_filters( 'woocommerce_drop_shop_detail_button_text', __( 'More Detail', 'woocommerce-drop-shop' ) ) . '</a>' . PHP_EOL;
	        		$html .= '<a href="#" title="' . esc_attr__( 'Close', 'woocommerce-drop-shop' ) . '" class="woocommerce-drop-shop-close-button dashicons dashicons-dismiss"></a>' . PHP_EOL;
	        		$html .= '</div>' . PHP_EOL;

					$output = json_encode( array( 'added' => false, 'productType' => 'variable', 'html' => $html ) );
				} else {
		        	// Add to cart validation
		        	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

		        	if ( $passed_validation ) {
		        		// if added successfully
						if ( WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) ) { 
							$output = json_encode( array( 'added' => true, 'productType' => 'variable' ) );
						} else {
			        		$html = '';
			        		$html .= '<div class="woocommerce-drop-shop-variation-popup-container">' . PHP_EOL;
			        		$html .= '<p class="woocommerce-drop-shop-cart-msg">' . PHP_EOL;
			        		$html .= apply_filters( 'woocommerce_drop_shop_error', __( 'Sorry, there was an issue with adding this product to the cart.  Please click the more detail link button and try there.', 'woocommerce-drop-shop' ) ) . PHP_EOL;
			        		$html .= '</p>' . PHP_EOL;
			        		$html .= '<a href="' . get_permalink( $product_id ) . '" class="woocommerce-drop-shop-detail-link">' . apply_filters( 'woocommerce_drop_shop_detail_button_text', __( 'More Detail', 'woocommerce-drop-shop' ) ) . '</a>' . PHP_EOL;
			        		$html .= '<a href="#" title="' . esc_attr__( 'Close', 'woocommerce-drop-shop' ) . '" class="woocommerce-drop-shop-close-button dashicons dashicons-dismiss"></a>' . PHP_EOL;
			        		$html .= '</div>' . PHP_EOL;			
			        					
							$output = json_encode( array( 'added' => false, 'productType' => 'variable', 'html' => $html ) );
						}
					}
				}
				break;

			case 'grouped' :
	    		$html = '';
	    		$html .= '<div class="woocommerce-drop-shop-variation-popup-container">' . PHP_EOL;
	    		$html .= '<p class="woocommerce-drop-shop-cart-msg">' . PHP_EOL;
	    		$html .= apply_filters( 'woocommerce_drop_shop_grouped_product_msg', __( 'This is a grouped product.  Please go to the product detail page to add this item to the cart.', 'woocommerce-drop-shop' ) ) . PHP_EOL;
	    		$html .= '</p>' . PHP_EOL;
	    		$html .= '<a href="' . get_permalink( $product_id ) . '" class="woocommerce-drop-shop-detail-link">' . apply_filters( 'woocommerce_drop_shop_detail_button_text', __( 'More Detail', 'woocommerce-drop-shop' ) ) . '</a>' . PHP_EOL;
	    		$html .= '<a href="#" title="' . esc_attr__( 'Close', 'woocommerce-drop-shop' ) . '" class="woocommerce-drop-shop-close-button dashicons dashicons-dismiss"></a>' . PHP_EOL;
	    		$html .= '</div>' . PHP_EOL;	

				$output = json_encode( array( 'added' => false, 'productType' => 'grouped', 'html' => $html ) );
				break;

			case 'external' :
				// get products external url
				$external_url = get_post_meta( $product_id, '_product_url', true );

	    		$html = '';
	    		$html .= '<div class="woocommerce-drop-shop-variation-popup-container">' . PHP_EOL;
	    		$html .= '<p class="woocommerce-drop-shop-cart-msg">' . PHP_EOL;
	    		$html .= apply_filters( 'woocommerce_drop_shop_external_product_msg', __( 'This is an external product.  Please click on the detail link to go to the product\'s page.', 'woocommerce-drop-shop' ) ) . PHP_EOL;
	    		$html .= '</p>' . PHP_EOL;
	    		$html .= '<a href="' . esc_url( $external_url ) . '" class="woocommerce-drop-shop-detail-link">' . apply_filters( 'woocommerce_drop_shop_detail_button_text', __( 'More Detail', 'woocommerce-drop-shop' ) ) . '</a>' . PHP_EOL;
	    		$html .= '<a href="#" title="' . esc_attr__( 'Close', 'woocommerce-drop-shop' ) . '" class="woocommerce-drop-shop-close-button dashicons dashicons-dismiss"></a>' . PHP_EOL;
	    		$html .= '</div>' . PHP_EOL;	

				$output = json_encode( array( 'added' => false, 'productType' => 'grouped', 'html' => $html ) );
				break;
		}

		echo $output;
		exit;
	}	
}

new WC_Drop_Shop_Frontend_Ajax();
