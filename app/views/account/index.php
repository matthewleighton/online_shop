<div class="account-page">
	<h1>Hi, <?php echo Sessions_helper::currentUser()['first_name']; ?></h1>
	<ul>
		<li><a href='<?php echo $this->rootPath(); ?>account/orders'>
			<div><?php echo $this->image_tag('icons/purchase_history.svg', ['height' => 200]); ?><br>Your Orders</div>
		</a></li>
		<li><a href='<?php echo $this->rootPath(); ?>wish_lists/show/1'>
			<div><?php echo $this->image_tag('icons/wish_list.svg', ['height' => 200]); ?><br>Wish Lists</div>
		</a></li>
		<li><a href='<?php echo $this->rootPath(); ?>account/paymentmethods'>
			<div><?php echo $this->image_tag('icons/payment_methods.svg', ['height' => 200]); ?><br>Manage Payment Methods</div>
		</a></li>
		<li><a href='<?php echo $this->rootPath(); ?>account/addresses'>
			<div><?php echo $this->image_tag('icons/addresses.svg', ['height' => 200]); ?><br>Manage Addresses</div>
		</a></li>
	</ul>
</div>