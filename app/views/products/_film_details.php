<div>
	<?php 
		if (isset($product['running_time'])) {
			echo "<p><b>Running time:</b> " . $this->printRunningTime($product['running_time']) . "</p>";
		}
	?>

	<!--
	<?php
		if (isset($product['release_date'])) {
			echo "<p><b>Publisher:</b> " . $product['publisher'];
			echo " (" . $this->formatDate($product['release_date']) . ")";
		}
	?>

	<p>
		<b>Language:</b> <?php echo $product['language']; ?>
	</p>
-->

</div>