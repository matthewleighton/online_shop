<?php $product = $this->data['product']; ?>
<div class='product-section-1'>
	<div class='product-image'>
		<?php echo $this->productImage($product['base_product_id'],
									   $product['product_version_id'],
									   250, 0, true,
									   ['class' => 'product-image']); ?>
	</div>
	
	<div class='product-description'>
		<div class="product-name">
			<?php echo $product['product_name'] . ' (' . $product['platform'] . ')' ?>
		</div>

		<div class="product-creator">
			<?php
				if (isset($product['authors'])) {
					echo "By " . $this->arrayToString($product['authors']);
				} elseif (isset($product['directors'])) {
					echo "Directed by " . $this->arrayToString($product['directors']);
				} elseif (isset($product['musicians'])) {
					echo "By " . $product['musicians'];
				}
			?>
		</div>

		<div class='product-versions'>
			<?php
				foreach ($this->data['productVersions'] as $productVersion) {
					echo "<a href='" . $GLOBALS['rootPath'] . 'products/item/' . $productVersion['product_version_id'] . "'" .
							"class='product-version-link'>";
					echo "<div class='product-version-box'";
					if ($productVersion['platform'] == $this->data['product']['platform']) {
						echo " id='current-product-version'";
					}
					echo ">";

					echo "<p class='product-version-platform'>" . $productVersion['platform'] . "</p>";
					echo "<p class='product-version-price'>£" . $this->formatPrice($productVersion['product_price']) . "</p>";

					echo "</div>";
					echo "</a>";
				}	
			?>
		</div>

		<div class="product-price-section">
			<span class='price-label'>Price: </span>
				<span class='product-price'>£<?php echo $this->formatPrice($product['product_price']); ?>
			</span>
		</div>

		<div class="product-description">
			<div id="product-description-text">
				<span id='product-description-measurement'><?php echo $product['product_description']; ?></span>
			</div>
			<a class="product-description-expander">
				<span id="product-description-more">Read more</span>
				<span id="product-description-less">Read less</span>
			</a>
			<br><br>

			<?php
				if (isset($product['songs'])) {
					echo "<h2>Track listing</h2>";

					$numberOfSongs = count($product['songs']);
					for ($i=0; $i < $numberOfSongs; $i++) { 
						echo "<span class='song-title-listing'>" . ($i + 1) . '. ' . $product['songs'][$i] . '</span>';
						echo " - <span class='song-running-time-listing'>" . $product['running_times'][$i] . '</span><br>';
					}
				}

				if (isset($product['episodes'])) {
					$numberOfEpisodes = count($product['episodes']);
					echo "<h2>Episodes</h2>";
					for ($i=0; $i < $numberOfEpisodes; $i++) { 
						echo ($i + 1) . '. ' . $product['episodes'][$i] . ' - ';
						echo $this->printRunningTime($product['episode_running_times'][$i]) . '<br>';
					}
					
					
				}
			?>

		</div>
	</div>

	<div class='add-to-cart-box'>
		<form action="<?php echo $GLOBALS['rootPath']; ?>carts/addItem" method="post">
			<span>Quantity: </span>
			<select name='quantity'>
				<option value='1'>1</option>
				<option value='2'>2</option>
				<option value='3'>3</option>
				<option value='4'>4</option>
			</select>
			<input type='hidden' name='price' value=<?php echo "'" . $product['product_price'] . "'"; ?>>
			<input type='hidden' name='productVersionId' value=<?php echo "'" . $product['product_version_id'] . "'" ?>>
			<input type='submit' value='Add to Basket' class='add-to-basket-btn'>
		</form>
		<form action="<?php echo $this->rootPath(); ?>wish_lists/addItem" method="post" id='add-to-wish-list-form'>
			<?php
				if (isset($_SESSION['user_id'])) {
					echo "<select name='wishListId' class='add-to-wish-list-btn' id='add-to-wish-list-id'>";
					echo "<option value=''></option>";
					foreach ($this->data['wishLists'] as $wishlist) {
						echo "<option value='" . $wishlist['wish_list_id'] . "'>" . $wishlist['wish_list_name'] . "</option>";
					}
					echo "<option value='new-wish-list'><b>Create New Wish List</b></option>";
					echo "</select>";
					echo "<h4 id='add-to-wish-list-btn-text' class='noselect'>Add to Wish List</h4>";
					echo "<input type='text' name='newWishList' id='new-wish-list-input'>";
				} else {
					echo "<input type='submit' value='Add to Wish List' class='add-to-wish-list-btn'>";
					echo "<input type='hidden' name='loggedOut' value='1'>";
				}
			?>

			<input type="hidden" name="productVersionId" value=<?php echo "'" . $product['product_version_id'] . "'" ?>>
		</form>



	</div>

</div>
<h3>Product Details</h3>
<div class="product-details">
	<?php 
		if (isset($product['release_date'])) {
	?>
			<p>
				<?php echo "<b>Release date:</b> " . $this->formatDate($product['release_date'], 'jS F Y'); ?>
			</p>
	<?php }

		if (isset($product['age_rating'])) {
			echo '<b><p>Age Rating:</b> ';
			echo $this->displayAgeRating($product['age_rating'], 30);
			echo '</p>';
		}

		if (file_exists('../app/views/products/_' . $product['product_catagory'] . '_details.php')) {
			include_once('../app/views/products/_' . $product['product_catagory'] . '_details.php');
		}
	?>

</div>