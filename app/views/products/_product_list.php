<p><?php echo count($this->data['products']); ?> results found.</p>

<div class="product-list">
	<?php

		$productList = $this->data['products'];

		foreach ($productList as $product) {
			if(array_key_exists('release_date', $product)) {
				$releaseDate = $product['release_date'];
			} else {
				$releaseDate = '';
			}
			$price = $this->formatPrice($product['price']);

			echo "<div class='product-container'>";
				$this->image_tag('placeholder-image.png', ['height' => 100, 'class' => 'product-list-image']);
					
				$this->link_to('products/item/' . $product['product_id'], $product['product_name'], ['class' => 'product-name']);
				
				echo "<span class='release-date'>" . $releaseDate . "</span>";

				echo "<br>";

				if(isset($product['authors']) && $product['authors'] != '') {
					echo "<span class='author-names'> By " . $product['authors'] . "</span><br>";
				}

				if(isset($product['book_type']) && $product['book_type'] != '') {
					echo "<span class='book-type'>" . ucfirst($product['book_type']) . "</span><br>";
				}

				echo "<span class='product-price'>£" . $price . "</span>";
				
			echo "</div>";
		}

	?>
</div>