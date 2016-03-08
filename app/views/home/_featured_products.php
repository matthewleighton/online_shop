<div class="featured-products-full">
	<h2>Featured products</h2><br>
	<div class="featured-products-visible">
		
		<?php
			foreach ($this->data['featuredProducts'] as $product) {
				$this->productImage($product['product_id'], 220, ['class' => 'featuredProductImage']);
			}
		?>
	</div>
	<?php $this->image_tag('icons/left_arrow.png', ['class' => 'featured-items-arrow',
													'id' => 'featured-arrow-left',
												'height' => 30]); ?>
	<?php $this->image_tag('icons/right_arrow.png', ['class' => 'featured-items-arrow',
													'id' => 'featured-arrow-right',
													'height' => 30]); ?>
</div>