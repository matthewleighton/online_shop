<?php $this->boxPageLogo(); ?>

<div class="box-page half-size">
	<h2>Review your order</h2>
	<form method="post" action="submit">
		<div class="checkout-confirm">
			<div class="checkout-confirm-top">
				<div class="payment-and-address">
					<div class="address">
						<h4>Delivery Address</h4>
						<?php $this->printAddress($this->data['address']); ?>
					</div>
					<div class="payment">
						<h4>Payment Method</h4>
						<?php $paymentMethod = $this->data['paymentMethod']; ?>
						<p>
							<?php echo $paymentMethod['card_type']; ?> ending in 
							<?php echo $this->safeCardNum($paymentMethod['card_number']); ?>
						</p>
						<br>
						<h4>Billing Address</h4>
						<?php
							if($this->data['address']['address_id'] == $paymentMethod['address_id']) {
								echo "<p>Same as delivery address</p>";
							} else {
								$this->printAddress($this->data['billingAddress']);
							}
						?>
					</div>
				</div>
				
				<div class="purchase-box-area">
					<?php
						$itemsPrice = $this->totalPrice($this->data['cart']);
						$totalPrice = $this->formatPrice(floatval($itemsPrice) + $this->data['deliveryPrice']);
					?>
					
					<div class="purchase-box-1 purchase-box">
						<input type="submit" value="Buy now" class="buy-now-btn">
						<h4>Order Summary</h4>
						<table>
							<tr>
								<td>Items:</td>
								<td class='right'>£<?php echo $itemsPrice; ?></td>
							</tr>
							<tr>
								<td>Postage & Packing:</td>
								<td class='right'>£<?php echo $this->formatPrice($this->data['deliveryPrice']); ?></td>
							</tr>
							<tr>
								<td colspan='2'>
									<hr>
								</td>
							</tr>
							<tr class='checkout-final-total'>
								<td>Order Total:</td>
								<td class='right'>£<?php echo $totalPrice; ?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="checkout-confirm-cart">
				<h4 class="delivery-time">Delivered by <?php echo $this->data['deliveryDate']; ?></h4>
				<?php
					foreach ($this->data['cart'] as $product) {
				?>
						<div class="checkout-cart-item">
							<div class="cart-product-image">
								<?php $this->productImage($product['product_id'], 75); ?>
							</div>
							<div class="cart-product-details">
								<p><strong><?php echo $product['product_name'];?></strong></p>
								<p>By <?php echo $this->productCreator($product); ?></p>
								<p class="price">£<?php echo $this->formatPrice($product['price']); ?></p>
								<p>Quantity: <?php echo $product['cart_quantity']; ?></p>
							</div>
						</div>
				<?php
					}
				?>
			</div>
			<?php
				// Adds a lower "buy now" button if the cart extends too far.
				if(count($this->data['cart']) > 6) {
			?>
				<div class="purchase-box-2 purchase-box">
					<input type="submit" value="Buy now" class="buy-now-btn">
				</div>	
			<?php
				}
			?>
		</div>
	</form>
</div>