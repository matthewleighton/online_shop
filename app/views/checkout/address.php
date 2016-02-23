<?php $this->boxPageLogo(); ?>

<div class='box-page'>
	<div class='checkout-left checkout-half'>
		<h2>Select a delivery address</h2>
		
		<form method="post">
			<input type='hidden' name='addressId' value='' id='address-id-input'>
			<?php include_once('../app/views/addresses/_address_list.php'); ?>
		</form>
		
	</div><div class='checkout-right checkout-half'>
		<h2>Or enter a new delivery address</h2>

		<?php include_once('../app/views/addresses/_address_form.php'); ?>

	</div>
</div>