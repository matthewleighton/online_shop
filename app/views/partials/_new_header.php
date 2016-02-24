<div id='main-header'>
	<div class='header-left'>
		
		<div class='logo'>
			<a href="/online_shop/public">
				<img src='/online_shop/public/assets/img/logo_placeholder.png' alt='logo' height='70'/>
			</a>
		</div>

		<div class="departments-dropdown">
			<button class="dropbtn">Departments...</button>
			<div class="dropdown-content">
				<ul>
					<li><?php $this->link_to('products/catagory/books', 'Books') ?></li>
					<li><?php $this->link_to('#', 'Movies & TV'); ?></li>
					<li><?php $this->link_to('#', 'Music'); ?></li>
					<li><?php $this->link_to('#', 'Video Games'); ?></li>
				</ul>
			</div>
		</div>
		
	</div>

	<div class='header-middle'>
		<div class='search-form'>
			<form method='POST' action='<?php echo $this->rootPath();?>products/search'>
				<input type="text" name="search" class="search-bar">
				<input type="submit" value="GO!" class="search-submit">
			</form>
		</div>
	</div>

	<div class='header-right'>
		<?php $this->link_to('carts/index', 'Basket'); ?>
		<?php $this->link_to('account', 'Your Account'); ?>
	</div>
</div>