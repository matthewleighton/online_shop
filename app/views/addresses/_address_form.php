<?php 
	// $includeForm specifies whether the <form> tags need to be included in this partial.
	// While creating an address for a payment method we don't need the form elements, as we're already inside one.
	if(isset($this->data['payment_method'])) {
		$includeForm = false;
	} else {
		$includeForm = true;
	}

	if($includeForm) {
?>
		<form action='<?php echo $GLOBALS['rootPath']; ?>addresses/add' method='post' class='input-page'>
<?php	 
	}
?>

	<?php $this->createInput('text', 'full_name', 'Full name', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'full_name'); ?>

	<?php $this->createInput('text', 'address_line_1', 'Address Line 1', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'address_line_1'); ?>	

	<?php $this->createInput('text', 'address_line_2', 'Address Line 2', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'address_line_2'); ?>

	<?php $this->createInput('text', 'city', 'City', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'city'); ?>

	<?php $this->createInput('text', 'county', 'County', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'county'); ?>

	<?php $this->createInput('text', 'postcode', 'Postcode', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'postcode'); ?>

	<?php $this->createInput('text', 'country', 'Country', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'country'); ?>

	<?php $this->createInput('text', 'phone_number', 'Phone number', 'address'); ?> <br>
	<?php $this->displayError($this->data['address'], 'phone_number'); ?>

	<input type="hidden" name="include_new_address" value="1">

<?php
	if($includeForm) {

		if(isset($this->data['redirect'])) {
			$redirect = $this->data['redirect'];
		} else {
			$redirect = '';
		}
?>
		<input type="hidden" name="redirect" value="<?php echo $redirect ?>">
		<input type="submit" value="Save">
		</form>
<?php
	}
?>