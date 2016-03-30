<div class="featured-products-full">
	<h2>Featured products</h2><br>
	<div class="featured-products-visible">
		<?php # TODO - We need to have both base IDs and version IDs.
			foreach ($this->data['featuredProductsIds'] as $product) {
				echo $this->productImage($product['base_product_id'], $product['product_version_id'],
										 220, 0, true, ['class' => 'featuredProductImage']);
				#echo $this->productImage($productId['product_version_id'], 220, ['class' => 'featuredProductImage']);
			}
		?>
	</div>
	<?php echo $this->image_tag('icons/left_arrow.png', ['class' => 'featured-items-arrow',
													'id' => 'featured-arrow-left',
												'height' => 30]); ?>
	<?php echo $this->image_tag('icons/right_arrow.png', ['class' => 'featured-items-arrow',
													'id' => 'featured-arrow-right',
													'height' => 30]); ?>
</div>