<div class='cart-main'>
	<?php
		$cart = $this->data['cart'];
		if(!empty($cart)) {
	?>

			<div class='cart-top'>
				<div class='cart-title'>Shopping Basket</div>
				<div class='cart-price-label'>Price</div>
				<div class='cart-quantity-label'>Quantity</div>
			</div>
	<?php
		} else {
	?>
			<div class='empty-cart-message'>
				Your cart is empty!
			</div>
	<?php
		}
	?>

	<br><br>


	<div class='cart-item-list'>
		<?php
			foreach ($cart as $product) {
		?>	
				<div class='cart-item'>
					<div class='cart-product-image'>
						<?php
							$this->productImage($product['product_id'], 100);
						?>
					</div>
					<div class='cart-product-details'>
						<span class='product-name'>
							<?php $this->link_to("products/item/" . $product['product_id'], $product['product_name']); ?>
						</span>
						<?php
							include('../app/views/cart/_cart_' . $product['product_catagory'] . '.php');
						?>
						<span class='cart-remove'>
							<?php $this->removeFromCart($product['product_id'], $cart); ?>
						</span>
					</div>

					<div class='cart-product-price price'>
						<span>£<?php echo $this->formatPrice($product['price']); ?></span>
					</div>

					<div class='cart-product-quantity'>
						<span><?php echo $product['cart_quantity']; ?></span>
					</div>
				</div>
		<?php
			}
		?>
	</div>

	<div class='cart-bottom'>
		<span class='subtotal'>Subtotal (<?php echo sizeof($cart); ?> items): 
			<span class='price'>£<?php echo $this->totalPrice($cart); ?></span>
		</span>
	</div>
</div>

<div class='cart-checkout-box'>
	<span class='subtotal'>Subtotal (<?php echo sizeof($cart); ?> items): 
		<span class='price'>£<?php echo $this->totalPrice($cart); ?></span>
	</span>

	<?php $this->link_to('checkout', 'Proceed to Checkout', ['class' => 'checkout-link']); ?>
</div>