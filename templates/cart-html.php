<?php
/**
 * Renders the footer cart
 *
 * @package WC_Drop_Shop
 * @version 1.0.0
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

$cart_image_width = apply_filters( 'woocommerce_drop_shop_horizontal_item_image_width', 80 );
$cart_image_height = apply_filters( 'woocommerce_drop_shop_horizontal_item_image_height', 80 );

$settings  = WC_Drop_Shop_Helper::get_settings();
$cart_only = isset( $settings['cart_only'] ) ? $settings['cart_only'] : 'no';
?> 
	<div class="woocommerce-drop-shop-center-wrap group">
		<i class="woocommerce-drop-shop-tab dashicons <?php echo esc_attr( apply_filters( 'woocommerce_drop_shop_cart_tab_icon', 'dashicons-cart' ) ); ?>"></i>

		<input type="hidden" class="woocommerce-drop-shop-settings" value="<?php echo esc_attr( json_encode( $settings ) ); ?>" />

		<div class="woocommerce-drop-shop-inner-wrap group"> 

			<div class="woocommerce-drop-shop-inlay group">
				
				<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) { ?>

					<span class="woocommerce-drop-shop-prev dashicons dashicons-arrow-left-alt2"></span>

					<div class="woocommerce-cart group">
						<ul>
							<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) { 
								$_product = $cart_item['data'];
								if ( $_product->exists() && $cart_item['quantity'] > 0 ) {
								?>
									<li class="draggable-cart-item cart_item_id_<?php echo esc_attr( $cart_item_key ); ?>" id="draggable-item-<?php echo esc_attr( $cart_item['product_id'] ); ?>">
										<?php if ( WC()->cart->get_item_data( $cart_item ) ) { 
											$title = esc_attr( $_product->get_title() ) . " (" . strip_tags( WC()->cart->get_item_data( $cart_item, true ) ) . ")";
										} else {
											$title = esc_attr( $_product->get_title() );
										}
										?>

										<a href="<?php echo get_permalink( $cart_item['product_id'] ); ?>" title="<?php echo esc_attr( $title ); ?>" class="wcds-tooltip">
											<?php $image_url = WC_Drop_Shop_Helper::get_cart_item_image( $cart_item['product_id'], $cart_item['variation_id'], $cart_image_width, $cart_image_height ); ?>

											<img src="<?php echo $image_url; ?>" class="woocommerce-drop-shop-cart-item" alt="<?php echo esc_attr( $_product->get_title() ); ?>" width="<?php echo esc_attr( $cart_image_width ); ?>" height="<?php echo esc_attr( $cart_image_height ); ?>" />
										</a>
										
										<form id="draggable-item-<?php echo esc_attr( $cart_item['product_id'] ); ?>-form" method="post" action="<?php the_permalink(); ?>" >
											<input type="hidden" value="<?php echo esc_attr( $cart_item['product_id'] ); ?>" name="product_id" />
											<input type="hidden" value="<?php echo esc_attr( $cart_item_key ); ?>" name="cart_id" />
											<input type="hidden" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" name="quantity" />
											<input type="hidden" value="<?php echo esc_attr( $cart_item['variation_id'] ); ?>" name="variation_id" />
										</form>

										<?php
										// check if we need to show the item quantity
										if ( isset( $settings['show_quantity'] ) && 'yes' === $settings['show_quantity'] ) {
										?>
											<span class="woocommerce-drop-shop-item-count"><?php echo $cart_item['quantity']; ?></span>
										<?php
										}
										?>
										<a href="#" title="<?php esc_attr_e( 'Remove Item', 'woocommerce-drop-shop' ); ?>" class="woocommerce-drop-shop-item-remover dashicons dashicons-no"></a>
									</li>
							<?php } 
								} ?>
						</ul>
						
					</div><!--.woocommerce-cart-->
					
					<span class="woocommerce-drop-shop-next dashicons dashicons-arrow-right-alt2"></span>
				<?php } else { ?>

					<p class="woocommerce-drop-shop-cart-empty">

					<?php
					$empty_cart_text = 'no' === $cart_only ? __( 'drop items here', 'woocommerce-drop-shop' ) : __( 'empty cart', 'woocommerce-drop-shop' );
					$empty_cart_text = apply_filters( 'woocommerce_drop_shop_empty_cart_text', $empty_cart_text ); 
					?>
							
					<span><?php echo $empty_cart_text; ?></span>
					<i class="dashicons <?php echo esc_attr( apply_filters( 'woocommerce_drop_shop_cart_empty_icon', 'dashicons-download' ) ); ?>" aria-hidden="true"></i>

					</p>

				<?php } // end check if have items ?>
				
				<i class="woocommerce-drop-shop-dragdrop-spinner dashicons <?php echo esc_attr( apply_filters( 'woocommerce_drop_shop_cart_load_spinner', 'dashicons-admin-generic' ) ); ?>" aria-hidden="true"></i> 

			</div><!--.woocommerce-drop-shop-inlay--> 

			<div class="woocommerce-drop-shop-action group">
				<p class="woocommerce-drop-shop-cart-total"><?php printf( '%s &nbsp; %s', apply_filters( 'woocommerce_drop_shop_subtotal_text', __( 'Sub-Total:', 'woocommerce-drop-shop' ) ), WC()->cart->get_cart_subtotal() ); ?></p>

				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php apply_filters( 'woocommerce_drop_shop_view_cart_text', esc_attr_e( 'View Cart', 'woocommerce-drop-shop' ) ); ?>" class="woocommerce-drop-shop-viewcart-button button"><?php apply_filters( 'woocommerce_drop_shop_view_cart_text', _e( 'View Cart', 'woocommerce-drop-shop' ) ); ?></a> 

				<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" title="<?php apply_filters( 'woocommerce_drop_shop_checkout_button_text', esc_attr_e( 'Checkout', 'woocommerce-drop-shop' ) ); ?>" class="woocommerce-drop-shop-checkout-button button"><?php apply_filters( 'woocommerce_drop_shop_checkout_button_text', _e( 'Checkout', 'woocommerce-drop-shop' ) ); ?></a> 

			</div><!--.woocommerce-drop-shop-action-->

		</div><!--.woocommerce-drop-shop-inner-wrap-->
	</div><!--.woocommerce-drop-shop-center-wrap-->
