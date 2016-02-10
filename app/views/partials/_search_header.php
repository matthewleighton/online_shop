<div id="navbar">
	<div class="nav-col-1">
		<a href="/online_shop/public"><img src='/online_shop/public/assets/img/logo_placeholder.png' alt='logo' height='70'/></a>
		<div class='search-form'>
			<form>
				<input type="text" name="search" class="search-bar">
				<input type="submit" value="GO!" class="search-submit">
			</form>
		</div>
		<span>
			<?php $this->link_to('carts/index', 'Shopping Cart'); ?>
		</span>
	</div>
	
	<div class="nav-col-2">
		<div class="dropdown">
			<button class="dropbtn">Departments...</button>
			<div class="dropdown-content">
				<ul>
					<li><?php $this->link_to('products/catagory/books', 'Books') ?></li>
					<li><?php $this->link_to('#', 'Movies & TV') ?></li>
					<li><?php $this->link_to('#', 'Music') ?></li>
					<li><?php $this->link_to('#', 'Video Games') ?></li>
				</ul>
			</div>
		</div>

		
	</div>
</div>