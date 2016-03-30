<!--<pre><?php print_r($this->data['products']); ?></pre>-->
<p><?php echo count($this->data['products']); ?> results found.</p>

<div class="product-list">
	<?php

		$productList = $this->data['products'];

		foreach ($productList as $baseProduct) {
			if(array_key_exists('release_date', $baseProduct[0])) {
				$releaseDate = $this->formatDate($baseProduct[0]['release_date'], 'jS F Y');
			} else {
				$releaseDate = '';
			}
			#$price = $this->formatPrice($product['product_price']);

			echo "<div class='product-container'>";
				echo "<div class='product-image'>";
				echo $this->productImage($baseProduct[0]['base_product_id'],
										 $baseProduct[0]['product_version_id'],
										 150, 0, ['class' => 'product-list-image']);
				echo "</div>";
				
				echo "<div class='product-versions'>";
				$this->link_to('products/item/' . $baseProduct[0]['product_version_id'], $baseProduct[0]['product_name'], ['class' => 'product-name']);
				
				echo "<span class='release-date'>" . $releaseDate . "</span><br>";

				if(isset($product['authors']) && $product['authors'] != '') {
					echo "<span class='author-names'> By " . $product['authors'] . "</span><br>";
				}

				if(isset($product['book_type']) && $product['book_type'] != '') {
					echo "<span class='book-type'>" . ucfirst($product['book_type']) . "</span><br>";
				}

				foreach ($baseProduct as $productVersion) {
					echo "<div class='product-platform'>";
					$price = $this->formatPrice($productVersion['product_price']);
					$this->link_to('products/item/' . $productVersion['product_version_id'],
								   $productVersion['platform'],
								   ['class' => 'platform-name']);
					echo "<br><span class='product-price'>£" . $price . "</span></div>";
				}
				echo "</div>";
				
			echo "</div>";
		}

	?>
</div>