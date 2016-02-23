<?php $this->boxPageLogo(); ?>

<div class='box-page half-size choose-delivery-method'>
	<h2>Choose a delivery method</h2>

	<form method='post'>
		<input type='hidden' name='deliveryMethod' value=''>

		<div class="js-select">
			<?php $this->image_tag('icons/slow_delivery.svg', ['height' => 140]); ?><br><span>Free Standard Delivery</span>
			<p hidden>free</p>
			<p class="delivery-due">Expected delivery: <?php echo date('l d M. Y', strtotime('+4 days', time())); ?>
		</div>
		<div class="js-select">
			<?php $this->image_tag('icons/fast_delivery.svg', ['height' => 140]); ?><br><span>1st Class Delivery</span>
			<p hidden>first-class</p>
			<p class="delivery-due">Expected delivery: <?php echo date('l d M. Y', strtotime('+2 days', time())); ?>
		</div>
		<div class="js-select">
			<?php $this->image_tag('icons/one_day_delivery.svg', ['height' => 140]); ?><br><span>One-day Delivery</span>
			<p hidden>one-day</p>
			<p class="delivery-due">Guaranteed delivery: <?php echo date('l d M. Y', strtotime('+1 days', time())); ?>
		</div>
	</form>

</div>