<div id='main-header'>
	<div class='header-left'>
		
		<div class='logo'>
			<a href='<?php echo $GLOBALS['rootPath']; ?>'>
				<img src='<?php echo $GLOBALS['rootPath']; ?>assets/img/logo_placeholder.png' alt='logo' height='70'/>
			</a>
		</div>

		<div class="departments-dropdown">
			<button class="dropbtn">Departments...</button>
			<div class="dropdown-content">
				<ul>
					<li><?php $this->link_to('products/catagory/books', 'Books') ?></li>
					<li><?php $this->link_to('products/catagory/films', 'Films'); ?></li>
					<li><?php $this->link_to('products/catagory/tv', 'TV'); ?></li>
					<li><?php $this->link_to('products/catagory/music', 'Music'); ?></li>
					<li><?php $this->link_to('products/catagory/video_games', 'Video Games'); ?></li>
				</ul>
			</div>
		</div>
		
	</div>

	<div class='header-middle'>
		<div class='search-form'>
			<?php
					if (isset($_GET['search'])) {
						$searchValue = $_GET['search'];
					} else {
						$searchValue = '';
					}

					if (isset($_GET['catagory'])) {
						$catagoryValue = $_GET['catagory'];
					} else {
						$catagoryValue = 'all';
					}
				?>
			<form method='GET' action='<?php echo $this->rootPath();?>products/search'>
				<select class='search-catagory' name='catagory' selected="<?php echo $catagoryValue; ?>">
					<?php
					$catagories = ['all', 'book', 'film'];
					foreach ($catagories as $catagory) {
						if (isset($_GET['catagory']) && $_GET['catagory'] == $catagory) {
							$selected = " selected='selected' ";
						} else {
							$selected = "";
						}
						echo "<option value='" . $catagory . "' " . $selected . ">" . ucfirst($catagory) . "</option>";
					}

				?>
				</select>
				<input type="text" name="search" class="search-bar" value="<?php echo $searchValue; ?>">
				<input type="submit" value="GO!" class="search-submit">				
			</form>
		</div>
	</div>

	<div class='header-right'>
		<?php $this->link_to('carts/index', 'Basket'); ?>
		<?php $this->link_to('account', 'Your Account'); ?>
	</div>
</div>