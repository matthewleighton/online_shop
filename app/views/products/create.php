<?php $this->boxPageLogo(); ?>

<div class="box-page input-page" id="product-create-page">
	<h2>New product</h2>
	<form method="post">
		<div class="pc-general">
			<?php $this->createInput('text', 'product_name', 'Product name', 'product'); ?> <br>
			<?php $this->displayError($this->data['product'], 'product_name'); ?>

			<?php $this->createInput('text', 'price', 'Price', 'product', ['class' => 'product-input-small',
																		   'id' => 'product-price-input']); ?>
			<?php $this->createInput('text', 'release_date', 'Release date', 'product', ['class' => 'product-input-small',
																						 'id' => 'product-date-input']); ?>


			<?php $this->displayError($this->data['product'], 'price'); ?>
			<?php $this->displayError($this->data['product'], 'release_date'); ?>

			<?php $this->createInput('text', 'product_description', 'Product Description', 'product'); ?> <br>
			<?php $this->displayError($this->data['product'], 'product_description'); ?>

			<p>Select a product type</p>
			<div class="div-btn" id='book-btn'>
				<p hidden>book</p>
				<p>Book</p>
			</div>
			<div class="div-btn" id='film-btn'>
				<p hidden>film</p>
				<p>Film</p>
			</div>
			<div class="div-btn" id='video-game-btn'>
				<p hidden>video-game</p>
				<p>Video Game</p>
			</div>
			<div class="div-btn" id='music-btn'>
				<p hidden>music</p>
				<p>Music</p>
			</div>
		</div>

		<div class="type-creation-form" id="book-form">
			<?php require_once('../app/views/products/_book_creation_form.php'); ?>
		</div>

		<div class="type-creation-form" id="film-form">
			<?php require_once('../app/views/products/_film_creation_form.php'); ?>
		</div>

		<div class="type-creation-form" id="video-game-form">
			This is the video game creation form.
		</div>

		<div class="type-creation-form" id="music-form">
			This is the music creation form.
		</div>
	
		<input type='hidden' value='' name='product_catagory' id='submitted-product-type'>
		<input type='submit' value='Create product' id='product-submit-btn'>
	</form>
</div>