<?php
/**
 * Helper class
 * @package WC_Drop_Shop
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

/**
 *
 * @since 1.0.0
 */ 
class WC_Drop_Shop_Helper {
	/**
	 * Get settings
	 *
	 * @access public
	 * @since 1.0.0
	 * @return json $settings
	 */
	public static function get_settings() {
		$settings = array(
			'show_on_shop'  => get_option( 'wc_drop_shop_show_on_shop', 'yes' ),
			'show_quantity' => get_option( 'wc_drop_shop_show_quantity', 'yes' ),
			'cart_only'     => get_option( 'wc_drop_shop_cart_only', 'no' ),
			'show_title'    => get_option( 'wc_drop_shop_show_title', 'yes' ),
			'autohide'      => get_option( 'wc_drop_shop_autohide', '6' ),
			'speed'         => apply_filters( 'woocommerce_drop_shop_carousel_scroll_speed', 400 ), // speed of the scroll
			'scrollby'      => apply_filters( 'woocommerce_drop_shop_carousel_scrollby', 1 ), // items to scroll by on one click
			'visible'       => apply_filters( 'woocommerce_drop_shop_carousel_visible', 3 ), // items visible in the carousel at one time
			'easing'        => apply_filters( 'woocommerce_drop_shop_carousel_easing', 'swing' ),
		);

		return $settings;
	}

	/**
	 * Gets the image if one is set to display in the cart
	 *
	 * @since 1.0.0
	 * @param int $product_id Pass in a product id
	 * @param int $variation_id Pass in a variation id
	 * @param int $image_width Pass in the width of the cart image
	 * @param int $image_height Pass in the height of the cart image
	 * @return string $image The URL of the image
	 */
	public static function get_cart_item_image( $product_id = 1, $variation_id = 1, $image_width = 50, $image_height = 50 ) {
		
		if ( isset( $variation_id ) && ! empty( $variation_id ) ) {

			// get the variation image
			$attach_id = get_post_meta( $variation_id, '_thumbnail_id', true );
			
			// get the image source
			$image = wp_get_attachment_image_src( $attach_id, array( $image_width, $image_height ) );

			// if image is found
			if ( $image ) {
			  $image = $image[0];  
			} else {
				// get the product image
				$attach_id = get_post_meta( $product_id, '_thumbnail_id', true );
				
				// get the image source
				$image = wp_get_attachment_image_src( $attach_id, array( $image_width, $image_height ) );			

				if ( $image ) {
					$image = $image[0];
				} else {
			  		return wc_placeholder_img_src();  
			  	}
			}
		} else {
			// get the product image
			$attach_id = get_post_meta( $product_id, '_thumbnail_id', true );
			
			// get the image source
			$image = wp_get_attachment_image_src( $attach_id, array( $image_width, $image_height ) );			

			if ( $image ) {
				$image = $image[0];
			} else {
		  		return wc_placeholder_img_src();  
		  	}
		}	
		
		return $image;
	}
}
