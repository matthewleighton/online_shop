<div>
	<?php 
		if (isset($product['page_count'])) {
	?>
			<p>
				<?php echo "<b>" . ucfirst($product['book_type']) . ":</b> " . $product['page_count'] . " pages"; ?>
			</p>	
	<?php } ?>

	
	<?php
		if (isset($product['release_date'])) {
			echo "<p><b>Publisher:</b> " . $product['publisher'];
			echo " (" . $this->formatDate($product['release_date']) . ")";
		}
	?>

	<p>
		<b>Language:</b> <?php echo $product['language']; ?>
	</p>

</div>