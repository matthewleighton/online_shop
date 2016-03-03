<div class="featured-products">
	<h2>Featured products</h2></br>
	<?php
		foreach ($this->data['featuredProducts'] as $product) {
			echo "<div>";
				$this->productImage($product['product_id'], 220);
			echo "</div>";
		}
	?>
</div>