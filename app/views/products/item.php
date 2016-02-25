<?php $product = $this->data['product']; ?>
<div class='product-section-1'>
	<div class='product-image'>
		<?php $this->productImage($product['product_id'], 250, ['class' => 'product-image']); ?>
	</div>
	
	<div class='product-description'>
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
			<div id="product-description-text">
				<?php echo $product['product_description']; ?>
			</div>
			<a class="product-description-expander">
				<span id="product-description-more">Read more</span>
				<span id="product-description-less">Read less</span>
			</a>
		</div>
	</div>

	<div class='add-to-cart-box'>
		<form action="<?php echo $this->rootPath(); ?>carts/addItem" method="post">
			<span>Quantity: </span>
			<select name='quantity'>
				<option value='1'>1</option>
				<option value='2'>2</option>
				<option value='3'>3</option>
				<option value='4'>4</option>
			</select>

			<input type='hidden' name='price' value=<?php echo "'" . $product['price'] . "'"; ?>>
			<input type='hidden' name='product_id' value=<?php echo "'" . $product['product_id'] . "'" ?>>
			<input type='submit' value='Add to Basket' class='add-to-basket-btn'>
		</form>
		<form action="<?php echo $this->rootPath(); ?>wishlist/addItem" method="post">
			<input type="hidden" name="productId" value=<?php echo "'" . $product['product_id'] . "'" ?>>
			<input type="submit" value="Add to Wish List" class="add-to-wish-list-btn">
		</form>

	</div>

</div>
<h3>Product Details</h3>
<div class="product-details">
	<?php
		if ($product['product_catagory'] == "book") {
			include_once('../app/views/products/_book_details.php');
		} elseif ($product['product_catagory'] == "film") {
			include_once('../app/views/products/_film_details.php');
		}
	?>
</div>