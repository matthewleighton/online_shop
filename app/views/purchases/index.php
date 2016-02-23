<div class="purchase-history-page">

	<?php
		foreach ($this->data['purchaseList'] as $purchase) {
	?>
		<div class="purchase-history-box">
			<div class="purchase-top">
				<div>
					<p class='purchase-label'>Order placed</p>
					<p class='purchase-value'><?php echo $this->formatDate($purchase['details']['order_created_at']); ?></p>
				</div>
				<div>
					<p class='purchase-label'>Total</p>
					<p class='price purchase-value'>
						£<?php echo $purchase['details']['products_price'] + $purchase['details']['delivery_price']; ?>
					</p>
				</div>
				<div>
					<p class='purchase-label'>Dispatch to</p>
					<p class='purchase-value'>Someone</p>
				</div>
			</div>

			<?php
				foreach ($purchase['products'] as $product) {
			?>		<div class="purchase-product-details">
						<div>
							<?php $this->productImage($product['product_id'], 80); ?>
						</div>
						<div>
							<p>
								<?php
									echo $this->linkToProduct($product['product_id'], $product['product_name']);
									if ($product['quantity_in_purchase'] != '1') {
										echo " (x" . $product['quantity_in_purchase'] . ")";
									}
								?>
							</p>
							<p>By <?php echo $product['authors']; ?></p>
							<p class="price">£<?php echo $this->formatPrice($product['price_at_purchase']); ?></p>
						</div>
					</div>
			<?php
				}
			?>
		</div>

	<?php
		}
	?>

</div>

<?php
/*
		<?php $this->productImage($product['product_id'], 100); ?>





			echo $product['product_name'] . "(£ " . $this->formatPrice($product['price_at_purchase']);
			echo ") x" . $product['quantity_in_purchase'] . "<br>";
		}
		echo "<br><br>-----------------<br><br>";
	}
*/
?>