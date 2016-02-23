<?php $this->boxPageLogo(); ?>

<div class='box-page'>
	<div class='checkout-left checkout-half'>
		<h2>Select a payment method</h2>
		<?php
			if(!$this->data['paymentList'] == []) {
		?>
				<form method="post">
					<input type="hidden" name="paymentMethodId" value="" id="payment-method-id-input">
		<?php
					foreach($this->data['paymentList'] as $payment) {
						?>
						<div class='input-page div-select js-select'>
							<p hidden class='payment-method-id'><?php echo $payment['payment_method_id']; ?></p>

							<span class='listing-label'>Card type:</span>
							<span class='listing-value'><?php echo $payment['card_type']; ?></span><br>
							
							<span class='listing-label'>Card number:</span>
							<span class='listing-value'><?php echo $this->safeCardNum($payment['card_number']); ?></span><br>

							<span class='listing-label'>Cardholder name:</span>
							<span class='listing-value'><?php echo $payment['cardholder_name']; ?></span><br>

							<span class='listing-label'>Exp. date:</span>
							<span class='listing-value'><?php echo $payment['exp_month'] . "/" . $payment['exp_year']; ?></span><br>

							<span class='listing-label'>Billing address:</span>
							<div class='billing-address-listing'>

								<?php
									foreach($this->data['addressAttributes'] as $attr) {
										if(isset($payment[$attr])) {
											echo "<span class='listing-value'>" . $payment[$attr] . "</span><br>";
										}
									}
								?>
							</div>
						</div>
				<?php
					}
				?>
				</form>
<?php
			} else { 
?>
				<p>You have no saved payment methods.</p>
		<?php
			}
		?>

	</div><div class='checkout-right checkout-half'>
		<h2>Or enter a new card</h2>

		<?php require_once('../app/views/payment_methods/_payment_methods_form.php'); ?>

	</div>
</div>