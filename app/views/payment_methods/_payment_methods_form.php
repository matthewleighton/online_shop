<form action="<?php echo $GLOBALS['rootPath']; ?>payment_methods/add" method="post" class="input-page" id="new-payment-method">
	<input type="hidden" name="addressId" value="">
	
	<div class='payment-method-warning'>
		This site is purely for demonstration purposes. Do NOT enter genuine payment information.
	</div>

	<div class='card-details-entry'>

		<div class='card-type inline-block'>
			<span class='small-field-label'>Card type: </span><br>
			<?php $this->createSelect('card_type',
									[['visa', 'Visa/Delta/Electron'], 'mastercard', 'american_express'], 
									  'payment_method', ['id' => 'card-type']); ?>
		</div>

		<div class='card-exp inline-block'>
			<span class='small-field-label'>Expiration Date:</span><br>
			<?php $this->createSelect('exp_month', range(01, 12), 'payment_method', ['id' => 'card-exp']); ?>
			<?php $this->createSelect('exp_year', range(2015, 2035), 'payment_method', ['id' => 'card-exp']); ?>
		</div>

		

		<?php $this->createInput('text', 'card_number', 'Card number', 'payment_method'); ?> <br>
		<?php $this->displayError($this->data['payment_method'], 'card_number'); ?>

		<?php $this->createInput('text', 'cardholder_name', 'Cardholder name', 'payment_method'); ?> <br>
		<?php $this->displayError($this->data['payment_method'], 'cardholder_name'); ?>
	</div>
	
	<span>Billing Address</span><br>

	<div id='card-creation-existing-address-btn' class='div-btn'>
		Existing Address
	</div><div id='card-creation-new-address-btn' class='div-btn'>
		New Address
	</div>

	<div id='card-creation-existing-address-form'>
		<?php include_once('../app/views/addresses/_address_list.php'); ?>
	</div>

	<div id='card-creation-new-address-form'>
		<?php require_once('../app/views/addresses/_address_form.php'); ?>
		<input type="submit">
	</div>

	<?php
		if(isset($this->data['redirect'])) {
			$redirect = $this->data['redirect'];
		} else {
			$redirect = '';
		}
	?>
	<input type="hidden" name="redirect" value="<?php echo $redirect ?>">
</form>