<div>
	<?php 
		if (isset($product['page_count'])) {
	?>
			<p>
				<?php echo "<b>" . ucfirst($product['platform']) . ":</b> " . $product['page_count'] . " pages"; ?>
			</p>	
	<?php } ?>

	
	<?php
		if ((isset($product['publisher'])) && strlen($product['publisher']) > 0) {
			echo "<p><b>Publisher:</b> " . $product['publisher'];
		}

		if (isset($product['languages'])) {
			echo '<p><b>Language';
			echo count($product['languages']) > 1 ? 's' : '';
			echo ': </b>' . $this->arrayToString($product['languages']) . '</p>';
		}
	?>



	

	<?php
		if (isset($product['file_type'])) {
			echo '<b>File type:</b> ' . $product['file_type'] . '<br>';
		}

		if (isset($product['file_size'])) {
			echo '<b>File size:</b> ' . $product['file_size'] . '<br>';
		}
	?>

</div>