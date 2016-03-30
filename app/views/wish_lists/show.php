<div class='list-of-wish-lists-area'>
	<h3>My Wish Lists</h3>
	<?php
		foreach ($this->data['listOfWishLists'] as $wishList) {
			echo "<div class='wish-list-name-box' ";
			if (isset($this->data['listInfo']) && ($wishList['wish_list_id'] == $this->data['listInfo']['wish_list_id'])) {
				echo "id='current-wish-list' ";
			}
			echo "><a href='" . $GLOBALS['rootPath'] . "wish_lists/show/" . $wishList['wish_list_id'] . "'>";
			echo $wishList['wish_list_name'] . "</a>";
			echo "</div>";
		}
	?>
</div>

<div class='current-wish-list-area'>
	<?php
		if (isset($this->data['listInfo'])) {
	?>
		<div class='wish-list-header'>
			<h3><?php echo $this->data['listInfo']['wish_list_name']; ?></h3>
			<?php
				echo "<a href='" . $GLOBALS['rootPath'] . "wish_lists/destroy/" . 
				$this->data['listInfo']['wish_list_id'] . "'>Delete Wish List</a>";
			?>
		</div>

	<?php
		}
		
		if (isset($this->data['wishList'])) {
			if (count($this->data['wishList']) > 0) {
				foreach ($this->data['wishList'] as $product) {
				echo "<div class='wish-list-product'>";
				echo "<div class='wish-list-product-image'>";
				echo $this->productImage($product['base_product_id'],
										 $product['product_version_id'],
										 150, 0, true);
				echo "</div>";

				echo "<div class='product-details-area'>";
				$this->linkToProduct($product['product_version_id'],
									 $product['product_name'] . ' (' . $product['platform'] . ')',
									 ['class' => 'product-title']);
				
				echo "<p class='product-creators'>" . $this->displayProductCreator($product) . '</p>';
				echo "<p class='price'>£" . $this->formatPrice($product['product_price']) . "</p>";
		?>

				<form action='<?php echo $GLOBALS['rootPath']; ?>carts/addItem' method='post'>
					<input type='submit' value='Add to Basket' class='add-to-basket-btn' id='add-to-basket-from-wish-list'>
					<input type='hidden' name='productVersionId' value='<?php echo $product['product_version_id']; ?>'>
					<input type='hidden' name='quantity' value='1'>
				</form>

				<form action='<?php echo $GLOBALS['rootPath']; ?>wish_lists/removeItem' method='post'>
					<input type='submit' value='Remove from Wish List' class='add-to-basket-btn' id='remove-from-wish-list-btn'>
					<input type='hidden' name='productVersionId' value="<?php echo $product['product_version_id']; ?>">
					<input type='hidden' name='wishListId' value="<?php echo $this->data['listInfo']['wish_list_id']; ?>">
				</form>


			<?php
				echo "</div>";
				echo "</div>";
				} 	
			} else {
				echo "<p id='empty-wish-list'>This wish list is empty.</p>";
			}
			
		} elseif (count($this->data['listOfWishLists']) > 0) {
			echo "<p id='empty-wish-list'>Chose a wish list.</p>";
		} else {
			echo "<p id='empty-wish-list'>You have no wish lists.</p>";
		}
			
		?>
		
</div>