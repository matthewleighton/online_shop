<?php $product = $this->data['product']; ?>
<div class='product-section-1'>
	<div class='product-image'>
		<?php $this->productImage($product['product_id'], 250, ['class' => 'product-image']); ?>
	</div>
	
	<div class='product-details'>
		<div class="product-name">
			<?php echo $product['product_name'] ?>
		</div>

		<div class="product-creator">
			By 
			<?php
				if(isset($product['authors'])) {
					echo $product['authors'];
				}
			?>
		</div>

		<div class="product-price-section">
			<span class='price-label'>Price: </span>
				<span class='product-price'>£<?php echo $this->formatPrice($product['price']); ?>
			</span>
		</div>

		<div class="product-description">
			<?php echo $product['product_description']; ?>
		</div>
	</div>

	<div class='purchase-box'>
		<form action="/online_shop/public/carts/addItem" method="post">
			<span>Quantity: </span>
			<select name='quantity'>
				<option value='1'>1</option>
				<option value='2'>2</option>
				<option value='3'>3</option>
				<option value='4'>4</option>
			</select>

			<input type='hidden' name='price' value=<?php echo "'" . $product['price'] . "'"; ?>>
			<input type='hidden' name='product_id' value=<?php echo "'" . $product['product_id'] . "'" ?>>
			<input type='submit' value='Add to Basket' class='add-to-basket'>
		</form>

	</div>

</div>